<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Receivable_MST;
use App\Process_MST;
use App\ImportLog;
use App\ImportLogErr;
use App\Credit_check;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use App\Service\MailService;
use App\Jobs\SendCreditMail;
use Crofun;
use Auth;
use Common;
use DB;
use Carbon\Carbon;
use Artisan;

define("PROCESS_STRING", '合計');
define("RECEIVABLE_STRING", "差額(科目 - 合計)");
define("STATUS_SUCCESS", "正常取込");
define("STATUS_FAIL", "取込エラー");
define("PROCESS_ERR_FILE_NAME", '売上データ_エラー詳細_');
define("RECEIVABLE_ERR_FILE_NAME", '売掛金残データ_エラー詳細_');

class PcaUploadController extends Controller
{


    protected $mail_service;
    protected $user_id = array();
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MailService $mail_service)
    {
        $this->middleware('auth');
        $this->mail_service = $mail_service;
    }

    public function index(Request $request)
    {
        return view('pca.upload', ['result' => 1]);
    }

    // if has err then check all row of uploaded file
    //エラーがある場合は保存されたデータは消して全ファイルのエラーを検索
    public function save_err($file_url, $import_type, $import_id, $company_id)
    {

        $row = 0;
        DB::beginTransaction();

        if (($handle = fopen($file_url, "r")) !== FALSE) {

            while (($data = fgets($handle)) !== FALSE) {

                $text    = mb_convert_encoding($data, 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');
                $content = explode(',', $text);

                $size    = sizeof($content);
                if ($size != 10) { // 行の内容は１０に異なる

                    $log_err          = new ImportLogErr();
                    $log_err->err     = trans('message.import_file_pca');
                    $log_err->file_id = $import_id;
                    $log_err->err_row = $row + 1;
                    $log_err->save();

                    continue;
                }


                if ($row >= 1) {

                    if (strcmp($import_type, '1') == 0) {

                        if ((strcmp(trim($content[5]), PROCESS_STRING) == 0)) {
                            $project          = Common::getProjectByCode(trim($content[0]), $company_id);

                            if ($project == null) {

                                $log_err          = new ImportLogErr();
                                $log_err->err     = trans('message.project_code_not_exist');
                                $log_err->file_id = $import_id;
                                $log_err->err_row = $row + 1;

                                $log_err->save();
                            }
                        }
                    } else {

                        if ((strcmp(trim($content[5]), PROCESS_STRING) != 0) && (strcmp(trim($content[5]), RECEIVABLE_STRING) != 0)) {

                            $client           = Common::getCustomerByCodeMain(trim($content[4]), $company_id);

                            if ($client == null) {

                                $log_err          = new ImportLogErr();
                                $log_err->err     = trans('message.client_code_not_exist');
                                $log_err->file_id = $import_id;
                                $log_err->err_row = $row + 1;

                                $log_err->save();
                            }
                        }
                    }
                }
                $row++;
            }
        }


        DB::commit();
        return $row - 1;
    }

    public function upload(Request $request)
    {

        $user_id         = Auth::user()->id;

        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [

                'file_data'                        => 'required|mimes:csv,txt',
                'company_id'                       => 'required',
                'import_type'                      => 'required',
                'get_time'                         => 'required|date_format:Y/m'

            ], [
                'file_data.mimes'                  => trans('validation.company_code'),
                'company_id.required'              => trans('validation.list_code'),
                'import_type.required'             => trans('validation.import_type'),
                'get_time.required'                => trans('validation.get_time_pca'),
                'get_time.date_format'             => trans('validation.target_data_en'),

            ]);
            session()->flashInput($request->input());
            if ($validator->fails()) { // if has errors then return detail to view

                $errors = $validator->errors();

                return view('pca.upload', [

                    'errors'    =>  $errors,
                    'result'    =>  1
                ]);
            }

            try {
                //トランザクションをはじめ
                DB::beginTransaction();
                $company_id  = $request->company_id;

                $import_type = $request->import_type;

                $get_time    = $request->get_time . '/01 00:00:00';
                $get_time    = Carbon::parse($get_time)->format('Y-m-d');
                $csv         = $request->file('file_data');
                $file_name   = $csv->getClientOriginalName();

                Storage::disk('public')->put($csv->getClientOriginalName(),  File::get($csv)); // save file uploaded
                $file_url = public_path() . '/uploads/' . $csv->getClientOriginalName();
                mb_language("Japanese");
                $row              = 0;
                $total            = 0;
                $import_type_name = '';
                $check_err        = 0;

                if ($import_type == '1') { // 1の場合は売上

                    $import_type_name = '売上';
                } else {                 //2の場合は売掛金残

                    $import_type_name = '売掛金残';
                }

                $import_log_id = $this->getMaxImportLogId();


                if (($handle = fopen($file_url, "r")) !== FALSE) {
                    //1行ずつのループ
                    while (($data = fgets($handle)) !== FALSE) {

                        if ($row >= 1) {

                            $text    = mb_convert_encoding($data, 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');

                            $content = explode(',', $text); //行の内容を切り分けて配列に挿入

                            $size    = sizeof($content);

                            if ($size != 10) {

                                // check file rule when file tpy is 1
                                // エラーがある場合は蓄積されたデータを消し
                                DB::rollBack();  // if has errors then do not save all data

                                $import_log = $this->saveImportLog(
                                    $import_log_id,
                                    $company_id,
                                    $file_name,
                                    $user_id,
                                    false,
                                    null,
                                    $import_type
                                );

                                //エラーがある場合は残りの行はすべてチェックしてエラーログ表に保存
                                $total = $this->save_err($file_url, $import_type, $import_log->id, $company_id);
                                fclose($handle);
                                unlink($file_url);
                                $import          = ImportLog::where("id", $import_log_id)->first();

                                //PCAログ
                                Crofun::log_create(Auth::user()->id, $import_log_id, config('constant.PCA'), config('constant.operation_FILEUP'), config('constant.PCA_UPDATE'), $company_id, $file_name, $import_log_id, json_encode($import), null);
                                if ($total < 0) $total = 0;
                                //エラーがある場合は画面に返すデータ
                                return view('pca.upload', [

                                    'file_name'    =>  $file_name, // get file name
                                    'type'         =>  $import_type_name,
                                    'data_total'   =>  $total,
                                    'company_name' =>  Common::getCompanyById($company_id)->company_name,
                                    'status'       =>  STATUS_FAIL,
                                    'time'         =>  Common::getToDay(),
                                    'company_id'   =>  $company_id,
                                    'url'          =>  $file_url,
                                    'file_name_err' =>  $import_type_name . 'データ_エラー詳細_' . Common::getToDayCSV(),
                                    'import_id'    =>  $import_log->id,
                                    'result'       =>  0
                                ]);
                            }

                            $saveProcess    = $this->saveProcess($content, $company_id, $get_time, $import_log_id);
                            if ($import_type == '1') { //if type of data is 1

                                if ($saveProcess == 2) {

                                    $total = $total + 1;
                                } else {


                                    if ($saveProcess == 1) {

                                        DB::rollBack();
                                        $import_log = $this->saveImportLog(
                                            $import_log_id,
                                            $company_id,
                                            $file_name,
                                            $user_id,
                                            false,
                                            null,
                                            $import_type
                                        );

                                        $total = $this->save_err($file_url, $import_type, $import_log->id, $company_id);


                                        $import          = ImportLog::where("id", $import_log_id)->first();

                                        //PCAログ
                                        Crofun::log_create(Auth::user()->id, $import_log_id, config('constant.PCA'), config('constant.operation_FILEUP'), config('constant.PCA_UPDATE'), $company_id, $file_name, $import_log_id, json_encode($import), null);
                                        return view('pca.upload', [

                                            'file_name'    =>  $file_name, // get file name
                                            'type'         =>  $import_type_name,
                                            'data_total'   =>  $total,
                                            'company_name' =>  Common::getCompanyById($company_id)->company_name,
                                            'status'       =>  STATUS_FAIL,
                                            'time'         =>  Common::getToDay(),
                                            'company_id'   =>  $company_id,
                                            'url'          =>  $file_url,
                                            'file_name_err' =>  PROCESS_ERR_FILE_NAME . Common::getToDayCSV(),
                                            'import_id'    =>  $import_log->id,
                                            'result'       =>  0
                                        ]);
                                    }
                                }
                            } else {  //if type of data is 2

                                $saveReceivable = $this->saveReceivable($content, $company_id, $get_time, $import_log_id);
                                if ($saveReceivable == 2) {

                                    $total = $total + 1;
                                } else {

                                    if ($saveReceivable == 1) {

                                        DB::rollBack();
                                        $import_log = $this->saveImportLog(
                                            $import_log_id,
                                            $company_id,
                                            $file_name,
                                            $user_id,
                                            false,
                                            null,
                                            $import_type
                                        );

                                        $total  = $this->save_err($file_url, $import_type, $import_log->id, $company_id);
                                        $import = ImportLog::where("id", $import_log_id)->first();

                                        //PCAログ
                                        Crofun::log_create(Auth::user()->id, $import_log_id, config('constant.PCA'), config('constant.operation_FILEUP'), config('constant.PCA_UPDATE'), $company_id, $file_name, $import_log_id, json_encode($import), null);

                                        return view('pca.upload', [

                                            'file_name'    =>  $file_name, // get file name
                                            'type'         =>  $import_type_name,
                                            'data_total'   =>  $total,
                                            'company_name' =>  Common::getCompanyById($company_id)->company_name,
                                            'status'       =>  STATUS_FAIL,
                                            'time'         =>  Common::getToDay(),
                                            'company_id'   =>  $company_id,
                                            'url'          =>  $file_url,
                                            'file_name_err' =>  RECEIVABLE_ERR_FILE_NAME . Common::getToDayCSV(),
                                            'import_id'    =>  $import_log->id,
                                            'result'       =>  0
                                        ]);
                                    }
                                }
                            }
                        }

                        $row++;
                    }

                    fclose($handle); // close opened file
                }
                //
                $import_log_data = $this->saveImportLog(
                    $import_log_id,
                    $company_id,
                    $file_name,
                    $user_id,
                    true,
                    $total,
                    $import_type,
                    ''
                );

                // unlink($file_url);

                //エアr－がない場合はトランザクションを蓄積
                DB::commit();

                $import          = ImportLog::where("id", $import_log_id)->first();

                //PCAログ
                Crofun::log_create(Auth::user()->id, $import_log_id, config('constant.PCA'), config('constant.operation_FILEUP'), config('constant.PCA_UPDATE'), $company_id, $file_name, $import_log_id, json_encode($import), null);

                return view('pca.upload', [

                    'file_name'    =>  $file_name, // get file name
                    'type'         =>  $import_type_name,
                    'data_total'   =>  $total,
                    'company_name' =>  Common::getCompanyById($company_id)->company_name,
                    'status'       =>  STATUS_SUCCESS,
                    'time'         =>  Common::getToDay(),
                    'company_id'   =>  $company_id,
                    'url'          =>  $file_url,
                    'result'       =>  0
                ]);
            } catch (Exception $e) {

                //エラーある場合は蓄積されたデータを消し
                DB::rollBack();

                throw new Exception($e);
            }
        }
    }
    // 一番大きいなidを取得
    public function getMaxImportLogId()
    {


        $id        = DB::select("select nextval('import_log_id_seq')");

        return $id[0]->nextval;
    }

    public function getErrFileName($file_name_err)
    {

        if ($file_name_err != '') {

            return $file_name_err . Common::getToDayCSV();
        } else {

            return $file_name_err;
        }
    }
    //エラーがある場合はデータベースに保存
    public function saveImportLog($import_log_id, $company_id, $file_name, $user_id, $status, $data_total, $import_type)
    {

        $file_name_err = '';
        if (strcmp($import_type, '1') == 0 && $status == false) { // 1の場合は売上

            $file_name_err = PROCESS_ERR_FILE_NAME;
        } else {
            //2の場合は売掛金残
            if ($status == false) {

                $file_name_err = RECEIVABLE_ERR_FILE_NAME;
            }
        }

        $import_log                 = new ImportLog();
        $import_log->id             = $import_log_id;
        $import_log->company_id     = $company_id;
        $import_log->file_name      = $file_name;
        $import_log->user_id        = $user_id;
        $import_log->status         = $status;
        $import_log->data_total     = $data_total;
        $import_log->file_type      = (int)$import_type;
        $import_log->file_name_err  = $this->getErrFileName($file_name_err);

        $import_log->save();   // save import log after has errors

        return $import_log;
    }
    // 売上データ保存
    public function saveProcess($content, $company_id, $get_time, $import_id)
    {

        if ((strcmp(trim($content[5]), PROCESS_STRING) == 0)) { //PROCESS_STRING は合計

            $process = new Process_MST();

            $process->company_id           = $company_id;
            $process->target_data          = $get_time;
            $process->import_id            = $import_id;
            $process->pca_department_code  = $content[0];
            $process->pca_department_name  = $content[1];
            $process->count_code           = $content[2];
            $process->count_name           = $content[3];
            $process->auxiliary_code       = $content[4];
            $process->auxiliary_name       = $content[5];
            $process->last_remain          = ($content[6] == '') ? null : $content[6];
            $process->credit               = ($content[7] == '') ? null : $content[7];
            $process->debit                = ($content[8] == '') ? null : $content[8];
            $process->receivable           = $content[9];
            $project                       = Common::getProjectByCode(trim($content[0]), $company_id);

            if ($project == null) {

                return 1;
            }

            $process->project_id           = $project->id;

            if ($process->save()) {

                return 2;
            } else {

                return false;
            }
        } else {

            return false;
        }
    }
    // 売掛金残データ保存
    public function saveReceivable($content, $company_id, $get_time, $import_id)
    {

        //PROCESS_STRING は合計,RECEIVABLE_STRING は差額(科目 - 合計)
        if ((strcmp(trim($content[5]), PROCESS_STRING) != 0) && (strcmp(trim($content[5]), RECEIVABLE_STRING) != 0)) {

            $receivable = new Receivable_MST();

            $receivable->company_id           = $company_id;
            $receivable->target_data          = $get_time;
            $receivable->import_id            = $import_id;
            $receivable->pca_department_code  = $content[0];
            $receivable->pca_department_name  = $content[1];
            $receivable->count_code           = $content[2];
            $receivable->count_name           = $content[3];
            // $receivable->auxiliary_code       = $content[4];
            // $receivable->auxiliary_name       = $content[5];
            $receivable->last_remain          = $content[6];
            $receivable->credit               = $content[7];
            $receivable->debit                = $content[8];
            $receivable->receivable           = $content[9];
            $client                           = Common::getCustomerByCodeMain(trim($content[4]), $company_id);

            if ($client == null) {

                return 1;
            }

            $receivable->client_id            = $client->id;
            $receivable->client_code          = $content[4];

            //event(new SendCreditMail($receivable->client_id,$receivable->receivable,$this->mail_service));
            if (!$client->project->isEmpty()) {

                $this->sednMail($receivable->client_id, $receivable->receivable);
            }

            // $worker = new SendCreditMail($receivable->client_id,$receivable->receivable,$this->mail_service);
            // $worker->start();

            if ($receivable->save()) {
                return 2;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function sednMail($client_id, $receivable)
    {
        // $credit     = Credit_check::where('client_id', $client_id)->orderBy('get_time', 'desc')->first();
        $credit     = Credit_check::where('client_id', $client_id)->orderBy('get_time', 'desc')->orderBy('created_at', 'desc')->first();

        if ($credit) {
            // $credit     = (int)$credit->credit_limit;
            $creditExpect     = (int)$credit->credit_expect;
            $receivable = (int)$receivable;
            if (($creditExpect - $receivable) < 0) {
                $send_user_id  = $this->mail_service->getListUserId($client_id);
                $list_user_id  = $this->check_user_id($send_user_id);
                $this->mail_service->sendCreditMail($client_id, $list_user_id);
                // $job = (new SendCreditMail($client_id,$this->mail_service))->delay(Carbon::now()->addMinutes(1));
                // dispatch($job);
            }
        }
    }

    public function check_user_id($send_user_id)
    {
        $list_checked_user_id = array();
        foreach ($send_user_id as $user_id) {
            //$this->user_id　配列の中に、取得した$user_idが存在するか。
            if (!in_array($user_id, $this->user_id)) {
                //送信するユーザーをセットする。　　$list_checked_user_id　リターン用　　$this->user_id　送信済み
                array_push($list_checked_user_id, $user_id);
                array_push($this->user_id, $user_id);
            }
        }

        return $list_checked_user_id;
    }

    // dowload csv from index page
    public function getCsv(Request $request)
    {

        try {
            $import_log = ImportLog::where('id', $request->import_id)->first();

            $file_name  = $import_log->file_name_err . Common::getToDayCSV();
            $callback   = $this->GetErrData($request->import_id);
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

    public function GetErrData($import_id)
    { // get errors data from database

        $list_err = array();
        $errors   = ImportLogErr::where('file_id', $import_id)->get();

        foreach ($errors as $err) {

            $a = array($err->err_row, $err->err);
            array_push($list_err, $a);
        }



        return $this->getDataForCreateCsv($list_err);
    }
    // return ファイルのパラメタ
    public function getDataForCreateCsv($datas)
    { // return stream of csv file


        $columns = array('行', 'エラー内容');

        echo "\xEF\xBB\xBF";


        $callback = function () use ($columns, $datas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            //fwrite($file, "sep=\t" . "\r\n");
            foreach ($datas as $data) {


                fputcsv($file,  $data);
            }

            fclose($file);
        };

        return $callback;
    }
}
