<?php

namespace App\Policies;

use App\User;
use App\Cost_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class CostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the cost_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Cost_MST  $costMST
     * @return mixed
     */
    public function view(User $user, Cost_MST $costMST)
    {
        //
    }

    /**
     * Determine whether the user can create cost_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.COST_ADD'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can update the cost_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Cost_MST  $costMST
     * @return mixed
     */
    public function update(User $user)
    {
       $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.COST_EDIT'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can delete the cost_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Cost_MST  $costMST
     * @return mixed
     */
    public function delete(User $user, Cost_MST $costMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the cost_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Cost_MST  $costMST
     * @return mixed
     */
    public function restore(User $user, Cost_MST $costMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the cost_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Cost_MST  $costMST
     * @return mixed
     */
    public function forceDelete(User $user, Cost_MST $costMST)
    {
        //
    }
}
