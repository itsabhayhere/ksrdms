<?php

namespace App\Http\Controllers\supplier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SupplierController extends Controller
{
    //
    
    public function __construct()
    {
        $this->middleware('SupplierAuth');
    }

    public function dashboard(){
        $u = Session::get("loginUserInfo");
        $curBal = DB::table("user_current_balance")->where("ledgerId", $u->ledgerId)->get()->first();
        
        return view("supplier.index", ["sup" => $u, "curBal" => $curBal, 'activepage' => "dashboard"]);
    }
}
