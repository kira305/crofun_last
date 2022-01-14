<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Department_MST;
use DB;
use Helper;
use Common;

class GetListDepartmentComposer
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
        $departments = Department_MST::join('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id')
                                    ->where('department_mst.status', true)
                                    ->where('headquarters_mst.company_id', $company_id)
                                    ->orderBy('headquarters_mst.company_id', 'asc')
                                    ->orderBy('department_code', 'asc')
                                    ->select('department_mst.*')->get();
        $view->with([
            'departments'          => $departments,
        ]);
    }
}
