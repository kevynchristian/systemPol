<?php

namespace App\Providers;

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
        // Compartilha a variável de usuários online com o layout principal
        \Illuminate\Support\Facades\View::composer('layout.app', function ($view) {
            $users = \App\Models\User::all();
            $onlineUsers = $users->filter(function($user) {
                return \Illuminate\Support\Facades\Cache::has('user-is-online-' . $user->id);
            });
            $view->with('onlineUsers', $onlineUsers);
        });
    }
}
