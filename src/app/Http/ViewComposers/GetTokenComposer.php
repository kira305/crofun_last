<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Company_MST;
use DB;
use Common;
class GetTokenComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        

        $token = 0;
          
        if (auth('api')->attempt(['usr_code'=>session('usr_code1'),'pw'=>session('pw1')])) {
            
       
            $token = auth('api')->attempt(['usr_code'=>session('usr_code1'),'pw'=>session('pw1')]);

        }

        $view->with([

            'token'          => $token,
            
        ]);
    }
}