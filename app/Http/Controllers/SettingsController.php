<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Repositories\SettingRepositoryShow;
use App\Services\SettingServiceStore;
use App\Workflow\Models\WorkflowStep;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends BaseController
{
    private const ALLOWED_KEYS = [
        'emails',
        'notifications',
        'privacy-and-policy',
        'registration',
    ];

    public function __construct(
        protected SettingRepositoryShow $serviceShow,
        protected SettingServiceStore $settingServiceStore
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit($key): Renderable
    {
        abort_unless(in_array($key, self::ALLOWED_KEYS, true), 404);

        $settings = $this->serviceShow->find_by(['key' => $key])->first();
        if ($settings) {
            $settings = json_decode($settings->value);
        }

        $data = ['settings' => $settings];

        // For emails page, preload workflow steps for per-step/event settings UI
        if ($key === 'emails') {
            try {
                $steps = WorkflowStep::with('template')
                    ->whereHas('template', function ($q) {
                        $q->where('is_active', true)
                            ->where(function ($q) {
                                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                            })
                            ->where(function ($q) {
                                $q->whereNull('retired_at')->orWhere('retired_at', '>', now());
                            });
                    })
                    ->orderBy('wf_template_id')
                    ->orderBy('position')
                    ->get();
                $data['wfSteps'] = $steps;
            } catch (\Throwable $e) {
                report($e);

                $data['wfSteps'] = collect();
            }
        }

        return view("dashboard.settings.$key", $data);
    }

    public function update($key, UpdateSettingRequest $request): RedirectResponse
    {
        abort_unless(in_array($key, self::ALLOWED_KEYS, true), 404);

        $setting = $this->serviceShow->find_by(['key' => $key])->first();
        if (! $setting) {
            return back()->withErrors(__('Setting not found'));
        }
        $setting = $this->settingServiceStore->update($setting->id, $request);
        if ($setting) {
            return redirect()->route('dashboard.setting.edit', ['key' => $key])->with('updated', __('messages.Updated', ['thing' => $key]));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function privacyAndPolicy()
    {
        return view('dashboard.settings.privacy-and-policy');
    }

    public function sendTest(string $key, Request $request): RedirectResponse
    {
        abort_unless($key === 'emails', 404);
        $user = $request->user();
        abort_unless($user, 403, 'Authentication required');
        $cfg = getSettingJson('emails');

        // General actions (welcome/approved/rejected/exam_*)
        if ($action = (string) $request->input('action')) {
            $node = (array) ($cfg[$action] ?? []);

            $labels = [
                'welcome' => 'Welcome (on registration)',
                'approved' => 'HR Approved',
                'rejected' => 'HR Rejected',
                'exam_pass' => 'Exam Passed',
                'exam_fail' => 'Exam Failed',
            ];
            $subjects = [
                'welcome' => 'Welcome to Fakeeh Care',
                'approved' => 'Your Profile Has Been Approved',
                'rejected' => 'Your Application Status',
                'exam_pass' => 'Exam Passed — Congratulations',
                'exam_fail' => 'Exam Result — Please Review',
            ];

            $subject = trim((string) (($node['subject'] ?? '') ?: ($subjects[$action] ?? ('Notification: '.ucwords(str_replace('_', ' ', $action))))));
            $template = trim((string) (($node['template'] ?? '') ?: 'send_mail'));
            $body = (string) (($node['body'] ?? '') ?: ('This is a test email for '.($labels[$action] ?? $action)));

            $payload = ['title' => $subject, 'body' => $body];
            $replacements = $payload + [
                'name' => $user->name ?? strtok((string) $user->email, '@'),
                'exam_title' => 'Sample Exam',
                'percent' => 80,
                'score' => 8,
            ];
            $payload = $this->replacePlaceholders($payload, $replacements);
            $subject = $payload['title'];

            try {
                $to = trim((string) $request->input('to'));
                if ($to !== '' && ! validateMail($to)) {
                    return back()->withErrors(['to' => 'Please enter a valid email address.']);
                }
                $recipient = $to !== '' ? $to : $user;
                processSendMails($recipient, $subject, $template, $payload, false);
                $sentTo = is_string($recipient) ? $recipient : ($recipient->email ?? 'your email');

                return back()->with('updated', 'Test email sent to '.$sentTo);
            } catch (\Throwable $e) {
                report($e);

                return back()->withErrors('Failed to send test email. Please check your mail configuration.');
            }
        }

        // Workflow per-step events
        $stepId = (int) $request->input('step_id');
        $stepKey = (string) $request->input('step_key');
        $event = (string) $request->input('event');
        if (($stepId || $stepKey) && $event) {
            $node = [];
            if ($stepId) {
                $node = (array) data_get($cfg, "workflow_steps.$stepId.$event", []);
            }
            if (! $node && $stepKey) {
                $node = (array) data_get($cfg, "workflow.$stepKey.$event", []); // backward compat
            }
            // Compute default subject using workflow name and step name
            $step = $stepId
                ? WorkflowStep::with('template')->find($stepId)
                : WorkflowStep::with('template')->where('key', $stepKey)->first();
            $tmplName = $step?->template?->name ?? 'Workflow';
            $stepName = $step?->name ?? ($stepKey ?: ('Step #'.$stepId));
            $eventLabel = ucwords(str_replace('_', ' ', $event));

            $subject = trim((string) ($node['subject'] ?? '')) ?: ("$tmplName — $stepName — $eventLabel");
            $template = trim((string) ($node['template'] ?? '')) ?: 'send_mail';
            $body = (string) ($node['body'] ?? 'This is a test for '.$eventLabel.' ('.$tmplName.' — '.$stepName.')');

            $payload = [
                'title' => $subject,
                'body' => $body,
                'step_name' => $stepName,
                'template_name' => $tmplName,
                'event' => $event,
            ];
            $replacements = $payload + ['name' => $user->name ?? strtok((string) $user->email, '@')];
            $payload = $this->replacePlaceholders($payload, $replacements);
            $subject = $payload['title'];

            try {
                $to = trim((string) $request->input('to'));
                if ($to !== '' && ! validateMail($to)) {
                    return back()->withErrors(['to' => 'Please enter a valid email address.']);
                }
                $recipient = $to !== '' ? $to : $user;
                processSendMails($recipient, $subject, $template, $payload, false);
                $sentTo = is_string($recipient) ? $recipient : ($recipient->email ?? 'your email');

                return back()->with('updated', 'Test email sent ('.($step?->key ?? $stepKey ?: $stepId).' '.$event.') to '.$sentTo);
            } catch (\Throwable $e) {
                report($e);

                return back()->withErrors('Failed to send test email. Please check your mail configuration.');
            }
        }

        return back()->withErrors('Invalid test request.');
    }

    private function replacePlaceholders(array $payload, array $replacements): array
    {
        foreach (['title', 'body'] as $key) {
            if (! isset($payload[$key]) || ! is_string($payload[$key])) {
                continue;
            }
            $payload[$key] = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($match) use ($replacements) {
                return (string) ($replacements[$match[1]] ?? $match[0]);
            }, $payload[$key]);
        }

        return $payload;
    }
}
