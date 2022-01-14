<?php

namespace App\Policies;

use App\User;
use App\Department_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the department_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Department_MST  $departmentMST
     * @return mixed
     */
    public function view(User $user, Department_MST $departmentMST)
    {
        //
    }

    /**
     * Determine whether the user can create department_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.DEPARTMENT_ADD'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }

    }

    /**
     * Determine whether the user can update the department_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Department_MST  $departmentMST
     * @return mixed
     */
    public function update(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.DEPARTMENT_EDIT'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can delete the department_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Department_MST  $departmentMST
     * @return mixed
     */
    public function delete(User $user, Department_MST $departmentMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the department_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Department_MST  $departmentMST
     * @return mixed
     */
    public function restore(User $user, Department_MST $departmentMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the department_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Department_MST  $departmentMST
     * @return mixed
     */
    public function forceDelete(User $user, Department_MST $departmentMST)
    {
        //
    }
}
