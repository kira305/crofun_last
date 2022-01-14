<?php

namespace App\Policies;

use App\User;
use App\Company_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the company_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Company_MST  $companyMST
     * @return mixed
     */
    public function view(User $user, Company_MST $companyMST)
    {
        //
    }

    /**
     * Determine whether the user can create company_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.COMPANY_ADD'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can update the company_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Company_MST  $companyMST
     * @return mixed
     */
    public function update(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.COMPANY_EDIT'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can delete the company_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Company_MST  $companyMST
     * @return mixed
     */
    public function delete(User $user, Company_MST $companyMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the company_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Company_MST  $companyMST
     * @return mixed
     */
    public function restore(User $user, Company_MST $companyMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the company_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Company_MST  $companyMST
     * @return mixed
     */
    public function forceDelete(User $user, Company_MST $companyMST)
    {
        //
    }
}
