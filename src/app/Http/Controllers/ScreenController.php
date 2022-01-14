<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Http\Request;
use App\User;
use App\Screen;
use Mail;
use Auth;
use Response;
use Excel;
use Helper;
class ScreenController extends Controller
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
          
        $screenes     = Screen::all();
       

        return view('screen.index',["screenes" => $screenes]);

    }

    public function edit(Request $request){
        

      
        if ($request->isMethod('post')) {
            
            $input = request()->validate([

                    'id'             => 'required',
                    'link_name'      => 'required'

            ]);
            
            $link            = Screen::where("id",$request->id)->first();

            $id              = $request->id;
            $link_name       = $request->link_name;

            $link->link_name = $link_name;
            $link->id        = $id;
            $link->update();
              
                
            return view('screen.edit',['screen' => $link,"message"=>'情報を変更しました。']);

                
               
         }

        $screen = Screen::where("id",$request->id)->first();
        return view('screen.edit',['screen' => $screen]);

    }
   
    public function create(Request $request){
           
         if ($request->isMethod('post')) {
            
            $input = request()->validate([

                    'id'             => 'required',
                    'link_name'      => 'required'
            ]);

            $id                        = $request->id;
            $link_name                 = $request->link_name;

            $link                      = new Screen();
            $link->id                  = $id;
            $link->link_name           = $link_name;
               
            $link->save();


            return view('screen.create',["message"=>'情報を登録しました。']);

                
               
         }
         return view('screen.create');
    }
}
