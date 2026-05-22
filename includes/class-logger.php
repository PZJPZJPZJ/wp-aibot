<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Logger {

    const OPTION_NAME = 'ai_chatbot_logs';
    const MAX_LOGS = 500;
    const SETTING_ENABLED = 'ai_chatbot_logging_enabled';

    /**
     * Check if logging is enabled.
     */
    public static function is_enabled(): bool {
        return (bool) get_option(self::SETTING_ENABLED, false);
    }

    /**
     * Add a log entry at the specified level.
     */
    public static function log(string $level, string $message, array $context = []): void {
        if (!self::is_enabled()) {
            return;
        }

        $logs = get_option(self::OPTION_NAME, []);
        if (!is_array($logs)) {
            $logs = [];
        }

        $logs[] = [
            'time'    => current_time('mysql'),
            'level'   => $level,
            'message' => $message,
            'context' => $context,
        ];

        // Trim to max size
        if (count($logs) > self::MAX_LOGS) {
            $logs = array_slice($logs, -self::MAX_LOGS);
        }

        update_option(self::OPTION_NAME, $logs);
    }

    public static function info(string $message, array $context = []): void {
        self::log('info', $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::log('error', $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::log('warning', $message, $context);
    }

    public static function debug(string $message, array $context = []): void {
        self::log('debug', $message, $context);
    }

    /**
     * Retrieve logs, most recent first, optionally filtered by level.
     */
    public static function get_logs(int $count = 100, string $level = ''): array {
        $logs = get_option(self::OPTION_NAME, []);
        if (!is_array($logs)) {
            return [];
        }

        $logs = array_reverse($logs);

        if (!empty($level)) {
            $logs = array_filter($logs, function ($log) use ($level) {
                return ($log['level'] ?? '') === $level;
            });
        }

        return array_slice($logs, 0, $count);
    }

    /**
     * Get all unique chatbot IDs referenced in logs (for filter dropdown).
     */
    public static function get_chatbots_in_logs(): array {
        $logs = get_option(self::OPTION_NAME, []);
        if (!is_array($logs)) {
            return [];
        }

        $ids = [];
        foreach ($logs as $log) {
            $cid = $log['context']['chatbot_id'] ?? 0;
            if ($cid > 0) {
                $ids[$cid] = get_the_title($cid) ?: "#{$cid}";
            }
        }
        return $ids;
    }

    /**
     * Clear all logs.
     */
    public static function clear(): void {
        delete_option(self::OPTION_NAME);
    }

    /**
     * Rough token estimate: ~4 chars per token for mixed Chinese/English text.
     */
    public static function estimate_tokens(string $text): int {
        $length = mb_strlen($text);
        return (int) ceil($length / 4);
    }

    /**
     * Format a context value for display (truncate long strings).
     */
    public static function format_context_value($value, int $max_len = 200): string {
        if (is_array($value) || is_object($value)) {
            $json = wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return self::truncate($json, $max_len);
        }
        return self::truncate((string) $value, $max_len);
    }

    /**
     * Truncate text with ellipsis.
     */
    public static function truncate(string $text, int $max_len = 200): string {
        if (mb_strlen($text) <= $max_len) {
            return $text;
        }
        return mb_substr($text, 0, $max_len) . '...';
    }
}
