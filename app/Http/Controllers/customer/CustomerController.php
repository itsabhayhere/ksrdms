<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;


class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('CustomerAuth');
    }

    public function dashboard()
    {
        $u = Session::get("loginUserInfo");
        // $mem = DB::table("member_personal_info")->where("memberPersonalCode", $u->memberPersonalCode)
        //     ->where("dairyId", $u->dairyId)->get()->first();

        $curBal = DB::table("user_current_balance")->where("ledgerId", $u->ledgerId)->get()->first();

        return view("customer.index", ["curBal" => $curBal, 'activepage' => "dashboard"]);
    }

    public function purchaseHistory()
    {
        $u = Session::get("loginUserInfo");
        $curBal = DB::table("user_current_balance")->where("ledgerId", $u->ledgerId)->get()->first();

        return view("customer.purchaseHistory", ["curBal" => $curBal, "cust" => $u, "activepage" => "purchase"]);
    }

    public function purchaseHistoryListAjax(Request $req)
    {
        // return $req->all();
        $u = Session::get("loginUserInfo");

        Session::put('purchaseHistoryFromDate', $req->fromdate);
        Session::put('purchaseHistoryToShift', $req->todate);

        $purchaseHistory = DB::table('sales')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where("partyCode", $u->customerCode)
            ->where("status", "true")
            ->whereBetween('saleDate', [date("Y-m-d", strtotime($req->fromdate)), date("Y-m-d", strtotime($req->todate))])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.purchaseHistoryListModel', ['purchaseHistory' => $purchaseHistory]);
    }

    public function payments(){
        $u = Session::get("loginUserInfo");
        $balSheet = DB::table("balance_sheet")->where("ledgerId", $u->ledgerId)
                        ->orderby("created_at")->get();

        return view("customer.payments", ["balSheet" => $balSheet, "cust" => $u, "activepage" => "payments"]);
    }

    public function paymentListAjax(Request $req){
        
        $u = Session::get("loginUserInfo");

        Session::put('paymentFromDate', $req->fromdate);
        Session::put('paymentToShift', $req->todate);

        $balSheet = DB::table("balance_sheet")->where("ledgerId", $u->ledgerId)
                        ->whereBetween("created_at", [date("Y-m-d", strtotime($req->fromdate))." 00:00:00", date("Y-m-d", strtotime($req->todate))." 23:59:59"])
                        ->orderby("created_at")->get();

        return view('customer.paymentListModel', ['balSheet' => $balSheet]);
    }
}
