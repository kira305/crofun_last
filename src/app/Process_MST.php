<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Process_MST extends Model
{
        public $timestamps = true;
        protected $table = 'process';
		
    public function company(){

      
        return $this->hasOne('App\Company_MST','id', 'company_id');

    }

    public function customer(){

      
        return $this->hasOne('App\Customer_details_MST','id', 'client_id');

    }
    public function project(){

        return $this->hasOne('App\Project_MST','id', 'project_id');
    }
}
