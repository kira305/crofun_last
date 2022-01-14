<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Department_MST;
use App\Group_MST;
class Cost_MST extends Model
{
        public $timestamps = true;
        protected $table = 'cost_mst';
    
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

    public function checkIsNull(){
       

        if($this->headquarter->status == false && $this->department_id != null && $this->group_id != null){

                 return 4;

        }
 
       if($this->department_id != null && $this->group_id != null){

            if($this->headquarter->status == false || $this->department->status == false || $this->group->status == false ){
                
                 return 1;
            }
       }
      
       if($this->department_id != null && $this->group_id == null){
 
            if($this->headquarter->status == false || $this->department->status == false ){

                 return 2;
            }
       }
        
        if($this->department_id == null && $this->group_id == null){
          
            if($this->headquarter->status == false ){

                 return 3;
            }
       }
       
       return 0;
    }
}