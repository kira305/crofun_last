<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Rule_MST;
use DB;

class RoleListComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       

        $rules = Rule_MST::all();
           
        $view->with([

            'rules'          => $rules,
            
        ]);
    }
}