<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Memory_Manager {

    /**
     * Load conversation history as an array of messages for AI context.
     */
    public function load_history(int $conversation_id, int $max_history = 10): array {
        if (!$conversation_id) {
            return [];
        }

        $history = get_post_meta($conversation_id, 'conversation_history', true);
        if (empty($history)) {
            return [];
        }

        $lines = explode("\n", trim($history));
        $messages = [];
        $count = 0;

        // Parse Markdown history format and take last N exchanges
        foreach (array_reverse($lines) as $line) {
            if ($count >= $max_history * 2) break;

            if (preg_match('/^\*\*User:\*\*\s*(.+)$/', $line, $m)) {
                array_unshift($messages, ['role' => 'user', 'content' => trim($m[1])]);
                $count++;
            } elseif (preg_match('/^\*\*Assistant:\*\*\s*(.+)$/', $line, $m)) {
                array_unshift($messages, ['role' => 'assistant', 'content' => trim($m[1])]);
                $count++;
            }
        }

        return $messages;
    }

    /**
     * Append a message exchange to the conversation history.
     */
    public function append(int $conversation_id, string $user_message, string $assistant_reply): void {
        if (!$conversation_id) {
            return;
        }

        $history = get_post_meta($conversation_id, 'conversation_history', true);
        $entry = "\n**User:** {$user_message}\n**Assistant:** {$assistant_reply}";
        $history .= $entry;

        update_post_meta($conversation_id, 'conversation_history', $history);

        // Update message count
        $count = (int) get_post_meta($conversation_id, 'conversation_message_count', true);
        update_post_meta($conversation_id, 'conversation_message_count', $count + 1);
    }
}
