<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class AdminProductWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function signInAsAdmin(): void
    {
        $admin = User::forceCreate([
            'first_name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'is_admin' => 1,
        ]);
        $this->actingAs($admin);
    }

    private function makeCategory(): Category
    {
        return Category::create([
            'title' => ['ar' => 'وجبات', 'en' => 'Meals'],
            'image' => '/logo/logo.png',
            'order_num' => 1,
        ]);
    }

    private function productPayload(Category $category, array $variants): array
    {
        return [
            'name' => ['ar' => 'وجبة تجريبية', 'en' => 'Test meal'],
            'description' => ['ar' => '', 'en' => ''],
            'category_id' => $category->id,
            'variants' => $variants,
        ];
    }

    private function makeProduct(Category $category): Product
    {
        return Product::create([
            'name' => ['ar' => 'وجبة', 'en' => 'Meal'],
            'description' => ['ar' => '', 'en' => ''],
            'image' => '/logo/logo.png',
            'category_id' => $category->id,
        ]);
    }

    public function test_schema_supports_sizes_ordering_and_variants_without_colors(): void
    {
        $this->assertTrue(Schema::hasTable('sizes'));
        $this->assertTrue(Schema::hasColumn('categories', 'order_num'));
        $product = $this->makeProduct($this->makeCategory());

        $variant = $product->variants()->create([
            'size' => 'فردي',
            'quantity' => 0,
            'price' => 95,
            'old_price' => null,
            'color_id' => null,
        ]);

        $this->assertNull($variant->fresh()->color_id);
    }

    public function test_admin_can_create_product_with_one_size_and_no_old_price_or_photo(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();

        $response = $this->post(route('products.store'), $this->productPayload($category, [
            ['size' => 'فردي', 'quantity' => 0, 'price' => 95],
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('products.index'));
        $product = Product::query()->firstOrFail();
        $this->assertSame('/logo/logo.png', $product->image);
        $this->assertCount(1, $product->variants);
        $this->assertNull($product->variants[0]->color_id);
        $this->assertNull($product->variants[0]->old_price);
        $this->assertSame(95.0, (float) $product->variants[0]->price);
    }

    public function test_admin_cannot_save_invalid_price_quantity_or_duplicate_sizes(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();

        $response = $this->post(route('products.store'), $this->productPayload($category, [
            ['size' => 'صغير', 'quantity' => -1, 'price' => 0],
            ['size' => 'صغير', 'quantity' => 1, 'price' => 80],
        ]));

        $response->assertSessionHasErrors(['variants.0.quantity', 'variants.0.price', 'variants.1.size']);
        $this->assertSame(0, Product::count());
    }

    public function test_old_price_must_be_greater_than_current_price_when_set(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();

        $this->post(route('products.store'), $this->productPayload($category, [
            ['size' => 'صغير', 'quantity' => 1, 'price' => 95, 'old_price' => 90],
        ]))->assertSessionHasErrors('variants.0.old_price');

        $this->assertSame(0, Product::count());
    }

    public function test_admin_updates_existing_variant_without_deleting_omitted_variant(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();
        $product = $this->makeProduct($category);
        $color = Color::create(['name' => ['ar' => 'عادي', 'en' => 'Regular'], 'code' => '#888888']);
        $first = $product->variants()->create([
            'size' => 'صغير', 'quantity' => 4, 'price' => 80,
            'old_price' => 100, 'color_id' => $color->id,
        ]);
        $second = $product->variants()->create([
            'size' => 'كبير', 'quantity' => 3, 'price' => 120,
            'color_id' => $color->id,
        ]);

        $response = $this->put(route('products.update', $product), $this->productPayload($category, [
            ['id' => $first->id, 'size' => 'صغير', 'quantity' => 2, 'price' => 85, 'old_price' => ''],
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('products.index'));
        $this->assertSame(2, $product->variants()->count());
        $this->assertSame(85.0, (float) $first->fresh()->price);
        $this->assertSame($color->id, $first->fresh()->color_id);
        $this->assertNull($first->fresh()->old_price);
        $this->assertSame(120.0, (float) $second->fresh()->price);
    }

    public function test_admin_cannot_update_variant_owned_by_another_product(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();
        $mine = $this->makeProduct($category);
        $other = $this->makeProduct($category);
        $foreignVariant = $other->variants()->create([
            'size' => 'كبير', 'quantity' => 2, 'price' => 130, 'color_id' => null,
        ]);

        $response = $this->put(route('products.update', $mine), $this->productPayload($category, [
            ['id' => $foreignVariant->id, 'size' => 'كبير', 'quantity' => 1, 'price' => 1],
        ]));

        $response->assertSessionHasErrors('variants.0.id');
        $this->assertSame(130.0, (float) $foreignVariant->fresh()->price);
        $this->assertSame(0, $mine->variants()->count());
    }

    public function test_admin_can_add_new_size_while_preserving_existing_variant_id(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();
        $product = $this->makeProduct($category);
        $first = $product->variants()->create([
            'size' => 'صغير', 'quantity' => 1, 'price' => 70, 'color_id' => null,
        ]);

        $response = $this->put(route('products.update', $product), $this->productPayload($category, [
            ['id' => $first->id, 'size' => 'صغير', 'quantity' => 1, 'price' => 70],
            ['size' => 'كبير', 'quantity' => 2, 'price' => 100],
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('products.index'));
        $this->assertSame(2, $product->variants()->count());
        $this->assertNotNull($first->fresh());
        $this->assertSame(100.0, (float) $product->variants()->where('size', 'كبير')->firstOrFail()->price);
    }

    public function test_admin_product_index_create_and_edit_templates_render(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();
        $product = $this->makeProduct($category);
        $variant = $product->variants()->create([
            'size' => 'وسط', 'quantity' => 5, 'price' => 95, 'color_id' => null,
        ]);

        $this->view('admin.products.index', [
            'products' => Product::with(['category', 'variants'])->get(),
        ])->assertSee('الأحجام والأسعار');
        $this->view('admin.products.create', [
            'categories' => Category::all(), 'sizes' => collect(),
            'errors' => new ViewErrorBag,
        ])->assertSee('حفظ الصنف');
        $this->view('admin.products.edit', [
            'product' => $product->load('variants'),
            'categories' => Category::all(),
            'sizes' => collect(),
            'errors' => new ViewErrorBag,
        ])
            ->assertSee('variants[0][id]', false)
            ->assertSee((string) $variant->id);
    }

    public function test_admin_list_renders_a_placeholder_instead_of_broken_image_for_imported_product(): void
    {
        $this->signInAsAdmin();
        $product = $this->makeProduct($this->makeCategory());
        $product->update(['image' => '']);

        $this->view('admin.products.index', [
            'products' => Product::with(['category', 'variants'])->get(),
        ])->assertSee('ap-photo-fallback', false)
            ->assertDontSee('<img src="http://localhost/"', false);
    }

    public function test_admin_list_does_not_request_the_site_root_for_missing_photo_paths(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();

        foreach (['', ' ', '/'] as $missingPath) {
            $this->makeProduct($category)->update(['image' => $missingPath]);
        }
        $this->makeProduct($category)->update(['image' => '/logo/logo.png']);

        $html = view('admin.products.index', [
            'products' => Product::with(['category', 'variants'])->get(),
        ])->render();

        $this->assertSame(3, substr_count($html, 'class="ap-photo-fallback"'));
        $this->assertStringNotContainsString('<img src="http://localhost/"', $html);
        $this->assertStringContainsString('<img src="http://localhost/logo/logo.png"', $html);
    }

    public function test_admin_layout_guards_optional_scrollbars_and_keeps_desktop_sidebar_script(): void
    {
        $this->signInAsAdmin();

        $html = view('admin.products.index', [
            'products' => collect(),
        ])->render();

        $this->assertStringNotContainsString('assets/plugins/perfect-scrollbar/p-scroll.js', $html);
        $this->assertStringContainsString('document.querySelector(selector)', $html);
        $this->assertStringContainsString('if (element)', $html);
        $this->assertStringContainsString('new PerfectScrollbar(element', $html);
        $this->assertStringContainsString('assets/plugins/side-menu/sidemenu.js', $html);
    }

    public function test_cannot_delete_category_that_still_contains_products(): void
    {
        $this->signInAsAdmin();
        $category = $this->makeCategory();
        $product = $this->makeProduct($category);

        $this->delete(route('categories.destroy', $category), ['id' => $category->id])
            ->assertSessionHasErrors('category');

        $this->assertNotNull($category->fresh());
        $this->assertNotNull($product->fresh());
    }

    public function test_admin_cannot_delete_product_linked_to_menu_import(): void
    {
        $this->signInAsAdmin();
        $product = $this->makeProduct($this->makeCategory());
        DB::table('menu_import_links')->insert([
            'menu_key' => 'ALSHAMY-20261109-001',
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::fake('public');
        Storage::disk('public')->put('images/products/keep.webp', 'original');
        $product->update(['image' => '/storage/images/products/keep.webp']);

        $this->from(route('products.index'))
            ->delete(route('products.destroy', $product), ['id' => $product->id])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors('product');

        $this->assertNotNull($product->fresh());
        $this->assertDatabaseHas('menu_import_links', [
            'menu_key' => 'ALSHAMY-20261109-001',
            'product_id' => $product->id,
        ]);
        Storage::disk('public')->assertExists('images/products/keep.webp');
    }

    public function test_imported_zero_quantity_does_not_claim_stock_is_sold_out(): void
    {
        $this->signInAsAdmin();
        $product = $this->makeProduct($this->makeCategory());
        $product->variants()->create(['size' => 'فردي', 'quantity' => 0, 'price' => 95]);
        DB::table('menu_import_links')->insert([
            'menu_key' => 'ALSHAMY-20261109-TEST-UNKNOWN-STOCK',
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $html = app(\App\Http\Controllers\Admin\ProductController::class)->index()->render();
        $this->assertStringContainsString('data-stock="unknown"', $html);
        $this->assertStringContainsString('التوفر غير مؤكد', $html);
        $this->assertStringNotContainsString('نفد المخزون', $html);
    }

    public function test_admin_cannot_delete_product_referenced_by_an_order_detail(): void
    {
        $this->signInAsAdmin();
        $product = $this->makeProduct($this->makeCategory());
        $orderId = DB::table('orders')->insertGetId([
            'code' => 912345,
            'user_name' => 'عميل',
            'phone' => '01012345678',
            'delivery' => 'home',
            'city' => 'مدينة السادات',
            'address' => 'شارع الاختبار',
            'total' => 95,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $detailId = DB::table('order_details')->insertGetId([
            'order_id' => $orderId,
            'product_id' => $product->id,
            'name' => 'وجبة',
            'color' => 'عادي',
            'size' => 'فردي',
            'quantity' => 1,
            'price' => 95,
            'total_price' => 95,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->from(route('products.index'))
            ->delete(route('products.destroy', $product), ['id' => $product->id])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors('product');

        $this->assertNotNull($product->fresh());
        $this->assertDatabaseHas('order_details', [
            'id' => $detailId,
            'order_id' => $orderId,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseHas('orders', ['id' => $orderId]);
    }

    public function test_admin_can_still_delete_unreferenced_product(): void
    {
        $this->signInAsAdmin();
        $product = $this->makeProduct($this->makeCategory());

        $this->from(route('products.index'))
            ->delete(route('products.destroy', $product), ['id' => $product->id])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasNoErrors();

        $this->assertNull($product->fresh());
    }
}
