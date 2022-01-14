<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule_MST extends Model
{
        public $timestamps = true;
        protected $table = 'rule_mst';
        
	    public function company(){

	      
	        return $this->hasOne('App\Company_MST','id', 'company_id');

	    }
}