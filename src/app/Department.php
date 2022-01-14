<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
        public    $timestamps = false;
        protected $table      = 'department';
        
        protected $primaryKey = null;
        public $incrementing = false;

}