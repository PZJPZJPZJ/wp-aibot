<?php
defined('ABSPATH') || exit;

class AI_Chatbot_AI_Client {

    private array $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * Send a chat completion request to the configured platform.
     * On failure, retries with fallback model if configured.
     */
    public function chat(array $messages): ?array {
        $platform = $this->config['chatbot_platform'] ?? 'openai';

        // Primary attempt
        $result = $platform === 'anthropic'
            ? $this->chat_anthropic($messages)
            : $this->chat_openai($messages);

        // Fallback retry if primary failed and fallback is configured
        if ($result === null) {
            $fallback_model = trim($this->config['chatbot_fallback_model'] ?? '');

            if ($fallback_model !== ''
                && $fallback_model !== $this->config['chatbot_model']
            ) {
                $original_model = $this->config['chatbot_model'];
                $this->config['chatbot_model'] = $fallback_model;

                $result = $platform === 'anthropic'
                    ? $this->chat_anthropic($messages)
                    : $this->chat_openai($messages);

                if ($result !== null) {
                    AI_Chatbot_Logger::info('Chat completed with fallback model', [
                        'original_model' => $original_model,
                        'fallback_model' => $fallback_model,
                    ]);
                }

                $this->config['chatbot_model'] = $original_model;
            }
        }

        return $result;
    }

    /**
     * OpenAI-compatible API (OpenAI, OpenRouter, DeepSeek, Custom).
     */
    private function chat_openai(array $messages): ?array {
        $body = [
            'model'       => $this->config['chatbot_model'] ?? 'gpt-4o-mini',
            'messages'    => $messages,
            'temperature' => (float) ($this->config['chatbot_temperature'] ?? 0.2),
            'max_tokens'  => (int) ($this->config['chatbot_max_tokens'] ?? 2000),
        ];

        $api_url = rtrim($this->config['chatbot_api_base_url'] ?? 'https://api.openai.com/v1', '/');
        $api_key = $this->config['chatbot_api_key'] ?? '';

        $response = wp_remote_post($api_url . '/chat/completions', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'body'    => wp_json_encode($body),
            'timeout' => 60,
        ]);

        if (is_wp_error($response)) {
            AI_Chatbot_Logger::error('OpenAI API HTTP error', [
                'error_message' => $response->get_error_message(),
                'api_url'       => $api_url,
            ]);
            return null;
        }

        $status = wp_remote_retrieve_response_code($response);
        $raw_body = wp_remote_retrieve_body($response);
        $data = json_decode($raw_body, true);

        if ($status !== 200) {
            $error_detail = $data['error']['message'] ?? $data['error'] ?? wp_remote_retrieve_response_message($response);
            AI_Chatbot_Logger::error('OpenAI API returned error status', [
                'status_code'    => $status,
                'error_detail'   => is_string($error_detail) ? $error_detail : wp_json_encode($error_detail),
                'model'          => $body['model'],
                'api_url'        => $api_url,
            ]);
            return null;
        }

        if (!isset($data['choices'][0]['message']['content'])) {
            AI_Chatbot_Logger::error('OpenAI API response missing content', [
                'status_code' => $status,
                'raw_response' => AI_Chatbot_Logger::truncate($raw_body, 500),
            ]);
            return null;
        }

        return [
            'content' => $data['choices'][0]['message']['content'],
            'raw'     => $data,
        ];
    }

    /**
     * Anthropic API format.
     */
    private function chat_anthropic(array $messages): ?array {
        $api_url = rtrim($this->config['chatbot_api_base_url'] ?? 'https://api.anthropic.com/v1', '/');
        $api_key = $this->config['chatbot_api_key'] ?? '';

        // Extract system message (Anthropic uses top-level "system" field)
        $system = '';
        $clean_messages = [];
        foreach ($messages as $msg) {
            if (($msg['role'] ?? '') === 'system') {
                $system .= ($system ? "\n\n" : '') . ($msg['content'] ?? '');
            } else {
                $clean_messages[] = $msg;
            }
        }

        $body = [
            'model'       => $this->config['chatbot_model'] ?? 'claude-sonnet-4-20250514',
            'messages'    => $clean_messages,
            'max_tokens'  => (int) ($this->config['chatbot_max_tokens'] ?? 2000),
            'temperature' => (float) ($this->config['chatbot_temperature'] ?? 0.2),
        ];

        if (!empty($system)) {
            $body['system'] = $system;
        }

        $response = wp_remote_post($api_url . '/messages', [
            'headers' => [
                'Content-Type'      => 'application/json',
                'x-api-key'         => $api_key,
                'anthropic-version' => '2023-06-01',
            ],
            'body'    => wp_json_encode($body),
            'timeout' => 60,
        ]);

        if (is_wp_error($response)) {
            AI_Chatbot_Logger::error('Anthropic API HTTP error', [
                'error_message' => $response->get_error_message(),
                'api_url'       => $api_url,
            ]);
            return null;
        }

        $status = wp_remote_retrieve_response_code($response);
        $raw_body = wp_remote_retrieve_body($response);
        $data = json_decode($raw_body, true);

        if ($status !== 200) {
            $error_detail = $data['error']['message'] ?? $data['error'] ?? wp_remote_retrieve_response_message($response);
            AI_Chatbot_Logger::error('Anthropic API returned error status', [
                'status_code'    => $status,
                'error_detail'   => is_string($error_detail) ? $error_detail : wp_json_encode($error_detail),
                'model'          => $body['model'],
                'api_url'        => $api_url,
            ]);
            return null;
        }

        if (!isset($data['content'][0]['text'])) {
            AI_Chatbot_Logger::error('Anthropic API response missing content', [
                'status_code'  => $status,
                'raw_response' => AI_Chatbot_Logger::truncate($raw_body, 500),
            ]);
            return null;
        }

        return [
            'content' => $data['content'][0]['text'],
            'raw'     => $data,
        ];
    }

    /**
     * [Reserved] Embeddings API for future RAG support.
     */
    public function embed(string $text): array {
        return [];
    }

    /**
     * List available models from the API.
     * For Anthropic: returns empty (manual entry only).
     * For OpenAI-compatible: calls GET {base_url}/models.
     */
    public function list_models(): array {
        $platform = $this->config['chatbot_platform'] ?? 'openai';

        if ($platform === 'anthropic') {
            return [];
        }

        $api_url = rtrim($this->config['chatbot_api_base_url'] ?? 'https://api.openai.com/v1', '/');
        $api_key = $this->config['chatbot_api_key'] ?? '';

        $response = wp_remote_get($api_url . '/models', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $models = $body['data'] ?? [];

        return array_values(array_map(function ($m) {
            return $m['id'] ?? '';
        }, array_filter($models, fn($m) => !empty($m['id']))));
    }
}
