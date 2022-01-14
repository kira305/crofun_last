<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ImportLog;
use App\ImportLogErr;
use App\Process_MST;
use App\Receivable_MST;
use Auth;
use DB;
use Common;
use Javascript;
use Session;
use Crofun;

class ImportSearchController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
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
                $errors = $validator->errors();
                $logs = $this->getAllImportLog();
                $requestOld = $request->all();
                return view('import_log.index', ['errors' => $errors, 'logs' => $logs, 'requestOld' => $requestOld]);
            }

            $company_id    = $request->company_id;
            $import_type   = $request->import_type;
            $user_name     = $request->user_name;
            $status        = $request->status;
            $start_time    = $request->start_time;
            $end_time      = $request->end_time;

            session(['company_id'       => $company_id]);
            session(['import_type'      => $import_type]);
            session(['user_name'        => $user_name]);
            session(['status_l'         => $status]);
            session(['start_time'       => $start_time]);
            session(['end_time'         => $end_time]);

            // $customers = Customer_MST::with('company_name')->get();

            $logs = $this->search($company_id, $import_type, $user_name, $status, $start_time, $end_time);
            return view('import_log.index', ['logs' => $logs]);
        }
        //検索条件存在しているかを確認
        if ($this->checkSessionExist($request) == 1) {

            $condition = $this->getSearchCondition($request);

            $logs      = $this->search($condition[0], $condition[1], $condition[2], $condition[3], $condition[4], $condition[5]);

            return view('import_log.index', ['logs' => $logs]);
        }

        $logs = $this->getAllImportLog();

        //デフォルト画面に本務の会社を表示させるためにセッションとして本務を持たせる
        session(['company_id'      => Auth::user()->company_id]);
        return view('import_log.index', ['logs' => $logs]);
    }

    /*
    * edit customer informatin
    * $request : form data
    * return update customer information status
    */


    public function search($company_id, $import_type, $user_name, $status, $start_time, $end_time)
    {

        $usr_id      = Auth::user()->id;
        $company_id_1  = Common::checkUserCompany($usr_id);

        $logs   = ImportLog::whereIn('import_log.company_id', $company_id_1)
            ->leftjoin('user_mst', 'user_mst.id', '=', 'import_log.user_id')->orderBy('import_log.created_at', 'desc')->select('import_log.*', 'usr_name');


        if ($company_id != "") {

            $logs = $logs->where('import_log.company_id', $company_id);
        }

        if ($import_type != "") {

            $logs = $logs->where('file_type', $import_type);
        }

        if ($user_name != "") {

            $logs = $logs->where('usr_name', 'like', "%$user_name%");
        }

        if ($status != "") {

            // $customers = $customers->where('corporation_num',$corporation_num);
            $logs = $logs->where('status', $status);
        }

        $start_time   = ($start_time == '' || $start_time == null) ? Common::get_min_created_time_import_log() : $start_time . " 00:00:00";
        $end_time     = ($end_time   == '' || $end_time == null) ? Common::get_max_created_time_import_log() : $end_time . " 23:59:59";

        $logs = $logs->whereBetween('import_log.created_at', [$start_time, $end_time]);

        return $logs->paginate(25);
    }

    public function checkSessionExist($request)
    {

        if (
            $request->session()->exists('company_id') ||
            $request->session()->exists('import_type') ||
            $request->session()->exists('user_name')  ||
            $request->session()->exists('status_l')     ||
            $request->session()->exists('start_time') ||
            $request->session()->exists('end_time')
        ) {

            return 1;
        } else {

            return 0;
        }
    }

    public function getSearchCondition($request)
    {

        $condition = array();

        if ($request->session()->exists('company_id')) {

            array_push($condition, session('company_id'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('import_type')) {

            array_push($condition, session('import_type'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('user_name')) {

            array_push($condition, session('user_name'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('status')) {

            array_push($condition, session('status'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('start_time')) {

            array_push($condition, session('start_time'));
        } else {

            array_push($condition, "");
        }

        if ($request->session()->exists('end_time')) {

            array_push($condition, session('end_time'));
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

            'start_time.date_format'       => trans('validation.import_log_start_time'),
            'end_time.date_format'         => trans('validation.import_log_end_time'),

        ]);

        $errors = $validator->errors();

        return $validator;
    }

    public function getAllImportLog()
    {

        $usr_id        = Auth::user()->id;
        $company_id_1  = Auth::user()->company_id;
        $logs          = ImportLog::where('company_id', $company_id_1)->orderBy('created_at', 'desc')->paginate(10);

        return $logs;
    }

    // dowload csv from search screen
    /*
    * create csv file when click delete button
    * $request : get file_id
    * return sream dowload
    */
    public function getCsv(Request $request)
    {

        try {

            $file_name = 'インポートログ_' . Common::getToDayCSV();
            $callback  = $this->getImportErrData($request); // call get customer data function from service
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
    * get list log err by search condition
    * $request : file id
    * return call funtion create stream csv file
    */
    public function getImportErrData($request)
    {

        $list_logs = array();
        $answer    = ImportLogErr::where('file_id', $request->file_id)->exists();

        if ($answer) {

            $logs    = ImportLogErr::where('file_id', $request->file_id)->get();

            foreach ($logs as $log) {

                $err = array($log->err_row, $log->err);

                array_push($list_logs, $err);
            }
        } else {

            $err = array('', '');

            array_push($list_logs, $err);
        }

        return $this->getDataForCreateCsv($list_logs);
    }

    /*
    * get data for create csv
    * $projects: プロジェクト リスト
    * return callback stream dowload
    */

    public function getDataForCreateCsv($logs)
    {


        $columns = array('行', 'エラー内容');

        echo "\xEF\xBB\xBF";


        $callback = function () use ($columns, $logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            //fwrite($file, "sep=\t" . "\r\n");
            foreach ($logs as $log) {


                fputcsv($file,  $log);
            }

            fclose($file);
        };

        return $callback;
    }


    /*
    * get data for create csv
    * $projects: プロジェクト リスト
    * return callback stream dowload
    */

    // public function getDataForCreateCsv($logs){


    //             $columns = array('ユーザー名', '取込ファイル名','種類', 'データ件数', 'ステータス', '取込日', 'エラーファイル');

    //             echo "\xEF\xBB\xBF";


    //             $callback = function() use ($columns,$logs)
    //             {
    //                 $file = fopen('php://output', 'w');
    //                 fputcsv($file, $columns);

    //                 //fwrite($file, "sep=\t" . "\r\n");
    //                 foreach($logs as $log) {


    //                        fputcsv($file,  $log);


    //                 }

    //                 fclose($file);
    //             };

    //             return $callback;
    // }

    //配列にプロジェクト情報を入れる
    /*
    * get project detail by id and modify data to array
    * $project_id : プロジェクト id
    * return project's array attribute
    */
    // public function changeFormatData($log_id){

    //     $log        = ImportLog::where('id',$log_id)->first();


    //     $log_array = array();

    //     array_push($log_array, $log->user->user_name);
    //     array_push($log_array, $log->file_name);

    //     if($log->file_type == 1){

    //          array_push($log_array, '売上');

    //     }else {

    //         array_push($log_array, '売掛金残');

    //     }

    //     array_push($log_array, $log->data_total);

    //     if($log->status == true){

    //          array_push($log_array, '正常取込');

    //     }else {

    //         array_push($log_array, '取込エラー');

    //     }

    //     array_push($log_array, $log->created_at);


    //     return $log_array;

    // }

    /*
    * get list projects by search condition
    * $request : search condition
    * return call funtion create stream csv file
    */
    // public function getImportLogData($request){

    //     $list_logs = array();

    //     if($this->checkSessionExist($request) == 1){


    //         $condition = $this->getSearchCondition($request);

    //         $logs      = $this->search($condition[0],$condition[1],$condition[2],$condition[3],$condition[4],$condition[5]);

    //         foreach ($logs as $log) {

    //            array_push($list_logs,$this->changeFormatData($log->id));

    //         }

    //     }else {

    //          $logs = $this->getAllImportLog();

    //          foreach ($logs as $log) {

    //            array_push($list_logs,$this->changeFormatData($log->id));

    //          }
    //     }

    //   return $this->getDataForCreateCsv($list_logs);

    // }

    public function delete(Request $request)
    {

        DB::beginTransaction();
        $id = $request->id;

        try {

            $del_data = ImportLog::where('id', $id)->first();

            $import = ImportLog::where('id', $id)->update(['status' => 2]);

            Crofun::log_create(Auth::user()->id, $id, config('constant.PCA'), config('constant.operation_DELETE'), config('constant.PCA_LOG'), $del_data->company_id, $del_data->file_name, null, null, null);

            $log_err    = ImportLogErr::where('file_id', $id)->delete();
            $receivable = Receivable_MST::where('import_id', $id)->delete();
            $process    = Process_MST::where('import_id', $id)->delete();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            throw new Exception($e);
        }


        // return redirect('/importlog/index');
        return back()->withInput();
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
}
