<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class JwtMiddleware
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
     
        $token = $request->token;
      
        
        try { 
            JWTAuth::setToken($token);
            if (! $claim = JWTAuth::getPayload()) {

                return response()->json(array('message'=>'user_not_found'), 404);

            }

            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                return response()->json(array('message'=>'token_expired'));

            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
             
                return response()->json(array('message'=>'token_invalid'));

            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                
                return response()->json(array('message'=>'token_invalid'));
             
            }

            return $next($request);
    }
}
