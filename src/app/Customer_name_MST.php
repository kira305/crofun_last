<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer_name_MST extends Model
{
        public $timestamps = true;
        protected $table   = 'customer_name';

    public function company(){

            return $this->leftjoin('customer_mst', 'customer_name.client_id', '=', 'customer_mst.id')->leftjoin('company_mst', 'customer_mst.company_id', '=', 'company_mst.id')->where('customer_name.id',$this->id)->first();
        }

    public function customer(){
       
        return $this->hasOne('App\Customer_MST','id', 'client_id');
    }
}