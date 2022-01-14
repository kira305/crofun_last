<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class password_cycle extends Model
{
        public $timestamps = false;
        protected $table = 'password_cycle';
        protected $primaryKey = 'id';
        public $incrementing = true;

}