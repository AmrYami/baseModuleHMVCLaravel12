<?php

$basePath = dirname(__DIR__);
$migrationDir = $basePath . '/database/migrations';
$outputDir = $basePath . '/docs';
$outputFile = $outputDir . '/database_diagram.mmd';

if (!is_dir($migrationDir)) {
    fwrite(STDERR, "Migration directory not found: {$migrationDir}\n");
    exit(1);
}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$files = glob($migrationDir . '/*.php');
sort($files);

$tables = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    if (!preg_match_all("/Schema::create\\(['\"]([^'\"]+)['\"],\\s*function\\s*\\(.*?\\)\\s*\\{(.*?)\\n\\s*\\}\\);/s", $content, $matches, PREG_SET_ORDER)) {
        continue;
    }

    foreach ($matches as $match) {
        $tableName = $match[1];
        $body = $match[2];
        $lines = preg_split('/\\r?\\n/', $body);
        $columns = [];
        foreach ($lines as $line) {
            if (preg_match('/\$\w+->([a-zA-Z_]+)\(([^;]*)\)/', $line, $colMatch)) {
                $method = $colMatch[1];
                $argsRaw = trim($colMatch[2]);
                $columnName = null;
                if (preg_match("/['\"]([^'\"]+)['\"]/", $argsRaw, $nameMatch)) {
                    $columnName = $nameMatch[1];
                }

                $type = mapColumnType($method);

                if ($method === 'timestamps') {
                    $columns[] = ['created_at', 'timestamp'];
                    $columns[] = ['updated_at', 'timestamp'];
                    continue;
                }

                if ($method === 'softDeletes') {
                    $columns[] = ['deleted_at', 'timestamp'];
                    continue;
                }

                if ($method === 'rememberToken') {
                    $columns[] = ['remember_token', 'string'];
                    continue;
                }

                $skip = ['index', 'unique', 'primary', 'foreign', 'foreignIdFor', 'dropColumn', 'dropForeign', 'dropUnique', 'dropPrimary', 'constrained'];
                if (in_array($method, $skip, true)) {
                    continue;
                }

                if ($method === 'morphs' || $method === 'uuidMorphs' || $method === 'nullableMorphs') {
                    $base = $columnName ?? '';
                    if ($base) {
                        $columns[] = [$base . '_type', 'string'];
                        $columns[] = [$base . '_id', $method === 'uuidMorphs' ? 'uuid' : 'unsignedBigInteger'];
                    }
                    continue;
                }

                if ($columnName === null) {
                    if (in_array($method, ['id', 'increments', 'bigIncrements'], true)) {
                        $columns[] = ['id', 'id'];
                    }
                    continue;
                }

                $columns[] = [$columnName, $type];
            }
        }

        if (!isset($tables[$tableName])) {
            $tables[$tableName] = [];
        }

        foreach ($columns as $col) {
            [$name, $type] = $col;
            $tables[$tableName][$name] = $type;
        }
    }
}

ksort($tables);

$lines = [];
$lines[] = "erDiagram";
foreach ($tables as $table => $columns) {
    $lines[] = "    " . formatIdentifier($table) . " {";
    foreach ($columns as $name => $type) {
        $lines[] = "        " . $type . " " . $name;
    }
    $lines[] = "    }";
}

file_put_contents($outputFile, implode(PHP_EOL, $lines) . PHP_EOL);
echo "Database diagram generated at {$outputFile}" . PHP_EOL;

function mapColumnType(string $method): string
{
    return match ($method) {
        'id', 'increments' => 'int',
        'bigIncrements' => 'bigint',
        'string', 'char', 'text', 'longText', 'mediumText', 'tinyText' => 'string',
        'integer', 'unsignedInteger', 'smallInteger', 'unsignedSmallInteger' => 'int',
        'bigInteger', 'unsignedBigInteger' => 'bigint',
        'float', 'double', 'decimal' => 'decimal',
        'boolean' => 'bool',
        'json', 'jsonb' => 'json',
        'uuid' => 'uuid',
        'date', 'dateTime', 'dateTimeTz' => 'datetime',
        'time', 'timeTz' => 'time',
        'timestamp', 'timestampTz' => 'timestamp',
        'foreignId', 'foreignUuid' => 'foreign',
        default => $method,
    };
}

function formatIdentifier(string $value): string
{
    return preg_replace('/[^A-Za-z0-9_]/', '_', $value);
}
