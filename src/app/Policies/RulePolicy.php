<?php

namespace App\Policies;

use App\User;
use App\Rule_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class RulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the rule_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Rule_MST  $ruleMST
     * @return mixed
     */
    public function view(User $user, Rule_MST $ruleMST)
    {
        //
    }

    /**
     * Determine whether the user can create rule_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
       $rule_action = Rule_action::where('rule_id',$user->rule)->where('action_id',config('constant.RULE_ADD'))->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }

        //
    }

    /**
     * Determine whether the user can update the rule_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Rule_MST  $ruleMST
     * @return mixed
     */
    public function update(User $user)
    {
       $rule_action = Rule_action::where('rule_id',$user->rule)->where('action_id',config('constant.RULE_EDIT'))->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can delete the rule_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Rule_MST  $ruleMST
     * @return mixed
     */
    public function delete(User $user, Rule_MST $ruleMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the rule_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Rule_MST  $ruleMST
     * @return mixed
     */
    public function restore(User $user, Rule_MST $ruleMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the rule_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Rule_MST  $ruleMST
     * @return mixed
     */
    public function forceDelete(User $user, Rule_MST $ruleMST)
    {
        //
    }
}
