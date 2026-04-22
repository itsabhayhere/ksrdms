<?php

namespace App;

use App\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class superAdmin extends Model
{
    //
    public function newDairySetup($req)
    {
        DB::beginTransaction();

        $password = $this->newPassword();
        $currentTime = date('Y-m-d H:i:s');

        $pp = DB::table("subscription_plan")->where("id", request("pricePlanId"))->get()->first();
        if($pp==null){
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: PLAN_NOT_FOUND"];
        }

        $dexist = DB::table("dairy_info")->where(["mobile" => $req->mobile])->get()->first();
        if($dexist != null){
            DB::rollback();
            return ["error" => true, "msg" => "Dairy with same mobile, already exist."];
        }

        $pexist = DB::table("dairy_propritor_info")->where(["PropritorMobile" => $req->owmobile])->get()->first();
        if($pexist != null){
            DB::rollback();
            return ["error" => true, "msg" => "Propritor mobile no. already registered."];
        }

        $dairyId = DB::table('dairy_info')->insertGetId([
            'createBySuperAdmin' => $req->createBySuperAdmin,
            'dairyName' => $req->name,
            'society_code' => $req->code,
            'mobile' => $req->mobile,
            'state' => $req->owstate,
            'city' => $req->owdistrict,
            'pincode' => $req->owpin,
            'cash_in_hand' => '0.0',
            'remainingSms' => (int)$pp->noOfSms+1,
            'status' => "ture",
            'firstTimeBalanceUpdated' => "false",
            'created_at' => $currentTime,
        ]);
        if ($dairyId == (false || null)) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: DAIRY_INSERT_ERROR"];
        }

        $colMan = DB::table('other_users')->insertGetId([
            'dairyId' => $dairyId,
            'status' => true,
            'roleId' => "1",
            'userName' => "DAIRYADMIN",
            'userEmail' => $req->owemail,
            'mobileNumber' => $req->owmobile,
            'password' => $password,
            'created_at' => $currentTime,
        ]);
        if ($colMan == (false || null)) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: COLLECTION_MANAGER_ERROR"];
        }

        /* insert info in ledger table and get related ledger id */
        $ledgerId = DB::table('ledger')->insertGetId([
            'userId' => $dairyId,
            'dairyId' => $dairyId,
            'userType' => "1",
            'ledgerType' => "credit",
            'created_at' => $currentTime,
        ]);
        if ($ledgerId == (false || null)) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: LEDGER_CREATION_ERROR"];
        }

        $dairyUpdate = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->update([
                'ledgerId' => $ledgerId,
            ]);
        if ($dairyUpdate == (false || null)) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: LEDGER_UPDATION_FAILED"];
        }

        /* submit dairy infomation */
        $dairyPropritorId = DB::table('dairy_propritor_info')->insertGetId([
            'dairyId' => $dairyId,
            'dairyPropritorName' => $req->owname,
            'password' => $password,
            'PropritorMobile' => $req->owmobile,
            'dairyPropritorAddress' => $req->owaddress,
            'dairyPropritorEmail' => $req->owemail,
            'dairyPropritorState' => $req->owstate,
            'dairyPropritorCity' => $req->owdistrict,
            'dairyPropritorDistrict' => $req->owdistrict,
            'dairyPropritorPincode' => $req->owpin,
            'created_at' => $currentTime,
        ]);
        if ($dairyPropritorId == (false || null)) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: DAIRY_PROPRITOR_ERROR"];
        }
        /* add opening balance in database current balance table  */
        $ubId = DB::table('user_current_balance')->insertGetId([
            'ledgerId' => $ledgerId,
            'userId' => $dairyId,
            'userType' => '1',
            'openingBalance' => 0,
            'openingBalanceType' => 'debit',
            'created_at' => $currentTime,
        ]);
        if ($ubId == (false || null)) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: CURRENT_BALANCE_ERROR"];
        }

        $cashCust = [
            'dairyId' => $dairyId,
            'status' => "true",
            'customerCode' => "1",
            'customerName' => "cash",
            'gender' => "female",
            'customerEmail' => "",
            'customerMobileNumber' => "0000000000",
            'customerAddress' => "No Address, Only for cash sales.",
            'customerState' => "",
            'customerCity' => "",
            'customerVillageDistrict' => "",
            'customerPincode' => "",
            'openingBalanceType' => "credit",
            'customerOpeningBalance' => 0,
        ];

        $submitClass = new Customer();
        $cashCust = $submitClass->CustomerSubmit((object) $cashCust);

        if (!$cashCust) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: CASH_CUSTOMER_ERROR"];
        }

        /********Subscribe********/
        $sp = db::table("subscription_plan")->where(["id" => request("pricePlanId"), "status" => "true"])->get()->first();
        if (!$sp) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: SUBSCRIBE_PLAN_ERROR"];
        }

        $expDate = date("Y-m-d H:i:s", time());
        $trialEndDate = date("Y-m-d H:i:s", strtotime("+1 month"));

        $subscribe = DB::table("subscribe")->insertGetId([
            "dairyId"       => $dairyId,
            "pricePlanId"   => request("pricePlanId"),
            "paymentId"     => 0,
            "planType"      => request("priceMonthlyOrYearly"),
            "dateOfSubscribe" => date("Y-m-d H:i:s"),
            // "dateOfPayment" => date("Y-m-d H:i:s"),
            "expiryDate"    => $expDate,
            "isActivated"   => 1,
            "isPaymentDone" => 0,
            "amount"        => 0,
            "trialEndDate"  => $trialEndDate,
            "created_at"    => date("Y-m-d H:i:s"),
        ]);

        if (!$subscribe) {
            DB::rollback();
            return ["error" => true, "msg" => "An error has occured, Error Code: SUBSCRIBE_ERROR"];
        }

        DB::commit();
        return ["error" => false,
            "pass" => $password,
            "id" => $req->owmobile,
            "dairyId" => $dairyId,
            "msg" => "Dairy successfully registered."];
    }

    public function newPassword()
    {
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for ($i = 0; $i < 8; $i++) {
            $random_int = mt_rand();
            $password .= $charset[$random_int % strlen($charset)];
        }
        // echo $password, "n";
        return $password;
    }
}
