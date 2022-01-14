<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class system extends Model
{
        public $timestamps = false;
        protected $table = 'system';
        protected $primaryKey = 'f_system_info_key';
        public $incrementing = true;

}