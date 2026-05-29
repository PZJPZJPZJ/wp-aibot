<?php
defined('ABSPATH') || exit;
/**
 * @var WP_Post $post
 */

$meta = AI_Chatbot_CPT_Chatbot::get_meta($post->ID);
$defaults = AI_Chatbot_CPT_Chatbot::get_defaults();
$i18n = !empty($meta['chatbot_i18n']) ? $meta['chatbot_i18n'] : $defaults['chatbot_i18n'];
?>

<div class="ai-chatbot-meta-tabs">
    <nav class="ai-chatbot-tab-nav">
        <button type="button" class="ai-chatbot-tab-btn active" data-tab="basic"><?php esc_html_e('Basic', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="api"><?php esc_html_e('API Provider', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="system"><?php esc_html_e('System Prompt', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="knowledge"><?php esc_html_e('Knowledge', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="memory"><?php esc_html_e('Memory', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="capture"><?php esc_html_e('Lead Capture', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="notify"><?php esc_html_e('Notifications', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="logs"><?php esc_html_e('Logs', 'wp-aibot'); ?></button>
    </nav>

    <!-- Basic Settings -->
    <div class="ai-chatbot-tab-panel active" data-tab="basic">
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_greeting"><?php esc_html_e('Greeting Message', 'wp-aibot'); ?></label>
                <textarea id="chatbot_greeting" name="chatbot_greeting" rows="3" style="max-width:500px;"><?php echo esc_textarea($meta['chatbot_greeting']); ?></textarea>
                <p class="description"><?php esc_html_e('Supports Markdown: **bold**, *italic*, [link text](url)', 'wp-aibot'); ?></p>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_offline_msg"><?php esc_html_e('Offline Message', 'wp-aibot'); ?></label>
                <textarea id="chatbot_offline_msg" name="chatbot_offline_msg" rows="3" style="max-width:500px;"><?php echo esc_textarea($meta['chatbot_offline_msg']); ?></textarea>
                <p class="description"><?php esc_html_e('Supports Markdown: **bold**, *italic*, [link text](url)', 'wp-aibot'); ?></p>
            </div>
        </div>

        <hr style="margin:20px 0;border:none;border-top:1px solid #ddd;">

        <div class="ai-chatbot-field">
            <label for="chatbot_layout_mode"><?php esc_html_e('Layout Mode', 'wp-aibot'); ?></label>
            <select id="chatbot_layout_mode" name="chatbot_layout_mode">
                <option value="inline" <?php selected($meta['chatbot_layout_mode'], 'inline'); ?>><?php esc_html_e('Inline', 'wp-aibot'); ?></option>
                <option value="floating" <?php selected($meta['chatbot_layout_mode'], 'floating'); ?>><?php esc_html_e('Floating Button', 'wp-aibot'); ?></option>
            </select>
        </div>

        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_position"><?php esc_html_e('FAB Position', 'wp-aibot'); ?></label>
                <select id="chatbot_fab_position" name="chatbot_fab_position">
                    <option value="bottom-right" <?php selected($meta['chatbot_fab_position'], 'bottom-right'); ?>><?php esc_html_e('Bottom Right', 'wp-aibot'); ?></option>
                    <option value="bottom-left" <?php selected($meta['chatbot_fab_position'], 'bottom-left'); ?>><?php esc_html_e('Bottom Left', 'wp-aibot'); ?></option>
                    <option value="top-right" <?php selected($meta['chatbot_fab_position'], 'top-right'); ?>><?php esc_html_e('Top Right', 'wp-aibot'); ?></option>
                    <option value="top-left" <?php selected($meta['chatbot_fab_position'], 'top-left'); ?>><?php esc_html_e('Top Left', 'wp-aibot'); ?></option>
                </select>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_distance_x"><?php esc_html_e('Horiz. Distance (px)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_fab_distance_x" name="chatbot_fab_distance_x" value="<?php echo esc_attr($meta['chatbot_fab_distance_x']); ?>" min="0" max="200" style="max-width:100px;" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_distance_y"><?php esc_html_e('Vert. Distance (px)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_fab_distance_y" name="chatbot_fab_distance_y" value="<?php echo esc_attr($meta['chatbot_fab_distance_y']); ?>" min="0" max="200" style="max-width:100px;" />
            </div>
        </div>

        <h4><?php esc_html_e('Color Settings', 'wp-aibot'); ?></h4>
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_popup_color"><?php esc_html_e('Popup/Header Color', 'wp-aibot'); ?></label>
                <input type="color" id="chatbot_popup_color" name="chatbot_popup_color" value="<?php echo esc_attr($meta['chatbot_popup_color'] ?? $meta['chatbot_primary_color']); ?>" style="width:60px;height:36px;padding:2px;cursor:pointer;" />
                <div class="description"><?php esc_html_e('Header background and user message bubbles.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_button_color"><?php esc_html_e('Button Color', 'wp-aibot'); ?></label>
                <input type="color" id="chatbot_button_color" name="chatbot_button_color" value="<?php echo esc_attr($meta['chatbot_button_color'] ?? $meta['chatbot_primary_color']); ?>" style="width:60px;height:36px;padding:2px;cursor:pointer;" />
                <div class="description"><?php esc_html_e('FAB, send button, and submit button.', 'wp-aibot'); ?></div>
            </div>
        </div>

        <div class="ai-chatbot-field">
            <label for="chatbot_fab_icon"><?php esc_html_e('FAB Icon', 'wp-aibot'); ?></label>
            <div style="display:flex;align-items:center;gap:10px;">
                <input type="text" id="chatbot_fab_icon" name="chatbot_fab_icon" value="<?php echo esc_attr($meta['chatbot_fab_icon']); ?>" style="width:200px;" placeholder="fa-comment" />
                <span id="ai-chatbot-fa-preview" style="font-size:24px;width:32px;height:32px;text-align:center;display:flex;align-items:center;justify-content:center;">
                    <?php if (strpos($meta['chatbot_fab_icon'], 'fa-') === 0): ?>
                    <i class="fa <?php echo esc_attr($meta['chatbot_fab_icon']); ?>"></i>
                    <?php elseif (strpos($meta['chatbot_fab_icon'], 'dashicons-') === 0): ?>
                    <span class="dashicons <?php echo esc_attr($meta['chatbot_fab_icon']); ?>"></span>
                    <?php else: ?>
                    <span style="font-size:20px;"><?php echo esc_html($meta['chatbot_fab_icon'] ?: '💬'); ?></span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="description" style="margin-top:6px;"><?php esc_html_e('Enter a Font Awesome 4 class (e.g., fa-comment) or Dashicons class (e.g., dashicons-format-chat). Emoji also supported.', 'wp-aibot'); ?></div>

            <h4 style="margin:16px 0 6px;font-size:12px;text-transform:uppercase;color:#666;"><?php esc_html_e('Font Awesome 4', 'wp-aibot'); ?></h4>
            <div class="ai-chatbot-fa-grid" style="display:flex;flex-wrap:wrap;gap:6px;max-width:400px;">
                <?php
                $fa_icons = ['fa-comment', 'fa-comments', 'fa-commenting', 'fa-comment-o', 'fa-comments-o', 'fa-weixin', 'fa-send', 'fa-envelope', 'fa-phone', 'fa-question-circle', 'fa-smile-o', 'fa-bell', 'fa-globe', 'fa-cog'];
                $current_icon = $meta['chatbot_fab_icon'];
                foreach ($fa_icons as $fa):
                    $active = ($fa === $current_icon) ? ' style="border-color:#2271b1;background:#f0f6fc;"' : '';
                ?>
                <span class="ai-chatbot-fa-option" data-icon="<?php echo esc_attr($fa); ?>"<?php echo $active; ?> title="<?php echo esc_attr($fa); ?>">
                    <i class="fa <?php echo esc_attr($fa); ?>"></i>
                </span>
                <?php endforeach; ?>
            </div>

            <h4 style="margin:12px 0 6px;font-size:12px;text-transform:uppercase;color:#666;"><?php esc_html_e('Dashicons (Fallback)', 'wp-aibot'); ?></h4>
            <div class="ai-chatbot-fa-grid" style="display:flex;flex-wrap:wrap;gap:6px;max-width:400px;">
                <?php
                $dashicons = ['dashicons-format-chat', 'dashicons-format-status', 'dashicons-testimonial', 'dashicons-admin-comments', 'dashicons-email', 'dashicons-phone', 'dashicons-editor-help', 'dashicons-thumbs-up', 'dashicons-star-filled', 'dashicons-heart', 'dashicons-lightbulb', 'dashicons-bell', 'dashicons-admin-users', 'dashicons-feedback'];
                foreach ($dashicons as $d):
                    $active = ($d === $current_icon) ? ' style="border-color:#2271b1;background:#f0f6fc;"' : '';
                ?>
                <span class="ai-chatbot-fa-option" data-icon="<?php echo esc_attr($d); ?>"<?php echo $active; ?> title="<?php echo esc_attr($d); ?>">
                    <span class="dashicons <?php echo esc_attr($d); ?>"></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>

        <h4><?php esc_html_e('FAB Animation', 'wp-aibot'); ?></h4>
        <div class="ai-chatbot-field">
            <label>
                <input type="checkbox" name="chatbot_fab_ripple_enabled" value="1" <?php checked($meta['chatbot_fab_ripple_enabled'], '1'); ?> />
                <?php esc_html_e('Enable ripple animation', 'wp-aibot'); ?>
            </label>
            <div class="description" style="margin-top:2px;"><?php esc_html_e('Continuous expanding rings around the FAB button for attention effect.', 'wp-aibot'); ?></div>
        </div>
        <div class="ai-chatbot-field-row" id="ai-chatbot-ripple-settings">
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_ripple_color"><?php esc_html_e('Ripple Color', 'wp-aibot'); ?></label>
                <input type="color" id="chatbot_fab_ripple_color" name="chatbot_fab_ripple_color" value="<?php echo esc_attr($meta['chatbot_fab_ripple_color']); ?>" style="width:60px;height:36px;padding:2px;cursor:pointer;" />
                <div class="description"><?php esc_html_e('Leave empty to match button color.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_ripple_opacity"><?php esc_html_e('Opacity', 'wp-aibot'); ?></label>
                <input type="range" id="chatbot_fab_ripple_opacity" name="chatbot_fab_ripple_opacity" value="<?php echo esc_attr($meta['chatbot_fab_ripple_opacity']); ?>" min="0.1" max="1" step="0.1" style="width:120px;vertical-align:middle;" />
                <span id="ai-chatbot-ripple-opacity-val" style="margin-left:6px;font-size:13px;"><?php echo esc_html($meta['chatbot_fab_ripple_opacity']); ?></span>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_ripple_speed"><?php esc_html_e('Speed (seconds)', 'wp-aibot'); ?></label>
                <input type="range" id="chatbot_fab_ripple_speed" name="chatbot_fab_ripple_speed" value="<?php echo esc_attr($meta['chatbot_fab_ripple_speed']); ?>" min="0.5" max="3" step="0.1" style="width:120px;vertical-align:middle;" />
                <span id="ai-chatbot-ripple-speed-val" style="margin-left:6px;font-size:13px;"><?php echo esc_html($meta['chatbot_fab_ripple_speed']); ?>s</span>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_ripple_radius"><?php esc_html_e('Radius', 'wp-aibot'); ?></label>
                <input type="range" id="chatbot_fab_ripple_radius" name="chatbot_fab_ripple_radius" value="<?php echo esc_attr($meta['chatbot_fab_ripple_radius']); ?>" min="1.5" max="4" step="0.1" style="width:120px;vertical-align:middle;" />
                <span id="ai-chatbot-ripple-radius-val" style="margin-left:6px;font-size:13px;"><?php echo esc_html($meta['chatbot_fab_ripple_radius']); ?>x</span>
            </div>
        </div>
        <div class="ai-chatbot-field">
            <label>
                <input type="checkbox" name="chatbot_fab_icon_shake" value="1" <?php checked($meta['chatbot_fab_icon_shake'], '1'); ?> />
                <?php esc_html_e('Icon shake', 'wp-aibot'); ?>
            </label>
            <div class="description" style="margin-top:2px;"><?php esc_html_e('Subtle up-and-down vibration of the FAB icon for extra urgency.', 'wp-aibot'); ?></div>
        </div>

        <hr style="margin:8px 0;border:none;border-top:1px solid #eee;">

        <h4 style="margin-top:12px;"><?php esc_html_e('FAB Hint & Default Open', 'wp-aibot'); ?></h4>
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_hint"><?php esc_html_e('Button hint text', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_fab_hint" name="chatbot_fab_hint" value="<?php echo esc_attr($meta['chatbot_fab_hint']); ?>" placeholder="<?php esc_attr_e('Contact us', 'wp-aibot'); ?>" style="max-width:300px;" />
                <div class="description"><?php esc_html_e('Tooltip text next to the FAB button. Leave empty for no hint.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_hint_position"><?php esc_html_e('Hint position', 'wp-aibot'); ?></label>
                <select id="chatbot_fab_hint_position" name="chatbot_fab_hint_position">
                    <option value="right" <?php selected($meta['chatbot_fab_hint_position'], 'right'); ?>><?php esc_html_e('Right', 'wp-aibot'); ?></option>
                    <option value="left" <?php selected($meta['chatbot_fab_hint_position'], 'left'); ?>><?php esc_html_e('Left', 'wp-aibot'); ?></option>
                    <option value="top" <?php selected($meta['chatbot_fab_hint_position'], 'top'); ?>><?php esc_html_e('Top', 'wp-aibot'); ?></option>
                    <option value="bottom" <?php selected($meta['chatbot_fab_hint_position'], 'bottom'); ?>><?php esc_html_e('Bottom', 'wp-aibot'); ?></option>
                </select>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_hint_bg"><?php esc_html_e('Hint background', 'wp-aibot'); ?></label>
                <input type="color" id="chatbot_fab_hint_bg" name="chatbot_fab_hint_bg" value="<?php echo esc_attr($meta['chatbot_fab_hint_bg']); ?>" style="width:60px;height:36px;padding:2px;cursor:pointer;" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_hint_text"><?php esc_html_e('Hint text color', 'wp-aibot'); ?></label>
                <input type="color" id="chatbot_fab_hint_text" name="chatbot_fab_hint_text" value="<?php echo esc_attr($meta['chatbot_fab_hint_text']); ?>" style="width:60px;height:36px;padding:2px;cursor:pointer;" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_hint_font_size"><?php esc_html_e('Hint font size (px)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_fab_hint_font_size" name="chatbot_fab_hint_font_size" value="<?php echo esc_attr($meta['chatbot_fab_hint_font_size']); ?>" min="8" max="48" style="max-width:100px;" />
                <div class="description"><?php esc_html_e('Font size of the hint tooltip text (8-48px).', 'wp-aibot'); ?></div>
            </div>
        </div>
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label>
                    <input type="checkbox" name="chatbot_fab_default_open" value="1" <?php checked($meta['chatbot_fab_default_open'], '1'); ?> />
                    <?php esc_html_e('Open popup by default', 'wp-aibot'); ?>
                </label>
                <div class="description"><?php esc_html_e('Popup opens automatically after page load. Uses local cache to remember close state.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field" id="ai-chatbot-cache-ttl-field">
                <label for="chatbot_open_cache_ttl"><?php esc_html_e('Cache TTL (minutes)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_open_cache_ttl" name="chatbot_open_cache_ttl" value="<?php echo esc_attr($meta['chatbot_open_cache_ttl']); ?>" min="1" max="10080" style="max-width:100px;" />
                <div class="description"><?php esc_html_e('How long to remember the closed state (1-10080 min, default 1440 = 24h).', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field" id="ai-chatbot-open-delay-field">
                <label for="chatbot_fab_open_delay"><?php esc_html_e('Open delay (seconds)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_fab_open_delay" name="chatbot_fab_open_delay" value="<?php echo esc_attr($meta['chatbot_fab_open_delay']); ?>" min="0" max="300" style="max-width:100px;" />
                <div class="description"><?php esc_html_e('Delay in seconds before the popup auto-opens. Set 0 to open immediately.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field" id="ai-chatbot-transition-field">
                <label for="chatbot_popup_transition_duration"><?php esc_html_e('Popup transition (ms)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_popup_transition_duration" name="chatbot_popup_transition_duration" value="<?php echo esc_attr($meta['chatbot_popup_transition_duration']); ?>" min="0" max="1000" style="max-width:100px;" />
                <div class="description"><?php esc_html_e('Fade-in/out duration for the popup (0-1000ms). Set 0 to disable.', 'wp-aibot'); ?></div>
            </div>
        </div>

        <hr style="margin:20px 0;border:none;border-top:1px solid #ddd;">
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_i18n_title"><?php esc_html_e('Title', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_i18n_title" name="chatbot_i18n[title]" placeholder="<?php echo esc_attr($defaults['chatbot_i18n']['title']); ?>" value="<?php echo esc_attr($i18n['title'] ?? ''); ?>" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_i18n_subtitle"><?php esc_html_e('Subtitle', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_i18n_subtitle" name="chatbot_i18n[subtitle]" placeholder="<?php echo esc_attr($defaults['chatbot_i18n']['subtitle']); ?>" value="<?php echo esc_attr($i18n['subtitle'] ?? ''); ?>" />
            </div>
        </div>
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_i18n_input_placeholder"><?php esc_html_e('Input Placeholder', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_i18n_input_placeholder" name="chatbot_i18n[input_placeholder]" placeholder="<?php echo esc_attr($defaults['chatbot_i18n']['input_placeholder']); ?>" value="<?php echo esc_attr($i18n['input_placeholder'] ?? ''); ?>" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_i18n_thinking_text"><?php esc_html_e('Thinking Text', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_i18n_thinking_text" name="chatbot_i18n[thinking_text]" placeholder="<?php echo esc_attr($defaults['chatbot_i18n']['thinking_text']); ?>" value="<?php echo esc_attr($i18n['thinking_text'] ?? ''); ?>" />
            </div>
        </div>

        <hr style="margin:20px 0;border:none;border-top:1px solid #ddd;">

        <h4><?php esc_html_e('Live Preview', 'wp-aibot'); ?></h4>
        <div id="ai-chatbot-preview-area" style="border:2px dashed #ddd;border-radius:8px;padding:16px;margin-bottom:20px;background:#fafafa;max-width:600px;">
            <?php
            if ($post->ID > 0) {
                echo AI_Chatbot_Plugin::render_chatbot_html($post->ID, 'admin_preview');
            } else {
                echo '<p style="color:#999;text-align:center;">' . esc_html__('Save the chatbot first to see the preview.', 'wp-aibot') . '</p>';
            }
            ?>
        </div>
    </div>

    <!-- API Provider -->
    <div class="ai-chatbot-tab-panel" data-tab="api">
        <div class="ai-chatbot-field">
            <label for="chatbot_platform"><?php esc_html_e('Platform', 'wp-aibot'); ?></label>
            <select id="chatbot_platform" name="chatbot_platform">
                <option value="openai" <?php selected($meta['chatbot_platform'], 'openai'); ?>>OpenAI</option>
                <option value="anthropic" <?php selected($meta['chatbot_platform'], 'anthropic'); ?>>Anthropic</option>
            </select>
            <div class="description"><?php esc_html_e('OpenAI-compatible and Anthropic-compatible APIs cover most providers (OpenRouter, DeepSeek, Azure, etc.). Select "OpenAI" for any API that uses the ChatGPT message format; set the API Base URL and Model below accordingly.', 'wp-aibot'); ?></div>
        </div>
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_api_base_url"><?php esc_html_e('API Base URL', 'wp-aibot'); ?></label>
                <input type="url" id="chatbot_api_base_url" name="chatbot_api_base_url" value="<?php echo esc_attr($meta['chatbot_api_base_url']); ?>" />
                <div class="description"><?php esc_html_e('e.g., https://api.openai.com/v1', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_api_key"><?php esc_html_e('API Key', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_api_key" name="chatbot_api_key" value="" placeholder="<?php esc_attr_e('Leave blank to keep current key', 'wp-aibot'); ?>" />
                <div class="description"><?php esc_html_e('Leave blank to keep current key. New value will be encrypted.', 'wp-aibot'); ?></div>
            </div>
        </div>
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_model"><?php esc_html_e('Model', 'wp-aibot'); ?></label>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <select id="chatbot_model" style="flex:1;min-width:120px;">
                        <option value=""><?php esc_html_e('— Select model —', 'wp-aibot'); ?></option>
                        <option value="__custom__"><?php esc_html_e('Custom...', 'wp-aibot'); ?></option>
                    </select>
                </div>
                <input type="hidden" id="chatbot_model_hidden" name="chatbot_model" value="<?php echo esc_attr($meta['chatbot_model']); ?>" />
                <div id="chatbot-model-custom-wrap" style="margin-top:6px;display:none;">
                    <input type="text" id="chatbot_model_custom" value="" placeholder="<?php esc_attr_e('Enter custom model name...', 'wp-aibot'); ?>" style="width:100%;" />
                </div>
                <div class="description"><?php esc_html_e('Available models are fetched automatically from the API. If fetching fails, use Custom... to enter the model name manually.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fallback_model"><?php esc_html_e('Fallback Model', 'wp-aibot'); ?></label>
                <select id="chatbot_fallback_model" style="width:100%;">
                    <option value=""><?php esc_html_e('— None (disabled) —', 'wp-aibot'); ?></option>
                    <option value="__custom__"><?php esc_html_e('Custom...', 'wp-aibot'); ?></option>
                </select>
                <input type="hidden" id="chatbot_fallback_model_hidden" name="chatbot_fallback_model" value="<?php echo esc_attr($meta['chatbot_fallback_model']); ?>" />
                <div id="chatbot-fallback-model-custom-wrap" style="margin-top:6px;display:none;">
                    <input type="text" id="chatbot_fallback_model_custom" value="" placeholder="<?php esc_attr_e('Enter custom model name...', 'wp-aibot'); ?>" style="width:100%;" />
                </div>
                <div class="description"><?php esc_html_e('Leave empty to disable fallback. When the primary model fails, the fallback is tried automatically.', 'wp-aibot'); ?></div>
            </div>
        </div>
        <div class="ai-chatbot-field-row" style="margin-top:12px;">
            <div class="ai-chatbot-field">
                <label for="chatbot_max_tokens"><?php esc_html_e('Max Output Tokens', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_max_tokens" name="chatbot_max_tokens" value="<?php echo esc_attr($meta['chatbot_max_tokens']); ?>" min="1" max="32000" />
                <div class="description"><?php esc_html_e('Maximum output tokens for the response. 1 token ≈ 0.75 words.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_temperature"><?php esc_html_e('Temperature', 'wp-aibot'); ?></label>
                <div style="display:flex;align-items:center;gap:8px;">
                    <input type="range" id="chatbot_temperature" name="chatbot_temperature" value="<?php echo esc_attr($meta['chatbot_temperature']); ?>" step="0.1" min="0" max="2" style="width:140px;vertical-align:middle;" />
                    <span id="ai-chatbot-temp-val" style="font-size:13px;min-width:24px;"><?php echo esc_html($meta['chatbot_temperature']); ?></span>
                </div>
                <div class="description"><?php esc_html_e('Randomness: 0 = deterministic, 2 = very random. Default 0.2.', 'wp-aibot'); ?></div>
            </div>
        </div>
    </div>
    <div class="ai-chatbot-tab-panel" data-tab="system">
        <div class="ai-chatbot-field">
            <label for="chatbot_system_prompt"><?php esc_html_e('① Background Info', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:4px;"><?php esc_html_e('Company/product background. The AI will answer visitor questions based on this information.', 'wp-aibot'); ?></div>
            <textarea id="chatbot_system_prompt" name="chatbot_system_prompt" rows="12" style="font-family:monospace;"><?php echo esc_textarea($meta['chatbot_system_prompt']); ?></textarea>
        </div>
        <div class="ai-chatbot-field">
            <label for="chatbot_ai_rules"><?php esc_html_e('② AI Behavior Rules', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:4px;"><?php esc_html_e('Security rules to prevent abuse and prompt injection. The AI always follows these rules over any conflicting user instructions.', 'wp-aibot'); ?></div>
            <textarea id="chatbot_ai_rules" name="chatbot_ai_rules" rows="8" style="font-family:monospace;"><?php echo esc_textarea($meta['chatbot_ai_rules'] ?? AI_Chatbot_CPT_Chatbot::default_ai_rules()); ?></textarea>
        </div>
        <div class="ai-chatbot-field">
            <label><?php esc_html_e('③ Lead Collection Items', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('Define what visitor information the AI should collect. The field name is auto-prefixed with "lead." when sent to the AI.', 'wp-aibot'); ?></div>
            <input type="hidden" name="chatbot_json_schema_sentinel" value="1" />
            <div id="js-schema-fields">
                <?php
                $schema_items = $meta['chatbot_json_schema'] ?? [];
                if (is_string($schema_items)) {
                    // Backward compat: convert old string to array
                    $schema_items = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_json_schema'];
                } elseif (empty($schema_items)) {
                    // Only use defaults when no meta has ever been saved
                    $stored = get_post_meta($post->ID, 'chatbot_json_schema', true);
                    if ($stored === '') {
                        $schema_items = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_json_schema'];
                    }
                }
                // Filter out auto-managed fields (answer and summary are always injected; should_notify_sales is deprecated)
                $schema_items = array_values(array_filter($schema_items, function($item) {
                    $path = is_array($item) ? ($item['path'] ?? '') : '';
                    return $path !== 'should_notify_sales' && $path !== 'answer' && $path !== 'summary';
                }));
                $idx = 0;
                foreach ($schema_items as $item):
                    $item = (array) $item;
                ?>
                <div class="js-schema-row" data-index="<?php echo $idx; ?>">
                    <div class="js-schema-fields-row">
                        <div class="js-schema-field-path">
                            <label><?php esc_html_e('Field Name', 'wp-aibot'); ?></label>
                            <div style="display:flex;align-items:center;">
                                <code style="margin-right:4px;flex-shrink:0;">lead.</code>
                                <input type="text" name="chatbot_json_schema[<?php echo $idx; ?>][path]" value="<?php echo esc_attr(preg_replace('/^lead\./', '', $item['path'] ?? '')); ?>" placeholder="e.g. email" style="flex:1;min-width:0;" />
                            </div>
                        </div>
                        <div class="js-schema-field-type">
                            <label><?php esc_html_e('Type', 'wp-aibot'); ?></label>
                            <select name="chatbot_json_schema[<?php echo $idx; ?>][type]" style="width:100%;">
                                <option value="string" <?php selected($item['type'] ?? '', 'string'); ?>><?php esc_html_e('String', 'wp-aibot'); ?></option>
                                <option value="boolean" <?php selected($item['type'] ?? '', 'boolean'); ?>><?php esc_html_e('Boolean', 'wp-aibot'); ?></option>
                                <option value="number" <?php selected($item['type'] ?? '', 'number'); ?>><?php esc_html_e('Number', 'wp-aibot'); ?></option>
                                <option value="enum" <?php selected($item['type'] ?? '', 'enum'); ?>><?php esc_html_e('Enum', 'wp-aibot'); ?></option>
                            </select>
                        </div>
                        <div class="js-schema-field-enum js-schema-dependent" data-dep-type="enum" style="<?php echo ($item['type'] ?? '') === 'enum' ? '' : 'display:none;'; ?>">
                            <label><?php esc_html_e('Enum Values', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_json_schema[<?php echo $idx; ?>][enum_values]" value="<?php echo esc_attr($item['enum_values'] ?? ''); ?>" placeholder="A|B|C|D" style="width:100%;" />
                        </div>
                        <div class="js-schema-field-desc">
                            <label><?php esc_html_e('Description', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_json_schema[<?php echo $idx; ?>][description]" value="<?php echo esc_attr($item['description'] ?? ''); ?>" placeholder="What this field represents" style="width:100%;" />
                        </div>
                        <div class="js-schema-field-req">
                            <label>&nbsp;</label>
                            <label class="js-schema-req-label">
                                <input type="checkbox" name="chatbot_json_schema[<?php echo $idx; ?>][required]" value="1" <?php checked(!empty($item['required'])); ?> />
                                <?php esc_html_e('Required', 'wp-aibot'); ?>
                            </label>
                        </div>
                        <div class="js-schema-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-schema-remove-row button button-small" title="<?php esc_attr_e('Remove field', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
                <?php $idx++; endforeach; ?>
            </div>
            <template id="js-schema-row-tpl">
                <div class="js-schema-row" data-index="__IDX__">
                    <div class="js-schema-fields-row">
                        <div class="js-schema-field-path">
                            <label><?php esc_html_e('Field Name', 'wp-aibot'); ?></label>
                            <div style="display:flex;align-items:center;">
                                <code style="margin-right:4px;flex-shrink:0;">lead.</code>
                                <input type="text" name="chatbot_json_schema[__IDX__][path]" value="" placeholder="e.g. email" style="flex:1;min-width:0;" />
                            </div>
                        </div>
                        <div class="js-schema-field-type">
                            <label><?php esc_html_e('Type', 'wp-aibot'); ?></label>
                            <select name="chatbot_json_schema[__IDX__][type]" style="width:100%;">
                                <option value="string"><?php esc_html_e('String', 'wp-aibot'); ?></option>
                                <option value="boolean"><?php esc_html_e('Boolean', 'wp-aibot'); ?></option>
                                <option value="number"><?php esc_html_e('Number', 'wp-aibot'); ?></option>
                                <option value="enum"><?php esc_html_e('Enum', 'wp-aibot'); ?></option>
                            </select>
                        </div>
                        <div class="js-schema-field-enum js-schema-dependent" data-dep-type="enum" style="display:none;">
                            <label><?php esc_html_e('Enum Values', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_json_schema[__IDX__][enum_values]" value="" placeholder="A|B|C|D" style="width:100%;" />
                        </div>
                        <div class="js-schema-field-desc">
                            <label><?php esc_html_e('Description', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_json_schema[__IDX__][description]" value="" placeholder="What this field represents" style="width:100%;" />
                        </div>
                        <div class="js-schema-field-req">
                            <label>&nbsp;</label>
                            <label class="js-schema-req-label">
                                <input type="checkbox" name="chatbot_json_schema[__IDX__][required]" value="1" />
                                <?php esc_html_e('Required', 'wp-aibot'); ?>
                            </label>
                        </div>
                        <div class="js-schema-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-schema-remove-row button button-small" title="<?php esc_attr_e('Remove field', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <div style="margin-top:8px;">
                <button type="button" class="js-schema-add-row button">+ <?php esc_html_e('Add Field', 'wp-aibot'); ?></button>
            </div>
        </div>
    </div>

    <!-- Knowledge -->
    <div class="ai-chatbot-tab-panel" data-tab="knowledge">
        <div class="ai-chatbot-field">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <label style="margin:0;"><?php esc_html_e('Knowledge Documents', 'wp-aibot'); ?></label>
                <?php
                $docs = get_posts(['post_type' => 'ai_knowledge', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'no_found_rows' => true]);
                $selected_ids = (array) ($meta['chatbot_knowledge_ids'] ?? []);
                $total_docs = count($docs);
                $selected_count = count(array_intersect($selected_ids, wp_list_pluck($docs, 'ID')));
                if ($total_docs > 0) {
                    echo '<span class="ai-chatbot-badge" style="font-size:11px;background:#e0e0e0;padding:2px 8px;border-radius:10px;color:#555;">' . esc_html($selected_count) . ' / ' . esc_html($total_docs) . ' ' . esc_html__('selected', 'wp-aibot') . '</span>';
                }
                ?>
            </div>
            <div class="ai-chatbot-checkbox-list" style="border:1px solid #e0e0e0;border-radius:4px;padding:4px 0;max-height:320px;overflow-y:auto;">
                <?php
                if (empty($docs)) {
                    echo '<div style="padding:16px;text-align:center;color:#999;">' . esc_html__('No knowledge documents yet. Create one under Knowledge Base.', 'wp-aibot') . '</div>';
                }
                foreach ($docs as $doc) {
                    echo '<label class="ai-chatbot-checkbox-item" style="display:flex;align-items:center;padding:6px 10px;margin:2px 4px;border-radius:3px;cursor:pointer;transition:background 0.1s;">';
                    echo '<input type="checkbox" name="chatbot_knowledge_ids[]" value="' . esc_attr($doc->ID) . '" ' . checked(in_array($doc->ID, $selected_ids), true, false) . ' style="margin-right:8px;">';
                    echo '<span>' . esc_html($doc->post_title) . '</span>';
                    echo '</label>';
                }
                ?>
            </div>
            <div class="description" style="margin-top:8px;"><?php esc_html_e('Tick the documents you want the AI to reference during conversations.', 'wp-aibot'); ?></div>
        </div>
    </div>

    <!-- Memory -->
    <div class="ai-chatbot-tab-panel" data-tab="memory">
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_max_history"><?php esc_html_e('Max History Rounds', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_max_history" name="chatbot_max_history" value="<?php echo esc_attr($meta['chatbot_max_history']); ?>" min="0" max="100" />
                <div class="description"><?php esc_html_e('Number of past conversation rounds sent to AI as context (0 = no history).', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_session_ttl"><?php esc_html_e('Session TTL (minutes)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_session_ttl" name="chatbot_session_ttl" value="<?php echo esc_attr($meta['chatbot_session_ttl']); ?>" min="1" max="1440" />
                <div class="description"><?php esc_html_e('Inactivity timeout. After this period a new conversation starts (old one kept as history).', 'wp-aibot'); ?></div>
            </div>
        </div>
        <div class="ai-chatbot-variables" style="margin-top:12px;">
            <strong><?php esc_html_e('Session Info', 'wp-aibot'); ?></strong><br>
            <?php esc_html_e('Each visitor gets a unique UUID stored in browser localStorage. Session ID format:', 'wp-aibot'); ?>
            <code>sess_{md5(visitor_id + chatbot_id)}</code><br>
            <?php esc_html_e('When a session expires, a new conversation is created automatically.', 'wp-aibot'); ?>
        </div>
    </div>

    <!-- Lead Capture -->
    <div class="ai-chatbot-tab-panel" data-tab="capture">
        <div class="ai-chatbot-field">
            <label>
                <input type="checkbox" name="chatbot_lead_capture_enabled" value="1" <?php checked($meta['chatbot_lead_capture_enabled'] ?? '1', '1'); ?> />
                <?php esc_html_e('Enable Lead Capture Form', 'wp-aibot'); ?>
            </label>
            <div class="description" style="margin-top:4px;"><?php esc_html_e('When enabled, a contact form popup appears when all rules below are met. The visitor\'s submission is sent to the AI as a chat message.', 'wp-aibot'); ?></div>
        </div>
        <div class="ai-chatbot-field">
            <label><?php esc_html_e('Form Fields', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('Define the input fields shown in the contact form. The "name" value is used as the field key when sending to AI.', 'wp-aibot'); ?></div>
            <input type="hidden" name="chatbot_lead_fields_sentinel" value="1" />
            <div id="js-lead-fields">
                <?php
                $lead_fields = $meta['chatbot_lead_fields'] ?? [];
                if (is_string($lead_fields)) {
                    $lead_fields = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_lead_fields'];
                }
                if (empty($lead_fields)) {
                    $lead_fields = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_lead_fields'];
                }
                $lidx = 0;
                foreach ($lead_fields as $lf):
                    $lf = (array) $lf;
                ?>
                <div class="js-lead-field-row" data-index="<?php echo $lidx; ?>">
                    <div class="js-notify-fields-row">
                        <div class="js-schema-field-path" style="flex:1;">
                            <label><?php esc_html_e('Name', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_fields[<?php echo $lidx; ?>][name]" value="<?php echo esc_attr($lf['name'] ?? ''); ?>" placeholder="email" style="width:100%;" />
                        </div>
                        <div class="js-schema-field-desc" style="flex:1.5;">
                            <label><?php esc_html_e('Placeholder', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_fields[<?php echo $lidx; ?>][placeholder]" value="<?php echo esc_attr($lf['placeholder'] ?? ''); ?>" placeholder="Email Address" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-actions" style="flex:0 0 auto;">
                            <label>&nbsp;</label>
                            <button type="button" class="js-lead-field-remove button button-small" title="<?php esc_attr_e('Remove field', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
                <?php $lidx++; endforeach; ?>
            </div>
            <template id="js-lead-field-tpl">
                <div class="js-lead-field-row" data-index="__LIDX__">
                    <div class="js-notify-fields-row">
                        <div class="js-schema-field-path" style="flex:1;">
                            <label><?php esc_html_e('Name', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_fields[__LIDX__][name]" value="" placeholder="email" style="width:100%;" />
                        </div>
                        <div class="js-schema-field-desc" style="flex:1.5;">
                            <label><?php esc_html_e('Placeholder', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_fields[__LIDX__][placeholder]" value="" placeholder="Email Address" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-actions" style="flex:0 0 auto;">
                            <label>&nbsp;</label>
                            <button type="button" class="js-lead-field-remove button button-small" title="<?php esc_attr_e('Remove field', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <div style="margin-top:8px;">
                <button type="button" class="js-lead-field-add button">+ <?php esc_html_e('Add Field', 'wp-aibot'); ?></button>
            </div>
        </div>
        <div class="ai-chatbot-field">
            <label><?php esc_html_e('Trigger Rules (OR between groups, AND within each group)', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('Define rule groups. Rules are OR\'d — any matching group triggers the form. Conditions within a group are AND\'d — all must match for that group to fire.', 'wp-aibot'); ?></div>
            <input type="hidden" name="chatbot_lead_capture_rules_sentinel" value="1" />
            <div id="js-capture-rules-fields">
                <?php
                $capture_groups = $meta['chatbot_lead_capture_rules'] ?? [];
                if (is_string($capture_groups)) {
                    $capture_groups = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_lead_capture_rules'];
                }
                if (empty($capture_groups)) {
                    $capture_groups = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_lead_capture_rules'];
                }
                $gidx = 0;
                foreach ($capture_groups as $group):
                    $group = (array) $group;
                ?>
                <div class="ai-chatbot-rule-group" data-group-index="<?php echo $gidx; ?>">
                    <?php if ($gidx > 0): ?>
                    <div class="ai-chatbot-rule-group-or"><?php esc_html_e('OR', 'wp-aibot'); ?></div>
                    <?php endif; ?>
                    <div class="ai-chatbot-rule-group-body">
                        <div class="ai-chatbot-rule-group-header">
                            <strong><?php printf(esc_html__('Rule Group %d', 'wp-aibot'), $gidx + 1); ?></strong>
                        </div>
                        <div class="ai-chatbot-rule-group-conditions">
                            <?php $cidx = 0; foreach ($group as $condition):
                                $condition = (array) $condition;
                            ?>
                            <div class="ai-chatbot-condition-row" data-cond-index="<?php echo $cidx; ?>">
                                <div class="js-notify-fields-row">
                                    <div class="js-notify-field-path">
                                        <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                                        <div style="display:flex;align-items:center;">
                                            <code style="margin-right:4px;flex-shrink:0;">lead.</code>
                                            <input type="text" name="chatbot_lead_capture_rules[<?php echo $gidx; ?>][<?php echo $cidx; ?>][field]" value="<?php echo esc_attr(preg_replace('/^lead\./', '', $condition['field'] ?? '')); ?>" placeholder="e.g. lead_score" style="flex:1;min-width:0;" />
                                        </div>
                                    </div>
                                    <div class="js-notify-field-operator">
                                        <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                                        <select name="chatbot_lead_capture_rules[<?php echo $gidx; ?>][<?php echo $cidx; ?>][operator]" style="width:100%;">
                                            <option value="eq" <?php selected($condition['operator'] ?? '', 'eq'); ?>><?php esc_html_e('equals (=)', 'wp-aibot'); ?></option>
                                            <option value="neq" <?php selected($condition['operator'] ?? '', 'neq'); ?>><?php esc_html_e('not equals (!=)', 'wp-aibot'); ?></option>
                                            <option value="in" <?php selected($condition['operator'] ?? '', 'in'); ?>><?php esc_html_e('in (comma-separated)', 'wp-aibot'); ?></option>
                                            <option value="contains" <?php selected($condition['operator'] ?? '', 'contains'); ?>><?php esc_html_e('contains', 'wp-aibot'); ?></option>
                                            <option value="gt" <?php selected($condition['operator'] ?? '', 'gt'); ?>><?php esc_html_e('greater than (>)', 'wp-aibot'); ?></option>
                                            <option value="lt" <?php selected($condition['operator'] ?? '', 'lt'); ?>><?php esc_html_e('less than (<)', 'wp-aibot'); ?></option>
                                            <option value="gte" <?php selected($condition['operator'] ?? '', 'gte'); ?>><?php esc_html_e('>=', 'wp-aibot'); ?></option>
                                            <option value="lte" <?php selected($condition['operator'] ?? '', 'lte'); ?>><?php esc_html_e('<=', 'wp-aibot'); ?></option>
                                            <option value="empty" <?php selected($condition['operator'] ?? '', 'empty'); ?>><?php esc_html_e('is empty', 'wp-aibot'); ?></option>
                                            <option value="not_empty" <?php selected($condition['operator'] ?? '', 'not_empty'); ?>><?php esc_html_e('is not empty', 'wp-aibot'); ?></option>
                                            <option value="changed" <?php selected($condition['operator'] ?? '', 'changed'); ?>><?php esc_html_e('changed (changes to any specified value)', 'wp-aibot'); ?></option>
                                        </select>
                                    </div>
                                    <div class="js-notify-field-value">
                                        <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                                        <input type="text" name="chatbot_lead_capture_rules[<?php echo $gidx; ?>][<?php echo $cidx; ?>][value]" value="<?php echo esc_attr($condition['value'] ?? ''); ?>" style="width:100%;" />
                                    </div>
                                    <div class="js-notify-field-actions">
                                        <label>&nbsp;</label>
                                        <button type="button" class="js-capture-remove-condition button button-small" title="<?php esc_attr_e('Remove condition', 'wp-aibot'); ?>">✕</button>
                                    </div>
                                </div>
                            </div>
                            <?php $cidx++; endforeach; ?>
                        </div>
                        <div class="ai-chatbot-rule-group-actions">
                            <button type="button" class="js-capture-add-condition button button-small">+ <?php esc_html_e('Add Condition', 'wp-aibot'); ?></button>
                            <button type="button" class="js-capture-remove-group button button-small"><?php esc_html_e('Remove Group', 'wp-aibot'); ?></button>
                        </div>
                    </div>
                </div>
                <?php $gidx++; endforeach; ?>
            </div>
            <template id="js-capture-group-tpl">
                <div class="ai-chatbot-rule-group" data-group-index="__GIDX__">
                    <div class="ai-chatbot-rule-group-or"><?php esc_html_e('OR', 'wp-aibot'); ?></div>
                    <div class="ai-chatbot-rule-group-body">
                        <div class="ai-chatbot-rule-group-header">
                            <strong><?php esc_html_e('New Rule Group', 'wp-aibot'); ?></strong>
                        </div>
                        <div class="ai-chatbot-rule-group-conditions"></div>
                        <div class="ai-chatbot-rule-group-actions">
                            <button type="button" class="js-capture-add-condition button button-small">+ <?php esc_html_e('Add Condition', 'wp-aibot'); ?></button>
                            <button type="button" class="js-capture-remove-group button button-small"><?php esc_html_e('Remove Group', 'wp-aibot'); ?></button>
                        </div>
                    </div>
                </div>
            </template>
            <template id="js-capture-condition-tpl">
                <div class="ai-chatbot-condition-row" data-cond-index="__CIDX__">
                    <div class="js-notify-fields-row">
                        <div class="js-notify-field-path">
                            <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                            <div style="display:flex;align-items:center;">
                                <code style="margin-right:4px;flex-shrink:0;">lead.</code>
                                <input type="text" name="chatbot_lead_capture_rules[__GIDX__][__CIDX__][field]" value="" placeholder="e.g. lead_score" style="flex:1;min-width:0;" />
                            </div>
                        </div>
                        <div class="js-notify-field-operator">
                            <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                            <select name="chatbot_lead_capture_rules[__GIDX__][__CIDX__][operator]" style="width:100%;">
                                <option value="eq"><?php esc_html_e('equals (=)', 'wp-aibot'); ?></option>
                                <option value="neq"><?php esc_html_e('not equals (!=)', 'wp-aibot'); ?></option>
                                <option value="in"><?php esc_html_e('in (comma-separated)', 'wp-aibot'); ?></option>
                                <option value="contains"><?php esc_html_e('contains', 'wp-aibot'); ?></option>
                                <option value="gt"><?php esc_html_e('greater than (>)', 'wp-aibot'); ?></option>
                                <option value="lt"><?php esc_html_e('less than (<)', 'wp-aibot'); ?></option>
                                <option value="gte"><?php esc_html_e('>=', 'wp-aibot'); ?></option>
                                <option value="lte"><?php esc_html_e('<=', 'wp-aibot'); ?></option>
                                <option value="empty"><?php esc_html_e('is empty', 'wp-aibot'); ?></option>
                                <option value="not_empty"><?php esc_html_e('is not empty', 'wp-aibot'); ?></option>
                                <option value="changed"><?php esc_html_e('changed (changes to any specified value)', 'wp-aibot'); ?></option>
                            </select>
                        </div>
                        <div class="js-notify-field-value">
                            <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_capture_rules[__GIDX__][__CIDX__][value]" value="" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-capture-remove-condition button button-small" title="<?php esc_attr_e('Remove condition', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <div style="margin-top:8px;">
                <button type="button" class="js-capture-add-group button">+ <?php esc_html_e('Add Rule Group', 'wp-aibot'); ?></button>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div class="ai-chatbot-tab-panel" data-tab="notify">
        <div class="ai-chatbot-field">
            <label>
                <input type="checkbox" name="chatbot_notify_enabled" value="1" <?php checked($meta['chatbot_notify_enabled'], '1'); ?> />
                <?php esc_html_e('Enable Notifications', 'wp-aibot'); ?>
            </label>
        </div>
        <div class="ai-chatbot-field">
            <label for="chatbot_notify_email"><?php esc_html_e('Notification Email', 'wp-aibot'); ?></label>
            <input type="email" id="chatbot_notify_email" name="chatbot_notify_email" value="<?php echo esc_attr($meta['chatbot_notify_email']); ?>" />
            <div class="description"><?php esc_html_e('Email address that receives lead notifications. Supports any WordPress mailer (SMTP, FluentSMTP, etc.).', 'wp-aibot'); ?></div>
        </div>
        <div class="ai-chatbot-field">
            <label for="chatbot_notify_webhook"><?php esc_html_e('Wecom Webhook', 'wp-aibot'); ?></label>
            <input type="url" id="chatbot_notify_webhook" name="chatbot_notify_webhook" value="<?php echo esc_attr($meta['chatbot_notify_webhook']); ?>" />
            <p class="description" style="margin-top:6px;">
                <?php esc_html_e('Push lead notifications to a WeCom (企业微信) group chat.', 'wp-aibot'); ?>
                <a href="#" onclick="return false;" style="color:#2271b1;text-decoration:underline;cursor:pointer;" id="js-wecom-guide-toggle"><?php esc_html_e('如何获取 Webhook？', 'wp-aibot'); ?></a>
            </p>
            <div id="js-wecom-guide" style="display:none;margin-top:8px;padding:12px;background:#f6f7f7;border:1px solid #dcdcde;border-radius:4px;font-size:13px;line-height:1.6;">
                <ol style="margin:0;padding-left:18px;">
                    <li><?php esc_html_e('打开企业微信，进入目标群聊。', 'wp-aibot'); ?></li>
                    <li><?php esc_html_e('点击群聊右上角的"..." → "群机器人" → "添加机器人"。', 'wp-aibot'); ?></li>
                    <li><?php esc_html_e('创建一个新机器人，复制其 Webhook 地址。', 'wp-aibot'); ?></li>
                    <li><?php esc_html_e('将 Webhook 地址粘贴到上方输入框中。', 'wp-aibot'); ?></li>
                </ol>
                <p style="margin:8px 0 0 0;color:#d63638;">
                    <strong><?php esc_html_e('安全提醒：', 'wp-aibot'); ?></strong>
                    <?php esc_html_e('Webhook 地址包含密钥，切勿提交到公开代码仓库或分享给他人，否则他人可滥用该地址向群聊推送垃圾消息。', 'wp-aibot'); ?>
                </p>
            </div>
        </div>
        <div class="ai-chatbot-field">
            <label><?php esc_html_e('Notification Mode', 'wp-aibot'); ?></label>
            <div style="margin-top:6px;">
                <label style="display:block;margin-bottom:6px;">
                    <input type="radio" name="chatbot_notify_mode" value="always" <?php checked($meta['chatbot_notify_mode'], 'always'); ?> />
                    <?php esc_html_e('Every match — send notification every time rules are met', 'wp-aibot'); ?>
                </label>
                <label style="display:block;">
                    <input type="radio" name="chatbot_notify_mode" value="once" <?php checked($meta['chatbot_notify_mode'], 'once'); ?> />
                    <?php esc_html_e('Send once — notify only once per conversation', 'wp-aibot'); ?>
                </label>
            </div>
        </div>
        <div class="ai-chatbot-field">
            <label><?php esc_html_e('Notification Rules (OR between groups, AND within each group)', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('Define rule groups. Any matching group triggers the notification. Conditions within a group all must match.', 'wp-aibot'); ?></div>
            <input type="hidden" name="chatbot_notify_rules_sentinel" value="1" />
            <div id="js-notify-rules-fields">
                <?php
                $notify_groups = $meta['chatbot_notify_rules'] ?? [];
                if (is_string($notify_groups)) {
                    $notify_groups = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_notify_rules'];
                }
                if (empty($notify_groups)) {
                    $notify_groups = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_notify_rules'];
                }
                $ngidx = 0;
                foreach ($notify_groups as $group):
                    $group = (array) $group;
                ?>
                <div class="ai-chatbot-rule-group" data-group-index="<?php echo $ngidx; ?>">
                    <?php if ($ngidx > 0): ?>
                    <div class="ai-chatbot-rule-group-or"><?php esc_html_e('OR', 'wp-aibot'); ?></div>
                    <?php endif; ?>
                    <div class="ai-chatbot-rule-group-body">
                        <div class="ai-chatbot-rule-group-header">
                            <strong><?php printf(esc_html__('Rule Group %d', 'wp-aibot'), $ngidx + 1); ?></strong>
                        </div>
                        <div class="ai-chatbot-rule-group-conditions">
                            <?php $ncidx = 0; foreach ($group as $condition):
                                $condition = (array) $condition;
                            ?>
                            <div class="ai-chatbot-condition-row" data-cond-index="<?php echo $ncidx; ?>">
                                <div class="js-notify-fields-row">
                                    <div class="js-notify-field-path">
                                        <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                                        <div style="display:flex;align-items:center;">
                                            <code style="margin-right:4px;flex-shrink:0;">lead.</code>
                                            <input type="text" name="chatbot_notify_rules[<?php echo $ngidx; ?>][<?php echo $ncidx; ?>][field]" value="<?php echo esc_attr(preg_replace('/^lead\./', '', $condition['field'] ?? '')); ?>" placeholder="e.g. lead_score" style="flex:1;min-width:0;" />
                                        </div>
                                    </div>
                                    <div class="js-notify-field-operator">
                                        <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                                        <select name="chatbot_notify_rules[<?php echo $ngidx; ?>][<?php echo $ncidx; ?>][operator]" style="width:100%;">
                                            <option value="eq" <?php selected($condition['operator'] ?? '', 'eq'); ?>><?php esc_html_e('equals (=)', 'wp-aibot'); ?></option>
                                            <option value="neq" <?php selected($condition['operator'] ?? '', 'neq'); ?>><?php esc_html_e('not equals (!=)', 'wp-aibot'); ?></option>
                                            <option value="in" <?php selected($condition['operator'] ?? '', 'in'); ?>><?php esc_html_e('in (comma-separated)', 'wp-aibot'); ?></option>
                                            <option value="contains" <?php selected($condition['operator'] ?? '', 'contains'); ?>><?php esc_html_e('contains', 'wp-aibot'); ?></option>
                                            <option value="gt" <?php selected($condition['operator'] ?? '', 'gt'); ?>><?php esc_html_e('greater than (>)', 'wp-aibot'); ?></option>
                                            <option value="lt" <?php selected($condition['operator'] ?? '', 'lt'); ?>><?php esc_html_e('less than (<)', 'wp-aibot'); ?></option>
                                            <option value="gte" <?php selected($condition['operator'] ?? '', 'gte'); ?>><?php esc_html_e('>=', 'wp-aibot'); ?></option>
                                            <option value="lte" <?php selected($condition['operator'] ?? '', 'lte'); ?>><?php esc_html_e('<=', 'wp-aibot'); ?></option>
                                            <option value="empty" <?php selected($condition['operator'] ?? '', 'empty'); ?>><?php esc_html_e('is empty', 'wp-aibot'); ?></option>
                                            <option value="not_empty" <?php selected($condition['operator'] ?? '', 'not_empty'); ?>><?php esc_html_e('is not empty', 'wp-aibot'); ?></option>
                                            <option value="changed" <?php selected($condition['operator'] ?? '', 'changed'); ?>><?php esc_html_e('changed (changes to any specified value)', 'wp-aibot'); ?></option>
                                        </select>
                                    </div>
                                    <div class="js-notify-field-value">
                                        <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                                        <input type="text" name="chatbot_notify_rules[<?php echo $ngidx; ?>][<?php echo $ncidx; ?>][value]" value="<?php echo esc_attr(is_array($condition['value'] ?? '') ? implode(',', $condition['value']) : ($condition['value'] ?? '')); ?>" style="width:100%;" />
                                    </div>
                                    <div class="js-notify-field-actions">
                                        <label>&nbsp;</label>
                                        <button type="button" class="js-notify-remove-condition button button-small" title="<?php esc_attr_e('Remove condition', 'wp-aibot'); ?>">✕</button>
                                    </div>
                                </div>
                            </div>
                            <?php $ncidx++; endforeach; ?>
                        </div>
                        <div class="ai-chatbot-rule-group-actions">
                            <button type="button" class="js-notify-add-condition button button-small">+ <?php esc_html_e('Add Condition', 'wp-aibot'); ?></button>
                            <button type="button" class="js-notify-remove-group button button-small"><?php esc_html_e('Remove Group', 'wp-aibot'); ?></button>
                        </div>
                    </div>
                </div>
                <?php $ngidx++; endforeach; ?>
            </div>
            <template id="js-notify-group-tpl">
                <div class="ai-chatbot-rule-group" data-group-index="__NGIDX__">
                    <div class="ai-chatbot-rule-group-or"><?php esc_html_e('OR', 'wp-aibot'); ?></div>
                    <div class="ai-chatbot-rule-group-body">
                        <div class="ai-chatbot-rule-group-header">
                            <strong><?php esc_html_e('New Rule Group', 'wp-aibot'); ?></strong>
                        </div>
                        <div class="ai-chatbot-rule-group-conditions"></div>
                        <div class="ai-chatbot-rule-group-actions">
                            <button type="button" class="js-notify-add-condition button button-small">+ <?php esc_html_e('Add Condition', 'wp-aibot'); ?></button>
                            <button type="button" class="js-notify-remove-group button button-small"><?php esc_html_e('Remove Group', 'wp-aibot'); ?></button>
                        </div>
                    </div>
                </div>
            </template>
            <template id="js-notify-condition-tpl">
                <div class="ai-chatbot-condition-row" data-cond-index="__NCIDX__">
                    <div class="js-notify-fields-row">
                        <div class="js-notify-field-path">
                            <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                            <div style="display:flex;align-items:center;">
                                <code style="margin-right:4px;flex-shrink:0;">lead.</code>
                                <input type="text" name="chatbot_notify_rules[__NGIDX__][__NCIDX__][field]" value="" placeholder="e.g. lead_score" style="flex:1;min-width:0;" />
                            </div>
                        </div>
                        <div class="js-notify-field-operator">
                            <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                            <select name="chatbot_notify_rules[__NGIDX__][__NCIDX__][operator]" style="width:100%;">
                                <option value="eq"><?php esc_html_e('equals (=)', 'wp-aibot'); ?></option>
                                <option value="neq"><?php esc_html_e('not equals (!=)', 'wp-aibot'); ?></option>
                                <option value="in"><?php esc_html_e('in (comma-separated)', 'wp-aibot'); ?></option>
                                <option value="contains"><?php esc_html_e('contains', 'wp-aibot'); ?></option>
                                <option value="gt"><?php esc_html_e('greater than (>)', 'wp-aibot'); ?></option>
                                <option value="lt"><?php esc_html_e('less than (<)', 'wp-aibot'); ?></option>
                                <option value="gte"><?php esc_html_e('>=', 'wp-aibot'); ?></option>
                                <option value="lte"><?php esc_html_e('<=', 'wp-aibot'); ?></option>
                                <option value="empty"><?php esc_html_e('is empty', 'wp-aibot'); ?></option>
                                <option value="not_empty"><?php esc_html_e('is not empty', 'wp-aibot'); ?></option>
                                <option value="changed"><?php esc_html_e('changed (changes to any specified value)', 'wp-aibot'); ?></option>
                            </select>
                        </div>
                        <div class="js-notify-field-value">
                            <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_notify_rules[__NGIDX__][__NCIDX__][value]" value="" style="width:100%;" />
                        </div>
                        </div>
                        <div class="js-notify-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-notify-remove-condition button button-small" title="<?php esc_attr_e('Remove condition', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <div style="margin-top:8px;">
                <button type="button" class="js-notify-add-group button">+ <?php esc_html_e('Add Rule Group', 'wp-aibot'); ?></button>
            </div>
        </div>
    </div>

    <!-- Logs -->
    <div class="ai-chatbot-tab-panel" data-tab="logs">
        <?php
        $logs = AI_Chatbot_Logger::is_enabled()
            ? AI_Chatbot_Logger::get_logs(200)
            : [];
        $log_cid = $post->ID;
        $enabled = AI_Chatbot_Logger::is_enabled();
        ?>

        <div class="ai-chatbot-field">
            <label>
                <input type="checkbox" id="ai-chatbot-log-toggle" value="1" <?php checked($enabled); ?> />
                <?php esc_html_e('Enable Logging', 'wp-aibot'); ?>
            </label>
            <p class="description">
                <?php esc_html_e('Log AI chat requests for this chatbot (up to 500 entries globally). Useful for diagnosing token limit issues, API errors, or prompt problems. Disable on production when not troubleshooting.', 'wp-aibot'); ?>
            </p>
        </div>

        <?php if ($enabled): ?>
            <div style="margin-bottom:12px;display:flex;gap:8px;align-items:center;">
                <button type="button" class="button button-secondary" id="ai-chatbot-log-refresh">
                    <?php esc_html_e('Refresh', 'wp-aibot'); ?>
                </button>
                <button type="button" class="button button-secondary" id="ai-chatbot-log-clear"
                    onclick="return confirm('<?php esc_attr_e('Clear all logs?', 'wp-aibot'); ?>');">
                    <?php esc_html_e('Clear All Logs', 'wp-aibot'); ?>
                </button>
                <span id="ai-chatbot-log-saving" style="display:none;color:#666;">
                    <span class="spinner is-active" style="float:none;margin:0;"></span> <?php esc_html_e('Saving...', 'wp-aibot'); ?>
                </span>
            </div>

            <table class="wp-list-table widefat fixed striped ai-chatbot-log-table" id="ai-chatbot-log-table">
                <thead>
                    <tr>
                        <th scope="col" style="width:160px;"><?php esc_html_e('Time', 'wp-aibot'); ?></th>
                        <th scope="col" style="width:70px;"><?php esc_html_e('Level', 'wp-aibot'); ?></th>
                        <th scope="col"><?php esc_html_e('Message', 'wp-aibot'); ?></th>
                        <th scope="col" style="width:60px;"><?php esc_html_e('Details', 'wp-aibot'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $has_rows = false;
                    foreach ($logs as $i => $log):
                        $cid = $log['context']['chatbot_id'] ?? 0;
                        if ((int) $cid !== $log_cid) {
                            continue;
                        }
                        $has_rows = true;
                        $level_class = 'log-level-' . ($log['level'] ?? 'info');
                        $detail_id = 'log-detail-' . $i;
                    ?>
                        <tr class="<?php echo esc_attr($level_class); ?>">
                            <td><?php echo esc_html($log['time'] ?? ''); ?></td>
                            <td>
                                <span class="log-badge log-badge-<?php echo esc_attr($log['level'] ?? 'info'); ?>">
                                    <?php echo esc_html(strtoupper($log['level'] ?? 'INFO')); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($log['message'] ?? ''); ?></td>
                            <td>
                                <?php if (!empty($log['context'])): ?>
                                    <button type="button" class="button button-small log-toggle"
                                        data-target="<?php echo esc_attr($detail_id); ?>">
                                        <?php esc_html_e('View', 'wp-aibot'); ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if (!empty($log['context'])): ?>
                            <tr class="log-detail-row" id="<?php echo esc_attr($detail_id); ?>" style="display:none;">
                                <td colspan="4">
                                    <div class="log-detail-content">
                                        <table class="log-context-table">
                                            <?php foreach ($log['context'] as $ckey => $cval):
                                                $display_val = AI_Chatbot_Logger::format_context_value($cval, 500);
                                            ?>
                                                <tr>
                                                    <th><?php echo esc_html($ckey); ?></th>
                                                    <td><pre><?php echo esc_html($display_val); ?></pre></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (!$has_rows): ?>
                        <tr><td colspan="4"><?php esc_html_e('No log entries for this chatbot yet.', 'wp-aibot'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(function($) {
    // Log toggle button details
    $(document).on('click', '.log-toggle', function() {
        var target = $('#' + $(this).data('target'));
        target.toggle();
        $(this).text(target.is(':visible') ? '<?php echo esc_js(__('Hide', 'wp-aibot')); ?>' : '<?php echo esc_js(__('View', 'wp-aibot')); ?>');
    });

    // Enable/disable logging via AJAX
    $('#ai-chatbot-log-toggle').on('change', function() {
        var enabled = $(this).is(':checked') ? '1' : '0';
        var $saving = $('#ai-chatbot-log-saving');
        $saving.show();
        $.post(ajaxurl, {
            action: 'ai_chatbot_toggle_logging',
            enabled: enabled,
            _ajax_nonce: '<?php echo esc_js(wp_create_nonce('ai_chatbot_toggle_logging')); ?>'
        }).always(function() {
            $saving.hide();
            location.reload();
        });
    });

    // Clear logs via AJAX
    $('#ai-chatbot-log-clear').on('click', function() {
        if (!confirm('<?php echo esc_js(__('Clear all log entries?', 'wp-aibot')); ?>')) return;
        var $saving = $('#ai-chatbot-log-saving');
        $saving.show();
        $.post(ajaxurl, {
            action: 'ai_chatbot_clear_logs',
            _ajax_nonce: '<?php echo esc_js(wp_create_nonce('ai_chatbot_clear_logs')); ?>'
        }).always(function() {
            $saving.hide();
            location.reload();
        });
    });

    // Refresh logs
    $('#ai-chatbot-log-refresh').on('click', function() {
        location.reload();
    });
});
</script>
