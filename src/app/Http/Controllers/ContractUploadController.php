<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use App\Contract_MST;
use Auth;
use DB;
use Common;
use Javascript;
use Session;
use Crofun;
class ContractUploadController extends Controller
{

  protected function upload(Request $request){

    if($request->isMethod('post')){

      $validator = Validator::make($request->all(),[

        'pdf'                        => 'required|mimes:pdf|max:10000',

      ],[
        'pdf.mimes'                  => trans('validation.contract_upload_pdf'),
        'pdf.max'                    => trans('validation.contract_upload_max'),
      ]);

      if ($validator->fails()) {

        $errors = $validator->errors();

        return response()->json([

         'status_code'   => 401,
         'message'       => $errors->first('pdf'),

       ]);


      }

      $pdf         = Input::file('pdf');
      $file_name   = $pdf->getClientOriginalName();
      $save_ol_name = $pdf->getClientOriginalName();
      $fn1 = strtotime("now");
      $fn2 = mt_rand(1, 99999);
      $fn3 = mt_rand(1, 99999);
      $fn4 = mt_rand(1, 99999);
      $sv_set_name = "gli".$fn1.$fn2.$fn3.$fn4.".pdf";


      Storage::disk('local')->put($sv_set_name,  File::get($pdf));
      $result = Storage::disk('public')->put('contract/'.$fileEncryptionName,  File::get($file));
      $file_url   = storage_path('app/private') . '/uploads/'.$sv_set_name;
      $client_id  = $request->client_id;
      $company_id = $request->company_id;

               if($request->type == 1){ // 顧客画面からアップ

                 if($this->saveContract($file_url,$client_id,$company_id,null,null,null,null,$sv_set_name,$save_ol_name)){

                  Crofun::log_create(Auth::user()->id,$request->id,config('constant.CONTRACT'),config('constant.operation_FILEUP'),config('constant.CLIENT_EDIT'),$company_id,$save_ol_name,$request->id,null,null);

                  return response()->json([
                   'status_code' => 200,
                   'message'     =>trans('message.save_success')
                 ]);

               	   }else { //プロジェクト編集画面からアップ
                    unlink($file_url);
                    return response()->json([
                     'status_code' => 500,
                     'message'     =>trans('message.save_fail')
                   ]);

                  }
                }else {

                 $headquarter_id = $request->headquarter_id;
                 $department_id  = $request->department_id;
                 $group_id       = $request->group_id;
                 $project_id     = $request->project_id;

                 if($this->saveContract(
                   $file_url,
                   $client_id,
                   $company_id,
                   $headquarter_id,
                   $department_id,
                   $group_id,
                   $project_id,$sv_set_name,$save_ol_name
                 ))
                 {


                  Crofun::log_create(Auth::user()->id,$request->id,config('constant.CONTRACT'),config('constant.operation_FILEUP'),config('constant.PROJECT_EDIT'),$company_id,$save_ol_name,$request->id,null,null);

                  return response()->json([
                    'status_code' => 200,
                    'message'     =>trans('message.save_success')
                  ]);

                }else {
                  unlink($file_url);
                  return response()->json([
                    'status_code' => 500,
                    'message'     =>trans('message.save_fail')
                  ]);

                }

              }


            }

          }

          protected function saveContract($contract_file,$client_id,$company_id,$headquarter_id,$department_id,$group_id,$project_id,$save_sv_name,$save_ol_name){

           $contract                 = new Contract_MST();

           $contract->id             = $this->getMaxId()[0]->max+1;
           $contract->contract_file  = $contract_file;
           $contract->client_id      = $client_id;
           $contract->company_id     = $company_id;
           $contract->headquarter_id = $headquarter_id;
           $contract->department_id  = $department_id;
           $contract->group_id       = $group_id;
           $contract->project_id     = $project_id;
           $contract->save_sv_name     = $save_sv_name;
           $contract->save_ol_name     = $save_ol_name;
           return $contract->save();


         }

         protected function getMaxId(){

          $id  = DB::select('select MAX(id) from contract');

          return $id;
        }

      }

      ?>
