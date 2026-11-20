<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Collector for CSP violation reports. Browsers post here when the policy is
 * in report-only mode (or when an enforced rule is breached), so the rollout
 * can be measured from real traffic instead of guesswork.
 */
final class CspReportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->json()->all() ?: $request->all();
        $report = $payload['csp-report'] ?? $payload['body'] ?? $payload;

        Log::channel('single')->warning('csp.violation', [
            'document' => $report['document-uri'] ?? null,
            'directive' => $report['effective-directive'] ?? $report['violated-directive'] ?? null,
            'blocked' => $report['blocked-uri'] ?? null,
            'source' => $report['source-file'] ?? null,
            'line' => $report['line-number'] ?? null,
            'ip' => $request->ip(),
        ]);

        return response()->noContent();
    }
}
