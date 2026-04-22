<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DairyAdminDashbordController extends Controller
{

    public function __construct()
    {
        $this->middleware('Auth');
    }

    public function dairyData(Request $request)
    {

        // $loginSessCookie = Cookie::get('loginSessCookie');
        // return $loginSessCookie;



        return view('dairyAdminDashbord');
    }

    public function updateCurrentDairyBal(Request $req)
    {
        // return $req->all();
        if ($req->bal == "" || $req->type == (null || "")) {
            return ["error" => true, "msg" => "fill all fields."];
        }
        $dairy = session()->get('dairyInfo');
        $colMan = session()->get('colMan');

        DB::beginTransaction();

        $u = DB::table("user_current_balance")->where("ledgerId", $dairy->ledgerId)
            ->update(["openingBalance" => $req->bal, "openingBalanceType" => $req->type, "created_at" => date("Y-m-d H:i:s")]);
        if (!$u) {
            DB::rollBack();
            return ["error" => true, "msg" => "Error Code: BALANCE_UPDATE_FAILED ."];
        }

        $u = DB::table("balance_sheet")->insert([
            "ledgerId" => $dairy->ledgerId,
            "dairyId" => $dairy->id,
            "srcDest" => $dairy->ledgerId,
            "colMan" => $colMan->userName,
            "transactionId" => "0",
            "transactionType" => "opening_balance",
            "amountType" => $req->type,
            "finalAmount" => $req->bal,
            "remark" => "Opening Balance Update.",
            "created_at" => date("Y-m-d H:i:s"),
        ]);
        if (!$u) {
            DB::rollBack();
            return ["error" => true, "msg" => "Error Code: BALANCE_SHEET_UPDATE_FAILED ."];
        }

        if ($req->type == "cash") {
            $bal = $req->bal;
        } else {
            $bal = 0;
        }

        $u = DB::table("dairy_info")->where("id", $dairy->id)->update(["cash_in_hand" => $bal, "firstTimeBalanceUpdated" => "true"]);
        if (!$u) {
            DB::rollBack();
            return ["error" => true, "msg" => "Error Code: INFO_UPDATE_FAILED ."];
        }

        $dairy = DB::table("dairy_info")->where("id", $dairy->id)->get()->first();
        unset($dairy->password);
        session()->put('dairyInfo', $dairy);

        DB::commit();
        return ["error" => false, "msg" => "Dairy balance updated successfuly."];
    }

    public function checkDairy()
    {
        $dairyId = session()->get('dairyInfo')->id;

        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        unset($dairyInfo->password);

        $countMembers = DB::table("member_personal_info")->where(["dairyId" => $dairyId, "status" => "true"])->count();

        $activeMembers = DB::table("daily_transactions")->where(["dairyId" => $dairyId, "status" => "true"])
            ->whereDate("date", ">=", date("Y-m-d", strtotime("-5 days")))
            ->distinct('memberCode')->count('memberCode');

        $this->addToInactiveMembers($dairyInfo);

        $creditMembers = DB::table('user_current_balance')
            ->join("member_personal_info", "member_personal_info.ledgerId", "=", "user_current_balance.ledgerId")
            ->where('member_personal_info.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->count();

        $countSuppliers = DB::table("suppliers")->where("dairyId", $dairyId)->count();
        $countCustomers = DB::table("customer")->where("dairyId", $dairyId)->count();
        $countMilkPlants = 0;
        $countProducts = DB::table("products")->where("dairyId", $dairyId)->count();
        $countManager = DB::table("other_users")->where("dairyId", $dairyId)->count();

        $mstatus = true;
        $mmsg = "";
        if ($dairyInfo->cowMilkPrice == (null || "")) {
            $mstatus = false;
            $mmsg = "Cow Milk price is not set.";
        }
        if ($dairyInfo->buffaloMilkPrice == (null || "")) {
            if ($mstatus == false) {
                $mmsg = "Cow & Buffalo Milk price is not set.";
            } else {
                $mstatus = false;
                $mmsg = "Buffalo Milk price is not set.";
            }
        }
        $milkPrice["status"] = $mstatus;
        $milkPrice["msg"] = $mmsg;

        $data = [
            "dairyInfo" => $dairyInfo,
            "members" => [
                "countMembers" => $countMembers,
                "activeMembers" => $activeMembers,
                "creditMembers" => $creditMembers,
            ],
            "suppliers" => [
                "countSuppliers" => $countSuppliers,
            ],
            "customers" => [
                "countCustomers" => $countCustomers,
            ],
            "milkPlants" => [
                "countMilkPlants" => $countMilkPlants,
            ],
            "products" => [
                "countProducts" => $countProducts,
            ],
            "manager" => [
                "countManager" => $countManager,
            ],
            "milkPrice" => [
                "milkPrice" => $milkPrice,
            ],
        ];

        return $data;
    }

    public function addToInactiveMembers($dairy)
    {
        $activeMembers = DB::table("daily_transactions")->where(["dairyId" => $dairy->id, "status" => "true"])
            ->whereDate("date", ">=", date("Y-m-d", strtotime("-5 days")))
            ->groupBy('memberCode')->get()->toArray();

        $activeMembers = array_column($activeMembers, 'memberCode');
        $mem = DB::table("member_personal_info")->where([
            "dairyId" => $dairy->id,
            "status" => "true",
        ])->get();

        if (count($activeMembers) > 0) {
            $activeMembers[count($activeMembers)] = $activeMembers[0];
            unset($activeMembers[0]);
        }

        $newInactives = [];
        foreach ($mem as $m) {
            // echo array_search($m->memberPersonalCode, $activeMembers)."A ";
            if (array_search($m->memberPersonalCode, $activeMembers) != "") {
                // echo $m->memberPersonalCode." ";
                continue;
            }

            $exist = DB::table('inactive_members')->where("ledgerId", $m->ledgerId)->get()->first();
            if ($exist) {
                continue;
            } else {
                $data = ["ledgerId" => $m->ledgerId, "created_at" => date("Y-m-d H:i:s")];
                $res = DB::table('inactive_members')->insertGetId($data);

                $newInactives[] = $m;
            }
        }
        if (count($newInactives) > 0) {
            //notify to dairy
            $not = [
                "ledgerId" => $dairy->ledgerId,
                "notification" => count($newInactives) . " New member found inactive.<br/><a href='" . url('memberDetailDash') . "?type=inactive'>Show Inactive Members</a>",
                "created_at" => date("Y-m-d H:i:s"),
            ];
            $res = DB::table("notifications")->insertGetId($not);
        }
    }

    public function getMilkCollactionData(Request $request)
    {
        $currentDate = date('Y-m-d');
        $YesterdayDate = date('Y-m-d', strtotime("-1 days"));

        /* Cow milk Collected today */
        $cowMilkToday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where('date', $currentDate)
            ->get();

        $cowMilkTodayQty = 0;
        foreach ($cowMilkToday as $cowMilkTodayData) {
            $cowMilkTodayQty = (float) $cowMilkTodayQty + (float) $cowMilkTodayData->milkQuality;
        }

        /* Cow milk Collected Yesterday */
        $cowMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where('date', $YesterdayDate)
            ->get();
        $cowMilkYesterdayQty = 0;
        foreach ($cowMilkYesterday as $cowMilkYesterdayData) {
            $cowMilkYesterdayQty = (float) $cowMilkYesterdayQty + (float) $cowMilkYesterdayData->milkQuality;
        }

        /* Buffalo milk Collected today */
        $buffaloMilkToday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where('date', $currentDate)
            ->get();
        $buffaloMilkTodayQty = 0;
        foreach ($buffaloMilkToday as $buffaloMilkTodayData) {
            $buffaloMilkTodayQty = (float) $buffaloMilkTodayQty + (float) $buffaloMilkTodayData->milkQuality;
        }

        /* Buffalo milk Collected Yesterday */
        $buffaloMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where('date', $YesterdayDate)
            ->get();

        $buffaloMilkYesterdayQty = 0;
        foreach ($buffaloMilkYesterday as $buffaloMilkYesterdayData) {
            $buffaloMilkYesterdayQty = (float) $buffaloMilkYesterdayQty + (float) $buffaloMilkYesterdayData->milkQuality;
        }

        return [
            round($cowMilkTodayQty),
            round($cowMilkYesterdayQty),
            round($buffaloMilkTodayQty),
            round($buffaloMilkYesterdayQty),
        ];
    }

    /* get Today Sale */
    public function getTodaySale(Request $request)
    {
        $currentDate = date('Y-m-d');

        /* local Sale */
        $localSale = DB::table('sales')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('saleType', 'local_sale')
            ->where('saleDate', $currentDate)
            ->get();

        $localSaleDataCount = 0;
        foreach ($localSale as $localSaleData) {
            $localSaleDataCount = $localSaleDataCount + $localSaleData->finalAmount;
        }

        /* plant sale */
        $plantSale = DB::table('sales')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('saleType', 'plant_sale')
            ->where('saleDate', $currentDate)
            ->get();

        $plantSaleDataCount = 0;
        foreach ($plantSale as $plantSaleData) {
            $plantSaleDataCount = $plantSaleDataCount + $plantSaleData->finalAmount;
        }

        /* product sale */
        $proSale = DB::table('sales')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('saleType', 'product_sale')
            ->where('saleDate', $currentDate)
            ->where('status', "true")
            ->get();

        $proSaleDataCount = 0;
        foreach ($proSale as $p) {
            $proSaleDataCount = $proSaleDataCount + $p->finalAmount;
        }

        /* Member on credit */
        $memberOnCredit = DB::table('member_personal_info')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'member_personal_info.ledgerId')
            ->where('member_personal_info.status', 'true')
            ->where('user_current_balance.userType', 4)
            ->where('member_personal_info.dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select('user_current_balance.openingBalance')
            ->get();
        $creditMember = 0;
        $creditMemberCount = 0;
        foreach ($memberOnCredit as $memberOnCreditData) {
            $creditMember = $creditMember + $memberOnCreditData->openingBalance;
            $creditMemberCount++;
        }
        unset($memberOnCredit);

        /* Customer on credit */
        $customerOnCredit = DB::table('customer')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'customer.ledgerId')
            ->where('customer.status', 'true')
            ->where('user_current_balance.userType', 2)
            ->where('customer.dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select('user_current_balance.openingBalance')
            ->groupBy("user_current_balance.ledgerId")
            ->get();
        $creditCustomer = 0;
        $creditCustomerCount = 0;
        foreach ($customerOnCredit as $c) {
            $creditCustomer = $creditCustomer + $c->openingBalance;
            $creditCustomerCount++;
        }
        unset($customerOnCredit);

        /* supplier on credit */
        $supplierOnCredit = DB::table('suppliers')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'suppliers.ledgerId')
            ->where('suppliers.status', 'true')
            ->where('user_current_balance.userType', 3)
            ->where('suppliers.dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select('user_current_balance.openingBalance')
            ->get();
        $creditSupp = 0;
        $creditSuppCount = 0;
        foreach ($supplierOnCredit as $s) {
            $creditSupp = $creditSupp + $s->openingBalance;
            $creditSuppCount++;
        }
        unset($supplierOnCredit);

        /* Member on debit */
        $memberOnDebit = DB::table('member_personal_info')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'member_personal_info.ledgerId')
            ->where('member_personal_info.status', 'true')
            ->where('user_current_balance.userType', 4)
            ->where('member_personal_info.dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select('user_current_balance.openingBalance')
            ->get();
        $debitMember = 0;
        $debitMemberCount = 0;
        foreach ($memberOnDebit as $memberOnDebitData) {
            $debitMember = $debitMember + $memberOnDebitData->openingBalance;
            $debitMemberCount++;
        }
        unset($memberOnDebit);

        /* Customer on debit */
        $customerOnDebit = DB::table('customer')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'customer.ledgerId')
            ->where('customer.status', 'true')
            ->where('user_current_balance.userType', 2)
            ->where('customer.dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select('user_current_balance.openingBalance')
            ->get();
        $debitCustomer = 0;
        $debitCustomerCount = 0;
        foreach ($customerOnDebit as $c) {
            $debitCustomer = $debitCustomer + $c->openingBalance;
            $debitCustomerCount++;
        }
        unset($customerOnDebit);

        /* supplier on debit */
        $supplierOnDebit = DB::table('suppliers')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'suppliers.ledgerId')
            ->where('suppliers.status', 'true')
            ->where('user_current_balance.userType', 3)
            ->where('suppliers.dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select('user_current_balance.*')
            ->get();
        $debitSupp = 0;
        $debitSuppCount = 0;
        foreach ($supplierOnDebit as $s) {
            $debitSupp = $debitSupp + $s->openingBalance;
            $debitSuppCount++;
        }
        unset($supplierOnDebit);

        return [
            "localSaleDataCount" => $localSaleDataCount,
            "plantSaleDataCount" => $plantSaleDataCount,
            "proSaleDataCount" => $proSaleDataCount,
            "creditMember" => number_format($creditMember, 1, ".", ""),
            "creditMemberCount" => $creditMemberCount,
            "debitMember" => number_format($debitMember, 1, ".", ""),
            "debitMemberCount" => $debitMemberCount,
            "creditCust" => number_format($creditCustomer, 1, ".", ""),
            "creditCustCount" => $creditCustomerCount,
            "debitCust" => number_format($debitCustomer, 1, ".", ""),
            "debitCustCount" => $debitCustomerCount,
            "creditSupp" => number_format($creditSupp, 1, ".", ""),
            "creditSuppCount" => $creditSuppCount,
            "debitSupp" => number_format($debitSupp, 1, ".", ""),
            "debitSuppCount" => $debitSuppCount,
        ];
    }

    public function monthlyMilkCollaction(Request $request)
    {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $Data = [];
        // echo "<pre>";

        $dairyId = session()->get('dairyInfo')->id;

        for ($i = 1; $i <= $currentMonth; $i++) {
            $currentLoopMonth = "";
            if ($i <= 9) {
                $currentLoopMonth = "0" . $i;
            } else {
                $currentLoopMonth = $i;
            }
            $startDate = $currentYear . '-' . $currentLoopMonth . '-01';
            $endDate = $currentYear . '-' . $currentLoopMonth . '-31';

            $singleFatSnfRange = DB::select("SELECT * FROM daily_transactions WHERE dairyId='$dairyId' AND date BETWEEN '" . $startDate . "' AND '" . $endDate . "'");
            $cowCount = 0;
            $buffaloCount = 0;
            foreach ($singleFatSnfRange as $singleFatSnfRangeData) {
                if ($singleFatSnfRangeData->milkType == "cow") {
                    $cowCount = (float) $cowCount + (float) $singleFatSnfRangeData->milkQuality;
                } else {
                    $buffaloCount = (float) $buffaloCount + (float) $singleFatSnfRangeData->milkQuality;
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

    /* member list */
    public function memberDetailDash(Request $req)
    {

        $dairyId = session()->get('loginUserInfo')->dairyId;

        switch ($req->type) {
            case "total": {
                    $members = DB::table('member_personal_info')
                        ->leftjoin("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
                        ->where(["member_personal_info.dairyId" => $dairyId, "member_personal_info.status" => "true"])->get();
                    break;
                }
            case "active": {
                    $members = DB::table('daily_transactions')
                        ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "daily_transactions.memberCode")
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
                        ->groupBy('daily_transactions.memberCode')
                        ->where(["member_personal_info.dairyId" => $dairyId, "daily_transactions.dairyId" => $dairyId, "daily_transactions.status" => "true"])
                        ->whereDate("daily_transactions.date", ">", date("Y-m-d", strtotime("-5 days")))->get();
                    break;
                }
            case "inactive": {
                    $m = DB::table('daily_transactions')
                        ->select("daily_transactions.memberCode")
                        ->groupBy('daily_transactions.memberCode')
                        ->where(["daily_transactions.dairyId" => $dairyId, "daily_transactions.status" => "true"])
                        ->whereDate("daily_transactions.date", ">", date("Y-m-d", strtotime("-5 days")))->pluck('memberCode')->toArray();
                    $members = DB::table('member_personal_info')
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
                        ->where([
                            "member_personal_info.dairyId" => $dairyId,
                            "member_personal_info.status" => "true"
                        ])
                        ->whereNotIn("member_personal_info.memberPersonalCode", $m)
                        ->groupby("member_personal_info.memberPersonalCode")->get();
                    // echo json_encode($members);exit;
                    break;
                }
            case "credit": {
                    $members = DB::table('member_personal_info')
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
                        ->where("user_current_balance.openingBalanceType", "credit")
                        ->where(["member_personal_info.dairyId" => $dairyId, "member_personal_info.status" => "true"])
                        ->get();
                    break;
                }
            case "debit": {
                    $members = DB::table('member_personal_info')
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
                        ->where("user_current_balance.openingBalanceType", "debit")
                        ->where(["member_personal_info.dairyId" => $dairyId, "member_personal_info.status" => "true"])
                        ->get();
                    break;
                }
        }

        $count = 0;
        foreach ($members as $membersData) {
            /* states */
            $getSatatNme = DB::table('states')
                ->where('id', $membersData->memberPersonalState)
                ->get()->first();
            if ($getSatatNme) {
                $members[$count]->memberPersonalState = $getSatatNme->name;
            } else {
                $members[$count]->memberPersonalState = "";
            }

            /* city */
            $getCityName = DB::table('city')
                ->where('id', $membersData->memberPersonalCity)
                ->get()->first();
            if ($getCityName) {
                $members[$count]->memberPersonalCity = $getCityName->name;
            } else {
                $members[$count]->memberPersonalCity = "";
            }
            $count++;
        }

        return view('memberListDash', ['members' => $members, "type" => $req->type, 'activepage' => "members"]);
    }

    /* member list */
    public function customerDetailDash(Request $req)
    {

        $dairyId = session()->get('loginUserInfo')->dairyId;

        switch ($req->type) {
            case "credit": {
                    $cust = DB::table('customer')
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "customer.ledgerId")
                        ->where("user_current_balance.openingBalanceType", "credit")
                        ->where(["customer.dairyId" => $dairyId, "customer.status" => "true"])
                        ->groupby('user_current_balance.ledgerId')
                        ->get();
                    break;
                }
            case "debit": {
                    $cust = DB::table('customer')
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "customer.ledgerId")
                        ->where("user_current_balance.openingBalanceType", "debit")
                        ->where(["customer.dairyId" => $dairyId, "customer.status" => "true"])
                        ->groupby('user_current_balance.ledgerId')
                        ->get();
                    break;
                }
            default:
                $cust = null;
        }

        $count = 0;
        foreach ($cust as $c) {
            $getSatatNme = DB::table('states')
                ->where('id', $c->customerState)
                ->get()->first();
            $cust[$count]->customerState = isset($getSatatNme->name) ? $getSatatNme->name : "";

            $getCityName = DB::table('city')
                ->where('id', $c->customerCity)
                ->get()->first();
            $cust[$count]->customerCity = isset($getCityName->name) ? $getCityName->name : "";
            $count++;
        }
        return view('customerListDash', ['cust' => $cust, "type" => $req->type, 'activepage' => "customers"]);
    }

    /* member list */
    public function suppDetailDash(Request $req)
    {

        $dairyId = session()->get('loginUserInfo')->dairyId;

        switch ($req->type) {
            case "credit": {
                    $supp = DB::table('suppliers')
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "suppliers.ledgerId")
                        ->where("user_current_balance.openingBalanceType", "credit")
                        ->where(["suppliers.dairyId" => $dairyId, "suppliers.status" => "true"])
                        ->get();
                    break;
                }
            case "debit": {
                    $supp = DB::table('suppliers')
                        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "suppliers.ledgerId")
                        ->where("user_current_balance.openingBalanceType", "debit")
                        ->where(["suppliers.dairyId" => $dairyId, "suppliers.status" => "true"])
                        ->get();
                    break;
                }
            default:
                $supp = null;
        }

        $count = 0;
        foreach ($supp as $s) {
            $getSatatNme = DB::table('states')
                ->where('id', $s->supplierState)
                ->get()->first();
            $supp[$count]->supplierState = isset($getSatatNme->name) ? $getSatatNme->name : '';

            $getCityName = DB::table('city')
                ->where('id', $s->supplierCity)
                ->get()->first();
            $supp[$count]->supplierCity = isset($getCityName->name) ? $getCityName->name : '';
            $count++;
        }

        return view('supplierListDash', ['supp' => $supp, "type" => $req->type, 'activepage' => "supliers"]);
    }

    public function dairyBal()
    {
        //   dd(DB::table('sales')->first());

        $dairyInfo = DB::table("dairy_info")->where("id", session()->get('loginUserInfo')->dairyId)
            ->get()->first();

        $credit = DB::table('credit')->where('dairyId', $dairyInfo->id)->sum('amount');

        $local_sale = DB::table('sales')->where([
            'dairyId' => session()->get('loginUserInfo')->dairyId,
            'saleType' => "local_sale",
            'status' => "true"
        ])
            ->orderby("created_at", "DESC")
            ->sum('finalAmount');


        $product_sale = DB::table('sales')
            ->select(
                "sales.id as id",
                "sales.partyCode as partyCode",
                "sales.partyName as partyName",
                "sales.saleDate as saleDate",
                "sales.productType",
                "sales.productPricePerUnit as productPricePerUnit",
                "sales.purchaseAmount as purchaseAmountS",
                "sales.productQuantity as productQuantity",
                "sales.amount as amount",
                "products.purchaseamount as purchaseamount",
                "sales.discount as discount",
                "sales.paidAmount as paidAmount",
                "sales.finalAmount as finalAmount",
                "sales.amountType as amountType",
                "products.productName as productName"
            )
            ->where([
                'sales.dairyId' => session()->get('loginUserInfo')->dairyId,
                'sales.saleType' => "product_sale",
                "sales.status" => "true"
            ])
            ->leftjoin("products", "products.productCode", "=", "sales.productType")
            ->sum('sales.finalAmount');

        $cash = DB::table("balance_sheet")->where(["dairyId" => $dairyInfo->id])->where('amountType', 'cash')->sum('finalAmount');



        $total = $credit + $local_sale + $product_sale + $cash;

        $advance = DB::table('advance')->where('dairyId', session()->get('loginUserInfo')->dairyId)->orderby("created_at", "desc")->sum('amount');

        $expenseList = DB::table('expense_setups')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->orderBy('date', 'desc')
            ->sum('amount');

        $purchase = DB::table('purchase_setups')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->orderBy("created_at", "desc")
            ->sum('amount');


        $purchases = DB::table('purchase_setups')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->orderBy("created_at", "desc")
            ->get();

        $totalBalance = $purchases->sum(function ($row) {
            return $row->amount - $row->paidAmount;
        });


        $valueAdd = $advance + $expenseList + $purchase + $totalBalance;

        $FinalTotal = $valueAdd - $total;

        //      $bs = DB::table('balance_sheet')->insertGetId([
        //     'ledgerId' => $dairyInfo->ledgerId,
        //     'transactionId' => $dairyInfo->id,
        //     'srcDest' => $dairyInfo->ledgerId,
        //     'dairyId' => $dairyInfo->id,
        //     'colMan' => $colMan->userName,
        //     'transactionType' => 'dairy_cash',
        //     'remark' => "Cash " . $req->type . " (" . $req->remark . ")",
        //     'amountType' => $type,
        //     'finalAmount' => $req->cash,
        //     'created_at' => date("Y-m-d H:i:s"),
        // ]);


        //  dd(DB::table('sales')->whereIn('saleType', ['local_sale'])->where('dairyId', $dairyInfo->id)->sum('amount'));
        //  $product_sale = DB::table('sales')->where('dairyId', $dairyInfo->id)->where('status', 'true')->whereIn('saleType', ['product_sale'])->sum('amount');
        //   $add_cash = DB::table('balance_sheet')->where('dairyId', $dairyInfo->id)->sum('amount');


        //$advance = DB::table('advance')->where('dairyId', $dairyInfo->id)->get();
        //  $expenses = DB::table('balance_sheet')->where('dairyId', $dairyInfo->id)->first();
        //   dd($expenses);




        return view("dairyBal", ["dairyInfo" => $dairyInfo, "activepage" => "dairyBal", "FinalTotal" => $FinalTotal]);
    }




    public function date_sort($a, $b)
    {

        return strtotime($a[0]) - strtotime($b[0]);
    }
    public function getDailryBalData()
    {

        $data = [];

        // Advance
        $advance = DB::table('advance')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->get();

        foreach ($advance as $a) {

            $d = [strtotime($a->date), date("d-m-Y", strtotime($a->date)), $a->partyCode, 'Advance', '', "<b>" . $a->amount . "</b>"];

            $data[] = $d;
        }
        // Credit

        $credit = DB::table('credit')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->get();
        foreach ($credit as $a) {
            $d = [strtotime($a->date), date("d-m-Y", strtotime($a->date)), $a->partyCode, 'Credit', "<b>" . $a->amount . "</b>", ''];
            $data[] = $d;
        }

        // Product sale

        $productsale = DB::table("sales")->where([
            "dairyId" => session()->get('loginUserInfo')->dairyId,
            "status" => "true",
            "saleType" => "product_sale",
        ])->get();

        foreach ($productsale as $p) {
            $d = [strtotime($p->saleDate), date("d-m-Y", strtotime($p->saleDate)), $p->partyCode, 'Product Sale', '', "<b>" . $p->finalAmount . "</b>"];
            $data[] = $d;
        }


        // $localsalepaid = DB::table("sales")->where([
        //     "dairyId" => session()->get('loginUserInfo')->dairyId,
        //     "status" => "true",
        //     "saleType" => "local_sale",
        //     "paidAmount" => "!0",
        // ])->get();


        $localsalepaid = DB::table("sales")
            ->where("dairyId", session()->get('loginUserInfo')->dairyId)
            ->where("status", "true")
            ->where("saleType", "local_sale")
            ->where("paidAmount", '!=', 0)
            ->get();


        foreach ($localsalepaid as $l) {
            $d = [strtotime($l->saleDate), date("d-m-Y", strtotime($l->saleDate)), $l->partyCode, 'Local Sale', '', "<b>" . $l->paidAmount . "</b>"];
            $data[] = $d;
        }

        $expenseList = DB::table('expense_setups')
            ->where('dairyId',  session()->get('loginUserInfo')->dairyId)
            ->get();


        foreach ($expenseList as $l) {
            $e = [strtotime($l->date), date("d-m-Y", strtotime($l->date)), $l->partyName, 'Expense', '', "<b>" . $l->amount . "</b>"];
            $data[] = $e;
        }

        //        usort($data, "date_sort");




        $supplier_amount = DB::table('purchase_setups')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->orderBy("created_at", "desc")
            ->get();


        //   dd($supplier_amount);
            

        foreach ($supplier_amount as $supplieramount) {
            $s = [strtotime($supplieramount->date), date("d-m-Y", strtotime($supplieramount->date)), $supplieramount->supplierCode, 'Paid To Supplier Amount', '', "<b>" . $supplieramount->amount . "</b>"];
            $data[] = $s;
        }


        // $purchases = DB::table('purchase_setups')
        //     ->where('dairyId', session()->get('loginUserInfo')->dairyId)
        //     ->where('status', 'true')
        //     ->orderBy("created_at", "desc")
        //     ->get();

        // $totalBalance = $purchases->sum(function ($row) {
        //     return $row->amount - $row->paidAmount;
        // });


        return ["data" => $data];
    }

    public function dairyBalOtherDetails(Request $req)
    {
        // if($req->byDate){
        //     $from = $req->from;
        //     $to = $req->to;
        // }else{
        //     $from = date("Y-m-d");
        //     $to = date("Y-m-d");
        // }

        $today = date("Y-m-d");
        $month = date('Y-m-01');

        $dairyInfo = session()->get('dairyInfo');
        $tdCash = DB::table("balance_sheet")->where("ledgerId", $dairyInfo->ledgerId)->where("amountType", "cash")
            ->whereBetween("created_at", [date("Y-m-d", strtotime($today)) . " 00:00:00", date("Y-m-d", strtotime($today)) . " 23:59:59"])
            ->sum("finalAmount");

        $mtCash = DB::table("balance_sheet")->where("ledgerId", $dairyInfo->ledgerId)->where("amountType", "cash")
            ->whereBetween("created_at", [date("Y-m-d", strtotime($month)) . " 00:00:00", date("Y-m-d", strtotime($today)) . " 23:59:59"])
            ->sum("finalAmount");

        return ["todaySaleInCash" => $tdCash, 'monthSaleInCash' => $mtCash];
    }

    public function submitCashUpdate(Request $req)
    {

        $dairyInfo = DB::table("dairy_info")->where("id", session()->get('loginUserInfo')->dairyId)->get()->first();

        $colMan = Session::get("colMan");

        if ($req->type == "add") {
            $cash = $dairyInfo->cash_in_hand + $req->cash;
            $type = "cash";
        } elseif ($req->type == "remove") {
            $type = "debit";
            // if($req->cash > $dairyInfo->cash_in_hand){
            //     Session::flash('msg', 'balance not sufficient.');
            //     Session::flash('alert-class', 'alert-danger');
            //     return redirect("dairyBal");
            // }
            // $cash = $dairyInfo->cash_in_hand - $req->cash;
            $cash = $req->cash;
        }

        DB::beginTransaction();

        $u = DB::table("dairy_info")->where("id", $dairyInfo->id)->update(["cash_in_hand" => $cash]);

        $bs = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $dairyInfo->ledgerId,
            'transactionId' => $dairyInfo->id,
            'srcDest' => $dairyInfo->ledgerId,
            'dairyId' => $dairyInfo->id,
            'colMan' => $colMan->userName,
            'transactionType' => 'dairy_cash',
            'remark' => "Cash " . $req->type . " (" . $req->remark . ")",
            'amountType' => $type,
            'finalAmount' => $req->cash,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        if ($u && $bs) {
            DB::commit();
            Session::flash('msg', 'Cash updated successfuly.');
            Session::flash('alert-class', 'alert-success');
            return redirect("dairyBal");
        }

        DB::rollback();
        Session::flash('msg', 'Cash not updated.');
        Session::flash('alert-class', 'alert-danger');
        return redirect("dairyBal");
    }

    public function submitCashEditUpdate(Request $req)
    {

        $dairyInfo = DB::table("dairy_info")->where("id", session()->get('loginUserInfo')->dairyId)->get()->first();

        $colMan = Session::get("colMan");

        $bal = DB::table("balance_sheet")->where(['id' => $req->balSheetId, "dairyId" => $dairyInfo->id])->get()->first();

        if ($bal == null) {
            Session::flash('msg', 'An Error has occured.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairyBal");
        }

        DB::beginTransaction();

        $cash = $dairyInfo->cash_in_hand - $bal->finalAmount;
        $cash = $cash + $req->cash;

        $blu = DB::table("balance_sheet")->where(['id' => $req->balSheetId, "dairyId" => $dairyInfo->id])
            ->update(["status" => 'false', "updated_at" => date("Y-m-d H:i:s")]);

        if ($blu == (false)) {
            DB::rollback();
            Session::flash('msg', 'An Error has occured.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairyBal");
        }

        $u = DB::table("dairy_info")->where("id", $dairyInfo->id)->update(["cash_in_hand" => $cash]);

        $bs = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $dairyInfo->ledgerId,
            'transactionId' => $dairyInfo->id,
            'srcDest' => $dairyInfo->ledgerId,
            'dairyId' => $dairyInfo->id,
            'colMan' => $colMan->userName,
            'transactionType' => 'dairy_cash',
            'remark' => "Cash " . $req->type . " (" . $req->remark . ")",
            'amountType' => $bal->amountType,
            'finalAmount' => $req->cash,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        if ($u && $bs) {
            DB::commit();
            Session::flash('msg', 'Cash updated successfuly.');
            Session::flash('alert-class', 'alert-success');
            return redirect("dairyBal");
        }

        DB::rollback();
        Session::flash('msg', 'Cash not updated.');
        Session::flash('alert-class', 'alert-danger');
        return redirect("dairyBal");
    }

    public function getLastCashEdit()
    {
        $dairyInfo = DB::table("dairy_info")->where("id", session()->get('loginUserInfo')->dairyId)->get()->first();

        $colMan = Session::get("colMan");

        $lastTxn = DB::table("balance_sheet")->where([
            "transactionType" => "dairy_cash",
            "dairyId" => $dairyInfo->id,
            "status" => "true",
        ])->orderBy('created_at', "DESC")->get()->first();

        if ($lastTxn == (null || false)) {
            return ["error" => true, "msg" => "Cash is not edited till now."];
        }

        if (strtotime($lastTxn->created_at) < strtotime("-1 day")) {
            $lastTxn->created_at = date("d-m-Y g:i A", strtotime($lastTxn->created_at));
            return ["error" => false, "editable" => false, "details" => $lastTxn];
        } else {
            $lastTxn->created_at = date("d-m-Y g:i A", strtotime($lastTxn->created_at));
            return ["error" => false, "editable" => true, "details" => $lastTxn];
        }
    }

    public function dairy_settings()
    {

        $dairy = DB::table("dairy_info")->where("id", session()->get('colMan')->dairyId)->get()->first();

        $prp = DB::table("dairy_propritor_info")->where("dairyId", session()->get('colMan')->dairyId)->get()->first();

        $dairy->stateName = DB::table("states")->where("id", $dairy->state)->pluck("name")->first();
        $dairy->cityName = DB::table("city")->where("id", $dairy->city)->pluck("name")->first();

        $prp->stateName = DB::table("states")->where("id", $prp->dairyPropritorState)->pluck("name")->first();
        $prp->cityName = DB::table("city")->where("id", $prp->dairyPropritorCity)->pluck("name")->first();

        $subsc = DB::table("subscribe")->where(["dairyId" => session()->get('colMan')->dairyId])
            ->join("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")
            ->get()->first();

        $t = (object) [
            "dairyId" => "",
            "status" => "",
            "machinType" => "",
            "communicationPort" => "",
            "maxSpeed" => "",
            "echo" => "",
            "connectionPerferenceDataBits" => "",
            "connectionPerferenceParity" => "",
            "connectionPerferenceStopBits" => "",
            "flowControl" => "",
            "weightMode" => "",
            "weightMode_auto_tare" => "",
            "weightMode_no_training" => "",
            "weightMode_weight_in_doublke_decimal" => "",
            "weightMode_write_in" => "",
            "isActive" => "0",
            "created_at" => "",
        ];

        $mu = DB::table('utility_setup')
            ->where('dairyId', session()->get('colMan')->dairyId)
            ->where('status', 'true')
            ->where("machinType", "milk")
            ->get()->first();

        $wu = DB::table('utility_setup')
            ->where('dairyId', session()->get('colMan')->dairyId)
            ->where('status', 'true')
            ->where("machinType", "weight")
            ->get()->first();

        if ($mu == null) {
            $mu = $t;
        }

        if ($wu == null) {
            $wu = $t;
        }

        return view("dairy_settings", ["dairy" => $dairy, "prp" => $prp, "subsc" => $subsc, "mu" => $mu, "wu" => $wu]);
    }

    public function checkNotification()
    {
        $dairy = session()->get("dairyInfo");

        $not = DB::table('notifications')
            ->selectRaw("*, DATE_FORMAT(created_at, '%h:%i %p %d %b %Y') as created_at")
            ->where(["ledgerId" => $dairy->ledgerId])->orderby("id", "DESC")
            ->limit(100)->get();

        if (count($not) > 0) {
            return ["error" => false, "data" => $not];
        } else {
            return ["error" => true, "msg" => "No new notification."];
        }
    }

    public function deleteNotification()
    {
        $dairy = session()->get("dairyInfo");

        $not = DB::table('notifications')->where(["ledgerId" => $dairy->ledgerId, "id" => request("notiId")])->delete();

        if ($not) {
            return ["error" => false, "msg" => "Notification deleted"];
        } else {
            return ["error" => true, "msg" => "Some error has occured while removing notification."];
        }
    }
}
