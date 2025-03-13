<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->singleton('currentLocale', function ($app) {
            $request = $app->make(Request::class);

            // First check if currentLocale is in the request
            if ($request->has('currentLocale')) {
                $currentLocale = $request->query('currentLocale');
                session(['currentLocale' => $currentLocale]);
            }
            // Then check if it's in the session
            else {
                $currentLocale = session('currentLocale', config('languages.default'));
            }

            // Validate the locale
            $availableLocales = array_keys(config('languages.languages') ?? []);
            if (!in_array($currentLocale, $availableLocales)) {
                $currentLocale = config('languages.default');
                session(['currentLocale' => $currentLocale]);
            }

            // Set the application locale
            App::setLocale($currentLocale);

            return $currentLocale;
        });

        // Share currentLocale with all views
        View::composer('*', function ($view) {
            $currentLocale = app('currentLocale');
            $availableLocales = array_keys(config('languages.languages') ?? []);

            $view->with('currentLocale', $currentLocale);
            $view->with('availableLocales', $availableLocales);
        });
    }
}
