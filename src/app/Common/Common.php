<?php // Code within app\Helpers\Helper.php

namespace App\Common;
use App\User;
use Auth;
use App\Company;
use App\Company_MST;
use App\Headquarters_MST;
use App\Department_MST;
use App\Group_MST;
use App\Position_MST;
use App\Customer_MST;
use App\Project_MST;
use App\Concurrently;
use App\Diagram;
use App\Cost_MST;
use Carbon\Carbon;
use App\Rule_MST;
use DB;

class Common
{
 

    public static function checkUserCompany($usr_id)
    {
      $user_rule  = Auth::user()->rule;
      $rule_action = Rule_MST::where('id',$user_rule)
               ->first();
      $flag = false;

      if($rule_action){
         
          /*スーパーフラグなら全会社OK*/
          if($rule_action->superuser_user == 1){
         
              $flag = true;
          }
      }     

      if($flag == false){
          $company_id_list = DB::table('concurrently_mst')->where('usr_id', $usr_id)->where('status',true)->pluck('company_id')->toArray();
          array_unshift($company_id_list,Auth::user()->company_id);
        }else{
          $company_id_list = DB::table('company_mst')->pluck('id')->toArray();

        }  

          return $company_id_list;
    }
    

   public static function getListUserId(){
          /**兼務と本務の会社IDを取得**/
          $company_id_list = DB::table('concurrently_mst')->where('usr_id', Auth::user()->id)->pluck('company_id')->toArray();
          /**配列に入れる**/
          array_unshift($company_id_list,Auth::user()->company_id);
          
          /**兼務の情報**/
          $concurrent_user_id_list = DB::table('concurrently_mst')
                                   ->wherein('company_id', $company_id_list)
                                   ->pluck('usr_id')->toArray();
          /**本務の情報**/
          $usr_mst_list_id         = DB::table('user_mst')
                                   ->wherein('company_id', $company_id_list)
                                   ->pluck('id')->toArray();
          /**ユーザーIDを格納**/
          $list_user_id  = array_merge($concurrent_user_id_list ,$usr_mst_list_id );

          return  $list_user_id;

   }
   
   public static function get_min_created_time_import_log(){
          
        $id         = DB::select('select MIN(created_at) from import_log');
        $created_at = $id[0]->min;
        return $created_at;

  }
  
  public static function get_max_created_time_import_log(){
          
        $id         = DB::select('select MAX(created_at) from import_log');
        $created_at = $id[0]->max;
        return $created_at;

  }

  public static function get_min_expiration_date_credit(){
          
        $id                = DB::select('select MIN(expiration_date) from credit_check');
        $expiration_date   = $id[0]->min;
        return $expiration_date;

  }
  
  public static function get_max_expiration_date_credit(){
          
        $id              = DB::select('select MAX(expiration_date) from credit_check');
        $expiration_date = $id[0]->max;
        return $expiration_date;

  }

   public static function getCompanyById($company_id){
       
        $company = Company_MST::where('id',$company_id)->first();

        return $company;
           
   }
  
    public static function getHeadquarterById($headquarter_id){

        $headquarter = Headquarters_MST::where('id',$headquarter_id)->first();

        return $headquarter;
   }
   
    public static function getDepartmentById($department_id){

        $headquarter = Department_MST::where('id',$department_id)->first();

        return $headquarter;
   }
   
    public static function getGroupById($group_id){

        $group = Group_MST::where('id',$group_id)->first();

        return $group;

        
   }

   public static function getPositionByID($position_code){

        $position = Position_MST::where('id',$position_code)->first();

        return $position;

   }
   
    public static function getCustomerByCode($client_code,$company_id){
      
           $customer = Customer_MST::where('client_code',$client_code)->where('company_id',$company_id)->first();

           return $customer;
    }
    // 売掛金残データをアップロードする際に顧客IDを取得
    public static function getCustomerByCodeMain($client_code,$company_id){
      
           $customer = Customer_MST::where('client_code_main',$client_code)->where('company_id',$company_id)->first();

           return $customer;


    }

    // 売掛金残データをアップロードする際にプロジェクトIDを取得
    public static function getProjectByCode($project_code,$company_id){
      
           $project = Project_MST::where('project_code',$project_code)->where('company_id',$company_id)->first();

           return $project;


    }
    public static function covertToString($data){
          
          return (string)$data;
    }

    public static function getToDay(){

    	  return date("Y-m-d h:i:s");
    }
    
    public static function getToDayCSV(){

        return date("Ymdhis");
    }
    

}

?>