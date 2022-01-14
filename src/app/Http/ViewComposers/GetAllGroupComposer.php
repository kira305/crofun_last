<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Group_MST;
use DB;
use Common;
class GetAllGroupComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       

             $usr_id      = Auth::user()->id;
       
             $company_id  = Common::checkUserCompany($usr_id);
    
             $groups      = Group_MST::where('group_mst.status',true)->orderBy('group_code', 'asc')->get();
                                     
         
        $view->with([

            'groups'          => $groups,
            
        ]);
    }
}