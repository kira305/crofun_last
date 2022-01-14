<?php

namespace App\Policies;

use App\User;
use App\Process_MST;
use App\Rule_action;
use App\Concurrently;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the process_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Process_MST  $processMST
     * @return mixed
     */
    public function view2(User $user, Process_MST $prcessMST)
    {

        /*ルールマスタを見て参照範囲の確認*/
        $rule_action = Rule_action::where('rule_id',$user->rule)
                     ->where('action_id',config('constant.PROCESS_INDEX'))
                     ->first();

        if($rule_action){

            /*管理者フラグなら全部OK*/
            if($user->getrole->admin_flag == 1){
           
                return true;
            }           
            if($user->company_id == $prcessMST->project->company_id && $user->position->company_look == true){

                return true;

            }
            /*ルールマスタを見て参照範囲の確認*/
            if($user->headquarter_id == $prcessMST->project->headquarter->id && $user->position->headquarter_look == true){

                return true;
            }

            if($user->department_id == $prcessMST->project->department->id && $user->position->department_look == true){

                return true;
            }

            if($user->group_id == $prcessMST->project->group->id && $user->position->group_look == true){

                return true;
            }

            $concurently = Concurrently::where('usr_id',$user->id)->where('status',true)->get();

            foreach ($concurently as $c) {
               
                if($c->company_id == $prcessMST->project->company_id && $user->position->company_look == true){

                    return true;
                
                }

                if($c->headquarter_id == $prcessMST->project->headquarter->id && $c->position->headquarter_look == true){

                    return true;
                }

                if($c->department_id == $prcessMST->project->department->id && $c->position->department_look == true){

                    return true;
                }

                if($c->group_id     == $prcessMST->project->group->id && $c->position->group_look == true){

                    return true;
                }
               

            }
        }

        return false;

    }


    public function view1(User $user)
    {
       $rule_action = Rule_action::where('rule_id',$user->rule)->where('action_id',config('constant.PROCESS_INDEX'))->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }


    /**
     * Determine whether the user can create process_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the process_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Process_MST  $processMST
     * @return mixed
     */
    public function update(User $user, Process_MST $processMST)
    {
        //
    }

    /**
     * Determine whether the user can delete the process_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Process_MST  $processMST
     * @return mixed
     */
    public function delete(User $user, Process_MST $processMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the process_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Process_MST  $processMST
     * @return mixed
     */
    public function restore(User $user, Process_MST $processMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the process_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Process_MST  $processMST
     * @return mixed
     */
    public function forceDelete(User $user, Process_MST $processMST)
    {
        //
    }
}
