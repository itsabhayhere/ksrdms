<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\DB;


class test
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
        // return $next($request);
        /*if($user = Auth::user())
        {
            return $next($request);
            // return redirect('my-users');
        }else{
            return redirect('/');
        }*/

        $loginUserInfo = session()->get('loginUserInfo');
        
        if(!empty($loginUserInfo->dairyId)){
        $s = DB::table("subscribe")->where(["dairyId" => $loginUserInfo->dairyId])->get()->first();

        }else{
             return redirect('/');
        }

        $subscription = DB::table('subscribe')
            ->where('dairyId', $loginUserInfo->dairyId)
            ->first();

        $today = \Carbon\Carbon::today();

        if ($subscription && $subscription->expiryDate) {
            $expiryDate = \Carbon\Carbon::parse($subscription->expiryDate);
            if ($today->greaterThan($expiryDate)) {
                return redirect('/expiredPage');
            }
        }

        if ($user = Auth::user() || !empty($loginUserInfo)) {
            return $next($request);
            // return redirect('my-users');
        } else {
            return $next($request);
            // return redirect('/');
        }
    }
}
