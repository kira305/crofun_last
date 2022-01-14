<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Group_MST;
use DB;
use Common;
class GetListGroupComposer
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
    
             $groups      = Group_MST::join('department_mst', 'department_mst.id', '=', 'group_mst.department_id')
                                    ->join('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id')
                                    ->where('group_mst.status',true)
                                    ->where('headquarters_mst.company_id', $company_id)
                                    ->orderBy('headquarters_mst.company_id', 'asc')
                                    ->orderBy('group_mst.group_code', 'asc')
                                    ->select('group_mst.*')->get();
                                     
        $view->with([

            'group_list'          => $groups,
            
        ]);
    }
}