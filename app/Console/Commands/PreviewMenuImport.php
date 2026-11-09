<?php

namespace App\Console\Commands;

use App\Services\Menu\MenuImporter;
use Illuminate\Console\Command;
use InvalidArgumentException;

class PreviewMenuImport extends Command
{
    // Changing the photographed source needs an explicit code review and a new approved hash.
    private const APPROVED_SOURCE_SHA256 = 'e2f24244fead99495868a9728091ca26bc4218fbb200428f9205b4d159d65bc3';

    protected $signature = 'menu:preview {--source=database/menu/alshamy-2026-11-09.csv : Source relative to project root} {--json : Print the complete reviewable diff}';

    protected $description = 'Compare the approved photographed menu with the current database without changing anything.';

    public function handle(MenuImporter $importer): int
    {
        try {
            $source = self::approvedSourcePath((string) $this->option('source'));
            $plan = $importer->preview($source);
            if ($this->option('json')) {
                $this->line(json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            } else {
                $this->info('Menu preview: no database changes were made.');
                $this->table(['Action', 'Count'], collect($plan['counts'])->map(fn ($count, $name) => [$name, $count])->values()->all());
                $this->line('Review digest: '.$plan['digest']);
                foreach ($plan['actions'] as $action) {
                    if ($action['status'] === 'review' || $action['status'] === 'price_change' || $action['status'] === 'addon_update') {
                        $this->warn($action['menu_key'].' '.$action['name'].' ['.$action['status'].'] '.($action['reason'] ?? json_encode($action['updates'], JSON_UNESCAPED_UNICODE)));
                    }
                }
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    public static function approvedSourcePath(string $source): string
    {
        $project = realpath(base_path());
        $expected = realpath(base_path('database/menu/alshamy-2026-11-09.csv'));
        $actual = realpath(base_path($source));
        $expectedDirectory = $project === false
            ? null
            : $project.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'menu';

        if (! $project || ! $expected || ! $actual || ! $expectedDirectory
            || ! self::samePath(dirname($expected), $expectedDirectory)
            || ! self::samePath($actual, $expected)
            || ! is_file($actual)) {
            throw new InvalidArgumentException('Only the approved canonical menu CSV database/menu/alshamy-2026-11-09.csv is permitted.');
        }

        if (! hash_equals(self::APPROVED_SOURCE_SHA256, (string) hash_file('sha256', $actual))) {
            throw new InvalidArgumentException('The approved canonical menu CSV fingerprint changed; review and pin its SHA256 before using the CLI.');
        }

        return $actual;
    }

    private static function samePath(string $first, string $second): bool
    {
        return PHP_OS_FAMILY === 'Windows'
            ? strcasecmp($first, $second) === 0
            : strcmp($first, $second) === 0;
    }
}
