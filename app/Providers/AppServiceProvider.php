<?php

namespace App\Providers;

use App\Rules\PhoneNumber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $custom_rules = [
        PhoneNumber::class,
    ];
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
        $this->registerValidationRules();
    }

    private function registerValidationRules(): void
    {
        if (!app()->runningInConsole()) {
            foreach ($this->custom_rules as $class) {
                $alias = (string)(new $class);
                if ($alias && strlen($alias) > 0) {
                    Validator::extend($alias, $class . '@passes', (new $class)->message());
                }
            }
        }
    }
}
