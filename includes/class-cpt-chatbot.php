<?php
defined('ABSPATH') || exit;

class AI_Chatbot_CPT_Chatbot {

    public static function register(): void {
        register_post_type('ai_chatbot', [
            'labels' => [
                'name'               => __('AI Chatbots', 'wp-aibot'),
                'singular_name'      => __('AI Chatbot', 'wp-aibot'),
                'add_new'            => __('New Chatbot', 'wp-aibot'),
                'add_new_item'       => __('Add New Chatbot', 'wp-aibot'),
                'edit_item'          => __('Edit Chatbot', 'wp-aibot'),
                'view_item'          => __('View Chatbot', 'wp-aibot'),
                'menu_name'          => __('AIBot', 'wp-aibot'),
                'all_items'          => __('AI Chatbots', 'wp-aibot'),
            ],
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-format-chat',
            'menu_position'      => 25,
            'supports'           => ['title'],
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
        ]);

        add_action('add_meta_boxes_ai_chatbot', [self::class, 'add_meta_boxes']);
        add_action('save_post_ai_chatbot', [self::class, 'save_meta'], 10, 2);
    }

    public static function add_meta_boxes(): void {
        add_meta_box(
            'ai_chatbot_config',
            __('Chatbot Configuration', 'wp-aibot'),
            [self::class, 'render_meta_box'],
            'ai_chatbot',
            'normal',
            'high'
        );
    }

    public static function render_meta_box($post): void {
        wp_nonce_field('ai_chatbot_meta', 'ai_chatbot_meta_nonce');
        include AI_CHATBOT_PATH . 'templates/admin-chatbot-meta-box.php';
    }

    public static function save_meta(int $post_id, $post): void {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST['ai_chatbot_meta_nonce'])
            || !wp_verify_nonce($_POST['ai_chatbot_meta_nonce'], 'ai_chatbot_meta')) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = [
            'chatbot_platform',
            'chatbot_api_base_url',
            'chatbot_api_key',
            'chatbot_model',
            'chatbot_temperature',
            'chatbot_max_tokens',
            'chatbot_system_prompt',
            'chatbot_json_schema',
            'chatbot_knowledge_ids',
            'chatbot_max_history',
            'chatbot_session_ttl',
            'chatbot_greeting',
            'chatbot_offline_msg',
            'chatbot_avatar',
            'chatbot_layout_mode',
            'chatbot_lead_fields',
            'chatbot_lead_score_rules',
            'chatbot_notify_enabled',
            'chatbot_notify_email',
            'chatbot_notify_webhook',
            'chatbot_notify_rules',
            'chatbot_i18n',
            'chatbot_primary_color',
            'chatbot_fab_icon',
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field]) && !self::is_placeholder_value($field, $_POST[$field])) {
                $value = $_POST[$field];

                // Special handling per field type
                if ($field === 'chatbot_api_key' && !empty($value)) {
                    $value = self::encrypt($value);
                } elseif ($field === 'chatbot_notify_rules' && is_string($value)) {
                    $decoded = json_decode($value, true);
                    $value = is_array($decoded) ? $decoded : [];
                } elseif ($field === 'chatbot_notify_rules' && is_array($value)) {
                    // Structured array from interactive UI — sanitize each field
                    foreach ($value as $i => $item) {
                        if (is_array($item)) {
                            $value[$i] = array_map('sanitize_text_field', $item);
                        }
                    }
                } elseif ($field === 'chatbot_json_schema' && is_array($value)) {
                    // Structured array from interactive UI — sanitize each field
                    foreach ($value as $i => $item) {
                        if (is_array($item)) {
                            $value[$i] = array_map('sanitize_text_field', $item);
                        }
                    }
                } elseif ($field === 'chatbot_knowledge_ids' && is_array($value)) {
                    $value = array_map('intval', $value);
                } elseif (is_array($value)) {
                    $value = array_map('sanitize_text_field', $value);
                } else {
                    $value = sanitize_text_field($value);
                }

                update_post_meta($post_id, $field, $value);
            } else {
                // Handle empty/unchecked fields
                if ($field === 'chatbot_knowledge_ids') {
                    update_post_meta($post_id, $field, []);
                } elseif ($field === 'chatbot_notify_enabled') {
                    update_post_meta($post_id, $field, '0');
                } elseif ($field === 'chatbot_json_schema' && isset($_POST['chatbot_json_schema_sentinel'])) {
                    // Schema section was rendered but all fields removed — save empty array
                    update_post_meta($post_id, $field, []);
                } elseif ($field === 'chatbot_notify_rules' && isset($_POST['chatbot_notify_rules_sentinel'])) {
                    // Rules section was rendered but all rules removed — save empty array
                    update_post_meta($post_id, $field, []);
                }
            }
        }
    }

    private static function is_placeholder_value(string $field, $value): bool {
        if ($field === 'chatbot_api_key' && $value === '********') {
            return true;
        }
        return false;
    }

    public static function get_defaults(): array {
        return [
            'chatbot_platform'           => 'openai',
            'chatbot_api_base_url'       => 'https://api.openai.com/v1',
            'chatbot_api_key'          => '',
            'chatbot_model'            => 'gpt-4o-mini',
            'chatbot_temperature'      => '0.2',
            'chatbot_max_tokens'       => '2000',
            'chatbot_system_prompt'    => self::default_system_prompt(),
            'chatbot_json_schema'      => self::default_json_schema(),
            'chatbot_knowledge_ids'    => [],
            'chatbot_max_history'      => '10',
            'chatbot_session_ttl'      => '60',
            'chatbot_greeting'         => 'Hello! How can I help you today?',
            'chatbot_offline_msg'      => 'We are currently offline. Please leave a message.',
            'chatbot_avatar'           => '',
            'chatbot_layout_mode'      => 'inline',
            'chatbot_lead_fields'      => [],
            'chatbot_lead_score_rules' => [],
            'chatbot_notify_enabled'   => '0',
            'chatbot_notify_email'     => '',
            'chatbot_notify_webhook'   => '',
            'chatbot_notify_on_scores' => ['A', 'B'],
            'chatbot_notify_rules'   => [
                ['field' => 'lead.lead_score', 'operator' => 'in', 'value' => ['A', 'B']],
            ],
            'chatbot_i18n'             => [
                'title'             => 'AI Assistant',
                'subtitle'          => 'Ask me anything',
                'input_placeholder' => 'Type your message...',
                'thinking_text'     => 'Thinking...',
            ],
            'chatbot_primary_color'    => '#4f46e5',
            'chatbot_fab_icon'         => '💬',
        ];
    }

    public static function get_meta(int $post_id): array {
        $defaults = self::get_defaults();
        $meta = [];
        foreach ($defaults as $key => $default) {
            $value = get_post_meta($post_id, $key, true);
            $meta[$key] = $value !== '' ? $value : $default;
        }
        // Decrypt API key
        if (!empty($meta['chatbot_api_key'])) {
            $meta['chatbot_api_key'] = self::decrypt($meta['chatbot_api_key']);
        }
        return $meta;
    }

    private static function encrypt(string $value): string {
        if (!function_exists('openssl_encrypt')) return $value;
        $key = defined('AI_CHAT_SESSION_SECRET') ? AI_CHAT_SESSION_SECRET : wp_salt('auth');
        $cipher = 'aes-256-cbc';
        $iv_len = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $encrypted = openssl_encrypt($value, $cipher, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    private static function decrypt(string $value): string {
        if (!function_exists('openssl_decrypt')) return $value;
        $key = defined('AI_CHAT_SESSION_SECRET') ? AI_CHAT_SESSION_SECRET : wp_salt('auth');
        $cipher = 'aes-256-cbc';
        $iv_len = openssl_cipher_iv_length($cipher);
        $data = base64_decode($value);
        if ($data === false || strlen($data) <= $iv_len) return $value;
        $iv = substr($data, 0, $iv_len);
        $encrypted = substr($data, $iv_len);
        return openssl_decrypt($encrypted, $cipher, $key, 0, $iv) ?: $value;
    }

    private static function default_system_prompt(): string {
        return 'You are a helpful AI assistant for {company_name}.

Your goals:
1. Answer questions about {company_name}\'s products and services.
2. Guide users to clarify their project requirements.
3. Collect lead information: name, email, whatsapp, country, city.
4. Never invent prices, delivery dates, certifications, or legal commitments.
5. Detect the user\'s language and answer in the same language.
6. Keep answers concise and professional.';
    }

    private static function default_json_schema(): array {
        return [
            ['path' => 'answer',              'type' => 'string', 'description' => 'your response to the visitor',             'required' => true],
            ['path' => 'lead.lead_score',     'type' => 'enum',  'enum_values' => 'A|B|C|D', 'description' => 'Lead score (A=hot, B=warm, C=cold, D=unknown)', 'required' => true],
            ['path' => 'lead.name',           'type' => 'string', 'description' => 'Visitor name',      'required' => false],
            ['path' => 'lead.email',          'type' => 'string', 'description' => 'Visitor email',     'required' => false],
            ['path' => 'lead.whatsapp',       'type' => 'string', 'description' => 'Visitor WhatsApp',  'required' => false],
            ['path' => 'lead.country',        'type' => 'string', 'description' => 'Visitor country',   'required' => false],
            ['path' => 'lead.city',           'type' => 'string', 'description' => 'Visitor city',      'required' => false],
            ['path' => 'lead.project_type',   'type' => 'string', 'description' => 'Project type/requirements', 'required' => false],
            ['path' => 'lead.summary',        'type' => 'string', 'description' => 'Conversation summary', 'required' => false],
            ['path' => 'should_notify_sales', 'type' => 'boolean', 'description' => 'Whether to notify sales team', 'required' => true],
        ];
    }

    /**
     * Convert the structured JSON schema array into a prompt instruction string.
     * Falls back to raw string for backward compatibility.
     */
    public static function build_json_instruction($schema): string {
        if (is_string($schema)) {
            return $schema; // backward compat
        }
        if (empty($schema) || !is_array($schema)) {
            return '';
        }

        $lines = ["Return ONLY valid JSON, no markdown, no code fences, in this exact shape:"];

        // Group by nesting prefix for cleaner output
        $roots = [];
        $nested = [];
        foreach ($schema as $field) {
            $path = $field['path'] ?? '';
            if (str_contains($path, '.')) {
                $parts = explode('.', $path, 2);
                $roots[$parts[0]][] = $field;
            } else {
                $nested[] = $field;
            }
        }

        $lines[] = '{';
        $top = [];

        foreach ($nested as $field) {
            $top[] = self::field_to_json_line($field);
        }
        foreach ($roots as $parent => $children) {
            // Only output parent heading if it exists as a flat field, otherwise skip
            $child_lines = [];
            foreach ($children as $child) {
                $child_parts = explode('.', $child['path'], 2);
                $child_lines[] = self::field_to_json_line($child, '    ');
            }
            $top[] = '  "' . $parent . '": {';
            $top[] = implode(",\n", $child_lines);
            $top[] = '  }';
        }

        $lines[] = implode(",\n", $top);
        $lines[] = '}';

        return implode("\n", $lines);
    }

    private static function field_to_json_line(array $field, string $indent = '  '): string {
        $path = $field['path'] ?? '';
        $type = $field['type'] ?? 'string';
        $desc = $field['description'] ?? '';
        $enum = $field['enum_values'] ?? '';

        // Extract just the field name from dotted path
        $name = str_contains($path, '.') ? substr($path, strrpos($path, '.') + 1) : $path;

        switch ($type) {
            case 'boolean':
                $default = 'false';
                break;
            case 'enum':
                $default = !empty($enum) ? '"' . $enum . '"' : '""';
                break;
            default:
                $default = '""';
                break;
        }

        $comment = !empty($desc) ? ' // ' . $desc : '';
        return $indent . '"' . $name . '": ' . $default . $comment;
    }
}
