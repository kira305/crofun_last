<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Javascript;
class SessionComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {

           
           \JavaScript::put([

                'session_timeout' => config('constant.SESSION_TIMEOUT')

           ]);
    }
}