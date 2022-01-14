<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Headquarters_MST;
use App\Department_MST;
use App\Group_MST;
use App\Project_MST;
use Tymon\JWTAuth\Exceptions\JWTException;
use Crofun;
use JWTAuth;
class ApiController extends Controller
{

    public function headquarterCheck(Request $request){

       $headquarter_id = $request->headquarter_id;
       $result    = Department_MST::where('headquarters_id', $headquarter_id)->first();
       $status    = 0;
       if($result){

         $status    = 1;

       }
       return response()->json(
                                [
                                  'status'  => $status,
                                  'message' => trans('message.close_message')

                                ]
                              );

    }
    
    public function departmentCheck(Request $request){

       $department_id = $request->department_id;
       $result    = Group_MST::where('department_id', $department_id)->first();
       $status    = 0;
       if($result){

         $status    = 1;

       }
       return response()->json(
                                [
                                  'status'  => $status,
                                  'message' => trans('message.close_message')

                                ]
                              );

    }

    public function groupCheck(Request $request){

       $group_id = $request->group_id;
       $result    = Project_MST::where('group_id', $group_id)->first();
       $status    = 0;
       if($result){

         $status    = 1;

       }
       return response()->json(
                                [
                                  'status'  => $status,
                                  'message' => trans('message.close_message')

                                ]
                              );

    }

}