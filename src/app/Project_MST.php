<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Crofun;
class Project_MST extends Model
{
        public $timestamps = true;
        protected $table = 'project_mst';
        public $incrementing = true;
    
        public function company(){

	      
	        return $this->hasOne('App\Company_MST','id', 'company_id');

	    }

	    public function headquarter(){

	      
	        return $this->hasOne('App\Headquarters_MST','id', 'headquarter_id');

	    }
	    
	    public function department(){

	        return $this->hasOne('App\Department_MST','id', 'department_id');

	    }

	    public function group(){

	        return $this->hasOne('App\Group_MST','id', 'group_id');

	    }

	    public function customer(){

	        return $this->hasOne('App\Customer_MST','id', 'client_id');

	    }
        
}