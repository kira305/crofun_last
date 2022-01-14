<?php

namespace App\Listeners;

use App\Events\GroupChangeWithoutParent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Department_MST;
use App\Group_MST;
use App\Project_MST;
use App\User;
use App\Concurrently;
use App\Contract_MST;
use App\Diagram;
use App\Cost_MST;
use Log;
use DB;
use Crofun;
use Auth;

class GroupChangeWithoutParentListener
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
    /*移行先を選択しなかった場合のログ*/
    public function handle(GroupChangeWithoutParent $event)
    {
        DB::beginTransaction();
        try {
            $group              = Group_MST::where('id', $event->group_id)->first();
            //ログ用の項目を作成
            $group_name_log = $group->group_name . ' → 選択なし';
            $group_code_log = $group->group_list_code . ' → 選択なし';

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
                Project_MST::where('group_id', $group->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(1)]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.PROJECT'), config('constant.operation_OFF'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);
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
                //ツリー図マスタの更新
                Cost_MST::where('group_id', $group->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(3)]);

                //ログの追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.COST'), config('constant.operation_OFF'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);
            }

            // 契約
            $this->contractEditGroup($group, $group_name_log, $group_code_log);

            DB::commit();
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            DB::rollBack();
            return view('500');
        }
    }

    private function contractEditGroup($group, $group_name_log, $group_code_log){
        $contractList    =  Contract_MST::where('group_id', $group->id)->where('status', true);
        //配列の初期化
        $old_Rule_log = array();

        //ログ用の配列に設定をする。
        foreach ($contractList->get() as $contract) {
            $old_Rule_log[] = '申請グループ非表示状態の契約ID: '.$contract->id;
        }

        //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
        if (empty($old_Rule_log) == false) {
            //プロジェクトマスタの更新
            $contractList->update(['status'=> false]);
            //ログの追加
            Crofun::log_create(Auth::user()->id, $group->id, config('constant.CONTRACT'), config('constant.operation_OFF'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group_name_log, $group_code_log, json_encode($old_Rule_log), null);
        }

        return true;
    }
}
