<?php

namespace App;

use App\Sms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class memberSetup extends Model
{
    //

    public function memberAdd($dairyId, $colMan)
    {
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for ($i = 0; $i < 8; $i++) {
            $random_int = mt_rand();
            $password .= $charset[$random_int % strlen($charset)];
        }

        $currentTime = date('Y-m-d H:i:s');

        DB::beginTransaction();

        $memberId = DB::table('member_personal_info')->insertGetId([
            'dairyId' => $dairyId,
            'status' => "true",
            'password' => $password,
            'memberPersonalCode' => request("memberCode"),
            'memberPersonalregisterDate' => date("Y-m-d"),
            'memberPersonalName' => request("name"),
            'memberPersonalFatherName' => request("fatherName") . "",
            'memberPersonalGender' => request("gender") . "",
            'memberPersonalEmail' => request("email") . "",
            'memberPersonalAadarNumber' => request("aadharNumber") . "",
            'memberPersonalMobileNumber' => request("mobileNumber"),
            'memberPersonalAddress' => request("address") . "",
            'memberPersonalState' => request("state") . "0",
            'memberPersonalCity' => request("city") . "",
            'memberPersonalDistrictVillage' => request("districtVillage") . "",
            'memberPersonalMobilePincode' => request("pin") . "",
            'created_at' => $currentTime,
        ]);

        if ($memberId == (false || null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "There is a problem, Error Code: ERROR_IN_INSERTING"];
        }

        $ledgerId = DB::table('ledger')->insertGetId([
            'userId' => $memberId,
            'dairyId' => $dairyId,
            'userType' => "4",
            'ledgerType' => request("openingBalanceType"),
            'created_at' => $currentTime,
        ]);

        if ($ledgerId == (false || null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "There is a problem, Error Code: ERROR_IN_INSERTING_2"];
        }

        $dairy_info = DB::table('member_personal_bank_info')->insertGetId([
            'memberPersonalUserId' => $memberId,
            'memberPersonalBankName' => request("bankName") . "",
            'memberPersonalAccountName' => request("accHolderName") . "",
            'memberPersonalAccountNumber' => request("accNumber") . "",
            'memberPersonalIfsc' => request("ifscCode") . "",
            'created_at' => $currentTime,
        ]);
        if ($dairy_info == (false || null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "There is a problem, Error Code: ERROR_IN_INSERTING_4"];
        }

        $dairy_info = DB::table('member_other_info')->insertGetId([
            'memberId' => $memberId,
            'alert_sms' => request('alertSms'),
            'alert_email' => request('alertEmail'),
            'created_at' => $currentTime,
        ]);
        if ($dairy_info == (false || null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "There is a problem, Error Code: ERROR_IN_INSERTING_5"];
        }

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $ledgerId,
            'transactionId' => $memberId,
            'srcDest' => $ledgerId,
            'dairyId' => $dairyId,
            'colMan' => $colMan,
            'transactionType' => 'member_personal_info',
            'remark' => "Opening balance",
            'amountType' => request("openingBalanceType"),
            'finalAmount' => request("openingBalance"),
            'created_at' => $currentTime,
        ]);

        /* add opening balance in database current balance table  */
        $userCurrentBalance = DB::table('user_current_balance')->insertGetId([
            'ledgerId' => $ledgerId,
            'userId' => $memberId,
            'userType' => '4',
            'openingBalance' => request("openingBalance"),
            'openingBalanceType' => request("openingBalanceType"),
            'created_at' => $currentTime,
        ]);

        if ($userCurrentBalance == (false || null || "") || $balanceSheetSubmit == (false || null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "There is a problem, Error Code: ERROR_IN_OPENING_BALANCE"];
        }

        $u = DB::table('member_personal_info')
            ->where('id', $memberId)->update(['ledgerId' => $ledgerId, "txnId" => $balanceSheetSubmit]);
        if ($u == (false || null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "There is a problem, Error Code: ERROR_IN_INSERTING_3"];
        }

        DB::commit();

        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $sms = new Sms();
        $smsRes = $sms->send(["message" => "You are registered in DMS - Society Code: " . $dairyInfo->society_code .
            ". id: " . request("mobileNumber") . " & password: " . $password . " login:" . request()->getHttpHost() . "",
            "numbers" => request("mobileNumber")], $dairyId);

        if (isset($smsRes["error"]) && !$smsRes['error']) {
            $msg = "SMS Sent.";
        } else {
            $msg = "Error in sending SMS.";
        }

        return ["error" => false, "msg" => "member successfully added. " . $msg];
    }
}
