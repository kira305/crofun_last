<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Service\DiagramService;
use App\Company_MST;
use App\Project_MST;
use App\Diagram;
use DB;
use Common;
class GetListProjectComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       
        $diagram_service = new DiagramService();
        $usr_id        = Auth::user()->id;
        
        $company_id    = Common::checkUserCompany($usr_id);

        $date            = $diagram_service->getToday();
       
        if (request()->search_date != null && request()->search_date != "") {

           $date = $diagram_service->changeFormatDate(request()->search_date);

        }
        //$dateの一番近いと最大のcreated_atを取得
        $list_id  = $diagram_service->getListTreeIdByTime($date);
        $array_created_at = array();
        foreach ($list_id as $key => $value) {

           array_push($array_created_at, $value->created_at);

        }
        if(request()->company_id != null || request()->company_id != ""){
        
          $company_id    = request()->company_id;
          $projects      = Diagram::where('company_id',$company_id)
                                  ->whereIn('created_at',$array_created_at)
                                  ->where('flag',true)
                                  ->get();

        }else {
          
          $projects      = Diagram::whereIn('company_id',$company_id)
                                  ->whereIn('created_at',$array_created_at)
                                  ->where('flag',true)
                                  ->get();

        }
        // dd($array_id);
        $list_projects_sale   = array(); 
        $list_projects_code   = array();
        $checked_project_sale = array();
        $checked_project_code = array();
        foreach ($projects as $project) {
           
           if($project->project_grp_code != null && !in_array($project->project_grp_code, $checked_project_sale) && $project->flag == true){

              array_push($list_projects_sale, $project);
              array_push($checked_project_sale, $project->project_grp_code);

           }
           
           if($project->project_code != null && !in_array($project->project_code, $checked_project_code) && $project->flag == true){
             
             array_push($list_projects_code, $project);
             array_push($checked_project_code, $project->project_code);

           }
           
        }
     
        $view->with([

            'projects'               =>  $list_projects_sale,
            'code_projects'          =>  $list_projects_code
        ]);
    }


}