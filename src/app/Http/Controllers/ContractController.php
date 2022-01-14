<?php

namespace App\Http\Controllers;

use App\Contract_file;
use Illuminate\Http\Request;
use App\Customer_MST;
use App\Contract_MST;
use App\Contract_progress;
use App\Contract_type;
use App\Department_MST;
use App\Http\Requests\ContractRequest;
use App\Rules\CompareUpdateTime;
use Auth;
use App\Service\ContractService;
use DB;
use Crofun;
use Common;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{

    private $contractService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->contractService = new ContractService();
    }

    public function index(Request $request)
    {
        $validator = $this->validationDataInput($request);
        $errors = null;
        if ($validator->fails()) {
            $errors = $validator->errors();
            $arrayError = $errors->toArray();
            foreach ($arrayError as $key => $item) {
                $request->offsetUnset($key);
            }
        }
        $contractTypes = Contract_type::where("hidden", 0)->orderBy('display_code', 'asc')->get();
        $departmentsRef    = Department_MST::get();
        $progressStatusList = Contract_progress::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id) As id'))->from('contract_progress')->groupBy('status');
        })->get(['status']);
        $datasearch = $this->contractService->createSession($request);
        $contract = $this->contractService->search($datasearch);
        //検索結果
        $contract          = $contract->paginate(20);

        if($errors == null){
            $compact = compact('contractTypes', 'departmentsRef', 'progressStatusList', 'contract');
        }else {
            $compact = compact('contractTypes', 'departmentsRef', 'progressStatusList', 'contract', 'errors');
        }
        return view('contract.index', $compact);
    }
    public function validationDataInput(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stamp_receipt_date_st'        => 'nullable|date_format:Y/m/d',
            'stamp_receipt_date_en'        => 'nullable|date_format:Y/m/d',
            'stamped_return_date_st'        => 'nullable|date_format:Y/m/d',
            'stamped_return_date_en'        => 'nullable|date_format:Y/m/d',
            'collection_date_st'        => 'nullable|date_format:Y/m/d',
            'collection_date_en'        => 'nullable|date_format:Y/m/d',
            'contract_conclusion_date_st'        => 'nullable|date_format:Y/m/d',
            'contract_conclusion_date_en'        => 'nullable|date_format:Y/m/d',
            'contract_start_date_st'        => 'nullable|date_format:Y/m/d',
            'contract_start_date_en'        => 'nullable|date_format:Y/m/d',
            'contract_end_date_st'        => 'nullable|date_format:Y/m/d',
            'contract_end_date_en'        => 'nullable|date_format:Y/m/d',
        ], [
            'stamp_receipt_date_st.date_format'       => trans('validation.import_log_start_time'),
            'stamp_receipt_date_en.date_format'       => trans('validation.import_log_start_time'),
            'stamped_return_date_st.date_format'       => trans('validation.import_log_start_time'),
            'stamped_return_date_en.date_format'       => trans('validation.import_log_start_time'),
            'collection_date_st.date_format'       => trans('validation.import_log_start_time'),
            'collection_date_en.date_format'       => trans('validation.import_log_start_time'),
            'contract_conclusion_date_st.date_format'       => trans('validation.import_log_start_time'),
            'contract_conclusion_date_en.date_format'       => trans('validation.import_log_start_time'),
            'contract_start_date_st.date_format'       => trans('validation.import_log_start_time'),
            'contract_start_date_en.date_format'       => trans('validation.import_log_start_time'),
            'contract_end_date_st.date_format'       => trans('validation.import_log_start_time'),
            'contract_end_date_en.date_format'       => trans('validation.import_log_start_time'),
        ]);
        $errors = $validator->errors();
        return $validator;
    }

    // pdf表す画面を表示
    public function display(Request $request)
    {
        $contract = Contract_MST::where('id', $request->contract_id)->first();
        $contractFile = Contract_file::where('id', $request->contract_file_id)->first();
        Crofun::log_create(Auth::user()->id, $contractFile->id, config('constant.CONTRACT_FILE'), config('constant.operation_REFERENCE'), config('constant.CONTRACT_EDIT'), $contract->company_id, $contractFile->file_original_name, null, null, null);
        $file_id = $request->contract_file_id;
        return view('contract.display', ['contract_file_id' => $file_id]);
    }

    // pdfファイルの内容
    public function viewfile(Request $request)
    {
        $contract_file_id = $request->contract_file_id;
        $contractFile    = Contract_file::where('id', $contract_file_id)->first();
        $encryptedContent = Storage::disk('local')->get('contract/' . $contractFile->file_encryption_name);
        $decryptedContent = decrypt($encryptedContent);

        $response = response()->make($decryptedContent, 200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }

    public function create(ContractRequest $request)
    {
        $departments    = Department_MST::orderBy('department_code', 'asc')->where('status', true)->get();
        $customer = Customer_MST::where('id', $request->client_id)->first();
        $contractTypes = Contract_type::where("hidden", 0)->where("company_id", $customer->company_id)->orderBy('display_code', 'asc')->get();
        $compact = compact('contractTypes', 'departments', 'customer');
        if (isset($request->refid)) {
            $contract = Contract_MST::where('id', $request->refid)->first();
            if (empty($contract)) {
                abort(404);
            } else {
                if (strpos(url()->previous(), 'contract/edit') !== false) {
                    $compact = compact('contractTypes', 'departments', 'customer', 'contract');
                }
            }
        }
        if ($request->isMethod('post')) {
            //保存用データの作成
            list($contract, $fileNoteArr) = $this->contractService->makeDataForCreate($customer->company_id);

            //create contract data
            if ($contract->save()) { {
                    $newContract = Contract_MST::where('company_id', $contract->company_id)->where('contract_id', $contract->contract_id)->first();
                    $newContractArr =  $newContract->toArray();
                    //アラートを表示する月を取得
                    $newContractArr['check_updates_deadline'] = $newContract->getCheckUpdatesDeadline();
                    request()->session()->flash('contract_create', $newContractArr);
                    Crofun::log_create(Auth::user()->id, $newContract->id, config('constant.CONTRACT'), config('constant.operation_CRATE'), config('constant.CONTRACT_CREATE'), $newContract->company_id, null, $newContract->application_num, json_encode($newContract), null);
                    // 契約書のアップロード
                    $uploadResult = $this->contractService->uploadFile($fileNoteArr, $contract, 'CONTRACT_CREATE');
                    //参照している契約書データの契約書を取得
                    $compact['contractFiles'] = $this->contractService->getContractFileObj4Edit($newContract->id);
                    $compact['newContract'] = $newContract;
                    if (!$uploadResult) {
                        return view('contract.create', $compact)->with('message', trans('message.save_fail'));
                    }
                    //巻きなおし
                    if (isset($request->refid))
                        $this->contractService->updateContractWhenRewind($request->refid);
                }

                return view('contract.create', $compact)->with('message', trans('message.create_success'));
            } else {
                return view('contract.create', $compact)->with('message', trans('message.save_fail'));
            }
        }

        return view('contract.create', $compact);
    }

    public function edit(ContractRequest $request)
    {
        $contract = Contract_MST::where('id', $request->id)->first();
        session(['contractId' => $contract->id]);
        $departmentsRef    = Department_MST::orderBy('department_code', 'asc');
        $departmentsRef = $contract->status ? $departmentsRef->where('status', true)->get() : $departmentsRef->get();
        $originData = json_encode($contract);
        $contractFiles = $this->contractService->getContractFileObj4Edit($contract->id);
        $customer = Customer_MST::where('id', $contract->client_id)->first();
        $contractTypes = Contract_type::where("hidden", 0)->where("company_id", $customer->company_id)->orderBy('display_code', 'asc')->get();
        $contractList4Ref = $this->contractService->getContractListForRef($contract);
        $updateTime = session()->has('update_time_session') ?  session('update_time_session') : $contract->updated_at;
        $compact = compact('contractFiles', 'contractTypes', 'customer', 'contract', 'departmentsRef', 'contractList4Ref', 'updateTime');
        if ($request->isMethod('post')) {
            //get data for save
            list($contract, $fileNoteArr) = $this->contractService->makeDataForEdit($contract);
            // delete file
            if (isset($request->file_delete)) {
                $result = $this->contractService->deleteFile($request->file_delete);
                if ($result) {
                    $compact['contractFiles'] = $this->contractService->getContractFileObj4Edit($contract->id);
                    $compact['contract'] = Contract_MST::where('id', $request->id)->first();
                    Crofun::log_create(Auth::user()->id, $request->file_delete, config('constant.CONTRACT_FILE'), config('constant.operation_DELETE'), config('constant.CONTRACT_EDIT'), $compact['contract']->company_id, Contract_file::find($request->file_delete)->file_original_name, null, null, null);
                    return view('contract.edit', $compact)->with('message', trans('message.delete_success'));
                } else {
                    return view('contract.edit', $compact)->with('message', trans('message.delete_fail'));
                }
            }

            //upload file
            $uploadResult = $this->contractService->uploadFile($fileNoteArr, $contract, 'CONTRACT_EDIT', $contractFiles);
            if (!$uploadResult) {
                $compact['contractFiles'] = $this->contractService->getContractFileObj4Edit($contract->id);
                return view('contract.edit', $compact)->with('message', trans('message.save_fail'));
            }

            //update contract data
            if ($contract->update()) {
                $compact['updateTime'] = $contract->updated_at;
                $compact['contractFiles'] = $this->contractService->getContractFileObj4Edit($contract->id);
                $compact['contract'] = Contract_MST::where('id', $request->id)->first();
                $compact['contractList4Ref'] = $this->contractService->getContractListForRef($contract);
                Crofun::log_create(Auth::user()->id, $compact['contract']->id, config('constant.CONTRACT'), config('constant.operation_UPDATE'), config('constant.CONTRACT_EDIT'), $compact['contract']->company_id, null, $compact['contract']->contract_id, json_encode($compact['contract']), $originData);
                return view('contract.edit', $compact)->with('message', trans('message.edit_success'));
            } else {
                return view('contract.edit', $compact)->with('message', trans('message.update_fail'));
            }
        }
        return view('contract.edit', $compact);
    }

    public function view(Request $request)
    {
        $contract = Contract_MST::where('id', $request->id)->first();
        $departmentsRef    = Department_MST::orderBy('department_code', 'asc');
        $departmentsRef = $contract->status ? $departmentsRef->where('status', true)->get() : $departmentsRef->get();
        $contractFiles = $this->contractService->getContractFileObj4Edit($contract->id);
        $customer = Customer_MST::where('id', $contract->client_id)->first();
        $contractTypes = Contract_type::where("hidden", 0)->where("company_id", $customer->company_id)->get();
        $contractList4Ref = $this->contractService->getContractListForRef($contract);
        $compact = compact('contractFiles', 'contractTypes', 'customer', 'contract', 'departmentsRef', 'contractList4Ref');
        if ($request->isMethod('post')) {
            $updateTime = $contract->updated_at;
            $validator = $this->validateData($request, $updateTime);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $contract->updated_at = $errors->has('update_time') ? $request->update_time : $updateTime;
                $compact['contract'] = $contract;
                $compact['errors'] = $errors;
                return view('contract.view', $compact);
            }
            //get data for save
            $contract = $this->contractService->makeDataForView($contract);
            //update contract data
            if ($contract->update()) {
                $compact['updateTime'] = $contract->updated_at;
                $compact['contract'] = Contract_MST::where('id', $request->id)->first();
                return view('contract.view', $compact)->with('message', trans('message.edit_success'));
            } else {
                return view('contract.view', $compact)->with('message', trans('message.update_fail'));
            }
        }
        return view('contract.view', $compact);
    }

    public function validateData(Request $request, $update_time = null)
    {
        $validator = Validator::make($request->all(), [
            'update_time'                => [new CompareUpdateTime($update_time)],
        ], []);
        return $validator;
    }
    public function getCsv(Request $request)
    {
        try {
            $file_name = '契約書情報_' . Common::getToDayCSV();
            $callback  = $this->contractService->getContractData($request); // call get customer data function from service

            $headers   = array(
                "Content-sale" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $file_name . '.csv',
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
        } catch (Exception $e) {
            throw new Exception($e);
        }
        return response()->stream($callback, 200, $headers);
    }

}
