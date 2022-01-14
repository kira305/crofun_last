<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Customer_name_MST;
use App\Rule_action;
use App\Menu;
use Auth;
use Response;
use Excel;
use Helper;
use Exception;
use DB;
use Crofun;
use App\Events\Event;
use App\Events\LogEvent;
class CustomnameController extends Controller
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


   //      $receivable = Receivable_MST::all();

     //    return view('receivable.index',["receivable" => $receivable]);


   // }
    public function index(Request $request){


     //リクエストが与えられたか？
      if ($request->isMethod('post')) {

          //画面からリクエストされている情報
          $company_id       = $request->company_id;
          $client_code      = $request->client_code;
          $client_name_kana = mb_convert_kana($request->client_name_kana, 'rhk');
          $corporation_num  = $request->corporation_num;
          $target_client_name_kana = mb_convert_kana($request->target_client_name_kana, 'rhk');

            session(['company_id_name'       => $company_id]);
            session(['client_code_name'      => $client_code]);
            session(['client_name_kana_name' => $client_name_kana]);
            session(['corporation_num_name'  => $corporation_num]);
            session(['target_client_name_kana_name'     => $target_client_name_kana]);



          $custom_name          = $this->search($company_id,$client_code,$client_name_kana,$corporation_num,$target_client_name_kana);

          return view('custom_name.index',[
                                          "custom_name" => $custom_name,
                                          "company_id"       =>session('company_id_name'),
                                          "client_code"      =>session('client_code_name'),
                                          "client_name_kana" =>session('client_name_kana_name'),
                                          "corporation_num"  =>session('corporation_num_name'),
                                          "target_client_name_kana" =>session('target_client_name_kana_name')
                                      ]);

      }

    //セッションの情報で検索　(他の画面から遷移した時)
      if(
          $request->session()->exists('company_id_name')       ||
          $request->session()->exists('client_code_name')      ||
          $request->session()->exists('client_name_kana_name') ||
          $request->session()->exists('corporation_num_name')  ||
          $request->session()->exists('target_client_name_kana_name')

         ){

          $condition = $this->searchCostBySession($request);

          $custom_name = $this->search($condition[0],$condition[1],$condition[2],$condition[3],$condition[4]);

              return view('custom_name.index',[
                                          "custom_name" => $custom_name,
                                          "company_id"       =>session('company_id_name'),
                                          "client_code"      =>session('client_code_name'),
                                          "client_name_kana" =>session('client_name_kana_name'),
                                          "corporation_num"  =>session('corporation_num_name'),
                                          "target_client_name_kana" =>session('target_client_name_kana_name')
                                      ]);

      }

      $company_id_R  =  Auth::user()->company_id;

      $custom_name = Customer_name_MST::leftjoin('customer_mst','customer_mst.id','=','customer_name.client_id')
                                     ->where('customer_mst.company_id',$company_id_R)
                                     ->orderBy('customer_mst.id', 'desc')
                                     ->orderBy('customer_name.created_at', 'desc')
                                     ->select('customer_name.*')
                                     ->paginate(15);

      return view('custom_name.index',['custom_name' => $custom_name]);

    }


    public function searchCostBySession($request){

        $condition = array();
        if ($request->session()->exists('company_id_name')) {

             $company_id = session('company_id_name');
             array_push($condition,$company_id);

        }else{

              $company_id = "";
              array_push($condition,$company_id);
        }

        if ($request->session()->exists('client_code_name')) {

              $client_code = session('client_code_name');
              array_push($condition,$client_code);

        }else{

              $client_code = "";
              array_push($condition,$client_code);
        }

        if ($request->session()->exists('client_name_kana_name')) {

              $client_name_kana  = session('client_name_kana_name');
              array_push($condition,$client_name_kana);

        }else{

              $client_name_kana  = "";
              array_push($condition,$client_name_kana);
        }

        if ($request->session()->exists('corporation_num_name')) {

              $corporation_num  = session('corporation_num_name');
              array_push($condition,$corporation_num);

        }else{

              $corporation_num  = "";
              array_push($condition,$corporation_num);
        }

        if ($request->session()->exists('target_client_name_kana_name')) {

              $target_client_name_kana  = session('target_client_name_kana_name');
              array_push($condition,$target_client_name_kana);

        }else{

              $target_client_name_kana  = "";
              array_push($condition,$target_client_name_kana);
        }



      return  $condition;

    }



    public function search($company_id,$client_code,$client_name_kana,$corporation_num,$target_client_name_kana){
      //大元の検索条件
      $custom_name = Customer_name_MST::leftjoin('customer_mst','customer_mst.id','=','customer_name.client_id')
                                     ->orderBy('customer_mst.id', 'desc')
                                     ->orderBy('customer_name.created_at', 'desc')
                                     ->select('customer_name.*')
                                     ->when($client_code != "", function ($query) use ($client_code) {
                                        return $query->where(function ($childQuery) use ($client_code) {
                                            $childQuery->where('customer_mst.client_code', $client_code)
                                                ->orWhere('customer_mst.client_code_main', $client_code);
                                        });
                                     });

            //検索の条件が有れば、条件をｾｯﾄする
            if($company_id != ""){

                $custom_name = $custom_name->where('customer_mst.company_id',$company_id);

             }

            // if($client_code != ""){

            //     $custom_name = $custom_name->where('customer_mst.client_code',$client_code)->orwhere('customer_mst.client_code_main',$client_code);

            //  }

             if($client_name_kana != ""){

                $custom_name = $custom_name->where('customer_mst.client_name_kana','like',"%$client_name_kana%");

             }

             if($corporation_num != ""){

                $custom_name = $custom_name->where('customer_mst.corporation_num',$corporation_num);

             }


             if($target_client_name_kana != ""){

                $custom_name = $custom_name->where('customer_name.client_name_hankaku_s','like',"%$target_client_name_kana%");

             }

             //検索結果
             $custom_name          = $custom_name->paginate(15);

             return $custom_name;

    }

    public function delete(Request $request){

         $id = $request->id;

         $del = Customer_name_MST::where('id',$id)->update(['del_flag' => true]);
         $del_data =Customer_name_MST::leftjoin('customer_mst','customer_mst.id','=','customer_name.client_id')->where('customer_name.id',$id)->first();

          Crofun::log_create(Auth::user()->id,$id,config('constant.CLIENTNAME'),config('constant.operation_DELETE'),config('constant.CLIENT_NAME'),$del_data->company_id,$del_data->client_name_hankaku_s,$del_data->client_code_main,null,null);

         return redirect('custom_name/index');

    }
}
