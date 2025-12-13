@php
    $page = __('Email Triggers');
    $settings = $settings ?? (object)[];
@endphp
@extends('dashboard.mt.main')
@section('content')
  @php
    $s = (array) ($settings ?? []);
    $get = function($a,$k,$d=''){ return isset($s[$a]) && is_object($s[$a]) && isset($s[$a]->{$k}) ? $s[$a]->{$k} : $d; };
    $actions = [
        'welcome'   => 'Welcome (on registration)',
        'approved'  => 'HR Approved',
        'rejected'  => 'HR Rejected',
        'exam_pass' => 'Exam Passed',
        'exam_fail' => 'Exam Failed',
    ];
  @endphp
  <div class="container-xxl">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Email Triggers</h3>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('dashboard.setting.update', ['key' => 'emails']) }}">@csrf
          @foreach($actions as $key => $label)
            <div class="border rounded p-3 mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>{{ $label }}</strong>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="actions[{{ $key }}][enabled]" @checked($get($key,'enabled')=='on')>
                  <label class="form-check-label">Enabled</label>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Subject</label>
                  <input class="form-control" name="actions[{{ $key }}][subject]" value="{{ $get($key,'subject') }}" placeholder="Default will be used if empty">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Template</label>
                  <input class="form-control" name="actions[{{ $key }}][template]" value="{{ $get($key,'template','send_mail') }}" placeholder="send_mail">
                </div>
                <div class="col-md-12">
                  <label class="form-label">Body (optional)</label>
                  <textarea class="form-control" rows="2" name="actions[{{ $key }}][body]" placeholder="You can use placeholders: {name}, {exam_title}, {score}, {percent}">{{ $get($key,'body') }}</textarea>
                  <div class="form-text">Placeholders you can use in Subject/Body: <code>{name}</code>, <code>{exam_title}</code>, <code>{score}</code>, <code>{percent}</code></div>
                </div>
              </div>
              <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                <form method="POST" action="{{ route('dashboard.setting.test', ['key' => 'emails']) }}" class="d-flex align-items-center gap-2">@csrf
                  <input type="hidden" name="action" value="{{ $key }}">
                  <input type="email" name="to" class="form-control form-control-sm email-test-input" placeholder="test@example.com">
                  <button class="btn btn-sm btn-outline-secondary" type="submit">Send Test</button>
                </form>
                <div class="form-text">Leave email blank to send to your account ({{ auth()->user()->email ?? 'your email' }}). Test uses current Subject/Template/Body.</div>
              </div>
            </div>
          @endforeach
          @php
            $wfById = (array) ($settings->workflow_steps ?? []);
            $wfByKey= (array) ($settings->workflow ?? []); // backward compat
            $wfGet = function($stepId, $stepKey, $event, $field, $default = '') use ($wfById, $wfByKey) {
              $node = (array) ($wfById[$stepId] ?? []);
              $ev   = (array) ($node[$event] ?? []);
              if (array_key_exists($field, $ev)) return $ev[$field];
              // fallback by key
              $node2 = (array) ($wfByKey[$stepKey] ?? []);
              $ev2   = (array) ($node2[$event] ?? []);
              return $ev2[$field] ?? $default;
            };
            $grouped = ($wfSteps ?? collect())->groupBy(fn($s) => optional($s->template)->name ?? ('Template #'.$s->wf_template_id));
            $events = ['submitted' => 'Submitted', 'approved' => 'Approved', 'rejected' => 'Rejected', 'returned' => 'Returned'];
          @endphp

          <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <strong>{{ __('Workflow Steps (per step/event)') }}</strong>
            </div>
            @forelse($grouped as $tmpl => $steps)
              <div class="mb-3">
                <div class="fw-semibold mb-2">{{ $tmpl }}</div>
                @foreach($steps as $step)
                  <div class="border rounded p-3 mb-3">
                    <div class="mb-2">
                      <strong>{{ $step->name }}</strong> <span class="text-muted">({{ $step->key }})</span>
                    </div>
                    @foreach($events as $evKey => $evLabel)
                      <div class="bg-light p-3 rounded mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="fw-semibold">{{ $evLabel }}</div>
                          <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="workflow_steps[{{ $step->id }}][{{ $evKey }}][enabled]" @checked(($wfGet($step->id,$step->key,$evKey,'enabled')=='on'))>
                            <label class="form-check-label">Enabled</label>
                          </div>
                        </div>
                        <div class="row g-3">
                          <div class="col-md-4">
                            <label class="form-label">Subject</label>
                            @php($defaultSubject = $tmpl.' — '.$step->name.' — '.$evLabel)
                            <input class="form-control" name="workflow_steps[{{ $step->id }}][{{ $evKey }}][subject]" value="{{ $wfGet($step->id,$step->key,$evKey,'subject') }}" placeholder="{{ $defaultSubject }}">
                          </div>
                          <div class="col-md-4">
                            <label class="form-label">Template</label>
                            <input class="form-control" name="workflow_steps[{{ $step->id }}][{{ $evKey }}][template]" value="{{ $wfGet($step->id,$step->key,$evKey,'template','send_mail') }}" placeholder="send_mail">
                          </div>
                          <div class="col-12">
                            <label class="form-label">Body</label>
                            <textarea class="form-control" rows="2" name="workflow_steps[{{ $step->id }}][{{ $evKey }}][body]" placeholder="Optional body with placeholders: {name}, {step_name}, {template_name}">{{ $wfGet($step->id,$step->key,$evKey,'body') }}</textarea>
                          </div>
                        </div>
                        <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                          <form method="POST" action="{{ route('dashboard.setting.test', ['key' => 'emails']) }}" class="d-flex align-items-center gap-2">@csrf
                            <input type="hidden" name="step_id" value="{{ $step->id }}">
                            <input type="hidden" name="event" value="{{ $evKey }}">
                            <input type="email" name="to" class="form-control form-control-sm email-test-input" placeholder="test@example.com">
                            <button class="btn btn-sm btn-outline-secondary" type="submit">Send Test</button>
                          </form>
                          <div class="form-text">Leave email blank to send to your account ({{ auth()->user()->email ?? 'your email' }}). Uses current Subject/Template/Body for this step and event.</div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endforeach
              </div>
            @empty
              <div class="text-muted">{{ __('No workflow steps found yet.') }}</div>
            @endforelse
          </div>
          <div class="mt-4">
            <button class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
