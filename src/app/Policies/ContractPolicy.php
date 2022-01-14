<?php

namespace App\Policies;

use App\Common\Crofun;
use App\User;
use App\Contract_MST;
use App\Rule_action;
use App\Concurrently;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the contract_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Contract_MST  $contractMST
     * @return mixed
     */
    public function view(User $user)
    {
        $rule_action = Rule_action::where('rule_id', $user->rule)->where('action_id', config('constant.CONTRACT_INDEX'))->first();

        if ($rule_action) {

            return true;
        } else {

            return false;
        }
    }

    public function display(User $user)
    {
        $rule_action = Rule_action::where('rule_id', $user->rule)
            ->where('action_id', config('constant.CONTRACT_VIEW'))
            ->orwhere('action_id', config('constant.CONTRACT_EDIT'))
            ->first();
        if ($rule_action) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can create contract_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id', $user->rule)
            ->where('action_id', config('constant.CONTRACT_CREATE'))
            ->first();
        if ($rule_action) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the contract_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Contract_MST  $contractMST
     * @return mixed
     */
    public function update(User $user)
    {
        $rule_action = Rule_action::where('rule_id', $user->rule)
            ->where('action_id', config('constant.CONTRACT_EDIT'))
            ->first();
        if ($rule_action) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the contract_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Contract_MST  $contractMST
     * @return mixed
     */
    public function delete(User $user, Contract_MST $contractMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the contract_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Contract_MST  $contractMST
     * @return mixed
     */
    public function restore(User $user, Contract_MST $contractMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the contract_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Contract_MST  $contractMST
     * @return mixed
     */
    public function forceDelete(User $user, Contract_MST $contractMST)
    {
        //
    }
}
