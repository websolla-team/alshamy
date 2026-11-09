<?php

namespace App\Console\Commands;

use App\Services\Menu\MenuImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use LogicException;

class ApplyMenuImport extends Command
{
    protected $signature = 'menu:apply
        {--source=database/menu/alshamy-2026-11-09.csv : Source relative to project root}
        {--digest= : Exact sha256 from the read-only preview}
        {--confirm-staging : Confirm the target is an isolated non-production database}
        {--accept-price-changes : Allow reviewed changes to existing confirmed prices}';

    protected $description = 'Apply reviewed menu differences to an isolated staging database only.';

    public function handle(MenuImporter $importer): int
    {
        try {
            if (! $this->option('confirm-staging')) {
                throw new LogicException('Use --confirm-staging after verifying the database target.');
            }
            if (! app()->environment(['local', 'testing', 'staging'])) {
                throw new LogicException('Imports are disabled outside local/testing/staging.');
            }
            $path = PreviewMenuImport::approvedSourcePath((string) $this->option('source'));
            $this->assertLocalSqliteTarget();

            $plan = $importer->preview($path);
            if ($this->option('accept-price-changes')) {
                $this->warn('Operator approval gate only: --accept-price-changes does not prove an independent signed review.');
                $this->warn('For independent approval, require a separately approved SHA256 review file before enabling price changes.');
            }
            $result = $importer->apply($plan, $path, (string) $this->option('digest'), (bool) $this->option('accept-price-changes'));
            $this->info('Staging menu import completed.');
            $this->table(['Result', 'Count'], collect($result)->map(fn ($n, $key) => [$key, $n])->values()->all());

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function assertLocalSqliteTarget(): void
    {
        $connectionName = (string) config('database.default');
        $config = config("database.connections.{$connectionName}");
        $message = 'CLI apply requires a local SQLite file under database/ or storage/, with staging/test in its filename.';

        if (! is_array($config) || ($config['driver'] ?? null) !== 'sqlite'
            || ! empty($config['url']) || ! empty($config['read']) || ! empty($config['write'])) {
            throw new LogicException($message);
        }

        $configuredPath = $config['database'] ?? null;
        if (! is_string($configuredPath) || ! $configuredPath
            || $configuredPath === ':memory:'
            || str_contains($configuredPath, '://')
            || str_starts_with($configuredPath, '//')
            || str_starts_with($configuredPath, '\\\\')) {
            throw new LogicException($message);
        }

        $target = realpath($configuredPath);
        $projectRoot = realpath(base_path());
        $databaseRoot = realpath(database_path());
        $storageRoot = realpath(storage_path());
        $name = $target ? basename($target) : '';
        $databaseIsLocal = $projectRoot && $databaseRoot
            && self::samePath($databaseRoot, $projectRoot.DIRECTORY_SEPARATOR.'database');
        $storageIsLocal = $projectRoot && $storageRoot
            && self::samePath($storageRoot, $projectRoot.DIRECTORY_SEPARATOR.'storage');
        if (! $target || ! is_file($target)
            || ! preg_match('/\.(sqlite)$/i', $name)
            || ! preg_match('/(?:staging|test)/i', $name)
            || (! ($databaseIsLocal && self::underDirectory($target, $databaseRoot))
                && ! ($storageIsLocal && self::underDirectory($target, $storageRoot)))) {
            throw new LogicException($message);
        }

        // Check the live PDO as well as its config: a cached/overridden connection
        // must not silently point at a different file or an external server.
        $connection = DB::connection($connectionName);
        if ($connection->getDriverName() !== 'sqlite') {
            throw new LogicException($message);
        }
        $databases = $connection->select('PRAGMA database_list');
        $connectedFile = $databases[0]->file ?? '';
        $connectedPath = $connectedFile ? realpath($connectedFile) : false;
        if (! $connectedPath || ! self::samePath($connectedPath, $target)) {
            throw new LogicException($message.' The connected SQLite file does not match the approved path.');
        }
    }

    private static function underDirectory(string $file, string|false $directory): bool
    {
        if (! $directory) {
            return false;
        }

        $prefix = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return PHP_OS_FAMILY === 'Windows'
            ? strncasecmp($file, $prefix, strlen($prefix)) === 0
            : strncmp($file, $prefix, strlen($prefix)) === 0;
    }

    private static function samePath(string $first, string $second): bool
    {
        return PHP_OS_FAMILY === 'Windows'
            ? strcasecmp($first, $second) === 0
            : strcmp($first, $second) === 0;
    }
}
