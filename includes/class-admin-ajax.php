<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Admin_Ajax {

    /**
     * AJAX handler for chatbot preview.
     */
    public static function preview(): void {
        if (!current_user_can('manage_options')) {
            wp_die(-1);
        }

        $chatbot_id = (int) ($_POST['chatbot_id'] ?? 0);
        if (!$chatbot_id || get_post_type($chatbot_id) !== 'ai_chatbot') {
            wp_send_json_error(['message' => 'Invalid chatbot.']);
        }

        $message = sanitize_text_field($_POST['message'] ?? 'Hello');

        // Use a deterministic visitor session so preview goes through the same code path as frontend
        $hash = md5('admin_preview_' . $chatbot_id);
        $visitor_id = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-4' . substr($hash, 12, 3) . '-a' . substr($hash, 15, 3) . '-' . substr($hash, 18, 12);
        $session_id = 'sess_' . md5($visitor_id . '_' . $chatbot_id);
        $session_token = hash_hmac('sha256', $session_id, AI_CHAT_SESSION_SECRET);

        // Simulate a chat via REST
        $request = new WP_REST_Request('POST', '/ai-chat/v1/chat');
        $request->set_body_params([
            'chatbot_id'    => $chatbot_id,
            'message'       => $message,
            'session_id'    => $session_id,
            'session_token' => $session_token,
            'visitor_id'    => $visitor_id,
            'metadata'      => ['page' => admin_url(), 'referrer' => '', 'language' => 'en'],
        ]);

        $response = AI_Chatbot_Chat_API::handle_chat($request);
        $data = $response->get_data();

        wp_send_json($data);
    }
}
