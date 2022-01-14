<?php

namespace App\Listeners;

use App\Events\DepartmentChangeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Department_MST;
use App\Group_MST;
use App\Project_MST;
use App\User;
use App\Events\GroupChangeEvent;
use App\Diagram;
use App\Concurrently;
use App\Contract_MST;
use App\Cost_MST;
use Log;
use DB;
use Auth;
use Crofun;

class GroupEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LogEvent  $event
     * @return void
     */
    public function handle(GroupChangeEvent $event)
    {
        DB::beginTransaction();
        try {

            $group              = Group_MST::where('id', $event->old_group_id)->first();
            $group_new          = Group_MST::where('id', $event->new_group_id)->first();
            $headquarter_new_id = $group_new->department()->headquarter()->id;
            $department_new_id  = $group_new->department()->id;

            //ログ用の項目を作成
            $group_name_log = $group->group_name . ' → ' . $group_new->group_name;
            $group_code_log = $group->group_list_code . ' → ' . $group_new->group_list_code;

            //部署
            $department_name_log = $group->department()->department_name . ' → ' . $group_new->department()->department_name;
            $department_code_log = $group->department()->department_list_code . ' → ' . $group_new->department()->department_list_code;

            //本部
            $headquarters_name_log = $group->headquarter()->headquarters . ' → ' . $group_new->headquarter()->headquarters;
            $headquarters_code_log = $group->headquarter()->headquarter_list_code . ' → ' . $group_new->headquarter()->headquarter_list_code;

            //ユーザーマスタに影響があるか。
            $user_edit_data    =  User::where('group_id', $group->id)->where('retire', false)->get();
            //配列の初期化
            $old_Rule_log      = array();

            //ログ用の配列に設定をする。
            foreach ($user_edit_data as $user_data) {
                $old_Rule_log[] = $user_data->usr_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //ユーザーマスタの更新
                User::where('group_id', $group->id)
                    ->where('retire', false)
                    ->update(['group_id'      => $group_new->id]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.USER'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);
                //ユーザー部署更新は必要か
                if ($group->department()->id !=  $group_new->department()->id) {
                    //ユーザー部署情報更新
                    User::where('group_id', $group_new->id)
                        ->where('retire', false)
                        ->update(['department_id' => $department_new_id]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.USER'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
                }
                //ユーザー本部の更新は必要か
                if ($group->headquarter()->id !=  $group_new->headquarter()->id) {
                    //ユーザー本部情報更新
                    User::where('group_id', $group_new->id)
                        ->where('retire', false)
                        ->update(['headquarter_id' => $headquarter_new_id]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.USER'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $headquarters_name_log, $headquarters_code_log, json_encode($old_Rule_log), null);
                }
            }

            //兼務マスタに影響があるか。
            $concurrently_edit_data    =  Concurrently::where('group_id', $group->id)->where('status', true)->get();
            //配列の初期化
            $old_Rule_log = array();
            //ログ用の配列に設定をする。
            foreach ($concurrently_edit_data as $concurrently_data) {
                $old_Rule_log[] = $concurrently_data->usr_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //兼務マスタの更新
                Concurrently::where('group_id', $group->id)->where('status', true)->update(['group_id' => $group_new->id]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.CONCURRENTLY'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);

                //兼務の部署の更新は必要か
                if ($group->department()->id !=  $group_new->department()->id) {
                    //兼務の部署情報更新
                    Concurrently::where('group_id', $group_new->id)->where('status', true)->update(['department_id'   => $department_new_id]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.CONCURRENTLY'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
                }

                //兼務の本部の更新は必要か
                if ($group->headquarter()->id !=  $group_new->headquarter()->id) {
                    //兼務の本部情報更新
                    Concurrently::where('group_id', $group_new->id)->where('status', true)->update(['headquarter_id'   => $headquarter_new_id]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.CONCURRENTLY'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $headquarters_name_log, $headquarters_code_log, json_encode($old_Rule_log), null);
                }
            }

            //プロジェクトマスタに影響があるか。
            $project_edit_data    =  Project_MST::where('group_id', $group->id)->where('status', true)->get();
            //配列の初期化
            $old_Rule_log = array();

            //ログ用の配列に設定をする。
            foreach ($project_edit_data as $project_data) {
                $old_Rule_log[] = $project_data->project_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //プロジェクトマスタの更新
                Project_MST::where('group_id', $group->id)->where('status', true)->update(['group_id'  => $group_new->id, 'updated_at'  => Crofun::getDate(1)]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.PROJECT'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);


                //兼務の部署の更新は必要か
                if ($group->department()->id !=  $group_new->department()->id) {
                    //プロジェクトの部署情報更新
                    Project_MST::where('group_id', $group_new->id)->where('status', true)->update(['department_id' => $department_new_id, 'updated_at'  => Crofun::getDate(1)]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.PROJECT'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
                }

                //兼務の本部の更新は必要か
                if ($group->headquarter()->id !=  $group_new->headquarter()->id) {
                    //プロジェクトの本部情報更新
                    Project_MST::where('group_id', $group_new->id)->where('status', true)->update(['headquarter_id' => $headquarter_new_id, 'updated_at'  => Crofun::getDate(3)]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.PROJECT'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $headquarters_name_log, $headquarters_code_log, json_encode($old_Rule_log), null);
                }
            }

            //原価マスタに影響があるか。
            $cost_edit_data    =  Cost_MST::where('group_id', $group->id)->where('status', true)->get();
            //配列の初期化
            $old_Rule_log = array();
            //ログ用の配列に設定をする。
            foreach ($cost_edit_data as $cost_data) {
                $old_Rule_log[] = $cost_data->cost_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //プロジェクトマスタの更新
                Cost_MST::where('group_id', $group->id)->where('status', true)->update(['group_id' => $group_new->id, 'updated_at'  => Crofun::getDate(5)]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.COST'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);
                //兼務の本部の更新は必要か
                if ($group->department()->id !=  $group_new->department()->id) {
                    //コストマスタの更新
                    Cost_MST::where('group_id', $group_new->id)->where('status', true)->update(['department_id'  => $department_new_id, 'updated_at'  => Crofun::getDate(5)]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.COST'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
                }
                //コストの本部の更新は必要か
                if ($group->headquarter()->id !=  $group_new->headquarter()->id) {
                    //プロジェクトの本部情報更新
                    Cost_MST::where('group_id', $group_new->id)->where('status', true)->update(['headquarter_id' => $headquarter_new_id, 'updated_at'  => Crofun::getDate(5)]);
                    //ログの追加
                    Crofun::log_create(Auth::user()->id, $group->id, config('constant.COST'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $headquarters_name_log, $headquarters_code_log, json_encode($old_Rule_log), null);
                }
            }

            // 契約
            $this->contractEditGroup($group, $group_new, $department_new_id, $headquarter_new_id, $headquarters_name_log, $headquarters_code_log, $group_name_log, $group_code_log, $department_name_log, $department_code_log);
            DB::commit();
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            DB::rollBack();
            return view('500');
        }
    }

    private function contractEditGroup($group, $group_new, $department_new_id, $headquarter_new_id, $headquarters_name_log, $headquarters_code_log, $group_name_log, $group_code_log, $department_name_log, $department_code_log)
    {
        $contractList    =  Contract_MST::where('group_id', $group->id)->where('status', true);
        $old_Rule_log = array();
        //ログ用の配列に設定をする。
        foreach ($contractList->get() as $contract) {
            $old_Rule_log[] = '申請グループ更新済みの契約ID: ' . $contract->id;
        }

        //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
        if (empty($old_Rule_log) == false) {
            $contractList->update(['group_id' => $group_new->id]);
            //ログの追加
            Crofun::log_create(Auth::user()->id, $group->id, config('constant.CONTRACT'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);
            //兼務の本部の更新は必要か
            if ($group->department()->id !=  $department_new_id) {
                //コストマスタの更新
                Contract_MST::where('group_id', $group_new->id)->where('status', true)->update(['department_id'  => $department_new_id]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.CONTRACT'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
            }
            //コストの本部の更新は必要か
            if ($group->department()->headquarter()->id !=  $headquarter_new_id) {
                //プロジェクトの本部情報更新
                Contract_MST::where('group_id', $group_new->id)->where('status', true)->update(['headquarter_id' => $headquarter_new_id]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.CONTRACT'), config('constant.operation_Bulk_up'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $headquarters_name_log, $headquarters_code_log, json_encode($old_Rule_log), null);
            }
        }
        return true;
    }
}
