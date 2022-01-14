<?php
use Faker\Generator as Faker;
use Illuminate\Support\Str;
$factory->define(App\User::class, function (Faker $faker) {
    return [

        'usr_code'   => 9999999,
        'usr_name'   => Str::random(10),
        'rule'       => 1,
        'company_id' => 1,
        'headquarter_id'   => 100,
        'department_id'    => 100,
        'group_id'         => 100,
        'retire'           => true,
        'position_id'      => 100,
        'pw_error_ctr'     => 1,
        'login_first'      => false,

    ];
});
