<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Log_MST;
use Auth;
use App\Table_MST;

class LogController extends Controller
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


    //      $log = Log_MST::all();

    //    return view('log.index',["log" => $log]);


    // }
    public function index(Request $request)
    {

        /*処理区分にデータを入れる*/
        $arrprocess = [config('constant.operation_UPDATE'), config('constant.operation_CRATE'), config('constant.operation_LOGIN'), config('constant.operation_LOGOUT'), config('constant.operation_DELETE'), config('constant.operation_REFERENCE'), config('constant.operation_FILEUP'), config('constant.operation_Bulk_up'), config('constant.operation_OFF')];
        $tables = Table_MST::orderBy('id', 'asc')->get();

        //もしクリアボタンを押されたら
        if ($request->clear1 == '1') {
            session(['company_id_log'       => $request->company_id]);
            session(['table_id_log'         => $request->table_id]);
            session(['form_id_log'          => $request->form_id]);
            session(['process_log'          => $request->process]);
            session(['update_code_log'      => $request->update_code]);
            session(['update_name_log'      => $request->update_name]);
            session(['update_data_st_log'   => $request->update_data_st]);
            session(['update_data_en_log'   => $request->update_data_en]);

            return view('log.index', ["arrprocess" => $arrprocess, 'tables' => $tables]);
        }

        //リクエストが与えられたか？
        if ($request->isMethod('post')) {
            //画面からリクエストされている情報
            $company_id       = $request->company_id;
            $table_id         = $request->table_id;
            $form_id          = $request->form_id;
            $process          = $request->process;
            $update_code      = $request->update_code;
            $update_name      = $request->update_name;
            $update_data_st   = $request->update_data_st;
            $update_data_en   = $request->update_data_en;

            $validator = $this->validateData($request);
            if ($validator->fails()) {
                $errors = $validator->errors();

                return view('log.index', [
                    "errors" => $errors,
                    "company_id"     => $company_id,
                    "table_id"       => $table_id,
                    "form_id"        => $form_id,
                    "process"        => $process,
                    "update_code"    => $update_code,
                    "update_name"    => $update_name,
                    "update_data_st" => $update_data_st,
                    "update_data_en" => $update_data_en,
                    'tables'         => $tables
                ])->with('arrprocess', $arrprocess);
            }

            session(['company_id_log'       => $company_id]);
            session(['table_id_log'         => $request->table_id]);
            session(['form_id_log'          => $form_id]);
            session(['process_log'          => $process]);
            session(['update_code_log'      => $update_code]);
            session(['update_name_log'      => $update_name]);
            session(['update_data_st_log'   => $update_data_st]);
            session(['update_data_en_log'   => $update_data_en]);

            $log          = $this->search($company_id, $table_id, $form_id, $process, $update_code, $update_name, $update_data_st, $update_data_en);

            return view('log.index', [
                "log"            => $log,
                "company_id"     => session('company_id_log'),
                "table_id"       => session('table_id_log'),
                "form_id"        => session('form_id_log'),
                "process"        => session('process_log'),
                "update_code"    => session('update_code_log'),
                "update_name"    => session('update_name_log'),
                "update_data_st" => session('update_data_st_log'),
                "update_data_en" => session('update_data_en_log'),
                'tables'         => $tables
            ])
                ->with('arrprocess', $arrprocess);
        }

        //セッションの情報で検索　(他の画面から遷移した時)
        if (
            $request->session()->exists('company_id_log')       ||
            $request->session()->exists('table_id_log')         ||
            $request->session()->exists('form_id_log')          ||
            $request->session()->exists('process_log')          ||
            $request->session()->exists('update_code_log')      ||
            $request->session()->exists('update_name_log')      ||
            $request->session()->exists('update_data_st_log')   ||
            $request->session()->exists('update_data_en_log')

        ) {

            $condition = $this->searchCostBySession($request);

            // $validator = $this->validateData($request);
            // if ($validator->fails()) {
            //       $log = Log_MST::leftjoin('user_mst','user_mst.id','=','log.user_id')
            //                      ->orderBy('log.created_at', 'desc')
            //                      ->where('log.company_id',$company_id)
            //                      ->select('log.*')
            //                      ->get();

            // }


            $log = $this->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5], $condition[6], $condition[7]);

            return view('log.index', [
                "log"            => $log,
                "company_id"     => session('company_id_log'),
                "table_id"       => session('table_id_log'),
                "form_id"        => session('form_id_log'),
                "process"        => session('process_log'),
                "update_code"    => session('update_code_log'),
                "update_name"    => session('update_name_log'),
                "update_data_st" => session('update_data_st_log'),
                "update_data_en" => session('update_data_en_log'),
                'tables'         => $tables
            ])
                ->with('arrprocess', $arrprocess);
        }
        $company_id =  Auth::user()->company_id;

        /* $log = Log_MST::leftjoin('user_mst','user_mst.id','=','log.user_id')
                           ->orderBy('log.created_at', 'desc')
                           ->where('log.company_id',$company_id)
                           ->select('log.*')
                           ->get(); */

        return view('log.index', ["arrprocess" => $arrprocess, 'tables' => $tables]);
    }


    public function search($company_id, $table_id, $form_id, $process, $update_code, $update_name, $update_data_st, $update_data_en)
    {

        //大元の検索条件



        $log = Log_MST::leftjoin('user_mst', 'user_mst.id', '=', 'log.user_id')
            ->orderBy('log.created_at', 'desc')
            ->select('log.*');

        if ($update_data_st != "" & $update_data_en != "") {

            $update_data_st = $update_data_st . " 00:00:00";
            $update_data_en = $update_data_en . " 23:59:59";
            $log = $log->whereBetween('log.updated_at', [$update_data_st, $update_data_en]);
        } elseif ($update_data_st == "" & $update_data_en != "") {

            $update_data_en = $update_data_en . " 23:59:59";
            $log = $log->whereBetween('log.updated_at', ["2000/01/01", $update_data_en]);
        } elseif ($update_data_st != "" & $update_data_en == "") {

            $update_data_st = $update_data_st . " 00:00:00";
            $log = $log->whereBetween('log.updated_at', [$update_data_st, '9999/12/31']);
        }

        //検索の条件が有れば、条件をｾｯﾄする
        if ($company_id != "") {

            $log = $log->where('log.company_id', $company_id);
        }


        if ($table_id != "") {

            $log = $log->where('log.table_id', $table_id);
        }


        if ($form_id != "") {

            $log = $log->where('log.form_id', $form_id);
        }

        if ($process != "") {

            $log = $log->where('log.process', $process);
        }

        if ($update_code != "") {

            $log = $log->where('log.code', $update_code);
        }

        if ($update_name != "") {

            $log = $log->where('log.name', $update_name);
        }


        //検索結果
        $log          = $log->paginate(30);

        return $log;
    }

    public function searchCostBySession($request)
    {

        $condition = array();
        if ($request->session()->exists('company_id_log')) {

            $company_id = session('company_id_log');
            array_push($condition, $company_id);
        } else {

            $company_id = "";
            array_push($condition, $company_id);
        }

        if ($request->session()->exists('table_id_log')) {

            $table_id = session('table_id_log');
            array_push($condition, $table_id);
        } else {

            $table_id = "";
            array_push($condition, $table_id);
        }


        if ($request->session()->exists('form_id_log')) {

            $form_id  = session('form_id_log');
            array_push($condition, $form_id);
        } else {

            $form_id  = "";
            array_push($condition, $form_id);
        }

        if ($request->session()->exists('process_log')) {

            $process  = session('process_log');
            array_push($condition, $process);
        } else {

            $process  = "";
            array_push($condition, $process);
        }

        if ($request->session()->exists('update_code_log')) {

            $update_code  = session('update_code_log');
            array_push($condition, $update_code);
        } else {

            $update_code  = "";
            array_push($condition, $update_code);
        }

        if ($request->session()->exists('update_name_log')) {

            $update_name  = session('update_name_log');
            array_push($condition, $update_name);
        } else {

            $update_name  = "";
            array_push($condition, $update_name);
        }

        if ($request->session()->exists('update_data_st_log')) {

            $update_data_st  = session('update_data_st_log');
            array_push($condition, $update_data_st);
        } else {

            $update_data_st  = "";
            array_push($condition, $update_data_st);
        }


        if ($request->session()->exists('update_data_en_log')) {

            $update_data_en  = session('update_data_en_log');
            array_push($condition, $update_data_en);
        } else {

            $update_data_en  = "";
            array_push($condition, $update_data_en);
        }

        return  $condition;
    }

    public function validateData(Request $request)
    {
        /*エラーのチェック 日付型*/
        $validator = Validator::make($request->all(), [

            'update_data_st'        => 'nullable|date',
            'update_data_en'        => 'nullable|date',

        ], [

            'update_data_st.date'       => trans('validation.start_data'),
            'update_data_en.date'       => trans('validation.end_data'),

        ]);

        $errors = $validator->errors();

        return $validator;
    }


    public function view(Request $request)
    {

        $log  = Log_MST::where("id", $request->id)->first();
        $old  = json_decode($log->old_data, true);
        $new  = json_decode($log->new_data, true);
        $item = Table_MST::where("id", $log->table_id)->first();

        if ($item) {

            $field_name = json_decode($item->field_name, true);
        } else {
            $field_name = null;
        }

        if ($log->table_id == config('constant.RULE')) {

            $base = $field_name;
        } elseif ($log->process == config('constant.operation_Bulk_up') || $log->process == config('constant.operation_OFF')) {

            foreach ($new as $check) {

                $itme[] = $item->table_name;
            }

            $field_name = $itme;
            $base = $itme;
        } else {

            $base = $new;
        }
        //   foreach($base as $key => $value){
        //         dump($old[$key]);
        //   }
        //   dd(1);
        return view('log.view', ['log' => $log, 'old_date' => $old, 'new_date' => $new, 'item' => $field_name, 'base' => $base]);
    }
}
