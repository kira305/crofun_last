<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Rule_action;
use App\Rule_MST;
use App\Menu;
use DB;

class GetListMenuWithRuleComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        
        $rule_action_id = array();

        if((session('menu') != null) && (sizeof(session('menu')->toArray()) > 0)){
            

                   $rule_action_id = session('menu')->toArray();
                   

        }else {

                    $usr_rule         = Auth::user()->rule;
                    $rule_action_id   = Rule_action::where('rule_id',$usr_rule)->get()->pluck('action_id');
                  
                    session(['menu'  => $rule_action_id]);

        }

     
        JavaScript::put([

                       'rule_action_id_array' => $rule_action_id

                    ]);


    }
}