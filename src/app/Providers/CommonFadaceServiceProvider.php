<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;

class CommonFadaceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('crofun', function() {
            return new \App\Common\Crofun;
        });

        App::bind('test', function() {
            return new \App\Common\Test;
        });
    }
}
