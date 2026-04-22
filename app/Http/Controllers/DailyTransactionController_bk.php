<?php

namespace App\Http\Controllers;

use App\dailyTransaction;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PushNotification;
use View;

class DailyTransactionController extends Controller
{

    public function __construct()
    {
        $this->middleware('Auth');
    }

    /* Daily Transaction Psf */
    public function DailyTransactionPsf(Request $request)
    {

        $dailyTransactionsSingle = DB::table('daily_transactions')
            ->where('id', $request->listId)
            ->get()->first();

        $dairyInfo = DB::table('dairy_info')
            ->where('id', session()->get('loginUserInfo')->dairyId)
            ->get()->first();
        $dairyPer = DB::table('dairy_propritor_info')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->get()->first();
        $member = DB::table('member_personal_info')
            ->where('memberPersonalCode', $dailyTransactionsSingle->memberCode)
            ->get()->first();

        // $pdf = PDF::loadView('pdfView', [
        //     'dairyInfo' => $dairyInfo,
        //     'dairyPer'  => $dairyPer,
        //     'member'    => $member,
        //     'memberCode' => $dailyTransactionsSingle->memberCode,
        //     "memberName" => $dailyTransactionsSingle->memberName,
        //     "date" => $dailyTransactionsSingle->date,
        //     "milkType" => $dailyTransactionsSingle->milkType,
        //     "milkQuality" => $dailyTransactionsSingle->milkQuality,
        //     // "rateCardType"=>$dailyTransactionsSingle->rateCardType,
        //     "fat" => $dailyTransactionsSingle->fat,
        //     "snf" => $dailyTransactionsSingle->snf,
        //     "amount" => $dailyTransactionsSingle->amount,
        // ]);

        // return $pdf->download('Daily Transactions Report.pdf');

        return view('pdfView', [
            'dairyInfo' => $dairyInfo,
            'dairyPer' => $dairyPer,
            'member' => $member,
            'memberCode' => $dailyTransactionsSingle->memberCode,
            "memberName" => $dailyTransactionsSingle->memberName,
            "date" => $dailyTransactionsSingle->date,
            "milkType" => $dailyTransactionsSingle->milkType,
            "milkQuality" => $dailyTransactionsSingle->milkQuality,
            // "rateCardType"=>$dailyTransactionsSingle->rateCardType,
            "fat" => $dailyTransactionsSingle->fat,
            "snf" => $dailyTransactionsSingle->snf,
            "amount" => $dailyTransactionsSingle->amount,
        ]);
    }

    /* Function for Daily Transaction List */
    public function DailyTransactionList(Request $request)
    {

        $dailyTransactions = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where("status", "true")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dailyTransactionList', ['dailyTransactions' => $dailyTransactions, "activepage" => "milkcollection"]);
    }

    /* Function for ajax Daily Transaction List */
    public function DailyTransactionListAjax(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        session()->put('dailyTransactionDate', $request->date);
        session()->put('dailyTransactionShift', $request->shift);

        $msc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d", strtotime($request->date)))->where("shift", "morning")
            ->where('status', "true")
            ->sum("milkQuality");

        $esc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d", strtotime($request->date)))->where("shift", "evening")
            ->where('status', "true")
            ->sum("milkQuality");

        // return $request;
        $dailyTransactions = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where("status", "true")
            ->where("date", date("Y-m-d", strtotime($request->date)))
            ->where("shift", strtolower($request->shift))
            ->orderBy('created_at', 'desc')
            ->get();

        $view = View::make('dailyTransactionListModel', ['dailyTransactions' => $dailyTransactions]);
        return ["error" => false, "content" => (string) $view, "msc" => number_format($msc, 1, ".", ""), "esc" => number_format($esc, 1, ".", "")];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function DailyTransactionForm(Request $req)
    {
        if (session()->has('dailyTransactionDate')) {$date = session()->get('dailyTransactionDate');} else { $date = date("d-m-Y");}

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $memberInfo = DB::table('member_personal_info')
            ->where('dairyId', $dairyId)->where("status", "true")
            ->get();

        $colMan = DB::table('other_users')
            ->where('dairyId', $dairyId)
            ->where('userName', session()->get('loginUserInfo')->userName)
            ->get()->first();

        $ratecardtype = $colMan->rateCardTypeForCow;

        $noMember = 0;
        $noRateCard = 0;
        if (count($memberInfo) == 0) {
            $noMember = 1;
        }
        if ($colMan->rateCardIdForCow == (null || "")) {
            $noRateCard = 1;
        }
        if ($colMan->rateCardIdForBuffalo == (null || "")) {
            $noRateCard = 1;
        }

        $msc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d", strtotime($date)))->where("shift", "morning")
            ->where('status', "true")
            ->sum("milkQuality");

        $esc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d", strtotime($date)))->where("shift", "evening")
            ->where('status', "true")
            ->sum("milkQuality");

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
            "decimal_digit" => "1",
            "created_at" => "",
        ];

        $mUtility = DB::table('utility_setup')
            ->where('dairyId', $dairyId)
            ->where('status', 'true')
            ->where("machinType", "milk")
            ->get()->first();

        $wUtility = DB::table('utility_setup')
            ->where('dairyId', $dairyId)
            ->where('status', 'true')
            ->where("machinType", "weight")
            ->get()->first();

        if ($mUtility == null) {
            $mUtility = $t;
        }

        if ($wUtility == null) {
            $wUtility = $t;
        }

        return view('dailyTransaction', ["memberInfo" => $memberInfo, "msc" => number_format($msc, 1, ".", ""), "esc" => number_format($esc, 1, ".", ""),
            "noRateCard" => $noRateCard, "noMember" => $noMember, "mUtility" => $mUtility, "wUtility" => $wUtility,
            "activepage" => "milkcollection", "colMan" => $colMan, "ratecardtype" => $ratecardtype]);
    }

    /* get member name by member code */
    public function DailyTransactionMemberCode(Request $request)
    {

        $memberInfo = DB::table('member_personal_info')
            ->where('memberPersonalCode', $request->member_code)
            ->where('status', "true")
            ->get()->first();

        if (!$memberInfo) {
            return $memberInfo->memberPersonalName;
        } else {
            return "";
        }
    }

    /* get member code by member name */

    public function DailyTransactionMemberName(Request $req)
    {

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $contents = "";

        if ($req->field == ("code" || "" || null)) {
            $memberInfo = DB::table('member_personal_info')
                ->where('status', "true")
                ->where('memberPersonalCode', $req->member_code)->where("dairyId", $dairyId)
                ->get()->first();
        }
        if ($req->field == "name") {
            $memberInfo = DB::table('member_personal_info')
                ->where('memberPersonalName', $req->member_code)->where("dairyId", $dairyId)
                ->where('status', "true")
                ->get()->first();
        }

        if ($memberInfo == (null || false)) {
            return ["error" => true, "msg" => "No Member Found."];
        }
        $info = DB::table('member_other_info')
            ->where('memberId', $memberInfo->id)
            ->get()->first();

        $balance = DB::table('user_current_balance')->where(["ledgerId" => $memberInfo->ledgerId])->get()->first();

        $trans = DB::table('daily_transactions')->where("memberCode", $memberInfo->memberPersonalCode)
            ->where("date", date("Y-m-d", strtotime($req->date)))
            ->where("status", "true")->where("dairyId", $dairyId)
            ->where("shift", $req->shift)
            ->get();

        $colman = DB::table("other_users")->where("dairyId", $dairyId)->where("userName", "DAIRYADMIN")->get()->first();

        $valueType = "fat";

        if ($info->milkeType == "cow") {
            $valueType = $colman->rateCardTypeForCow;
        }elseif ($info->milkeType == "buffalo") {
            $valueType = $colman->rateCardTypeForBuffalo;
        }else{
            $valueType = $colman->rateCardTypeForCow;
        }

        if (count($trans) > 0) {
            $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
            $view = View::make('userTransEntryModel', ["trans" => $trans, "dairyInfo" => $dairyInfo, "memberInfo" => $memberInfo, "info" => $info,
                "date" => $req->date, "shift" => $req->shift, "valueType" => $valueType]);
            $contents = (string) $view;
            // or
            // $contents = $view->render();
        }

        if (!(empty($memberInfo))) {
            return ["error" => false, "msg" => "", "code" => $memberInfo->memberPersonalCode, "name" => $memberInfo->memberPersonalName,
                "trans" => $contents, "milkType" => $info->milkeType, "valueType" => $valueType,
                "balance" => $balance->openingBalance, "balanceType" => ($balance->openingBalanceType == 'credit') ? "Cr." : "Dr."];
        } else {
            return ["error" => true, "msg" => "No Member Found."];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
        // return ["error" => true, "msg"=> "false"];
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairy = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        if ($dairy == null) {
            return ["error" => true, "msg" => "Some error occured. please login again."];
        }
        $submitClass = new dailyTransaction();
        $res = $submitClass->DailyTransactionSubmit($req);
        
        if ($res["error"]) {
            return ["error" => true, "msg" => $res["msg"]];
        } else {
            $app_data = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $res['memberId']])->get();

            $appSettings = DB::table('androidappsetting')->get()->first();

            if ($app_data == null) {
                $memberToken = null;
            } else {
                $memberToken = $app_data->pluck('token_key');
            }

            // $memberToken = implode(",",$memberToken);

            $data = [
                "dairyId" => $dairyId,
                "memberId" => $res['memId'],
                "mobile" => $res["mobile"],
                "society_code" => $dairy->society_code,
                "society_name" => $dairy->dairyName,
                "memberName" => $res['memberName'],
                "dailyShift" => request("dailyShift"),
                "date" => request('date'),
                "quantity" => request('quantity'),
                "fat" => request('fat'),
                "snf" => request('snf'),
                "rate" => $res['rate'],
                "amount" => $res['amount'],
                "bal" => $res['currentBalance'],
                "memberToken" => $memberToken,
                "serverKey" => $appSettings->server_api_key,
            ];

            $alerts = DB::table("member_other_info")->where(["memberId" => $res["memId"]])->get()->first();
            $slip = false;
            $sms = false;
            $slip_data = null;
            $sms_data = null;
            if ($alerts->alert_print_slip == "true") {
                $slip = true;
                $slip_data = $this->printSlipTrans($res["transId"]);
            }
            if ($alerts->alert_sms == "true") {
                $sms = true;

                // return $data;
                $sms_msg = $this->dailyTransactionSmsTemplate($data);
            }

            $noti = $this->dailyTransactionPushNoti($data);

            return ["error" => false, "msg" => "Transaction finished, Milk Collected.", "isSlip" => $slip,
                "transId" => $res["transId"], "slip_data" => $slip_data, "sms" => $sms, "noti" => $noti, "sms_msg" => isset($sms_msg) ? $sms_msg : '',
                "memberToken" => $memberToken, "sdfrer" => $appSettings->server_api_key,"memberToken" => $memberToken,
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\dailyTransaction  $dailyTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // echo $request->transactionId ;
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $memberInfo = DB::table('member_personal_info')
                ->where('dairyId', $dairyId)
                ->get();

        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->get()->first();

        $dailyTransactions = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->orderBy('created_at', 'desc')
            ->get();

        $dailyTransactionsSingle = DB::table('daily_transactions')
            ->where('id', $request->transactionId)
            ->get()->first();

        $returnData = array($memberInfo, $dairyInfo->rateCard, $dailyTransactions, $dailyTransactionsSingle);
        return view('dailyTransactionEdit', ["returnData" => $returnData, "activepage" => "milkcollection"]);

    }

    public function DailyTransactionDelete(Request $request)
    {
        $submitClass = new dailyTransaction();
        $submitReturn = $submitClass->DailyTransactionDelete($request);

        if ($submitReturn) {
            return ["error" => false, "msg" => Session::get('msg'), "alert-class" => Session::get("alert-class")];
        } else {
            return ["error" => true, "msg" => Session::get('msg'), "alert-class" => Session::get("alert-class")];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\dailyTransaction  $dailyTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $submitClass = new dailyTransaction();
        $submitReturn = $submitClass->DailyTransactionEditSubmit($request);

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $memberInfo = DB::table('member_personal_info')
            ->where('dairyId', $dairyId)
            ->get();

        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->get()->first();

        $dailyTransactions = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->orderBy('created_at', 'desc')
            ->get();

        $returnData = array($memberInfo, $dairyInfo->rateCard, $dailyTransactions);
        return view('dailyTransaction', ["returnData" => $returnData, "activepage" => "milkcollection"]);

    }

    public function DailyTransactionResendNoti(Request $request)
    {
        $submitClass = new dailyTransaction();
        $submitReturn = $submitClass->DailyTransactionResendNoti($request);

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $memberInfo = DB::table('member_personal_info')
            ->where('dairyId', $dairyId)
            ->get();

        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->get()->first();

        $dailyTransactions = DB::table('daily_transactions')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->orderBy('created_at', 'desc')
            ->get();

        $returnData = array($memberInfo, $dairyInfo->rateCard, $dailyTransactions);
        return view('dailyTransaction', ["returnData" => $returnData, "activepage" => "milkcollection"]);

        /*$submitClass = new dailyTransaction();
    $submitReturn = $submitClass->DailyTransactionResendNoti($request,$dailyTransactionsSingle->amount);*/

    }

    public function updateTransaction(Request $req)
    {
        // return $req->all();

        if ($req->action == "new") {

            $validatedData = Validator::make($req->all(), [
                'quantity' => 'required',
                'fat' => 'required',
                // 'snf'    => 'required',
                'amount' => 'required',
                'memberCode' => 'required',
                'memberName' => 'required',
                'date' => 'required',
                'dairyId' => 'required',
                'dailyShift' => 'required',
                'price' => 'required',
                'milkType' => "required",
            ]);

            if ($validatedData->fails()) {
                return ["error" => true, "msg" => "Some fields are missing."];
            }

            if ($req->rateCardType == "fat/snf") {
                if ($req->snf == (null || "")) {
                    return ["error" => true, "msg" => "SNF required."];
                }
            }

            // $submitClass = new dailyTransaction();
            // $submitReturn = $submitClass->DailyTransactionSubmit($req);

            // if ($req->ajax == "true") {
            //     if ($submitReturn["error"]) {
            //         return ["error" => true, "msg" => $submitReturn["msg"]];
            //     } else {
            //         $alerts = DB::table("member_other_info")->where(["id" => $submitReturn["memId"]])->get()->first();
            //         $slip = false;
            //         $slip_data = null;
            //         if($alerts->alert_print_slip == "true"){
            //             $slip = true;
            //             $slip_data = $this->printSlipTrans($submitReturn["transId"]);
            //         }

            //         return ["error" => false, "msg" => "Transaction Saved.", "isSlip" => $slip,
            //                 "transId" => $submitReturn["transId"], "slip_data" => $slip_data];
            //     }
            // }

            return $this->create($req);
        }

        if ($req->action == "replace") {

            $validatedData = $req->validate([
                'dailyTransactionId' => 'required',
                'quantity' => 'required',
                'fat' => 'required',
                // 'snf' => 'required',
                'amount' => 'required',
                'memberCode' => 'required',
                'dairyId' => 'required',
            ]);

            if ($req->rateCardType == "fat/snf") {
                if ($req->snf == (null || "")) {
                    return ["error" => true, "msg" => "SNF required."];
                }
            }

            $updateClass = new dailyTransaction();
            $updateReturn = $updateClass->DailyTransactionEditSubmit($req);

            if ($req->ajax == "true") {
                $c = Session::get('alert-class');

                if ($updateReturn) {
                    return ["error" => false, "msg" => Session::get('msg')];
                } else {
                    return ["error" => true, "msg" => Session::get('msg')];
                }

            }

            // return redirect("DailyTransactionForm?date=" . $req->date . "&shift=" . $req->dailyShift);
        }

        return ["false" => false];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\dailyTransaction  $dailyTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(dailyTransaction $dailyTransaction)
    {
        //
    }

    public function getUserDetail(Request $req)
    {
        // return $req->all();
        $dairyId = session()->get("loginUserInfo")->dairyId;

        switch ($req->user) {
            case "memberDetail":
            case "member":{
                    if ($req->qtype == "name") {
                        $member = DB::table('member_personal_info')
                            ->where('status', "true")
                            ->where('memberPersonalName', $req->q)->where('dairyId', $dairyId)
                            ->get()->first();
                        if ($member == (null || false)) {
                            return ["error" => true, "msg" => "No member found like '$req->q'"];
                        }
                    } else {
                        $member = DB::table('member_personal_info')
                            ->where('status', "true")
                            ->where(['memberPersonalCode' => $req->q, 'dairyId' => $dairyId])
                            ->get()->first();
                        if ($member == (null || false)) {
                            return ["error" => true, "msg" => "No member found with code '$req->q'"];
                        }
                    }
                    unset($member->password);
                    $bl = DB::table("user_current_balance")->where("ledgerId", $member->ledgerId)->get()->first();
                    if ($bl == (null || false)) {
                        return ["error" => true, "msg" => "Member Balance Acc not found."];
                    }

                    return ["error" => false, "data" => ["name" => $member->memberPersonalName, "code" => $member->memberPersonalCode,
                        "bal" => round($bl->openingBalance, 2), "balType" => strtolower($bl->openingBalanceType), "isCash" => false,
                        "ledgerId" => $member->ledgerId]];

                    break;
                }

            case "customer":{
                    if ($req->qtype == "name") {
                        $customer = DB::table('customer')
                            ->where('customerName', $req->q)->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($customer == (null || false)) {
                            return ["error" => true, "msg" => "No customer found like '$req->q'"];
                        }
                    } else {
                        $customer = DB::table('customer')
                            ->where('customerCode', $req->q)->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($customer == (null || false)) {
                            return ["error" => true, "msg" => "No customer found with code '$req->q'"];
                        }
                    }
                    if ($customer->customerCode == $req->dairyId . "C1") {
                        $cashuser = true;
                    } else {
                        $cashuser = false;
                    }

                    $bl = DB::table("user_current_balance")->where("ledgerId", $customer->ledgerId)->get()->first();
                    if ($bl == (null || false)) {
                        return ["error" => true, "msg" => "Customer Balance Acc not found."];
                    }

                    unset($customer->password);
                    return ["error" => false, "data" => ["name" => $customer->customerName, "code" => $customer->customerCode,
                        "bal" => round($bl->openingBalance, 2), "balType" => strtolower($bl->openingBalanceType), "isCash" => $cashuser]];
                    break;
                }

            case "supplier":{
                    if ($req->qtype == "name") {
                        $supplier = DB::table('suppliers')
                            ->where('supplierFirmName', $req->q)->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($supplier == (null || false)) {
                            return ["error" => true, "msg" => "No Supplier found like '$req->q'"];
                        }
                    } else {
                        $supplier = DB::table('suppliers')
                            ->where('supplierCode', $req->q)->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($supplier == (null || false)) {
                            return ["error" => true, "msg" => "No Supplier found with code '$req->q'"];
                        }
                    }

                    $bl = DB::table("user_current_balance")->where("ledgerId", $supplier->ledgerId)->get()->first();
                    if ($bl == (null || false)) {
                        return ["error" => true, "msg" => "Supplier Balance Acc not found."];
                    }

                    unset($supplier->password);
                    return ["error" => false, "data" => ["name" => $supplier->supplierFirmName, "code" => $supplier->supplierCode,
                        "bal" => round($bl->openingBalance, 2), "balType" => strtolower($bl->openingBalanceType), "isCash" => false]];
                    break;
                }

            default:
                return ["error" => true, "msg" => "Something bed."];
        }
    }

    public function milkRequest()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $colMan = Session::get("colMan");
        $colMans = Db::table("other_users")->where("dairyId", $dairyId)->get();

        if (request("deliveryType") == (null || "0" || 0)) {
            $isDeliverd = 0;
            $deliveryType = "0";
        } elseif (request("deliveryType") == 2) {
            $isDeliverd = 2;
            $deliveryType = "2";
        } else {
            $deliveryType = request("deliveryType");
            $isDeliverd = 1;
            if ($deliveryType == "lastweek") {
                $btwn['from'] = date("Y-m-d 00:00:00", strtotime("-1 week"));
                $btwn['to'] = date("Y-m-d 23:59:59");
            }
            if ($deliveryType == "lastmonth") {
                $btwn['from'] = date("Y-m-d 00:00:00", strtotime("-1 month"));
                $btwn['to'] = date("Y-m-d 23:59:59");
            }
            if ($deliveryType == "6month") {
                $btwn['from'] = date("Y-m-d 00:00:00", strtotime("-6 month"));
                $btwn['to'] = date("Y-m-d 23:59:59");
            }
        }

        $query = DB::table('milkrequest');
        if ($colMan->userName == "DAIRYADMIN") {
            $query = $query->where(["dairyId" => $dairyId, "isDeliverd" => $isDeliverd])->whereIn("type", ["cowMilk", "buffaloMilk", "milk"]);
            if (isset($btwn)) {
                $query = $query->whereBetween("created_at", $btwn);
                $query = $query->orderby("created_at", "asc");
            } else {
                $query = $query->orderby("created_at", "desc");
            }
            $milkReq = $query->get();
            $isAdmin = true;
        } else {
            $query = $query->where(["dairyId" => $dairyId, 'colMan' => $colMan->userName, "isDeliverd" => $isDeliverd])
                ->whereIn("type", ["cowMilk", "buffaloMilk", "milk"]);
            if (isset($btwn)) {
                $query = $query->whereBetween("created_at", $btwn);
                $query = $query->orderby("created_at", "asc");
            } else {
                $query = $query->orderby("created_at", "desc");
            }
            $milkReq = $query->get();

            $isAdmin = false;
        }

        if (count($milkReq) == 0) {
            $noMember = 1;
        } else {
            $noMember = 0;
        }

        return view("milkRequest", ["noMember" => $noMember, "colMan" => $colMan, "milkReq" => $milkReq, "colMans" => $colMans,
            "isAdmin" => $isAdmin, "activepage" => "milkRequest", "deliveryType" => $deliveryType]);
    }

    public function prodRequest()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $colMan = Session::get("colMan");
        $colMans = Db::table("other_users")->where("dairyId", $dairyId)->get();

        if (request("deliveryType") == (null || "0" || 0)) {
            $isDeliverd = 0;
            $deliveryType = "0";
        } elseif (request("deliveryType") == 2) {
            $isDeliverd = 2;
            $deliveryType = "2";
        } else {
            $deliveryType = request("deliveryType");
            $isDeliverd = 1;
            if ($deliveryType == "lastweek") {
                $btwn['from'] = date("Y-m-d 00:00:00", strtotime("-1 week"));
                $btwn['to'] = date("Y-m-d 23:59:59");
            }
            if ($deliveryType == "lastmonth") {
                $btwn['from'] = date("Y-m-d 00:00:00", strtotime("-1 month"));
                $btwn['to'] = date("Y-m-d 23:59:59");
            }
            if ($deliveryType == "6month") {
                $btwn['from'] = date("Y-m-d 00:00:00", strtotime("-6 month"));
                $btwn['to'] = date("Y-m-d 23:59:59");
            }
        }

        $query = DB::table('milkrequest');
        if ($colMan->userName == "DAIRYADMIN") {
            $query = $query->where(["dairyId" => $dairyId, "type" => "product", "isDeliverd" => $isDeliverd]);
            if (isset($btwn)) {
                $query = $query->whereBetween("created_at", $btwn);
                $query = $query->orderby("created_at", "asc");
            } else {
                $query = $query->orderby("created_at", "desc");
            }
            $prodReq = $query->get();
            $isAdmin = true;
        } else {
            $query = $query->where(["dairyId" => $dairyId, "type" => "product", 'colMan' => $colMan->userName, "isDeliverd" => $isDeliverd]);
            if (isset($btwn)) {
                $query = $query->whereBetween("created_at", $btwn);
                $query = $query->orderby("created_at", "asc");
            } else {
                $query = $query->orderby("created_at", "desc");
            }
            $prodReq = $query->get();

            $isAdmin = false;
        }

        if (count($prodReq) == 0) {
            $noMember = 1;
        } else {
            $noMember = 0;
        }

        return view("prodRequest", ["noMember" => $noMember, "colMan" => $colMan, "prodReq" => $prodReq, "colMans" => $colMans,
            "isAdmin" => $isAdmin, "activepage" => "prodRequest", "deliveryType" => $deliveryType]);
    }

    public function getReqs(Request $req)
    {
        // $seen = DB::table("milkrequest")->where("dairyId", $u->dairyId)->where("memberCode", $u->memberPersonalCode)
        // ->where("isSeen", "false")->update(["isSeen"=>"true", "seen_at"=> date("Y-m-d H:i:s")]);

        $u = Session::get("loginUserInfo");

        $milkReq = DB::table("milkrequest")->where("memberCode", $req->memCode)->where("dairyId", $u->dairyId)->get();

        if ($milkReq != (null || false)) {
            $content = View::make('requestViewModel', ["milkReq" => $milkReq]);
            return ["error" => false, "content" => (string) $content];
        }

        return ["error" => true, "milkReq" => $milkReq];
    }

    public function notifSubmit(Request $req)
    {
        $res = DB::table("milkrequest")->where("id", $req->requestId)->update([
            "resText" => $req->resText,
            "response_at" => date("Y-m-d H:i:s"),
        ]);

        if ($res == (null || false)) {
            Session::flash('msg', 'There is a problem, Error Code: ERROR_IN_INSERTING');
            Session::flash('alert-class', 'alert-danger');
            return redirect("milkRequest");
        }

        Session::flash('msg', 'Response Sent');
        Session::flash('alert-class', 'alert-success');
        return redirect("milkRequest");
    }

    public function assignReq(Request $req)
    {
        // return [$req->all()];
        $res = DB::table("milkrequest")->where("id", $req->milkReqId)->update([
            "colMan" => $req->colMan,
            "updated_at" => date("Y-m-d H:i:s"),
        ]);

        if ($res == (null || false)) {
            return ["error" => true, "msg" => "There is a problem, Error Code: ERROR_IN_INSERTING"];
        }

        return ["error" => false, "msg" => "Updated!"];

    }

    public function printSlipTrans($dailyTransId = null)
    {
        if ($dailyTransId == null && request("dailyTransId") == null) {
            return ["error" => true, "msg" => "transaction id is null.", "data" => null];
        }
        if (request("dailyTransId") != null) {
            $dailyTransId = request("dailyTransId");
        }
        if ($dailyTransId == null) {
            return ["error" => true, "msg" => "transaction id is null.", "data" => null];
        }

        $colMan = Session::get("colMan");
        if ($colMan == (null || false)) {
            return ["error" => true, "msg" => "Authentication required.", "data" => null];
        }

        $dairy = DB::table("dairy_info")->where("id", $colMan->dairyId)->get()->first();

        $tr = DB::table("daily_transactions")->where(["id" => $dailyTransId, "status" => "true"])->get()->first();

        if ($tr == (null || false)) {
            return ["error" => true, "msg" => "No transaction found to print.", "data" => null];
        }

        $mem = DB::table("member_personal_info")->where(["memberPersonalCode" => $tr->memberCode, "dairyId" => $dairy->id])->get()->first();
        $ub = DB::table("user_current_balance")->where(["ledgerId" => $mem->ledgerId])->get()->first();

        $data = View::make("print.dailyTrans", ["dairy" => $dairy, 'trans' => $tr, "ub" => $ub]);

        return ["error" => false, "msg" => "", "data" => (string) $data];

        // return view("print.dailyTrans", ["dairy" => $dairy, 'trans' => $tr, "ub" => $ub]);
    }

    public function dailyTransactionSmsTemplate($data)
    {
        $newLine = "%0A";
        if (request('snf') != null) {
            $snf = "SNF: " . $data['snf'] . $newLine;
            $messageType = "milkCollectionWithSnf";
        } else {
            $snf = "";
            $messageType = "milkCollectionWithoutSnf";
        }
        $shift = strtoupper(substr($data["dailyShift"], 0, 1));

        $tempName = explode(" ", $data['memberName']);
        if (isset($tempName[1])) {
            $memName = $tempName[0] . $tempName[1];
        } else {
            $memName = $tempName[0];
        }

        $smsData = ["message" => "Dear " . $memName . "," . $newLine . $data['society_code'] . " - " . $data['society_name'] . $newLine
            . "Date: " . date("d-m-Y ", strtotime($data['date'])) . $shift . $newLine
            . "Qty: " . $data['quantity'] . $newLine
            . "FAT: " . $data['fat'] . $newLine
            . $snf
            . "Rate: " . $data['rate'] . $newLine
            . "Amount: " . $data['amount'] . $newLine
            . "Current Balance: " . $data['bal'] . $newLine,

            "numbers" => $data['mobile'],
            "messageType" => $messageType
        ];

        $sms = new \App\Sms();

        // $sms->saveToQueue($data, $dairyId);
        return $sms->send($smsData, $data['dairyId']);
    }

    public function dailyTransactionPushNoti($data)
    {
        // if($data["memberToken"] == null){
        //     return false;
        // }

        $newLine = "\n";
        if (request('snf') != null) {
            $snf = "SNF: " . $data['snf'] . $newLine;
        } else {
            $snf = "";
        }
        $shift = strtoupper(substr($data["dailyShift"], 0, 1));

        $tempName = explode(" ", $data['memberName']);
        if (isset($tempName[1])) {
            $memName = $tempName[0] . $tempName[1];
        } else {
            $memName = $tempName[0];
        }
        $message = "Dear " . $memName . "," . $newLine . $data['society_code'] . " - " . $data['society_name'] . $newLine
        . "Date: " . date("d-m-Y ", strtotime($data['date'])) . $shift . $newLine
            . "Qty: " . $data['quantity'] . $newLine
            . "FAT: " . $data['fat'] . $newLine
            . $snf
            . "Rate: " . $data['rate'] . $newLine
            . "Amount: " . $data['amount'] . $newLine
            . "Current Balance: " . $data['bal'] . $newLine;

        // $tkns = implode(",", $data['memberToken']);
        // $tkns = $data['memberToken'];

        $response = [];
        if(count($data['memberToken']) > 0){

            foreach($data['memberToken'] as $tkn){
                $response[] = PushNotification::setService('fcm')
                    ->setMessage([
                        'data' => [
                            "message" => $message,
                        ],
                    ])
                    ->setApiKey($data['serverKey'])
                    ->setDevicesToken($tkn)
                    ->send()
                    ->getFeedback();
            }
    
        }else{
            
            $response[] = PushNotification::setService('fcm')
                ->setMessage([
                    'data' => [
                        "message" => $message,
                    ],
                ])
                ->setApiKey($data['serverKey'])
                ->setDevicesToken($data['memberToken'])
                ->send()
                ->getFeedback();

        }

        return $response;
    }

    public function requestComplete()
    {
        $r = DB::table("milkrequest")->where("id", request("id"))->get()->first();

        if ($r == (null || false)) {
            return ["error" => true, "msg" => "Request not found"];
        }
        if (isset($r->isDeliverd) && $r->isDeliverd) {
            return ["error" => true, "msg" => "Request Already Completed."];
        }

        if (request("action") == "complete") {
            $m = DB::table("member_personal_info")->where(["memberPersonalCode" => $r->memberCode, "dairyId" => $r->dairyId])->get()->first();
            if ($m == (null || false)) {
                return ["error" => true, "msg" => "Member not found"];
            }

            if ($r->type == "milk") {
                $res = DB::table("milkrequest")->where("id", request("id"))->update(["isDeliverd" => 1]);
                if ($res == false) {
                    return ["error" => true, "msg" => "Error in updating."];
                } else {
                    return ["error" => false, "msg" => "Milk request Complted."];
                }
            }

            if ($r->type == "product") {
                $p = DB::table('products')->where(["dairyId" => $r->dairyId, "productCode" => $r->productCode])->get()->first();
                if ($p == null || $p->productUnit < $r->qty) {
                    return ["error" => true, "msg" => "Stock is not enough to product delivery."];
                }

                $dt = [
                    'dairyId' => $r->dairyId,
                    'status' => "true",
                    'partyName' => $m->memberPersonalName,
                    'partyType' => 'member',
                    'memberCode' => $r->memberCode,
                    'product' => $r->productCode,
                    'unit' => 'Unit',
                    'quantity' => $r->qty,
                    'PricePerUnit' => $r->rate,
                    'amount' => '0',
                    'discount' => '0',
                    'finalAmount' => number_format((float) $r->qty * (float) $r->rate, 2, ".", ""),
                    'paidAmount' => '0',
                    'date' => date("Y-m-d"),
                    'sale_type' => 'product_sale',
                ];

                $sale = new \App\sales();
                $saleRes = $sale->localSaleFormSubmit((object) $dt);

                if (!$saleRes) {
                    return ["error" => true, "msg" => "There is an error occured."];
                }

                $res = DB::table("milkrequest")->where("id", request("id"))->update(["isDeliverd" => 1]);

                return ["error" => false, "msg" => "Product delivery done, amount debited"];
            }
        } elseif (request("action") == "decline") {
            $res = DB::table("milkrequest")->where("id", request("id"))->update(["isDeliverd" => 2]);
            return ["error" => false, "msg" => ucfirst($r->type) . " request has been decliend."];
        } else {
            return ["error" => true, "msg" => ucfirst($r->type) . " request can't be completed, bed request."];
        }
    }

}
