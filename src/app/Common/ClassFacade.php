<?php 
namespace App\Common;
use Illuminate\Support\Facades\Facade;
class ClassFacade extends Facade{

    protected static function getFacadeAccessor() { 
        return 'crofun'; 
    }

}

?>