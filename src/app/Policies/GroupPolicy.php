<?php

namespace App\Policies;

use App\User;
use App\Group_MST;
use App\Rule_action;
use App\Concurrently;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the group_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Group_MST  $groupMST
     * @return mixed
     */
    public function view(User $user, Group_MST $groupMST)
    {
        $rule_action = Rule_action::where('rule_id', $user->rule)
            ->where('action_id', config('constant.PROCESS_INDEX'))
            ->first();

        if ($rule_action) {


            /*管理者フラグなら全部OK*/
            if ($user->getrole->admin_flag == 1) {

                return true;
            }

            if ($user->company_id == $groupMST->headquarter()->company_id && $user->position->company_look == true) {

                return true;
            }

            /*ルールマスタを見て参照範囲の確認*/
            if ($user->headquarter_id == $groupMST->headquarter()->id && $user->position->headquarter_look == true) {

                return true;
            }

            if ($user->department_id == $groupMST->department()->id && $user->position->department_look == true) {

                return true;
            }

            if ($user->group_id == $groupMST->id && $user->position->group_look == true) {

                return true;
            }

            $concurently = Concurrently::where('usr_id', $user->id)->where('status', true)->get();

            foreach ($concurently as $c) {

                if ($c->company_id ==  $groupMST->headquarter()->company_id && $user->position->company_look == true) {

                    return true;
                }

                if ($c->headquarter_id ==  $groupMST->headquarter()->company_id && $c->position->headquarter_look == true) {

                    return true;
                }

                if ($c->department_id == $groupMST->department()->id && $c->position->department_look == true) {

                    return true;
                }

                if ($c->group_id     == $groupMST->id && $c->position->group_look == true) {

                    return true;
                }
            }
        }


        return false;
    }



    /**
     * Determine whether the user can create group_ m s ts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $rule_action = Rule_action::where('rule_id', $user->rule)->where('action_id', config('constant.GROUP_ADD'))->first();

        if ($rule_action) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * Determine whether the user can update the group_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Group_MST  $groupMST
     * @return mixed
     */
    public function update(User $user)
    {
        $rule_action = Rule_action::where('rule_id', $user->rule)->where('action_id', config('constant.GROUP_EDIT'))->first();

        if ($rule_action) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * Determine whether the user can delete the group_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Group_MST  $groupMST
     * @return mixed
     */
    public function delete(User $user, Group_MST $groupMST)
    {
        //
    }

    /**
     * Determine whether the user can restore the group_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Group_MST  $groupMST
     * @return mixed
     */
    public function restore(User $user, Group_MST $groupMST)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the group_ m s t.
     *
     * @param  \App\User  $user
     * @param  \App\Group_MST  $groupMST
     * @return mixed
     */
    public function forceDelete(User $user, Group_MST $groupMST)
    {
        //
    }
}
