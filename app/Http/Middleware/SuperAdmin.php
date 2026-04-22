<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Session;
class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $loginType = Session::get('loginUserType');

        if ($loginType == (null || "")) {
            Session::flash('msg', 'You are not authorised to access this page, please login first.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairy-login");
        }
        if ($loginType != "sa") {
            Session::flash('msg', 'You are not authorised to access this page, please login first.');
            Session::flash('alert-class', 'alert-danger');
            // $redirect->to('dairy-login')->send();
            return redirect("dairy-login");
        }

        return $next($request);
    }
}
