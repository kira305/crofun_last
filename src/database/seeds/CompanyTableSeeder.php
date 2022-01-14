<?php

use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name'           => '若林正恭',
                'name_kana'      => 'ワカバヤシマサヤス',
                'email'          => 'wakabayashi@example.com',
                'password'       => Hash::make('password'),
                'user_type'      => 2,
                'remember_token' => str_random(10),
            ],
            [
                'name'           => '春日俊彰',
                'name_kana'      => 'カスガトシアキ',
                'email'          => 'kasuga@example.com',
                'password'       => Hash::make('password'),
                'user_type'      => 2,
                'remember_token' => str_random(10),
            ],
        ]);
    }
}
