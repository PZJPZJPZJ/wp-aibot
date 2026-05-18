<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Plugin {

    private static ?AI_Chatbot_Plugin $instance = null;

    public static function init(): void {
        if (self::$instance === null) {
            self::$instance = new self();
        }
    }

    private function __construct() {
        $this->load_dependencies();
        $this->register_hooks();
    }

    private function load_dependencies(): void {
        $includes = AI_CHATBOT_PATH . 'includes/';

        // CPTs
        require_once $includes . 'class-cpt-chatbot.php';
        require_once $includes . 'class-cpt-knowledge.php';
        require_once $includes . 'class-cpt-conversation.php';

        // Core engine
        require_once $includes . 'class-ai-client.php';
        require_once $includes . 'class-knowledge-loader.php';
        require_once $includes . 'class-memory-manager.php';
        require_once $includes . 'class-lead-processor.php';
        require_once $includes . 'class-notifier.php';
        require_once $includes . 'class-chat-api.php';

        // Widget (Elementor integration)
        require_once $includes . 'class-widget.php';

        // Admin
        if (is_admin()) {
            require_once $includes . 'class-admin-columns.php';
            require_once $includes . 'class-admin-ajax.php';
            require_once $includes . 'class-export.php';
            new AI_Chatbot_Admin_Columns();
            new AI_Chatbot_Export();
        }
    }

    private function register_hooks(): void {
        add_action('init', [$this, 'register_cpts']);
        add_action('init', [$this, 'register_widget']);
        add_action('rest_api_init', [$this, 'register_api_routes']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_filter('plugin_action_links_' . plugin_basename(AI_CHATBOT_FILE), [$this, 'plugin_action_links']);
        add_shortcode('ai_chatbot', [$this, 'shortcode_render']);

        // AJAX handlers
        add_action('wp_ajax_ai_chatbot_preview', ['AI_Chatbot_Admin_Ajax', 'preview']);
    }

    /**
     * Shortcode: [ai_chatbot id="123"]
     */
    public function shortcode_render($atts): string {
        $chatbot_id = (int) ($atts['id'] ?? 0);
        if (!$chatbot_id || get_post_type($chatbot_id) !== 'ai_chatbot') {
            return '<div class="ai-chat-error">' . __('Invalid chatbot ID.', 'wp-aibot') . '</div>';
        }

        // Ensure CSS and JS are enqueued
        $this->enqueue_widget_assets();

        return self::render_chatbot_html($chatbot_id);
    }

    /**
     * Render chatbot HTML — shared by shortcode, admin preview, and widget.
     */
    public static function render_chatbot_html(int $chatbot_id, string $widget_id = ''): string {
        // Ensure CSS and JS are always loaded, regardless of how the widget is rendered
        wp_enqueue_style('ai-chat-widget', AI_CHATBOT_URL . 'assets/css/chat-widget.css', [], AI_CHATBOT_VERSION);
        wp_enqueue_script('ai-chat-widget', AI_CHATBOT_URL . 'assets/js/chat-widget.js', [], AI_CHATBOT_VERSION, true);

        $config = AI_Chatbot_CPT_Chatbot::get_meta($chatbot_id);
        if (empty($widget_id)) {
            $widget_id = 'ai_' . $chatbot_id . '_' . uniqid();
        }

        // Session
        $client_ip = self::get_client_ip();
        $session_id = 'sess_' . md5($client_ip . '_' . $chatbot_id);
        $session_token = hash_hmac('sha256', $session_id, AI_CHAT_SESSION_SECRET);

        // Pass config to JS
        wp_localize_script('ai-chat-widget', 'AIChatConfig_' . $widget_id, [
            'chatbot_id'    => $chatbot_id,
            'session_id'    => $session_id,
            'session_token' => $session_token,
            'layout_mode'   => $config['chatbot_layout_mode'] ?: 'inline',
            'greeting'      => $config['chatbot_greeting'] ?: 'Hello!',
            'avatar'        => $config['chatbot_avatar'] ? wp_get_attachment_url($config['chatbot_avatar']) : '',
            'i18n'          => $config['chatbot_i18n'] ?: [],
            'fab_icon'      => $config['chatbot_fab_icon'] ?: '💬',
            'widget_id'     => $widget_id,
        ]);

        $container_id = 'ai-chatbot-container-' . $widget_id;
        $layout = $config['chatbot_layout_mode'] ?? 'inline';
        ob_start();

        // Inline theme styles for custom primary color
        $primary = $config['chatbot_primary_color'] ?? '';
        if (!empty($primary) && $primary !== '#4f46e5') {
            $safe = esc_attr($primary);
            echo '<style id="ai-chat-theme-' . esc_attr($widget_id) . '">
.ai-chatbot-fab { background: ' . $safe . '; }
.ai-chatbot-header { background: ' . $safe . '; }
.ai-chatbot-send { background: ' . $safe . '; }
.ai-chatbot-send:hover { background: ' . $safe . '; }
.ai-chatbot-user .ai-chatbot-bubble { background: ' . $safe . '; }
.ai-chatbot-input:focus { border-color: ' . $safe . '; }
</style>';
        }

        // Render container — custom HTML or default
        $custom_html = $config['chatbot_html_template'] ?? '';
        if (!empty($custom_html)) {
            echo str_replace(
                ['{{container_id}}', '{{widget_id}}', '{{chatbot_id}}', '{{layout}}'],
                [esc_attr($container_id), esc_attr($widget_id), (int) $chatbot_id, esc_attr($layout)],
                $custom_html
            );
        } else {
            echo '<div id="' . esc_attr($container_id) . '" class="ai-chatbot-container" data-widget-id="' . esc_attr($widget_id) . '" data-layout="' . esc_attr($layout) . '"></div>';
        }

        // Inline custom CSS if saved
        $custom_css = $config['chatbot_custom_css'] ?? '';
        if (!empty($custom_css)) {
            echo '<style id="ai-chat-css-' . esc_attr($widget_id) . '">' . "\n" . $custom_css . "\n" . '</style>';
        }

        // Inline custom JS if saved
        $custom_js = $config['chatbot_custom_js'] ?? '';
        if (!empty($custom_js)) {
            echo '<script id="ai-chat-js-' . esc_attr($widget_id) . '">' . "\n" . $custom_js . "\n" . '</script>';
        }

        return ob_get_clean();
    }

    public static function get_client_ip(): string {
        $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($headers as $h) {
            if (!empty($_SERVER[$h])) {
                $ips = explode(',', $_SERVER[$h]);
                return trim($ips[0]);
            }
        }
        return '127.0.0.1';
    }

    public function register_cpts(): void {
        AI_Chatbot_CPT_Chatbot::register();
        AI_Chatbot_CPT_Knowledge::register();
        AI_Chatbot_CPT_Conversation::register();
    }

    public function register_widget(): void {
        AI_Chatbot_Widget::init();
    }

    public function register_api_routes(): void {
        AI_Chatbot_Chat_API::register_routes();
    }

    public function enqueue_admin_assets(string $hook): void {
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->post_type, ['ai_chatbot', 'ai_knowledge', 'ai_conversation'], true)) {
            return;
        }
        wp_enqueue_style('ai-chatbot-admin', AI_CHATBOT_URL . 'assets/css/admin.css', [], AI_CHATBOT_VERSION);

        // Load widget CSS/JS on chatbot edit screen for the live preview
        if ($screen->post_type === 'ai_chatbot' && $screen->base === 'post') {
            $this->enqueue_widget_assets();

            // Admin JS for chatbot config (tabs, schema builder, notification rules)
            wp_enqueue_script(
                'ai-chatbot-admin',
                AI_CHATBOT_URL . 'assets/js/admin.js',
                ['jquery'],
                AI_CHATBOT_VERSION,
                true
            );
        }
    }

    public function enqueue_frontend_assets(): void {
        // Only enqueue on demand via widget
    }

    /**
     * Enqueue the chatbot widget CSS and JS.
     */
    public function enqueue_widget_assets(): void {
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

        wp_localize_script('ai-chat-widget', 'AIChatBotGlobals', [
            'rest_url'    => esc_url_raw(rest_url('ai-chat/v1/chat')),
            'history_url' => esc_url_raw(rest_url('ai-chat/v1/history')),
            'nonce'       => wp_create_nonce('wp_rest'),
        ]);
    }

    public function plugin_action_links(array $links): array {
        $settings_link = '<a href="' . admin_url('edit.php?post_type=ai_chatbot') . '">'
            . __('Manage Chatbots', 'wp-aibot') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}
