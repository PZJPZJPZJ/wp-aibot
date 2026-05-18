<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Installer {

    public static function activate(): void {
        self::register_cpts();
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    private static function register_cpts(): void {
        require_once AI_CHATBOT_PATH . 'includes/class-cpt-chatbot.php';
        require_once AI_CHATBOT_PATH . 'includes/class-cpt-knowledge.php';
        require_once AI_CHATBOT_PATH . 'includes/class-cpt-conversation.php';
        AI_Chatbot_CPT_Chatbot::register();
        AI_Chatbot_CPT_Knowledge::register();
        AI_Chatbot_CPT_Conversation::register();
    }
}
