<?php

namespace App\Services\Menu;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Two-phase menu import. Preview is read-only; apply verifies an exact
 * state-dependent digest before changing a non-production database.
 */
final class MenuImporter
{
    private const CATEGORY_ALIASES = [
        'مشويات بالكيلو' => ['مشاوي بالكيلو'],
        'وجبات على الفحم' => ['مشويات'],
        'المناقيش والمعجنات' => ['معجنات'],
        'الطواجن' => ['طواجن'],
    ];

    private const SECTION_PRIORITY = [
        'وجبات الشامي',
        'شاورما الشامي',
        'سندوتشات الشامي',
        'دجاج الشامي',
        'بيتزا الشامي',
        'وجبات على الفحم',
        'مشويات بالكيلو',
        'مندي الشامي',
        'معجنات بالكيلو',
        'المناقيش والمعجنات',
        'الطواجن',
        'مقبلات',
        'مشروبات الشامي',
    ];

    public function preview(string $source): array
    {
        $catalog = MenuCatalog::fromCsv($source);
        $categories = Category::with('products.variants')->get();
        $linkedProducts = DB::table('menu_import_links')->pluck('product_id', 'menu_key');
        $existingAddons = DB::table('menu_addons')->get()->keyBy('menu_key');
        $aliases = $this->readLegacyAliases(base_path('database/menu/legacy-aliases.csv'));

        $actions = [];
        $reservedProductIds = [];
        $counts = [
            'source_items' => count($catalog->items()),
            'source_choices' => $catalog->choiceCount(),
            'review' => 0,
            'create' => 0,
            'link' => 0,
            'unchanged' => 0,
            'price_change' => 0,
            'addon_create' => 0,
            'addon_update' => 0,
            'addon_unchanged' => 0,
        ];

        foreach ($catalog->items() as $item) {
            $action = [
                'menu_key' => $item['menu_key'],
                'section' => $item['section'],
                'name' => $item['name'],
                'type' => $item['type'],
                'choices' => $item['choices'],
                'status' => 'review',
                'reason' => null,
                'product_id' => null,
                'updates' => [],
            ];

            if ($item['requires_review']) {
                $action['reason'] = 'Unconfirmed size, addon rule, or price in the photographed source';
                $actions[] = $action;
                $counts['review']++;

                continue;
            }

            if ($item['type'] === 'إضافة') {
                $existing = $existingAddons->get($item['menu_key']);
                if (! $existing) {
                    $action['status'] = 'addon_create';
                } elseif (json_decode($existing->choices, true) == $item['choices']
                    && $existing->name === $item['name'] && $existing->section === $item['section']) {
                    $action['status'] = 'addon_unchanged';
                } else {
                    $action['status'] = 'addon_update';
                    $action['updates'] = ['old' => json_decode($existing->choices, true), 'new' => $item['choices']];
                }
                $counts[$action['status']]++;
                $actions[] = $action;

                continue;
            }

            $matches = $categories->filter(fn (Category $category): bool => $this->sectionMatches($category->getTranslation('title', 'ar', false) ?? '', $item['section']));
            if ($matches->count() > 1) {
                $action['reason'] = 'Multiple categories match the source section';
                $actions[] = $action;
                $counts['review']++;

                continue;
            }
            $category = $matches->first();
            $existingProduct = null;
            $linkedId = $linkedProducts->get($item['menu_key']);

            if ($linkedId) {
                $existingProduct = Product::with('variants')->find($linkedId);
                if (! $existingProduct || ! $category || $existingProduct->category_id !== $category->id) {
                    $action['reason'] = 'Existing import link is missing or points to a different category';
                    $actions[] = $action;
                    $counts['review']++;

                    continue;
                }
            } elseif ($category) {
                $matchesByName = $category->products->filter(fn (Product $product): bool => MenuCatalog::normalize((string) $product->getTranslation('name', 'ar', false)) === MenuCatalog::normalize($item['name']));

                if ($matchesByName->count() === 1) {
                    $existingProduct = $matchesByName->first();
                } elseif ($matchesByName->count() > 1) {
                    $action['reason'] = 'Multiple existing products have the same name in this category';
                    $actions[] = $action;
                    $counts['review']++;

                    continue;
                } elseif (isset($aliases[$item['menu_key']])) {
                    $alias = $aliases[$item['menu_key']];
                    if ($this->sectionMatches($category->getTranslation('title', 'ar', false) ?? '', $alias['category'])) {
                        $aliasMatches = $category->products->filter(fn (Product $product): bool => MenuCatalog::normalize((string) $product->getTranslation('name', 'ar', false)) === MenuCatalog::normalize($alias['name']));
                        if ($aliasMatches->count() === 1) {
                            $existingProduct = $aliasMatches->first();
                        } elseif ($aliasMatches->count() > 1) {
                            $action['reason'] = 'Approved alias points to more than one existing product';
                            $actions[] = $action;
                            $counts['review']++;

                            continue;
                        }
                    }
                }
            }

            if (! $existingProduct) {
                // Preserve potential legacy duplicates for review. Never fuzzy-overwrite them.
                if ($category && $this->hasPossibleDuplicate($category, $item['name'])) {
                    $action['reason'] = 'Similar existing product; requires explicit legacy identity mapping';
                    $actions[] = $action;
                    $counts['review']++;

                    continue;
                }
                $action['status'] = 'create';
                $actions[] = $action;
                $counts['create']++;

                continue;
            }

            if ($linkedProducts->contains($existingProduct->id) && ! $linkedId) {
                $action['reason'] = 'Existing product already belongs to another imported menu key';
                $actions[] = $action;
                $counts['review']++;

                continue;
            }

            $action['product_id'] = $existingProduct->id;
            if (isset($reservedProductIds[$existingProduct->id])
                && $reservedProductIds[$existingProduct->id] !== $item['menu_key']) {
                $action['reason'] = 'A second menu key targets a product already reserved in this preview';
                $actions[] = $action;
                $counts['review']++;

                continue;
            }
            $reservedProductIds[$existingProduct->id] = $item['menu_key'];
            $current = $existingProduct->variants;
            $selected = [];
            $unmatched = [];

            foreach ($item['choices'] as $choice) {
                $size = MenuCatalog::normalize($choice['size']);
                $sameSize = $current->filter(fn ($v) => MenuCatalog::normalize((string) $v->size) === $size);
                if ($sameSize->count() > 1) {
                    $unmatched[] = $choice['size'];

                    continue;
                }
                $variant = $sameSize->first();
                if (! $variant && $current->count() === 1 && count($item['choices']) === 1) {
                    $variant = $current->first();
                }
                if (! $variant) {
                    $action['updates'][] = ['variant_id' => null, 'size' => $choice['size'], 'old' => null, 'new' => $choice['price']];
                } else {
                    $selected[] = $variant->id;
                    if ((float) $variant->price !== (float) $choice['price']
                        || (string) $variant->size !== (string) $choice['size']) {
                        $action['updates'][] = [
                            'variant_id' => $variant->id,
                            'size' => $choice['size'],
                            'old_size' => $variant->size,
                            'old' => (float) $variant->price,
                            'new' => $choice['price'],
                        ];
                    }
                }
            }

            if ($unmatched || $current->count() !== count($selected)) {
                $action['reason'] = 'Existing sizes differ from the new menu; preserve them pending review';
                $action['updates'] = [];
                $actions[] = $action;
                $counts['review']++;

                continue;
            }

            $action['status'] = $action['updates'] ? 'price_change' : ($linkedId ? 'unchanged' : 'link');
            $actions[] = $action;
            $counts[$action['status']]++;
        }

        $plan = [
            'source_items' => $catalog->items(),
            'source_file_sha256' => hash_file('sha256', $source),
            'counts' => $counts,
            'actions' => $actions,
        ];
        $plan['digest'] = hash('sha256', json_encode($plan, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $plan;
    }

    public function apply(array $plan, string $source, string $approvedDigest, bool $acceptPriceChanges): array
    {
        if (app()->environment('production')) {
            throw new LogicException('Production imports are blocked. Use a verified staging database.');
        }
        $connection = (string) config('database.default');
        $settings = config("database.connections.{$connection}");
        $driver = (string) ($settings['driver'] ?? '');
        $configuredDatabase = (string) ($settings['database'] ?? '');
        $database = strtolower($configuredDatabase);
        if (! app()->environment(['local', 'testing', 'staging'])
            || $driver !== 'sqlite'
            || ! empty($settings['url']) || ! empty($settings['read']) || ! empty($settings['write'])
            || ! (str_contains($database, 'staging') || str_contains($database, 'test') || $database === ':memory:')) {
            throw new LogicException('Importer writes are restricted to isolated local SQLite staging or test databases without remote connection overrides.');
        }
        $resolved = false;
        if ($database !== ':memory:') {
            $resolved = realpath($configuredDatabase);
            $databaseRoot = realpath(database_path());
            $storageRoot = realpath(storage_path());
            $insideProject = ($databaseRoot && str_starts_with(strtolower($resolved ?: ''), strtolower($databaseRoot.DIRECTORY_SEPARATOR)))
                || ($storageRoot && str_starts_with(strtolower($resolved ?: ''), strtolower($storageRoot.DIRECTORY_SEPARATOR)));
            if (! $resolved || ! $insideProject) {
                throw new LogicException('Staging SQLite must be inside the project database or storage directory.');
            }
        }
        $active = DB::connection($connection);
        if ($active->getDriverName() !== 'sqlite') {
            throw new LogicException('The active database connection is not SQLite.');
        }
        $main = collect($active->select('PRAGMA database_list'))
            ->first(fn ($attached) => $attached->name === 'main');
        $activeFile = (string) ($main->file ?? '');
        $matchingFile = $database === ':memory:'
            ? $activeFile === ''
            : $activeFile !== '' && realpath($activeFile) === $resolved;
        if (! $matchingFile) {
            throw new LogicException('The active SQLite database is different from the approved isolated target.');
        }
        if (! $approvedDigest || ! hash_equals((string) $plan['digest'], $approvedDigest)) {
            throw new LogicException('Preview digest was not approved.');
        }

        return DB::transaction(function () use ($source, $approvedDigest, $acceptPriceChanges): array {
            $currentPlan = $this->preview($source);
            if (! hash_equals($approvedDigest, $currentPlan['digest'])) {
                throw new LogicException('Menu or database changed since preview. Run preview again.');
            }
            if (! $acceptPriceChanges && ($currentPlan['counts']['price_change'] > 0 || $currentPlan['counts']['addon_update'] > 0)) {
                throw new LogicException('Existing prices differ. Approve price changes explicitly after checking the diff.');
            }

            $results = ['created' => 0, 'linked' => 0, 'updated' => 0, 'addons' => 0, 'skipped' => 0];
            $sections = [];
            foreach (Category::all() as $category) {
                $sections[MenuCatalog::normalize((string) $category->getTranslation('title', 'ar', false))] = $category;
                foreach (self::CATEGORY_ALIASES as $canonical => $aliases) {
                    if (in_array(MenuCatalog::normalize($category->getTranslation('title', 'ar', false)), array_map([MenuCatalog::class, 'normalize'], $aliases), true)) {
                        $sections[MenuCatalog::normalize($canonical)] = $category;
                    }
                }
            }

            $menuPosition = 0;
            foreach ($currentPlan['actions'] as $action) {
                if ($action['status'] === 'review') {
                    $results['skipped']++;

                    continue;
                }
                if (str_starts_with($action['status'], 'addon_')) {
                    if ($action['status'] !== 'addon_unchanged') {
                        DB::table('menu_addons')->upsert([[
                                'menu_key' => $action['menu_key'],
                                'name' => $action['name'], 'section' => $action['section'],
                                'choices' => json_encode($action['choices'], JSON_UNESCAPED_UNICODE),
                                'created_at' => now(), 'updated_at' => now(),
                            ]],
                            ['menu_key'],
                            ['name', 'section', 'choices', 'updated_at']
                        );
                        $results['addons']++;
                    }

                    continue;
                }

                $sectionKey = MenuCatalog::normalize($action['section']);
                if (! isset($sections[$sectionKey])) {
                    $priority = array_search($action['section'], self::SECTION_PRIORITY, true);
                    $sections[$sectionKey] = Category::create([
                        'title' => ['ar' => $action['section']],
                        'image' => '',
                        'order_num' => $priority === false ? 100 + ++$menuPosition : $priority + 1,
                    ]);
                }
                $category = $sections[$sectionKey];

                if ($action['status'] === 'create') {
                    $product = Product::create([
                        'category_id' => $category->id,
                        'name' => ['ar' => $action['name']],
                        'description' => ['ar' => ''],
                        'image' => '',
                        'is_featured' => 0,
                    ]);
                    foreach ($action['choices'] as $choice) {
                        $product->variants()->create([
                            'size' => $choice['size'],
                            'price' => $choice['price'],
                            'old_price' => null,
                            'quantity' => 0,
                            'color_id' => null,
                        ]);
                    }
                    $results['created']++;
                } else {
                    $product = Product::findOrFail($action['product_id']);
                    if ($action['status'] === 'price_change') {
                        foreach ($action['updates'] as $update) {
                            if ($update['variant_id']) {
                                $changed = $product->variants()
                                    ->whereKey($update['variant_id'])
                                    ->where('size', $update['old_size'])
                                    ->where('price', $update['old'])
                                    ->update(['price' => $update['new'], 'size' => $update['size']]);
                                if ($changed !== 1) {
                                    throw new LogicException('Variant price changed after preview; run preview again.');
                                }
                            } else {
                                $product->variants()->create([
                                    'size' => $update['size'],
                                    'price' => $update['new'],
                                    'old_price' => null,
                                    'quantity' => 0,
                                    'color_id' => null,
                                ]);
                            }
                        }
                        $results['updated']++;
                    }
                    if ($action['status'] === 'link') {
                        $results['linked']++;
                    }
                }

                // Re-applying an unchanged plan must not rewrite identity history.
                // An upsert keeps the original created_at for an existing link.
                if ($action['status'] !== 'unchanged') {
                    DB::table('menu_import_links')->upsert([[
                            'menu_key' => $action['menu_key'],
                            'product_id' => $product->id,
                            'created_at' => now(), 'updated_at' => now(),
                        ]],
                        ['menu_key'],
                        ['product_id', 'updated_at']
                    );
                }
            }

            return $results;
        }, 3);
    }

    private function sectionMatches(string $existing, string $expected): bool
    {
        $normalized = MenuCatalog::normalize($existing);
        if ($normalized === MenuCatalog::normalize($expected)) {
            return true;
        }
        foreach (self::CATEGORY_ALIASES[$expected] ?? [] as $alias) {
            if ($normalized === MenuCatalog::normalize($alias)) {
                return true;
            }
        }

        return false;
    }

    private function hasPossibleDuplicate(Category $category, string $name): bool
    {
        $candidate = MenuCatalog::normalize($name);
        foreach ($category->products as $product) {
            $existing = MenuCatalog::normalize((string) $product->getTranslation('name', 'ar', false));
            similar_text($existing, $candidate, $score);
            if ($score >= 75) {
                return true;
            }
        }

        return false;
    }

    private function readLegacyAliases(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }

        $aliases = [];
        $handle = fopen($path, 'rb');
        try {
            $headers = fgetcsv($handle);
            if ($headers !== ['menu_key', 'existing_category', 'existing_name']) {
                throw new LogicException('Invalid legacy alias file header.');
            }
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== 3 || isset($aliases[$row[0]])) {
                    throw new LogicException('Duplicate or malformed legacy alias.');
                }
                $aliases[$row[0]] = ['category' => $row[1], 'name' => $row[2]];
            }
        } finally {
            fclose($handle);
        }

        return $aliases;
    }
}
