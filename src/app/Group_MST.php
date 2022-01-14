<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group_MST extends Model
{
    public $timestamps    = true;
    protected $table      = 'group_mst';
    protected $primaryKey = 'id';

    public function department()
    {
        return $this->hasOne('App\Department_MST', 'id', 'department_id')->first();
    }


    public function headquarter()
    {
        return $this->leftjoin('department_mst', 'department_mst.id', '=', 'group_mst.department_id')
            ->leftjoin('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id')
            ->leftjoin('company_mst', 'headquarters_mst.company_id', '=', 'company_mst.id')
            ->where('group_mst.id', $this->id)
            ->first();
    }
}
