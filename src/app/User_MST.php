<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_MST extends Model
{
    public $timestamps = false;
    protected $table = 'user_mst';
    public function group()
    {
        return $this->belongsToMany('App\Group_MST', 'department', 'usr_id', 'group_code');
    }

    public function company()
    {
        return $this->hasOne('App\Company_MST', 'id', 'company_id');
    }
}
