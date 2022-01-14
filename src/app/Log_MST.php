<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_MST extends Model
{
        public $timestamps = true;
        protected $table = 'log';
		
    public function company(){

      
        return $this->hasOne('App\Company_MST','id', 'company_id');

    }

    public function menu(){

      
        return $this->hasOne('App\Menu','id', 'form_id');

    }

    public function user(){

      
        return $this->hasOne('App\User_MST','id', 'user_id');

    }

    public function table(){

        return $this->hasOne('App\Table_MST','id', 'table_id');

    }

    }
