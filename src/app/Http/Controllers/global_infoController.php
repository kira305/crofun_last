<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Concerns;

use App\Service\UserServiceInterface;
use Illuminate\Http\Request;
use App\Events\GroupChangeEvent;
use Crofun;
use App\global_info;
use Mail;
use Auth;
use Response;
use Excel;
use Helper;
use DB;
use Common;
class global_infoController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	$dis_data = array(1=>"重要",2=>"注意",3=>"連絡" );
    	$sel_data = array("1"=>"掲載中","2"=>"掲載期間前","3"=>"掲載期間終了","4"=>"削除" );
		//時間帯

		if ($request->isMethod('post')) {
			$important_flg        = $request->important_flg;
		 	session(['important_flg_info' => $important_flg]);
		 	$search_condition  = $this->searchGlobalifoSession($request);
		 	$global_infos = $this->searchGlobalifo($search_condition);
		}
		if($request->session()->exists('important_flg_info')){
		  	$search_condition  = $this->searchGlobalifoSession($request);
		  	if (!empty($search_condition['important_flg'])){
		  		$global_infos = $this->searchGlobalifo($search_condition);
		  	}else{
				$global_infos = array();
		  	}
		}else {
			$global_infos = array();
		}
		return view('global_info.index',
				[
		         "global_infos" =>$global_infos,
				 "important_flg_info" =>session('important_flg_info'),
		         "dis_data" => $dis_data,
		         "sel_data" => $sel_data,
		        ]);
    }
    
    public function searchGlobalifo($search_condition){

    	$global_infos = global_info::orderBy('id', 'desc')
                                ->select('global_info.*');
			switch ($search_condition['important_flg']) {
			    case "1":	//掲載中
					$global_infos->where('start_date','<=',date('Y/m/d H:i:s'))->where('end_date','>=',date('Y/m/d H:i:s'));
					$global_infos->where('delete_flg',0);
			        break;
			    case "2":	//前
					$global_infos->where('start_date','>',date("Y/m/d H:i:s"));
					$global_infos->where('delete_flg',0);
			        break;
			    case "3":	//後
					$global_infos->where('end_date','<',date("Y/m/d H:i:s"));
					$global_infos->where('delete_flg',0);
			        break;
			    case "4":	//削除
					$global_infos->where('delete_flg',1);
			        break;
			}

           
             $global_infos = $global_infos->orderBy('id', 'desc')->paginate(5);

             return $global_infos;

    }
    
    public function searchGlobalifoSession($request){

        $condition = array();
        if ($request->session()->exists('important_flg_info')) {
               
              $condition['important_flg'] = session('important_flg_info');
              
        }else{
              $condition['important_flg'] = "";
        }

        return  $condition;

    }


    public function edit(Request $request){
        
        $Time_array_Get = Crofun::Time_array_Get();
    	
        if ($request->isMethod('post')) {

         	$mode1 = $request->input('mode1');
         	$mode2 = $request->input('mode2');

         	switch ($mode1) {
			case "add":
		 		$global_info = new global_info();
				$mode_name ="登録";
				$ret_view ="create";
				break;
			case "update":
         		$global_info = global_info::where("id",$request->id)->first();
				$mode_name ="更新";
				$ret_view ="edit";
				$old_date = json_encode($global_info);
				break;
			}

			$global_info->global_info_title      = $request->global_info_title;
			$global_info->global_info_content    = $request->global_info_content;


			$global_info->start_date             = $request->start_date;
			$global_info->start_time             = $request->start_time;

			$global_info->end_date               = $request->end_date;
			$global_info->end_time               = $request->end_time;
			$global_info->important_flg          = $request->important_flg; 
			$global_info->save_ol_name 			 = $request->save_ol_name;
			$global_info->save_sv_name 			 = $request->save_sv_name;

			if ($request->delete_flg == true){
				$global_info->delete_flg             = 1; 
			}else{
				$global_info->delete_flg             = 0; 
			}

         	switch ($mode2) {
			case "file_add":
	            $save_ol_name = $request->file('save_ol_name');
				$global_info->save_ol_name = $save_ol_name->getClientOriginalName();
				$fn1 = strtotime("now");
				$fn2 = mt_rand(1, 99999);
				$fn3 = mt_rand(1, 99999);
				$fn4 = mt_rand(1, 99999);
				$sv_set_name = "gli".$fn1.$fn2.$fn3.$fn4.".data";
				$global_info->save_sv_name = $sv_set_name;
				$global_info->save_ol_name = $save_ol_name->getClientOriginalName();
				Storage::disk('public')->put($sv_set_name,  File::get($save_ol_name));
				break;
			case "file_delete":
				//ファイル削除
				$global_info->save_ol_name = "";
				$global_info->save_sv_name = "";
				break;
			default:
                $validator = $this->validateData($request);
                if ($validator->fails()) {
                   
                    $errors = $validator->errors();

                    return view('global_info.'.$ret_view,
                    		[
                    		'global_info' => $global_info,
                    		'status' => $global_info->status,
                    		'errors' => $errors,
                    		'TIME_ARRAY'=>$Time_array_Get
                    		]);
                 
                }

         		if (!empty($request->start_date)){
         			$global_info->start_date             = $request->start_date . " " . $request->start_time . ":00";
         		}else{
         			$global_info->start_date = null;
         		}
         		if (!empty($request->end_date)){
	       		    $global_info->end_date               = $request->end_date   . " " . $request->end_time . ":00";
         		}else{
         			$global_info->end_date = null;
         		}

				unset($global_info['start_time']);
         		unset($global_info['end_time']);
         		$global_info_log = $global_info;
				$global_info_log->start_date = str_replace('/','-',$global_info_log->start_date);
				$global_info_log->end_date = str_replace('/','-',$global_info_log->end_date);
         		$global_info_log = json_encode($global_info);

         		if($global_info->save()){
                	
                	if (!empty($request->start_date)){
				    	$global_info['start_time'] = date('H:i', strtotime($global_info->start_date));
	         		}else{
				    	$global_info['start_time'] = "";
	         		}
	         		if (!empty($request->end_date)){
				    	$global_info['end_time'] = date('H:i', strtotime($global_info->end_date));
	         		}else{
				    	$global_info['end_time'] = "";
	         		}

		         	switch ($mode1) {
					case "add":
	  		       		Crofun::log_create(Auth::user()->id,$global_info->id,config('constant.global_info'),config('constant.operation_CRATE'),config('constant.SYSTEM_INFOR'),null,$global_info->global_info_title,$global_info->id,$global_info_log,null);
						break;
					case "update":
	         			Crofun::log_create(Auth::user()->id,$global_info->id,config('constant.global_info'),config('constant.operation_UPDATE'),config('constant.SYSTEM_INFOR'),null,$global_info->global_info_title,$global_info->id,$global_info_log,$old_date);
						break;
					}


                	return view('global_info.'.$ret_view,["message"=>$mode_name."が完了しました。",'global_info' => $global_info,'TIME_ARRAY'=>$Time_array_Get]);

                }else{
                   
                    return view('global_info.'.$ret_view,["message"=>trans('message.group_change_fail'),'global_info' => $global_info,'TIME_ARRAY'=>$Time_array_Get]);

                }				
				
				
				
			
			}
			
            
        }else{
			$global_info = global_info::where("id",$request->id)->first();
			$ret_view = "edit";
        }
       
    	if (!empty($global_info->start_date)){
    		$global_info['start_time'] = date('H:i', strtotime($global_info->start_date));
    	}else{
    		$global_info['start_time'] = "";
    	}
    	if (!empty($global_info->end_date)){
	    	$global_info['end_time'] = date('H:i', strtotime($global_info->end_date));
    	}else{
    		$global_info['end_time'] = "";
    	}

    	return view('global_info.'.$ret_view,['global_info' => $global_info,'mode' => "update",'TIME_ARRAY'=>$Time_array_Get]);

    }
   
    public function create(Request $request){

          $Time_array_Get = Crofun::Time_array_Get();

         if ($request->isMethod('post')) {
         	
                $validator = $this->validateData($request);

		 		$global_info                         = new global_info();
                $global_info->global_info_title      = $request->global_info_title;
                $global_info->global_info_content    = $request->global_info_content;

         	
         		$global_info->start_date             = $request->start_date;
         		$global_info->start_time             = $request->start_time;
                
         		$global_info->end_date               = $request->end_date;
          		$global_info->end_time               = $request->end_time;
                $global_info->important_flg          = $request->important_flg; 
	            $save_ol_name			             = $request->file('save_ol_name');
				$global_info->save_ol_name 			 = $save_ol_name->getClientOriginalName();

         		if ($request->delete_flg == true){
                	$global_info->delete_flg             = 1; 
         		}else{
                	$global_info->delete_flg             = 0; 
         		}

                if ($validator->fails()) {
                   
                    $errors = $validator->errors();

                    return view('global_info.create',
                    		[
                    		'global_info' => $global_info,
                    		'status' => $global_info->status,
                    		'errors' => $errors,
                    		'TIME_ARRAY'=>$Time_array_Get
                    		]);
                 
                }

                if($this->checkCodeRule($request)){
                          $validator->errors()->add('unique',trans('validation.code_unique'));
                          $errors = $validator->errors();
                          return view('global_info.create',['global_info' => $global_info,'status' => $global_info->status,'errors' => $errors,'TIME_ARRAY'=>$Time_array_Get]);
                }

                if($this->checkCodeRule($request)){
                  return view('global_info.create',['global_info' => $global_info,'unique' => trans('validation.code_unique'),'TIME_ARRAY'=>$Time_array_Get]);
                }
         		if (!empty($request->start_date)){
         			$global_info->start_date             = $request->start_date . " " . $request->start_time . ":00";
         		}else{
         			$global_info->start_date = null;
         		}
         		if (!empty($request->end_date)){
	       		    $global_info->end_date               = $request->end_date   . " " . $request->end_time . ":00";
         		}else{
         			$global_info->end_date = null;
         		}
         		unset($global_info['start_time']);
         		unset($global_info['end_time']);
				if($save_ol_name != null){
					$global_info->save_ol_name = $save_ol_name->getClientOriginalName();
					$fn1 = strtotime("now");
					$fn2 = mt_rand(1, 99999);
					$fn3 = mt_rand(1, 99999);
					$fn4 = mt_rand(1, 99999);
					$sv_set_name = "gli".$fn1.$fn2.$fn3.$fn4.".data";
					$global_info->save_sv_name = $sv_set_name;
					Storage::disk('public')->put($sv_set_name,  File::get($save_ol_name));
					//Storage::disk('public')->put($save_ol_name->getClientOriginalName(),  File::get($save_ol_name));
				}else{
					$global_info->save_ol_name = "";
					$global_info->save_sv_name = "";
				}
                if($global_info->save()){
	         		if (!empty($request->start_date)){
				    	$global_info['start_time'] = date('H:i', strtotime($global_info->start_date));
	         		}else{
				    	$global_info['start_time'] = "";
	         		}
	         		if (!empty($request->end_date)){
				    	$global_info['end_time'] = date('H:i', strtotime($global_info->end_date));
	         		}else{
				    	$global_info['end_time'] = "";
	         		}

	         		Crofun::log_create(Auth::user()->id,$global_info->id,config('constant.global_info'),config('constant.operation_CRATE'),config('constant.SYSTEM_INFOR'),null,$global_info->global_info_title,$global_info->id,json_encode($global_info),null);

                   return view('global_info.create',["message"=>"登録が完了しました。",'global_info' => $global_info,'TIME_ARRAY'=>$Time_array_Get]);

                }else{
                   
                    return view('global_info.create',["message"=>trans('message.group_change_fail'),'global_info' => $global_info,'TIME_ARRAY'=>$Time_array_Get]);

                }
         }
     	 $global_info = array();
		 $global_info  = new global_info();
    	
         return view('global_info.create',['global_info' => $global_info,'mode' => "create",'TIME_ARRAY'=>$Time_array_Get]);


    
    }
    
    public function checkCodeRule(Request $request){

		return false;

    }
    

    public function checkCodeWhenCreate(Request $request){
         
         $department       = Department_MST::where('id',$request->department_id)->first();
       
         $check_code_group = Group_MST::where("group_code",$request->group_code)->get();

         foreach ($check_code_group as $check) {
              
           if(($check->headquarter()->company_id == $department->headquarter()->company_id) && ($check->id !=  $request->id) ){

                       return true;

            }

         }

         return false;


    }
    
    public function validateData(Request $request){

        $validator = Validator::make($request->all(),[

            'global_info_title'         => 'required',
            'global_info_content'       => 'required',
            'important_flg'             => 'required',
        ],[
            'global_info_title.required'    => trans('validation.cost_name'),
            'global_info_content.required'  => trans('validation.cost_name'),
            'important_flg.required'        => "重要度を選択して下さい。",
        ]);
    	
		//個別
        $validator->after(function($validator) use ($request) {
            // セグメントチェック
			if (empty($request->start_date)){
				$validator->errors()->add('start_date',"掲載開始を入力して下さい。");
			}
			if (empty($request->end_date)){
				$validator->errors()->add('end_date',"掲載完了終了を入力して下さい。");
			}
			if (!empty($request->start_date) && empty($request->start_time)){
				$validator->errors()->add('start_date',"開始時間を指定してください。。");
			}
			if (!empty($request->end_date) && empty($request->end_time)){
				$validator->errors()->add('end_date',"終了時間を指定してください。。");
			}

			if (!empty($request->start_date) && !empty($request->end_date)){
			   if ($request->start_date > $request->end_date){
					$validator->errors()->add('start_date',"開始と終了が逆転しています。");
			   }
			   if ($request->start_date == $request->end_date){
					if ($request->start_time >= $request->end_time){
						$validator->errors()->add('start_date',"開始と終了が逆転しています。");
					}
			   }
			}
    	
        });
    	
    	return $validator;

    }

	public function download(Request $request,$id,$ol_name,$sv_name){

		$path = public_path()."/uploads/";
        
		if ($id == 0){
			$filePath = $sv_name;
			$filename = $ol_name;
			$filePath = $path.$filePath;
		}else{
         	$global_info = global_info::where("id",$request->id)->first();
			$filePath = $global_info->save_sv_name;
			$filename = $global_info->save_ol_name;
			$filePath = $path.$filePath;
			if (\File::exists($filePath)) {
				//echo "ファイルがあります！";
			}else{
				$filePath = $sv_name;
				$filename = $ol_name;
				$filePath = $path.$filePath;
				if (\File::exists($filePath)) {
					//echo "ファイルがあります！";
				}else{
					echo "ファイルが存在しません！";
				}
			}
		}

		

		$fileName = $filename;

		$fPath = $filePath;  // ファイルパスを取得
		$fName = $fileName;  // オリジナルのファイル名を取得

		return response()->download($fPath, $fName);  // ダウンロード処理
           
    }

}
