<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule_action extends Model
{
        public $timestamps = true;
        protected $table = 'rule_lure_mst';
        protected $primaryKey = 'id';
        public $incrementing = true;

}