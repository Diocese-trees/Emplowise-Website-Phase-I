<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StudentToolkit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StudentController extends Controller
{
    public function __construct(private readonly StudentToolkit $toolkit)
    {
    }

    public function interviewQuestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'min:2', 'max:60'],
            'level' => ['required', 'string', 'in:Internship,Entry level,1 to 3 years,Career switch'],
        ]);

        return response()->json($this->toolkit->interviewQuestions($validated['role'], $validated['level']));
    }

    public function interviewFeedback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'min:2', 'max:60'],
            'level' => ['required', 'string', 'in:Internship,Entry level,1 to 3 years,Career switch'],
            'question' => ['required', 'string', 'min:8', 'max:400'],
            'answer' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        return response()->json($this->toolkit->interviewFeedback(
            $validated['role'],
            $validated['level'],
            $validated['question'],
            $validated['answer'],
        ));
    }

    public function ats(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'resume' => ['required', 'string', 'min:60', 'max:12000'],
            'job_description' => ['required', 'string', 'min:60', 'max:8000'],
        ]);

        return response()->json($this->toolkit->atsScore($validated['resume'], $validated['job_description']));
    }

    public function profile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'headline' => ['nullable', 'string', 'max:200'],
            'summary' => ['nullable', 'string', 'max:1500'],
            'bullets' => ['nullable', 'string', 'max:2000'],
            'target_role' => ['nullable', 'string', 'max:80'],
        ]);

        if (trim((string) ($validated['headline'] ?? '')) === ''
            && trim((string) ($validated['summary'] ?? '')) === ''
            && trim((string) ($validated['bullets'] ?? '')) === '') {
            return response()->json([
                'message' => 'Add a headline, a summary, or at least one bullet to rewrite.',
            ], 422);
        }

        return response()->json($this->toolkit->optimizeProfile(
            (string) ($validated['headline'] ?? ''),
            (string) ($validated['summary'] ?? ''),
            (string) ($validated['bullets'] ?? ''),
            (string) ($validated['target_role'] ?? ''),
        ));
    }
}
