<?php

namespace App\Listeners;

use App\Contract_MST;
use App\Events\DepartmentChangeWithoutParent;
use App\Department_MST;
use App\Group_MST;
use App\Project_MST;
use App\Cost_MST;
use DB;
use Crofun;
use Auth;

class DepartmentChangeWithoutParentListener
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
    public function handle(DepartmentChangeWithoutParent $event)
    {
        DB::beginTransaction();
        try {
            $department         =  Department_MST::where('id', $event->department_id)->first();
            //ログ用の項目を作成
            $department_name_log = $department->department_name . ' → 選択なし';
            $department_code_log = $department->department_list_code . ' → 選択なし';

            //グループマスタに影響があるか。
            $this->editGroupData($department, $department_name_log, $department_code_log);
            //プロジェクトマスタに影響があるか。
            $this->editGroupDssata($department, $department_name_log, $department_code_log);
            //原価マスタに影響があるか。
            $this->costEditData($department, $department_name_log, $department_code_log);

            $this->contractEditData($department, $department_name_log, $department_code_log);
            DB::commit();
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            DB::rollBack();
            return view('500');
        }
    }

    private function editGroupData($department, $department_name_log, $department_code_log)
    {
        $Group_edit_data    =  Group_MST::where('department_id', $department->id)->where('status', true)->get();
        //ログ用の配列に設定をする。
        foreach ($Group_edit_data as $check) {

            $old_Rule_log[] = $check->group_name;
        }


        //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
        if (empty($old_Rule_log) == false) {
            //グループマスタの更新
            Group_MST::where('department_id', $department->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(1)]);

            //ログの追加
            Crofun::log_create(Auth::user()->id, $department->id, config('constant.GROUP'), config('constant.operation_OFF'), config('constant.DEPARTMENT_EDIT'), $department->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
        }
        return true;
    }

    private function editGroupDssata($department, $department_name_log, $department_code_log)
    {
        $project_edit_data    =  Project_MST::where('department_id', $department->id)->where('status', true)->get();
        //配列の初期化
        $old_Rule_log = array();
        //ログ用の配列に設定をする。
        foreach ($project_edit_data as $project_data) {
            $old_Rule_log[] = $project_data->project_name;
        }

        //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
        if (empty($old_Rule_log) == false) {
            //プロジェクトマスタの更新
            Project_MST::where('department_id', $department->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(3)]);
            //ログの追加
            Crofun::log_create(Auth::user()->id, $department->id, config('constant.PROJECT'), config('constant.operation_OFF'), config('constant.DEPARTMENT_EDIT'), $department->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
        }
        return true;
    }

    private function costEditData($department, $department_name_log, $department_code_log){
        $cost_edit_data    =  Cost_MST::where('department_id', $department->id)->where('status', true)->get();
        //配列の初期化
        $old_Rule_log = array();

        //ログ用の配列に設定をする。
        foreach ($cost_edit_data as $cost_data) {
            $old_Rule_log[] = $cost_data->cost_name;
        }

        //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
        if (empty($old_Rule_log) == false) {
            //プロジェクトマスタの更新
            Cost_MST::where('department_id', $department->id)->update(['status'   => false, 'updated_at'  => Crofun::getDate(5)]);
            //ログの追加
            Crofun::log_create(Auth::user()->id, $department->id, config('constant.COST'), config('constant.operation_OFF'), config('constant.DEPARTMENT_EDIT'), $department->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
        }
        return true;
    }

    private function contractEditData($department, $department_name_log, $department_code_log){
        $contractList    =  Contract_MST::where('department_id', $department->id)->where('status', true);
        //配列の初期化
        $old_Rule_log = array();

        //ログ用の配列に設定をする。
        foreach ($contractList->get() as $contract) {
            $old_Rule_log[] = '申請部署非表示状態の契約ID: '.$contract->id;
        }

        //ログ用の配列に設定が有れば、ログにセット及び、更新を行う。
        if (empty($old_Rule_log) == false) {
            //プロジェクトマスタの更新
            $contractList->update(['status' => false]);
            //ログの追加
            Crofun::log_create(Auth::user()->id, $department->id, config('constant.CONTRACT'), config('constant.operation_OFF'), config('constant.DEPARTMENT_EDIT'), $department->headquarter()->company_id, $department_name_log, $department_code_log, json_encode($old_Rule_log), null);
        }
        return true;
    }
}
