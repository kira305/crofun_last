<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Rule_action;
class Menu extends Model
{
        public $timestamps = false;
        protected $table = 'menu';

    public function rule_action($rule_id){
       
       $rule_action = Rule_action::where('rule_id',$rule_id)->where('action_id',$this->id)->first();

       if($rule_action){

       	 return true;

       }else {

         return false;
       }

    }
}


