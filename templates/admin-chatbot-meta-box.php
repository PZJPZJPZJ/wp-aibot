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
        <button type="button" class="ai-chatbot-tab-btn" data-tab="ai"><?php esc_html_e('AI Config', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="knowledge"><?php esc_html_e('Knowledge', 'wp-aibot'); ?></button>
        <button type="button" class="ai-chatbot-tab-btn" data-tab="memory"><?php esc_html_e('Memory', 'wp-aibot'); ?></button>
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
                <label for="chatbot_primary_color"><?php esc_html_e('Primary Color', 'wp-aibot'); ?></label>
                <input type="color" id="chatbot_primary_color" name="chatbot_primary_color" value="<?php echo esc_attr($meta['chatbot_primary_color']); ?>" style="width:60px;height:36px;padding:2px;cursor:pointer;" />
                <div class="description"><?php esc_html_e('Header, button, and accent color.', 'wp-aibot'); ?></div>
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_fab_icon"><?php esc_html_e('FAB Icon', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_fab_icon" name="chatbot_fab_icon" value="<?php echo esc_attr($meta['chatbot_fab_icon']); ?>" style="width:80px;font-size:18px;text-align:center;" />
                <div class="description"><?php esc_html_e('Emoji or text for the floating button.', 'wp-aibot'); ?></div>
            </div>
        </div>

        <hr style="margin:20px 0;border:none;border-top:1px solid #ddd;">

        <h4><?php esc_html_e('Text Localization', 'wp-aibot'); ?></h4>
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

    <!-- AI Config -->
    <div class="ai-chatbot-tab-panel" data-tab="ai">
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
            <input type="text" id="chatbot_api_key" name="chatbot_api_key" value="<?php echo esc_attr($meta['chatbot_api_key'] ? '********' : ''); ?>" />
            <div class="description"><?php esc_html_e('Leave blank to keep current key. New value will be encrypted.', 'wp-aibot'); ?></div>
        </div>
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_model"><?php esc_html_e('Model', 'wp-aibot'); ?></label>
                <input type="text" id="chatbot_model" name="chatbot_model" value="<?php echo esc_attr($meta['chatbot_model']); ?>" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_temperature"><?php esc_html_e('Temperature', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_temperature" name="chatbot_temperature" value="<?php echo esc_attr($meta['chatbot_temperature']); ?>" step="0.1" min="0" max="2" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_max_tokens"><?php esc_html_e('Max Tokens', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_max_tokens" name="chatbot_max_tokens" value="<?php echo esc_attr($meta['chatbot_max_tokens']); ?>" min="1" max="32000" />
            </div>
        </div>
        <div class="ai-chatbot-field">
            <label for="chatbot_system_prompt"><?php esc_html_e('System Prompt', 'wp-aibot'); ?></label>
            <textarea id="chatbot_system_prompt" name="chatbot_system_prompt" rows="12" style="font-family:monospace;"><?php echo esc_textarea($meta['chatbot_system_prompt']); ?></textarea>
            <div class="description"><?php esc_html_e('Instructions passed to the AI at the start of each conversation. Do not include JSON format instructions — those are managed separately below.', 'wp-aibot'); ?></div>
            <div class="ai-chatbot-variables">
                <strong><?php esc_html_e('Available Variables:', 'wp-aibot'); ?></strong><br>
                <code>{company_name}</code> — <?php esc_html_e('Your company or site name', 'wp-aibot'); ?><br>
                <code>{knowledge_context}</code> — <?php esc_html_e('Selected knowledge documents (injected automatically)', 'wp-aibot'); ?><br>
                <code>{conversation_history}</code> — <?php esc_html_e('Previous messages in this session', 'wp-aibot'); ?>
            </div>
        </div>
        <div class="ai-chatbot-field">
            <label><?php esc_html_e('JSON Response Schema', 'wp-aibot'); ?></label>
            <div class="description" style="margin-bottom:8px;"><?php esc_html_e('Define the JSON fields the AI must return. The system will auto-generate the format instruction.', 'wp-aibot'); ?></div>
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
                <button type="button" class="js-schema-preview-toggle button" style="margin-left:6px;"><?php esc_html_e('Preview Generated Prompt', 'wp-aibot'); ?></button>
            </div>
            <div id="js-schema-preview" style="display:none;margin-top:10px;">
                <pre class="ai-chatbot-variables" style="white-space:pre-wrap;font-family:monospace;font-size:12px;max-width:100%;overflow-x:auto;margin:0;"></pre>
            </div>
        </div>
    </div>

    <!-- Knowledge -->
    <div class="ai-chatbot-tab-panel" data-tab="knowledge">
        <div class="ai-chatbot-field">
            <label><?php esc_html_e('Knowledge Documents', 'wp-aibot'); ?></label>
            <div class="ai-chatbot-checkbox-list">
                <?php
                $docs = get_posts(['post_type' => 'ai_knowledge', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
                $selected_ids = (array) ($meta['chatbot_knowledge_ids'] ?? []);
                if (empty($docs)) {
                    echo '<span style="color:#999;">' . esc_html__('No knowledge documents yet. Create one under Knowledge Base.', 'wp-aibot') . '</span>';
                }
                foreach ($docs as $doc) {
                    echo '<label class="ai-chatbot-checkbox-item">';
                    echo '<input type="checkbox" name="chatbot_knowledge_ids[]" value="' . esc_attr($doc->ID) . '" ' . checked(in_array($doc->ID, $selected_ids), true, false) . '>';
                    echo esc_html($doc->post_title);
                    echo '</label>';
                }
                ?>
            </div>
            <div class="description"><?php esc_html_e('Check knowledge documents to inject into AI context.', 'wp-aibot'); ?></div>
        </div>
    </div>

    <!-- Memory -->
    <div class="ai-chatbot-tab-panel" data-tab="memory">
        <div class="ai-chatbot-field-row">
            <div class="ai-chatbot-field">
                <label for="chatbot_max_history"><?php esc_html_e('Max History Rounds', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_max_history" name="chatbot_max_history" value="<?php echo esc_attr($meta['chatbot_max_history']); ?>" min="0" max="100" />
            </div>
            <div class="ai-chatbot-field">
                <label for="chatbot_session_ttl"><?php esc_html_e('Session TTL (minutes)', 'wp-aibot'); ?></label>
                <input type="number" id="chatbot_session_ttl" name="chatbot_session_ttl" value="<?php echo esc_attr($meta['chatbot_session_ttl']); ?>" min="1" max="1440" />
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
        </div>
        <div class="ai-chatbot-field">
            <label for="chatbot_notify_webhook"><?php esc_html_e('Webhook URL (企业微信)', 'wp-aibot'); ?></label>
            <input type="url" id="chatbot_notify_webhook" name="chatbot_notify_webhook" value="<?php echo esc_attr($meta['chatbot_notify_webhook']); ?>" />
            <div class="description"><?php esc_html_e('WeCom (企业微信) bot webhook URL.', 'wp-aibot'); ?></div>
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
window.aiChatbotAdmin.noFieldsText = '<?php echo esc_js(__('No fields defined. The default schema will be used.', 'wp-aibot')); ?>';
</script>
