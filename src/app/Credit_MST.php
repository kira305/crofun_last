<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit_MST extends Model
{
    public $timestamps = true;
        
    protected $table = 'credit_check';
		
    public function company(){

      
        return $this->hasOne('App\Company_MST','id', 'company_id');

    }

    public function customer(){

      
        return $this->hasOne('App\Customer_details_MST','id', 'client_id');

    }



}
