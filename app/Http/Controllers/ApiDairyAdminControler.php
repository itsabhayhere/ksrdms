<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Session\TokenMismatchException;  

class ApiDairyAdminControler extends Controller
{
    
    /* get all state list  */
    public function dairyAdminCreate(){
    	$states = DB::table('states')->get();
		return $states ;
    }

    /* get city by state id from database */
    public function CityByStateId(Request $request){
		$cities = DB::table('city')->get()->where('state_id', $request->state_id);
		$cityArray = [];
		foreach($cities as $cities_data){
			$cityArray[] = $cities_data ;
		}
		return response()->json($cityArray);
    }

    public function getcsrf(){
        return csrf_token() ;
    }

    public function dairyRegister(Request $request){
        
        /*society code validation */
        $dairySocietyCode = DB::table('dairy_info')
                                    ->where('society_code', $request->society_code)
                                    ->get();
        if(!(empty($dairySocietyCode[0]))){
           $retrunArray = array("Success"=>"False","message"=>"This society code is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            die;
        }

       /*mobile number validation */
        $numberValidation = DB::table('dairy_propritor_info')
                                    ->where('PropritorMobile', $request->PropritorMobile )
                                    ->get();
      
        /* mobile number already being used */
        if(!(empty($numberValidation[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This number is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            die;
        }


        /*  random password */
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for($i = 0; $i < 8; $i++)
        {
          $random_int = mt_rand();
          $password .= $charset[$random_int % strlen($charset)];
        }



        /* submit dairy infomation */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $dairy_info = DB::table('dairy_info')->insertGetId(
              [
                'createBySuperAdmin' => $request->createBySuperAdmin,
                'society_code' => $request->society_code,
                'password' => $password,
                'society_name' => $request->society_name,
                'dairyAddress' => $request->dairyAddress,
                'state' => $request->state,
                'city' => $request->city,
                'district' => $request->district, 
                'pincode' => $request->pincode, 
                'status' => "true",
                'rateCard' => $request->rateCardType,
                'created_at' => $currentTime,

              ]
        );

     /* get current dairy setup id */
        $dairy_info = DB::table('dairy_info')
                ->where('society_name', $request->society_name)
                ->orderBy('id', 'desc')
                ->limit(1)
                ->get();
        
        $currentAdiryStupId = $dairy_info[0]->id ;

         /* insert info in ledger table and get related related ledger id */
            $ledgerId = DB::table('ledger')->insertGetId(
                  [
                    'userId' => $currentAdiryStupId,
                    'dairyId' => $currentAdiryStupId ,
                    'userType' => "1",
                    'ledgerType' => "credit",
                    'created_at' => $currentTime,
                ]
            );

           DB::table('dairy_info')
            ->where('id', $currentAdiryStupId)
            ->update([
                'ledgerId' => $ledgerId,
            ]);
        
        /* submit dairy infomation */
        $dairy_info = DB::table('dairy_propritor_info')->insertGetId(
            [
                'dairyId' => $currentAdiryStupId,
                'password' => $password,
                'dairyPropritorName' => $request->dairyPropritorName,
                'PropritorMobile' => $request->PropritorMobile,
                'dairyPropritorAddress' => $request->dairyPropritorAddress,
                'dairyPropritorEmail' => $request->dairyPropritorEmail,
                'dairyPropritorState' => $request->dairyPropritorState,
                'dairyPropritorCity' => $request->dairyPropritorCity, 
                'dairyPropritorDistrict' => $request->dairyPropritorDistrict, 
                'dairyPropritorPincode' => $request->dairyPropritorPincode, 
                'created_at' => $currentTime,
            ]
        );


          /* add opening balance in database current balance table  */
            $userCurrentBalance = DB::table('user_current_balance')->insertGetId(
                [
                    'ledgerId' => $ledgerId,
                    'userId' => $currentAdiryStupId,
                    'userType' => '1' ,
                    'openingBalance' => $request->openingBalance,
                    'openingBalanceType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );
      
        $returnSuccessArray = array("Success"=>"True","Message"=>"Dairy Successfully Register","Dairy_id"=>  $currentAdiryStupId);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;
    
    }

    /* get Edit Dairy info */
    public function editDairy(Request $request){

         /*email validation */
        $dairyPropritorInfoEmail = DB::table('dairy_propritor_info')
                                    ->where('dairyPropritorEmail', $request->dairyPropritorEmail)
                                    ->get();

        /* email valid fomat */
        if (!(filter_var($request->dairyPropritorEmail, FILTER_VALIDATE_EMAIL))) {
            $retrunArray = array("Success"=>"False","message"=>"This email is not valid");
            $returnJson = json_encode($retrunArray);
            return $returnJson ;
            die;        
        }
        
        /* email already being used */
        if(!(empty($dairyPropritorInfoEmail[0]))){
            if($dairyPropritorInfoEmail[0] != $request->dairyId){
                $retrunArray = array("Success"=>"False","message"=>"This email is already being used");
                $returnJson = json_encode($retrunArray);
                return $returnJson  ;
                die;    
            }
        }

        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
        DB::table('dairy_info')
            ->where('id', $request->dairyId)
            ->update([
                'society_name' => $request->society_name,
                'dairyAddress' => $request->dairyAddress,
                'state' => $request->state,
                'city' => $request->city,
                'district' => $request->district, 
                'pincode' => $request->pincode, 
                'status' => "active",
                'updated_at' => $currentTime,
            ]);

        DB::table('dairy_propritor_info')
        ->where('dairyId', $request->dairyId)
        ->update([
            'dairyPropritorName' => $request->dairyPropritorName,
            'PropritorMobile' => $request->PropritorMobile,
            'dairyPropritorAddress' => $request->dairyPropritorAddress,
            'dairyPropritorEmail' => $request->dairyPropritorEmail,
            'dairyPropritorState' => $request->dairyPropritorState,
            'dairyPropritorCity' => $request->dairyPropritorCity, 
            'dairyPropritorDistrict' => $request->dairyPropritorDistrict, 
            'dairyPropritorPincode' => $request->dairyPropritorPincode, 
            'updated_at' => $currentTime,
         ]);

        $returnSuccessArray = array("Success"=>"True","Message"=>"Dairy Successfully Updated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;
    }

    public function getDairyInfo(Request $request){
        $dairy_info = DB::table('dairy_info')
            ->where('id', $request->dairyId)
            ->get();
        $dairyInfo = json_decode($dairy_info);

        $dairy_propritor_info = DB::table('dairy_propritor_info')
            ->where('dairyId', $request->dairyId)
            ->get();
        $dairyPropritorInfo = json_decode($dairy_propritor_info);
        /*
        $shift_timing = DB::table('shift_timing')
            ->where('shiftDairyId', $request->dairyId)
            ->get();
        $shiftTiming = json_decode($shift_timing);*/

        // $returnData = array_merge($dairyInfo,$dairyPropritorInfo,$shiftTiming);
        $returnData = array_merge($dairyInfo,$dairyPropritorInfo);
        return json_encode($returnData);
    
    }

      /* Register dairy member api */
    public function registerMember(Request $request){

        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');

        /*number validation */
           $memberNumber = DB::table('member_personal_info')
                                    ->where('memberPersonalMobileNumber', $request->memberPersonalMobileNumber )
                                    ->get();

    
        /* number already being used */
        if(!(empty($memberNumber[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This number is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            die;
        }

         /*name validation */
             $memberName = DB::table('member_personal_info')
                                    ->where('memberPersonalMobileNumber', $request->memberPersonalMobileNumber )
                                    ->get();

    
        /* name already being used */
        if(!(empty($memberName[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This name is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            die;
        }

         /*aadhar number validation */
              $memberAadharNumber = DB::table('member_personal_info')
                                    ->where('memberPersonalAadarNumber', $request->memberPersonalAadarNumber )
                                    ->get();

    
        /* aadhar number already being used */
        if(!(empty($memberAadharNumber[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This aadhar number is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            die;
        }

        /* check member code */
        $memberPersonalCode = DB::table('member_personal_info')
            ->where('memberPersonalCode', $request->memberPersonalCode)
            ->get();

        if(!(empty($memberPersonalCode[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This Code is already used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            die;
        }

            /*  random password */
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for($i = 0; $i < 8; $i++)
        {
          $random_int = mt_rand();
          $password .= $charset[$random_int % strlen($charset)];
        }
        
        $memberId = DB::table('member_personal_info')->insertGetId(
              [
                'dairyId' => $request->dairyId,
                'status' => "ture",
                'memberPersonalCode' => $request->memberPersonalCode,
                'password' => $password,
                'memberPersonalregisterDate' => $request->memberPersonalregisterDate,
                'memberPersonalName' => $request->memberPersonalName,
                'memberPersonalFatherName' => $request->memberPersonalFatherName,
                'memberPersonalGender' => $request->memberPersonalGender,
                'memberPersonalEmail' => $request->memberPersonalEmail,
                'memberPersonalAadarNumber' => $request->memberPersonalAadarNumber,
                'memberPersonalMobileNumber' => $request->memberPersonalMobileNumber,
                'memberPersonalAddress' => $request->memberPersonalAddress,
                'memberPersonalState' => $request->memberPersonalState,
                'memberPersonalCity' => $request->memberPersonalCity,
                'memberPersonalDistrictVillage' => $request->memberPersonalDistrictVillage,
                'memberPersonalMobilePincode' => $request->memberPersonalMobilePincode,
                'created_at' => $currentTime,
            ]
        );

            $ledgerId = DB::table('ledger')->insertGetId(
                        [
                            'userId' => $memberId,
                            'dairyId' => $request->dairyId,
                            'userType' => "4",
                            'ledgerType' => $request->openingBalanceType,
                            'created_at' => $currentTime,
                        ]
                    );

                DB::table('member_personal_info')
                ->where('id', $memberId)
                ->update([
                    'ledgerId' => $ledgerId,
                ]);

        $dairy_info = DB::table('member_personal_info')
                ->where('memberPersonalEmail', $request->memberPersonalEmail)
                ->get();
        
        $currentMemberInfo = $dairy_info[0]->id ;

        $dairy_info = DB::table('member_personal_bank_info')->insertGetId(
              [
                'memberPersonalUserId' => $currentMemberInfo,
                'memberPersonalBankName' => $request->memberBankName,
                'memberPersonalAccountName' => $request->memberBankNumber,
                'memberPersonalIfsc' => $request->memberBankIfsc,
                'memberPersonalBranchCode' => $request->memberBankBranchCode,
                'created_at' => $currentTime,
            ]
        );

        $dairy_info = DB::table('member_other_info')->insertGetId(
              [
                'memberId' => $currentMemberInfo,
                'milkeType' => $request->milkType,
                'alert_print_slip' => $request->aleryPrintSlip,
                'alert_sms' => $request->alerySms,
                'alert_email' => $request->aleryEmail,
                'created_at' => $currentTime,
            ]
        );

        /* add opening balance in database current balance table  */
         $userCurrentBalance = DB::table('user_current_balance')->insertGetId(
                [
                    'ledgerId' => $ledgerId,
                    'userId' => $memberId,
                    'userType' => '4' ,
                    'openingBalance' => $request->memberPersonalOpeningBalance,
                    'openingBalanceType' => $request->openingBalanceType,
                    'created_at' => $currentTime,
                ]
            );

        $returnSuccessArray = array("Success"=>"True","Message"=>"Member Successfully Added");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;

    }

    public function getAllMember(Request $request){

        $member_info = DB::table('member_personal_info')
            ->where('dairyId', $request->dairyId)
            ->get();
        $memberInfo = json_decode($member_info);
        $returnArraydata = [];
 
        foreach($memberInfo as $memberInfoData){
        $memberAllInfo = array();

        /* get member other info */
        $member_other_info = DB::table('member_other_info')
            ->where('memberId', $memberInfoData->id)
            ->get();
        $memberOtherInfo = json_decode($member_other_info);

        /* get member bank info */
        $member_personal_bank_info = DB::table('member_personal_bank_info')
            ->where('memberPersonalUserId', $memberInfoData->id)
            ->get();
        $memberPersonalBankInfo = json_decode($member_personal_bank_info);

        /* array for return form foreach loop */
        $memberAllInfo  = ['member personal info' => $memberInfoData,'member other info' => $memberOtherInfo[0],'member bank info'=> $memberPersonalBankInfo[0] ];
        $returnArraydata[]= $memberAllInfo;

      
        }

        
        return json_encode($returnArraydata);
    }

    public function editMember(Request $request){

              /*email validation */
        $memberPersonalEmail = DB::table('member_personal_info')
                                    ->where('memberPersonalEmail', $request->memberPersonalEmail)
                                    ->get();

        /* email valid fomat */
        if (!(filter_var($request->memberPersonalEmail, FILTER_VALIDATE_EMAIL))) {
            $retrunArray = array("Success"=>"False","message"=>"This email is not valid");
            $returnJson = json_encode($retrunArray);
            return $returnJson ;
            die;        
        }
        /* email already being used */
        if(!(empty($memberPersonalEmail[0]))){
            if($memberPersonalEmail[0] != $request->memberId){
                $retrunArray = array("Success"=>"False","message"=>"This email is already being used");
                $returnJson = json_encode($retrunArray);
                return $returnJson  ;
                die;    
            }
        }

        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
        DB::table('member_personal_info')
            ->where('id', $request->memberId)
            ->update([
                'memberPersonalregisterDate' => $request->memberPersonalregisterDate,
                'memberPersonalName' => $request->memberPersonalName,
                'memberPersonalFatherName' => $request->memberPersonalFatherName,
                'memberPersonalGender' => $request->memberPersonalGender,
                'memberPersonalEmail' => $request->memberPersonalEmail,
                'memberPersonalAadarNumber' => $request->memberPersonalAadarNumber,
                'memberPersonalMobileNumber' => $request->memberPersonalMobileNumber,
                'memberPersonalAddress' => $request->memberPersonalAddress,
                'memberPersonalState' => $request->memberPersonalState,
                'memberPersonalCity' => $request->memberPersonalCity,
                'memberPersonalDistrictVillage' => $request->memberPersonalDistrictVillage,
                'memberPersonalMobilePincode' => $request->memberPersonalMobilePincode,
                'updated_at' => $currentTime,
            ]);

        DB::table('member_personal_bank_info')
        ->where('memberPersonalUserId', $request->memberId)
        ->update([
            'memberPersonalBankName' => $request->memberBankName,
            'memberPersonalAccountName' => $request->memberBankNumber,
            'memberPersonalIfsc' => $request->memberBankIfsc,
            'memberPersonalBranchCode' => $request->memberBankBranchCode,
            'memberPersonalOpeningBalance' => $request->memberPersonalOpeningBalance,
            'openingBalanceType' => $request->openingBalanceType,
            'updated_at' => $currentTime,
         ]);

        DB::table('member_other_info')
        ->where('memberId', $request->memberId)
        ->update([
            'milkeType' => $request->milkType,
            'alert_print_slip' => $request->aleryPrintSlip,
            'alert_sms' => $request->alerySms,
            'alert_email' => $request->aleryEmail,
            'updated_at' => $currentTime,
       ]);

        $returnSuccessArray = array("Success"=>"True","Message"=>"Dairy Successfully Updated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;

    }

    /* delete member */
    public function deleteMember(Request $request){
        DB::table('member_personal_info')
        ->where('id', $request->memberId)
        ->update([
            'status' => "false",
       ]);

        $returnSuccessArray = array("Success"=>"True","Message"=>"Member Successfully Delated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;
    }

}
