<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class supplier extends Model
{
    public function supplierSubmit($request)
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

        $currentTime = date('Y-m-d H:i:s');
        $supplierCode = $request->dairyId . "S" . $request->supplierCode;

        $submiteInfo = DB::table('suppliers')->insertGetId([
            'dairyId' => $request->dairyId,
            'status' => $request->status,
            'supplierCode' => $supplierCode,
            'password' => $password,
            'supplierFirmName' => $request->supplierFirmName,
            'supplierPersonName' => $request->supplierPersonName,
            'supplierEmail' => $request->supplierEmail,
            'gender' => $request->gender,
            'supplierMobileNumber' => $request->supplierMobileNumber,
            'supplierGstin' => $request->supplierGstin,
            'supplierAddress' => $request->supplierAddress,
            'supplierState' => $request->supplierState,
            'supplierCity' => $request->supplierCity,
            'supplierVillageDistrict' => $request->supplierVillageDistrict,
            'supplierPincode' => $request->supplierPincode,
            'openingBalance' => $request->openingBalance,
            'openingBalanceType' => $request->openingBalanceType,
        ]);
        if($submiteInfo == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "An Error is occured, Error in supplier inserting."];
        }

        /* insert info in ledger table and get related related ledger id */
        $ledgerId = DB::table('ledger')->insertGetId([
                'userId' => $submiteInfo,
                'dairyId' => $request->dairyId,
                'userType' => "3",
                'ledgerType' => $request->openingBalanceType,
                'created_at' => $currentTime,
            ]);
        if($ledgerId == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "An Error is occured, Error in ledger creation."];
        }

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $ledgerId,
            'transactionId' => $submiteInfo,
            'srcDest' => $ledgerId,
            'colMan' => $colMan->userName,
            'dairyId' => $request->dairyId,
            'transactionType' => 'suppliers',
            'remark' => "Opening balance",
            'amountType' => $request->openingBalanceType,
            'finalAmount' => $request->openingBalance,
            'created_at' => $currentTime,
        ]);
        if($balanceSheetSubmit == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "An Error is occured, Error in balanceSheet."];
        }


        /* add opening balance in database current balance table  */
        $userCurrentBalance = DB::table('user_current_balance')->insertGetId([
            'ledgerId' => $ledgerId,
            'userId' => $submiteInfo,
            'userType' => '3',
            'openingBalance' => $request->openingBalance,
            'openingBalanceType' => $request->openingBalanceType,
            'created_at' => $currentTime,
        ]);
        if($userCurrentBalance == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in completing current Balance."];
        }

        $u = DB::table('suppliers')
            ->where('id', $submiteInfo)
            ->update(['ledgerId' => $ledgerId, "txnId" => $balanceSheetSubmit]);
        if($u == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in completing supplier registration."];
        }

        DB::commit();

        return ["error" => false, "msg" => "Supplier registered."];
    }

    public function editSupplier($request)
    {

        $suppliers = DB::table('suppliers')
            ->where('dairyId', $request->dairyId)
            ->get();

        $submitedCity;
        if ($request->supplierCity == "--City--") {
            $submitedCity = $suppliers[0]->supplierCity;
        } else {
            $submitedCity = $request->supplierCity;
        }

        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('Y-m-d H:i:s');

        $updateReturn = DB::table('suppliers')
            ->where('id', $request->supplierId)
            ->update([
                'supplierFirmName' => $request->supplierFirmName,
                'supplierPersonName' => $request->supplierPersonName,
                'supplierEmail' => $request->supplierEmail,
                'gender' => $request->gender,
                'supplierMobileNumber' => $request->supplierMobileNumber,
                'supplierGstin' => $request->supplierGstin,
                'supplierAddress' => $request->supplierAddress,
                'supplierState' => $request->supplierState,
                'supplierCity' => $submitedCity,
                'supplierVillageDistrict' => $request->supplierVillageDistrict,
                'supplierPincode' => $request->supplierPincode,
                'updated_at' => $currentTime,
            ]);
        // echo $updateReturn ;
        if ($updateReturn == 1) {
            return array("error" => false, "Message" => "Supplier Successfully Updated");
        }
    }

    public function supplierNewApi($dairyId, $colMan)
    {
        DB::beginTransaction();

        /* password */
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for ($i = 0; $i < 8; $i++) {
            $random_int = mt_rand();
            $password .= $charset[$random_int % strlen($charset)];
        }

        $currentTime = date('Y-m-d H:i:s');
        $supplierCode = $dairyId . "S" . request("supplierCode");

        $submiteInfo = DB::table('suppliers')->insertGetId([
            'dairyId' => $dairyId,
            'status' => "true",
            'supplierCode' => $supplierCode,
            'password' => $password,
            'supplierFirmName' => request("supplierFirmName"),
            'supplierPersonName' => request("supplierPersonName"),
            'supplierEmail' => request("email"),
            'gender' => request("gender"),
            'supplierMobileNumber' => request("mobileNumber"),
            'supplierGstin' => request("gstin"),
            'supplierAddress' => request("address"),
            'supplierState' => request("state"),
            'supplierCity' => request("city"),
            'supplierVillageDistrict' => request("villageDistrict"),
            'supplierPincode' => request("pin"),
            'openingBalance' => request("openingBalance"),
            'openingBalanceType' => request("openingBalanceType"),
        ]);
        if($submiteInfo == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in completing supplier registration."];
        }

        /* insert info in ledger table and get related related ledger id */
        $ledgerId = DB::table('ledger')->insertGetId(
            [
                'userId' => $submiteInfo,
                'dairyId' => $dairyId,
                'userType' => "3",
                'ledgerType' => request("openingBalanceType"),
                'created_at' => $currentTime,
            ]
        );
        if($ledgerId == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in completing supplier registration."];
        }

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $ledgerId,
            'transactionId' => $submiteInfo,
            'srcDest' => $ledgerId,
            'colMan' => $colMan,
            'dairyId' => $dairyId,
            'transactionType' => 'suppliers',
            'remark' => "Opening balance",
            'amountType' => request("openingBalanceType"),
            'finalAmount' => request("openingBalance"),
            'created_at' => $currentTime,
        ]);
        if($balanceSheetSubmit == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in completing supplier registration."];
        }

        /* add opening balance in database current balance table  */
        $userCurrentBalance = DB::table('user_current_balance')->insertGetId([
            'ledgerId' => $ledgerId,
            'userId' => $submiteInfo,
            'userType' => '3',
            'openingBalance' => request("openingBalance"),
            'openingBalanceType' => request("openingBalanceType"),
            'created_at' => $currentTime,
        ]);
        if($userCurrentBalance == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in completing supplier registration."];
        }

        $u = DB::table('suppliers')
            ->where('id', $submiteInfo)
            ->update(['ledgerId' => $ledgerId, "txnId" => $balanceSheetSubmit]);
        if($u == (null || false)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in completing supplier registration."];
        }

        DB::commit();

        return ["error" => true, "msg" => "success"];

    }
}
