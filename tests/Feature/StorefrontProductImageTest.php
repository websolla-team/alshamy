<?php

namespace Tests\Feature;

use App\Http\Controllers\Front\HomeController;
use App\Models\Category;
use App\Models\Product;
use Database\Seeders\MenuPreviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_blank_and_root_only_images_use_a_placeholder_without_requesting_the_site_root(): void
    {
        $this->seed(MenuPreviewSeeder::class);

        $category = Category::create([
            'title' => ['ar' => 'وجبات الشامي'],
            'image' => '',
            'order_num' => 1,
        ]);

        foreach (['', '  ', '/'] as $path) {
            $product = Product::create([
                'name' => ['ar' => 'وجبة اختبار'],
                'description' => ['ar' => ''],
                'category_id' => $category->id,
                'image' => $path,
            ]);
            $product->variants()->create(['size' => 'وجبة', 'price' => 100, 'quantity' => 0]);
        }

        $product = Product::create([
            'name' => ['ar' => 'وجبة بصورة'],
            'description' => ['ar' => ''],
            'category_id' => $category->id,
            'image' => 'storage/images/products/genuine.webp',
        ]);
        $product->variants()->create(['size' => 'وجبة', 'price' => 100, 'quantity' => 0]);

        $html = app(HomeController::class)->index()->render();
        $this->assertSame(3, substr_count($html, 'class="alshamy-menu-card__no-image"'));
        $this->assertStringNotContainsString('src="http://localhost/"', $html);
        $this->assertStringContainsString('storage/images/products/genuine.webp', $html);
    }
}
