<?php

namespace App\Providers;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Blade;
class AppServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Blade::withoutDoubleEncoding();
        Blade::component('components.paginate','paginate');
        Passport::routes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind('App\Service\CustomerService');
    }
}
