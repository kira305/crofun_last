<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Diagram;
use Carbon\Carbon;
use DB;

class GetListCalendarDiagramComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $usr_id = Auth::user()->usr_id;

        if(request()->company_id == null){
            
            $company_id = Auth::user()->company_id;

        }else {

            $company_id = request()->company_id;

        }

        $days       = Diagram::where('company_id',$company_id)->get();

        $days       = Diagram::where('company_id',$company_id)->pluck('created_at');

        $array_date = array();

        foreach ($days as $day) {
            
            $time     = Carbon::parse($day);
            $time     = explode(' ',$time);
           
            // $year     = $time->year;
            // $month    = $time->month;
            // $day      = $time->day;
            $date = date("Y-m-d", strtotime($time[0]));
            array_push($array_date,$date);

        }
        
        JavaScript::put([

                'days' => $array_date

        ]);

    }
}