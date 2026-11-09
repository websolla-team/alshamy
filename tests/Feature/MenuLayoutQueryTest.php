<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\MenuPreviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuLayoutQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_does_not_query_shared_shop_details_once_per_view_partial(): void
    {
        $this->seed(MenuPreviewSeeder::class);
        $category = Category::create(['title' => ['ar' => 'وجبات الشامي'], 'image' => '', 'order_num' => 1]);
        $product = Product::create([
            'name' => ['ar' => 'وجبة اختبار'],
            'description' => ['ar' => ''],
            'category_id' => $category->id,
            'image' => '',
        ]);
        $product->variants()->create(['size' => 'وجبة', 'quantity' => 0, 'price' => 100, 'old_price' => null, 'color_id' => null]);

        $queries = [];
        DB::listen(function ($query) use (&$queries): void {
            $sql = strtolower($query->sql);
            foreach (['contacts', 'pages', 'settings'] as $table) {
                if (str_contains($sql, $table)) {
                    $queries[] = $table;
                }
            }
        });

        $this->withoutMiddleware()->get(route('home'))->assertOk();
        $this->assertLessThanOrEqual(4, count($queries), 'Shared shop details should be loaded once per request.');
    }
}
