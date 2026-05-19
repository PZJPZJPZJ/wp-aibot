<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Admin_Columns {

    public function __construct() {
        // Chatbot columns
        add_filter('manage_ai_chatbot_posts_columns', [$this, 'chatbot_columns']);
        add_action('manage_ai_chatbot_posts_custom_column', [$this, 'chatbot_column_data'], 10, 2);

        // Conversation columns
        add_filter('manage_ai_conversation_posts_columns', [$this, 'conversation_columns']);
        add_action('manage_ai_conversation_posts_custom_column', [$this, 'conversation_column_data'], 10, 2);
    }

    public function chatbot_columns(array $columns): array {
        $columns['platform'] = __('Platform', 'wp-aibot');
        $columns['model']    = __('Model', 'wp-aibot');
        $columns['layout']   = __('Layout', 'wp-aibot');
        return $columns;
    }

    public function chatbot_column_data(string $column, int $post_id): void {
        $config = AI_Chatbot_CPT_Chatbot::get_meta($post_id);
        switch ($column) {
            case 'platform':
                echo esc_html($config['chatbot_platform'] ?? '—');
                break;
            case 'model':
                echo esc_html($config['chatbot_model'] ?? '—');
                break;
            case 'layout':
                echo esc_html($config['chatbot_layout_mode'] ?? 'inline');
                break;
        }
    }

    public function conversation_columns(array $columns): array {
        $columns['chatbot']      = __('Chatbot', 'wp-aibot');
        $columns['lead_score']   = __('Lead Score', 'wp-aibot');
        $columns['notification'] = __('Notification', 'wp-aibot');
        $columns['messages']     = __('Messages', 'wp-aibot');
        $columns['started']      = __('Started', 'wp-aibot');
        return $columns;
    }

    public function conversation_column_data(string $column, int $post_id): void {
        switch ($column) {
            case 'chatbot':
                $bid = get_post_meta($post_id, 'conversation_chatbot_id', true);
                $bot = $bid ? get_post($bid) : null;
                echo $bot ? esc_html($bot->post_title) : '—';
                break;
            case 'lead_score':
                $lead = get_post_meta($post_id, 'conversation_lead_data', true);
                echo $lead ? esc_html($lead['lead_score'] ?? '—') : '—';
                break;
            case 'notification':
                $status = get_post_meta($post_id, 'conversation_notification_status', true);
                $labels = [
                    'sent'     => '<span style="color:#46b450;">✓ ' . esc_html__('Sent', 'wp-aibot') . '</span>',
                    'failed'   => '<span style="color:#dc3232;">✗ ' . esc_html__('Failed', 'wp-aibot') . '</span>',
                    'none'     => '<span style="color:#888;">— ' . esc_html__('No match', 'wp-aibot') . '</span>',
                    'disabled' => '<span style="color:#bbb;">— ' . esc_html__('Disabled', 'wp-aibot') . '</span>',
                ];
                echo isset($labels[$status]) ? $labels[$status] : '<span style="color:#ccc;">—</span>';
                break;
            case 'messages':
                echo (int) get_post_meta($post_id, 'conversation_message_count', true);
                break;
            case 'started':
                echo esc_html(get_post_meta($post_id, 'conversation_started_at', true));
                break;
        }
    }
}
