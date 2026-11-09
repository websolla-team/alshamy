<?php

namespace Tests\Feature;

use App\Http\Controllers\Front\HomeController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use Database\Seeders\MenuPreviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StorefrontPizzaAddonTest extends TestCase
{
    use RefreshDatabase;

    public function test_stuffed_crust_is_shown_as_a_pizza_addon_not_an_independent_meal(): void
    {
        $this->seed(MenuPreviewSeeder::class);

        $pizzaCategory = Category::create([
            'title' => ['ar' => 'بيتزا الشامي'],
            'image' => '',
            'order_num' => 1,
        ]);
        $pizza = Product::create([
            'category_id' => $pizzaCategory->id,
            'name' => ['ar' => 'بيتزا مارجريتا'],
            'description' => ['ar' => ''],
            'image' => '',
            'is_featured' => 0,
        ]);
        Variant::create([
            'product_id' => $pizza->id,
            'size' => 'وسط',
            'price' => 118,
            'old_price' => null,
            'quantity' => 0,
        ]);

        DB::table('menu_addons')->insert([
            'menu_key' => 'ALSHAMY-20261109-029',
            'name' => 'محشية الأطراف',
            'section' => 'بيتزا الشامي',
            'choices' => json_encode([
                ['size' => 'وسط', 'price' => 39],
                ['size' => 'كبير', 'price' => 50],
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app()->setLocale('ar');
        $html = app(HomeController::class)->index()->render();

        $this->assertStringContainsString('محشية الأطراف', $html);
        $this->assertStringContainsString('إضافة اختيارية للبيتزا', $html);
        $this->assertStringContainsString('وسط +39 جنيه', $html);
        $this->assertStringContainsString('كبير +50 جنيه', $html);
        $this->assertSame(1, Product::count());
    }
}
