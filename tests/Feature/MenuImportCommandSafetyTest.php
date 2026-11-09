<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class MenuImportCommandSafetyTest extends TestCase
{
    use RefreshDatabase;

    private array $temporarySqliteFiles = [];

    protected function tearDown(): void
    {
        parent::tearDown();

        // The test process never opens these databases. The child PHP processes
        // release their SQLite handles before we delete the temporary files.
        foreach ($this->temporarySqliteFiles as $file) {
            if (is_file($file) && ! unlink($file)) {
                $this->fail('Temporary SQLite test database could not be deleted: '.$file);
            }
        }
    }

    public function test_preview_rejects_another_csv_inside_the_menu_directory(): void
    {
        $this->artisan('menu:preview', ['--source' => 'database/menu/legacy-aliases.csv'])
            ->expectsOutputToContain('approved canonical menu CSV')
            ->assertExitCode(1);
    }

    public function test_preview_accepts_only_the_pinned_original_csv(): void
    {
        $this->artisan('menu:preview')
            ->expectsOutputToContain('Menu preview: no database changes were made.')
            ->assertExitCode(0);
    }

    public function test_apply_rejects_another_csv_even_when_digest_is_provided(): void
    {
        $this->artisan('menu:apply', [
            '--source' => 'database/menu/legacy-aliases.csv',
            '--digest' => str_repeat('a', 64),
            '--confirm-staging' => true,
        ])->expectsOutputToContain('approved canonical menu CSV')->assertExitCode(1);
    }

    public function test_preview_rejects_paths_that_escape_the_fixed_source(): void
    {
        $this->artisan('menu:preview', [
            '--source' => 'database/menu/../../storage/framework/testing/other.csv',
        ])->expectsOutputToContain('approved canonical menu CSV')->assertExitCode(1);
    }

    public function test_mysql_staging_is_rejected_before_any_connection_attempt(): void
    {
        $original = config('database.default');
        try {
            config(['database.default' => 'mysql']);
            config(['database.connections.mysql.database' => 'remote-staging']);

            $this->artisan('menu:apply', [
                '--confirm-staging' => true,
                '--digest' => str_repeat('a', 64),
            ])->expectsOutputToContain('local SQLite file')->assertExitCode(1);
        } finally {
            config(['database.default' => $original]);
        }
    }

    public function test_sqlite_memory_database_is_rejected_for_cli_apply(): void
    {
        $this->artisan('menu:apply', [
            '--confirm-staging' => true,
            '--digest' => str_repeat('a', 64),
        ])->expectsOutputToContain('local SQLite file')->assertExitCode(1);
    }

    public function test_remote_database_url_cannot_override_an_apparently_safe_sqlite_config(): void
    {
        $original = config('database.connections.sqlite.url');
        try {
            config(['database.connections.sqlite.url' => 'mysql://example.invalid/remote-staging']);
            $this->artisan('menu:apply', [
                '--confirm-staging' => true,
                '--digest' => str_repeat('a', 64),
            ])->expectsOutputToContain('local SQLite file')->assertExitCode(1);
        } finally {
            config(['database.connections.sqlite.url' => $original]);
        }
    }

    public function test_sqlite_paths_outside_the_project_or_without_test_filename_are_rejected(): void
    {
        $original = config('database.connections.sqlite.database');
        try {
            foreach ([
                'file:///remote/staging.sqlite',
                '//remote.example/share/staging.sqlite',
                storage_path('../outside-staging.sqlite'),
                database_path('production.sqlite'),
            ] as $path) {
                config(['database.connections.sqlite.database' => $path]);
                $this->artisan('menu:apply', [
                    '--confirm-staging' => true,
                    '--digest' => str_repeat('a', 64),
                ])->expectsOutputToContain('local SQLite file')->assertExitCode(1);
            }
        } finally {
            config(['database.connections.sqlite.database' => $original]);
        }
    }

    public function test_local_staging_sqlite_requires_the_digest_then_can_apply_the_approved_source(): void
    {
        $directory = storage_path('framework/testing');
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $file = $directory.DIRECTORY_SEPARATOR.'menu-cli-staging-test-'.bin2hex(random_bytes(6)).'.sqlite';
        touch($file);
        $this->temporarySqliteFiles[] = $file;
        $environment = [
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => $file,
            'DATABASE_URL' => false,
        ];

        $migrate = new Process([
            PHP_BINARY, base_path('artisan'), 'migrate', '--force', '--no-interaction',
        ], base_path(), $environment);
        $migrate->run();
        $this->assertSame(0, $migrate->getExitCode(), $migrate->getOutput().$migrate->getErrorOutput());

        $preview = new Process([PHP_BINARY, base_path('artisan'), 'menu:preview', '--json'], base_path(), $environment);
        $preview->run();
        $this->assertSame(0, $preview->getExitCode(), $preview->getOutput().$preview->getErrorOutput());
        $plan = json_decode($preview->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertGreaterThan(0, $plan['counts']['create']);

        $denied = new Process([
            PHP_BINARY, base_path('artisan'), 'menu:apply', '--confirm-staging',
            '--digest='.str_repeat('a', 64),
        ], base_path(), $environment);
        $denied->run();
        $this->assertSame(1, $denied->getExitCode());
        $this->assertStringContainsString('Preview digest was not approved', $denied->getOutput().$denied->getErrorOutput());

        $approved = new Process([
            PHP_BINARY, base_path('artisan'), 'menu:apply', '--confirm-staging', '--digest='.$plan['digest'],
        ], base_path(), $environment);
        $approved->run();
        $this->assertSame(0, $approved->getExitCode(), $approved->getOutput().$approved->getErrorOutput());

        $after = new Process([PHP_BINARY, base_path('artisan'), 'menu:preview', '--json'], base_path(), $environment);
        $after->run();
        $this->assertSame(0, $after->getExitCode(), $after->getOutput().$after->getErrorOutput());
        $afterPlan = json_decode($after->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(0, $afterPlan['counts']['create']);
    }
}
