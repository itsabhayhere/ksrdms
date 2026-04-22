<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ApiCustomer extends Model
{
        public function customerSubmit($request){
             /* password */
              $password = "";
              $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
              
              for($i = 0; $i < 8; $i++)
              {
                  $random_int = mt_rand();
                  $password .= $charset[$random_int % strlen($charset)];
              }
		
         date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
 
       $submiteInfo = DB::table('customer')->insertGetId(
              [
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'customerCode' => $request->customerCode,
                'password' => $password,
                'customerName' => $request->customerName,
                'gender' => $request->gender,
                'customerEmail' => $request->customerEmail,
                'customerMobileNumber' => $request->customerMobileNumber,
                'customerAddress' => $request->customerAddress, 
                'customerState' => $request->customerState, 
                'customerCity' => $request->customerCity ,
                'customerVillageDistrict' => $request->customerVillageDistrict,
                'customerPincode' => $request->customerPincode,
                'openingBalance' => $request->customerOpeningBalance,       
                'openingBalanceType' => $request->openingBalanceType,
 				'created_at' => $currentTime,
              ]
        );

            $ledgerId = DB::table('ledger')->insertGetId(
                  [
                    'userId' => $submiteInfo,
                    'dairyId' => $request->dairyId,
                    'userType' => "2",
                    'ledgerType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );


            DB::table('customer')
            ->where('id', $submiteInfo)
            ->update([
                'ledgerId' => $ledgerId,
            ]);

              /* add opening balance in database current balance table  */
         $userCurrentBalance = DB::table('user_current_balance')->insertGetId(
                [
                    'ledgerId' => $ledgerId,
                    'userId' => $submiteInfo,
                    'userType' => '2' ,
                    'openingBalance' => $request->customerOpeningBalance,
                    'openingBalanceType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );
       
        $returnSuccessArray = array("Success"=>"True","Message"=>"Customer Successfully Register","Customer id"=> $submiteInfo);
        // $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessArray  ; 
    }
    public function editCustomer($request){
        
          
        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
        $updateReturn = DB::table('customer')
            ->where('id', $request->customerId)
            ->update([
                'customerName' => $request->customerName,
                'gender' => $request->gender,
                'customerEmail' => $request->customerEmail,
                'customerMobileNumber' => $request->customerMobileNumber,
                'customerAddress' => $request->customerAddress, 
                'customerState' => $request->customerState, 
                'customerCity' =>  $request->customerCity ,
                'customerVillageDistrict' => $request->customerVillageDistrict,
                'customerPincode' => $request->customerPincode,
                'updated_at' => $currentTime,
            ]);
         
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"Customer Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;
        }
    }
}
