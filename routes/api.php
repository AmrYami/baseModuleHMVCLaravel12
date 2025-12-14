<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Security\CspReportController;

// Basic example user endpoint (kept for reference)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// CSP violation reports (used by Content-Security-Policy report-uri/report-to)
Route::post('/csp-report', CspReportController::class)->name('csp.report');
