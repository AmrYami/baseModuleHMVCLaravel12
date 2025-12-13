<?php

namespace Users\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Contracts\Support\Renderable;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends BaseController
{
    public function index(): Renderable
    {
        $logs = Activity::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(30);

        return view('users::logs.index', [
            'logs' => $logs,
            'page' => __('Activity Logs'),
            'action' => 'logs',
        ]);
    }
}
