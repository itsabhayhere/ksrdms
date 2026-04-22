<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Cookie;

class Auth
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
        $loginType = Session::get('loginUserType');

        if ($loginType == (null || "") || $loginType != "dairy") {
            Session::flash('msg', 'You are not authorised to access this page, please login first.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairy-login");
        }
        // if ($loginType == "customer") {
        //     return redirect("customer/dashboard");
        // }
        // if ($loginType == "supplier") {
        //     return redirect("supplier/dashboard");
        // }
        // if ($loginType == "member") {
        //     return redirect("member/dashboard");
        // }
        // if ($loginType == "dairy") {
        //     return redirect("DairyAdminDashbord");
        // }
        
        // if ($loginType == "sa") {
        //     return redirect("sa/dashboard");
        // }
        return $next($request);
    }
}
