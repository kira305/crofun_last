<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Department_MST;
use Common;

class GetAllDepartmentComposer
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
        $departments = Department_MST::join('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id')
            ->where('department_mst.status', true)
            ->whereIn('headquarters_mst.company_id', $company_id)
            ->orderBy('headquarters_mst.company_id', 'asc')
            ->orderBy('department_mst.department_code', 'asc')
            ->select('department_mst.*')->get();
        $view->with([
            'departments'          => $departments,
        ]);
    }
}
