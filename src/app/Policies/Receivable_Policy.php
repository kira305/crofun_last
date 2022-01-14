<?php

namespace App\Policies;

use App\User;
use App\Receivable_MST;
use App\Rule_action;
use Illuminate\Auth\Access\HandlesAuthorization;

class Receivable_Policy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the receivable_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Receivable_MST  $receivableMST
     * @return mixed
     */
    public function view(User $user)
    {
      
      $rule_action = Rule_action::where('rule_id',$user->rule)->where('action_id',config('constant.RSCEIVABLE_INDEX'))->first();
       

       if($rule_action){

        return true;

       }else {

        return false;

       }


        //
    }

    /**
     * Determine whether the user can create receivable_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the receivable_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Receivable_MST  $receivableMST
     * @return mixed
     */
    public function update(User $user, Receivable_MST $receivableMST)
    {
        //
    }

    /**
     * Determine whether the user can delete the receivable_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Receivable_MST  $receivableMST
     * @return mixed
     */
    public function delete(User $user, Receivable_MST $receivableMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the receivable_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Receivable_MST  $receivableMST
     * @return mixed
     */
    public function restore(User $user, Receivable_MST $receivableMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the receivable_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Receivable_MST  $receivableMST
     * @return mixed
     */
    public function forceDelete(User $user, Receivable_MST $receivableMST)
    {
        //
    }
}
