<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User; 
use App\Company_MST;
use DB;
use Common;
class Usr_edit_company
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

        $companies   = Company_MST::all();
           
        $view->with([

            'companies'          => $companies,
            
        ]);
    }
}