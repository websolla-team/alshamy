<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\Menu\MenuCatalog;
use App\Services\Menu\MenuImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuImportTest extends TestCase
{
    use RefreshDatabase;

    private function source(): string
    {
        return base_path('database/menu/alshamy-2026-11-09.csv');
    }

    public function test_canonical_menu_accounts_for_all_confirmed_choices_and_ambiguities(): void
    {
        $catalog = MenuCatalog::fromCsv($this->source());
        $confirmed = $catalog->confirmedProducts();
        $addons = $catalog->confirmedAddons();
        $review = $catalog->reviewItems();

        $this->assertCount(131, $catalog->items());
        $this->assertSame(183, $catalog->choiceCount());
        $this->assertCount(13, $catalog->sections());
        $this->assertCount(125, $confirmed);
        $this->assertSame(173, array_sum(array_map(fn ($item) => count($item['choices']), $confirmed)));
        $this->assertCount(1, $addons);
        $this->assertCount(2, $addons[0]['choices']);
        $this->assertCount(5, $review);
        $this->assertSame(8, array_sum(array_map(fn ($item) => count($item['choices']), $review)));
        $this->assertSame([
            'ALSHAMY-20261109-008',
            'ALSHAMY-20261109-009',
            'ALSHAMY-20261109-092',
            'ALSHAMY-20261109-093',
            'ALSHAMY-20261109-094',
        ], array_column($review, 'menu_key'));
    }

    public function test_preview_is_read_only_and_flags_ambiguous_entries(): void
    {
        $plan = app(MenuImporter::class)->preview($this->source());
        $this->assertSame(131, $plan['counts']['source_items']);
        $this->assertSame(183, $plan['counts']['source_choices']);
        $this->assertSame(5, $plan['counts']['review']);
        $this->assertSame(125, $plan['counts']['create']);
        $this->assertSame(1, $plan['counts']['addon_create']);
        $this->assertSame(0, Product::count());
        $this->assertSame(0, Category::count());
    }

    public function test_apply_imports_only_confirmed_products_addon_and_is_idempotent(): void
    {
        $importer = app(MenuImporter::class);
        $first = $importer->preview($this->source());
        $importer->apply($first, $this->source(), $first['digest'], false);

        $this->assertSame(125, Product::count());
        $this->assertSame(125, DB::table('menu_import_links')->count());
        $this->assertSame(173, DB::table('variants')->count());
        $this->assertSame(173, DB::table('variants')->where('quantity', 0)->count());
        $this->assertSame(1, DB::table('menu_addons')->count());
        $this->assertSame(13, Category::count());
        $this->assertSame('وجبات الشامي', Category::orderBy('order_num')->firstOrFail()->getTranslation('title', 'ar'));
        $this->assertSame(5, app(MenuImporter::class)->preview($this->source())['counts']['review']);
        $this->assertSame(0, app(MenuImporter::class)->preview($this->source())['counts']['create']);

        $second = app(MenuImporter::class)->preview($this->source());
        app(MenuImporter::class)->apply($second, $this->source(), $second['digest'], false);
        $this->assertSame(125, Product::count());
        $this->assertSame(173, DB::table('variants')->count());
        $this->assertSame(1, DB::table('menu_addons')->count());
    }

    public function test_price_change_needs_explicit_approval_and_stale_digest_is_rejected(): void
    {
        $importer = app(MenuImporter::class);
        $first = $importer->preview($this->source());
        $importer->apply($first, $this->source(), $first['digest'], false);
        $pizza = Product::whereHas('category', fn ($q) => $q->where('title->ar', 'بيتزا الشامي'))
            ->where('name->ar', 'بيتزا مارجريتا')->firstOrFail();
        $variant = $pizza->variants()->where('size', 'وسط')->firstOrFail();
        $variant->update(['price' => 90]);

        $newPlan = app(MenuImporter::class)->preview($this->source());
        $this->assertGreaterThan(0, $newPlan['counts']['price_change']);
        $this->expectException(\LogicException::class);
        app(MenuImporter::class)->apply($newPlan, $this->source(), $newPlan['digest'], false);
    }

    public function test_stale_preview_cannot_overwrite_recent_price_change(): void
    {
        $importer = app(MenuImporter::class);
        $first = $importer->preview($this->source());
        $importer->apply($first, $this->source(), $first['digest'], false);
        $plan = app(MenuImporter::class)->preview($this->source());
        $product = Product::where('name->ar', 'بيتزا مارجريتا')->firstOrFail();
        $product->variants()->where('size', 'وسط')->firstOrFail()->update(['price' => 99]);

        $this->expectException(\LogicException::class);
        app(MenuImporter::class)->apply($plan, $this->source(), $plan['digest'], true);
    }

    public function test_rolling_back_import_support_does_not_erase_existing_links_and_addons(): void
    {
        $importer = app(MenuImporter::class);
        $plan = $importer->preview($this->source());
        $importer->apply($plan, $this->source(), $plan['digest'], false);
        $migration = require database_path('migrations/2026_11_09_210000_create_menu_import_tables.php');

        $migration->up();
        $this->assertSame(125, DB::table('menu_import_links')->count());
        $this->assertSame(1, DB::table('menu_addons')->count());

        $migration->down();
        $this->assertSame(125, DB::table('menu_import_links')->count());
        $this->assertSame(1, DB::table('menu_addons')->count());

        $migration->up();
        $this->assertSame(125, DB::table('menu_import_links')->count());
    }
}
