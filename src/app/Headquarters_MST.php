<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Headquarters_MST extends Model
{
        public $timestamps    = true;
        protected $table      = 'headquarters_mst';
        protected $primaryKey = 'id';
        public $incrementing  = false;
        
        public function company(){

        	 return $this->hasOne('App\Company_MST','id', 'company_id')->first();
        }
}