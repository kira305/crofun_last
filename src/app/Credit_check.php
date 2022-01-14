<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Credit_check extends Model
{
    public $timestamps = true;
    protected $table = 'credit_check';

    public function getGetTimeAttribute()
    {
        return !empty($this->attributes['get_time']) ? Carbon::parse($this->attributes['get_time'])->format('Y-m-d') : null;
    }

    public function getExpirationDateAttribute()
    {
        return !empty($this->attributes['expiration_date']) ? Carbon::parse($this->attributes['expiration_date'])->format('Y-m-d') : null;
    }
}
