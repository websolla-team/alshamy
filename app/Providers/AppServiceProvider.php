<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        $this->app->scoped('alshamy.shared_view_data', static function () {
            return [
                'Contact' => Contact::first(),
                'About' => Page::first(),
                'Setting' => Setting::first(),
                'Pages' => Page::where('id', '!=', 1)->get(['id', 'title']),
                'CategoryTypes' => Category::get(['id', 'title']),
            ];
        });
        View::composer('*', static function ($view) {
            $view->with(app('alshamy.shared_view_data'));
        });
    }
}
