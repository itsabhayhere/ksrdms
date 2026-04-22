<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ApiMilkPlant extends Model
{
     public function milkPlantSubmit($request){
		
			 /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $submiteInfo = DB::table('milk_plants')->insertGetId(
            [
              'dairyId' => $request->dairyId,
              'status' => $request->status,
              'plantName' => $request->plantName,
              'contactNumber' => $request->contactNumber,
              'address' => $request->address,
              'state' => $request->state,
              'city' => $request->city,
              'district' => $request->district, 
              'pinCode' => $request->pinCode, 
              'plantHeadName' => $request->plantHeadName ,
              'sex' => $request->sex,
              'email' => $request->email,
              'mobile' => $request->mobileNumber,
              'openingBalance' => $request->openingBalance,       
              'openingBalanceType' => $request->openingBalanceType,
              'created_at' => $currentTime,
            ]
        );

           $ledgerId = DB::table('ledger')->insertGetId(
                  [
                    'userId' => $submiteInfo,
                    'userType' => "milk_plants",
                    'ledgerType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );


          DB::table('milk_plants')
          ->where('id', $submiteInfo)
          ->update([
              'ledgerId' => $ledgerId,
            ]);
       
         /* add opening balance in database current balance table  */
         $userCurrentBalance = DB::table('user_current_balance')->insertGetId(
                [
                    'ledgerId' => $ledgerId,
                    'userId' => $submiteInfo,
                    'userType' => '6' ,
                    'openingBalance' => $request->openingBalance,
                    'openingBalanceType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );

        $returnSuccessArray = array("Success"=>"True","Message"=>"Milk Plant Successfully Register","plant id"=> $submiteInfo);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;
      
    }

    public function editMilkPlant($request){

    	$milkPlant = DB::table('milk_plants')
            ->where('id', $request->milkPlantId)
            ->get();

	    $submitedCity ;
        if($request->city == "--City--"){

            $submitedCity = $milkPlant[0]->city;
        }else{
            $submitedCity = $request->city ;
        }
        
          /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
        $updateReturn = DB::table('milk_plants')
            ->where('id', $request->milkPlantId)
            ->update([
                'plantName' => $request->plantName,
	            'contactNumber' => $request->contactNumber,
	            'address' => $request->address,
	            'state' => $request->state,
	            'city' => $submitedCity ,
	            'district' => $request->district, 
	            'pinCode' => $request->pinCode, 
	            'plantHeadName' => $request->plantHeadName ,
	            'sex' => $request->sex,
	            'email' => $request->email,
	            'mobile' => $request->mobileNumber,
                'updated_at' => $currentTime,
            ]);
         
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"Milk Plant Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;

    	}
	}
}
