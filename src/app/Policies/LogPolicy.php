<?php

namespace App\Policies;

use App\User;
use App\Log_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the log_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Log_MST  $logMST
     * @return mixed
     */
    public function index(User $user)
    {
       $rule_action = Rule_action::where('rule_id',$user->rule)->where('action_id',config('constant.LOG_INDEX'))->first();
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    public function view(User $user)
    {
       $rule_action = Rule_action::where('rule_id',$user->rule)->where('action_id',config('constant.LOG_VIEW'))->first();
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can create log_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the log_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Log_MST  $logMST
     * @return mixed
     */
    public function update(User $user, Log_MST $logMST)
    {
        //
    }

    /**
     * Determine whether the user can delete the log_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Log_MST  $logMST
     * @return mixed
     */
    public function delete(User $user, Log_MST $logMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the log_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Log_MST  $logMST
     * @return mixed
     */
    public function restore(User $user, Log_MST $logMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the log_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Log_MST  $logMST
     * @return mixed
     */
    public function forceDelete(User $user, Log_MST $logMST)
    {
        //
    }
}
