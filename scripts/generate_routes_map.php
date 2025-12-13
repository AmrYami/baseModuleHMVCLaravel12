<?php

use Illuminate\Support\Str;

$basePath = dirname(__DIR__);
require $basePath . '/vendor/autoload.php';
$app = require $basePath . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Artisan::call('route:list', ['--json' => true]);
$output = \Artisan::output();
$routes = json_decode($output, true);

if (!is_array($routes)) {
    fwrite(STDERR, "Failed to decode routes JSON.\n");
    exit(1);
}

usort($routes, function ($a, $b) {
    return strcmp($a['uri'], $b['uri']) ?: strcmp($a['method'], $b['method']);
});

$outputDir = $basePath . '/docs';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$outputFile = $outputDir . '/routes_map.md';

$lines = [];
$lines[] = '# Routes Map';
$lines[] = '';
$lines[] = '| Method | URI | Name | Action | Response |';
$lines[] = '| --- | --- | --- | --- | --- |';

foreach ($routes as $route) {
    $method = $route['method'] ?? '';
    $uri = $route['uri'] ?? '';
    $name = $route['name'] ?? '';
    $action = $route['action'] ?? '';
    $response = determineResponse($action);

    $lines[] = sprintf(
        '| %s | %s | %s | %s | %s |',
        escapePipe($method),
        escapePipe($uri),
        escapePipe($name ?: '—'),
        escapePipe($action ?: 'Closure'),
        escapePipe($response)
    );
}

file_put_contents($outputFile, implode(PHP_EOL, $lines) . PHP_EOL);
echo "Route map generated at {$outputFile}" . PHP_EOL;

function determineResponse(?string $action): string
{
    if (!$action || !Str::contains($action, '@')) {
        return 'Closure / Unknown';
    }

    [$class, $method] = explode('@', $action);
    if (!class_exists($class) || !method_exists($class, $method)) {
        return 'Unknown';
    }

    try {
        $ref = new ReflectionMethod($class, $method);
        $file = $ref->getFileName();
        if (!$file || !is_file($file)) {
            return 'Unknown';
        }
        $start = $ref->getStartLine() - 1;
        $end = $ref->getEndLine() - 1;
        $source = file($file);
        $snippet = implode('', array_slice($source, $start, $end - $start + 1));

        if (preg_match("/return\\s+view\\(['\"]([^'\"]+)['\"]/i", $snippet, $m)) {
            return 'view(' . $m[1] . ')';
        }

        if (preg_match("/return\\s+Inertia::render\\(['\"]([^'\"]+)['\"]/i", $snippet, $m)) {
            return 'inertia(' . $m[1] . ')';
        }

        if (preg_match("/return\\s+redirect\\(\\)->route\\(['\"]([^'\"]+)['\"]/i", $snippet, $m)) {
            return 'redirect(route ' . $m[1] . ')';
        }

        if (preg_match("/return\\s+redirect\\(['\"]([^'\"]+)['\"]/i", $snippet, $m)) {
            return 'redirect(' . $m[1] . ')';
        }

        if (preg_match("/return\\s+back\\(/i", $snippet)) {
            return 'redirect(back)';
        }

        if (preg_match("/return\\s+response\\(\\)->json/i", $snippet)) {
            return 'json';
        }

        if (preg_match("/return\\s+response\\(.*?->download/i", $snippet)) {
            return 'download';
        }

        if (preg_match("/return\\s+\$this->success\\(/i", $snippet)) {
            return 'json(success)';
        }

        if (preg_match("/return\\s+response\\(/i", $snippet)) {
            return 'response()';
        }

        return 'Unknown';
    } catch (Throwable $e) {
        return 'Unknown';
    }
}

function escapePipe(string $value): string
{
    return str_replace('|', '\\|', $value);
}
