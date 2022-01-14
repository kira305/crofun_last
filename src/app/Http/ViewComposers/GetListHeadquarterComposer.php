<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Headquarters_MST;
use DB;
use Common;

class GetListHeadquarterComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       
         $company_id  = Auth::user()->company_id;

      

         $headquarters = Headquarters_MST::where('headquarters_mst.status',true)
                        ->where('headquarters_mst.company_id', $company_id)->orderBy('headquarters_mst.company_id', 'asc')->orderBy('headquarters_code', 'asc')->get();
                                     

         
        $view->with([

            'headquarter_list'          => $headquarters,
            
        ]);
    }
}