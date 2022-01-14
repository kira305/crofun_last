<?php

namespace App\Listeners;

use App\Events\HeadquarterChangeWithoutParent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Headquarters_MST;
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

class HeadquarterChangeWithoutParentListener
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
    public function handle(HeadquarterChangeWithoutParent $event)
    {
        DB::beginTransaction();
        try {
            $headquarter     = Headquarters_MST::where('id', $event->headquarter_id)->first();
            //ログ用の項目を作成
            $headquarter_name_log = $headquarter->headquarters . ' → 選択なし';
            $headquarter_code_log = $headquarter->headquarter_list_code . ' → 選択なし';

            //部署マスタに影響があるか。
            $Department_edit_data    =  Department_MST::where('headquarters_id', $headquarter->id)->where('status', true)->get();
            //ログ用の配列に設定をする。
            foreach ($Department_edit_data as $check) {
                $old_Rule_log[] = $check->department_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //部署マスタの更新
                Department_MST::where('headquarters_id', $headquarter->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(1)]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $headquarter->id, config('constant.DEPARTMENT'), config('constant.operation_OFF'), config('constant.HEADQUATER_EDIT'), $headquarter->company_id, $headquarter_name_log, $headquarter_code_log, json_encode($old_Rule_log), null);
            }

            //グループマスタに影響があるか。
            $department_id_list = DB::table('department_mst')->where('headquarters_id', $event->headquarter_id)
                ->pluck('id')->toArray();
            $Group_edit_data    =  Group_MST::whereIn('department_id', $department_id_list)->where('group_mst.status', true)->get();

            //ログ用の配列に設定をする。
            foreach ($Group_edit_data as $check) {
                $old_Rule_log[] = $check->group_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //グループマスタの更新
                Group_MST::whereIn('department_id', $department_id_list)
                    ->update(['status'   => false, 'updated_at'  => Crofun::getDate(3)]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $headquarter->id, config('constant.GROUP'), config('constant.operation_OFF'), config('constant.HEADQUATER_EDIT'), $headquarter->company_id, $headquarter_name_log, $headquarter_code_log, json_encode($old_Rule_log), null);
            }

            //プロジェクトマスタに影響があるか。
            $project_edit_data    =  Project_MST::where('headquarter_id', $headquarter->id)->where('status', true)->get();
            //配列の初期化
            $old_Rule_log = array();

            //ログ用の配列に設定をする。
            foreach ($project_edit_data as $project_data) {
                $old_Rule_log[] = $project_data->project_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //プロジェクトマスタの更新
                Project_MST::where('headquarter_id', $headquarter->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(4)]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $headquarter->id, config('constant.PROJECT'), config('constant.operation_OFF'), config('constant.HEADQUATER_EDIT'), $headquarter->company_id, $headquarter_name_log, $headquarter_code_log, json_encode($old_Rule_log), null);
            }

            //原価マスタに影響があるか。
            $cost_edit_data    =  Cost_MST::where('headquarter_id', $headquarter->id)->where('status', true)->get();
            //配列の初期化
            $old_Rule_log = array();
            //ログ用の配列に設定をする。
            foreach ($cost_edit_data as $cost_data) {
                $old_Rule_log[] = $cost_data->cost_name;
            }

            //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
            if (empty($old_Rule_log) == false) {
                //プロジェクトマスタの更新
                Cost_MST::where('headquarter_id', $headquarter->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(5)]);
                //ログの追加
                Crofun::log_create(Auth::user()->id, $headquarter->id, config('constant.COST'), config('constant.operation_OFF'), config('constant.HEADQUATER_EDIT'), $headquarter->company_id, $headquarter_name_log, $headquarter_code_log, json_encode($old_Rule_log), null);
            }

            // 契約
            $this->contractEditHeadquater($headquarter, $headquarter_name_log, $headquarter_code_log);
            DB::commit();
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            DB::rollBack();
            return view('500');
        }
    }
    private function contractEditHeadquater($headquarter, $headquarter_name_log, $headquarter_code_log)
    {
        $contractList    =  Contract_MST::where('headquarter_id', $headquarter->id)->where('status', true);
        $old_Rule_log = array();
        //ログ用の配列に設定をする。
        foreach ($contractList->get() as $contract) {
            $old_Rule_log[] = '申請本部非表示状態の契約ID: '.$contract->id;
        }

        //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
        if (empty($old_Rule_log) == false) {
            $contractList->update(['status'   => false]);
            //ログの追加
            Crofun::log_create(Auth::user()->id, $headquarter->id, config('constant.CONTRACT'), config('constant.operation_OFF'), config('constant.HEADQUATER_EDIT'), $headquarter->company_id, $headquarter_name_log, $headquarter_code_log, json_encode($old_Rule_log), null);
        }
    }
}
