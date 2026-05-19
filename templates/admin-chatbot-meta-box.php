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
    </nav>

    <!-- Basic Settings -->
    <div class="ai-chatbot-tab-panel active" data-tab="basic">
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_greeting"><?php esc_html_e('Greeting Message', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_greeting" name="chatbot_greeting" value="<?php echo esc_attr($meta['chatbot_greeting']); ?>" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_offline_msg"><?php esc_html_e('Offline Message', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_offline_msg" name="chatbot_offline_msg" value="<?php echo esc_attr($meta['chatbot_offline_msg']); ?>" />
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
            <label for="chatbot_fab_icon"><?php esc_html_e('FAB Icon (Font Awesome)', 'wp-aibot'); ?></label>
            <div style="display:flex;align-items:center;gap:10px;">
                <input type="text" id="chatbot_fab_icon" name="chatbot_fab_icon" value="<?php echo esc_attr($meta['chatbot_fab_icon']); ?>" style="width:180px;" placeholder="fa-comment" />
                <span id="ai-chatbot-fa-preview" style="font-size:24px;width:32px;height:32px;text-align:center;display:flex;align-items:center;justify-content:center;">
                    <?php if (strpos($meta['chatbot_fab_icon'], 'fa-') === 0): ?>
                    <i class="fa <?php echo esc_attr($meta['chatbot_fab_icon']); ?>"></i>
                    <?php else: ?>
                    <span style="font-size:20px;"><?php echo esc_html($meta['chatbot_fab_icon'] ?: '💬'); ?></span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="description" style="margin-top:6px;"><?php esc_html_e('Enter a Font Awesome 4 class name (e.g., fa-comment, fa-comments, fa-weixin). Emoji also supported.', 'wp-aibot'); ?></div>
            <div class="ai-chatbot-fa-grid" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;max-width:400px;">
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
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var rippleToggle = document.querySelector('[name="chatbot_fab_ripple_enabled"]');
            var rippleSettings = document.getElementById('ai-chatbot-ripple-settings');
            function toggleRippleSettings() {
                rippleSettings.style.display = rippleToggle && rippleToggle.checked ? 'flex' : 'none';
            }
            if (rippleToggle && rippleSettings) {
                rippleToggle.addEventListener('change', toggleRippleSettings);
                toggleRippleSettings();
            }
            var opacityRange = document.getElementById('chatbot_fab_ripple_opacity');
            var opacityVal = document.getElementById('ai-chatbot-ripple-opacity-val');
            if (opacityRange && opacityVal) {
                opacityRange.addEventListener('input', function() { opacityVal.textContent = this.value; });
            }
            var speedRange = document.getElementById('chatbot_fab_ripple_speed');
            var speedVal = document.getElementById('ai-chatbot-ripple-speed-val');
            if (speedRange && speedVal) {
                speedRange.addEventListener('input', function() { speedVal.textContent = this.value + 's'; });
            }
            var radiusRange = document.getElementById('chatbot_fab_ripple_radius');
            var radiusVal = document.getElementById('ai-chatbot-ripple-radius-val');
            if (radiusRange && radiusVal) {
                radiusRange.addEventListener('input', function() { radiusVal.textContent = this.value + 'x'; });
            }
            var tempRange = document.getElementById('chatbot_temperature');
            var tempVal = document.getElementById('ai-chatbot-temp-val');
            if (tempRange && tempVal) {
                tempRange.addEventListener('input', function() { tempVal.textContent = this.value; });
            }
            // Cache TTL toggle
            var defaultOpenToggle = document.querySelector('[name="chatbot_fab_default_open"]');
            var cacheTtlField = document.getElementById('ai-chatbot-cache-ttl-field');
            function toggleCacheTtl() {
                cacheTtlField.style.display = defaultOpenToggle && defaultOpenToggle.checked ? 'block' : 'none';
            }
            if (defaultOpenToggle && cacheTtlField) {
                defaultOpenToggle.addEventListener('change', toggleCacheTtl);
                toggleCacheTtl();
            }
        });
        </script>

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
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_model"><?php esc_html_e('Model', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_model" name="chatbot_model" value="<?php echo esc_attr($meta['chatbot_model']); ?>" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_max_tokens"><?php esc_html_e('Max Tokens', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_max_tokens" name="chatbot_max_tokens" value="<?php echo esc_attr($meta['chatbot_max_tokens']); ?>" min="1" max="32000" />
                <div class="description"><?php esc_html_e('Maximum response length. 1 token ≈ 0.75 words.', 'wp-aibot'); ?></div>
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
            <label><?php esc_html_e('③ JSON Response Schema', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('What information do you want from visitors? The AI will extract these fields automatically.', 'wp-aibot'); ?></div>
            <input type="hidden" name="chatbot_json_schema_sentinel" value="1" />
            <div class="js-schema-row-answer" style="background:#f0f0f1;border:1px solid #dcdcde;padding:8px;margin-bottom:8px;border-radius:4px;">
                <div class="js-schema-fields-row">
                    <div class="js-schema-field-path">
                        <label><?php esc_html_e('Path', 'wp-aibot'); ?></label>
                        <code style="display:block;padding:4px 8px;background:#fff;border:1px solid #dcdcde;border-radius:3px;">answer</code>
                    </div>
                    <div class="js-schema-field-type">
                        <label><?php esc_html_e('Type', 'wp-aibot'); ?></label>
                        <code style="display:block;padding:4px 8px;background:#fff;border:1px solid #dcdcde;border-radius:3px;">string</code>
                    </div>
                    <div class="js-schema-field-desc" style="flex:2;">
                        <label><?php esc_html_e('Description', 'wp-aibot'); ?></label>
                        <code style="display:block;padding:4px 8px;background:#fff;border:1px solid #dcdcde;border-radius:3px;"><?php esc_html_e('Your response to the visitor (auto-managed)', 'wp-aibot'); ?></code>
                    </div>
                    <div class="js-schema-field-req">
                        <label>&nbsp;</label>
                        <span style="display:block;padding:4px 8px;color:#888;font-size:12px;"><?php esc_html_e('Always required', 'wp-aibot'); ?></span>
                    </div>
                    <div class="js-schema-field-actions">
                        <label>&nbsp;</label>
                        <span style="display:block;padding:4px 8px;color:#bbb;">&mdash;</span>
                    </div>
                </div>
            </div>
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
                // Filter out auto-managed fields (answer is always injected; should_notify_sales is deprecated)
                $schema_items = array_values(array_filter($schema_items, function($item) {
                    $path = is_array($item) ? ($item['path'] ?? '') : '';
                    return $path !== 'should_notify_sales' && $path !== 'answer';
                }));
                $idx = 0;
                foreach ($schema_items as $item):
                    $item = (array) $item;
                ?>
                <div class="js-schema-row" data-index="<?php echo $idx; ?>">
                    <div class="js-schema-fields-row">
                        <div class="js-schema-field-path">
                            <label><?php esc_html_e('Path', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_json_schema[<?php echo $idx; ?>][path]" value="<?php echo esc_attr($item['path'] ?? ''); ?>" placeholder="e.g. lead.email" style="width:100%;" />
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
                            <label><?php esc_html_e('Path', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_json_schema[__IDX__][path]" value="" placeholder="e.g. lead.email" style="width:100%;" />
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
                $docs = get_posts(['post_type' => 'ai_knowledge', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
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
            <label><?php esc_html_e('Trigger Rules (AND logic — all must match)', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('Define conditions that trigger the lead capture form. All rules must be satisfied for the form to appear.', 'wp-aibot'); ?></div>
            <input type="hidden" name="chatbot_lead_capture_rules_sentinel" value="1" />
            <div id="js-capture-rules-fields">
                <?php
                $capture_rules = $meta['chatbot_lead_capture_rules'] ?? [];
                if (is_string($capture_rules)) {
                    $capture_rules = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_lead_capture_rules'];
                }
                if (empty($capture_rules)) {
                    $capture_rules = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_lead_capture_rules'];
                }
                $cidx = 0;
                foreach ($capture_rules as $rule):
                    $rule = (array) $rule;
                ?>
                <div class="js-capture-rule-row" data-index="<?php echo $cidx; ?>">
                    <div class="js-notify-fields-row">
                        <div class="js-notify-field-path">
                            <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_capture_rules[<?php echo $cidx; ?>][field]" value="<?php echo esc_attr($rule['field'] ?? ''); ?>" placeholder="lead.lead_score" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-operator">
                            <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                            <select name="chatbot_lead_capture_rules[<?php echo $cidx; ?>][operator]" style="width:100%;">
                                <option value="eq" <?php selected($rule['operator'] ?? '', 'eq'); ?>><?php esc_html_e('equals (=)', 'wp-aibot'); ?></option>
                                <option value="neq" <?php selected($rule['operator'] ?? '', 'neq'); ?>><?php esc_html_e('not equals (!=)', 'wp-aibot'); ?></option>
                                <option value="in" <?php selected($rule['operator'] ?? '', 'in'); ?>><?php esc_html_e('in (comma-separated)', 'wp-aibot'); ?></option>
                                <option value="contains" <?php selected($rule['operator'] ?? '', 'contains'); ?>><?php esc_html_e('contains', 'wp-aibot'); ?></option>
                                <option value="gt" <?php selected($rule['operator'] ?? '', 'gt'); ?>><?php esc_html_e('greater than (>)', 'wp-aibot'); ?></option>
                                <option value="lt" <?php selected($rule['operator'] ?? '', 'lt'); ?>><?php esc_html_e('less than (<)', 'wp-aibot'); ?></option>
                                <option value="gte" <?php selected($rule['operator'] ?? '', 'gte'); ?>><?php esc_html_e('>=', 'wp-aibot'); ?></option>
                                <option value="lte" <?php selected($rule['operator'] ?? '', 'lte'); ?>><?php esc_html_e('<=', 'wp-aibot'); ?></option>
                                <option value="empty" <?php selected($rule['operator'] ?? '', 'empty'); ?>><?php esc_html_e('is empty', 'wp-aibot'); ?></option>
                                <option value="not_empty" <?php selected($rule['operator'] ?? '', 'not_empty'); ?>><?php esc_html_e('is not empty', 'wp-aibot'); ?></option>
                            </select>
                        </div>
                        <div class="js-notify-field-value">
                            <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_capture_rules[<?php echo $cidx; ?>][value]" value="<?php echo esc_attr($rule['value'] ?? ''); ?>" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-capture-remove-rule button button-small" title="<?php esc_attr_e('Remove rule', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
                <?php $cidx++; endforeach; ?>
            </div>
            <template id="js-capture-rule-tpl">
                <div class="js-capture-rule-row" data-index="__CIDX__">
                    <div class="js-notify-fields-row">
                        <div class="js-notify-field-path">
                            <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_capture_rules[__CIDX__][field]" value="" placeholder="lead.lead_score" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-operator">
                            <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                            <select name="chatbot_lead_capture_rules[__CIDX__][operator]" style="width:100%;">
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
                            </select>
                        </div>
                        <div class="js-notify-field-value">
                            <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_lead_capture_rules[__CIDX__][value]" value="" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-capture-remove-rule button button-small" title="<?php esc_attr_e('Remove rule', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <div style="margin-top:8px;">
                <button type="button" class="js-capture-add-rule button">+ <?php esc_html_e('Add Rule', 'wp-aibot'); ?></button>
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
            <label><?php esc_html_e('Notification Rules', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('When a JSON field matches a rule, notifications are sent to the email/webhook above. Multiple rules: any match triggers the notification.', 'wp-aibot'); ?></div>
            <input type="hidden" name="chatbot_notify_rules_sentinel" value="1" />
            <div id="js-notify-rules-fields">
                <?php
                $notify_rules = $meta['chatbot_notify_rules'] ?? [];
                if (is_string($notify_rules)) {
                    $notify_rules = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_notify_rules'];
                }
                if (empty($notify_rules)) {
                    $notify_rules = AI_Chatbot_CPT_Chatbot::get_defaults()['chatbot_notify_rules'];
                }
                $ridx = 0;
                foreach ($notify_rules as $rule):
                    $rule = (array) $rule;
                ?>
                <div class="js-notify-rule-row" data-index="<?php echo $ridx; ?>">
                    <div class="js-notify-fields-row">
                        <div class="js-notify-field-path">
                            <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_notify_rules[<?php echo $ridx; ?>][field]" value="<?php echo esc_attr($rule['field'] ?? ''); ?>" placeholder="lead.lead_score" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-operator">
                            <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                            <select name="chatbot_notify_rules[<?php echo $ridx; ?>][operator]" style="width:100%;">
                                <option value="eq" <?php selected($rule['operator'] ?? '', 'eq'); ?>><?php esc_html_e('equals (=)', 'wp-aibot'); ?></option>
                                <option value="neq" <?php selected($rule['operator'] ?? '', 'neq'); ?>><?php esc_html_e('not equals (!=)', 'wp-aibot'); ?></option>
                                <option value="in" <?php selected($rule['operator'] ?? '', 'in'); ?>><?php esc_html_e('in (comma-separated)', 'wp-aibot'); ?></option>
                                <option value="contains" <?php selected($rule['operator'] ?? '', 'contains'); ?>><?php esc_html_e('contains', 'wp-aibot'); ?></option>
                                <option value="gt" <?php selected($rule['operator'] ?? '', 'gt'); ?>><?php esc_html_e('greater than (>)', 'wp-aibot'); ?></option>
                                <option value="lt" <?php selected($rule['operator'] ?? '', 'lt'); ?>><?php esc_html_e('less than (<)', 'wp-aibot'); ?></option>
                            </select>
                        </div>
                        <div class="js-notify-field-value">
                            <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_notify_rules[<?php echo $ridx; ?>][value]" value="<?php echo esc_attr(is_array($rule['value'] ?? '') ? implode(',', $rule['value']) : ($rule['value'] ?? '')); ?>" placeholder="A,B" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-notify-remove-rule button button-small" title="<?php esc_attr_e('Remove rule', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
                <?php $ridx++; endforeach; ?>
            </div>
            <template id="js-notify-rule-tpl">
                <div class="js-notify-rule-row" data-index="__RIDX__">
                    <div class="js-notify-fields-row">
                        <div class="js-notify-field-path">
                            <label><?php esc_html_e('Field', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_notify_rules[__RIDX__][field]" value="" placeholder="lead.lead_score" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-operator">
                            <label><?php esc_html_e('Operator', 'wp-aibot'); ?></label>
                            <select name="chatbot_notify_rules[__RIDX__][operator]" style="width:100%;">
                                <option value="eq"><?php esc_html_e('equals (=)', 'wp-aibot'); ?></option>
                                <option value="neq"><?php esc_html_e('not equals (!=)', 'wp-aibot'); ?></option>
                                <option value="in"><?php esc_html_e('in (comma-separated)', 'wp-aibot'); ?></option>
                                <option value="contains"><?php esc_html_e('contains', 'wp-aibot'); ?></option>
                                <option value="gt"><?php esc_html_e('greater than (>)', 'wp-aibot'); ?></option>
                                <option value="lt"><?php esc_html_e('less than (<)', 'wp-aibot'); ?></option>
                            </select>
                        </div>
                        <div class="js-notify-field-value">
                            <label><?php esc_html_e('Value', 'wp-aibot'); ?></label>
                            <input type="text" name="chatbot_notify_rules[__RIDX__][value]" value="" placeholder="A,B" style="width:100%;" />
                        </div>
                        <div class="js-notify-field-actions">
                            <label>&nbsp;</label>
                            <button type="button" class="js-notify-remove-rule button button-small" title="<?php esc_attr_e('Remove rule', 'wp-aibot'); ?>">✕</button>
                        </div>
                    </div>
                </div>
            </template>
            <div style="margin-top:8px;">
                <button type="button" class="js-notify-add-rule button">+ <?php esc_html_e('Add Rule', 'wp-aibot'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
window.aiChatbotAdmin = window.aiChatbotAdmin || {};
window.aiChatbotAdmin.schemaIdx = <?php echo max($idx, 0); ?>;
window.aiChatbotAdmin.notifyIdx = <?php echo max($ridx ?? 0, 0); ?>;
window.aiChatbotAdmin.captureIdx = <?php echo max($cidx ?? 0, 0); ?>;
document.getElementById('js-wecom-guide-toggle')?.addEventListener('click', function(e) {
    e.preventDefault();
    var guide = document.getElementById('js-wecom-guide');
    if (guide) {
        var isHidden = guide.style.display === 'none';
        guide.style.display = isHidden ? '' : 'none';
        this.textContent = isHidden ? '<?php echo esc_js(__('收起指南', 'wp-aibot')); ?>' : '<?php echo esc_js(__('如何获取 Webhook？', 'wp-aibot')); ?>';
    }
});
</script>
