<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::directive('css', function ($path) {
            static $library;
            if (! isset($library[$path])) {
                $library = [];
            }

            if (isset($library[$path])) {
                return;
            }
            $library[$path] = true;

            return "<?= '<link rel=\"stylesheet\" href=\"'.$path.'\" />' ?>\n";
        });

        Blade::directive('js', function (string $path) {
            static $jslibrary;
            if (! isset($jslibrary[$path])) {
                $jslibrary = [];
            }

            if (isset($jslibrary[$path])) {
                return;
            }
            $jslibrary[$path] = true;

            return "<?= '<script src=\"'.$path.'\"></script>' ?>\n";
        });
    }
}
