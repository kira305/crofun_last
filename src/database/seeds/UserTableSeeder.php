<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
             factory(App\User::class, 1000)->create();
            // factory(App\User::class, 50)->create()->each(function ($user) {

            //     // $user->group()->save(factory(App\Group_MST::class)->make());
            //     // $user->department()->save(factory(App\Department_MST::class)->make());
            //     // $user->headquarter()->save(factory(App\Headquarter_MST::class)->make());
            //     // $user->company()->save(factory(App\Company_MST::class)->make());
            //     // $user->position()->save(factory(App\Position_MST::class)->make());
            //     // $user->concurrent()->save(factory(App\Concurrently::class)->make());
            //     // $user->getrole()->save(factory(App\Rule_MST::class)->make());

            // });
    }
}
