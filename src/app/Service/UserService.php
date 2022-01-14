<?php
namespace App\Service;
use App\User;
use App\Concurrently;
use Auth;
use Common;
use Exception;
class UserService implements UserServiceInterface
{

   public function getAllUsers(){
        
      $users = User::orderBy('company_id', 'asc')->orderBy('retire', 'asc')->orderBy('usr_code', 'asc');

      return $users->paginate(25);

   }

   public function getAllUserOfCompany(){

            $usr_id       = Auth::user()->id;
            $list_user_id = Common::getListUserId();
            
            $users = User::whereIn('id',$list_user_id)->orderBy('company_id', 'asc')->orderBy('retire', 'asc')->orderBy('usr_code', 'asc');

            return $users->paginate(25);
   }

    public function searchBySuperRule($company_id,$headquarter_id,$department_id,$group_id,$user_code,$user_name,$position_id,$rule_id){
            
            $users = User::orderBy('company_id', 'asc')->orderBy('retire', 'asc')->orderBy('usr_code', 'asc');
            
            if($company_id != ""){
                 
                 $users = $users->where('company_id',$company_id);

             }
               
             if($headquarter_id != ""){
                 
                 $users = $users->where('headquarter_id',$headquarter_id);

             }
             

             if($department_id != ""){
                 
                $users = $users->where('department_id',$department_id);
                

             }
             
            if($group_id != ""){
                 
                $users = $users->where('group_id',$group_id);
                

             }
             
             
             if($user_code != ""){
                  
                 $users->where('usr_code', $user_code);
  
             }
               
            if($user_name != ""){
                  
                 $users->where('usr_name','like', "%$user_name%" );
  
             }

            if($position_id != ""){
                  
                 $users->where('position_id', $position_id);
  
             }

            if($rule_id != ""){
                  
                 $users->where('rule', $rule_id);
  
             }

            return $users->paginate(25);

    }

    public function searchUser($company_id,$headquarter_id,$department_id,$group_id,$user_code,$user_name,$position_id,$rule_id){
             
             $usr_id                = Auth::user()->id;
       
             $company_id_look       = Common::getListUserId();
              /**usrmut の検索結果のユーザーID(配列)**/
             $list_mst_user_id = $this->getListUserInMst(
                                                          $company_id,
                                                          $headquarter_id,
                                                          $department_id,
                                                          $group_id,
                                                          $user_code,
                                                          $user_name,
                                                          $position_id,
                                                          $rule_id
                                                        );

              /**兼務MST の検索結果のユーザーID(配列)**/            
             $list_concurrent_user_id = $this->getListUserConcurrently(
                                                                       $company_id,
                                                                       $headquarter_id,
                                                                       $department_id,
                                                                       $group_id,
                                                                       $user_code,
                                                                       $user_name,
                                                                       $position_id,
                                                                       $rule_id);

             $list_user_id     = array_merge($list_mst_user_id,$list_concurrent_user_id);

             $list_get_user_id = Common::getListUserId();
           
             $users            = User::whereIn('id', $list_user_id)
                                ->whereIn('id', $list_get_user_id)
                                ->orderBy('company_id', 'asc')
                                ->orderBy('retire', 'asc')->orderBy('usr_code', 'asc');
            
             return $users->paginate(25);
            
    }
        
        public function getListUserInMst($company_id,$headquarter_id,$department_id,$group_id,$user_code,$user_name,$position_id,$rule_id){

             $usr_id = Auth::user()->id;
       
             $company_id_look  = Common::checkUserCompany($usr_id);
             
             $users = User::whereIn('user_mst.company_id', $company_id_look)->select('user_mst.*');
            
              if($company_id != ""){
                 
                 $users = $users->where('company_id',$company_id);

             }
               
             if($headquarter_id != ""){
                 
                 $users = $users->where('headquarter_id',$headquarter_id);

             }
             

             if($department_id != ""){
                 
                $users = $users->where('department_id',$department_id);
                

             }
             
            if($group_id != ""){
                 
                $users = $users->where('group_id',$group_id);
                

             }
             
             
             if($user_code != ""){
                  
                 $users->where('usr_code', $user_code);
  
             }
               
            if($user_name != ""){
                  
                 $users->where('usr_name','like', "%$user_name%" );
  
             }

            if($position_id != ""){
                  
                 $users->where('position_id', $position_id);
  
             }

            if($rule_id != ""){
                  
                 $users->where('rule', $rule_id);
  
             }
             
             $users      = $users->get();
             
             $list_user_id  = array();

             foreach ($users as $user) {
                 
                   array_push($list_user_id,$user->id);
             }
           
            return $list_user_id;


        }

        public  function getListUserConcurrently($company_id,$headquarter_id,$department_id,$group_id,$user_code,$user_name,$position_id,$rule_id){
          
       
             $concurrently         = Concurrently::select('concurrently_mst.*');
               
            if($company_id != ""){
                 
                 $concurrently  = $concurrently->where('company_id',$company_id);

             }
             
         
          if($headquarter_id != ""){
                 
                $concurrently  = $concurrently->where('headquarter_id',$headquarter_id);

             }
             
             
             if($department_id != ""){
                 
                 $concurrently  = $concurrently->where('department_id',$department_id);
                

             }
              
            if($group_id != ""){
                 
                $concurrently  = $concurrently->where('group_id',$group_id);                

             }
             
             if($user_code != ""){
                  
                 $concurrently  = $concurrently->where('usr_code',$user_code);       
  
             }
              
             if($user_name != ""){
                  
                 $concurrently  = $concurrently->where('usr_name','like', "%$user_name%" );  
  
             }
              
             if($position_id != ""){
                  
                 $concurrently  = $concurrently->where('position_id',$position_id);       
  
             }
              
             if($rule_id != ""){
                  
                 $concurrently  = $concurrently->where('rule_id',$rule_id);       
  
             }

             $concurrently  = $concurrently->where('status',true); 
            
             // $concurrently  = $concurrently->get();
             $list_user_id  = array();
             $concurrently  = $concurrently->get();
             foreach ($concurrently as $c) {
                 
                   array_push($list_user_id,$c->usr_id);
             }
             
              
             return $list_user_id;
 
    }
    


    public function getUserBySession($request){
        
        $condition = array();
        if ($request->session()->exists('headquarter_id')) {
               
              $headquarter_id = session('headquarter_id');
              array_push($condition,$headquarter_id);
              
        }else{

              $headquarter_id = "";
              array_push($condition,$headquarter_id);
        }
        
        if ($request->session()->exists('department_id')) {
               
              $department_id  = session('department_id');
              array_push($condition,$department_id);
              
        }else{

              $department_id  = "";
              array_push($condition,$department_id);
        }
        
        if ($request->session()->exists('group_id')) {
               
              $group_id = session('group_id');
              array_push($condition,$group_id);
              
        }else{

              $group_id = "";
              array_push($condition,$group_id);
        }
        
        if ($request->session()->exists('usr_code')) {
               
              $usr_code = session('usr_code');
              array_push($condition,$usr_code);
            
              
        }else{

              $usr_code = "";
              array_push($condition,$usr_code);
        }
        
        if ($request->session()->exists('usr_name')) {
               
              $usr_name = session('usr_name');
              array_push($condition,$usr_name);
            
              
        }else{

              $usr_name = "";
              array_push($condition,$usr_name);
        }

        if ($request->session()->exists('position_id')) {
               
              $position_id = session('position_id');
              array_push($condition,$position_id);
            
              
        }else{

              $position_id = "";
              array_push($condition,$position_id);
        }
        
        if ($request->session()->exists('rule_id')) {
               
              $rule_id = session('rule_id');
              array_push($condition,$rule_id);
            
              
        }else{

              $rule_id = "";
              array_push($condition,$rule_id);
        }
        
        if ($request->session()->exists('company_id')) {
               
              $company_id = session('company_id');
              array_push($condition,$company_id);
            
              
        }else{

              $company_id = "";
              array_push($condition,$company_id);
        }

        return  $condition;

    }

    public function getUserInfor($usr_id){
         
           $user = User::where('id',$usr_id)->first();
           
           return $user;

    }

    public function getConcurrently($usr_id){

              $concurrents = Concurrently::where('usr_id',$usr_id)->get();
              return $concurrents;
    }

    public function checkUserIdIsExsist($usr_code){
        

            $user = User::where('usr_code',$usr_code)->first();

            if($user){

                return 1;

            }else {

                return 0;
            }

    }


}