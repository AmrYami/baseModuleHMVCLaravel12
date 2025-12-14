<?php

namespace App\Http\Controllers\Security;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CspReportController
{
    /**
     * Handle CSP violation reports (report-to / report-uri).
     * We keep payloads small and never throw to avoid breaking client reports.
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $report = $request->json()->all() ?: json_decode((string) $request->getContent(), true);
            $client = $request->ip();
            $agent = $request->userAgent();

            Log::warning('CSP violation reported', [
                'client_ip'   => $client,
                'user_agent'  => $agent,
                'report'      => $report,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to record CSP report', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
