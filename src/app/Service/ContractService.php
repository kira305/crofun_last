<?php

namespace App\Service;

use App\Contract_file;
use App\Contract_MST;
use App\Contract_progress;
use App\Customer_MST;
use App\Project_MST;
use App\Contract_type;
use App\Department_MST;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Crofun;
use Auth;

class ContractService
{
    //日付によって、ステータスをセット
    public function getContractProcess($contract)
    {
        if($contract->contract_canceled == true){
            return 11;
        }elseif($contract->update_finished == true){
            return 10;
        }
        $today = date('Y-m-d');
        $contractProgress['stamp_receipt_date'] = true;
        $contractProgress['stamped_return_date'] = !empty($contract->stamped_return_date) ? true : false;
        //日付を　Y-m-d　に変換
        $contractProgress['before_contract_starts'] = $this->compareBeforeContractStarts($contract->contract_start_date, $today);
        $contractProgress['contract_period_entered'] = $this->compareContractPeriodEntered($contract, $today);
        $contractProgress['within_confirm_period'] = $this->compareWithinConfirmPeriod($contract, $today);
        $contractProgress['after_contract_period'] = $this->compareAfterContractPeriod($contract->contract_end_date, $today);
        $contractProgress['over_confirm_deadline'] = $this->compareOverConfirmDeadline($contract, $today);
        $contractStatus =  Contract_progress::orderBy('id', 'desc');
        foreach($contractProgress as $key => $item){
            $contractStatus = $contractStatus->where($key, $item);
        }
        $contractStatus = $contractStatus->first();
        if (empty($contractStatus)) {
            return 0;
        }else {
            return $contractStatus->id;
        }
    }

    private function compareBeforeContractStarts($compareItem, $today){
        if(empty($compareItem)) return false;
        return $today < Carbon::parse($compareItem)->format('Y-m-d') ? true : false;
    }

    private function compareContractPeriodEntered($contract, $today){
        if(empty($contract->contract_start_date) || empty($contract->contract_end_date)) return false;
        return $today >= Carbon::parse($contract->contract_start_date)->format('Y-m-d') && $today <= Carbon::parse($contract->contract_end_date)->format('Y-m-d') ? true : false;
    }

    private function compareWithinConfirmPeriod($contract, $today){
        if(empty($contract->check_updates_deadline)) return false;
        return $today >= $this->getPre3MonthsFromCheckUpdatesDeadline($contract) && $today <= $contract->check_updates_deadline ? true : false;
    }

    private function getPre3MonthsFromCheckUpdatesDeadline($contract)
    {
        if(empty($contract->check_updates_deadline)) return null;
        $pre3Months = Carbon::parse($contract->check_updates_deadline)->subMonths(3)->format('Y-m-d');;
        return $pre3Months;
    }

    private function compareOverConfirmDeadline($contract, $today){
        if(empty($contract->check_updates_deadline) || empty($contract->contract_end_date)) return false;
        return $today > $contract->check_updates_deadline && $today <= Carbon::parse($contract->contract_end_date)->format('Y-m-d') ? true : false;
    }

    private function compareAfterContractPeriod($compareItem, $today){
        if(empty($compareItem)) return false;
        return $today > Carbon::parse($compareItem)->format('Y-m-d') ? true : false;
    }

    //ポスグレ用のフォーマットに変更　参照部署　プロジェクト一覧
    public function toPgArray($set)
    {
        if (empty($set)) return null;
        settype($set, 'array'); // can be called with a scalar or array
        $result = array();
        foreach ($set as $t) {
            //PHPの配列かどうか
            if (is_array($t)) {
                $result[] = $this->toPgArray($t);
            } else {
                $t = str_replace('"', '\\"', $t); // escape double quote
                if (!is_numeric($t)) // quote only non-numeric values
                    $t = '"' . $t . '"';
                $result[] = $t;
            }
        }
        return '{' . implode(",", $result) . '}'; // format
    }

    public static function pgArrayParse($literal)
    {
        if ($literal == '') return null;
        preg_match_all('/(?<=^\{|,)(([^,"{]*)|\s*"((?:[^"\\\\]|\\\\(?:.|[0-9]+|x[0-9a-f]+))*)"\s*)(,|(?<!^\{)(?=\}$))/i', $literal, $matches, PREG_SET_ORDER);
        $values = [];
        foreach ($matches as $match) {
            $temp = $match[3] != '' ? stripcslashes($match[3]) : (strtolower($match[2]) == 'null' ? null : $match[2]);
            $values[$temp] = $temp;
        }
        return $values;
    }

    //最大値+１
    public function createContractId($company_id)
    {
        return $this->getMaxIdContractByCompanyId($company_id) + 1;
    }

    //最大値の取得
    public function getMaxIdContractByCompanyId($company_id)
    {
        $contract = Contract_MST::where('company_id', $company_id)->orderBy('contract_id', 'desc')->first();
        return $contract->contract_id ?? 1;
    }

    //前の契約参照一覧
    public function getContractListForRef($contract)
    {
        return Contract_MST::where('client_id', $contract->client_id)->where('contract_type', $contract->contract_type)->whereNotIn('id', [$contract->id])->get();
    }

    //契約書のアップロード
    public function uploadFile($fileNoteArr, $contract, $action, $contractFiles = null)
    {
        //update note file
        if ($contractFiles != null) {
            $result = $this->updateNoteFile($fileNoteArr, $contractFiles);
            if (!$result) return false;
        }
        // create new file
        $files =  request()->file('contract_file');
        if ($files != null) {
            foreach ($files as $key => $file) {
                $fileType = $key == 1 ? 1 : 2;
                $resultSave = $this->saveContractFileData($file, $contract, $fileNoteArr[$key], $fileType, $action);
                if (!$resultSave) return false;
            }
        }
        return true;
    }

    public function updateNoteFile($fileNoteArr, $contractFiles)
    {
        $countRow = $contractFiles->count();
        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $countRow; $i++) {
                $result = $this->saveNote($fileNoteArr[$i], $contractFiles[$i - 1]);
                if (!$result) {
                    DB::rollback();
                    return false;
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function saveNote($note, $contractFile)
    {
        $contractFile->note = $note;
        if ($contractFile->update()) {
            return true;
        } else {
            return false;
        }
    }

    public function saveContractFileData($file, $contract, $note, $fileType, $action)
    {
        $fileEncryptionName = $this->makeFileNameForSave($file);
        $contractFile = new Contract_file();
        $contractFile->file_encryption_name = $fileEncryptionName;
        $contractFile->file_original_name = $file->getClientOriginalName();
        $contractFile->file_type = $fileType;
        $contractFile->contract_id = $contract->id;
        $contractFile->note = $note;
        DB::beginTransaction();
        try {
            if ($contractFile->save()) {
                if ($this->saveContractFile($file, $fileEncryptionName)) {
                    DB::commit();
                    Crofun::log_create(Auth::user()->id, $contractFile->id, config('constant.CONTRACT_FILE'), config('constant.operation_CRATE'), config('constant.'.$action), $contract->company_id, $contractFile->file_original_name, null, json_encode($contractFile), null);
                    return true;
                } else {
                    DB::rollback();
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    //ファイル自体の暗号化と格納
    public function saveContractFile($file, $fileEncryptionName)
    {
        //ファイルの内容を暗号化
        $encryptedContent = encrypt(File::get($file));
        return Storage::disk('local')->put('contract/' . $fileEncryptionName,  $encryptedContent);
    }

     //ファイル名を変更
    public function makeFileNameForSave($file)
    {
        $extension = $file->getClientOriginalExtension();
        $fn1 = strtotime("now");
        $fn2 = mt_rand(1, 99999);
        $fn3 = mt_rand(1, 99999);
        $fn4 = mt_rand(1, 99999);
        $fileEncryptionName = "contract" . $fn1 . $fn2 . $fn3 . $fn4 . '.' . $extension;
        return $fileEncryptionName;
    }

    public function deleteFile($fileId)
    {
        $contractFiles = Contract_file::where('id', $fileId)->first();
        $contractFiles->del_flg = true;
        return $contractFiles->update();
    }

    //参照している契約書データの契約書ファイルを取得
    public function getContractFileObj4Edit($contractId)
    {
        return Contract_file::where('contract_id', $contractId)->where('del_flg', false)->orderBy('file_type', 'asc')->orderBy('updated_at', 'asc')->get();
    }

    public function makeDataForEdit($contract)
    {
        $dataList = request()->all();
        unset($dataList['_token']);
        unset($dataList['post_act']);
        unset($dataList['update_time']);
        $fileNoteArr = $dataList['file_note'];
        unset($dataList['contract_file']);
        unset($dataList['file_note']);
        $dataList['project_id'] = isset($dataList['project_id']) ? $this->toPgArray($dataList['project_id']) : null;
        $dataList['referenceable_department'] = isset($dataList['referenceable_department']) ?  $this->toPgArray($dataList['referenceable_department']) : null;
        $dataList['check_updates_deadline'] = $this->parseMonthsToDate($dataList['check_updates_deadline'], $dataList['contract_end_date']);
        // $dataList['contract_completed'] = isset($dataList['contract_completed']) ? true : false;
        $dataList['contract_canceled'] = isset($dataList['contract_canceled']) ? true : false;
        $dataList['update_finished'] = isset($dataList['update_finished']) ? true : false;
        //日付を　Y-m-d　にセット
        if(!empty($dataList['stamp_receipt_date'])) $dataList['stamp_receipt_date'] =  Carbon::parse($dataList['stamp_receipt_date'])->format('Y-m-d');
        if(!empty($dataList['stamped_return_date'])) $dataList['stamped_return_date'] = Carbon::parse($dataList['stamped_return_date'])->format('Y-m-d');
        if(!empty($dataList['collection_date'])) $dataList['collection_date'] = Carbon::parse($dataList['collection_date'])->format('Y-m-d');
        if(!empty($dataList['contract_conclusion_date'])) $dataList['contract_conclusion_date'] = Carbon::parse($dataList['contract_conclusion_date'])->format('Y-m-d');
        if(!empty($dataList['contract_start_date'])) $dataList['contract_start_date'] = Carbon::parse($dataList['contract_start_date'])->format('Y-m-d');
        if(!empty($dataList['contract_end_date'])) $dataList['contract_end_date'] = Carbon::parse($dataList['contract_end_date'])->format('Y-m-d');
        foreach ($dataList as $key => $item) {
            $contract->{$key} = $item;
        }
        $contract->progress_status = $this->getContractProcess($contract);
        return array($contract, $fileNoteArr);
    }

    public function makeDataForView($contract)
    {
        $dataList = request()->all();
        unset($dataList['_token']);
        unset($dataList['post_act']);
        unset($dataList['update_time']);
        $dataList['project_id'] = isset($dataList['project_id']) ? $this->toPgArray($dataList['project_id']) : null;
        $dataList['referenceable_department'] = isset($dataList['referenceable_department']) ?  $this->toPgArray($dataList['referenceable_department']) : null;
        foreach ($dataList as $key => $item) {
            $contract->{$key} = $item;
        }

        return $contract;
    }

    public function parseMonthsToDate($months,$originDate)
    {
        if($months == null || $originDate == null) return null;
        return Carbon::parse($originDate)->subMonths($months)->format('Y-m-d');
    }

    //保存用データに可能する。
    public function makeDataForCreate($company_id)
    {
        $dataList = request()->all();
        if(isset($dataList['refid']) && !empty($dataList['refid'])){
            $dataList['pre_contract_id'] = $dataList['refid'];
        }
        unset($dataList['_token']);
        unset($dataList['refid']);
        $dataList['project_id'] = isset($dataList['project_id']) ? $this->toPgArray($dataList['project_id']) : null;
        $dataList['referenceable_department'] = isset($dataList['referenceable_department']) ? $this->toPgArray($dataList['referenceable_department']) : null;
        //日付情報を　Y-m-d　に変換
        $dataList['check_updates_deadline'] = $this->parseMonthsToDate($dataList['check_updates_deadline'], $dataList['contract_end_date']);
        if(!empty($dataList['stamp_receipt_date'])) $dataList['stamp_receipt_date'] =  Carbon::parse($dataList['stamp_receipt_date'])->format('Y-m-d');
        if(!empty($dataList['stamped_return_date'])) $dataList['stamped_return_date'] = Carbon::parse($dataList['stamped_return_date'])->format('Y-m-d');
        if(!empty($dataList['collection_date'])) $dataList['collection_date'] = Carbon::parse($dataList['collection_date'])->format('Y-m-d');
        if(!empty($dataList['contract_conclusion_date'])) $dataList['contract_conclusion_date'] = Carbon::parse($dataList['contract_conclusion_date'])->format('Y-m-d');
        if(!empty($dataList['contract_start_date'])) $dataList['contract_start_date'] = Carbon::parse($dataList['contract_start_date'])->format('Y-m-d');
        if(!empty($dataList['contract_end_date'])) $dataList['contract_end_date'] = Carbon::parse($dataList['contract_end_date'])->format('Y-m-d');
        $fileNoteArr = $dataList['file_note'];
        unset($dataList['file_note']);
        unset($dataList['contract_file']);
        $contract = new Contract_MST();
        foreach ($dataList as $key => $item) {
            $contract->{$key} = $item;
        }
        $contract->contract_id = $this->createContractId($company_id);
        $contract->progress_status = $this->getContractProcess($contract);
        return array($contract, $fileNoteArr);
    }

    // 再締結　前の契約書は、契約更新済　に変更。
    public function updateContractWhenRewind($id){
        $contract = Contract_MST::where('id', $id)->first();
        $originData = json_encode($contract);
        if(empty($contract)) return false;
        $contract->update_finished = true;
        $contract->progress_status = 10;
        if ($contract->update()) {
            Crofun::log_create(Auth::user()->id, $contract->id, config('constant.CONTRACT'), config('constant.operation_UPDATE'), config('constant.CONTRACT_EDIT'), $contract->company_id, null, $contract->application_num, json_encode($contract), $originData);
            return true;
        } else {
            return false;
        }
    }

    public function createSession($request)
    {
        session(['contractMst' => array('page' => $request->page)]);
        if (!empty($request->client_id) && (strpos(url()->previous(), 'customer/edit') !== false || strpos(url()->previous(), 'customer/view') !== false)){
            $customer    = Customer_MST::where('id', $request->client_id)->first();
            $client_code = $customer->client_code_main != null ? $customer->client_code_main : $customer->client_code;
            $dataSearch = array('client_code' => $client_code);
            session(['contract' => $dataSearch]);
            return $dataSearch;
        } elseif(!empty($request->project_id) && (strpos(url()->previous(), 'project/edit') !== false || strpos(url()->previous(), 'project/view') !== false)){
            $dataSearch = array('project_code' => Project_MST::where('id', $request->project_id)->first()->project_code);
            session(['contract' => $dataSearch]);
            return $dataSearch;
        } elseif(request()->method() == 'POST'){
            $dataSearch = $request->all();
            $dataSearch['client_name_kana'] = mb_convert_kana($dataSearch['client_name_kana'], 'rhk');
            unset($dataSearch['_token']);
            unset($dataSearch['project_id']);
            unset($dataSearch['client_id']);
            session(['contract' => $dataSearch]);
            return $dataSearch;
        }
        if (!$request->session()->exists('contract')) {
            $companyId = Auth::user()->company_id;
            session(['contract' => array()]);
            $dataSearch = array('company_id' => $companyId);
        }else {
            $dataSearch = session('contract');
        }
        return $dataSearch;
    }

    public function createSession4ContractType($request)
    {
        if(request()->method() == 'POST'){
            $dataSearch = $request->all();
            unset($dataSearch['_token']);
            if(isset($dataSearch['hidden'])){
                $dataSearch['hidden'] = 0;
            }
            session(['contract_type' => $dataSearch]);
            return $dataSearch;
        }
        if (!$request->session()->exists('contract_type')) {
            session(['contract_type' => array()]);
            $dataSearch = array();
        }else {
            $dataSearch = session('contract_type');
        }
        return $dataSearch;
    }

    public function searchContractType($dataSearch)
    {
        $contractType = Contract_type::orderBy('company_id', 'asc')->orderBy('hidden', 'asc')->orderBy('display_code', 'asc');
        //検索の条件が有れば、条件をｾｯﾄする
        if (isset($dataSearch['company_id'])) {
            $contractType = $contractType->where('company_id', $dataSearch['company_id']);
        }
        if (isset($dataSearch['hidden'])) {
            $contractType = $contractType->where('hidden', $dataSearch['hidden']);
        }
        //検索結果
        $contractType          = $contractType->paginate(20);
        return $contractType;
    }

    public function search($dataSearch)
    {

        $contract = Contract_MST::select('contract.*')
                    ->leftjoin('customer_mst', 'customer_mst.id', '=', 'contract.client_id')
                    ->leftjoin('contract_progress', 'contract_progress.id', '=', 'contract.progress_status')
                    ->orderBy('contract.progress_status', 'asc')
                    ->orderBy('contract.stamp_receipt_date', 'desc')
                    ->orderBy('contract.id', 'desc');

        //検索の条件が有れば、条件をｾｯﾄする
        if (!empty($dataSearch['company_id'])) {
            $contract = $contract->where('contract.company_id', $dataSearch['company_id']);
        }

        if (!empty($dataSearch['headquarter_id'])) {
            $contract = $contract->where('contract.headquarter_id', $dataSearch['headquarter_id']);
        }

        if (!empty($dataSearch['department_id'])) {
            $contract = $contract->where('contract.department_id', $dataSearch['department_id']);
        }

        if (!empty($dataSearch['group_id'])) {
            $contract = $contract->where('contract.group_id', $dataSearch['group_id']);
        }

        if (!empty($dataSearch['client_code'])) {
            $client_code = $dataSearch['client_code'];
            $contract = $contract->when($client_code != "", function ($query) use ($client_code) {
                return $query->where(function ($childQuery) use ($client_code) {
                    $childQuery->where('customer_mst.client_code', $client_code)
                        ->orWhere('customer_mst.client_code_main', $client_code);
                });
            });
        }

        if (!empty($dataSearch['client_name_kana'])) {
            $contract = $contract->where('customer_mst.client_name_kana','like', $dataSearch['client_name_kana']."%");
        }

        if (!empty($dataSearch['corporation_num'])) {
            $contract = $contract->where('customer_mst.corporation_num', $dataSearch['corporation_num']);
        }

        if (!empty($dataSearch['contract_type'])) {
            $contract = $contract->where('contract.contract_type', $dataSearch['contract_type']);
        }

        if (!empty($dataSearch['auto_update'])) {
            $contract = $contract->where('contract.auto_update', $dataSearch['auto_update']);
        }

        if (!empty($dataSearch['stamp_receipt_date_st'])) {
            $contract = $contract->where('contract.stamp_receipt_date', '>=',  $dataSearch['stamp_receipt_date_st']);
        }
        if (!empty($dataSearch['stamp_receipt_date_en'])) {
            $contract = $contract->where('contract.stamp_receipt_date', '<=',  $dataSearch['stamp_receipt_date_en']);
        }

        if (!empty($dataSearch['stamped_return_date_st'])) {
            $contract = $contract->where('contract.stamped_return_date', '>=',  $dataSearch['stamped_return_date_st']);
        }
        if (!empty($dataSearch['stamped_return_date_en'])) {
            $contract = $contract->where('contract.stamped_return_date', '<=',  $dataSearch['stamped_return_date_en']);
        }

        if (!empty($dataSearch['collection_date_st'])) {
            $contract = $contract->where('contract.collection_date', '>=',  $dataSearch['collection_date_st']);
        }
        if (!empty($dataSearch['collection_date_en'])) {
            $contract = $contract->where('contract.collection_date', '<=',  $dataSearch['collection_date_en']);
        }

        if (!empty($dataSearch['contract_conclusion_date_st'])) {
            $contract = $contract->where('contract.contract_conclusion_date', '>=',  $dataSearch['contract_conclusion_date_st']);
        }
        if (!empty($dataSearch['contract_conclusion_date_en'])) {
            $contract = $contract->where('contract.contract_conclusion_date', '<=',  $dataSearch['contract_conclusion_date_en']);
        }

        if (!empty($dataSearch['contract_start_date_st'])) {
            $contract = $contract->where('contract.contract_start_date', '>=',  $dataSearch['contract_start_date_st']);
        }
        if (!empty($dataSearch['contract_start_date_en'])) {
            $contract = $contract->where('contract.contract_start_date', '<=',  $dataSearch['contract_start_date_en']);
        }

        if (!empty($dataSearch['contract_end_date_st'])) {
            $contract = $contract->where('contract.contract_end_date', '>=',  $dataSearch['contract_end_date_st']);
        }
        if (!empty($dataSearch['contract_end_date_en'])) {
            $contract = $contract->where('contract.contract_end_date', '<=',  $dataSearch['contract_end_date_en']);
        }

        if (!empty($dataSearch['application_num'])) {
            $contract = $contract->where('contract.application_num', $dataSearch['application_num']);
        }

        if (!empty($dataSearch['progress_status'])) {
            $contract = $contract->where('contract_progress.status', $dataSearch['progress_status']);
        }

        if (!empty($dataSearch['project_code'])) {
            $projectId =  !empty($project = Project_MST::where('project_code', $dataSearch['project_code'])->first()) ? $project->id : null;
            if (!empty($projectId)){
                $contract = $contract->whereRaw($projectId." = ANY(project_id)");
            }else{
                $contract = $contract->where('project_id', '{0}');
            }
        }

        if (!empty($dataSearch['project_name'])) {
            $project_name = $dataSearch['project_name'];
            $projectId =  !empty($project = Project_MST::where('project_name', 'like',"%$project_name%")->first()) ? $project->id : null;
            if (!empty($projectId)){
                $contract = $contract->whereRaw($projectId." = ANY(project_id)");
            }else{
                $contract = $contract->where('project_id', '{0}');
            }
        }

        if (!empty($dataSearch['referenceable_department'])) {
            $contract = $contract->whereRaw($dataSearch['referenceable_department']." = ANY(referenceable_department)");
        }
        return $contract;
    }

    // 検索するときのcsv作成
    public function getContractData($request)
    {
        if($request->id == 'multi'){
            $datasearch = $this->createSession($request);
            $contracts = $this->search($datasearch);
            $contracts =$contracts->get();
        }else{
            $contracts = Contract_MST::where('id', $request->id)->get();
        }
        foreach ($contracts as $contract) {
            $contractArray = $this->changeFormatDatacontract($contract);
        }
        return $this->getDataForCreateCsv($contracts);
    }

    private function changeFormatDatacontract($contract){
        $contractObj = (object)Array();
        $contractObj->id = $contract->id;
        $contractObj->progress_status = $contract->progress_status;
        $contractObj->client_id = $contract->client_id;
        $contractObj->client_name = $contract->customer->client_name;
        $contractObj->project_id = $contract->project_id != null ?  $this->changeFormatProjectRef($contract->project_id) : '';
        $contractObj->referenceable_department = $contract->referenceable_department != null ? $this->changeFormatDepartmentRef($contract->referenceable_department) : '';
        $contractObj->contract_type = $contract->getContractTypeName();
        $contractObj->contract_completed = $contract->contract_completed == 2 ? '法務チェック完了' : '法務チェック不要';
        $contractObj->headquarter_id = $contract->headquarter->headquarters;
        $contractObj->department_id = $contract->department->department_name;
        $contractObj->group_id = $contract->group->group_name;
        $contractObj->application_user_name = $contract->application_user_name;
        $contractObj->application_num = $contract->application_num;
        $contractObj->stamp_receipt_date = $contract->stamp_receipt_date;
        $contractObj->stamped_return_date = $contract->stamped_return_date;
        $contractObj->collection_date = $contract->collection_date;
        $contractObj->contract_conclusion_date = $contract->contract_conclusion_date;
        $contractObj->contract_start_date = $contract->contract_start_date;
        $contractObj->contract_end_date = $contract->contract_end_date;
        $contractObj->check_updates_deadline = $contract->getCheckUpdatesDeadline() != null ? $contract->getCheckUpdatesDeadline().'ヶ月' : '';
        $contractObj->auto_update = $contract->auto_update == 'true' ? 'あり' : 'なし';
        $contractObj->contract_span = $contract->contract_span != null ? $contract->contract_span.'ヶ月' : '';
        $contractObj->update_log = $contract->update_log;
        $contractObj->note = $contract->note;
        $contractObj->contract_canceled = $contract->contract_canceled ? 'true' : 'false';
        $contractObj->update_finished = $contract->update_finished ? 'true' : 'false';
        return (array)$contractObj;
    }

    private function changeFormatProjectRef($projectIdArray){
        $projectsForgetCsv = '"';
        $projectIdList = array_keys($projectIdArray);
        $projects = Project_MST::whereIn('id', $projectIdList)->get();
        foreach($projects as $project){
            $projectsForgetCsv .= $project->project_code.':'.$project->project_name.",";
        }
        $projectsForgetCsv = rtrim($projectsForgetCsv, ',');
        $projectsForgetCsv .= '"';
        return $projectsForgetCsv;
    }

    private function changeFormatDepartmentRef($departmentIdArray){
        $departmentsForgetCsv = '"';
        $departmentIdList = array_keys($departmentIdArray);
        $departments = Department_MST::whereIn('id', $departmentIdList)->get();
        foreach($departments as $department){
            $departmentsForgetCsv .= $department->headquarter()->headquarters.':'.$department->department_name.",";
        }
        $departmentsForgetCsv = rtrim($departmentsForgetCsv, ',');
        $departmentsForgetCsv .= '"';
        return $departmentsForgetCsv;
    }

    // csv　ファイル作成
    public function getDataForCreateCsv($contracts)
    {
        $columns = array('契約書ID','進捗状況','顧客コード','顧客名','プロジェクト','参照可能部署','種類','法務チェック','申請本部','申請部署','申請Grp','申請者',
        '申請番号','押印受付日','押印返却日','回収日','契約締結日','契約開始日','契約終了日','更新の確認期限','自動更新','契約スパン','更新ログ','備考','契約中止','更新完了');
        echo "\xEF\xBB\xBF";
        //ファイルの作成
        $callback = function () use ($columns, $contracts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($contracts as $contract) {
                $contractArray = $this->changeFormatDatacontract($contract);
                fputcsv($file, $contractArray);
            }
            fclose($file);
        };
        return $callback;
    }

}
