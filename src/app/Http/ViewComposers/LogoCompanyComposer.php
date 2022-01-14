<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Company_MST;
use DB;
use Common;
class LogoCompanyComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       

         $user         = Auth::user();

         $company_logo = $user->company->logo;
        
           
        $view->with([

            'logo'          =>  $company_logo,
            
        ]);
    }
}