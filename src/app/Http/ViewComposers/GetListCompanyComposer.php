<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Company_MST;
use DB;
use Common;
class GetListCompanyComposer
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

        $companies = Company_MST::where('id', $company_id)->get();
           
        $view->with([

            'companies'          => $companies,
            
        ]);
    }
}