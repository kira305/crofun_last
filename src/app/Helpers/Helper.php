<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

class Helper
{
    public static function pr($data)
    {
        echo "<pre>";
		    print_r($data); // or var_dump($data);
		    echo "</pre>";
    }
    
    public static function getCurrentTime()
    {
          date_default_timezone_set('Australia/Melbourne');
          $date = date('Y-m-d h:i:s', time());
          return $date;
    }
}

?>