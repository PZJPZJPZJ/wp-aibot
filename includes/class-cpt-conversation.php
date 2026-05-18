<?php
defined('ABSPATH') || exit;

class AI_Chatbot_CPT_Conversation {

    public static function register(): void {
        register_post_type('ai_conversation', [
            'labels' => [
                'name'               => __('Conversations', 'wp-aibot'),
                'singular_name'      => __('Conversation', 'wp-aibot'),
                'edit_item'          => __('View Conversation', 'wp-aibot'),
                'all_items'          => __('Conversations', 'wp-aibot'),
            ],
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=ai_chatbot',
            'supports'           => ['title'],
            'capability_type'    => 'post',
            'capabilities'       => [
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap'       => true,
        ]);

        add_action('add_meta_boxes_ai_conversation', [self::class, 'add_meta_boxes']);
        add_action('save_post_ai_conversation', [self::class, 'prevent_manual_edit'], 10, 3);
        add_action('admin_head-post.php', [self::class, 'hide_submit_meta_box']);
        add_action('admin_head-post-new.php', [self::class, 'hide_submit_meta_box']);
    }

    public static function hide_submit_meta_box(): void {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'ai_conversation') {
            remove_meta_box('submitdiv', 'ai_conversation', 'side');
        }
    }

    public static function add_meta_boxes(): void {
        add_meta_box(
            'ai_conversation_details',
            __('Conversation Details', 'wp-aibot'),
            [self::class, 'render_meta_box'],
            'ai_conversation',
            'normal',
            'high'
        );
    }

    public static function render_meta_box($post): void {
        $session_id   = get_post_meta($post->ID, 'conversation_session_id', true);
        $chatbot_id   = (int) get_post_meta($post->ID, 'conversation_chatbot_id', true);
        $history      = get_post_meta($post->ID, 'conversation_history', true);
        $msg_count    = (int) get_post_meta($post->ID, 'conversation_message_count', true);
        $started_at   = get_post_meta($post->ID, 'conversation_started_at', true);
        $lead_data    = get_post_meta($post->ID, 'conversation_lead_data', true);

        $ip       = get_post_meta($post->ID, 'conversation_visitor_ip', true);
        $ua       = get_post_meta($post->ID, 'conversation_visitor_ua', true);
        $page_url = get_post_meta($post->ID, 'conversation_visitor_page_url', true);

        $bot      = $chatbot_id ? get_post($chatbot_id) : null;
        $bot_name = $bot ? $bot->post_title : '—';

        include AI_CHATBOT_PATH . 'templates/admin-conversation-meta-box.php';
    }

    public static function prevent_manual_edit(int $post_id, $post, bool $update): void {
        // Conversations are system-managed only
    }

    public static function create(string $session_id, int $chatbot_id, array $visitor_data): int {
        $title = sprintf('Chat #%s - Bot #%d', substr($session_id, 0, 12), $chatbot_id);
        $id = wp_insert_post([
            'post_title'  => $title,
            'post_type'   => 'ai_conversation',
            'post_status' => 'publish',
            'meta_input'  => [
                'conversation_session_id'    => $session_id,
                'conversation_chatbot_id'    => $chatbot_id,
                'conversation_history'       => '',
                'conversation_visitor_ip'    => $visitor_data['ip'] ?? '',
                'conversation_visitor_ua'    => $visitor_data['ua'] ?? '',
                'conversation_visitor_page_url'  => $visitor_data['page_url'] ?? '',
                'conversation_message_count' => 0,
                'conversation_started_at'    => current_time('mysql'),
            ],
        ]);

        if (is_wp_error($id)) {
            return 0;
        }

        return $id;
    }
}
