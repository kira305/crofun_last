<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User; 
use App\Company_MST;
use Illuminate\Http\Request;
use DB;
use Common;
class GetListCompanyForUserEditComposer
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
        
        $companies   = Company_MST::whereIn('id',$company_id)->get();

        
           
        $view->with([

            'companies'          => $companies,
            
        ]);
    }
}