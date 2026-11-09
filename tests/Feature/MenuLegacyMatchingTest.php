<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\Menu\MenuImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuLegacyMatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_menu_keys_cannot_be_linked_to_the_same_legacy_product(): void
    {
        $category = Category::create(['title' => ['ar' => 'بيتزا الشامي'], 'image' => '', 'order_num' => 1]);
        $product = Product::create([
            'name' => ['ar' => 'بيتزا مارجريتا'],
            'description' => ['ar' => ''],
            'image' => '',
            'category_id' => $category->id,
        ]);
        $product->variants()->create(['size' => 'وسط', 'quantity' => 0, 'price' => 118, 'color_id' => null]);

        $source = tempnam(sys_get_temp_dir(), 'menu');
        file_put_contents($source, implode("\n", [
            'menu_key,page,section,name_ar,type,variant,price_egp,unparsed_price,notes,requires_review',
            'ALSHAMY-20261109-019,1,بيتزا الشامي,بيتزا مارجريتا,صنف,وسط,118,,,no',
            'ALSHAMY-20261109-777,1,بيتزا الشامي,بيتزا مارجريتا,صنف,وسط,118,,,no',
        ]));

        try {
            $importer = app(MenuImporter::class);
            $plan = $importer->preview($source);
            $this->assertSame(1, $plan['counts']['link']);
            $this->assertSame(1, $plan['counts']['review']);
            $importer->apply($plan, $source, $plan['digest'], false);
            $this->assertSame(1, \Illuminate\Support\Facades\DB::table('menu_import_links')->where('product_id', $product->id)->count());
        } finally {
            unlink($source);
        }
    }

    public function test_tawajin_category_alias_keeps_existing_kabab_hindi_product_for_review(): void
    {
        $category = Category::create(['title' => ['ar' => 'طواجن'], 'image' => '', 'order_num' => 1]);
        $product = Product::create([
            'name' => ['ar' => 'وجبه طاجن كباب هندي لحم'],
            'description' => ['ar' => ''],
            'image' => '',
            'category_id' => $category->id,
        ]);
        $product->variants()->create(['size' => 'لارج', 'quantity' => 0, 'price' => 140, 'color_id' => null]);

        $plan = app(MenuImporter::class)->preview(base_path('database/menu/alshamy-2026-11-09.csv'));
        $action = collect($plan['actions'])->firstWhere('menu_key', 'ALSHAMY-20261109-034');
        $this->assertSame($product->id, $action['product_id']);
        $this->assertSame('review', $action['status'], 'New size choices must be reviewed, not duplicate the old product.');
    }

    public function test_existing_public_catalog_is_compared_without_replacing_prices_or_creating_duplicates(): void
    {
        $this->seedPublicMenuSnapshot();
        $this->assertSame(97, Product::count());

        $before = Product::with('variants')->get()->mapWithKeys(
            fn (Product $product) => [$product->id => $product->variants->pluck('price', 'size')->all()]
        )->all();
        $plan = app(MenuImporter::class)->preview(base_path('database/menu/alshamy-2026-11-09.csv'));

        $this->assertSame(97, Product::count());
        $this->assertSame(131, $plan['counts']['source_items']);
        $this->assertGreaterThan(40, $plan['counts']['price_change'] + $plan['counts']['link'] + $plan['counts']['unchanged']);
        $this->assertGreaterThan(7, $plan['counts']['review']);

        $changed = collect($plan['actions'])->firstWhere('menu_key', 'ALSHAMY-20261109-122');
        $this->assertSame('price_change', $changed['status']);
        $this->assertSame(185.0, $changed['updates'][0]['old']);
        $this->assertSame(246.0, $changed['updates'][0]['new']);

        $after = Product::with('variants')->get()->mapWithKeys(
            fn (Product $product) => [$product->id => $product->variants->pluck('price', 'size')->all()]
        )->all();
        $this->assertSame($before, $after);
    }

    public function test_reviewed_legacy_merge_preserves_existing_ids_and_can_be_repeated(): void
    {
        $this->seedPublicMenuSnapshot();
        $beforeIds = Product::pluck('id')->all();
        $importer = app(MenuImporter::class);
        $source = base_path('database/menu/alshamy-2026-11-09.csv');
        $plan = $importer->preview($source);

        $result = $importer->apply($plan, $source, $plan['digest'], true);
        $this->assertSame(97 + $plan['counts']['create'], Product::count());
        $this->assertSame($plan['counts']['create'], $result['created']);
        $this->assertSame($plan['counts']['price_change'], $result['updated']);
        $this->assertSame([], array_diff($beforeIds, Product::pluck('id')->all()));

        $second = $importer->preview($source);
        $this->assertSame(0, $second['counts']['create']);
        $this->assertSame(0, $second['counts']['price_change']);
        $importer->apply($second, $source, $second['digest'], false);
        $this->assertSame(97 + $plan['counts']['create'], Product::count());
    }

    private function seedPublicMenuSnapshot(): void
    {
        $categories = json_decode(file_get_contents(base_path('database/menu/live-menu-2026-11-09.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach ($categories as $index => $section) {
            $category = Category::create([
                'title' => ['ar' => $section['category']],
                'image' => '',
                'order_num' => $index + 1,
            ]);
            foreach ($section['items'] as $item) {
                $product = Product::create([
                    'name' => ['ar' => $item['name']],
                    'description' => ['ar' => ''],
                    'image' => '',
                    'category_id' => $category->id,
                    'is_featured' => 0,
                ]);
                foreach ($item['variants'] as $choice) {
                    $product->variants()->create([
                        'size' => $choice['size'],
                        'price' => (float) $choice['price'],
                        'old_price' => (float) $choice['oldPrice'] > 0 ? $choice['oldPrice'] : null,
                        'color_id' => null,
                        'quantity' => 0,
                    ]);
                }
            }
        }
    }
}
