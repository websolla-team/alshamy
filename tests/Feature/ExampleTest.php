<?php

namespace Tests\Feature;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_unlocalized_home_redirects_to_the_configured_locale(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getDefaultLocale(),
            '/'
        ));
    }
}
