<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Headquarters_MST;
use App\Department_MST;
use App\Group_MST;
use DB;
use Common;
class UserIndexViewComposer
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

         $headquarters = Headquarters_MST::whereIn('headquarters_mst.company_id', $company_id)->get();

         $departments  = DB::select('select department_mst.id,department_mst.department_name from department_mst,headquarters_mst where (department_mst.headquarters_id = headquarters_mst.id ) and headquarters_mst.company_id IN  ('.implode(',',$company_id).')');
       
         $groups  = DB::select('select group_mst.id,group_mst.group_name from group_mst,department_mst,headquarters_mst where group_mst.department_id = department_mst.id and (department_mst.headquarters_id = headquarters_mst.id ) and headquarters_mst.company_id IN  ('.implode(',',$company_id).')');

       

         $view->with([
            
            'headquarters'         => $headquarters,
            'departments'          => $departments,
            'groups'               => $groups,

            
        ]);
    }
}