<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Http\Request;
use App\Service\CustomerService;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Credit_MST;
use App\Customer_MST;
use Auth;
use Carbon\Carbon;
use Response;
use Common;
use Illuminate\Support\Facades\Session;

class CreditSearchController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {

            $validator = $this->validationDataInput($request);

            if ($validator->fails()) {
                $errors     = $validator->errors();
                // $usr_id        = Auth::user()->id;
                // $company_id_1  = Common::checkUserCompany($usr_id);
                $company_id_1  = Array(1000);
                $customers = $this->customer_service->crediteslatest($company_id_1);
                $customers = $customers->paginate(25);
                $requestOld = $request->all();
                return view('credit.search', ['errors' => $errors, 'customers' => $customers, 'requestOld' => $requestOld]);
            }

            $company_id       = $request->company_id;
            $client_name      = mb_convert_kana($request->client_name, 'rhk');
            $client_code      = $request->client_code;
            $corporation_num  = $request->corporation_num;
            session(['start_time_crdit'       => $request->start_time]);
            session(['end_time_crdit'         => $request->end_time]);
            $start_time       = !empty($request->start_time) ? Carbon::parse($request->start_time)->format('Y-m-d') : null;
            $end_time         = !empty($request->end_time) ? Carbon::parse($request->end_time)->format('Y-m-d') : null;

            session(['company_id_crdit'       => $company_id]);
            session(['client_name_crdit'      => $client_name]);
            session(['client_code_crdit'      => $client_code]);
            session(['corporation_num_crdit'  => $corporation_num]);
            // session(['start_time_crdit'       => $start_time]);
            // session(['end_time_crdit'         => $end_time]);





            // $customers = Customer_MST::with('company_name')->get();
            $customers = $this->search($company_id, $client_name, $client_code, $corporation_num, $start_time, $end_time);
            return view('credit.search', ['customers' => $customers]);
        }

        if ($request->client_id != null) {

            $client_id   =   $request->client_id;
            $customer = Customer_MST::where('id', $client_id)->first();
            session(['company_id_crdit'      => $customer->company_id]);
            session(['client_name_crdit'     => null]);
            session(['client_code_crdit'     => $customer->client_code_main != null ? $customer->client_code_main : $customer->client_code]);
            session(['corporation_num_crdit' => null]);
            session(['start_time_crdit'      => null]);
            session(['end_time_crdit'        => null]);
        }

        if ($this->checkSessionExist($request) == 1) {

            $condition = $this->getSearchCondition($request);
            $customers      = $this->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5]);


            return view('credit.search', ['customers' => $customers]);
        }

        $usr_id        = Auth::user()->id;
        $company_id_1  = Auth::user()->company_id;
        //デフォルト画面に本務の会社を表示させるためにセッションとして本務を持たせる
        session(['company_id_crdit'      => $company_id_1]);

        $customers = $this->customer_service->crediteslatest(array($company_id_1));

        $customers = $customers->paginate(25);
        return view('credit.search', ['customers' => $customers]);
    }

    public function search($company_id, $client_name, $client_code, $corporation_num, $start_time, $end_time)
    {

        $usr_id        = Auth::user()->id;
        $company_id_1  = Common::checkUserCompany($usr_id);

        $customers = $this->customer_service->crediteslatest($company_id_1);


        if ($company_id != "") {

            $customers = $customers->where('customer_mst.company_id', $company_id);
        }

        if ($client_code != "") {


            if (strpos($client_code, 'K') !== false) {

                $customers = $customers->where('client_code', $client_code);
            } else {


                $customers = $customers->where('client_code_main', $client_code);
            }
        }

        if ($client_name != "") {

            $customers = $customers->where('client_name_kana', 'like', "%$client_name%");
        }

        if ($corporation_num != "") {

            // $customers = $customers->where('corporation_num',$corporation_num);
            $customers = $customers->where('corporation_num', $corporation_num);
        }
        //fix me
        // $start_time   = ($start_time == null) ? Common::get_min_expiration_date_credit() : $start_time;
        // $end_time     = ($end_time   == null) ? Common::get_max_expiration_date_credit() : $end_time;

        if ($start_time != null) {
            $customers = $customers->where('expiration_date', '>=' ,$start_time);
        }

        if ($end_time != null) {
            $customers = $customers->where('expiration_date', '<=' ,$end_time);
        }

        // $customers = $customers->whereBetween('expiration_date', [$start_time, $end_time]);
        return $customers->paginate(25);
    }

    public function checkSessionExist($request)
    {

        if (
            $request->session()->exists('company_id_crdit') ||
            $request->session()->exists('client_name_crdit') ||
            $request->session()->exists('client_code_crdit')  ||
            $request->session()->exists('corporation_num_crdit')     ||
            $request->session()->exists('start_time_crdit') ||
            $request->session()->exists('end_time_crdit')
        ) {

            return 1;
        } else {

            return 0;
        }
    }

    public function getSearchCondition($request)
    {

        $condition = array();

        if ($request->session()->exists('company_id_crdit')) {

            array_push($condition, session('company_id_crdit'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('client_name_crdit')) {

            array_push($condition, session('client_name_crdit'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('client_code_crdit')) {

            array_push($condition, session('client_code_crdit'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('corporation_num_crdit')) {

            array_push($condition, session('corporation_num_crdit'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('start_time_crdit')) {

            array_push($condition, session('start_time_crdit'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('end_time_crdit')) {

            array_push($condition, session('end_time_crdit'));
        } else {

            array_push($condition, "");
        }

        return $condition;
    }


    public function validationDataInput(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'start_time'        => 'nullable|date_format:Y/m/d',
            'end_time'          => 'nullable|date_format:Y/m/d',

        ], [

            'start_time.date_format'       => trans('validation.credit_start_time'),
            'end_time.date_format'         => trans('validation.credit_end_time'),

        ]);

        $errors = $validator->errors();

        return $validator;
    }
}
