<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Customer_MST;
use App\Receivable_MST;
use App\Rule_action;
use App\Menu;
use Auth;
use Response;
use Excel;
use Helper;
use Exception;
use DB;
use App\Events\Event;
use App\Events\LogEvent;

class ReceivableController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');

    }

    //   public function index(){


    //      $receivable = Receivable_MST::all();

    //    return view('receivable.index',["receivable" => $receivable]);


    // }
    public function index(Request $request)
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
            $target_data_st   = $request->target_data_st;
            $target_data_en   = $request->target_data_en;

            $validator = $this->validateData($request);
            if ($validator->fails()) {
                $receivable = Receivable_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'account_receivable.client_id')
                    ->orderBy('customer_mst.client_code', 'asc')
                    ->orderBy('account_receivable.target_data', 'desc')
                    ->where('account_receivable.company_id', $company_id)
                    ->select('account_receivable.*')
                    ->paginate(30);

                $errors = $validator->errors();
                $requestOld = $request->all();
                return view('receivable.index', [
                    'requestOld' => $requestOld,
                    "errors" => $errors,
                    "receivable"       => $receivable,
                    "company_id"       => session('company_id_r'),
                    "client_code"      => $client_code,
                    "client_name_kana" => $client_name_kana,
                    "corporation_num"  => $corporation_num,
                    "client_name"      => $client_name,
                    "target_data_st"   => $target_data_st,
                    "target_data_en"   => $target_data_en
                ]);
            }

            session(['company_id_r'       => $company_id]);
            session(['client_code_r'      => $client_code]);
            session(['client_name_r'      => $client_name]);
            session(['client_name_kana_r' => $client_name_kana]);
            session(['corporation_num_r'  => $corporation_num]);
            session(['target_data_st_r'   => $target_data_st]);
            session(['target_data_en_r'   => $target_data_en]);

            $receivable          = $this->search($company_id, $client_code, $client_name, $client_name_kana, $corporation_num, $target_data_st, $target_data_en);
            return view('receivable.index', [
                "receivable"       => $receivable,
                "company_id"       => session('company_id_r'),
                "client_code"      => session('client_code_r'),
                "client_name_kana" => session('client_name_kana_r'),
                "corporation_num"  => session('corporation_num_r'),
                "client_name"      => session('client_name_r'),
                "target_data_st"   => session('target_data_st_r'),
                "target_data_en"   => session('target_data_en_r')
            ]);
        }

        if (isset($request->client_id)) {

            $client_id   =   $request->client_id;
            $customer = Customer_MST::where('id', $client_id)->first();
            session(['company_id_r'       => $customer->company_id]);
            session(['client_code_r'      => $customer->client_code_main != null ? $customer->client_code_main : $customer->client_code]);
            session(['client_name_r'      => null]);
            session(['client_name_kana_r' => null]);
            session(['corporation_num_r'  => null]);
            session(['target_data_st_r'   => null]);
            session(['target_data_en_r'   => null]);
        }

        //セッションの情報で検索　(他の画面から遷移した時)
        if (
            $request->session()->exists('company_id_r')       ||
            $request->session()->exists('client_code_r')      ||
            $request->session()->exists('client_name_r')      ||
            $request->session()->exists('client_name_kana_r') ||
            $request->session()->exists('corporation_num_r')  ||
            $request->session()->exists('target_data_st_r')   ||
            $request->session()->exists('target_data_en_r')

        ) {

            $condition = $this->searchCostBySession($request);

            $validator = $this->validateData($request);

            if ($validator->fails()) {

                $receivable = Receivable_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'account_receivable.client_id')
                    ->orderBy('account_receivable.target_data', 'desc')
                    ->orderBy('customer_mst.client_code', 'asc')
                    ->orderBy('account_receivable.target_data', 'desc')
                    ->where('account_receivable.company_id', $company_id)
                    ->select('account_receivable.*')
                    ->paginate(30);
            }

            $receivable = $this->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5], $condition[6]);

            return view('receivable.index', [
                "receivable"       => $receivable,
                "company_id"       => session('company_id_r'),
                "client_code"      => session('client_code_r'),
                "client_name_kana" => session('client_name_kana_r'),
                "corporation_num"  => session('corporation_num_r'),
                "client_name"      => session('client_name_r'),
                "target_data_st"   => session('target_data_st_r'),
                "target_data_en"   => session('target_data_en_r')
            ]);
        }

        $receivable = Receivable_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'account_receivable.client_id')
            ->orderBy('account_receivable.target_data', 'desc')
            ->orderBy('customer_mst.client_code', 'asc')
            ->where('account_receivable.company_id', Auth::user()->company_id)
            ->select('account_receivable.*')
            ->paginate(30);

        return view('receivable.index');
    }


    public function searchCostBySession($request)
    {

        $condition = array();
        if ($request->session()->exists('company_id_r')) {

            $company_id = session('company_id_r');
            array_push($condition, $company_id);
        } else {

            $company_id = "";
            array_push($condition, $company_id);
        }

        if ($request->session()->exists('client_code_r')) {

            $client_code = session('client_code_r');
            array_push($condition, $client_code);
        } else {

            $client_code = "";
            array_push($condition, $client_code);
        }

        if ($request->session()->exists('client_name_kana_r')) {

            $client_name_kana  = session('client_name_kana_r');
            array_push($condition, $client_name_kana);
        } else {

            $client_name_kana  = "";
            array_push($condition, $client_name_kana);
        }

        if ($request->session()->exists('corporation_num_r')) {

            $corporation_num  = session('corporation_num_r');
            array_push($condition, $corporation_num);
        } else {

            $corporation_num  = "";
            array_push($condition, $corporation_num);
        }

        if ($request->session()->exists('client_name_r')) {

            $client_name  = session('client_name_r');
            array_push($condition, $client_name);
        } else {

            $client_name  = "";
            array_push($condition, $client_name);
        }

        if ($request->session()->exists('target_data_st_r')) {

            $target_data_st  = session('target_data_st_r');
            array_push($condition, $target_data_st);
        } else {

            $target_data_st  = "";
            array_push($condition, $target_data_st);
        }


        if ($request->session()->exists('target_data_en_r')) {

            $target_data_en  = session('target_data_en_r');
            array_push($condition, $target_data_en);
        } else {

            $target_data_en  = "";
            array_push($condition, $target_data_en);
        }

        return  $condition;
    }



    public function search($company_id, $client_code, $client_name, $client_name_kana, $corporation_num, $target_data_st, $target_data_en)
    {

        //大元の検索条件
        $receivable = Receivable_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'account_receivable.client_id')
            ->orderBy('account_receivable.target_data', 'desc')
            ->orderBy('customer_mst.client_code', 'asc')
            ->select('account_receivable.*')
            ->when($client_code != "", function ($query) use ($client_code) {

                return $query->where('customer_mst.client_code', $client_code)->orwhere('customer_mst.client_code_main', $client_code);
            });

        //検索の条件が有れば、条件をｾｯﾄする
        if ($company_id != "") {

            $receivable = $receivable->where('account_receivable.company_id', $company_id);
        }

        // if($client_code != ""){

        //     $receivable = $receivable->where('customer_mst.client_code',$client_code)
        //                 ->orwhere('customer_mst.client_code_main',$client_code);


        //  }

        if ($client_name != "") {

            $receivable = $receivable->where('customer_mst.client_name', 'like', "$client_name%");
        }

        if ($client_name_kana != "") {

            $receivable = $receivable->where('customer_mst.client_name_kana', 'like', "$client_name_kana%");
        }

        if ($corporation_num != "") {

            $receivable = $receivable->where('customer_mst.corporation_num', $corporation_num);
        }

        if ($target_data_st != "" & $target_data_en != "") {

            $receivable = $receivable->whereBetween('account_receivable.target_data', [$target_data_st . "/01", $target_data_en . "/01"]);
        } elseif ($target_data_st == "" & $target_data_en != "") {

            $receivable = $receivable->whereBetween('account_receivable.target_data', ["2000/01/01", $target_data_en . "/01"]);
        } elseif ($target_data_st != "" & $target_data_en == "") {

            $receivable = $receivable->whereBetween('account_receivable.target_data', [$target_data_st . "/01", '9999/12/31']);
        }

        //検索結果
        $receivable          = $receivable->paginate(30);

        return $receivable;
    }
    public function validateData(Request $request)
    {
        /*エラーのチェック 日付型*/
        $validator = Validator::make($request->all(), [

            'target_data_st'        => 'nullable|date_format:Y/m',
            'target_data_en'        => 'nullable|date_format:Y/m',

        ], [

            'target_data_st.date_format'       => trans('validation.target_data_st'),
            'target_data_en.date_format'       => trans('validation.target_data_en'),

        ]);

        $errors = $validator->errors();

        return $validator;
    }
}
