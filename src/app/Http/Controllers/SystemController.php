<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Cost_MST;
use App\system;
use Auth;
use Response;
use DB;
use Common;
use Crofun;
class SystemController extends Controller
{    /**
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
	public function index(Request $request){

		if ($request->isMethod('post')) {
		}else{
		}

         
		if($request->session()->exists('company_id_c')     ||
			$request->session()->exists('headquarter_id_c') ||
			$request->session()->exists('status_c')){

		}
		//全件呼び出し
		$system_datas = system::orderBy('f_setting_group', 'asc')->orderBy('f_setting_name', 'asc')->get();
		//$system_datasx = new \App\system();

        // 削除 (指定の値を個別に)
        $request->session()->forget('ms_1');
        //保存
		$set_array = array();
		foreach ($system_datas as $system_data){
			$set_array[$system_data->f_system_info_key]['f_system_info_key'] = $system_data->f_system_info_key;
			$set_array[$system_data->f_system_info_key]['f_setting_group'] = $system_data->f_setting_group;
			$set_array[$system_data->f_system_info_key]['f_setting_name'] = $system_data->f_setting_name;
			$set_array[$system_data->f_system_info_key]['f_details_control'] = $system_data->f_details_control;
			$set_array[$system_data->f_system_info_key]['f_setting_data'] = $system_data->f_setting_data;
			$set_array[$system_data->f_system_info_key]['f_setting_nm'] = $system_data->f_setting_nm;
			$set_array[$system_data->f_system_info_key]['edit_flg'] = 0;
			$set_array[$system_data->f_system_info_key]['new_ent'] = 0;
		}
		
		//$request->session()->put('ms_1', $set_array);
    	//$s_entry = $request->session()->get('ms_1', array());

		$hash_object = array( (object) $set_array );
		$disabled_ctl = "";
		return view('system.index',['system_datas' => $set_array,'disabled_ctl' => $disabled_ctl]);
            
    }

    public function confirmation(Request $request){

    	$mode = $request->act_bnt;
		// セッションに入力情報を保存？
		switch ($mode) {
		case "row_add":		//項目追加
		case "row_conf":	//確認
	        // 削除 (指定の値を個別に)
	        $request->session()->forget('ms_1');
	        // セッションに入力情報を保存
			$input = $request->all();
	        $request->session()->put('ms_1', $input['m_s1']);
			break;
		case "row_upd":		//更新
		case "row_back":	//戻る
			break;
		}
    	
    	//セッションからセット
    	$s_entry = $request->session()->get('ms_1', array());

    	//状態
    	$disabled_ctl = "";
    	$message = array();
		switch ($mode) {
		case "row_add":		//項目追加

			$s_max = max(array_keys($s_entry)); //最大値
			++$s_max;
			$s_entry[$s_max]['f_system_info_key'] = "";
			$s_entry[$s_max]['f_setting_group'] = "";
			$s_entry[$s_max]['f_setting_name'] = "";
			$s_entry[$s_max]['f_details_control'] = 0;
			$s_entry[$s_max]['f_setting_data'] = "";
			$s_entry[$s_max]['f_setting_nm'] = "";
			$s_entry[$s_max]['edit_flg'] = 1;
			$s_entry[$s_max]['new_ent'] = 1;
			
			
			$disabled_ctl = "";
			$message['msg'] = "";
			$message['color'] = "";
			break;
		case "row_conf":	//確認
			//入力チェック
			$ent1 = 0;
			$ent2 = 0;
			foreach ($s_entry as $Key => $system_data){
				if (!empty($system_data['edit_flg'])){
					++$ent1;
					if (empty($system_data['f_setting_group']) ||
					    empty($system_data['f_setting_name']) ||
					    empty($system_data['f_setting_name']) ||
					    empty($system_data['f_setting_data']) ||
					    empty($system_data['f_setting_nm'])
					){
						++$ent2;
					}
				}
			}
			if ($ent2 != 0){
				$message['msg'] = "全項目必須入力です。";
				$message['color'] = "red";
			}
			if ($ent1 == 0){
				$message['msg'] = "変更対象が指定されていません。";
				$message['color'] = "red";
			}

			//重複確認
			foreach($s_entry as $key_data_a => $val_data_a){
				if (!empty($s_entry[$key_data_a]['f_setting_name'])){
					foreach($s_entry as $key_data_b => $val_data_b){
						if ($s_entry[$key_data_a]['f_setting_name'] == $s_entry[$key_data_b]['f_setting_name'] && $key_data_a != $key_data_b){
							$s_entry[$key_data_a]['dis_Error'] = "svalue has-error";
							$s_entry[$key_data_b]['dis_Error'] = "svalue has-error";
							$message['msg'] = "管理キーが重複しています。";
							$message['color'] = "red";
						}
					}
				}
			}
			
			
			if (empty($message['msg'])){
				$disabled_ctl = "conf";
				$message['msg'] = "設定内容に問題がないか確認を行ってください。";
				$message['color'] = "green";
			}else{
				$disabled_ctl = "";
				$mode = "row_back";
			}
			break;
		case "row_back":	//戻る
			$disabled_ctl = "";
			$message['msg'] = "";
			$message['color'] = "";
			break;
		case "row_upd":		//更新
			if ($this->system_edit_data($s_entry)){
				$disabled_ctl = "row_back";
				$message['msg'] = "データ反映時にエラーが発生しました。";
				$message['color'] = "red";
			}else{
				$disabled_ctl = "";
				$message['msg'] = "更新が完了しました。";
				$message['color'] = "green";
			}
			break;
		}
    	
		//画面セット項目
    	$set_array = array();
		foreach ($s_entry as $Key => $system_data){
			if ($mode == "row_conf"){
				if (!empty($system_data['edit_flg'])){
					if (empty($system_data['edit_flg'])|| $mode =="row_upd"){
						$set_array[$Key]['edit_flg'] = 0;
					}else{
						$set_array[$Key]['edit_flg'] = 1;
					}
					if (empty($system_data['f_details_control'])){
						$set_array[$Key]['f_details_control'] = 0;
					}else{
						$set_array[$Key]['f_details_control'] = 1;
					}
					$set_array[$Key]['f_system_info_key'] = $Key;
					$set_array[$Key]['f_setting_group'] = $system_data['f_setting_group'];
					$set_array[$Key]['f_setting_name'] = $system_data['f_setting_name'];
					$set_array[$Key]['f_setting_data'] = $system_data['f_setting_data'];
					$set_array[$Key]['f_setting_nm'] = $system_data['f_setting_nm'];
					$set_array[$Key]['new_ent'] = $system_data['new_ent'];
					if (!empty($system_data['dis_Error'])){
						$set_array[$Key]['dis_Error'] = $system_data['dis_Error'];
					}
				}
			}else{
				if (empty($system_data['edit_flg']) || $mode =="row_upd"){
					$set_array[$Key]['edit_flg'] = 0;
				}else{
					$set_array[$Key]['edit_flg'] = 1;
				}
				if (empty($system_data['f_details_control'])){
					$set_array[$Key]['f_details_control'] = 0;
				}else{
					$set_array[$Key]['f_details_control'] = 1;
				}
				$set_array[$Key]['f_system_info_key'] = $Key;
				$set_array[$Key]['f_setting_group'] = $system_data['f_setting_group'];
				$set_array[$Key]['f_setting_name'] = $system_data['f_setting_name'];
				$set_array[$Key]['f_setting_data'] = $system_data['f_setting_data'];
				$set_array[$Key]['f_setting_nm'] = $system_data['f_setting_nm'];
				$set_array[$Key]['new_ent'] = $system_data['new_ent'];
				if (!empty($system_data['dis_Error'])){
					$set_array[$Key]['dis_Error'] = $system_data['dis_Error'];
				}
			}
		}	

    	return view('system.index',['system_datas' => $set_array,'disabled_ctl' => $disabled_ctl,'message' => $message]);
            
    }
    
    public function system_edit_data($set_array){
    	
    	$condition = false;
    	
		foreach ($set_array as $Key => $edit_data){
			if (!empty($edit_data['edit_flg'])){
				if ($this->system_data_create($edit_data['new_ent'],$edit_data)){
			    	$condition = true;
				}
			}
		}
        return  $condition;
    
    }

    public function system_data_create($u_mode,$set_data){
	
		$condition = false;
		// オブジェクト作成
    	if ($u_mode == 0){
    		//更新
			$e_system = system::where('f_system_info_key',$set_data['f_system_info_key'])->first();
			$old_date  = json_encode($e_system); 
    	}else{
    		//新規
	    	$e_system = new system();
    	}

		$e_system->f_setting_group   = $set_data['f_setting_group'];
		if (empty($set_data['f_details_control'])){
			$e_system->f_details_control = 0;
		}else{
			$e_system->f_details_control = 1;
		}
		$e_system->f_setting_name    = $set_data['f_setting_name'];
		$e_system->f_setting_data    = $set_data['f_setting_data'];
		$e_system->f_setting_nm      = $set_data['f_setting_nm'];
    	if (empty($e_system->f_dummy01)){
			$e_system->f_dummy01  = "''";
    	}
    	if (empty($e_system->f_insert_id)){
			$e_system->f_insert_id  = 0;
    	}
    	if (empty($e_system->f_update_id)){
			$e_system->f_update_id  = 0;
    	}
    	
    	try {

			DB::beginTransaction();

			if($e_system->save()){ //保存

				if ($u_mode == 0){
	                Crofun::log_create(Auth::user()->id,$e_system->id,config('constant.system'),config('constant.operation_UPDATE'),config('constant.SYSTEM_NEW'),null,$e_system->f_setting_nm,$e_system->f_system_info_key,json_encode($e_system),$old_date);
            	}else{
	                Crofun::log_create(Auth::user()->id,$e_system->id,config('constant.system'),config('constant.operation_UPDATE'),config('constant.operation_CRATE'),null,$e_system->f_setting_nm,$e_system->f_system_info_key,json_encode($e_system),null);
		    	}

				DB::commit();  
//				return back()->with('success', trans('message.save_success'));  
			}

		}catch(Exception $e) {
			$condition = true;
			DB::rollBack();  			// 情報を保存しない
			throw new Exception($e); 	// エラーが発生したら別途で解決
		}	

		return  $condition;

    }

}
