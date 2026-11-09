<?php

namespace Tests\Feature;

use App\Http\Controllers\Front\ContactLinks;
use PHPUnit\Framework\TestCase;

class StorefrontContactLinksTest extends TestCase
{
    public function test_egyptian_mobile_number_becomes_valid_whatsapp_link(): void
    {
        $this->assertSame('https://wa.me/201012345678', ContactLinks::whatsapp('010 1234 5678'));
        $this->assertSame('https://wa.me/201012345678', ContactLinks::whatsapp('+20 10 1234 5678'));
        $this->assertSame('https://wa.me/201012345678', ContactLinks::whatsapp('https://wa.me/+201012345678'));
        $this->assertSame('https://wa.me/201012345678', ContactLinks::whatsapp('https://api.whatsapp.com/send?phone=201012345678'));
    }

    public function test_missing_or_invalid_whatsapp_number_does_not_generate_link(): void
    {
        $this->assertNull(ContactLinks::whatsapp(null));
        $this->assertNull(ContactLinks::whatsapp(''));
        $this->assertNull(ContactLinks::whatsapp('https://example.com/201012345678'));
        $this->assertNull(ContactLinks::whatsapp('010123'));
        $this->assertNull(ContactLinks::whatsapp('201012345678;malicious'));
    }

    public function test_phone_link_preserves_country_prefix_and_rejects_unusable_number(): void
    {
        $this->assertSame('tel:+201012345678', ContactLinks::telephone('+20 (10) 1234-5678'));
        $this->assertSame('tel:01012345678', ContactLinks::telephone('010 1234 5678'));
        $this->assertNull(ContactLinks::telephone(''));
        $this->assertNull(ContactLinks::telephone('not a phone number'));
    }
}
