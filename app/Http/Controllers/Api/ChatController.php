<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Services\EmplowiseAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class ChatController extends Controller
{
    public function __invoke(Request $request, EmplowiseAssistant $assistant): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:800'],
            'session_id' => ['nullable', 'string', 'max:64'],
            'history' => ['nullable', 'array', 'max:8'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:800'],
        ]);

        $sessionId = $validated['session_id'] ?? (string) Str::uuid();
        $message = trim($validated['message']);

        $history = array_map(
            static fn (array $turn): array => ['role' => $turn['role'], 'content' => Str::limit($turn['content'], 800)],
            $validated['history'] ?? [],
        );

        $result = $assistant->reply($message, $history);

        ChatMessage::query()->create([
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $message,
            'intent' => $result['intent'],
            'model' => $result['model'],
        ]);

        ChatMessage::query()->create([
            'session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $result['reply'],
            'intent' => $result['intent'],
            'model' => $result['model'],
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'reply' => $result['reply'],
            'intent' => $result['intent'],
            'model' => $result['model'],
            'source' => $result['source'],
        ]);
    }
}
