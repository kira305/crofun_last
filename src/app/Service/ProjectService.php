<?php
namespace App\Service;
use App\Project_MST;
use App\Customer_MST;
use Auth;
use Mail;
use DB;
use Exception;
use Common;
class ProjectService
{

    /*
    * search project when load page in first time
    *
    * return project's detail list
    */

    public function getAllProject(){

        $usr_id      = Auth::user()->id;
        $company_id  = Common::checkUserCompany($usr_id);
        $customers   = Project_MST::where('company_id', Auth::user()->company_id)
                         ->orderBy('project_mst.id', 'desc')
                                   ->get();

        return $customers;
    }
    // get session value to array
    /*
    * get project detail by id and modify data to array
    * $request if session's variable is existed then push it to array or push '' to array
    * return project's array attribute
    */
    public function getSearchCondition($request){

        $condition = array();

        if ($request->session()->exists('company_id_p')) {

              array_push($condition,session('company_id_p'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('headquarter_id_p')) {

              array_push($condition,session('headquarter_id_p'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('department_id_p')) {

              array_push($condition,session('department_id_p'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('group_id_p')) {

              array_push($condition,session('group_id_p'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('client_code')) {

              array_push($condition,session('client_code'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('personal_code')) {

              array_push($condition,session('personal_code'));

        }else{

              array_push($condition,"");
        }


        if ($request->session()->exists('client_name')) {

              array_push($condition,session('client_name'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('project_code')) {

              array_push($condition,session('project_code'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('project_name')) {

              array_push($condition,session('project_name'));

        }else{

              array_push($condition,"");
        }

        if ($request->session()->exists('project_status')) {

              array_push($condition,session('project_status'));

        }else{

              array_push($condition,"");
        }

        return $condition;
    }
    // search project by condition
    /*
    * search project list by condition
    *
    * return project's detail list
    */
    public function search($company_id,$headquarter_id,$department_id,$group_id,$client_code,$personal_code,$client_name,$project_code,$project_name,$project_status, $isDownload = false){

            $usr_id        = Auth::user()->id;
            $company_id_1  = Common::checkUserCompany($usr_id); // 本務の会社id と 兼務の会社id

            $projects    = Project_MST::join('customer_mst','customer_mst.id','=','project_mst.client_id')
                         ->orderBy('project_mst.id', 'desc')

                         ->select('project_mst.*','client_name_kana','client_code','client_code_main','corporation_num')
                         ->when($client_code != "", function ($query) use ($client_code) {
                            return $query->where(function ($childQuery) use ($client_code) {
                                $childQuery->where('customer_mst.client_code', $client_code)
                                    ->orWhere('customer_mst.client_code_main', $client_code);
                            });
                         });

            if($company_id != ""){

                 $projects = $projects->where('project_mst.company_id',$company_id);

             }

            if($headquarter_id != ""){

                 $projects = $projects->where('headquarter_id',$headquarter_id);

             }

            if($department_id != ""){

                 $projects = $projects->where('department_id',$department_id);

             }

            if($group_id != ""){

                 $projects = $projects->where('group_id',$group_id);

             }

            // if($client_code != ""){

            //      // $projects = $projects->where('client_code_main',$client_code)->orwhere('client_code',$client_code);
            //      $projects = $projects->where(function($query) use ($client_code){

            //                             $query->where('client_code' , $client_code)
            //                             ->orWhere('client_code_main', $client_code);

            //                           });
            //  }

            if($personal_code != ""){

                 $projects = $projects->where('corporation_num',$personal_code);

             }

            if($client_name != ""){

                 $projects = $projects->where('client_name_kana','like',"%$client_name%");

             }

            if($project_code != ""){

                 $projects = $projects->where('project_code',$project_code);

             }

            if($project_name != ""){

                 $projects = $projects->where('project_name','like',"%$project_name%");

             }

            if($project_status === '1' || $project_status === '2'){

                  if($project_status == 1){

                     $project_status   = true;

                  }else {

                     $project_status   = false;

                  }
                  $projects = $projects->where('project_mst.status',$project_status);

             }

            return $isDownload ? $projects->get() : $projects->paginate(25);




    }

    //配列にプロジェクト情報を入れる
    /*
    * get project detail by id and modify data to array
    * $project_id : プロジェクト id
    * return project's array attribute
    */
    public function changeFormatData($project_id){

        $project        = Project_MST::where('id',$project_id)->first();

        $project_array = array();

        array_push($project_array, $project->project_code);
        array_push($project_array, $project->project_name);
        array_push($project_array, $project->headquarter->headquarters);
        array_push($project_array, $project->department->department_name);
        array_push($project_array, $project->group->group_name);
        array_push($project_array, $project->customer->client_code_main);
        array_push($project_array, $project->customer->client_name);
        array_push($project_array, $project->customer->client_name_kana);
        if($project->status == true){

                array_push($project_array, '取引中');

        }else {

                array_push($project_array, '取引終了');
        }
        return $project_array;

    }
    /*
    * check session is existed
    * $request : data have been saved
    * return if exist return true or not return false
    */
    public function checkSessionExist($request){

        if(
              $request->session()->exists('company_id_p')     ||
              $request->session()->exists('headquarter_id_p') ||
              $request->session()->exists('department_id_p')  ||
              $request->session()->exists('group_id_p')       ||
              $request->session()->exists('client_code')      ||
              $request->session()->exists('personal_code')    ||
              $request->session()->exists('client_name')      ||
              $request->session()->exists('project_code')     ||
              $request->session()->exists('project_name')
          ){

              return 1;

          }else {

              return 0;
          }

    }

    /*
    * get list projects by search condition
    * $request : search condition
    * return call funtion create stream csv file
    */
    public function getProjectData($request){

        $list_projects = array();

        if($this->checkSessionExist($request) == 1){

            $condition = $this->getSearchCondition($request); // get search condition have been saved in session

            $projects  = $this->search($condition[0],$condition[1],$condition[2],$condition[3],$condition[4],$condition[5],$condition[6],$condition[7],$condition[8],$condition[9], true);

            foreach ($projects as $project) {

               array_push($list_projects,$this->changeFormatData($project->id));

            }

        }else {

             $projects = $this->getAllProject();

             foreach ($projects as $project) {

               array_push($list_projects,$this->changeFormatData($project->id));

             }
        }

      return $this->getDataForCreateCsv($list_projects);

    }
    /*
    * get data by id for create csv
    * $project_id : プロジェクト id
    * return callback
    */
    public function GetOnceProjectData($project_id){

       $list_projects = array();
       array_push($list_projects,$this->changeFormatDataProject($project_id));

       return $this->getDataForCreateCsv1($list_projects);

    }

    /*
    * get data for create csv
    * $projects: プロジェクト リスト
    * return callback stream dowload
    */

    public function getDataForCreateCsv($projects){


                $columns = array('プロジェクトコード', 'プロジェクト名', '担当事業本部', '担当部署', '担当Grp', '顧客コード', '顧客名','顧客名カナ','ステータス');

                echo "\xEF\xBB\xBF";


                $callback = function() use ($columns,$projects)
                {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);

                    //fwrite($file, "sep=\t" . "\r\n");
                    foreach($projects as $project) {


                           fputcsv($file,  $project);


                    }

                    fclose($file);
                };

                return $callback;
    }

    /*
    * create csv's stream data at update screen
    * $projects: プロジェクト リスト
    * return callback stream dowload
    */

    public function  getDataForCreateCsv1($projects){

                $columns = array('プロジェクトコード', 'プロジェクト名', '担当事業本部', '担当部署', '担当Grp', '顧客コード', '顧客名','集計コード','集計コード名','取引想定限','単発','スポット取引想定','ステータス','備考');

                echo "\xEF\xBB\xBF";

                $callback = function() use ($columns,$projects)
                {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);

                    //fwrite($file, "sep=\t" . "\r\n");
                    foreach($projects as $project) {


                           fputcsv($file,  $project);


                    }

                    fclose($file);
                };

                return $callback;

    }

    /*
    * push project's detail to array
    * $project_id: プロジェクト　id
    * return project detail array
    */

   public function changeFormatDataProject($project_id){

        $project        = Project_MST::where('id',$project_id)->first();

        $project_array = array();

        array_push($project_array, $project->project_code);
        array_push($project_array, $project->project_name);
        array_push($project_array, $project->headquarter->headquarters);
        array_push($project_array, $project->department->department_name);
        array_push($project_array, $project->group->group_name);

        if($project->customer->status != 3){

            array_push($project_array, $project->customer->client_code);

        }else {

            array_push($project_array, $project->customer->client_code_main);
        }

        array_push($project_array, $project->customer->client_name);
        array_push($project_array, $project->get_code);
        array_push($project_array, $project->get_code_name);
        array_push($project_array, $project->transaction_money);
        array_push($project_array, $project->once_shot);
        array_push($project_array, $project->transaction_shot);
        if($project->status == true){

                array_push($project_array, '取引中');

        }else {

                array_push($project_array, '取引終了');
        }
        // $note = str_replace( "&#13;&#10;",'\n', $project->note);
        $note = explode('&#13;&#10;', $project->note);
        $note_result = '';
        foreach ($note as $n) {
          $note_result = $note_result."\n".$n;
        }

        array_push($project_array, $note_result);

        return $project_array;

   }
     //最新の与信情報のID
     public function getCreditIdlatest(){

        $credit_id  = DB::select('select client_id, max(get_time) as get_time from credit_check group by client_id');
        return $credit_id;
     }
      //最新の与信情報
     public function crediteslatest($company_id,$customers){

      $credit_id_data = $this->getCreditIdlatest();
      $credites     = Customer_MST::whereIn('credit_check.get_time', array_column($credit_id_data, 'get_time'))->join('credit_check', 'customer_mst.id', '=', 'credit_check.client_id')->where('credit_check.client_id',$customers);

      return $credites;

     }
}

?>
