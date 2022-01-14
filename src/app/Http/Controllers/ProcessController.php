<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Process_MST;
use App\Rule_action;
use App\Menu;
use App\Customer_MST;
use App\Project_MST;
use Auth;
use Response;
use Excel;
use Helper;
use Exception;
use DB;
use Crofun;
use App\Events\Event;
use App\Events\LogEvent;
use App\Group_MST;

class ProcessController extends Controller
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

            //画面からリクエストされている情報
            $company_id       = $request->company_id;
            $headquarter_id   = $request->headquarter_id;
            $department_id    = $request->department_id;
            $group_id         = $request->group_id;
            $client_code      = $request->client_code;
            $client_name_kana = mb_convert_kana($request->client_name_kana, 'rhk');
            $corporation_num  = $request->corporation_num;
            $project_code     = $request->project_code;
            $project_name     = $request->project_name;
            $target_data_st   = $request->target_data_st;
            $target_data_en   = $request->target_data_en;

            $validator = $this->validateData($request);
            if ($validator->fails()) {

                $processe = Process_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'process.client_id')
                    ->where('process.company_id', $company_id)
                    ->orderBy('customer_mst.client_code', 'asc')
                    ->orderBy('process.target_data', 'desc')
                    ->select('process.*')
                    ->paginate(25);

                $errors = $validator->errors();

                return view('process.index', [
                    "errors" => $errors,
                    "processe"       => $processe,
                    "company_id"       => $company_id,
                    "headquarter_id"   => $headquarter_id,
                    "department_id"    => $department_id,
                    "group_id"         => $group_id,
                    "client_code"      => $client_code,
                    "client_name_kana" => $client_name_kana,
                    "corporation_num"  => $corporation_num,
                    "project_code"     => $project_code,
                    "project_name"     => $project_name,
                    "target_data_st"   => $target_data_st,
                    "target_data_en"   => $target_data_en
                ]);
            }

            session(['company_id_pr'       => $company_id]);
            session(['headquarter_id_pr'   => $headquarter_id]);
            session(['department_id_pr'    => $department_id]);
            session(['group_id_pr'         => $group_id]);
            session(['client_code_pr'      => $client_code]);
            session(['client_name_kana_pr' => $client_name_kana]);
            session(['corporation_num_pr'  => $corporation_num]);
            session(['project_code_pr'     => $project_code]);
            session(['project_name_pr'     => $project_name]);
            session(['target_data_st_pr'   => $target_data_st]);
            session(['target_data_en_pr'   => $target_data_en]);


            $processe          = $this->search($company_id, $headquarter_id, $department_id, $group_id, $client_code, $client_name_kana, $project_code, $project_name, $corporation_num, $target_data_st, $target_data_en);


            return view('process.index', [
                "processe"       => $processe,
                "company_id"       => session('company_id_pr'),
                "headquarter_id"   => session('headquarter_id_pr'),
                "department_id"    => session('department_id_pr'),
                "group_id"         => session('group_id_pr'),
                "client_code"      => session('client_code_pr'),
                "client_name_kana" => session('client_name_kana_pr'),
                "corporation_num"  => session('corporation_num_pr'),
                "project_code"     => session('project_code_pr'),
                "project_name"     => session('project_name_pr'),
                "target_data_st"   => session('target_data_st_pr'),
                "target_data_en"   => session('target_data_en_pr')
            ]);
        }

        if ($request->client_id != null) {
            $client_id   =   $request->client_id;
            $customer    = Customer_MST::where('id', $client_id)->first();

            session(['company_id_pr'        => $customer->company_id]);
            session(['headquarter_id_pr'    => null]);
            session(['department_id_pr'     => null]);
            session(['group_id_pr'          => null]);
            session(['client_code_pr'       =>  $customer->client_code_main]);
            session(['client_name_kana_pr'  => null]);
            session(['corporation_num_pr'   => null]);
            session(['project_code_pr'      => null]);
            session(['project_name_pr'      => null]);
            session(['target_data_st_pr'    => null]);
            session(['target_data_en_pr'    => null]);
        }
        if ($request->project_id != null) {

            $project_id  =   $request->project_id;
            $project     = Project_MST::where('id', $project_id)->first();

            session(['company_id_pr'        => $project->company_id]);
            session(['headquarter_id_pr'    => null]);
            session(['department_id_pr'     => null]);
            session(['group_id_pr'          => null]);
            session(['client_code_pr'  => null]);
            session(['client_name_kana_pr'  => null]);
            session(['corporation_num_pr'  => null]);
            session(['project_code_pr'      => $project->project_code]);
            session(['project_name_pr'      => null]);
            session(['target_data_st_pr'     => null]);
            session(['target_data_en_pr'     => null]);
        }

        //セッションの情報で検索　(他の画面から遷移した時)
        if (
            $request->session()->exists('company_id_pr')       ||
            $request->session()->exists('headquarter_id_pr')   ||
            $request->session()->exists('department_id_pr')    ||
            $request->session()->exists('group_id_pr')         ||
            $request->session()->exists('client_code_pr')      ||
            $request->session()->exists('client_name_kana_pr') ||
            $request->session()->exists('corporation_num_pr')  ||
            $request->session()->exists('project_code_pr')     ||
            $request->session()->exists('project_name_pr')     ||
            $request->session()->exists('target_data_st_pr')   ||
            $request->session()->exists('target_data_en_pr')

        ) {
            $validator = $this->validateData($request);
            if ($validator->fails()) {

                $company_id_R  =  Auth::user()->company_id;

                $processe = Process_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'process.client_id')
                    ->where('process.company_id', $company_id_R)
                    ->orderBy('process.target_data', 'desc')
                    ->orderBy('customer_mst.client_code', 'asc')
                    ->select('process.*')
                    ->paginate(25);

                $errors = $validator->errors();

                return view('process.index', [
                    "errors" => $errors,
                    "processe"       => $processe,
                    "company_id"       => session('company_id_pr'),
                    "headquarter_id"   => session('headquarter_id_pr'),
                    "department_id"    => session('department_id_pr'),
                    "group_id"         => session('group_id_pr'),
                    "client_code"      => session('client_code_pr'),
                    "client_name_kana" => session('client_name_kana_pr'),
                    "corporation_num"  => session('corporation_num_pr'),
                    "project_code"     => session('project_code_pr'),
                    "project_name"     => session('project_name_pr'),
                    "target_data_st"   => session('target_data_st_pr'),
                    "target_data_en"   => session('target_data_en_pr')
                ]);
            }
            $condition = $this->searchCostBySession($request);

            $processe = $this->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5], $condition[6], $condition[7], $condition[8], $condition[9], $condition[10]);

            return view('process.index', [
                "processe"       => $processe,
                "company_id"       => session('company_id_pr'),
                "headquarter_id"   => session('headquarter_id_pr'),
                "department_id"    => session('department_id_pr'),
                "group_id"         => session('group_id_pr'),
                "client_code"      => session('client_code_pr'),
                "client_name_kana" => session('client_name_kana_pr'),
                "corporation_num"  => session('corporation_num_pr'),
                "project_code"     => session('project_code_pr'),
                "project_name"     => session('project_name_pr'),
                "target_data_st"   => session('target_data_st_pr'),
                "target_data_en"   => session('target_data_en_pr')
            ]);
        }

        $company_id_R  =  Auth::user()->company_id;

        $processe = Process_MST::leftjoin('customer_mst', 'customer_mst.id', '=', 'process.client_id')
            ->where('process.company_id', $company_id_R)
            ->orderBy('process.target_data', 'desc')
            ->orderBy('customer_mst.client_code', 'asc')
            ->select('process.*')
            ->paginate(25);

        return view('process.index');
    }


    public function searchCostBySession($request)
    {

        $condition = array();
        if ($request->session()->exists('company_id_pr')) {

            $company_id = session('company_id_pr');
            array_push($condition, $company_id);
        } else {

            $company_id = "";
            array_push($condition, $company_id);
        }


        if ($request->session()->exists('headquarter_id_pr')) {

            $headquarter_id = session('headquarter_id_pr');
            array_push($condition, $headquarter_id);
        } else {

            $headquarter_id = "";
            array_push($condition, $headquarter_id);
        }


        if ($request->session()->exists('department_id_pr')) {

            $department_id = session('department_id_pr');
            array_push($condition, $department_id);
        } else {

            $department_id = "";
            array_push($condition, $department_id);
        }

        if ($request->session()->exists('group_id_pr')) {

            $group_id = session('group_id_pr');
            array_push($condition, $group_id);
        } else {

            $group_id = "";
            array_push($condition, $group_id);
        }

        if ($request->session()->exists('client_code_pr')) {

            $client_code = session('client_code_pr');
            array_push($condition, $client_code);
        } else {

            $client_code = "";
            array_push($condition, $client_code);
        }

        if ($request->session()->exists('client_name_kana_pr')) {

            $client_name_kana  = session('client_name_kana_pr');
            array_push($condition, $client_name_kana);
        } else {

            $client_name_kana  = "";
            array_push($condition, $client_name_kana);
        }


        if ($request->session()->exists('project_code_pr')) {

            $project_code  = session('project_code_pr');
            array_push($condition, $project_code);
        } else {

            $project_code  = "";
            array_push($condition, $project_code);
        }

        if ($request->session()->exists('project_name_pr')) {

            $project_name  = session('project_name_pr');
            array_push($condition, $project_name);
        } else {

            $project_name  = "";
            array_push($condition, $project_name);
        }

        if ($request->session()->exists('corporation_num_pr')) {

            $corporation_num  = session('corporation_num_pr');
            array_push($condition, $corporation_num);
        } else {

            $corporation_num  = "";
            array_push($condition, $corporation_num);
        }

        if ($request->session()->exists('target_data_st_pr')) {

            $target_data_st  = session('target_data_st_pr');
            array_push($condition, $target_data_st);
        } else {

            $target_data_st  = "";
            array_push($condition, $target_data_st);
        }


        if ($request->session()->exists('target_data_en_pr')) {

            $target_data_en  = session('target_data_en_pr');
            array_push($condition, $target_data_en);
        } else {

            $target_data_en  = "";
            array_push($condition, $target_data_en);
        }

        return  $condition;
    }

    public function checkProcess()
    {
        $group_id = array();
        $groups = Group_MST::all();
        foreach ($groups as $key => $group) {
            if (Auth::user()->can('view', $group)) {
                array_push($group_id, $group->id);
            }
        }
        return $group_id;
    }

    public function search($company_id, $headquarter_id, $department_id, $group_id, $client_code, $client_name_kana, $project_code, $project_name, $corporation_num, $target_data_st, $target_data_en)
    {
        //     $processe = Process_MST::leftjoin('project_mst','project_mst.id','=','process.project_id')
        //                           ->leftjoin('customer_mst','customer_mst.id','=','project_mst.client_id')
        //                           ->select('process.*');
        $array_id = $this->checkProcess();


        //大元の検索条件
        $processe = Process_MST::leftjoin('project_mst', 'project_mst.id', '=', 'process.project_id')
            ->leftjoin('customer_mst', 'customer_mst.id', '=', 'project_mst.client_id')
            ->orderBy('process.target_data', 'desc')
            ->orderBy('customer_mst.client_code', 'asc')
            ->whereIn('project_mst.group_id', $array_id)
            ->select('process.*');
        //検索の条件が有れば、条件をｾｯﾄする
        if ($company_id != "") {

            $processe = $processe->where('process.company_id', $company_id);
        }

        if ($headquarter_id != "") {

            $processe = $processe->where('project_mst.headquarter_id', $headquarter_id);
        }

        if ($department_id != "") {

            $processe = $processe->where('project_mst.department_id', $department_id);
        }

        if ($group_id != "") {

            $processe = $processe->where('project_mst.group_id', $group_id);
        }

        if ($client_code != "") {

            $processe = $processe->where('customer_mst.client_code', $client_code)->orwhere('customer_mst.client_code_main', $client_code);
        }

        if ($project_name != "") {

            $processe = $processe->where('project_mst.project_name', 'like', "$project_name%");
        }

        if ($project_code != "") {

            $processe = $processe->where('project_mst.project_code', 'like', "$project_code");
        }

        if ($client_name_kana != "") {

            $processe = $processe->where('customer_mst.client_name_kana', 'like', "$client_name_kana%");
        }

        if ($corporation_num != "") {

            $processe = $processe->where('customer_mst.corporation_num', $corporation_num);
        }

        if ($target_data_st != "" & $target_data_en != "") {

            $processe = $processe->whereBetween('process.target_data', [$target_data_st . "/01", $target_data_en . "/01"]);
        } elseif ($target_data_st == "" & $target_data_en != "") {

            $processe = $processe->whereBetween('process.target_data', ["2000/01/01", $target_data_en . "/01"]);
        } elseif ($target_data_st != "" & $target_data_en == "") {

            $processe = $processe->whereBetween('process.target_data', [$target_data_st . "/01", '9999/12/31']);
        }

        //検索結果
        $processe          = $processe->paginate(25);
        // dd($processe);
        return $processe;
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
