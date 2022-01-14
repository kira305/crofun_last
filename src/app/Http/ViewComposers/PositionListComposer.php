<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Position_MST;
use DB;
use Common;
class PositionListComposer
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

         $positions = Position_MST::whereIn('position_mst.company_id', $company_id)->get();

        $view->with([

            'position_list'          => $positions,
            
        ]);

    }
}