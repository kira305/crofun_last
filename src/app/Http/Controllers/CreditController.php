<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Service\CustomerService;
use App\Credit_MST;
use App\Project_MST;
use App\Customer_MST;
use Auth;
use Crofun;
use Exception;
use Carbon\Carbon;

class CreditController extends Controller
{
    protected $customer_service;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CustomerService $customer_service)
    {
        //$this->middleware('auth');
        $this->customer_service   = $customer_service;
    }

    //   public function index(){


    //      $receivable = Receivable_MST::all();

    //    return view('receivable.index',["receivable" => $receivable]);


    // }
    public function log(Request $request)
    {

        //リクエストが与えられたか？
        if ($request->isMethod('post')) {
            $validator = $this->validateData($request);
            //画面からリクエストされている情報
            $company_id       = $request->company_id;
            $client_code      = $request->client_code;
            $client_name      = $request->client_name;
            $client_name_kana = mb_convert_kana($request->client_name_kana, 'rhk');
            $corporation_num  = $request->corporation_num;
            $get_data_st      = $request->get_data_st;
            $get_data_en      = $request->get_data_en;
            $expiration_data_st      = $request->expiration_data_st;
            $expiration_data_en      = $request->expiration_data_en;

            $validator = $this->validateData($request);
            if ($validator->fails()) {
                $credit = $this->getCreditListByCompanyId($company_id);

                $errors = $validator->errors();

                return view('credit.log', [
                    "errors" => $errors,
                    "credit"           => $credit,
                    "client_code"      => $client_code,
                    "client_name_kana" => $client_name_kana,
                    "corporation_num"  => $corporation_num,
                    "client_name"      => $client_name,
                    "get_data_st"   => $get_data_st,
                    "get_data_en"   => $get_data_en,
                    "expiration_data_st"   => $expiration_data_st,
                    "expiration_data_en"   => $expiration_data_en
                ]);
            }

            session(['company_id_cr_log'       => $company_id]);
            session(['client_code_cr_log'      => $client_code]);
            session(['client_name_cr_log'      => $client_name]);
            session(['client_name_kana_cr_log' => $client_name_kana]);
            session(['corporation_num_cr_log'  => $corporation_num]);
            session(['get_data_st_cr_log'   => $get_data_st]);
            session(['get_data_en_cr_log'   => $get_data_en]);
            session(['expiration_data_st_cr_log'   => $expiration_data_st]);
            session(['expiration_data_en_cr_log'   => $expiration_data_en]);

            $credit          = $this->search($company_id, $client_code, $client_name, $client_name_kana, $corporation_num, $get_data_st, $get_data_en, $expiration_data_st, $expiration_data_en);

            return view('credit.log', [
                "credit"           => $credit,
                "company_id"       => session('company_id_cr_log'),
                "client_code"      => session('client_code_cr_log'),
                "client_name_kana" => session('client_name_kana_cr_log'),
                "corporation_num"  => session('corporation_num_cr_log'),
                "client_name"      => session('client_name_cr_log'),
                "get_data_st"   => session('get_data_st_cr_log'),
                "get_data_en"   => session('get_data_en_cr_log'),
                "expiration_data_st"   => session('expiration_data_st_cr_log'),
                "expiration_data_en"   => session('expiration_data_en_cr_log')
            ]);
        }
        if (request()->client_id != null || request()->id != null) {
            $client_id   =   $request->client_id;
            $customer = Customer_MST::where('id', $client_id)->first();
            session(['company_id_cr_log'        => $customer->company_id]);
            session(['client_code_cr_log'       => $customer->client_code_main != null ? $customer->client_code_main : $customer->client_code]);
            session(['client_name_cr_log'       => null]);
            session(['client_name_kana_cr_log'  => null]);
            session(['corporation_num_cr_log'   => null]);
            session(['get_data_st_cr_log'       => null]);
            session(['get_data_en_cr_log'       => null]);
            session(['expiration_data_st_cr_log'=> null]);
            session(['expiration_data_en_cr_log'=> null]);
        }
        //セッションの情報で検索　(他の画面から遷移した時)
        if (
            $request->session()->exists('company_id_cr_log')       ||
            $request->session()->exists('client_code_cr_log')      ||
            $request->session()->exists('client_name_cr_log')      ||
            $request->session()->exists('client_name_kana_cr_log') ||
            $request->session()->exists('corporation_num_cr_log')  ||
            $request->session()->exists('get_data_st_cr_log')   ||
            $request->session()->exists('get_data_en_cr_log') ||
            $request->session()->exists('expiration_data_st_cr_log') ||
            $request->session()->exists('expiration_data_en_cr_log')

        ) {

            $condition = $this->searchCostBySession($request);

            $validator = $this->validateData($request);
            if ($validator->fails()) {
                $company_id =  Auth::user()->company_id;
                $credit = $this->getCreditListByCompanyId($company_id);
            }

            $credit = $this->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5], $condition[6], $condition[7], $condition[8]);

            return view('credit.log', [
                "credit"           => $credit,
                "company_id"       => session('company_id_cr_log'),
                "client_code"      => session('client_code_cr_log'),
                "client_name_kana" => session('client_name_kana_cr_log'),
                "corporation_num"  => session('corporation_num_cr_log'),
                "client_name"      => session('client_name_cr_log'),
                "get_data_st"   => session('get_data_st_cr_log'),
                "get_data_en"   => session('get_data_en_cr_log'),
                "expiration_data_st"   => session('expiration_data_st_cr_log'),
                "expiration_data_en"   => session('expiration_data_en_cr_log')
            ]);
        }

        $company_id =  Auth::user()->company_id;

        $credit = $this->getCreditListByCompanyId($company_id);

        return view('credit.log', ['credit' => $credit]);
    }

    private function getCreditListByCompanyId($company_id){
        return Credit_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'credit_check.client_id')
        ->orderBy('credit_check.get_time', 'desc')
        ->where('credit_check.company_id', $company_id)
        ->select('credit_check.*')
        ->paginate(25);
    }


    public function searchCostBySession($request)
    {

        $condition = array();
        if ($request->session()->exists('company_id_cr_log')) {

            $company_id = session('company_id_cr_log');
            array_push($condition, $company_id);
        } else {

            $company_id = "";
            array_push($condition, $company_id);
        }

        if ($request->session()->exists('client_code_cr_log')) {

            $client_code = session('client_code_cr_log');
            array_push($condition, $client_code);
        } else {

            $client_code = "";
            array_push($condition, $client_code);
        }
        if ($request->session()->exists('client_name_cr_log')) {

            $client_name  = session('client_name_cr_log');
            array_push($condition, $client_name);
        } else {

            $client_name  = "";
            array_push($condition, $client_name);
        }

        if ($request->session()->exists('client_name_kana_cr_log')) {

            $client_name_kana  = session('client_name_kana_cr_log');
            array_push($condition, $client_name_kana);
        } else {

            $client_name_kana  = "";
            array_push($condition, $client_name_kana);
        }

        if ($request->session()->exists('corporation_num_cr_log')) {

            $corporation_num  = session('corporation_num_cr_log');
            array_push($condition, $corporation_num);
        } else {

            $corporation_num  = "";
            array_push($condition, $corporation_num);
        }

        if ($request->session()->exists('get_data_st_cr_log')) {

            $get_data_st  = session('get_data_st_cr_log');
            array_push($condition, $get_data_st);
        } else {

            $get_data_st  = "";
            array_push($condition, $get_data_st);
        }

        if ($request->session()->exists('get_data_en_cr_log')) {

            $get_data_en  = session('get_data_en_cr_log');
            array_push($condition, $get_data_en);
        } else {

            $get_data_en  = "";
            array_push($condition, $get_data_en);
        }

        if ($request->session()->exists('expiration_data_st_cr_log')) {

            $expiration_data_st  = session('expiration_data_st_cr_log');
            array_push($condition, $expiration_data_st);
        } else {

            $expiration_data_st  = "";
            array_push($condition, $expiration_data_st);
        }

        if ($request->session()->exists('expiration_data_en_cr_log')) {

            $expiration_data_st  = session('expiration_data_en_cr_log');
            array_push($condition, $expiration_data_st);
        } else {

            $expiration_data_en  = "";
            array_push($condition, $expiration_data_en);
        }
        return  $condition;
    }



    public function search($company_id, $client_code, $client_name, $client_name_kana, $corporation_num, $get_data_st, $get_data_en, $expiration_data_st, $expiration_data_en)
    {
        //大元の検索条件
        $credit = Credit_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'credit_check.client_id')
            ->orderBy('credit_check.get_time', 'desc')
            ->select('credit_check.*');

        //検索の条件が有れば、条件をｾｯﾄする
        if ($company_id != "") {

            $credit = $credit->where('credit_check.company_id', $company_id);
        }

        if ($client_code != "") {
            $credit = $credit->where(function ($query) use ($client_code) {
                $query->where('customer_mst.client_code', $client_code)
                    ->orWhere('customer_mst.client_code_main', $client_code);
            });
        }

        if ($client_name != "") {

            $credit = $credit->where('customer_mst.client_name', 'like', "$client_name%");
        }

        if ($client_name_kana != "") {

            $credit = $credit->where('customer_mst.client_name_kana', 'like', "%$client_name_kana%");
        }

        if ($corporation_num != "") {

            $credit = $credit->where('customer_mst.corporation_num', $corporation_num);
        }

        if ($get_data_st != "" & $get_data_en != "") {

            $credit = $credit->whereBetween('credit_check.get_time', [$get_data_st, $get_data_en]);
        } elseif ($get_data_st == "" & $get_data_en != "") {

            $credit = $credit->whereBetween('credit_check.get_time', ["2000/01/01", $get_data_en]);
        } elseif ($get_data_st != "" & $get_data_en == "") {

            $credit = $credit->whereBetween('credit_check.get_time', [$get_data_st, '9999/12/31']);
        }

        if ($expiration_data_st != "" & $expiration_data_en != "") {

            $credit = $credit->whereBetween('credit_check.expiration_date', [$expiration_data_st, $expiration_data_en]);
        } elseif ($expiration_data_st == "" & $expiration_data_en != "") {

            $credit = $credit->whereBetween('credit_check.expiration_date', ["2000/01/01", $expiration_data_en]);
        } elseif ($expiration_data_st != "" & $expiration_data_en == "") {

            $credit = $credit->whereBetween('credit_check.expiration_date', [$expiration_data_st, '9999/12/31']);
        }

        //検索結果
        $credit          = $credit->paginate(25);

        return $credit;
    }
    public function validateData(Request $request)
    {
        /*エラーのチェック 日付型*/
        $validator = Validator::make($request->all(), [

            'get_data_st'        => 'nullable|date',
            'get_data_en'        => 'nullable|date',
            'expiration_data_st'        => 'nullable|date',
            'expiration_data_en'        => 'nullable|date',

        ], [

            'get_data_st.date'       => trans('validation.import_log_start_time'),
            'get_data_en.date'       => trans('validation.import_log_end_time'),
            'expiration_data_st.date'       => trans('validation.import_log_end_time'),
            'expiration_data_en.date'       => trans('validation.import_log_end_time'),

        ]);

        $errors = $validator->errors();

        return $validator;
    }


    public function validateDataInput(Request $request)
    {
        /*エラーのチェック 日付型*/
        $validator = Validator::make($request->all(), [

            'credit_expect'       =>  [
                'required',
                'numeric',
                'max:99999999',
                //   new CreditExpect()
            ],
            'credit_division'     => 'required',
            'get_time'            => 'required|date',
            'rank'                => 'required',
        ], [

            'credit_expect.required'       => trans('validation.credit_expect'),
            'credit_division.required'     => trans('validation.credit_division'),
            'get_time.required'            => trans('validation.get_time'),
            'get_time.date'                => trans('validation.get_time'),
            'rank.required'                => trans('validation.rank'),
            'credit_expect.min'            => trans('validation.min1000'),
            'credit_expect.max'            => trans('validation.credit_expect_max'),
            'credit_expect.numeric'        => trans('validation.code_int'),
        ]);



        return $validator;
    }
    public function validator_rm(Request $request)
    {
        /*エラーのチェック 日付型*/

        $validator = Validator::make($request->all(), [

            'rank'                => 'required|in:A,B,C,D,E1,F1,E2,F2,F3,G',
            'get_time'            => 'required|date',
            'credit_limit'        => [
                'required',
                'numeric',
                'max:99999999',
                // new CreditLimit()
            ],
            'credit_expect'       => [
                'required',
                'numeric',
                'max:99999999',
                //new CreditExpect()
            ]
        ], [
            'rank.required'             => trans('validation.rank_RM'),
            'rank.in'                   => trans('validation.rank_RM'),
            'get_time.required'         => trans('validation.get_time'),
            'get_time.date'             => trans('validation.get_time'),
            'credit_limit.required'     => trans('validation.credit_limit'),
            'credit_limit.max'          => trans('validation.credit_limit_max'),
            'credit_expect.required'    => trans('validation.credit_expect'),
            'credit_expect.max'         => trans('validation.credit_expect_max'),
            'credit_expect.numeric'     => trans('validation.code_int'),

        ]);

        return $validator;
    }
    public function validator_TSR(Request $request)
    {
        /*エラーのチェック 日付型*/

        $validator = Validator::make($request->all(), [

            'rank'                => 'required|integer|between:0,100',
            'get_time'            => 'required|date',
            'credit_expect'       =>  [
                'required',
                'numeric',
                'max:99999999',
                // new CreditExpect()
            ]

        ], [
            'rank.required'                => trans('validation.rank_TSR'),
            'rank.integer'                 => trans('validation.rank_TSR'),
            'rank.between'                 => trans('validation.rank_TSR'),
            'get_time.required'            => trans('validation.get_time'),
            'get_time.date'                => trans('validation.get_time'),
            'credit_expect.required'       => trans('validation.credit_expect'),
            'credit_expect.max'         => trans('validation.credit_expect_max'),
            'credit_expect.min'            => trans('validation.min1000'),
            'credit_expect.numeric'        => trans('validation.code_int'),
        ]);


        return $validator;
    }

    public function validator_TDB(Request $request)
    {
        /*エラーのチェック 日付型*/
        $validator = Validator::make($request->all(), [

            'rank'                => 'required|in:A,B,C,D,E',
            'get_time'            => 'required|date',
            'credit_expect'       =>  [
                'required',
                'numeric',
                'max:99999999',
                // new CreditExpect()
            ]

        ], [
            'rank.required'                => trans('validation.rank_TDB'),
            'rank.in'                      => trans('validation.rank_TDB'),
            'get_time.required'            => trans('validation.get_time'),
            'get_time.date'                => trans('validation.get_time'),
            'credit_expect.required'       => trans('validation.credit_expect'),
            'credit_expect.max'         => trans('validation.credit_expect_max'),
            'credit_expect.min'            => trans('validation.min1000'),

        ]);



        return $validator;
    }

    public function create(Request $request)
    {
        $preUrlStatus = $this->customer_service->getPrePathInfo();
        if (isset($request->pre_url_status)) {
            $preUrlStatus = $preUrlStatus == 3 ? $request->pre_url_status : $preUrlStatus;
        }
        $client_id  = $request->client_id;
        $company_id = $request->company_id;

        $client_code = Crofun::getClientById($client_id)->client_code_main;
        $client_name = Crofun::getClientById($client_id)->client_name;

        //取引想定額
        $transaction_expect = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
            ->where('client_id', $client_id)
            ->where('project_mst.status', 'true')
            ->sum('transaction_money');

        //単発
        $transaction_shot = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
            ->where('client_id', $client_id)
            ->where('project_mst.status', 'true')
            ->where('project_mst.once_shot', 'true')
            ->sum('transaction_shot');

        $transaction =  $transaction_expect + $transaction_shot;

        if ($request->isMethod('post')) {


            $credit_expect = (int)filter_var($request->credit_expect, FILTER_SANITIZE_NUMBER_INT);
            $request->merge([
                'credit_expect' => $credit_expect,
            ]);
            $credit_limit = (int)filter_var($request->credit_limit, FILTER_SANITIZE_NUMBER_INT);
            $request->merge([
                'credit_limit' => $credit_limit,
            ]);
            $expiration_date = $request->expiration_date;
            $rank            = $request->rank;
            $credit_expect   = $request->credit_expect;

            /*連続登録の場合は、はじく*/
            if ($expiration_date != "") {

                return view('credit.create', [
                    'client_code'    => $client_code,
                    'client_name'    => $client_name,
                    'renew_time'     => $expiration_date,
                    'rank_conversion' => $rank,
                    'client_id'      => $client_id,
                    'company_id'     => $company_id,
                    'transaction'    => $transaction,
                    'pre_url_status'    => $preUrlStatus
                ])->with('message', trans('message.credit_double'));
            }

            session()->flashInput($request->input());
            //与信の取得元がどこかによって、処理を切り替え
            //RMの場合
            if ($request->credit_division == 1) {
                $validator     =  $this->validator_rm($request);
                if ($validator->fails()) {

                    $errors = $validator->errors();

                    return view('credit.create', [
                        'client_code'    => $client_code,
                        'client_name'    => $client_name,
                        'renew_time'     => $expiration_date,
                        'rank_conversion' => $rank,
                        'client_id'      => $client_id,
                        'company_id'     => $company_id,
                        'transaction'    => $transaction,
                        'errors'         => $errors,
                        'pre_url_status'    => $preUrlStatus
                    ]);
                } else {

                    $rank_conversion             = $this->customer_service->credit_rank($request->rank);
                    $renew_time                  = $this->customer_service->getRenewTimeRM($request->get_time, $request->rank, $request->credit_expect, $request->credit_limit);
                }
            //TSRの場合
            } elseif ($request->credit_division == 2) {

                $validator     =  $this->validator_TSR($request);
                if ($validator->fails()) {

                    $errors = $validator->errors();

                    return view('credit.create', [
                        'client_code'    => $client_code,
                        'renew_time'     => $expiration_date,
                        'rank_conversion' => $rank,
                        'client_name'    => $client_name,
                        'client_id'      => $client_id,
                        'company_id'     => $company_id,
                        'transaction'    => $transaction,
                        'errors'         => $errors,
                        'pre_url_status'    => $preUrlStatus
                    ]);
                } else {

                    $rank_conversion             = $this->customer_service->credit_rank_TSR($request->rank);
                    $renew_time                  = $this->getRenewTime_TSR($request->get_time, $request->rank);
                }
            //TDBの場合
            } elseif ($request->credit_division == 3) {
                $validator     =  $this->validator_TDB($request);

                if ($validator->fails()) {

                    $errors = $validator->errors();

                    return view('credit.create', [
                        'client_code'    => $client_code,
                        'client_name'    => $client_name,
                        'renew_time'     => $expiration_date,
                        'rank_conversion' => $rank,
                        'client_id'      => $client_id,
                        'company_id'     => $company_id,
                        'transaction'    => $transaction,
                        'errors'         => $errors,
                        'pre_url_status'    => $preUrlStatus
                    ]);
                } else {
                    $rank_conversion             = $this->customer_service->credit_rank($request->rank);
                    $renew_time                    = $this->customer_service->getRenewTimeTDB($request->get_time, $request->rank);
                }
            } else {
                $validator     =  $this->validateDataInput($request);
                if ($validator->fails()) {

                    $errors = $validator->errors();

                    return view('credit.create', [
                        'client_code'    => $client_code,
                        'client_name'    => $client_name,
                        'client_id'      => $client_id,
                        'renew_time'     => $expiration_date,
                        'rank_conversion' => $rank,
                        'company_id'     => $company_id,
                        'transaction'    => $transaction,
                        'errors'         => $errors,
                        'pre_url_status'    => $preUrlStatus
                    ]);
                }
            }

            $credit_limit    = $request->credit_limit;
            $get_time        = $request->get_time;

            $credit_division = $request->credit_division;
            $note            = $request->note;

            //取引想定額の方が大きい場合
            if ($transaction > $credit_expect) {

                $note =   $note . '与信希望限度額：' . $credit_expect . ' ・取引想定合計額：' . ($transaction / 1000) . '・登録日:' . now();
            }

            $credit = new Credit_MST();

            $credit->client_id   = $client_id;
            $credit->company_id  = $company_id;

            if ($request->credit_limit != "") {

                $credit->credit_limit      = $credit_limit * 1000;
            } else {

                $credit->credit_limit      = 0;
            }

            if ($request->credit_expect != "") {

                $credit->credit_expect      = $credit_expect * 1000;
            } else {

                $credit->credit_expect      = 0;
            }

            $credit->rank              = $rank_conversion;
            $credit->expiration_date   = $renew_time;
            $credit->get_time          = $request->get_time;
            $credit->credit_division   = $credit_division;
            $credit->note              = $note;


            if ($credit->save()) {

                Crofun::log_create(Auth::user()->id, null, config('constant.CREDIT'), config('constant.operation_CRATE'), config('constant.CREDIT_ADD'), $credit->company_id, $client_name, $client_code, json_encode($credit), null);

                return view('credit.create', [
                    'note'           => $note,
                    'client_code'    => $client_code,
                    'client_name'    => $client_name,
                    'client_id'      => $client_id,
                    'company_id'     => $company_id,
                    'rank_conversion' => $rank_conversion,
                    'transaction'    => $transaction,
                    'renew_time'     => $renew_time,
                    'pre_url_status'    => $preUrlStatus
                ])->with('message', trans('message.save_success'));
            } else {

                return view('credit.create', [
                    'client_code'    => $client_code,
                    'client_name'    => $client_name,
                    'client_id'      => $client_id,
                    'company_id'     => $company_id,
                    'errors'         => $errors,
                    'rank_conversion' => $rank,
                    'transaction'    => $transaction,
                    'renew_time'     => null,
                    'pre_url_status'    => $preUrlStatus
                ])->with('message', trans('message.save_fail'));
            }
        }


        return view('credit.create', [
            'client_code'    => $client_code,
            'client_name'    => $client_name,
            'client_id'      => $client_id,
            'rank_conversion' => null,
            'renew_time'     => null,
            'transaction'    => $transaction,
            'company_id'     => $company_id,
            'pre_url_status'    => $preUrlStatus
        ]);
    }

    public function upload(Request $request)
    {

        $usr_id         = Auth::user()->id;
        $client_id      = $request->client_id;
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [

                'csv'                  => 'required|mimes:csv,txt'

            ], [

                'csv.required'         => trans('validation.company_name'),
                'csv.mimes'            => trans('validation.file_upload_fomat')
            ]);

            if ($validator->fails()) { // if has errors the return code 302

                $errors = $validator->errors();

                return response()->json([
                    'sale' => 302,
                    'errors' => $errors->first('csv')
                ]);
            }

            try {

                $csv = Input::file('csv');

                Storage::disk('public')->put($csv->getClientOriginalName(),  File::get($csv));
                // save file wa uloaded to public disk
                $csv_url = public_path() . '/uploads/' . $csv->getClientOriginalName();
                mb_language("Japanese");
                $row = 0;

                $get_time        = '';
                $credit_limit    = 0;
                $expiration_date = '';
                $rank            = '';
                $client_name     = '';
                $size = 0;
                if (($handle = fopen($csv_url, "r")) !== FALSE) {

                    while (($data = fgetcsv($handle)) !== FALSE) { // if data is not end row

                        if ($row >= 1) {
                            $size               = sizeof($data);

                            if (sizeof($data) != 77) {

                                return response()->json([
                                    'status_code' =>  400,
                                    'message'     =>  trans('message.import_file_risumon')

                                ]);
                            }

                            $client_name        = mb_convert_encoding($data[0], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $tsr_code           = $data[1];
                            $client_address     = mb_convert_encoding($data[2], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $tel                = $data[4];
                            $corporation_num    = mb_convert_encoding($data[74], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $closing_month      = mb_convert_encoding($data[17], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $get_time           = $data[12];
                            $rank               = $data[13];
                            $credit_limit       = $data[21];
                            /* $expiration_date    = $this->customer_service->getRenewTime($get_time,$rank);*/
                        }

                        $row++;
                    }

                    fclose($handle);
                }


                unlink($csv_url); //delete file uploaded

                $customer = Customer_MST::where('id', $client_id)
                    ->first();

                $note1 = null;
                $note2 = null;
                $note3 = null;

                if ($customer->client_name !=  $client_name) {
                    $note1 =  trans('message.credit_client_name') . $customer->client_name . "/" . $client_name;
                }

                if ($customer->corporation_num !=  $corporation_num  || $customer->corporation_num == null) {
                    $note2 = trans('message.credit_corporation_num') . $customer->corporation_num . "/"  . $corporation_num;
                }

                if ($customer->tsr_code !=  $tsr_code || $customer->tsr_code == null) {
                    $note3 = trans('message.credit_tsr_code') . $customer->tsr_code . "/"  . $tsr_code;
                }

                $note = $note1 . $note2 . $note3;
                return response()->json([  // return error detail to view
                    'size_l'            =>  $size,
                    'get_time'          =>  $get_time,
                    'rank'              =>  $rank,
                    'credit_limit'      =>  $credit_limit,
                    'expiration_date'   =>  $expiration_date,
                    'client_name'       =>  $request->client_name,
                    'credit_division'   =>  '1',
                    'note'              =>  $note
                ]);
            } catch (Exception $e) {

                throw new Exception($e);
            }
        }
    }

    public function edit(Request $request)
    {

        $credit = Credit_MST::where('id', $request->id)
            ->first();
        //取引想定額
        $transaction_expect = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
            ->where('client_id', $credit->client_id)
            ->where('project_mst.status', 'true')
            ->sum('transaction_money');

        //単発
        $transaction_shot = Project_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
            ->where('client_id', $credit->client_id)
            ->where('project_mst.status', 'true')
            ->where('project_mst.once_shot', 'true')
            ->sum('transaction_shot');

        $transaction =  $transaction_expect + $transaction_shot;

        return view('credit.edit', ['credit' => $credit, 'transaction'    => $transaction]);
    }

    //TSR　与信有効期限の取得
    public function getRenewTime_TSR($get_time, $rank)
    {


        $newDate        = date("Y-m-d", strtotime($get_time));
        $d              = date_parse_from_format("Y-m-d", $newDate);
        $month          = $d["month"];
        $day            = $d["day"];
        $year           = $d["year"];

        //20200904 チン　fix bug rank = 48
        if ($rank >= 48) {

            $year       = $year + 1;
            $month_next = 9;
            $renew_date = $year . '-' . $month_next . '-01';

            $time = Carbon::parse($renew_date)->format('Y-m-d');

            return $time;
        } else {

            if ($month <= 2) {

                $month_next = 3;
            } elseif ($month <= 8) {

                $month_next = 9;
            } elseif ($month <= 12) {

                $year       = $year + 1;
                $month_next = 3;
            }

            $renew_date = $year . '-' . $month_next . '-01';

            $time = Carbon::parse($renew_date)->format('Y-m-d');

            return $time;
        }
    }


    // public function credit_rank_TSR($rank)
    // {
    //     if ($rank >= 80) {

    //         $credit_rank = '☆☆☆☆☆';
    //     } elseif ($rank >= 65) {

    //         $credit_rank = '☆☆☆☆';
    //     } elseif ($rank >= 50) {

    //         $credit_rank = '☆☆☆';
    //     } elseif ($rank >= 30) {

    //         $credit_rank = '☆☆';
    //     } elseif ($rank < 30) {

    //         $credit_rank = '☆';
    //     }


    //     return $credit_rank;
    // }
}
