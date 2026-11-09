<?php

namespace App\Services\Menu;

use InvalidArgumentException;

/**
 * Validates the photographed menu transcription without writing to the database.
 */
final class MenuCatalog
{
    private function __construct(private readonly array $items, private readonly array $sections, private readonly int $choiceCount) {}

    public static function fromCsv(string $path): self
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException('Menu source is missing or unreadable.');
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new InvalidArgumentException('Could not open menu source.');
        }

        try {
            $headers = fgetcsv($handle);
            if (! is_array($headers)) {
                throw new InvalidArgumentException('Menu file has no header.');
            }

            $headers[0] = ltrim($headers[0], "\xEF\xBB\xBF");
            $required = ['menu_key', 'page', 'section', 'name_ar', 'type', 'variant', 'price_egp', 'unparsed_price', 'notes', 'requires_review'];
            if ($headers !== $required) {
                throw new InvalidArgumentException('Unexpected menu CSV columns.');
            }

            $items = [];
            $sections = [];
            $choiceCount = 0;
            $line = 1;
            while (($values = fgetcsv($handle)) !== false) {
                $line++;
                if ($values === [null]) {
                    continue;
                }
                if (count($values) !== count($headers)) {
                    throw new InvalidArgumentException("Invalid menu row {$line}.");
                }
                $row = array_combine($headers, $values);
                $key = trim($row['menu_key']);
                $section = trim($row['section']);
                $name = trim($row['name_ar']);
                $type = trim($row['type']);
                $variant = trim($row['variant']);
                $price = trim($row['price_egp']);
                $flag = trim($row['requires_review']);

                if (! preg_match('/^ALSHAMY-\d{8}-\d{3}$/', $key)
                    || $section === '' || $name === '' || $variant === ''
                    || ! in_array($type, ['صنف', 'إضافة'], true)
                    || ! in_array($flag, ['yes', 'no'], true)) {
                    throw new InvalidArgumentException("Invalid menu identity or choice at row {$line}.");
                }

                // Prices are EGP in DECIMAL(10,2): no exponent notation,
                // silent rounding or overflow beyond eight integer digits.
                if ($price !== '' && (! preg_match('/^[0-9]{1,8}(?:\.[0-9]{1,2})?$/D', $price)
                    || (float) $price <= 0)) {
                    throw new InvalidArgumentException("Invalid price at row {$line}.");
                }
                if ($price === '' && $flag !== 'yes') {
                    throw new InvalidArgumentException("Unconfirmed price must be flagged at row {$line}.");
                }
                if (trim($row['unparsed_price']) !== '' && $flag !== 'yes') {
                    throw new InvalidArgumentException("Unparsed price must be flagged for review at row {$line}.");
                }

                if (! isset($items[$key])) {
                    $items[$key] = [
                        'menu_key' => $key,
                        'section' => $section,
                        'name' => $name,
                        'type' => $type,
                        'notes' => trim($row['notes']),
                        'requires_review' => false,
                        'choices' => [],
                    ];
                }

                $item = &$items[$key];
                if ($item['section'] !== $section || $item['name'] !== $name || $item['type'] !== $type) {
                    throw new InvalidArgumentException("Menu key reused for another product at row {$line}.");
                }
                foreach ($item['choices'] as $choice) {
                    if (self::normalize($choice['size']) === self::normalize($variant)) {
                        throw new InvalidArgumentException("Duplicate size for {$key} at row {$line}.");
                    }
                }

                $item['choices'][] = [
                    'size' => $variant,
                    'price' => $price === '' ? null : (float) $price,
                ];
                $item['requires_review'] = $item['requires_review'] || $flag === 'yes';
                unset($item);
                $sections[$section] = true;
                $choiceCount++;
            }

            return new self(array_values($items), array_keys($sections), $choiceCount);
        } finally {
            fclose($handle);
        }
    }

    public function items(): array
    {
        return $this->items;
    }

    public function choiceCount(): int
    {
        return $this->choiceCount;
    }

    public function sections(): array
    {
        return $this->sections;
    }

    public function reviewItems(): array
    {
        return array_values(array_filter($this->items, fn (array $item): bool => $item['requires_review']));
    }

    public function confirmedProducts(): array
    {
        return array_values(array_filter($this->items, fn (array $item): bool => ! $item['requires_review'] && $item['type'] === 'صنف'));
    }

    public function confirmedAddons(): array
    {
        return array_values(array_filter($this->items, fn (array $item): bool => ! $item['requires_review'] && $item['type'] === 'إضافة'));
    }

    public static function normalize(string $value): string
    {
        $value = str_replace(['أ', 'إ', 'آ', 'ٱ', 'ى', 'ة', 'ؤ', 'ئ'], ['ا', 'ا', 'ا', 'ا', 'ي', 'ه', 'و', 'ي'], trim($value));
        $value = preg_replace('/[\x{064B}-\x{065F}\x{0640}]/u', '', $value);
        $value = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $value ?? '');

        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $value ?? '')), 'UTF-8');
    }
}
