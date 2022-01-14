<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract_type extends Model
{
    public $timestamps = true;
    protected $table = 'contract_type';

    public function company()
    {
        return $this->hasOne('App\Company_MST', 'id', 'company_id');
    }

}
