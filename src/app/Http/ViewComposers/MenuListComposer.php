<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;
use App\User;
use App\Menu;
use DB;

class MenuListComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       
        $menu_all_list = Menu::whereNotIn('position',[0])->orderBy('position', 'asc')->orderBy('dis_sort', 'asc')->get();
        $menu_1_list   = Menu::where('position',1)->get();
        $menu_2_list   = Menu::where('position',2)->get();
        $menu_3_list   = Menu::where('position',3)->get();
                 
        $view->with([
            
            'menu_all_list'        => $menu_all_list,

            'menu_1_list'          => $menu_1_list,

            'menu_2_list'          => $menu_2_list,

            'menu_3_list'          => $menu_3_list,
            
        ]);
    }
}