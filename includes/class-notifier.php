<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Notifier {

    /**
     * Send notifications if any rule matches the parsed AI response.
     * Supports: WeCom (企业微信) Webhook and Email.
     *
     * @param array $parsed  Full parsed JSON from AI (includes 'answer', 'lead', 'should_notify_sales')
     * @param array $visitor_data
     * @param array $config
     */
    public function notify(array $parsed, array $visitor_data, array $config): void {
        if (empty($config['chatbot_notify_enabled'])) {
            return;
        }

        if (!$this->should_notify($parsed, $config)) {
            return;
        }

        $lead_data = $parsed['lead'] ?? [];
        $payload = array_merge($lead_data, ['visitor' => $visitor_data]);

        // Webhook (企业微信)
        if (!empty($config['chatbot_notify_webhook'])) {
            $this->send_webhook($config['chatbot_notify_webhook'], $payload);
        }

        // Email
        if (!empty($config['chatbot_notify_email'])) {
            $this->send_email($config['chatbot_notify_email'], $payload);
        }
    }

    /**
     * Evaluate notification rules against parsed AI data.
     */
    private function should_notify(array $parsed, array $config): bool {
        $rules = $config['chatbot_notify_rules'] ?? null;

        // If no rules configured, fall back to legacy chatbot_notify_on_scores behavior
        if (empty($rules)) {
            $score = $parsed['lead']['lead_score'] ?? 'D';
            $notify_on = (array) ($config['chatbot_notify_on_scores'] ?? ['A', 'B']);
            return in_array($score, $notify_on, true);
        }

        foreach ($rules as $rule) {
            if ($this->evaluate_rule($parsed, $rule)) {
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

        // If the field doesn't exist in the data, rule does not match
        if ($actual === null && $operator !== 'neq') {
            // null only matches neq when the expected value is also null-aware
            // For simplicity, null doesn't match any rule by default
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

    private function send_webhook(string $url, array $payload): void {
        $markdown = $this->format_wecom_markdown($payload);

        wp_remote_post($url, [
            'headers'  => ['Content-Type' => 'application/json'],
            'body'     => wp_json_encode([
                'msgtype'    => 'markdown_v2',
                'markdown_v2' => ['content' => $markdown],
            ]),
            'timeout'  => 15,
            'blocking' => false,
        ]);
    }

    private function send_email(string $to, array $payload): void {
        $subject = sprintf(
            '[AI Chatbot] New Lead - Score %s',
            $payload['lead_score'] ?? 'N/A'
        );

        $body = "Lead Details:\n\n";
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $body .= "$key: " . print_r($value, true) . "\n";
            } else {
                $body .= "$key: $value\n";
            }
        }

        wp_mail($to, $subject, $body);
    }

    private function format_wecom_markdown(array $data): string {
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

        $lines[] = "";
        $lines[] = "> *此通知由 AI Chatbot 插件自动发送*";

        return implode("\n", $lines);
    }
}
