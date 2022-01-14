<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department_MST extends Model
{
        public $timestamps = true;
        protected $table      = 'department_mst';
        protected $primaryKey = 'id';
        public function groups(){

        	return $this->hasMany('App\Group_MST', 'id', 'department_id');
        }
        
        public function headquarter(){
            
            return $this->hasOne('App\Headquarters_MST', 'id', 'headquarters_id')->first();

        }

        public function company(){

        	 return $this->leftjoin('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id')->leftjoin('company_mst', 'headquarters_mst.company_id', '=', 'company_mst.id')->where('department_mst.id',$this->id)->first();;

        }
}