<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ApiSupplier extends Model
{
    public function supplierSubmit($request){

        /* password */
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        
        for($i = 0; $i < 8; $i++)
        {
            $random_int = mt_rand();
            $password .= $charset[$random_int % strlen($charset)];
        }

           /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
		
        $submiteInfo = DB::table('suppliers')->insertGetId(
            [
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'supplierCode' => $request->supplierCode,
                'password' => $password,
                'supplierFirmName' => $request->supplierFirmName,
                'supplierPersonName' => $request->supplierPersonName,
                'supplierEmail' => $request->supplierEmail,
                'gender' => $request->gender,
                'supplierMobileNumber' => $request->supplierMobileNumber, 
                'supplierGstin' => $request->supplierGstin, 
                'supplierAddress' => $request->supplierAddress ,
                'supplierState' => $request->supplierState,
                'supplierCity' => $request->supplierCity,
                'supplierVillageDistrict' => $request->supplierVillageDistrict,
                'supplierPincode' => $request->supplierPincode,
                // 'openingBalance' => $request->openingBalance,
                // 'openingBalanceType' => $request->openingBalanceType,
            ]
        );

        /* insert info in ledger table and get related related ledger id */
            $ledgerId = DB::table('ledger')->insertGetId(
                  [
                    'userId' => $submiteInfo,
                    'dairyId' => $request->dairyId,
                    'userType' => "3",
                    'ledgerType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );

           DB::table('suppliers')
            ->where('id', $submiteInfo)
            ->update([
                'ledgerId' => $ledgerId,
            ]);



   /* add opening balance in database current balance table  */
         $userCurrentBalance = DB::table('user_current_balance')->insertGetId(
                [
                    'ledgerId' => $ledgerId,
                    'userId' => $submiteInfo,
                    'userType' => '3' ,
                    'openingBalance' => $request->openingBalance,
                    'openingBalanceType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );
       
        $returnSuccessArray = array("Success"=>"True","Message"=>"Supplier Successfully Register","supplier id"=> $submiteInfo);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ; 
    }
    public function editSupplier($request){
          /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
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
                'supplierCity' => $request->supplierCity,
                'supplierVillageDistrict' => $request->supplierVillageDistrict,
                'supplierPincode' => $request->supplierPincode,
                'updated_at' => $currentTime,
            ]);
        // echo $updateReturn ;    
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"Supplier Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;
        }
    }
}
