<?php

namespace App\Policies;

use App\User;
use App\Credit_MST;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Rule_action;

class CreditPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the credit_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Credit_MST  $creditMST
     * @return mixed
     */
    public function view(User $user, Credit_MST $creditMST)
    {
        //
    }

    /**
     * Determine whether the user can create credit_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the credit_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Credit_MST  $creditMST
     * @return mixed
     */
    public function update(User $user, Credit_MST $creditMST)
    {
        //
    }

    /**
     * Determine whether the user can delete the credit_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Credit_MST  $creditMST
     * @return mixed
     */
    public function delete(User $user, Credit_MST $creditMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the credit_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Credit_MST  $creditMST
     * @return mixed
     */
    public function restore(User $user, Credit_MST $creditMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the credit_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Credit_MST  $creditMST
     * @return mixed
     */
    public function forceDelete(User $user, Credit_MST $creditMST)
    {
        //
    }

    public function log(User $user)
    {
           $rule_action = Rule_action::where('rule_id',$user->rule)->where('action_id',config('constant.CREDIT_LOG'))->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }    }

}
