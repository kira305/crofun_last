<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class global_info extends Model
{
        public $timestamps = false;
        protected $table = 'global_info';
        protected $primaryKey = 'id';
        public $incrementing = true;

}