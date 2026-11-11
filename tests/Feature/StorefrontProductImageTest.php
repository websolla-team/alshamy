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

    public function test_suggested_photo_is_used_only_when_no_dashboard_photo_exists(): void
    {
        $this->seed(MenuPreviewSeeder::class);
        $category = Category::create(['title' => ['ar' => 'مقبلات'], 'image' => '', 'order_num' => 1]);
        foreach (['', 'storage/images/products/custom.webp'] as $path) {
            $product = Product::create([
                'name' => ['ar' => 'تبولة'], 'description' => ['ar' => ''],
                'category_id' => $category->id, 'image' => $path,
            ]);
            $product->variants()->create(['size' => 'طبق', 'price' => 50, 'quantity' => 0]);
        }

        $html = app(HomeController::class)->index()->render();
        $this->assertStringContainsString('website/assets/img/menu-photos/IMG_2363.jpg', $html);
        $this->assertStringContainsString('storage/images/products/custom.webp', $html);
    }

    public function test_blank_and_root_only_images_render_no_thumbnail_without_requesting_the_site_root(): void
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
        $this->assertSame(0, substr_count($html, 'alshamy-menu-card__no-image'));
        $this->assertSame(1, substr_count($html, 'class="alshamy-row alshamy-row--with-image"'));
        $this->assertSame(1, substr_count($html, 'class="alshamy-row__thumb"'));
        $this->assertStringNotContainsString('src="http://localhost/"', $html);
        $this->assertStringContainsString('storage/images/products/genuine.webp', $html);
    }
}
