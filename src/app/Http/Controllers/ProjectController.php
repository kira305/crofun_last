<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Project_MST;
use App\Company_MST;
use App\Rules\CheckProjectCode;
use App\Service\ProjectService;
use App\Customer_MST;
use App\Repositories\CreditInforRepository;
use App\Rules\CompareUpdateTime;
use Auth;
use Exception;
use DB;
use Crofun;
use Common;

class ProjectController extends Controller
{
    protected $creditInforRepository;
    protected $project_service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProjectService $project_service, CreditInforRepository $creditInforRepository)
    {
        $this->project_service       = $project_service;
        $this->creditInforRepository = $creditInforRepository;
    }
    /*
    * return results of search action or list projects in first time
    * $request : search condition
    * return list projects
    */
    public function index(Request $request)
    {
        session(['project' => array('page' => $request->page)]);
        if ($request->isMethod('post')) {
            $company_id       = $request->company_id_p;
            $headquarter_id   = $request->headquarter_id_p;
            $department_id    = $request->department_id_p;
            $group_id         = $request->group_id_p;
            $client_code      = $request->client_code;
            $personal_code    = $request->personal_code;
            $client_name      = mb_convert_kana($request->client_name, 'rhk');
            $project_code     = $request->project_code;
            $project_name     = $request->project_name;
            $project_status   = $request->status;

            session()->flashInput($request->input());
            // save search condition to session
            session(['company_id_p'     => $company_id]);
            session(['headquarter_id_p' => $headquarter_id]);
            session(['department_id_p'  => $department_id]);
            session(['group_id_p'       => $group_id]);
            session(['client_code'      => $client_code]);
            session(['personal_code'    => $personal_code]);
            session(['client_name'      => $client_name]);
            session(['project_code'     => $project_code]);
            session(['project_name'     => $project_name]);
            session(['project_status'   => $project_status]);

            $projects = $this->project_service->search(
                $company_id,
                $headquarter_id,
                $department_id,
                $group_id,
                $client_code,
                $personal_code,
                $client_name,
                $project_code,
                $project_name,
                $project_status
            );
            return view('project.index', [
                "projects" => $projects
            ]);
        }

        if (isset($request->client_id)) {
            $client_id   =   $request->client_id;
            $customer    = Customer_MST::where('id', $client_id)->first();
            session(['company_id_p'     => $customer->company_id]);
            session(['headquarter_id_p' => null]);
            session(['department_id_p'  => null]);
            session(['group_id_p'       => null]);
            session(['client_code'      => $customer->client_code_main != null ? $customer->client_code_main : $customer->client_code]);
            session(['personal_code'    => null]);
            session(['client_name'      => null]);
            session(['project_code'     => null]);
            session(['project_name'     => null]);
            session(['project_status'   => null]);
        }

        // if is not search action hen check session' varible is exist?
        if ($this->project_service->checkSessionExist($request) == 1) {
            $condition = $this->project_service->getSearchCondition($request);
            $projects  = $this->project_service->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5], $condition[6], $condition[7], $condition[8], $condition[9]);
            return view('project.index', ['projects' => $projects]);
        }
        //本務の情報をセットする
        $company_id_R  =  Auth::user()->company_id;
        $projects  = $this->project_service->search($company_id_R, '', '', '', '', '', '', '', '', '');

        return view('project.index', ["projects" => $projects]); // return view of index
    }

    /*
    * check data' rule
    * $request : form data
    * return check result
    */

    public function validateData(Request $request, $project = null)
    {
        $update_time = $project == null ? null : $project->updated_at;
        $validator = Validator::make($request->all(), [
            'update_time'                => [new CompareUpdateTime($update_time)],
            'headquarter_id'             => 'required',
            'department_id'              => 'required',
            'group_id'                   => 'required',
            'project_code'               => [
                'required',
                'max:4',
                'min:4',
                new CheckProjectCode($request->company_id, $request->id)
                // check project code is not duplicate in a company
            ],
            'project_name'               => 'required',
            'get_code'                   => 'nullable|max:4|regex:/^[a-zA-Z0-9あ-ん]*$/u',
            'get_code_name'              => 'nullable|max:25',
            'transaction_money'          => 'required|numeric|max:99999999|regex:/^[0-9,]*$/u',
            'transaction_shot'           => 'nullable|numeric|max:99999999|regex:/^[0-9,]*$/u',
        ], [
            'headquarter_id.required'         => trans('validation.headquarter_chose'),
            'department_id.required'          => trans('validation.department_chose'),
            'group_id.required'               => trans('validation.group_chose'),
            'project_code.required'           => trans('validation.project_code'),
            'project_code.max'                => trans('validation.max_string_4'),
            'project_code.min'                => trans('validation.max_string_4'),
            'project_name.required'           => trans('validation.project_name'),
            'get_code.regex'                  => trans('validation.list_code_format'),
            'get_code.max'                    => trans('validation.max_string_4'),
            'get_code_name.max'               => trans('validation.max_string_25'),
            'transaction_money.required'      => trans('validation.transaction_money'),
            'transaction_money.max'           => trans('validation.transaction_money_max'),
            'transaction_money.regex'         => trans('validation.transaction_money_regex'),
            'transaction_shot.max'            => trans('validation.transaction_shot_max'),
            'transaction_shot.regex'          => trans('validation.transaction_shot.regex'),
        ]);

        return $validator;
    }

    /*
    * create new project
    * $request : form data
    * return update project's detail status
    */

    public function create(Request $request)
    {
        $company_id  = $request->company_id;
        $customer_id = $request->customer_id;
        // $transaction =  $transaction_expect + $transaction_shot;
        $transaction =  $this->creditInforRepository->getTransactionMoney($customer_id);
        //最新の希望限度額
        $credit_expect = $this->project_service->crediteslatest($company_id, $customer_id);
        $credit_expect = $credit_expect->first();

        if ($credit_expect) {

            $credit_expect = $credit_expect->credit_expect;
        } else {

            $credit_expect = null;
        }

        if ($request->isMethod('post')) {
            DB::beginTransaction();
            $validator = $this->validateData($request);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                return view('project.create', ['errors' => $errors, 'customer_id' => $customer_id, 'company_id' => $company_id, 'transaction' => $transaction, 'credit_expect' => $credit_expect]);
            }

            /*プロジェクト名重複チェック*/
            //取引想定額を今回入力データの追加
            $transaction_money = $request->transaction_money * 1000;
            if ($request->transaction_shot != "") {
                $transaction_shot  = $request->transaction_shot * 1000;
            } else {
                $transaction_shot = 0;
            }
            $transaction = $transaction + $transaction_money + $transaction_shot;
            $headquarter_id    = $request->headquarter_id;
            $department_id     = $request->department_id;
            $group_id          = $request->group_id;
            $project_name      = $request->project_name;
            $project_code      = $request->project_code;
            $get_code          = $request->get_code;
            $get_code_name     = $request->get_code_name;
            $once_shot         = $request->once_shot;
            $note              = trim($request->note);

            //取引想定額の方が大きい場合
            if ($transaction > $credit_expect) {
                $note =   $note . '&#13;&#10;与信希望限度額：' . number_format($credit_expect / 1000) . ' ・取引想定合計額：' . number_format($transaction / 1000) . '・登録日:' . now();
            }
            $project           = new Project_MST();
            $project->client_id         = $customer_id;
            $project->company_id        = $company_id;
            $project->headquarter_id    = $headquarter_id;
            $project->department_id     = $department_id;
            $project->group_id          = $group_id;
            $project->project_code      = $project_code;
            $project->project_name      = $project_name;
            $project->get_code          = $get_code;
            $project->get_code_name     = $get_code_name;
            $project->transaction_money = $transaction_money;
            $project->transaction_shot  = $transaction_shot;
            $project->once_shot         = $once_shot;
            $project->note              = $note;
            $project->status            = true;

            if ($project->save()) { // if save success
                $company   = Company_MST::where('id', $project->company_id)->first();
                $company->project_max_code = $company->project_max_code + 1;
                $company->update();
                DB::commit();
                //ログ追加
                Crofun::log_create(Auth::user()->id, $project->id, config('constant.PROJECT'), config('constant.operation_CRATE'), config('constant.PROJECT_ADD'), $project->company_id, $project->project_name, $project->project_code, json_encode($project), null);

                return view('project.create', [
                    'message'      => trans('message.save_success'),
                    'customer_id'  => $customer_id,
                    'company_id'   => $company_id,
                    'transaction'  => $transaction,
                    'credit_expect' => $credit_expect,
                    'note'         => $note,
                    'success'      => 1
                ]);
            } else {  // if not save success then return view with errors
                $validator->errors()->add('message', trans('message.save_fail')); // add error to array
                $errors = $validator->errors();
                return view('project.create', ['errors' => $errors, 'customer_id' => $customer_id, 'company_id' => $company_id, 'transaction' => $transaction, 'credit_expect' => $credit_expect]);
            }
        }

        return view('project.create', ['customer_id' => $customer_id, 'company_id' => $company_id, 'transaction' => $transaction, 'credit_expect' => $credit_expect]);
    }

    /*
    * create new project
    * $request : form data
    * return update project's information status
    */

    public function edit(Request $request)
    {
        //開くプロジェクトの取得
        $project = Project_MST::where('id', $request->id)->first();

        $company_id  = $request->company_id;
        $customer_id = $project->client_id;
        $transaction =  $this->creditInforRepository->getTransactionMoney($customer_id);
        //最新の希望限度額
        $credit_expect = $this->project_service->crediteslatest($company_id, $customer_id);
        $customer      = Customer_MST::where('id', $customer_id)->first();
        $credit = $customer->credit_check();

        //希望限度額がセットされているかどうか
        if ($credit = $customer->credit_check()) {
            $credit_expect = $credit->credit_expect;
        } else {
            $credit_expect = null;
        }

        //リクエスト情報かどうか
        if ($request->isMethod('post')) {
            $transaction_money = mb_convert_kana($request->transaction_money, "rn");
            $transaction_money = (int)filter_var($transaction_money, FILTER_SANITIZE_NUMBER_INT);
            $request->merge([
                'transaction_money' => $transaction_money,
            ]);
            $transaction_shot  = 0;
            if ($request->transaction_shot != null) {
                $transaction_shot  = mb_convert_kana($request->transaction_shot, "rn");
                $transaction_shot  = (int)filter_var($transaction_shot, FILTER_SANITIZE_NUMBER_INT);
                $request->merge([
                    'transaction_shot' => $transaction_shot,
                ]);
                $transaction_shot  =  $transaction_shot * 1000;
            }

            $validator = $this->validateData($request, $project);
            session()->flashInput($request->input());
            $old_date            = json_encode($project);
            $headquarter_id      = $request->headquarter_id;
            $department_id       = $request->department_id;
            $group_id            = $request->group_id;
            $project_name        = $request->project_name;
            $project_code        = $request->project_code;
            $get_code            = $request->get_code;
            $get_code_name       = $request->get_code_name;
            $transaction_money   = $request->transaction_money * 1000;
            $transaction_shot_s  =  $transaction_shot;
            $once_shot         = $request->once_shot;
            $note              = trim($request->note);
            $status            = $request->status;

            if ($once_shot == 'on') {
                $once_shot = true;
            } else {
                $once_shot = false;
            }

            if ($status == 'on') {
                $status = false;
            } else {
                $status = true;
            }

            $project->headquarter_id    = $headquarter_id;
            $project->department_id     = $department_id;
            $project->group_id          = $group_id;
            $project->project_name      = $project_name;
            $project->get_code          = $get_code;
            $project->get_code_name     = $get_code_name;
            $project->once_shot         = $once_shot;
            $project->status            = $status;
            $project->transaction_money = $transaction_money;
            $project->transaction_shot  = $transaction_shot_s;
            $project->note              =  $note;
            $project->updated_at        =  $request->update_time;
            if ($validator->fails()) {
                $errors = $validator->errors();
                return view('project.edit', ['errors' => $errors, 'project' => $project, 'transaction' => $transaction, 'credit_expect' => $credit_expect]);
            }

            /*プロジェクト名重複チェック*/
            //取引想定額 自分のプロジェクトは除く
            $transaction_expect = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
                ->where('client_id', $customer_id)
                ->where('project_mst.id', "<>", $request->id)
                ->where('project_mst.status', 'true')
                ->sum('transaction_money');

            //単発　 自分のプロジェクトは除く
            $transaction_shot = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
                ->where('client_id', $customer_id)
                ->where('project_mst.id', "<>", $request->id)
                ->where('project_mst.status', 'true')
                ->where('project_mst.once_shot', 'true')
                ->sum('transaction_shot');

            //合計
            $transaction =  $transaction_expect + $transaction_shot;
            if (($request->once_shot == "on") && ($request->transaction_shot != "") && ($project->status == true)) {
                $transaction = $project->transaction_money + $project->transaction_shot + $transaction;
            } else {
                $project->transaction_shot  = 0;
                if ($project->status == true) {
                    $transaction = $project->transaction_money +  $transaction;
                }
            }

            //取引想定額の方が大きい場合
            if ($transaction > $credit_expect) {
                $project->note =   $note . '&#13;&#10;与信希望限度額：' . number_format($credit_expect / 1000) . ' ・取引想定合計額：' . number_format($transaction / 1000) . '・登録日:' . now();
            } else {
                $project->note =  $note;
            }
            if ($project->update()) {
                //ログ追加
                Crofun::log_create(Auth::user()->id, $project->id, config('constant.PROJECT'), config('constant.operation_UPDATE'), config('constant.PROJECT_EDIT'), $project->company_id, $project->project_name, $project->project_code, json_encode($project), $old_date);
                return view('project.edit', ['project'     => $project, 'message'     => trans('message.update_success'), 'transaction' => $transaction, 'credit_expect' => $credit_expect]);
            } else {
                $validator->errors()->add('message', trans('message.save_fail'));
                $errors = $validator->errors();
                return view('project.edit', [
                    'project'      => $project,
                    'errors'       => $errors,
                    'transaction'  => $transaction,
                    'credit_expect' => $credit_expect
                ]);
            }
        }
        $this->authorize('update', $project);
        $project = Project_MST::where('id', $request->id)->first();
        return view('project.edit', ['project' => $project, 'transaction' => $transaction, 'credit_expect' => $credit_expect]);
    }

    public function view(Request $request)
    {
        $project = Project_MST::where('id', $request->id)->first();
        //取引想定額
        $transaction_expect = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
            ->where('client_id', $project->client_id)
            ->where('project_mst.status', 'true')
            ->sum('transaction_money');

        //単発
        $transaction_shot = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
            ->where('client_id', $project->client_id)
            ->where('project_mst.status', 'true')
            ->where('project_mst.once_shot', 'true')
            ->sum('transaction_shot');

        $transaction =  $transaction_expect + $transaction_shot;
        $credit_expect = $this->project_service->crediteslatest($project->company_id, $project->client_id);
        $credit_expect = $credit_expect->first();
        if (isset($credit_expect)) {
            $credit_expect = $credit_expect->credit_expect;
        } else {
            $credit_expect = null;
        }

        return view('project.project_view', [
            'project'      => $project,
            'transaction'   => $transaction,
            'credit_expect' => $credit_expect

        ]);
    }
    /*
    * stream data for create csv file
    * $request : search condition
    * return stream data for create csv file
    */

    public function getCsv1(Request $request)
    {
        try {
            $file_name = 'プロジェクト情報_' . Common::getToDayCSV();
            $callback  = $this->project_service->getProjectData($request);
            $headers   = array(
                "Content-type" => "text/csv",
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

    /*
    * stream data for create csv file
    * $request : search condition
    * return stream data for create csv file
    */
    public function getCsv2(Request $request)
    {
        try {
            $project_id = $request->project_id;
            $file_name =  $this->getProjectName($project_id) . '_' . Common::getToDayCSV();
            $callback  = $this->project_service->getOnceProjectData($project_id); // get stream data of once project
            $headers   = array(
                "Content-type" => "text/csv",
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

    public function getProjectName($project_id)
    {
        $project_name = Project_MST::where('id', $project_id)->first()->project_name;
        return $project_name;
    }
    public function getMaxCode(Request $request)
    {
        $company_id =  $request->company_id;
        $num        =  Crofun::get_max_code_project($company_id);
        return response()->json(['num' =>  $num]);
    }

    public function checkprojectCreate(Request $request)
    {
        $project = Project_MST::where('project_name', $request->project_name)->where('company_id', $request->company_id)->first();
        if ($project) {
            return true;
        }
        return false;
    }

    public function checkprojectEdit(Request $request, $project)
    {
        $getproject = Project_MST::where('project_name', $request->project_name)->where('company_id', $project->company_id)->first();
        if ($getproject) {
            if ($getproject->id != $request->id) {
                return true;
            }
        }
        return false;
    }
}
