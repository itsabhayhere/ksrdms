<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Customer extends Model
{

    public function CustomerSubmit($request)
    {
        DB::beginTransaction();
        /* password */
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for ($i = 0; $i < 8; $i++) {
            $random_int = mt_rand();
            $password .= $charset[$random_int % strlen($charset)];
        }

        $colMan = Session::get('colMan');

        // $dairy = DB::table("dairy_info")->where("id", session()->get("loginUserInfo")->dairyId)->get()->first();
        $custCode = $request->dairyId . "C" . $request->customerCode;

        $currentTime = date('Y-m-d H:i:s');

        $submiteInfo = DB::table('customer')->insertGetId([
            'dairyId' => $request->dairyId,
            'status' => $request->status,
            'customerCode' => $custCode,
            'password' => $password,
            'customerName' => $request->customerName,
            'gender' => $request->gender,
            'customerEmail' => $request->customerEmail,
            'customerMobileNumber' => $request->customerMobileNumber,
            'customerAddress' => $request->customerAddress,
            'customerState' => $request->customerState,
            'customerCity' => $request->customerCity,
            'customerVillageDistrict' => $request->customerVillageDistrict,
            'customerPincode' => $request->customerPincode,
            // 'openingBalance' => $request->customerOpeningBalance,
            // 'openingBalanceType' => $request->openingBalanceType,
            'created_at' => $currentTime,
        ]);
        if ($submiteInfo == (null || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "an error has occured."];
        }

        $ledgerId = DB::table('ledger')->insertGetId([
            'userId' => $submiteInfo,
            'dairyId' => $request->dairyId,
            'userType' => "2",
            'ledgerType' => $request->openingBalanceType,
            'created_at' => $currentTime,
        ]);
        if ($ledgerId == (null || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "an error has occured."];
        }

        if( $request->openingBalanceType == "credit" ) {
            $minBalType = "cr";
        } else {
            $minBalType = "dr";
        }

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $ledgerId,
            'transactionId' => $submiteInfo,
            'srcDest' => $ledgerId,
            'dairyId' => $request->dairyId,
            'colMan' => isset($colMan->userName) ? $colMan->userName : "DAIRYADMIN",
            'transactionType' => 'customer',
            'remark' => "Opening balance",
            'amountType' => $request->openingBalanceType,
            'finalAmount' => $request->customerOpeningBalance,
            'currentBalance' => $request->customerOpeningBalance." ".$minBalType,
            'created_at' => $currentTime,
        ]);
        if ($balanceSheetSubmit == (null || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "an error has occured."];
        }

        /* add opening balance in database current balance table  */
        $userCurrentBalance = DB::table('user_current_balance')->insertGetId([
            'ledgerId' => $ledgerId,
            'userId' => $submiteInfo,
            'userType' => '2',
            'openingBalance' => $request->customerOpeningBalance,
            'openingBalanceType' => $request->openingBalanceType,
            'created_at' => $currentTime,
        ]);
        if ($userCurrentBalance == (null || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "an error has occured."];
        }

        $u = DB::table('customer')
            ->where('id', $submiteInfo)
            ->update(['ledgerId' => $ledgerId, "txnId" => $balanceSheetSubmit]);
        if ($u == (null || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "an error has occured."];
        }

        DB::commit();

        return ["error" => false, "msg" => "Customer Added."];

    }

    public function editCustomer($request)
    {

        $suppliers = DB::table('customer')
            ->where('dairyId', $request->dairyId)
            ->get();

        $submitedCity;
        if ($request->customerCity == "--City--") {

            $submitedCity = $suppliers[0]->customerCity;
        } else {
            $submitedCity = $request->customerCity;
        }

        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('Y-m-d H:i:s');

        $updateReturn = DB::table('customer')
            ->where('id', $request->customerId)
            ->update([
                'customerName' => $request->customerName,
                'gender' => $request->gender,
                'customerEmail' => $request->customerEmail,
                'customerMobileNumber' => $request->customerMobileNumber,
                'customerAddress' => $request->customerAddress,
                'customerState' => $request->customerState,
                'customerCity' => $submitedCity,
                'customerVillageDistrict' => $request->customerVillageDistrict,
                'customerPincode' => $request->customerPincode,
                'updated_at' => $currentTime,
            ]);

        if ($updateReturn == 1) {
            $returnSuccessArray = array("Success" => "True", "Message" => "Customer Successfully Updated");
            $returnSuccessJson = json_encode($returnSuccessArray);
            return $returnSuccessJson;
        }
    }

    public function customerNewApi($dairyId, $colMan)
    {
        DB::beginTransaction();
        /* password */
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for ($i = 0; $i < 8; $i++) {
            $random_int = mt_rand();
            $password .= $charset[$random_int % strlen($charset)];
        }

        // $dairy = DB::table("dairy_info")->where("id", session()->get("loginUserInfo")->dairyId)->get()->first();
        $custCode = $dairyId . "C" . request("customerCode");

        $currentTime = date('Y-m-d H:i:s');

        $submiteInfo = DB::table('customer')->insertGetId([
            'dairyId' => $dairyId,
            'status' => "true",
            'customerCode' => $custCode,
            'password' => $password,
            'customerName' => request("name"),
            'gender' => request("gender") . "",
            'customerEmail' => request("email") . "",
            'customerMobileNumber' => request("mobileNumber"),
            'customerAddress' => request("address") . "",
            'customerState' => request("state") . "",
            'customerCity' => request("city") . "",
            'customerVillageDistrict' => request("villageDistrict") . "",
            'customerPincode' => request("pin") . "",
            'created_at' => $currentTime,
        ]);

        $ledgerId = DB::table('ledger')->insertGetId([
            'userId' => $submiteInfo,
            'dairyId' => $dairyId,
            'userType' => "2",
            'ledgerType' => request("openingBalanceType"),
            'created_at' => $currentTime,
        ]);

        if( $request->openingBalanceType == "credit" ) {
            $minBalType = "cr";
        } else {
            $minBalType = "dr";
        }

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $ledgerId,
            'transactionId' => $submiteInfo,
            'srcDest' => $ledgerId,
            'dairyId' => $dairyId,
            'colMan' => $colMan,
            'transactionType' => 'customer',
            'remark' => "Opening balance",
            'amountType' => request("openingBalanceType"),
            'finalAmount' => request("openingBalance"),
            'currentBalance' => request('openingBalance')." ".$minBalType,
            'created_at' => $currentTime,
        ]);

        /* add opening balance in database current balance table  */
        $userCurrentBalance = DB::table('user_current_balance')->insertGetId([
            'ledgerId' => $ledgerId,
            'userId' => $submiteInfo,
            'userType' => '2',
            'openingBalance' => request("openingBalance"),
            'openingBalanceType' => request("openingBalanceType"),
            'created_at' => $currentTime,
        ]);

        $u = DB::table('customer')
            ->where('id', $submiteInfo)
            ->update(['ledgerId' => $ledgerId, "txnId" => $balanceSheetSubmit]);
        if ($u == (null || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "an error has occured."];
        }

        DB::commit();

        return ["error" => false, "msg" => "success"];

    }
}
