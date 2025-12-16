<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Services\PhoneNumberService;

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
        // Custom validation rule for international phone numbers
        Validator::extend('intl_phone', function ($attribute, $value) {
            try {
                PhoneNumberService::parse($value);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        });

        Validator::replacer('intl_phone', function ($message, $attribute, $rule, $parameters) {
            return 'Enter a valid phone number with country code. Example: +14155552671';
        });
    }
}
