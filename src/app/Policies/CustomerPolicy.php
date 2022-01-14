<?php

namespace App\Policies;

use App\User;
use App\Customer_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the customer_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Customer_MST  $customerMST
     * @return mixed
     */
    public function view(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.CLIENT_VIEW'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
       //  $rule_action = Rule_action::where('rule_id',$user->rule)
       //                 ->where('action_id',config('constant.CLIENT_VIEW'))
       //                 ->first();
       
       // if($rule_action){

       //  return true;

       // }else {

       //  return false;

       // }
      
    }
    /**
     * Determine whether the user can create customer_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.CLIENT_ADD'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
      
    }

    /**
     * Determine whether the user can update the customer_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Customer_MST  $customerMST
     * @return mixed
     */
    public function update(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.CLIENT_EDIT'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can delete the customer_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Customer_MST  $customerMST
     * @return mixed
     */
    public function delete(User $user, Customer_MST $customerMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the customer_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Customer_MST  $customerMST
     * @return mixed
     */
    public function restore(User $user, Customer_MST $customerMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the customer_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Customer_MST  $customerMST
     * @return mixed
     */
    public function forceDelete(User $user, Customer_MST $customerMST)
    {
        //
    }
}
