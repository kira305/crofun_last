<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;
class Diagram extends Model
{       
	   
        public $timestamps = false;
        protected $table = 'organization_history';
        protected $primaryKey = 'id';
        public $incrementing = true;

	    public function searchableAs()
	    {
	        return 'organization_history';
	    }

        public function getScoutKey()
	    {
	        return $this->id;
	    }
        public function getCreateAtAttribute()
		{   
			return Carbon::changeFormatDateyymmdd($this->attributes['created_at']);
		  
		}

}