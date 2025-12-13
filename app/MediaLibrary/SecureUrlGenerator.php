<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class SecureUrlGenerator extends DefaultUrlGenerator
{
    /**
     * @param string $conversionName
     * @return string
     */
    public function getUrl(string $conversionName = ''): string
    {
        $diskName = $this->media->disk;
        $diskConfig = config("filesystems.disks.{$diskName}", []);
        $driver = $diskConfig['driver'] ?? null;

        // If the media lives on a local/public disk, build a local URL.
        if ($driver === 'local' || $driver === 'public' || ($diskName === 'media' && $driver === null)) {
            // build the path (including any conversions)
            $path = $this->getPathRelativeToRoot($conversionName);

            // generate an HTTPS URL
            $url = secure_asset($path);

            // append a version string if you have version_urls = true
            return $this->versionUrl($url);
        }

        $path = $this->getPathRelativeToRoot($conversionName);

        // Prefer a custom URL (CDN) if provided for cloud disks
        // Prefer an explicit CDN/base URL. If missing and the driver is s3, fall back to AWS_URL or MEDIA_URL.
        $base = rtrim($diskConfig['url'] ?? '', '/');
        if (!$base && $driver === 's3') {
            $base = rtrim(env('AWS_URL', env('MEDIA_URL', '')), '/');
        }
        if ($base) {
            // ensure root/prefix (e.g., AWS_ROOT) is included
            $root = trim($diskConfig['root'] ?? env('AWS_ROOT', ''), '/');
            $fullPath = $root ? ($root . '/' . ltrim($path, '/')) : $path;
            return $this->versionUrl($base . '/' . $fullPath);
        }

        // otherwise (e.g. S3 with no url override), try parent; on misconfig fall back to app URL
        try {
            return parent::getUrl($conversionName);
        } catch (\Throwable $e) {
            return $this->versionUrl(secure_asset($path));
        }
    }
}
