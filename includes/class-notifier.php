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
        $conv = $this->load_conversation_overview($conversation_id);
        $payload = array_merge($lead_data, ['visitor' => $visitor_data, 'conversation' => $conv]);

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
     * Send notification for a conversation, bypassing enable/once/rules checks.
     * Used for manual "trigger notification" button in admin.
     *
     * @return bool True if at least one channel succeeded.
     */
    public function force_notify(int $conversation_id): bool {
        $lead_data = get_post_meta($conversation_id, 'conversation_lead_data', true);
        $chatbot_id = (int) get_post_meta($conversation_id, 'conversation_chatbot_id', true);

        if (!is_array($lead_data)) {
            $lead_data = [];
        }

        $config = $chatbot_id ? AI_Chatbot_CPT_Chatbot::get_meta($chatbot_id) : [];

        $has_webhook = !empty($config['chatbot_notify_webhook']);
        $has_email   = !empty($config['chatbot_notify_email']);

        if (!$has_webhook && !$has_email) {
            return false;
        }

        $visitor_data = [
            'ip'       => get_post_meta($conversation_id, 'conversation_visitor_ip', true),
            'ua'       => get_post_meta($conversation_id, 'conversation_visitor_ua', true),
            'page_url' => get_post_meta($conversation_id, 'conversation_visitor_page_url', true),
        ];

        $conv = $this->load_conversation_overview($conversation_id);
        $payload = array_merge($lead_data, ['visitor' => $visitor_data, 'conversation' => $conv]);

        $webhook_ok = true;
        $email_ok   = true;

        if ($has_webhook) {
            $webhook_ok = $this->send_webhook($config['chatbot_notify_webhook'], $payload, $conversation_id);
        }
        if ($has_email) {
            $email_ok = $this->send_email($config['chatbot_notify_email'], $payload, $conversation_id);
        }

        $any_ok = ($has_webhook && $webhook_ok) || ($has_email && $email_ok);
        if ($any_ok) {
            $this->increment_sent_count($conversation_id);
        }

        return $any_ok;
    }

    /**
     * Load conversation overview data from post meta.
     */
    private function load_conversation_overview(int $conversation_id): array {
        if ($conversation_id <= 0) {
            return [];
        }

        $chatbot_id = (int) get_post_meta($conversation_id, 'conversation_chatbot_id', true);
        $bot = $chatbot_id ? get_post($chatbot_id) : null;

        return [
            'session_id'    => get_post_meta($conversation_id, 'conversation_session_id', true),
            'chatbot_name'  => $bot ? $bot->post_title : '—',
            'chatbot_id'    => $chatbot_id,
            'message_count' => (int) get_post_meta($conversation_id, 'conversation_message_count', true),
            'started_at'    => get_post_meta($conversation_id, 'conversation_started_at', true),
            'summary'       => get_post_meta($conversation_id, 'conversation_summary', true),
        ];
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
        ob_start();
        include AI_CHATBOT_PATH . 'templates/notify-wecom-markdown.php';
        return ob_get_clean();
    }

    private function format_email_html(array $data, int $conversation_id = 0): string {
        ob_start();
        include AI_CHATBOT_PATH . 'templates/notify-email-html.php';
        return ob_get_clean();
    }
}
