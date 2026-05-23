<?php
/**
 * Plugin Name: WP AIBot
 * Description: AI-powered chatbot plugin. Supports OpenAI-compatible APIs, knowledge base, lead capture, and multi-bot management.
 * Version: 2.0.0
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * Author: AzzDev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://api.github.com/repos/pzjpzjpzj/wp-aibot
 * Text Domain: wp-aibot
 */

defined('ABSPATH') || exit;

define('AI_CHATBOT_VERSION', '1.0.0');
define('AI_CHATBOT_FILE', __FILE__);
define('AI_CHATBOT_PATH', plugin_dir_path(__FILE__));
define('AI_CHATBOT_URL', plugin_dir_url(__FILE__));

// Session secret — deferred to plugins_loaded so wp_salt() is guaranteed available
add_action('plugins_loaded', function () {
    if (!defined('AI_CHAT_SESSION_SECRET')) {
        define('AI_CHAT_SESSION_SECRET', wp_salt('auth'));
    }
    // Encryption key for stored API keys — separate from session HMAC secret
    if (!defined('AI_CHAT_ENCRYPT_KEY')) {
        define('AI_CHAT_ENCRYPT_KEY', wp_salt('secure_auth'));
    }
}, 1);

// Autoload includes
require_once AI_CHATBOT_PATH . 'includes/class-installer.php';
require_once AI_CHATBOT_PATH . 'includes/class-plugin.php';

// Activation / Deactivation
register_activation_hook(__FILE__, [AI_Chatbot_Installer::class, 'activate']);
register_deactivation_hook(__FILE__, [AI_Chatbot_Installer::class, 'deactivate']);

// Bootstrap
add_action('plugins_loaded', [AI_Chatbot_Plugin::class, 'init']);
