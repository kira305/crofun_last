<?php

namespace App\Policies;

use App\User;
use App\Headquarters_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class HeadquarterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the headquarters_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Headquarters_MST  $headquartersMST
     * @return mixed
     */
    public function view(User $user, Headquarters_MST $headquartersMST)
    {
        //
    }

    /**
     * Determine whether the user can create headquarters_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.HEADQUATER_ADD'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }

    }

    /**
     * Determine whether the user can update the headquarters_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Headquarters_MST  $headquartersMST
     * @return mixed
     */
    public function update(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.HEADQUATER_EDIT'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }

    }

    /**
     * Determine whether the user can delete the headquarters_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Headquarters_MST  $headquartersMST
     * @return mixed
     */
    public function delete(User $user, Headquarters_MST $headquartersMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the headquarters_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Headquarters_MST  $headquartersMST
     * @return mixed
     */
    public function restore(User $user, Headquarters_MST $headquartersMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the headquarters_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Headquarters_MST  $headquartersMST
     * @return mixed
     */
    public function forceDelete(User $user, Headquarters_MST $headquartersMST)
    {
        //
    }
}
