<?php

namespace Tests\Feature;

use Database\Seeders\MenuPreviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Tests\TestCase;

class StorefrontPageAssetsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MenuPreviewSeeder::class);
    }

    public function test_menu_keeps_bootstrap_tabs_without_loading_unused_shop_plugins(): void
    {
        $html = $this->renderScriptsFor('home');

        $this->assertStringContainsString('bootstrap.bundle.min.js', $html);
        $this->assertStringNotContainsString('jquery-3.7.1.min.js', $html);
        $this->assertStringNotContainsString('swiper-bundle.min.js', $html);
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/sweetalert2', $html);
    }

    public function test_other_pages_keep_existing_plugin_scripts(): void
    {
        $html = $this->renderScriptsFor('contacts');

        $this->assertStringContainsString('jquery-3.7.1.min.js', $html);
        $this->assertStringContainsString('main.js', $html);
    }

    public function test_home_does_not_load_unused_storefront_plugin_stylesheets(): void
    {
        $html = $this->renderHeadFor('home');

        $this->assertStringContainsString('bootstrap.min.css', $html);
        $this->assertStringContainsString('alshamy-menu.css', $html);
        foreach (['animate.css', 'magnific-popup.css', 'meanmenu.css', 'swiper-bundle.min.css', 'nice-select.css'] as $unused) {
            $this->assertStringNotContainsString($unused, $html);
        }
    }

    public function test_legacy_pages_keep_their_existing_plugin_stylesheets(): void
    {
        $html = $this->renderHeadFor('contacts');

        foreach (['animate.css', 'magnific-popup.css', 'meanmenu.css', 'swiper-bundle.min.css', 'nice-select.css'] as $existing) {
            $this->assertStringContainsString($existing, $html);
        }
    }

    private function renderHeadFor(string $routeName): string
    {
        $route = (new Route('GET', '/', fn () => null))->name($routeName);
        request()->setRouteResolver(fn () => $route);

        return view('website.layouts.head')->render();
    }

    private function renderScriptsFor(string $routeName): string
    {
        $route = (new Route('GET', '/', fn () => null))->name($routeName);
        request()->setRouteResolver(fn () => $route);

        return view('website.layouts.footer-scripts')->render();
    }
}
