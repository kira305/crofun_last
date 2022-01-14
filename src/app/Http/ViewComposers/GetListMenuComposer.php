<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User; 
use App\Menu;
use DB;
use Common;
class GetListMenuComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {

        $menues   = Menu::where('dis_sort','!=','1')
                    ->orWhereNull('dis_sort')
                    ->orderBy('id')
                    ->get();
           
        $view->with([

            'formes'          => $menues,
            
        ]);
    }
}