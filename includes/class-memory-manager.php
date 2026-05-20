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

        $lines = explode("\n", $history);
        $messages = [];
        $current_role = null;
        $current_content = [];

        // Parse multi-line messages: accumulate content until next marker
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/^\*\*(User|Assistant):\*\*\s*(.*)$/', $line, $m)) {
                // Save previous message
                if ($current_role !== null) {
                    $messages[] = [
                        'role'    => $current_role,
                        'content' => implode("\n", $current_content),
                    ];
                }
                $current_role = strtolower($m[1]);
                $current_content = [trim($m[2])];
            } elseif ($current_role !== null) {
                $current_content[] = $line;
            }
        }
        // Save last message
        if ($current_role !== null) {
            $messages[] = [
                'role'    => $current_role,
                'content' => implode("\n", $current_content),
            ];
        }

        // Keep only the last N complete rounds (user + assistant pairs)
        $total = count($messages);
        if ($total > $max_history * 2) {
            $messages = array_slice($messages, $total - $max_history * 2);
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
