<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// Conditionally enable RefreshDatabase based on env
$__pest = pest()->extend(Tests\TestCase::class);
if (!env('PERSIST_TEST_DATA', false)) {
    $__pest->use(Illuminate\Foundation\Testing\RefreshDatabase::class);
}
$__pest->in('Feature', 'Unit');

// Minimal named routes used by middleware in unit tests (kernel doesn't load app routes here)
beforeAll(function () {
    \Illuminate\Support\Facades\Route::get('/exams', fn() => 'exams')
        ->name('assessments.candidate.exams.index');
    \Illuminate\Support\Facades\Route::get('/rejected', fn() => 'rejected')
        ->name('rejected');
});

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

// Helper to conditionally apply RefreshDatabase within a test file
if (!function_exists('useRefreshDatabaseConditionally')) {
    function useRefreshDatabaseConditionally(): void
    {
        if (!env('PERSIST_TEST_DATA', false)) {
            uses(Illuminate\Foundation\Testing\RefreshDatabase::class);
        }
    }
}

// Ensure a permission row compatible with our schema exists
if (!function_exists('ensurePermission')) {
    function ensurePermission(string $name, string $group = 'workflow', ?string $display = null, ?string $desc = null): void
    {
        $guard = config('auth.defaults.guard', 'web');
        $display = $display ?? $name;
        $desc = $desc ?? $name;
        // Insert directly to match custom columns (display_name, description, permission_group)
        \Illuminate\Support\Facades\DB::table('permissions')->insertOrIgnore([
            'name' => $name,
            'display_name' => $display,
            'description' => $desc,
            'permission_group' => $group,
            'guard_name' => $guard,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        try { app(Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions(); } catch (\Throwable $e) {}
    }
}

// Build a WorkflowStepSubmissionRequest bound to a given step instance and user
if (!function_exists('buildWorkflowRequest')) {
    function buildWorkflowRequest(App\Workflow\Models\WorkflowStepInstance $si, \Users\Models\User $user, array $payload, string $method = 'PUT')
    {
        $req = App\Workflow\Http\Requests\WorkflowStepSubmissionRequest::create('/workflows/tasks/'.$si->id, $method, $payload);
        $req->setUserResolver(fn() => $user);
        // Minimal route resolver exposing parameter('stepInstance') for FormRequest::route()
        $req->setRouteResolver(function () use ($si) {
            return new class($si) {
                public function __construct(private $si) {}
                public function parameter(string $key) { return $key === 'stepInstance' ? $this->si : null; }
            };
        });
        return $req;
    }
}
