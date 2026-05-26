<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Notifier {

    /**
     * Previous lead data for 'changed' operator comparison.
     * Set by notify() from conversation_lead_data post meta before evaluation.
     */
    private ?array $old_lead_data = null;

    /**
     * Send notifications if any rule matches the parsed AI response.
     * Supports: WeCom (企业微信) Webhook and Email.
     *
     * @param array $parsed  Full parsed JSON from AI (includes 'answer', 'lead')
     * @param array $visitor_data
     * @param array $config
     * @param int   $conversation_id  Save notification count to this conversation.
     */
    public function notify(array $parsed, array $visitor_data, array $config, int $conversation_id = 0): void {
        if (empty($config['chatbot_notify_enabled'])) {
            return; // disabled — no count written (0 = not sent)
        }

        // Load previous lead data for 'changed' operator comparison.
        // Note: conversation_lead_data stores the lead sub-array only (e.g. {lead_score, name, ...}),
        // but rule field paths use "lead.xxx" prefix relative to the full parsed data.
        // Wrap it so resolve_field('lead.lead_score') resolves correctly.
        $this->old_lead_data = null;
        if ($conversation_id > 0) {
            $old = get_post_meta($conversation_id, 'conversation_lead_data', true);
            if (is_array($old)) {
                $this->old_lead_data = ['lead' => $old];
            }
        }

        // Deduplication: 'once' mode skips if already sent
        $notify_mode = $config['chatbot_notify_mode'] ?? 'once';
        if ($notify_mode === 'once' && $conversation_id > 0) {
            $existing_count = (int) get_post_meta($conversation_id, 'conversation_notification_count', true);
            if ($existing_count > 0) {
                return;
            }
        }

        if (!$this->should_notify($parsed, $config)) {
            return; // no match — no count written
        }

        $lead_data = $parsed['lead'] ?? [];
        $payload = array_merge($lead_data, ['visitor' => $visitor_data]);

        $has_webhook = !empty($config['chatbot_notify_webhook']);
        $has_email   = !empty($config['chatbot_notify_email']);

        $webhook_ok = true;
        $email_ok   = true;

        // Webhook (企业微信)
        if ($has_webhook) {
            $webhook_ok = $this->send_webhook($config['chatbot_notify_webhook'], $payload, $conversation_id);
        }

        // Email
        if ($has_email) {
            $email_ok = $this->send_email($config['chatbot_notify_email'], $payload, $conversation_id);
        }

        // Increment count only if at least one channel succeeded
        $any_ok = ($has_webhook && $webhook_ok) || ($has_email && $email_ok);
        if ($any_ok) {
            $this->increment_sent_count($conversation_id);
        }
    }

    /**
     * Increment the notification sent count for a conversation.
     */
    private function increment_sent_count(int $conversation_id): void {
        if ($conversation_id <= 0) {
            return;
        }
        $count = (int) get_post_meta($conversation_id, 'conversation_notification_count', true);
        update_post_meta($conversation_id, 'conversation_notification_count', $count + 1);
    }

    /**
     * Evaluate notification rules against parsed AI data.
     * OR between rule groups, AND within each group.
     */
    private function should_notify(array $parsed, array $config): bool {
        $rules = $config['chatbot_notify_rules'] ?? null;

        // If no rules configured, fall back to legacy chatbot_notify_on_scores behavior
        if (empty($rules)) {
            $score = $parsed['lead']['lead_score'] ?? 'D';
            $notify_on = (array) ($config['chatbot_notify_on_scores'] ?? ['A', 'B']);
            return in_array($score, $notify_on, true);
        }

        // Backward compat: flat format -> single group
        if (isset($rules[0]['field'])) {
            $rules = [$rules];
        }

        foreach ($rules as $group) {
            $match = true;
            foreach ($group as $condition) {
                if (!$this->evaluate_rule($parsed, $condition)) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate a single rule against the parsed data.
     */
    private function evaluate_rule(array $data, array $rule): bool {
        $field = $rule['field'] ?? '';
        $operator = $rule['operator'] ?? 'eq';
        $expected = $rule['value'] ?? null;

        if (empty($field)) {
            return false;
        }

        $actual = $this->resolve_field($data, $field);

        // If the field doesn't exist in the data, only changed/empty/not_empty/neq can proceed
        if ($actual === null && !in_array($operator, ['neq', 'changed', 'empty', 'not_empty'], true)) {
            return false;
        }

        switch ($operator) {
            case 'eq':
            case '==':
                return $actual === $expected;

            case 'neq':
            case '!=':
                return $actual !== $expected;

            case 'in':
                $values = is_array($expected)
                    ? $expected
                    : array_map('trim', explode(',', (string) $expected));
                return in_array((string) $actual, $values, true);

            case 'contains':
                return is_string($actual) && str_contains($actual, (string) $expected);

            case 'gt':
            case '>':
                return is_numeric($actual) && is_numeric($expected) && (float) $actual > (float) $expected;

            case 'gte':
            case '>=':
                return is_numeric($actual) && is_numeric($expected) && (float) $actual >= (float) $expected;

            case 'lt':
            case '<':
                return is_numeric($actual) && is_numeric($expected) && (float) $actual < (float) $expected;

            case 'lte':
            case '<=':
                return is_numeric($actual) && is_numeric($expected) && (float) $actual <= (float) $expected;

            case 'empty':
                return empty($actual) && $actual !== false && $actual !== 0;

            case 'not_empty':
                return !empty($actual) || $actual === false || $actual === 0;

            case 'changed':
                if (!isset($this->old_lead_data)) {
                    // First exchange — treat as changed from nothing so it can trigger too
                    if ($expected !== null && $expected !== '') {
                        $values = array_map('trim', explode(',', (string) $expected));
                        return in_array((string) $actual, $values, true);
                    }
                    return true;
                }
                $old_value = $this->resolve_field($this->old_lead_data, $field);
                if ($actual === $old_value) {
                    return false; // value did not change
                }
                // Value changed — if expected values specified, check match
                if ($expected !== null && $expected !== '') {
                    $values = array_map('trim', explode(',', (string) $expected));
                    return in_array((string) $actual, $values, true);
                }
                return true; // changed, no value filter

            default:
                return false;
        }
    }

    /**
     * Resolve a dot-notation field path (e.g. "lead.lead_score") against an array.
     */
    private function resolve_field(array $data, string $path) {
        $keys = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return null;
            }
            $current = $current[$key];
        }

        return $current;
    }

    private function send_webhook(string $url, array $payload, int $conversation_id = 0): bool {
        $markdown = $this->format_wecom_markdown($payload, $conversation_id);

        $response = wp_remote_post($url, [
            'headers'  => ['Content-Type' => 'application/json'],
            'body'     => wp_json_encode([
                'msgtype'    => 'markdown_v2',
                'markdown_v2' => ['content' => $markdown],
            ]),
            'timeout'  => 15,
            'blocking' => true,
        ]);

        if (is_wp_error($response)) {
            return false;
        }
        $code = wp_remote_retrieve_response_code($response);
        return $code >= 200 && $code < 300;
    }

    private function send_email(string $to, array $payload, int $conversation_id = 0): bool {
        $subject = sprintf(
            '[AI Chatbot] New Lead - Score %s',
            $payload['lead_score'] ?? 'N/A'
        );

        $body = $this->format_email_html($payload, $conversation_id);
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        return wp_mail($to, $subject, $body, $headers);
    }

    private function format_wecom_markdown(array $data, int $conversation_id = 0): string {
        $lines = [
            "# 新 AI 线索通知",
            "",
        ];

        $score = $data['lead_score'] ?? 'N/A';
        $lines[] = "**线索评分:** **$score**";
        $lines[] = "---";
        $lines[] = "";

        // Lead fields table
        $rows = [];
        foreach ($data as $key => $value) {
            if ($key === 'visitor' || is_array($value)) {
                continue;
            }
            $label = ucwords(str_replace(['_', '-'], ' ', $key));
            $rows[] = "| $label | " . (is_string($value) ? $value : '') . " |";
        }

        if (!empty($rows)) {
            $lines[] = "| 字段 | 内容 |";
            $lines[] = "| :--- | :--- |";
            $lines = array_merge($lines, $rows);
            $lines[] = "";
            $lines[] = "---";
            $lines[] = "";
        }

        if (isset($data['visitor'])) {
            $visitor = $data['visitor'];
            $lines[] = "**访问者信息**";
            $lines[] = "";
            $lines[] = "| 字段 | 内容 |";
            $lines[] = "| :--- | :--- |";
            $lines[] = "| IP | " . ($visitor['ip'] ?? 'N/A') . " |";
            $lines[] = "| 页面 | " . ($visitor['page_url'] ?? 'N/A') . " |";
            $lines[] = "";
            $lines[] = "---";
        }

        // Conversation history link
        if ($conversation_id > 0) {
            $url = admin_url('post.php?post=' . $conversation_id . '&action=edit');
            $lines[] = "";
            $lines[] = "> [查看对话历史]($url)";
            $lines[] = "";
        }

        $lines[] = "";
        $lines[] = "> *此通知由 AI Chatbot 插件自动发送*";

        return implode("\n", $lines);
    }

    private function format_email_html(array $data, int $conversation_id = 0): string {
        $score = $data['lead_score'] ?? 'N/A';

        $html = '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;color:#333;">';
        $html .= '<h2 style="color:#25b366;margin-bottom:4px;">🔔 New Lead Notification</h2>';
        $html .= '<p style="color:#666;font-size:13px;margin-top:0;">AI Chatbot Plugin</p>';
        $html .= '<hr style="border:none;border-top:1px solid #e0e0e0;">';

        $html .= '<p><strong style="color:#555;">Lead Score:</strong> <span style="font-weight:700;color:#25b366;">' . esc_html($score) . '</span></p>';
        $html .= '<hr style="border:none;border-top:1px solid #e0e0e0;">';

        // Lead fields table
        $rows = '';
        foreach ($data as $key => $value) {
            if ($key === 'visitor' || is_array($value)) {
                continue;
            }
            $label = ucwords(str_replace(['_', '-'], ' ', $key));
            $val   = is_string($value) ? esc_html($value) : '';
            $rows .= '<tr><td style="padding:6px 10px;border:1px solid #e0e0e0;background:#f9f9f9;font-weight:500;width:120px;">' . esc_html($label) . '</td><td style="padding:6px 10px;border:1px solid #e0e0e0;">' . $val . '</td></tr>';
        }

        if (!empty($rows)) {
            $html .= '<table style="width:100%;border-collapse:collapse;margin:12px 0;">';
            $html .= '<thead><tr><th style="padding:8px 10px;border:1px solid #e0e0e0;background:#25b366;color:#fff;text-align:left;">Field</th><th style="padding:8px 10px;border:1px solid #e0e0e0;background:#25b366;color:#fff;text-align:left;">Value</th></tr></thead>';
            $html .= '<tbody>' . $rows . '</tbody></table>';
        }

        // Visitor info
        if (isset($data['visitor'])) {
            $visitor = $data['visitor'];
            $html .= '<hr style="border:none;border-top:1px solid #e0e0e0;">';
            $html .= '<h3 style="color:#555;font-size:14px;">Visitor Information</h3>';
            $html .= '<table style="width:100%;border-collapse:collapse;margin:8px 0;">';
            $html .= '<tr><td style="padding:6px 10px;border:1px solid #e0e0e0;background:#f9f9f9;font-weight:500;width:120px;">IP</td><td style="padding:6px 10px;border:1px solid #e0e0e0;">' . esc_html($visitor['ip'] ?? 'N/A') . '</td></tr>';
            $html .= '<tr><td style="padding:6px 10px;border:1px solid #e0e0e0;background:#f9f9f9;font-weight:500;width:120px;">Page</td><td style="padding:6px 10px;border:1px solid #e0e0e0;">' . esc_html($visitor['page_url'] ?? 'N/A') . '</td></tr>';
            $html .= '</table>';
        }

        // Conversation history link
        if ($conversation_id > 0) {
            $url = admin_url('post.php?post=' . $conversation_id . '&action=edit');
            $html .= '<hr style="border:none;border-top:1px solid #e0e0e0;">';
            $html .= '<p style="text-align:center;margin:16px 0 0;">';
            $html .= '<a href="' . esc_url($url) . '" style="display:inline-block;padding:10px 20px;background:#25b366;color:#fff;text-decoration:none;border-radius:4px;font-size:14px;">📋 View Conversation History</a>';
            $html .= '</p>';
        }

        $html .= '<hr style="border:none;border-top:1px solid #e0e0e0;">';
        $html .= '<p style="font-size:11px;color:#999;text-align:center;">This notification was sent automatically by the AI Chatbot plugin.</p>';
        $html .= '</div>';

        return $html;
    }
}
