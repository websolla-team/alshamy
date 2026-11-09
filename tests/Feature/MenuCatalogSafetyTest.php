<?php

namespace Tests\Feature;

use App\Services\Menu\MenuCatalog;
use InvalidArgumentException;
use Tests\TestCase;

class MenuCatalogSafetyTest extends TestCase
{
    private function parseOne(string $price, string $unparsedPrice = '', string $review = 'no'): MenuCatalog
    {
        $csv = tempnam(sys_get_temp_dir(), 'menu-catalog-');
        try {
            file_put_contents($csv, implode("\n", [
                'menu_key,page,section,name_ar,type,variant,price_egp,unparsed_price,notes,requires_review',
                implode(',', ['ALSHAMY-20261109-999', '1', 'مقبلات', 'صنف تجريبي', 'صنف', 'طبق', $price, $unparsedPrice, '', $review]),
            ]));

            return MenuCatalog::fromCsv($csv);
        } finally {
            unlink($csv);
        }
    }

    public function test_rejects_non_canonical_or_unrepresentable_egyptian_pound_prices(): void
    {
        foreach (['1e309', '1e2', '123.456', '100000000'] as $price) {
            try {
                $this->parseOne($price);
                $this->fail("Invalid price {$price} must not be silently imported or rounded.");
            } catch (InvalidArgumentException $exception) {
                $this->assertStringContainsString('Invalid price', $exception->getMessage());
            }
        }
    }

    public function test_unresolved_printed_price_cannot_be_approved_as_confirmed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unparsed price must be flagged');

        $this->parseOne('120', '120-150', 'no');
    }

    public function test_valid_two_decimal_price_and_review_flag_remain_supported(): void
    {
        $confirmed = $this->parseOne('123.45');
        $this->assertSame(123.45, $confirmed->items()[0]['choices'][0]['price']);
        $this->assertCount(1, $confirmed->confirmedProducts());

        $unresolved = $this->parseOne('120', '120-150', 'yes');
        $this->assertCount(1, $unresolved->reviewItems());
        $this->assertCount(0, $unresolved->confirmedProducts());
    }
}
