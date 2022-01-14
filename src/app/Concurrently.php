<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concurrently extends Model
{

    public $timestamps = true;
    protected $table = 'concurrently_mst';
    protected $primaryKey = 'id';


   public function user(){

       return $this->hasOne('App\User_MST','id', 'usr_id');
   }
   public function group(){

        return $this->hasOne('App\Group_MST','id', 'group_id');

    }

    public function position(){

        return $this->hasOne('App\Position_MST', 'id', 'position_id');

    }

    public function department(){

        return $this->hasOne('App\Department_MST','id', 'department_id');

    }

    public function headquarter(){


        return $this->hasOne('App\Headquarters_MST','id', 'headquarter_id');

    }

    public function company(){


        return $this->hasOne('App\Company_MST','id', 'company_id');

    }

    public function checkIsDisable($concurrently_id){

      $concurrently = Concurrently::where('id',$concurrently_id)->first();
      if($concurrently->headquarter->status == false || $concurrently->department->status == false || $concurrently->group->status == false){

            return 1;
      }
      return 0;
    }
}
