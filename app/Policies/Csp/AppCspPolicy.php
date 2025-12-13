<?php

namespace App\Policies\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Presets\Basic;

class AppCspPolicy extends Basic
{
    public function configure(Policy $policy): void
    {
        parent::configure($policy);

        $assetUrl = rtrim(env('MEDIA_URL', env('AWS_URL', '')), '/');
        $assetHost = $this->hostFromUrl($assetUrl);
        $self = Keyword::SELF;

        $allowed = [$self];
        if ($assetHost) {
            $allowed[] = $assetHost;
        }

        $scriptSources = array_merge($allowed, [
            'https://code.jquery.com',
        ]);
        $styleSources = array_merge($allowed, [
            'https://fonts.googleapis.com',
        ]);
        $fontSources = array_merge($allowed, [
            'https://fonts.gstatic.com',
            'data:',
        ]);

        $policy
            ->add(Directive::IMG, array_merge($allowed, ['data:']))
            ->add(Directive::SCRIPT, $scriptSources)
            ->add(Directive::STYLE, $styleSources)
            ->add(Directive::FONT, $fontSources)
            ->add(Directive::MEDIA, $allowed)
            ->add(Directive::CONNECT, $allowed);

        // Honor configured report-uri if set
        if ($uri = config('csp.report_uri')) {
            $policy->setReportUri($uri);
        }
    }

    protected function hostFromUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        $parts = parse_url($url);
        if (empty($parts['host'])) {
            return null;
        }
        $scheme = $parts['scheme'] ?? 'https';
        return "{$scheme}://{$parts['host']}";
    }
}
