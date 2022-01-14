<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSeeder extends Model
{
        public $timestamps = true;
        protected $table = 'test_seeder';
        protected $primaryKey = 'id';
        public $incrementing = true;

}