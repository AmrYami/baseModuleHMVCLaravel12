<?php


use Illuminate\Support\Facades\Http;

if (!function_exists('getLang')) {
    function getLang()
    {
        return app()->getLocale();
    }
}


if (!function_exists('serverName')) {
    function serverName()
    {
        $sslOrNot = env('HTTPS');
        if ($sslOrNot == FALSE)
            $sslOrNot = 'http://';
        else
            $sslOrNot = 'https://';
        return $sslOrNot . request()->getHost();
    }
}


if (!function_exists('detectIsMobile')) {
    function detectIsMobile()
    {
        $detect = new \Detection\MobileDetect();
        return $detect->isMobile();
    }
}

if (!function_exists('displayFiles')) {
    function displayFiles($path)
    {
        return secure_asset($path);
    }
}

if (!function_exists('getCountry')) {
    function getCountry($code, $lang = 'en', $fromCompany = null)
    {
        if (!$code)
            return null;
        if ($fromCompany && is_numeric($code))
            $country = \App\Models\CountryModel::where('id', $code)->first();
        else
            $country = \App\Models\CountryModel::where('key', $code)->first();
        if ($lang == 'en') {
            return $country->name;
        }
        return $country->getTranslation('name', 'ar');
    }
}

if (!function_exists('storeLogs')) {
    function storeLogs($key, $value)
    {
        \DB::table('errors_log')->insert([
            [
                "key" => $key,
                "value" => $value,

            ],
        ]);
    }
}

if (!function_exists('validateMail')) {
    function validateMail($email)
    {

        $apiKey = env('ZERO_BOUNCE_API', '');
        $baseUrl = rtrim(env('ZERO_URL', ''), '/');

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->get("$baseUrl/validate", [
            'api_key' => $apiKey,
            'email' => $email,
        ]);

        // if the HTTP call failed or the status isn’t valid/unknown
        if ($response->failed()
            || !in_array(
                $response->json('status', ''),
                ['valid', 'unknown', 'catch-all'],
                true
            )
        ) {
            return false;
        } else {
            return true;
        }
    }
}

if(!function_exists('isActivePage')){
    /**
     * [isActivePage checks if the current page is active or not]
     * @param string $page
     * @param string|array $sourcePages
     *
     * @return bool
     */
    function isActivePage(string $page, string|array $sourcePages): bool
    {
        if(is_string($sourcePages)){
            $sourcePages = [$sourcePages];
        }

        return (bool) in_array($page, $sourcePages);
    }
}
if(!function_exists('jsonMessage')){
function jsonMessage(
    $data = [],
    string $message ='',
    int $code = 200,
    array $extra = []
){
    return response()->json([
        'message' => $message,
        'data' => $data,
    ] + $extra, $code);
    }
}

if (!function_exists('hid')) {
    /**
     * Encode an ID (or model) into a short hashid string.
     */
    function hid(int|string|\Illuminate\Database\Eloquent\Model|null $value): string
    {
        return \App\Support\IdHasher::encode($value);
    }
}

if (!function_exists('hid_decode')) {
    /**
     * Decode a hashid (or passthrough numeric) back to an integer.
     */
    function hid_decode(int|string|null $value): ?int
    {
        return \App\Support\IdHasher::decode($value);
    }
}

if (!function_exists('getSettingJson')) {
    function getSettingJson(string $key): array
    {
        try {
            $rec = \App\Models\SettingModel::query()->where('key', $key)->first();
            if (!$rec || !$rec->value) return [];
            $val = json_decode($rec->value, true);
            return is_array($val) ? $val : [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('getDefaultRegistrationStatus')) {
    function getDefaultRegistrationStatus(): int
    {
        $cfg = getSettingJson('registration');
        $status = strtolower((string) ($cfg['default_status'] ?? 'pending'));
        return $status === 'active' ? 1 : 0;
    }
}

if (!function_exists('sendActionMail')) {
    /**
     * Send email for a named action if enabled in settings (key: 'emails').
     * Available actions: welcome, approved, rejected, exam_pass, exam_fail
     */
    function sendActionMail(string $action, object|string $recipient, array $data = [], bool $queue = true): void
    {
        $cfg = getSettingJson('emails');
        $node = $cfg[$action] ?? null;
        // Backward compatibility: simple checkbox format
        if (!is_array($node)) {
            $enabled = (bool) ($node === 'on' || $node === true);
            $node = ['enabled' => $enabled ? 'on' : 'off'];
        } else {
            $enabled = (bool) (($node['enabled'] ?? '') === 'on' || ($node['enabled'] ?? false) === true);
        }
        if (!$enabled) return;

        // Default subjects per action; templates can be a simple shared one
        $subjects = [
            'welcome'   => 'Welcome to Fakeeh Care',
            'approved'  => 'Your Profile Has Been Approved',
            'rejected'  => 'Your Application Status',
            'exam_pass' => 'Exam Passed — Congratulations',
            'exam_fail' => 'Exam Result — Please Review',
        ];
        $subject = trim((string) ($node['subject'] ?? '')) ?: ($subjects[$action] ?? ('Notification: '.ucwords(str_replace('_',' ', $action))));
        $template = trim((string) ($node['template'] ?? '')) ?: 'send_mail';

        // Normalize data defaults
        $payload = [
            'title' => $data['title'] ?? $subject,
            'body'  => $data['body'] ?? ($node['body'] ?? ($data['message'] ?? '')),
        ] + $data;

        // Basic placeholder replacement e.g. {name}, {exam_title}
        $replacements = $payload + [
            'name' => is_object($recipient) ? ($recipient->name ?? '') : strtok((string) $recipient, '@'),
        ];
        foreach (['title','body'] as $k) {
            if (!isset($payload[$k]) || !is_string($payload[$k])) continue;
            $payload[$k] = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function($m) use ($replacements){
                return (string) ($replacements[$m[1]] ?? $m[0]);
            }, $payload[$k]);
        }

        processSendMails($recipient, $subject, $template, $payload, $queue);
    }
}


/**
 * Send an email using the shared MailService with a unified data contract.
 *
 * @param object|string $recipient  User model (with ->email/->name) or an email string
 * @param string        $subject    Email subject
 * @param string        $template   Blade/MJML template key used by MailService
 * @param array         $data       Unified payload:
 *                                  - title:   string  (defaults to $subject)
 *                                  - body:    string  (defaults to empty)
 *                                  - name:    string  (defaults to recipient name or email local-part)
 *                                  - ...any additional keys the template expects
 * @param bool          $queue      Whether to queue (passed to MailService::sendMail second arg)
 */
function processSendMails(object|string $recipient, string $subject, string $template, array $data = [], bool $queue = false): void
{
    $email = is_object($recipient) ? ($recipient->email ?? null) : (string) $recipient;
    if (!$email) {
        return; // nothing to send
    }

    if (!validateMail($email)) {
        return;
    }

    $fallbackName = '';
    if (is_object($recipient)) {
        $fallbackName = (string) ($recipient->name ?? '');
    }
    if ($fallbackName === '' && is_string($email)) {
        $fallbackName = strtok($email, '@') ?: '';
    }

    // Normalize the payload to a consistent shape while preserving extra keys
    $normalized = [
        'title' => $data['title'] ?? ($data['subject'] ?? $subject),
        'body'  => $data['body'] ?? ($data['message'] ?? ''),
        'name'  => $data['name'] ?? $fallbackName,
    ] + $data; // keep any additional template-specific keys

    $mailService = new \App\Mail\MailService(
        subject: $subject,
        template: $template,
        data: $normalized
    );

    $mailService->sendMail($email, $queue);
}

if (!function_exists('csp_nonce')) {
    /**
     * Expose the CSP nonce generated by spatie/laravel-csp for Blade templates.
     */
    function csp_nonce(): string
    {
        try {
            if (!config('csp.enabled', true) || !config('csp.nonce_enabled', true)) {
                return '';
            }

            return app()->has('csp-nonce') ? (string) app('csp-nonce') : '';
        } catch (\Throwable $e) {
            return '';
        }
    }
}
