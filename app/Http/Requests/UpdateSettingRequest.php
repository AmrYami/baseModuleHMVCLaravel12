<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->key == 'notifications'){
                $this->replace(['key'=> 'notifications', 'value'=> json_encode([
                    'approval_profile' =>$this->approval_profile,
                    'new_account' =>$this->new_account,
                    'notifiers' =>$this->notifiers,
                ])]);
            }
            if ($this->key == 'emails'){
                $payload = [];
                // New structured format
                if ($this->has('actions')) {
                    foreach ((array) $this->actions as $k => $v) {
                        $payload[$k] = [
                            'enabled'  => ($v['enabled'] ?? null) ? 'on' : 'off',
                            'subject'  => $v['subject'] ?? '',
                            'template' => $v['template'] ?? 'send_mail',
                            'body'     => $v['body'] ?? '',
                        ];
                    }
                } else {
                    // Backward compatibility with simple checkboxes
                    $payload = [
                        'welcome' => $this->welcome,
                        'approved' => $this->approved,
                        'rejected' => $this->rejected,
                        'exam_pass' => $this->exam_pass,
                        'exam_fail' => $this->exam_fail,
                    ];
                }
                // Workflow step emails from structured inputs
                if (is_array($this->input('workflow_steps'))) {
                    $wfStepsPayload = [];
                    foreach ((array) $this->input('workflow_steps') as $stepId => $events) {
                        foreach ((array) $events as $evKey => $node) {
                            $wfStepsPayload[$stepId][$evKey] = [
                                'enabled'  => !empty($node['enabled']) ? 'on' : 'off',
                                'subject'  => $node['subject'] ?? '',
                                'template' => $node['template'] ?? 'send_mail',
                                'body'     => $node['body'] ?? '',
                            ];
                        }
                    }
                    $payload['workflow_steps'] = $wfStepsPayload;
                } elseif (is_array($this->input('workflow'))) {
                    $wfPayload = [];
                    foreach ((array) $this->input('workflow') as $stepKey => $events) {
                        foreach ((array) $events as $evKey => $node) {
                            $wfPayload[$stepKey][$evKey] = [
                                'enabled'  => !empty($node['enabled']) ? 'on' : 'off',
                                'subject'  => $node['subject'] ?? '',
                                'template' => $node['template'] ?? 'send_mail',
                                'body'     => $node['body'] ?? '',
                            ];
                        }
                    }
                    $payload['workflow'] = $wfPayload;
                } elseif ($this->filled('workflow_json')) {
                    // Fallback: JSON textarea
                    $wf = json_decode($this->input('workflow_json'), true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($wf)) {
                        $payload['workflow'] = $wf;
                    }
                }
                $this->replace(['key'=> 'emails', 'value'=> json_encode($payload)]);
            }

            if ($this->key == 'registration') {
                $status = $this->input('default_status', 'pending');
                $this->replace(['key' => 'registration', 'value' => json_encode([
                    'default_status' => $status,
                ])]);
            }
        });
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->key === 'registration') {
            return [
                'default_status' => ['required', Rule::in(['pending', 'active'])],
            ];
        }

        if ($this->key === 'notifications') {
            return [
                'new_account' => 'sometimes',
                'approval_profile' => 'sometimes',
                'notifiers' => 'sometimes',
            ];
        }

        return [];
    }
}
