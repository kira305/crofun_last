<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\Service\DiagramService;
use App\User;
use App\Company_MST;
use App\Group_MST;
use App\Cost_MST;
use App\Cost;
use App\Diagram;
use DB;

class CostListComposer
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
        $list_cost_1 = array();
        $diagram_service = new DiagramService();
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
        if(request()->company_id == null){
                    
                    $company_id = Auth::user()->company_id;

        }else {

                    $company_id = request()->company_id;

        }
        
        $diagrams = Diagram::where('company_id',$company_id)
                    ->whereIn('created_at',$array_created_at)
                    ->where('flag',true)
                    ->distinct()->select('sales_management_code','sales_management','company_id')->get();

        $list_cost_2  = array();
        foreach ($diagrams as $diagram) {

            if($diagram->sales_management_code != null){

                $cost             = new Cost();
                $cost->cost_code  = $diagram->sales_management_code;
                $cost->cost_name  = $diagram->sales_management;
                $cost->company_id = $diagram->company_id;
                array_push( $list_cost_2, $cost);
            }

        }
 
        $diagrams     = Diagram::where('company_id',$company_id)->distinct()->select('cost_code','cost_name','company_id')
                      ->whereIn('created_at',$array_created_at)
                      ->where('flag',true)
                      ->get();
        
        $list_cost_1  = array();
        foreach ($diagrams as $diagram) {

            if($diagram->cost_code != null){

                $cost             = new Cost();
                $cost->cost_code  = $diagram->cost_code;
                $cost->cost_name  = $diagram->cost_name;
                $cost->company_id = $diagram->company_id;
                array_push( $list_cost_1, $cost);
            }

        }
        // $groups      = Group_MST::where('group_mst.status',true)
        //              ->get();
        
        // foreach ($groups as $group) {
           
        //      $cost             = new Cost();
        //      $cost->cost_code  = $group->cost_code;
        //      $cost->cost_name  = $group->cost_name;
        //      $cost->group_id   = $group->id;
        //      $cost->company_id = $group->headquarter()->company_id;

        //      array_push($list_cost_1,$cost);

        // }

        // $costs = Cost_MST::where('company_id', $company_id)
        //                  ->where('type',1)
        //                  ->get();

        // foreach ($costs as $c) {
            
        //      $cost             = new Cost();
        //      $cost->cost_code  = $c->cost_code;
        //      $cost->cost_name  = $c->cost_name;
        //      $cost->group_id   = $c->group_id;
        //      $cost->company_id = $c->company_id;
        //      array_push($list_cost_1,$cost);

        // }
        
        // $list_cost_2  = array();

        // $costs = Cost_MST::where('company_id', $company_id)
        //                  ->where('type',2)
        //                  ->get();

        // foreach ($costs as $c) {
            
        //      $cost             = new Cost();
        //      $cost->cost_code  = $c->cost_code;
        //      $cost->cost_name  = $c->cost_name;
        //      $cost->group_id   = $c->group_id;
        //      $cost->company_id = $c->company_id;
        //      array_push($list_cost_2,$cost);

        // }
      
        $view->with([
            
            'hanka_s'          =>  $list_cost_2,
            'genka_s'          =>  $list_cost_1,

            
        ]);
    }
}