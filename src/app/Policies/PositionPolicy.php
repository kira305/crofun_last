<?php

namespace App\Policies;

use App\User;
use App\Position_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the position_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Position_MST  $positionMST
     * @return mixed
     */
    public function view(User $user, Position_MST $positionMST)
    {

    }

    /**
     * Determine whether the user can create position_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.POSITION_ADD'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can update the position_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Position_MST  $positionMST
     * @return mixed
     */
    public function update(User $user)
    {
        $rule_action = Rule_action::where('rule_id',$user->rule)
                       ->where('action_id',config('constant.POSITION_EDIT'))
                       ->first();
       
       if($rule_action){

        return true;

       }else {

        return false;

       }
    }

    /**
     * Determine whether the user can delete the position_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Position_MST  $positionMST
     * @return mixed
     */
    public function delete(User $user, Position_MST $positionMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the position_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Position_MST  $positionMST
     * @return mixed
     */
    public function restore(User $user, Position_MST $positionMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the position_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Position_MST  $positionMST
     * @return mixed
     */
    public function forceDelete(User $user, Position_MST $positionMST)
    {
        //
    }
}
