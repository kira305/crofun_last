<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receivable_MST extends Model
{
        public $timestamps = true;
        protected $table = 'account_receivable';
		protected $dates = [
                                'target_data',
                            ];
                            
    public function company(){

      
        return $this->hasOne('App\Company_MST','id', 'company_id');

    }

    public function customer(){

        return $this->hasOne('App\Customer_details_MST','id', 'client_id');

    }
}
