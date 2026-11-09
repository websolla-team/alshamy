<?php

namespace Tests\Unit;

use App\Services\Menu\MenuCatalog;
use PHPUnit\Framework\TestCase;

class MenuCatalogTest extends TestCase
{
    public function test_source_has_131_unique_items_and_183_choices(): void
    {
        $catalog = MenuCatalog::fromCsv(dirname(__DIR__, 2).'/database/menu/alshamy-2026-11-09.csv');

        $this->assertCount(131, $catalog->items());
        $this->assertSame(183, $catalog->choiceCount());
        $this->assertCount(13, $catalog->sections());
        $this->assertCount(5, $catalog->reviewItems());
    }

    public function test_unconfirmed_prices_and_addons_do_not_become_regular_products(): void
    {
        $catalog = MenuCatalog::fromCsv(dirname(__DIR__, 2).'/database/menu/alshamy-2026-11-09.csv');

        $this->assertCount(125, $catalog->confirmedProducts());
        $this->assertCount(1, $catalog->confirmedAddons());
        $this->assertSame('محشية الأطراف', $catalog->confirmedAddons()[0]['name']);
        $this->assertFalse(in_array('كول سلو', array_column($catalog->confirmedProducts(), 'name'), true));
        $this->assertContains('برجر لحم', array_column($catalog->confirmedProducts(), 'name'));
        $this->assertContains('وجبة مندي لحم ضاني', array_column($catalog->confirmedProducts(), 'name'));
    }

    public function test_import_rejects_duplicate_size_with_conflicting_price(): void
    {
        $csv = tempnam(sys_get_temp_dir(), 'menu');
        file_put_contents($csv, implode("\n", [
            'menu_key,page,section,name_ar,type,variant,price_egp,unparsed_price,notes,requires_review',
            'ITEM-1,1,بيتزا,بيتزا اختبار,صنف,وسط,100,,,no',
            'ITEM-1,1,بيتزا,بيتزا اختبار,صنف,وسط,120,,,no',
        ]));

        try {
            $this->expectException(\InvalidArgumentException::class);
            MenuCatalog::fromCsv($csv);
        } finally {
            unlink($csv);
        }
    }
}
