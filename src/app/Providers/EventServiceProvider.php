<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        'App\Events\LogEvent' => [
            'App\Listeners\LogEventListener',
        ],
        'App\Events\HeadquarterChangeEvent' => [
            'App\Listeners\HeadquarterEventListener',
        ],
        'App\Events\DepartmentChangeEvent' => [
            'App\Listeners\DepartmentEventListener',
        ],
        'App\Events\GroupChangeEvent' => [
            'App\Listeners\GroupEventListener',
        ],
        'App\Events\SendCreditMail' => [
            'App\Listeners\SendCreditMailListener',
        ],
        'App\Events\ChangePassEvent' => [
            'App\Listeners\ChangePassListener',
        ],
        'App\Events\HeadquarterChangeWithoutParent' => [
            'App\Listeners\HeadquarterChangeWithoutParentListener',
        ],
        'App\Events\DepartmentChangeWithoutParent' => [
            'App\Listeners\DepartmentChangeWithoutParentListener',
        ],
        'App\Events\GroupChangeWithoutParent' => [
            'App\Listeners\GroupChangeWithoutParentListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
