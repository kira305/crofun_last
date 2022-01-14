<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ImportLogErr;
class ImportLog extends Model
{
        public $timestamps = false;
        protected $table = 'import_log';
        

    public function user(){

       return $this->hasOne('App\User_MST','id', 'user_id');
    }
    
    public function import_err(){

    	$log_err  = ImportLogErr::where('file_id',$this->id)->first();

        return $log_err;

    }

}