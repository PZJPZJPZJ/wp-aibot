<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Widget {

    public static function init(): void {
        add_action('elementor/widgets/register', [self::class, 'register_widget']);
        add_action('elementor/frontend/after_enqueue_scripts', [self::class, 'enqueue_frontend_assets']);
        add_action('elementor/preview/enqueue_styles', [self::class, 'enqueue_frontend_assets']);
    }

    public static function register_widget($widgets_manager): void {
        require_once AI_CHATBOT_PATH . 'includes/class-widget-base.php';
        $widgets_manager->register(new AI_Chatbot_Widget_Base());
    }

    public static function enqueue_frontend_assets(): void {
        wp_enqueue_style(
            'ai-chat-widget',
            AI_CHATBOT_URL . 'assets/css/chat-widget.css',
            [],
            AI_CHATBOT_VERSION
        );

        wp_enqueue_script(
            'ai-chat-widget',
            AI_CHATBOT_URL . 'assets/js/chat-widget.js',
            [],
            AI_CHATBOT_VERSION,
            true
        );

        // Inline config — handled by enqueue_widget_assets() for non-Elementor contexts
    }
}
