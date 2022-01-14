<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Headquarters_MST;
use DB;
use Common;

class GetAllHeadquarterComposer
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

         $headquarters = Headquarters_MST::where('headquarters_mst.status',true)->whereIn('headquarters_mst.company_id', $company_id)->orderBy('company_id', 'asc')->orderBy('headquarters_code', 'asc')->get();
                                     

         
        $view->with([

            'headquarters'          => $headquarters,
            
        ]);
    }
}