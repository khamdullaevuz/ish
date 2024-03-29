<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Enums\UserRoles;
use App\Models\Company;
use App\Models\Employee;
use App\Policies\CompanyPolicy;
use App\Policies\EmployeePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        Company::class => CompanyPolicy::class,
        Employee::class => EmployeePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', function ($user) {
            return $user->role === UserRoles::ADMIN;
        });
    }
}
