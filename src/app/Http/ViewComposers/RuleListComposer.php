<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Rule_MST;
use DB;
use Common;

class RuleListComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       
    $usr_id       = Auth::user()->id;
   
    $company_id   = Common::checkUserCompany($usr_id);
    // dd($company_id);
    $rules = Rule_MST::whereIn('rule_mst.company_id', $company_id)->get();
    // dd();
    $view->with([

        'rule_list'          => $rules,

    ]);
    }
}