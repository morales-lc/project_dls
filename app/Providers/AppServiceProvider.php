<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

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
        // Use Bootstrap 5 for pagination views
        Paginator::useBootstrapFive();


        // Register custom MIME types for MARC files
        \Illuminate\Support\Facades\Validator::extend('marc_file', function ($attribute, $value, $parameters, $validator) {
            if (!$value instanceof \Illuminate\Http\UploadedFile) {
                return false;
            }
            $extension = strtolower($value->getClientOriginalExtension());
            return in_array($extension, ['001', 'mrc', 'marc']);
        });

        \Illuminate\Support\Facades\Validator::replacer('marc_file', function ($message, $attribute, $rule, $parameters) {
            return 'The :attribute must be a valid MARC file (.001, .mrc, or .marc).';
        });


    }
}
