<?php

namespace Tests\Feature;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;

class SecurityDependencyCompatibilityTest extends TestCase
{
    public function test_http_client_includes_fix_for_noncanonical_host_checks(): void
    {
        $this->assertTrue(
            version_compare(InstalledVersions::getVersion('guzzlehttp/guzzle'), '7.15.2', '>='),
            'Guzzle must include the fix for CVE-2026-69246.',
        );
    }

    public function test_markdown_parser_includes_fix_for_raw_html_and_table_denial_of_service(): void
    {
        $this->assertTrue(
            version_compare(InstalledVersions::getVersion('league/commonmark'), '2.10.2', '>='),
            'CommonMark must include the September 2026 HTML and table-parsing security fixes.',
        );
    }
}
