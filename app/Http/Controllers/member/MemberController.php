<?php

namespace App\Http\Controllers\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use View;

class MemberController extends Controller
{

    public function __construct()
    {
        $this->middleware('MemberAuth');
    }

    public function dashboard()
    {
        // return session()->all();
        $u = Session::get("loginUserInfo");
        // $mem = DB::table("member_personal_info")->where("memberPersonalCode", $u->memberPersonalCode)
        //     ->where("dairyId", $u->dairyId)->get()->first();

        $curBal = DB::table("user_current_balance")->where("ledgerId", $u->ledgerId)->get()->first();

        return view("member.index", ["curBal" => $curBal, 'activepage' => "dashboard"]);
    }

    public function getMilkCollactionAjax()
    {

        $u = Session::get("loginUserInfo");

        $curDate = date('Y-m-d');
        $yesDate = date('Y-m-d', strtotime("-1 days"));

        /* Cow milk Collected today */
        $cowMilkToday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where("memberCode", $u->memberPersonalCode)
            ->where('date', $curDate)
            ->get();

        $cowMilkTodayQty = 0;
        foreach ($cowMilkToday as $cowMilkTodayData) {
            $cowMilkTodayQty = $cowMilkTodayQty + $cowMilkTodayData->milkQuality;
        }

        /* Cow milk Collected Yesterday */
        $cowMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where("memberCode", $u->memberPersonalCode)
            ->where('date', $yesDate)
            ->get();

        $cowMilkYesterdayQty = 0;
        foreach ($cowMilkYesterday as $cowMilkYesterdayData) {
            $cowMilkYesterdayQty = $cowMilkYesterdayQty + $cowMilkYesterdayData->milkQuality;
        }

        /* Buffalo milk Collected today */
        $buffaloMilkToday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where("memberCode", $u->memberPersonalCode)
            ->where('date', $curDate)
            ->get();
        $buffaloMilkTodayQty = 0;
        foreach ($buffaloMilkToday as $buffaloMilkTodayData) {
            $buffaloMilkTodayQty = $buffaloMilkTodayQty + $buffaloMilkTodayData->milkQuality;
        }

        /* Buffalo milk Collected Yesterday */
        $buffaloMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where("memberCode", $u->memberPersonalCode)
            ->where('date', $yesDate)
            ->get();

        $buffaloMilkYesterdayQty = 0;
        foreach ($buffaloMilkYesterday as $buffaloMilkYesterdayData) {
            $buffaloMilkYesterdayQty = $buffaloMilkYesterdayQty + $buffaloMilkYesterdayData->milkQuality;
        }

        $returnValue = array($cowMilkTodayQty, $cowMilkYesterdayQty, $buffaloMilkTodayQty, $buffaloMilkYesterdayQty);

        return $returnValue;
    }

    public function monthlyMilkCollaction(Request $request)
    {

        $currentYear = date("Y");
        $currentMonth = date("m");
        $Data = [];
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $u = Session::get("loginUserInfo");

        for ($i = 1; $i <= $currentMonth; $i++) {
            $currentLoopMonth = "";
            if ($i <= 9) {
                $currentLoopMonth = "0" . $i;
            } else {
                $currentLoopMonth = $i;
            }
            $startDate = $currentYear . '-' . $currentLoopMonth . '-01';
            $endDate = $currentYear . '-' . $currentLoopMonth . '-31';

            $singleFatSnfRange = DB::select("SELECT * FROM daily_transactions WHERE dairyId='$dairyId' AND memberCode='$u->memberPersonalCode' AND date BETWEEN '" . $startDate . "' AND '" . $endDate . "'");
            $cowCount = 0;
            $buffaloCount = 0;
            foreach ($singleFatSnfRange as $singleFatSnfRangeData) {
                if ($singleFatSnfRangeData->milkType == "cow") {
                    $cowCount = $cowCount + $singleFatSnfRangeData->milkQuality;
                } else {
                    $buffaloCount = $buffaloCount + $singleFatSnfRangeData->milkQuality;
                }
            }
            $Data[] = array($cowCount, $buffaloCount);

        }
        $loopCount = count($Data);
        $returnValue = [];
        for ($i = 0; $i < 12; $i++) {
            if (!empty($Data[$i])) {
                $returnValue[] = $Data[$i];
            } else {
                $returnValue[] = array(0, 0);
            }
        }
        // print_r($returnValue);
        // die;
        return $returnValue;
    }

    public function collectionHistory()
    {
        $u = Session::get("loginUserInfo");

        return view("member.collectionHistory", ["mem" => $u]);
    }

    public function dailyTransactionListAjax(Request $req)
    {
        // return $req->all();
        $u = Session::get("loginUserInfo");

        Session::put('dailyTransactionFromDate', $req->fromdate);
        Session::put('dailyTransactionToShift', $req->todate);

        $dailyTransactions = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where("status", "true")
            ->whereBetween('date', [date("Y-m-d", strtotime($req->fromdate)), date("Y-m-d", strtotime($req->todate))])
            ->where("memberCode", $u->memberPersonalCode)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('member.dailyTransactionListModel', ['dailyTransactions' => $dailyTransactions]);
    }

    public function purchaseHistory()
    {
        $u = Session::get("loginUserInfo");

        return view("member.purchaseHistory", ["mem" => $u, "activepage" => "purchase"]);
    }

    public function purchaseHistoryListAjax(Request $req)
    {
        // return $req->all();
        $u = Session::get("loginUserInfo");

        Session::put('purchaseHistoryFromDate', $req->fromdate);
        Session::put('purchaseHistoryToShift', $req->todate);

        $purchaseHistory = DB::table('sales')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where("partyCode", $u->memberPersonalCode)
            ->where("status", "true")
            ->whereBetween('saleDate', [date("Y-m-d", strtotime($req->fromdate)), date("Y-m-d", strtotime($req->todate))])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('member.purchaseHistoryListModel', ['purchaseHistory' => $purchaseHistory]);
    }

    public function payments()
    {
        $u = Session::get("loginUserInfo");
        $balSheet = DB::table("balance_sheet")->where("ledgerId", $u->ledgerId)
            ->orderby("created_at")->get();

        return view("member.payments", ["balSheet" => $balSheet, "mem" => $u, "activepage" => "payments"]);
    }

    public function paymentListAjax(Request $req)
    {

        $u = Session::get("loginUserInfo");

        Session::put('paymentFromDate', $req->fromdate);
        Session::put('paymentToShift', $req->todate);

        $balSheet = DB::table("balance_sheet")->where("ledgerId", $u->ledgerId)
            ->whereBetween("created_at", [date("Y-m-d", strtotime($req->fromdate)) . " 00:00:00", date("Y-m-d", strtotime($req->todate)) . " 23:59:59"])
            ->orderby("created_at")->get();

        return view('member.paymentListModel', ['balSheet' => $balSheet]);
    }

    public function notification()
    {
        $u = Session::get("loginUserInfo");

        $products = DB::table("products")->where("dairyId", $u->dairyId)->get();
        $milkReq = DB::table("milkrequest")->where("dairyId", $u->dairyId)->where("memberCode", $u->memberPersonalCode)
            ->orderby("created_at", "desc")->get();

        return view("member.notification", ["mem" => $u, "products" => $products, "milkReq" => $milkReq, "activepage" => "notification"]);
    }

    public function sendReq(Request $req)
    {
        $u = Session::get("loginUserInfo");

        $validatedData = $req->validate([
            'date' => 'required',
            'shift' => 'required',
            'type' => 'required',
            'memCode' => 'required',
        ]);

        if ($req->type == "product") {
            $rate = DB::table("products")->where(["dairyId" => $u->dairyId, "productCode" => $req->productCode])->get()->first();
            if ($rate == (null || false)) {
                return ['error' => true, "msg" => "There are some error occured: NO_PRODUCT_FOUND."];
            }
            $amount = $rate->amount;
        } else {
            $amount = null;
        }

        $id = DB::table("milkrequest")->insert([
            "dairyId" => $u->dairyId,
            "memberCode" => $req->memCode,
            "colMan" => "DAIRYADMIN",
            "date" => date("Y-m-d", strtotime($req->date)),
            "type" => $req->type,
            "productCode" => $req->productCode,
            "qty" => $req->qty,
            "rate" => $amount,
            "shift" => $req->shift,
            "comment" => $req->comment."",
            "isDeliverd" => 0,
            "isSeen" => "false",
            "resText" => "",
            "created_at" => date("Y-m-d H:i:s"),
        ]);

        if ($id == (null || false || "")) {
            return ['error' => true, "msg" => "There are some error occured."];
        }

        return ['error' => false, "msg" => $req->type." request sent successfuly."];
    }

    public function getProductUnit(Request $req)
    {
        //    return $req->dairyId;

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $product = DB::table('products')
            ->where('dairyId', $dairyId)
            ->where('productCode', $req->productCode)
            ->where('status', "true")
            ->get()->first();
        return ["unit" => $product->productUnit, "rate" => $product->amount, "stock" => $product->productUnit];
    }


    public function statement(){
        return view("member.statement");
    }

    public function statementListAjax(Request $req){
        $u = Session::get("loginUserInfo");

        $dairyInfo = DB::table("dairy_info")->where("id", $u->dairyId)->get()->first();

        $mem = DB::table("member_personal_info")->where("dairyId", $u->dairyId)->where("memberPersonalCode", $u->memberPersonalCode)->get()->first();
        if($mem==(false||null)){
            return ["error" => true, "msg" => "No member found"];
        }

        $ubal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->get()->first();

        $opb = DB::table("balance_sheet")->where(["ledgerId" => $mem->ledgerId, "transactionType" => "member_personal_info"])->get()->first();

        // if($req->groupByDate){
        //     $balSheet = DB::table("balance_sheet")->where("ledgerId", $mem->ledgerId)
        //                         ->whereBetween("created_at", [date("Y-m-d", strtotime($req->startDate))." 00:00:00", date("Y-m-d", strtotime($req->endDate))." 23:59:59"])
        //                         ->where('transactionType', 'daily_transactions')
        //                         ->orderby("created_at")->get();

        //     $report = view::make('report.memStatementReport', ['balSheet' => $balSheet, "ubal" => $ubal]);
            
        //     return ["content" => (string)$report, "headings" => ["dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
        //         "report" => "Member Statement Report", "from" => (string)$req->startDate, "to" => (string)$req->endDate]];
        // }else{

            $ranges = $this->getDateranges($req->startDate, $req->endDate);

            $i=0;
            foreach($ranges as $r){
                
                $milkCollection = DB::table("daily_transactions")
                    ->where(['dairyId' => $u->dairyId, 'memberCode' => $mem->memberPersonalCode, "status" => "true"])
                    ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                    ->sum("amount");

                $localsalefinal = DB::table("sales")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                                                        "status" => "true", "saleType" => "local_sale"])
                                                    ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                                                    ->sum('finalAmount');
                $localsalepaid = DB::table("sales")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                                        "status" => "true", "saleType" => "local_sale"])
                                                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                                                ->sum('paidAmount');

                $productsale = DB::table("sales")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                                                    "status" => "true", "saleType" => "product_sale"])
                                                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                                                ->get();

                $advance = DB::table("advance")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode])
                                            ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                                            ->get();

                $credit = DB::table("credit")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode])
                                            ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                                            ->get();
                                            
                $balSheet[$r['s']]['range'] = $r['s'] ." to ". $r['e'];
                $balSheet[$r['s']]['milkCollection'] = number_format($milkCollection, 2, ".", "");
                $balSheet[$r['s']]['localsaleFinal'] = number_format($localsalefinal, 2, ".", "");
                $balSheet[$r['s']]['localsalePaid'] = number_format($localsalepaid, 2, ".", "");
                $balSheet[$r['s']]['productsale'] = $productsale;
                $balSheet[$r['s']]['advance'] = $advance;
                $balSheet[$r['s']]['credit'] = $credit;
                $i++;
            }

            // return $balSheet;

            $report = view::make('report.memStatementReport2', ['balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb]);
            
            return ["content" => (string)$report, "headings" => ["dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report", "from" => (string)$req->startDate, "to" => (string)$req->endDate]];
    }


    public function getDateranges($from, $to){
        
        $s = $from;
        $e = $to;
        $sd = date("Y-m-d", strtotime($s));
        $ed = date("Y-m-d", strtotime($e));
        $r = [];
        $i=0;
        $rflag = 0;

        $loop = true;
        $tsd = $sd;

        // echo date("d", strtotime($tsd)) + (10 - (date("d", strtotime($tsd)) % 10)) ; exit;

        while($loop){
            $startday = 0;

            if($i == 0){
                $startday = 0;
            }else{
                $startday = 1;
            }

            $number = cal_days_in_month(CAL_GREGORIAN, date("m", strtotime($tsd." +$startday day")), date("Y", strtotime($tsd." +$startday day")));

            $__dt = date("d", strtotime($tsd." +$startday day"));

            if($number == 31 &&  $__dt <= 31 && $__dt > 20){
                if($__dt == 31){
                    $day = 0;
                }elseif($__dt == 30){
                    $day = 1;
                }else{
                    $day = (11 - ($__dt % 10));
                }
            }elseif($number == 28 &&  $__dt <= 31 && $__dt > 20){
                $day = (8 - ($__dt % 10));
            }elseif($number == 29 &&  $__dt <= 31 && $__dt > 20){
                $day = (9 - ($__dt % 10));
            }else{
                if($__dt == 30){
                    $day = 0;
                }else{
                    $day = (10 - ($__dt % 10));
                }
            }

            $r[$i]["s"] = date("Y-m-d", strtotime($tsd." +$startday day"));
           
            if(strtotime($tsd) < strtotime($ed)){
                $r[$i]["e"] = date("Y-m-d", strtotime($tsd." +".($day+$startday)." day"));
                $tsd = date("Y-m-d", strtotime($tsd." +".($day+$startday)." day"));
            }
            
            if(strtotime($tsd) >= strtotime($ed)){
                $loop = false;
                // $tsd = date("Y-m-d", strtotime($ed));
                // $r[$i+1]["s"] = date("Y-m-d", strtotime($tsd." +$startday day"));
                $r[$i]["e"] = date("Y-m-d", strtotime($ed));
                break;
            }

            $i++;
        }

        return $r;
    }

    public function changePassword()
    {
        return view("member.changePassword");
    }

}
