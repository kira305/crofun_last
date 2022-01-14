<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Company_MST;
use Mail;
use Auth;
use Response;
use Excel;
use Helper;
use DB;
use Crofun;
use Common;
class CompanyController extends Controller
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
        
        $usr_id        = Auth::user()->id; 
        $company_id    = Common::checkUserCompany($usr_id);
        $companies     = Company_MST::whereIn('id', $company_id)->orderBy('id', 'ASC')->paginate(20);
       
        return view('company.index',["companies" => $companies]);

    }

    public function edit(Request $request){
        

      
        if ($request->isMethod('post')) {
            
            $validator = Validator::make($request->all(),[

            
                    'company_name'               => 'required|max:25',
                    'abbreviate_name'            => 'required|max:10',
                         'logo'                  => 'nullable|mimes:jpg,png,jpeg'
                      

                ],[
                   
                    'company_name.required'           => trans('validation.company_name'),
                    'company_name.max'                => trans('validation.max_string_25'),
                    'abbreviate_name.required'        => trans('validation.company_abbreviate'),
                    'abbreviate_name.max'             => trans('validation.max_string_10'),
                    'logo.required'                   => trans('validation.company_logo'),
                    'logo.mimes'                      => trans('validation.company_logo_img')
           

                ]);
            session()->flashInput($request->input());
            $company_mst      = Company_MST::where("own_company",$request->own_company)->first();
            $own_company      = $request->own_company;
            $company_name     = $request->company_name;
            $abbreviate_name  = $request->abbreviate_name;
            $logo             = $request->file('logo');
            $note             = $request->note;
            $old_date         = json_encode($company_mst); 

            $company_mst->own_company     = $own_company;
            $company_mst->company_name    = $company_name; 
            $company_mst->abbreviate_name = $abbreviate_name;
            $company_mst->note            = $note;

            if ($validator->fails()) {


                    $errors = $validator->errors();
                    return view('company.edit',['company' => $company_mst,'errors' => $errors]);
                 
            }
         
            if($logo != null){
                   
                $save_ol_name = $logo->getClientOriginalName();
                $fn1 = strtotime("now");
                $fn2 = mt_rand(1, 99999);
                $fn3 = mt_rand(1, 99999);
                $fn4 = mt_rand(1, 99999);
                $extension = $request->file('logo')->getClientOriginalExtension();
                $sv_set_name = "log".$fn1.$fn2.$fn3.$fn4.'.'.$extension;


                $company_mst->logo            = $sv_set_name; 
                $company_mst->save_ol_name    = $logo->getClientOriginalName();
                Storage::disk('public')->put('logo/'.$sv_set_name,  File::get($logo));
            }

            Crofun::log_create(Auth::user()->id,$company_mst->id,config('constant.COMPANY'),config('constant.operation_UPDATE'),config('constant.COMPANY_EDIT'),$company_mst->id,$company_mst->abbreviate_name,$company_mst->id,json_encode($company_mst),$old_date);

            $company_mst->update();

            return view('company.edit',['company' => $company_mst,"message"=>'会社情報を変更しました。']);

        
         }

        $company_mst = Company_MST::where("own_company",$request->id)->first();
        return view('company.edit',['company' => $company_mst]);

    }
   
    public function create(Request $request){
           
         if ($request->isMethod('post')) {
            
             
            $validator = Validator::make($request->all(),[

            
                    'company_name'               => 'required|max:25',
                    'abbreviate_name'            => 'required|max:10',
                    'logo'                       => 'required|mimes:jpg,png,jpeg'

                ],[
                   
                    'company_name.required'           => trans('validation.company_name'),
                    'company_name.max'                => trans('validation.max_string_25'),
                    'abbreviate_name.required'        => trans('validation.company_abbreviate'),
                    'abbreviate_name.max'             => trans('validation.max_string_25'),
                    'logo.required'                   => trans('validation.company_logo'),
                    'logo.mimes'                      => trans('validation.company_logo_img')

                ]);
            session()->flashInput($request->input());
            if ($validator->fails()) {


                    $errors = $validator->errors();
                    return view('company.create',['errors' => $errors]);
                 
            }
         
         
            session()->flashInput($request->input());

            $save_ol_name = $request->file('logo');
            $fn1 = strtotime("now");
            $fn2 = mt_rand(1, 99999);
            $fn3 = mt_rand(1, 99999);
            $fn4 = mt_rand(1, 99999);
            $extension = $request->file('logo')->getClientOriginalExtension();
            $sv_set_name = "log".$fn1.$fn2.$fn3.$fn4.'.'.$extension;

         
            $company_name                 = $request->company_name;
            $logo                         = $request->file('logo');
            $abbreviate_name              = $request->abbreviate_name;
            $company_mst                  = new Company_MST();
            $company_mst->id              = $this->getMaxId()[0]->max+1; 
            $company_mst->own_company     = rand(1,1000);
            $company_mst->company_name    = $company_name;
            $company_mst->logo            = $sv_set_name; 
            $company_mst->save_ol_name    = $logo->getClientOriginalName();
            $company_mst->abbreviate_name = $abbreviate_name;
               
            $company_mst->save();

            Crofun::log_create(Auth::user()->id,$company_mst->id,config('constant.COMPANY'),config('constant.operation_CRATE'),config('constant.COMPANY_ADD'),$company_mst->id,$company_mst->abbreviate_name,$company_mst->id,json_encode($company_mst),null);



            Storage::disk('public')->put('logo/'.$sv_set_name,  File::get($logo));

            return view('company.create',["message"=>'会社情報を登録しました。']);

                
               
         }
         return view('company.create');
    }

    public function getMaxId(){

        $id  = DB::select('select MAX(id) from company_mst');

        return $id;
    }


}
