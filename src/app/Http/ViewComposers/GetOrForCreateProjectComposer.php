<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Company_MST;
use App\Headquarters_MST;
use App\Department_MST;
use App\Group_MST;
use DB;
use Common;

class GetOrForCreateProjectComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        
        $user = Auth::user();
        $usr_id       = Auth::user()->id;
        $company_id   = Common::checkUserCompany($usr_id);

        if($user->position->company_look == true){
          

          $headquarters = Headquarters_MST::where('headquarters_mst.status',true)->whereIn('headquarters_mst.company_id', $company_id)->orderBy('company_id', 'asc')->orderBy('headquarters_code', 'asc')->get();
          $headquarter_id_list = $headquarters->pluck('id')->toArray();
          $departments = Department_MST::join('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id') ->where('department_mst.status',true)->whereIn('headquarters_mst.company_id', $company_id)->orderBy('headquarters_mst.company_id', 'asc')->orderBy('department_mst.department_code', 'asc')->select('department_mst.*')->get();
          $department_id_list =  $departments->pluck('id')->toArray();
          $groups      = Group_MST::where('group_mst.status',true)->orderBy('group_code', 'asc')->get();
          $group_id_list      = $groups->pluck('id')->toArray();
        }

        if($user->position->headquarter_look == true){
          
          $headquarter_id_list = DB::table('concurrently_mst')
                                ->where('usr_id', $usr_id)
                                ->where('status',true)->pluck('headquarter_id')->toArray();
          array_push($headquarter_id_list,$user->headquarter_id);

          $headquarters = Headquarters_MST::where('headquarters_mst.status',true)->whereIn('id',$headquarter_id_list)->get();
          $departments = Department_MST::join('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id') ->where('department_mst.status',true)->whereIn('headquarters_mst.company_id', $company_id)->orderBy('headquarters_mst.company_id', 'asc')->orderBy('department_mst.department_code', 'asc')->select('department_mst.*')->get();
          $department_id_list =  $departments->pluck('id')->toArray();
          $groups      = Group_MST::where('group_mst.status',true)->orderBy('group_code', 'asc')->get();
          $group_id_list      = $groups->pluck('id')->toArray();
        }

        if($user->position->department_look == true){
          
         $headquarter_id_list = DB::table('concurrently_mst')
                                ->where('usr_id', $usr_id)
                                ->where('status',true)->pluck('headquarter_id')->toArray();
          array_push($headquarter_id_list,$user->headquarter_id);

          $headquarters = Headquarters_MST::where('headquarters_mst.status',true)->whereIn('id',$headquarter_id_list)->get();
          $department_id_list = DB::table('concurrently_mst')
                                ->where('usr_id', $usr_id)
                                ->where('status',true)->pluck('department_id')->toArray();
          array_push($department_id_list,$user->department_id);

          $departments   = Department_MST::where('status',true)->whereIn('id',$department_id_list)->get();
          $groups        = Group_MST::where('group_mst.status',true)->orderBy('group_code', 'asc')->get();
          $group_id_list =  $groups->pluck('id')->toArray();
      
        }
        

        if($user->position->group_look == true){
         
          $headquarter_id_list = DB::table('concurrently_mst')
                                ->where('usr_id', $usr_id)
                                ->where('status',true)->pluck('headquarter_id')->toArray();
          array_push($headquarter_id_list,$user->headquarter_id);

          $headquarters = Headquarters_MST::where('headquarters_mst.status',true)->whereIn('id',$headquarter_id_list)->get();

          $department_id_list = DB::table('concurrently_mst')
                                ->where('usr_id', $usr_id)
                                ->where('status',true)->pluck('department_id')->toArray();

          array_push($department_id_list,$user->department_id);

          $departments = Department_MST::where('status',true)->whereIn('id',$department_id_list)->get();
      
          $group_id_list = DB::table('concurrently_mst')
                                ->where('usr_id', $usr_id)
                                ->where('status',true)->pluck('group_id')->toArray();
          array_push($group_id_list,$user->group_id);

          $groups = Group_MST::where('status',true)->whereIn('id',$group_id_list)->get();
      
        }
        

        if($user->getrole->admin_flag == 1 || $user->getrole->superuser_user == 1){
             
           $headquarters = Headquarters_MST::where('headquarters_mst.status',true)->whereIn('headquarters_mst.company_id', $company_id)->orderBy('company_id', 'asc')->orderBy('headquarters_code', 'asc')->get();
           $headquarter_id_list = $headquarters->pluck('id')->toArray();
           $departments = Department_MST::join('headquarters_mst', 'department_mst.headquarters_id', '=', 'headquarters_mst.id') ->where('department_mst.status',true)->whereIn('headquarters_mst.company_id', $company_id)->orderBy('headquarters_mst.company_id', 'asc')->orderBy('department_mst.department_code', 'asc')->select('department_mst.*')->get();
           $department_id_list =  $departments->pluck('id')->toArray();
           $groups             = Group_MST::where('group_mst.status',true)->orderBy('group_code', 'asc')->get();
           $group_id_list      = $groups->pluck('id')->toArray();
        }

        $view->with([

            'headquarters'          =>   $headquarters,
            'departments'           =>   $departments,
            'groups'                =>   $groups,
            
        ]);
          

        JavaScript::put([

                'p' => 1,
                'headquarter_id_list'          =>   $headquarter_id_list,
                'department_id_list'           =>   $department_id_list,
                'group_id_list'                =>   $group_id_list,

        ]);

    }
}