<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Mail\MailService;

// Forgot-password form
//Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
//    ->middleware('guest')
//    ->name('password.request');
//
//// Password reset form (you already added this)
//Route::get('/password/reset/{token}', [\Laravel\Fortify\Http\Controllers\NewPasswordController::class, 'create'])
//    ->middleware('guest')web
//    ->name('password.reset'); 0 to 150


Route::get('/', function () {
    return view('welcome');
});

// Rejected landing (no sidebar) — must be accessible when gated
Route::middleware(['auth:doctor,web', config('jetstream.auth_session'), 'verified'])
    ->get('/rejected', function(){ return view('auth.rejected'); })
    ->name('rejected');

Route::middleware([
    'auth:doctor,web',
    config('jetstream.auth_session'),
    'verified',
    'exam.gate',
])->group(function () {



Route::get('/dashboard', function () {
    return view('dashboard.home');
})->name('dashboard');

Route::group([
    'prefix' => 'dashboard',
    'as' => 'dashboard.'
], function () {
    // settings
    Route::get('setting/{key}', [\App\Http\Controllers\SettingsController::class, 'edit'])->middleware('role_or_permission:setting-notifications')->name('setting.edit');
    Route::post('setting/{key}', [\App\Http\Controllers\SettingsController::class, 'update'])->middleware('role_or_permission:setting-notifications')->name('setting.update');
    Route::post('setting/{key}/test', [\App\Http\Controllers\SettingsController::class, 'sendTest'])->middleware('role_or_permission:setting-notifications')->name('setting.test');


});
});

Route::get('/two-factor-challenge', [\App\Http\Controllers\Auth\CustomTwoFactorChallengeController::class, 'create'])
    ->middleware(['guest:' . config('fortify.guard')])
    ->name('two-factor.login');
//general
Route::get('privacy-and-policy', function () {
    return view('privacy-and-policy');
})->name('privacy-and-policy');

Route::get('contact-us', function () {
    return view('contact-us');
})->name('contact-us');
//Route::get('/', function () {
//    return view('dashboard');
//});
//Route::middleware(['web', 'auth', 'verified'])->group(function () {
//
//    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
//    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
//    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
//
//    Route::get('SeeAllNotifications', ['as' => 'SeeAllNotifications', 'uses' => [\App\Http\Controllers\NotificationController::class, 'seeAllNotifications']])->middleware(['web', 'auth']);
//
//    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//
//});

// Workflow Admin
//Route::prefix('admin/workflows')->name('admin.workflows.')->middleware(['auth','verified'])->group(function () {
//    // Templates
//    Route::resource('templates', \App\Workflow\Http\Controllers\Admin\WorkflowTemplateController::class)
//        ->middlewareFor(['index','show'],  'permission:workflow.templates.index')
//        ->middlewareFor('create',          'permission:workflow.templates.create')
//        ->middlewareFor('store',           'permission:workflow.templates.store')
//        ->middlewareFor('edit',            'permission:workflow.templates.edit')
//        ->middlewareFor('update',          'permission:workflow.templates.update')
//        ->middlewareFor('destroy',         'permission:workflow.templates.destroy');
//
//    // Steps nested under template
//    Route::resource('templates.steps', \App\Workflow\Http\Controllers\Admin\WorkflowStepController::class)
//        ->shallow()
//        ->middlewareFor(['index','show'],  'permission:workflow.steps.index')
//        ->middlewareFor('create',          'permission:workflow.steps.create')
//        ->middlewareFor('store',           'permission:workflow.steps.store')
//        ->middlewareFor('edit',            'permission:workflow.steps.edit')
//        ->middlewareFor('update',          'permission:workflow.steps.update')
//        ->middlewareFor('destroy',         'permission:workflow.steps.destroy');
//
//    Route::post('templates/{template}/steps/{step}/up', [\App\Workflow\Http\Controllers\Admin\WorkflowStepController::class, 'moveUp'])
//        ->middleware('permission:workflow.steps.reorder')
//        ->name('templates.steps.up');
//    Route::post('templates/{template}/steps/{step}/down', [\App\Workflow\Http\Controllers\Admin\WorkflowStepController::class, 'moveDown'])
//        ->middleware('permission:workflow.steps.reorder')
//        ->name('templates.steps.down');
//
//    // Instances board (read-only list for now)
//    Route::get('instances', [\App\Workflow\Http\Controllers\Admin\WorkflowInstanceController::class, 'index'])
//        ->middleware('permission:workflow.instances.index')
//        ->name('instances.index');
//    Route::post('instances', [\App\Workflow\Http\Controllers\Admin\WorkflowInstanceController::class, 'store'])
//        ->middleware('permission:workflow.instances.override')
//        ->name('instances.store');
//    Route::get('instances/{instance}', [\App\Workflow\Http\Controllers\Admin\WorkflowInstanceController::class, 'show'])
//        ->middleware('permission:workflow.instances.index')
//        ->name('instances.show');
//    Route::post('reminders/overdue', [\App\Workflow\Http\Controllers\Admin\WorkflowInstanceController::class, 'sendOverdueReminders'])
//        ->middleware('permission:workflow.instances.override')
//        ->name('reminders.overdue');
//});

// Workflow Department User routes
//Route::prefix('workflows')->name('workflows.')->middleware(['auth','verified'])->group(function () {
//    Route::get('tasks', [\App\Workflow\Http\Controllers\TasksController::class, 'index'])
//        ->name('tasks.index');
//    Route::get('tasks/{stepInstance}', [\App\Workflow\Http\Controllers\TasksController::class, 'edit'])
//        ->name('tasks.edit');
//    Route::put('tasks/{stepInstance}', [\App\Workflow\Http\Controllers\TasksController::class, 'update'])
//        ->name('tasks.update');
//    Route::post('tasks/{stepInstance}/approve', [\App\Workflow\Http\Controllers\TasksController::class, 'approve'])
//        ->name('tasks.approve');
//    Route::post('tasks/{stepInstance}/reject', [\App\Workflow\Http\Controllers\TasksController::class, 'reject'])
//        ->name('tasks.reject');
//    Route::post('tasks/{stepInstance}/returned', [\App\Workflow\Http\Controllers\TasksController::class, 'returned'])
//        ->name('tasks.returned');
//    Route::post('tasks/{stepInstance}/comments', [\App\Workflow\Http\Controllers\TasksController::class, 'comment'])
//        ->name('tasks.comments.store');
//});
