<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company_MST extends Model
{
        public $timestamps = true;
        protected $table = 'company_mst';
        protected $primaryKey = 'own_company';
        public $incrementing = false;

}