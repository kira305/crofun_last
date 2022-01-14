<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Receivable_MST;
use App\Process_MST;
use App\ImportLog;
use App\ImportLogErr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Crofun;
use Auth;
use Common;
class ReceivableUploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
       
        
        return view('receivable.upload');

    }

    public function upload(Request $request){

        $usr_id         = Auth::user()->id;
     
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(),[

                    'file_data'                        => 'required|mimes:csv,txt',
                    'company_id'                       => 'required',
                    'import_type'                      => 'required',
                    'get_time'                         => 'required'

                ],[
                    'file_data.mimes'                  => trans('validation.company_code'),
                    'company_id.required'              => trans('validation.list_code'),
                    'import_type.required'             => trans('validation.headquarter_name'),
                    'get_time.required'                => trans('validation.headquarter_code'),

                ]);

            if ($validator->fails()) {
                            
               $errors = $validator->errors();
                           
                return response()->json([
                    'status_code'   => 401,
                    'file_data'     =>$errors->first('file_data'),
                    'company_id'    =>$errors->first('company_id'),
                    'import_type'   =>$errors->first('import_type'),
                    'get_time'      =>$errors->first('get_time'),
                ]);

                     
            }

            try{
               
               $company_id  = $request->company_id;
               $import_type = $request->import_type;
               $get_time    = $request->get_time;
               $csv         = Input::file('file_data');
             
               Storage::disk('public')->put($csv->getClientOriginalName(),  File::get($csv));
               $file_url = public_path() . '/uploads/'.$csv->getClientOriginalName();
               mb_language("Japanese"); 
               $row = 0;
               $import_type_name = '';
               if($import_type == '1'){

                  $import_type_name = '売上';

               }else {

                   $import_type_name = '取込エラー';
               }

                $import_log             = new ImportLog();
                $import_log->company_id = $company_id;
                $import_log->file_name  = $csv->getClientOriginalName();
                $import_log->user_id    = $usr_id;

                $import_log->save();

               if (($handle = fopen($file_url, "r")) !== FALSE) {

                    while (($data = fgets($handle)) !== FALSE) {
                   
                     if($row >=1){
                        
                        $text = mb_convert_encoding ($data, 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');

                        $content = explode(',',$text);

                        $size    = sizeof($content);

                        if($import_type == '1' && $size != 10){
                            
                            $log_err          = new ImportLogErr();
                            $log_err->err     = trans('message.import_file_pca');
                            $log_err->file_id = $import_log->id;
                            $log_err->err_row = $row;

                            $log_err->save();

                            return response()->json([

                                         'file_name'      =>  $csv->getClientOriginalName(),
                                         'type'           =>  $import_type_name,
                                         'data_total'     =>  $row++,
                                         'company_name'   =>  Common::getCompanyById($company_id)->company_name,
                                         'status'         =>  '取込エラー',
                                         'time'           =>  Common::getToDay(),
                                         'status_code'    =>  302,
                                         'file_name_err'  =>  '売上データ_エラー詳細_'.Common::getToDayCSV(),

                                      ]);
                        }
                        
                        if($import_type == '2' && $size != 11){
                            
                            $log_err          = new ImportLogErr();
                            $log_err->err     = trans('message.import_file_pca');
                            $log_err->file_id = $import_log->id;
                            $log_err->err_row = $row;

                            $log_err->save();

                            return response()->json([

                                         'file_name'    =>  $csv->getClientOriginalName(),
                                         'type'         =>  $import_type_name,
                                         'data_total'   =>  $row++,
                                         'company_name' =>  Common::getCompanyById($company_id)->company_name,
                                         'status'       =>  '取込エラー',
                                         'time'         =>  Common::getToDay(),
                                         'status_code'  =>  302,
                                         'file_name_err'  =>  '売掛金残データ_エラー詳細_'.Common::getToDayCSV(),

                                      ]);
                        }

                        if($import_type == '1'){
                          
                            $process = new Process_MST();
                        
                            $process->company_id           = $company_id;
                            $process->target_data          = $get_time;
                            $process->import_id            = $import_log->id;
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
                            $process->project_id           = 1;
                        

                            $process->save();

                        }else {
                            
                            $receivable = new Receivable_MST();
                        
                            $receivable->company_id           = $company_id;
                            $receivable->target_data          = $get_time;
                            $receivable->import_id            = $import_log->id;
                            $receivable->pca_department_code  = $content[0];
                            $receivable->pca_department_name  = $content[1];
                            $receivable->count_code           = $content[2];
                            $receivable->count_name           = $content[3];
                            $receivable->auxiliary_code       = $content[4];
                            $receivable->auxiliary_name       = $content[5];
                            $receivable->last_remain          = $content[6];
                            $receivable->credit               = $content[7];
                            $receivable->debit                = $content[8];
                            $receivable->receivable           = $content[9];
                            $receivable->client_id            = Common::getCustomerByCode(trim($content[10]))->id;
                            $receivable->client_code          = $content[10];

                            $receivable->save();

                        }

                   
                     }

                     $row++;

                    }

                    fclose($handle);
                }

               $import_log->data_total = $row-1;
               $import_log->data_total = true;

               $import_log->save();


               unlink($file_url);
               
               return response()->json([
                                         'file_name'    =>  $csv->getClientOriginalName(),
                                         'type'         =>  $import_type_name,
                                         'data_total'   =>  $row,
                                         'company_name' =>  Common::getCompanyById($company_id)->company_name,
                                         'status'       =>  '正常取込',
                                         'time'         =>  Common::getToDay()

                                      ]);

             }catch(Exception $e) {

                throw new Exception($e);

             }
          }
     
    }

    public function test(Request $request){

           return response()->json(['csv' =>  $request->price]);

    }


}
