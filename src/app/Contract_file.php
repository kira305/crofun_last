<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Contract_file extends Model
{
    public $timestamps = true;
    protected $table = 'contract_file';

    public function getUpdatedAtAttribute()
    {
        return !empty($this->attributes['updated_at']) ? Carbon::parse($this->attributes['updated_at'])->format('Y/m/d H:i:s') : null;
    }
}
