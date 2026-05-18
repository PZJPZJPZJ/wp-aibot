<?php
defined('ABSPATH') || exit;

class AI_Chatbot_Knowledge_Loader {

    /**
     * Full-text injection strategy: loads all bound knowledge documents
     * as plain text context. [Reserved for future RAG replacement.]
     */
    public function load_context(int $chatbot_id): string {
        $knowledge_ids = get_post_meta($chatbot_id, 'chatbot_knowledge_ids', true);
        if (empty($knowledge_ids)) {
            return '';
        }

        $parts = [];
        foreach ((array) $knowledge_ids as $doc_id) {
            $doc = get_post($doc_id);
            if (!$doc || $doc->post_status !== 'publish') {
                continue;
            }
            $markdown = get_post_meta($doc_id, 'knowledge_markdown', true);
            if (!empty($markdown)) {
                $parts[] = "---\nSource: {$doc->post_title}\n{$markdown}\n---";
            }
        }

        $context = implode("\n\n", $parts);

        return apply_filters('ai_chatbot_knowledge_context', $context, $chatbot_id);
    }
}
