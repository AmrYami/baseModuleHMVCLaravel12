<?php

use App\Http\Controllers\Security\CspReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Basic example user endpoint protected by the Passport API guard.
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// CSP violation reports (used by Content-Security-Policy report-uri/report-to)
Route::post('/csp-report', CspReportController::class)->name('csp.report');
