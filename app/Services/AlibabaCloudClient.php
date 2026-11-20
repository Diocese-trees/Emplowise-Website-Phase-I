<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin client for Alibaba Cloud Model Studio using the OpenAI compatible
 * chat completions endpoint. Credentials come from ALIBABA_CLOUD_* env vars.
 */
final class AlibabaCloudClient
{
    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{content: string, model: string}
     */
    public function chat(array $messages, float $temperature = 0.6, int $maxTokens = 800, bool $json = false): array
    {
        $key = (string) config('ai.alibaba.api_key');

        if ($key === '') {
            throw new RuntimeException('ALIBABA_CLOUD_API_KEY is not configured.');
        }

        $models = array_values(array_unique(array_filter([
            (string) config('ai.alibaba.primary_model'),
            (string) config('ai.alibaba.fallback_model'),
        ])));

        $lastError = 'No model configured.';

        foreach ($models as $model) {
            try {
                return $this->request($key, $model, $messages, $temperature, $maxTokens, $json);
            } catch (RuntimeException|ConnectionException $exception) {
                $lastError = $exception->getMessage();
            }
        }

        throw new RuntimeException('Alibaba Cloud models unavailable: '.$lastError);
    }

    public function configured(): bool
    {
        return (string) config('ai.alibaba.api_key') !== '';
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{content: string, model: string}
     */
    private function request(
        string $key,
        string $model,
        array $messages,
        float $temperature,
        int $maxTokens,
        bool $json,
    ): array {
        $url = rtrim((string) config('ai.alibaba.base_url'), '/').'/chat/completions';

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];

        if ($json) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withToken($key)
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('ai.alibaba.timeout'))
            ->connectTimeout((int) config('ai.alibaba.connect_timeout'))
            ->post($url, $payload);

        if ($response->failed()) {
            throw new RuntimeException(sprintf('Model %s returned HTTP %s: %s', $model, $response->status(), mb_substr($response->body(), 0, 300)));
        }

        $content = $response->json('choices.0.message.content');

        if (is_array($content)) {
            $content = implode('', array_map(
                static fn ($part): string => is_array($part) ? (string) ($part['text'] ?? '') : (string) $part,
                $content,
            ));
        }

        $content = trim((string) $content);

        if ($content === '') {
            throw new RuntimeException('Model '.$model.' returned an empty completion.');
        }

        return ['content' => $content, 'model' => $model];
    }
}
