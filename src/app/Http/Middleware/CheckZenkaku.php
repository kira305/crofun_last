<?php

namespace App\Http\Middleware;

use Closure;
use Crofun;
class CheckZenkaku
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      
        $input = $request->all();
        if($request->isMethod('post')){

                if (isset($input['transaction_money'])) {
                   
                    $input['transaction_money'] = mb_convert_kana($input['transaction_money'], "rn");
                    $input['transaction_money'] = (int)filter_var($input['transaction_money'], FILTER_SANITIZE_NUMBER_INT);
                    $request->replace($input);
                   
                }

                if (isset($input['transaction_shot'])) {
                   
                    $input['transaction_shot'] = mb_convert_kana($input['transaction_shot'], "rn");
                    $input['transaction_shot'] = (int)filter_var($input['transaction_shot'], FILTER_SANITIZE_NUMBER_INT);
                    $request->replace($input);
                   
                }
               
        }

        
        return $next($request);
    }
}
