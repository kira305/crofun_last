<?php

namespace App\Http\Controllers;

use Crofun;
use Auth;
use App\Contract_type;
use Illuminate\Http\Request;
use App\Http\Requests\ContractTypeRequest;
use App\Service\ContractService;

class ContractTypeController extends Controller
{
    private $contractService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->contractService = new contractService();
    }

    public function index(Request $request)
    {
        $datasearch = $this->contractService->createSession4ContractType($request);
        $contractTypes = $this->contractService->searchContractType($datasearch);
        return view('contract_type.index', compact('contractTypes'));
    }


    public function create(ContractTypeRequest $request)
    {
        if (request()->method() == 'POST') {
            $contractType = new Contract_type();
            $contractType->type_name  = $request->type_name;
            $contractType->display_code     = $request->display_code;
            $contractType->description   = $request->description;
            $contractType->company_id   = $request->company_id;
            if ($contractType->save()) {
                $newContractType = Contract_type::where("type_name", $request->type_name)->first();
                Crofun::log_create(Auth::user()->id, $newContractType->id, config('constant.CONTRACT_TYPE'), config('constant.operation_CRATE'), config('constant.CONTRACT_TYPE_CREATE'), $request->company_id, $request->type_name, null, json_encode($newContractType), null);
                return view('contract_type.create')->with('message', trans('message.create_success'));
            } else {
                return view('contract_type.create', ["message" => trans('message.group_change_fail')]);
            }
        }

        return view('contract_type.create');
    }

    public function edit(ContractTypeRequest $request)
    {

        $contractType = Contract_type::where("id", $request->id)->first();
        $updateTime = session()->has('update_time_session') ?  session('update_time_session') : $contractType->updated_at;
        $originData = json_encode($contractType);
        if (request()->method() == 'POST') {
            $contractType->type_name  = $request->type_name;
            $contractType->display_code     = $request->display_code;
            $contractType->description   = $request->description;
            $contractType->company_id   = $request->company_id;
            $contractType->hidden   = $request->hidden == 'on' ? 1 : 0;
            if ($contractType->update()) {
                $updateTime = $contractType->updated_at;
                Crofun::log_create(Auth::user()->id, $contractType->id, config('constant.CONTRACT_TYPE'), config('constant.operation_UPDATE'), config('constant.CONTRACT_TYPE_EDIT'), $request->company_id, $request->type_name, null, json_encode($contractType), $originData);
                return view('contract_type.edit', compact('contractType', 'updateTime'))->with('message', trans('message.edit_success'));
            } else {
                return view('contract_type.edit', compact('contractType', 'updateTime'))->with('message', trans('message.group_change_fail'));
            }
        } else {
            return view('contract_type.edit', compact('contractType', 'updateTime'));
        }
    }
}
