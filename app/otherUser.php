<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class otherUser extends Model
{
    public function otherUserSubmit($request)
    {
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

       	$submiteInfo = DB::table('other_users')->insertGetId(
            [
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'roleId' => $request->selectedRole,
                'userName' => $request->otherUserName,
                'password' => $password,
                'fatherName' => $request->otherUserFatherName,
                'aadharNumber' => $request->otherUserAadharNumber,
                'userEmail' => $request->otherUserEmail,
                'gender' => $request->gender,
                'mobileNumber' => $request->otherUserMobileNumber, 
                'address' => $request->otherUserAddress, 
                'state' => $request->otherUserState ,
                'city' => $request->otherUserCity,
                'villageDistrict' => $request->otherUserVillageDistrict,
                'pincode' => $request->otherUserPincode,
                'menuId' => $request->selectedMenu,
                'created_at' => $currentTime,
			]
        );

 		$returnSuccessArray = array("Success"=>"True","Message"=>"User Successfully Register","supplier id"=> $submiteInfo);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;

    }


    public function editOtherUser($request){
        
        $other_users = DB::table('other_users')
            ->where('dairyId', $request->dairyId)
            ->get();
        
        $submitedCity ;
        if($request->supplierCity == "--City--"){

            $submitedCity = $other_users[0]->city;
        }else{
            $submitedCity = $request->city ;
        }
        
        $submitedMenu ;
        if($request->selectedMenu == ""){
            $submitedMenu = $other_users[0]->menuId;
        }else{
            $submitedMenu = $request->menuId ;
        }
        // echo $request->selectedRole ;
        // die;
          /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
        $updateReturn = DB::table('other_users')
            ->where('id', $request->otherUserId)
            ->update([
                'roleId' => $request->selectedRole,
                'userName' => $request->otherUserName,
               
                'fatherName' => $request->otherUserFatherName,
                'aadharNumber' => $request->otherUserAadharNumber,
                'userEmail' => $request->otherUserEmail,
                'gender' => $request->gender,
                'mobileNumber' => $request->otherUserMobileNumber, 
                'address' => $request->otherUserAddress, 
                'state' => $request->otherUserState ,
                'city' => $request->otherUserCity,
                'villageDistrict' => $request->otherUserVillageDistrict,
                'pincode' => $request->otherUserPincode,
                'menuId' => $submitedMenu ,
                'updated_at' => $currentTime,
            ]);
        // echo $updateReturn ;    
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"User Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;
        }
    }
}
