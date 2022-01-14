<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Service\CustomerService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Rules\CheckUniqueStrCode;
use App\Rules\CheckUniqueCorporationNum;
use App\Rules\CheckClientName;
use App\Rules\CheckClientNameKana;
use App\Repositories\CreditInforRepository;
use App\Customer_MST;
use App\Customer_name_MST;
use App\Credit_check;
use App\Project_MST;
use App\Rules\CompareUpdateTime;
use Auth;
use Carbon\Carbon;
use DB;
use Common;
use Session;
use Crofun;

class CustomerController extends Controller
{

    protected $customer_service;
    protected $creditInforRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CustomerService $customer_service, CreditInforRepository $creditInforRepository)
    {
        //$this->middleware('auth');
        $this->customer_service        = $customer_service;
        //取引想定額の取得　顧客単位の想定額　＋　単発想定額
        $this->creditInforRepository   = $creditInforRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        session(['customer' => array('page' => $request->page)]);
        if ($request->isMethod('post')) {
            $company_id    = $request->company_id;
            $customer_code = $request->customer_code;
            $customer_name = mb_convert_kana($request->customer_name, 'rhk');
            $personal_code = $request->personal_code;
            $sale          = $request->sale;
            $status        = $request->status;
            $fgl_flag        = $request->fgl_flag == 'on' ? true : false;

            session(['company_id_c'     => $company_id]);
            session(['customer_code_c'  => $customer_code]);
            session(['customer_name_c'  => $customer_name]);
            session(['personal_code_c'  => $personal_code]);
            session(['sale'             => $sale]);
            session(['status'           => $status]);
            session(['fgl_flag'           => $fgl_flag]);

            $customers = $this->customer_service->search($company_id, $customer_code, $customer_name, $personal_code, $sale, $status,false, $fgl_flag);

            return view('customer.index', ['customers' => $customers]);
        }
        //検索条件存在しているかを確認
        if ($this->customer_service->checkSessionExist($request) == 1) {

            $condition = $this->customer_service->getSearchCondition($request);

            $customers = $this->customer_service->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5], false, $condition[6]);
            return view('customer.index', ['customers' => $customers]);
        }

        $company_id_R  =  Auth::user()->company_id;

        $customers = $this->customer_service->search($company_id_R, null, null, null, null, null);
        return view('customer.index', ['customers' => $customers]);
    }

    /*
    * edit customer informatin
    * $request : form data
    * return update customer information status
    */


    public function search(Request $request)
    {
        if ($request->isMethod('post')) {
            $company_id    = $request->company_id;
            $customer_code = $request->customer_code;
            $customer_name = $request->customer_name;
            $personal_code = $request->personal_code;
            $sale          = $request->sale;
            $status        = $request->status;

            session(['company_id_c'     => $company_id]);
            session(['customer_code_c'  => $customer_code]);
            session(['customer_name_c'  => $customer_name]);
            session(['personal_code_c'  => $personal_code]);
            session(['sale'             => $sale]);
            session(['status'           => $status]);
            $customers = $this->customer_service->search($company_id, $customer_code, $customer_name, $personal_code, $sale, $status);

            return response()->json(['customers' =>  $customers]);
        }
    }

    //エラーチェック
    public function validationDataInput(Request $request, $update_time = null)
    {
                $validator = Validator::make($request->all(), [
            'update_time'           => [new CompareUpdateTime($update_time)],
            'company_id'            => 'required',
            'client_name'           => [
                'required',
                new CheckClientName($request->company_id, $request->id)
            ],
            'client_name_kana_conversion'      =>
            [
                'required',
                'regex:/^[ｦ-ﾟ\x20]*$/u',
                new CheckClientNameKana($request->company_id, $request->id)
            ],
            'corporation_num'       => [
                'nullable',
                'max:13',
                'min:13',
                'regex:/^[0-9]*$/u',
                new CheckUniqueCorporationNum($request->company_id, $request->id)
            ],
            'tsr_code'              => [
                'nullable',
                'max:9',
                'min:9',
                'regex:/^[0-9]*$/u',
                new CheckUniqueStrCode($request->company_id, $request->id)
            ],
            'sale'                  => 'required',
            'group_id'              => 'required',
            'credit_expect'         => 'required_if:rank,A,B,C,D,E1,E2,F1,F2'
        ], [
            'company_id.required'         => trans('validation.company_code'),
            'client_name.required'        => trans('validation.client_name'),
            'client_name_kana_conversion.required'  => trans('validation.customer_name_kana'),
            'client_name_kana_conversion.regex'     => trans('validation.client_name_kana_hankaku'),
            'client_name_ab.required'     => trans('validation.client_name_ab'),
            'tsr_code.required'           => trans('validation.tsr_code'),
            'tsr_code.min'                => trans('validation.tsr_code_lenght'),
            'tsr_code.max'                => trans('validation.tsr_code_lenght'),
            'tsr_code.regex'              => trans('validation.tsr_code_int'),
            'client_code.required'        => trans('validation.client_code'),
            'client_address.required'     => trans('validation.client_address'),
            'corporation_num.max'         => trans('validation.corporation_num_lenght'),
            'corporation_num.min'         => trans('validation.corporation_num_lenght'),
            'corporation_num.regex'       => trans('validation.corporation_num_int'),
            'sale.required'               => trans('validation.status'),
            'credit_expect.required_if'   => trans('validation.credit_expect'),
            'group_id.required'           => trans('validation.group_id'),
        ]);

        return $validator;
    }

    /*
    * edit customer informatin
    * $request : form data
    * return update customer information status
    */

    public function edit(Request $request)
    {
        session(['client_id'     => $request->id]);
        $client_id     =  $request->id;
        $transaction =  $this->creditInforRepository->getTransactionMoney($request->id);

        if ($request->isMethod('post')) {
            DB::beginTransaction();
            DB::select('LOCK customer_mst');
            $transaction =  $this->creditInforRepository->getTransactionMoney($request->id);
            //カナに変換する
            $client_name_kana_conversion     =  mb_convert_kana($request->client_name_kana, 'rhk');
            $client_name_kana_conversion     =  preg_replace(array("/ /", "/　/"), "", $client_name_kana_conversion);

            $request->merge([
                'client_name_kana_conversion' => $client_name_kana_conversion,
            ]);
            $update_time = Customer_MST::where('id', $request->id)->first()->updated_at;
            $validator     =  $this->validationDataInput($request, $update_time); // check form's data rule
            $client_id     =  $request->id;
            $customer      =  $this->customer_service->getCustomerById($client_id); // get customer object by id
            $original_status = $customer->status;
            $old_date      = json_encode($customer);
            $old_name      =  $customer->client_name_kana;
            $company_id              = $request->company_id;
            $client_name             = $request->client_name;
            $client_name_ab          = $request->client_name_ab;
            $client_name_kana        = $request->client_name_kana_conversion;
            $corporation_num         = $request->corporation_num;
            $client_address          = $request->client_address;
            $closing_time            = $request->closing_month;
            $tsr_code                = $request->tsr_code;
            $akikura_code            = $request->akikura_code;
            $collection_site         = $request->collection_site;
            $transferee_name         = $request->transferee_name;
            $sale                    = $request->sale;
            $status                  = $request->status;
            $note                    = $request->note;
            // checkbox
            $transferee              = $request->transferee;
            $antisocial              = $request->antisocial;
            $credit                  = $request->credit;
            $fgl_flag                  = $request->fgl_flag;
            $request_group           = $request->group_id;

            $transferee = ($transferee == 'on') ? true : false;
            $antisocial = ($antisocial == 'on') ? true : false;
            $credit = ($credit == 'on') ? true : false;
            $fgl_flag = ($fgl_flag == 'on') ? true : false;
            //データロックをしている。
            if ($request->client_code_main != null) {
                //最大値+1の取得
                $num =  Crofun::customer_number_create_main($company_id);
                $customer->client_code_main  =  $num;
                $pattern = "/^[0-9]/";
                if (preg_match($pattern, $request->client_code_main)) {
                } else {
                    $validator->errors()->add('client_code_main', trans('validation.client_code_main'));
                }
            }

            $customer->client_name       =  $client_name;
            $customer->client_name_ab    =  $client_name_ab;
            $customer->client_name_kana  =  $client_name_kana;
            $customer->corporation_num   =  $corporation_num;
            $customer->client_address    =  $client_address;
            $customer->closing_time      =  $closing_time;
            $customer->tsr_code          =  $tsr_code;
            $customer->akikura_code      =  $akikura_code;
            $customer->collection_site   =  $collection_site;
            $customer->transferee_name   =  $transferee_name;
            $customer->sale              =  $sale;
            $customer->status            =  $status;
            $customer->note              =  $note;
            $customer->transferee        =  $transferee;
            $customer->antisocial        =  $antisocial;
            $customer->credit            =  $credit;
            $customer->fgl_flag            =  $fgl_flag;
            $customer->representative_name = $request->representative_name;
            $customer->request_group     =  $request_group;
            $customer->updated_at        =  $request->update_time;

            if ($validator->fails()) {
                $errors = $validator->errors();
                return view('customer.edit', ['customer' => $customer, "errors" => $errors, 'transaction' => $transaction]);
            }

            try {
                if ($customer->update()) {
                    // 会社名が変更されているか。　されている場合旧社名を保存
                    if (!$this->companyNameCompare($client_name_kana, $old_name)) {
                        $customer_name                        = new Customer_name_MST(); // customer's name for search
                        $customer_name->id                    = $this->getMaxIdCustomerName()[0]->max + 1;
                        $customer_name->client_id             = $customer->id;
                        $customer_name->client_name_s         = $customer->client_name;
                        $customer_name->client_name_hankaku_s = $customer->client_name_kana;
                        $customer_name->del_flag              = false;
                        $customer_name->save();
                    }

                    if ($customer->client_code_main != null) {
                        $code  =  $customer->client_code_main;
                    } else {
                        $code  =  $customer->client_code;
                    }

                    if($customer->status == 1 && $original_status != 1){
                        //変更した際、その顧客に紐づくプロジェクトが取引終了になる。
                        $projects = Project_MST::where('client_id',$client_id)->where('status', true)->get();
                        $old_Rule_log = array();
                        foreach($projects as $project){
                            $old_Rule_log[] = $project->project_name;
                            $project->status = false;
                            $project->update();
                        }
                        Crofun::log_create(Auth::user()->id, $customer->id, config('constant.PROJECT'), config('constant.operation_OFF'), config('constant.CLIENT_EDIT'), $customer->company_id, $customer->client_name, $code, json_encode($old_Rule_log), null);
                    }
                }

                Crofun::log_create(Auth::user()->id, $customer->id, config('constant.CLIENT'), config('constant.operation_UPDATE'), config('constant.CLIENT_EDIT'), $customer->company_id, $customer->client_name, $code, json_encode($customer), $old_date);
                DB::commit(); // if not exist exception then commit all transaction
                DB::select('COMMIT');
                Session::flash('message', trans('message.update_success'));
                return view('customer.edit', ['customer' => $customer, 'transaction' => $transaction])->with('message', trans('message.edit_success'));
            } catch (Exception $e) {
                DB::rollBack();
                DB::select('ROLLBACK');
                throw new Exception($e);
            }
        }
        $customer_id =  $request->id;
        $customer    =  $this->customer_service->getCustomerById($customer_id);
        $transaction =  $this->creditInforRepository->getTransactionMoney($request->id);
        return view('customer.edit', ['customer' => $customer, 'transaction' => $transaction]);
    }

    public function view(Request $request)
    {
        $customer_id =  $request->id;
        $customer    =  $this->customer_service->getCustomerById($customer_id);
        $transaction =  $this->creditInforRepository->getTransactionMoney($request->id);
        return view('customer.view', ['customer' => $customer, 'transaction' => $transaction]);
    }

    /*
    * compare newname and oldname , if different then return false if not different return true
    * $new_name form's value
    * $old_name value get from database
    */
    public function companyNameCompare($new_name, $old_name)
    {
        if (strcmp(trim($new_name), trim($old_name)) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * create new customer' information
    * $new_name form's value
    * $old_name value get from database
    */

    public function create(Request $request, $id = null)
    {
        if ($request->isMethod('post')) {
            //カナに変換する
            $client_name_kana_conversion     =  mb_convert_kana($request->client_name_kana, 'rhk');
            $client_name_kana_conversion     =  preg_replace(array("/ /", "/　/"), "", $client_name_kana_conversion);

            //与信情報
            $rank_conversion             = $this->customer_service->credit_rank($request->rank);
            $renew_time                  = $this->customer_service->getRenewTimeRM(
                $request->get_time,
                $request->rank,
                $request->credit_expect,
                $request->credit_limit
            );
            $credit_limit               = mb_convert_kana($request->credit_limit, 'rn');
            $credit_limit               = (int)filter_var($credit_limit, FILTER_SANITIZE_NUMBER_INT);

            $request->merge([
                'client_name_kana_conversion' => $client_name_kana_conversion,
                'rank_conversion' => $rank_conversion,
                'renew_time_conversion' => $renew_time,
                'credit_limit' => $credit_limit
            ]);
            try {

                session()->flashInput($request->input());
                //エラーチェック
                $validator     =  $this->validationDataInput($request);

                if ($validator->fails()) {

                    $errors = $validator->errors();

                    return view('customer.create', ["errors" => $errors]);
                }

                //データのセット準備
                $company_id              = $request->company_id;
                $client_code             = $request->client_code;
                $client_name             = $request->client_name;
                $client_name_ab          = $request->client_name_ab;
                $client_name_kana        = $request->client_name_kana_conversion;
                $corporation_num         = $request->corporation_num;
                $client_address          = $request->client_address;
                $closing_time            = $request->closing_month;
                $tsr_code                = $request->tsr_code;
                $akikura_code            = $request->akikura_code;
                $collection_site         = $request->collection_site;
                $transferee_name         = $request->transferee_name;
                $sale                    = $request->sale;
                $status                  = $request->status;
                $note                    = $request->note;
                // checkbox
                $transferee              = $request->transferee;
                $antisocial              = $request->antisocial;
                $credit                  = $request->credit;
                $fgl_flag                  = $request->fgl_flag;
                $request_group           = $request->group_id;

                $transferee = ($transferee == 'on') ? true : false;
                $antisocial = ($antisocial == 'on') ? true : false;
                $credit = ($credit == 'on') ? true : false;
                $fgl_flag = ($fgl_flag == 'on') ? true : false;

                DB::beginTransaction();
                //データのセット
                $customer                    = new Customer_MST(); // create new object
                $customer->id                =  $this->getMaxId()[0]->max + 1;
                $customer->company_id        =  $company_id;
                $customer->client_code       =  $client_code;
                $customer->client_name       =  $client_name;
                $customer->client_name_ab    =  $client_name_ab;
                $customer->client_name_kana  =  $client_name_kana;
                $customer->corporation_num   =  $corporation_num;
                $customer->client_address    =  $client_address;
                $customer->closing_time      =  $closing_time;
                $customer->tsr_code          =  $tsr_code;
                $customer->akikura_code      =  $akikura_code;
                $customer->collection_site   =  $collection_site;
                $customer->transferee_name   =  $transferee_name;
                $customer->sale              =  $sale;
                $customer->status            =  $status;
                $customer->note              =  $note;
                $customer->transferee        =  $transferee;
                $customer->antisocial        =  $antisocial;
                $customer->credit            =  $credit;
                $customer->fgl_flag          =  $fgl_flag;
                $customer->representative_name = $request->representative_name;
                $customer->request_group     =  $request_group;
                $customer->save();

                $credit_limit                  = $request->credit_limit;
                $get_time                      = $request->get_time;
                $credit_expect                 = $request->credit_expect;
                //与信情報が存在するか
                if ($request->check_credit == 1) { // if is existed credit data then save to database
                    //与信情報のセット
                    if ($request->rank == 'G') {
                        $credit_check                  = new Credit_check();
                        $credit_check->id              = $this->getMaxIdCredit()[0]->max + 1;
                        $credit_check->client_id       = $customer->id;
                        $credit_check->company_id      = $company_id;
                        $credit_check->get_time        = $get_time;
                        $credit_check->credit_limit    = $credit_limit;
                        $credit_check->rank            = $rank_conversion;
                        $credit_check->credit_division = $sale;
                        $credit_check->save();
                    } else {
                        $credit_check                  = new Credit_check();
                        $credit_check->id              = $this->getMaxIdCredit()[0]->max + 1; // get max id of table
                        $credit_check->client_id       = $customer->id;
                        $credit_check->company_id      = $company_id;
                        $credit_check->get_time        = $get_time;

                        if ($credit_limit != "") {
                            $credit_check->credit_limit    = $credit_limit * 1000;
                        } else {
                            $credit_check->credit_limit    = 0;
                        }

                        $credit_check->rank            = $rank_conversion;
                        $credit_check->credit_division = $sale;
                        $credit_check->expiration_date = $renew_time;
                        $credit_check->credit_expect   = $credit_expect * 1000;

                        $credit_check->save();
                    }
                    Crofun::log_create(Auth::user()->id, $credit_check->id, config('constant.CREDIT'), config('constant.operation_CRATE'), config('constant.CLIENT_ADD'), $credit_check->company_id, $customer->client_name, $customer->client_code, json_encode($credit_check), null);
                }

                //顧客名テーブルにデータ格納
                $customer_name                        = new Customer_name_MST();
                $customer_name->id                    = $this->getMaxIdCustomerName()[0]->max + 1;
                $customer_name->client_id             = $customer->id;
                $customer_name->client_name_s         = $customer->client_name;
                $customer_name->client_name_hankaku_s = $customer->client_name_kana;
                $customer_name->del_flag              = false;
                $customer_name->save();

                Crofun::log_create(Auth::user()->id, $customer->id, config('constant.CLIENT'), config('constant.operation_CRATE'), config('constant.CLIENT_ADD'), $customer->company_id, $customer->client_name, $customer->client_code, json_encode($customer), null);
                DB::commit();
                return back()->with('message', trans('message.save_success'));
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception($e);
            }
        }
        return view('customer.create');
    }
    // upload customer's file information
    /*
    * リスモンからのcsvファイルをアップロード
    * $new_name form's value
    * return ファイルの内容を取得してデータベースに蓄積
    */
    public function upload(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'csv'                  => 'required|mimes:csv,txt',
                'credit_expect'        => 'required|regex:/^[0-9]*$/u'

            ], [
                'csv.required'               => trans('validation.company_name'),
                'csv.mimes'                  => trans('validation.file_upload_fomat'),
                'credit_expect.required'     => trans('validation.credit_expect'),
                'credit_expect.regex'        => trans('validation.credit_expect_format')

            ]);
            //エラーチェック
            if ($validator->fails()) { // if has errors the return code 302
                $errors = $validator->errors();
                if ($errors->has('csv')) {
                    return response()->json([
                        'status'   => 302,
                        'errors'   => $errors
                    ]);
                }

                if ($errors->has('credit_expect')) {
                    return response()->json([
                        'status'   => 300,
                        'errors'   => $errors
                    ]);
                }
            }

            try {
                //ファイルの取込
                $csv = Input::file('csv');
                $credit_expect = $request->credit_expect;
                //パブリックのストレージとして、取込ファイルを取得する
                Storage::disk('public')->put($csv->getClientOriginalName(),  File::get($csv));
                // save file wa uloaded to public disk
                //Pathの指定
                $csv_url = public_path() . '/uploads/' . $csv->getClientOriginalName();
                mb_language("Japanese");
                $row = 0;
                $customer                     = new Customer_MST(); // create new customer object
                //読み取り専用でファイルを開く
                if (($handle = fopen($csv_url, "r")) !== FALSE) {
                    //ファイルを配列化し、データを参照する
                    while (($data = fgetcsv($handle)) !== FALSE) { // if data is not end row
                        if ($row >= 1) {
                            $client_name        = mb_convert_encoding($data[0], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $tsr_code           = $data[1];
                            $client_address     = mb_convert_encoding($data[2], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $tel                = $data[4];
                            $corporation_num    = mb_convert_encoding($data[74], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $closing_month      = mb_convert_encoding($data[17], 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                            $get_time           = $data[12];
                            $rank               = $data[13];
                            $credit_limit       = $data[21];

                            $customer->client_name        =  $client_name;
                            $customer->tsr_code           =  $tsr_code;
                            $customer->client_address     =  $client_address;
                            $customer->corporation_num    =  $corporation_num;
                            $customer->closing_month      =  $closing_month;
                            $customer->get_time           =  $get_time;
                            $customer->rank               =  $rank;
                            $customer->credit_limit       =  $credit_limit;
                            $expiration_date              =  $this->customer_service->getRenewTimeRM(
                                $get_time,
                                $rank,
                                $credit_expect,
                                $credit_limit
                            );
                        }
                        $row++;
                    }
                    fclose($handle);
                }
                unlink($csv_url); //delete file uploaded
                return response()->json(['csv' =>  $customer, 'expiration_date' =>  $expiration_date]);
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
    }
    // dowload csv from search screen
    /*
    * create csv file at search screen
    * $request :search condition had been saved in session
    * return sream dowload
    * 検索画面からの出力
    */
    public function getCsv1(Request $request)
    {
        try {
            $file_name = '顧客情報_' . Common::getToDayCSV();
            $callback  = $this->customer_service->getCustomerData($request, 'list'); // call get customer data function from service
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
    // dowload csv from update information screen

    /*
    * create csv file at update screen
    * $request: customer's id
    * return sream dowload
    * 編集・参照画面からの出力
    */
    public function getCsv2(Request $request)
    {
        try {
            $client_id = $request->client_id;
            $file_name = '顧客情報_' . $this->getCustomerName($client_id) . '_' . Common::getToDayCSV();
            $callback  = $this->customer_service->getOnceCustomerData($client_id, 'edit');
            // call get customer's strem data from service
            $headers   = array(
                "Content-sale" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $file_name . '.csv',
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
        } catch (Exception $e) {

            throw new Exception($e); // if has exception then break and handout

        }
        return response()->stream($callback, 200, $headers);
    }

    // get max id from table
    public function getMaxId()
    {
        $id  = DB::select('select MAX(id) from customer_mst');
        return $id;
    }

    public function getMaxIdCustomerName()
    {
        $id  = DB::select('select MAX(id) from customer_name');
        return $id;
    }

    public function getMaxIdCredit()
    {
        $id  = DB::select('select MAX(id) from credit_check');
        return $id;
    }


    public function getCustomerName($id)
    {
        $customer = Customer_MST::where('id', $id)->first();
        return $customer->client_name_ab;
    }

    //仮コード最大値+1
    public function getCustomerCode(Request $request)
    {
        $company_id = $request->company_id;
        $num        =  Crofun::customer_number_create($company_id);
        return response()->json(['num' =>  $num]);
    }

    //顧客コード最大値+1
    public function changeCode(Request $request)
    {
        $company_id = $request->company_id;
        $num =  Crofun::customer_number_create_main($company_id);
        return response()->json(['num' =>  $num]);
    }

    public function checkProjectNotEnd(Request $request)
    {
        $customer_id = $request->customer_id;
        $status      = Crofun::checkProjectIsEnd($customer_id);
        return response()->json(
            [
                'status'  => $status,
                'message' => trans('message.customer_close')

            ]
        );
    }

    public function checkCustomerIsEnd(Request $request)
    {
        $status = 0;
        $customer_id = $request->customer_id;
        $customer    = Customer_MST::where('id', $customer_id)->first();

        if ($customer->status == 4) {
            $status = 1;
        }

        return response()->json(
            [
                'status'  => $status,
                'message' => trans('message.project_close')
            ]
        );
    }
}
