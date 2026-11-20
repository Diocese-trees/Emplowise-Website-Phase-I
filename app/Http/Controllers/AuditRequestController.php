<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AuditRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Honeypot. Bots fill hidden fields, humans do not.
        if ((string) $request->input('website', '') !== '') {
            return response()->json(['message' => 'Request received.'], 202);
        }

        $validated = $request->validate([
            'contact_name' => ['required', 'string', 'min:2', 'max:120'],
            'company_name' => ['required', 'string', 'min:2', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{7,40}$/'],
            'headcount_band' => ['required', 'string', 'in:1-10,11-20,21-50,51-100,100+'],
            'pain_points' => ['required', 'array', 'min:1', 'max:6'],
            'pain_points.*' => ['string', 'in:payroll,performance,policy,compliance,recruitment,reporting'],
            'notes' => ['nullable', 'string', 'max:1500'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(60)->toDateString()],
            'preferred_slot' => ['required', 'string', 'in:Morning (9:00 to 12:00),Afternoon (12:00 to 16:00),Evening (16:00 to 19:00)'],
            'consent' => ['accepted'],
        ], [
            'consent.accepted' => 'Please confirm we can contact you about the audit.',
            'preferred_date.after_or_equal' => 'Pick a date from today onward.',
            'pain_points.required' => 'Select at least one HR pain point.',
        ]);

        $audit = AuditRequest::query()->create([
            'reference' => $this->reference(),
            'contact_name' => $validated['contact_name'],
            'company_name' => $validated['company_name'],
            'email' => Str::lower($validated['email']),
            'phone' => $validated['phone'],
            'headcount_band' => $validated['headcount_band'],
            'pain_points' => array_values(array_unique($validated['pain_points'])),
            'notes' => $validated['notes'] ?? null,
            'preferred_date' => $validated['preferred_date'],
            'preferred_slot' => $validated['preferred_slot'],
            'status' => 'new',
            'source' => 'web',
        ]);

        return response()->json([
            'message' => 'Audit request received. Keep your reference for follow up.',
            'reference' => $audit->reference,
            'preferred_date' => $audit->preferred_date?->format('d M Y'),
            'preferred_slot' => $audit->preferred_slot,
            'next_step' => 'We review your intake, prepare a gap map, and confirm the window you selected.',
        ], 201);
    }

    private function reference(): string
    {
        do {
            $reference = 'EMP-'.now()->format('ymd').'-'.strtoupper(Str::random(4));
        } while (AuditRequest::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
