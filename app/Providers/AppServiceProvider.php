<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::define('manage-users', fn ($user) => $user->hasPermission('manage-users'));
        Gate::define('access-gestao', fn ($user) => $user->hasPermission('access-gestao'));
        Gate::define('access-projetos', fn ($user) => $user->hasPermission('access-projetos'));
        Gate::define('access-exploracao', fn ($user) => $user->hasPermission('access-exploracao'));
        Gate::define('access-admin', fn ($user) => $user->hasPermission('access-admin'));
    }
}
