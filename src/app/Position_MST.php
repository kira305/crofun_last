<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position_MST extends Model
{
        public $timestamps = true;
        protected $table = 'position_mst';
		
	    public function company(){

	      
	        return $this->hasOne('App\Company_MST','id', 'company_id');

	    }

        public function getLookAttribute()
		{
			   if($this->company_look == true){

			   	  return '全事業本部';
			   }

			   if($this->headquarter_look == true){

			   	  return '所属事業本部のみ';
			   }
			   
			   if($this->department_look == true){

			   	  return '所属部署のみ';
			   }

			   if($this->group_look == true){

			   	  return '所属Grpのみ';
			   }

		}

}