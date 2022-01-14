<?php

namespace App\Policies;

use App\User;
use App\Project_MST;
use App\Rule_action;
use App\Concurrently;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the project_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Project_MST  $projectMST
     * @return mixed
     */
    public function view(User $user, Project_MST $projectMST)
    {  
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.PROJECT_VIEW'))
                       ->first();
       
        if($rule_action){
           
            /*管理者フラグなら全部OK*/
            if($user->getrole->admin_flag == 1){
           
                return true;
            }

            if($user->company_id == $projectMST->company_id && $user->position->company_look == true){

                return true;
            }

            /*ルールマスタを見て参照範囲の確認*/
            if($user->headquarter_id == $projectMST->headquarter_id && $user->position->headquarter_look == true){

                return true;
            }

            if($user->department_id == $projectMST->department_id && $user->position->department_look == true){

                return true;
            }

            if($user->group_id == $projectMST->group_id && $user->position->group_look == true){

                return true;
            }

            $concurently = Concurrently::where('usr_id',$user->id)->where('status',true)->get();

            foreach ($concurently as $c) {
               

                if($c->headquarter_id == $projectMST->headquarter_id && $c->position->headquarter_look == true){

                    return true;
                }

                if($c->department_id == $projectMST->department_id && $c->position->department_look == true){

                    return true;
                }

                if($c->group_id     == $projectMST->group_id && $c->position->group_look == true){

                    return true;
                }
               

            }
        }

        return false;
    }

    /**
     * Determine whether the user can create project_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.PROJECT_ADD'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can update the project_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Project_MST  $projectMST
     * @return mixed
     */
    public function update(User $user, Project_MST $projectMST)
    {
  
       

        /*ルールマスタを見て参照範囲の確認*/
        $rule_action = Rule_action::where('rule_id',$user->rule)
                     ->where('action_id',config('constant.PROJECT_EDIT'))
                     ->first();
        
        if($rule_action){

            /*管理者フラグなら全部OK*/
            if($user->getrole->admin_flag == 1){
           
                return true;
            }           


            /*ルールマスタを見て参照範囲の確認*/
            if($user->company_id == $projectMST->company_id && $user->position->company_look == true){

                return true;
            }

            if($user->headquarter_id == $projectMST->headquarter_id && $user->position->headquarter_look == true){

                return true;
            }

            if($user->department_id == $projectMST->department_id && $user->position->department_look == true){

                return true;
            }

            if($user->group_id == $projectMST->group_id && $user->position->group_look == true){

                return true;
            }

            $concurently = Concurrently::where('usr_id',$user->id)->where('status',true)->get();

            foreach ($concurently as $c) {
               

                if($c->headquarter_id == $projectMST->headquarter_id && $c->position->headquarter_look == true){

                    return true;
                }

                if($c->department_id == $projectMST->department_id && $c->position->department_look == true){

                    return true;
                }

                if($c->group_id     == $projectMST->group_id && $c->position->group_look == true){

                    return true;
                }
               
               
            }

            return false;
        }

        return false;

    }
    
    public function checkProjectParent(User $user, Project_MST $projectMST){

          if($projectMST->headquarter->status == false || $projectMST->department->status == false || $projectMST->group->status == false){

               return 1;
          }

          return 0;

    }
    
    /**
     * Determine whether the user can delete the project_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Project_MST  $projectMST
     * @return mixed
     */
    public function delete(User $user, Project_MST $projectMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the project_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Project_MST  $projectMST
     * @return mixed
     */
    public function restore(User $user, Project_MST $projectMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the project_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Project_MST  $projectMST
     * @return mixed
     */
    public function forceDelete(User $user, Project_MST $projectMST)
    {
        //
    }
}
