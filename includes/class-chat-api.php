<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Chat_API {

    private const RATE_LIMIT = 30; // requests per minute
    private const RATE_WINDOW = 60; // seconds
    private const MAX_MESSAGE_LENGTH = 2000;

    public static function register_routes(): void {
        register_rest_route('ai-chat/v1', '/chat', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'handle_chat'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('ai-chat/v1', '/history', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'handle_history'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function handle_chat(WP_REST_Request $request): WP_REST_Response {
        // Validate
        $chatbot_id = (int) $request->get_param('chatbot_id');
        $message = trim($request->get_param('message') ?? '');
        $session_id = trim($request->get_param('session_id') ?? '');
        $session_token = trim($request->get_param('session_token') ?? '');
        $visitor_id = trim($request->get_param('visitor_id') ?? '');
        $metadata = $request->get_param('metadata') ?: [];

        // Validate chatbot exists
        $chatbot = get_post($chatbot_id);
        if (!$chatbot || $chatbot->post_type !== 'ai_chatbot' || $chatbot->post_status !== 'publish') {
            return self::error('invalid_chatbot', 'Chatbot not found or not published.', 404);
        }

        // Validate message
        if (empty($message)) {
            return self::error('empty_message', 'Message cannot be empty.', 400);
        }
        if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
            return self::error('message_too_long', 'Message exceeds maximum length.', 400);
        }

        // Visitor-based session — single source of truth (localStorage UUID)
        if (empty($visitor_id) || !preg_match('/^[a-f0-9-]{36}$/i', $visitor_id)) {
            return self::error('invalid_session', 'Invalid visitor ID.', 403);
        }
        $session_id = 'sess_' . md5($visitor_id . '_' . $chatbot_id);
        $expected_token = hash_hmac('sha256', $session_id, AI_CHAT_SESSION_SECRET);
        if (empty($session_token)) {
            $session_token = $expected_token;
        } elseif (!hash_equals($expected_token, $session_token)) {
            $session_token = $expected_token;
        }

        // Rate limit
        $client_ip = AI_Chatbot_Plugin::get_client_ip();
        if (self::is_rate_limited($client_ip, $session_id)) {
            return self::error('rate_limited', 'Too many requests. Please try again later.', 429);
        }

        // Load chatbot config
        $config = AI_Chatbot_CPT_Chatbot::get_meta($chatbot_id);

        // Get or create conversation
        $conversation_id = self::get_conversation($session_id, $chatbot_id, $client_ip, $metadata);

        // Session TTL check — if conversation has expired, start a new one (same session_id)
        $session_ttl = (int) ($config['chatbot_session_ttl'] ?? 720);
        if ($conversation_id && $session_ttl > 0) {
            $started_at = get_post_meta($conversation_id, 'conversation_started_at', true);
            if (!empty($started_at)) {
                $expiry = strtotime($started_at) + ($session_ttl * 60);
                if (time() > $expiry) {
                    // New conversation with same session_id; get_conversation() returns latest by date DESC
                    $conversation_id = AI_Chatbot_CPT_Conversation::create($session_id, $chatbot_id, [
                        'ip'       => $client_ip,
                        'ua'       => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'page_url' => $metadata['page'] ?? '',
                    ]);
                }
            }
        }

        // Collect visitor data
        $visitor_data = self::collect_visitor_data($client_ip, $metadata);

        // Load knowledge context
        $knowledge_loader = new AI_Chatbot_Knowledge_Loader();
        $knowledge_context = $knowledge_loader->load_context($chatbot_id);

        // Load conversation history
        $memory = new AI_Chatbot_Memory_Manager();
        $history = $memory->load_history($conversation_id, (int) $config['chatbot_max_history']);

        // Build messages
        $messages = self::build_messages($config, $knowledge_context, $history, $message);

        // Call AI
        $ai_client = new AI_Chatbot_AI_Client($config);
        $result = $ai_client->chat($messages);

        if ($result === null) {
            return self::error('ai_error', 'AI service error. Please try again.', 502);
        }

        $ai_content = $result['content'];

        // Parse lead
        $lead_processor = new AI_Chatbot_Lead_Processor();
        $parsed = $lead_processor->parse($ai_content);

        if ($parsed === null) {
            return new WP_REST_Response([
                'ok'   => true,
                'data' => [
                    'reply'            => $ai_content,
                    'session_id'       => $session_id,
                    'session_token'    => $session_token,
                    'conversation_id'  => $conversation_id,
                    'lead_score'       => 'D',
                    'should_notify_sales' => false,
                    'should_collect_contact' => false,
                ],
            ], 200);
        }

        $reply = $parsed['answer'] ?? $ai_content;
        $lead_data = $parsed['lead'] ?? [];

        // Save to memory
        $memory->append($conversation_id, $message, $reply);

        // Save lead data
        if (!empty($lead_data)) {
            update_post_meta($conversation_id, 'conversation_lead_data', $lead_data);
        }

        // Trigger notification if rules match (pass full parsed data for rule evaluation)
        $notifier = new AI_Chatbot_Notifier();
        $notifier->notify($parsed, $visitor_data, $config);

        return new WP_REST_Response([
            'ok'   => true,
            'data' => [
                'reply'            => $reply,
                'session_id'       => $session_id,
                'session_token'    => $session_token,
                'conversation_id'  => $conversation_id,
                'lead_score'       => $lead_data['lead_score'] ?? 'D',
                'should_notify_sales' => $parsed['should_notify_sales'] ?? false,
                'should_collect_contact' => in_array($lead_data['lead_score'] ?? 'D', ['A', 'B']),
            ],
        ], 200);
    }

    private static function build_messages(array $config, string $knowledge_context, array $history, string $message): array {
        $system = $config['chatbot_system_prompt'] ?? '';

        // Inject JSON schema instruction (managed separately from the user prompt)
        $json_schema = $config['chatbot_json_schema'] ?? '';
        $json_instruction = is_string($json_schema)
            ? $json_schema
            : AI_Chatbot_CPT_Chatbot::build_json_instruction($json_schema);
        if (!empty($json_instruction)) {
            $system .= "\n\n---\n\n{$json_instruction}";
        }

        // Inject knowledge context
        if (!empty($knowledge_context)) {
            $system .= "\n\n---\n\nKnowledge Base:\n{$knowledge_context}\n\n---";
        }

        $messages = [['role' => 'system', 'content' => $system]];

        // Append history
        foreach ($history as $h) {
            $messages[] = $h;
        }

        // Current user message
        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }

    private static function get_conversation(string $session_id, int $chatbot_id, string $ip, array $metadata): int {
        $existing = get_posts([
            'post_type'      => 'ai_conversation',
            'meta_key'       => 'conversation_session_id',
            'meta_value'     => $session_id,
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        if (!empty($existing)) {
            return (int) $existing[0];
        }

        return AI_Chatbot_CPT_Conversation::create($session_id, $chatbot_id, [
            'ip'       => $ip,
            'ua'       => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'page_url' => $metadata['page'] ?? '',
        ]);
    }

    private static function collect_visitor_data(string $ip, array $metadata): array {
        return [
            'ip'       => $ip,
            'ua'       => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'page_url' => $metadata['page'] ?? '',
            'referrer' => $metadata['referrer'] ?? '',
            'language' => $metadata['language'] ?? '',
        ];
    }

    private static function is_rate_limited(string $ip, string $session_id): bool {
        $key = 'ai_chat_rate_' . md5($ip . '_' . $session_id);
        $data = get_transient($key);

        if ($data === false) {
            set_transient($key, 1, self::RATE_WINDOW);
            return false;
        }

        if ((int) $data >= self::RATE_LIMIT) {
            return true;
        }

        set_transient($key, (int) $data + 1, self::RATE_WINDOW);
        return false;
    }

    private static function error(string $code, string $message, int $status): WP_REST_Response {
        return new WP_REST_Response([
            'ok'      => false,
            'code'    => $code,
            'message' => $message,
        ], $status);
    }

    /**
     * GET /ai-chat/v1/history — load conversation messages without creating a new session.
     */
    public static function handle_history(WP_REST_Request $request): WP_REST_Response {
        $chatbot_id = (int) $request->get_param('chatbot_id');
        $session_id = trim($request->get_param('session_id') ?? '');
        $session_token = trim($request->get_param('session_token') ?? '');
        $visitor_id = trim($request->get_param('visitor_id') ?? '');

        // Validate chatbot exists
        $chatbot = get_post($chatbot_id);
        if (!$chatbot || $chatbot->post_type !== 'ai_chatbot') {
            return self::error('invalid_chatbot', 'Chatbot not found.', 404);
        }

        // Visitor-based session — single source of truth (localStorage UUID)
        if (empty($visitor_id) || !preg_match('/^[a-f0-9-]{36}$/i', $visitor_id)) {
            return self::error('invalid_session', 'Invalid visitor ID.', 403);
        }
        $session_id = 'sess_' . md5($visitor_id . '_' . $chatbot_id);
        $expected_token = hash_hmac('sha256', $session_id, AI_CHAT_SESSION_SECRET);
        if (empty($session_token)) {
            $session_token = $expected_token;
        } elseif (!hash_equals($expected_token, $session_token)) {
            $session_token = $expected_token;
        }

        // Look up existing conversation (never create)
        $conversation_id = self::find_conversation($session_id);
        $messages = [];

        if ($conversation_id !== null) {
            $memory = new AI_Chatbot_Memory_Manager();
            $history = $memory->load_history($conversation_id, 50);
            foreach ($history as $msg) {
                $messages[] = [
                    'role'    => $msg['role'] === 'assistant' ? 'bot' : 'user',
                    'content' => $msg['content'],
                ];
            }
        }

        return new WP_REST_Response([
            'ok'   => true,
            'data' => [
                'messages'        => $messages,
                'session_id'      => $session_id,
                'session_token'   => $session_token,
                'conversation_id' => $conversation_id ?? 0,
            ],
        ], 200);
    }

    /**
     * Find an existing conversation by session_id, without creating one.
     */
    private static function find_conversation(string $session_id): ?int {
        $existing = get_posts([
            'post_type'      => 'ai_conversation',
            'meta_key'       => 'conversation_session_id',
            'meta_value'     => $session_id,
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        return !empty($existing) ? (int) $existing[0] : null;
    }
}
