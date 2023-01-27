<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('create-users', function (User $user) {
            $authorized_roles = ['admin'];
            return in_array($user->role, $authorized_roles);
        });

        Gate::define('view-users', function (User $user) {
            $authorized_roles = ['admin', 'editor'];
            return in_array($user->role, $authorized_roles);
        });
    }
}
