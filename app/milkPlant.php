<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class milkPlant extends Model
{
    public function milkPlantSubmit(){
        
        // dd(request()->all()); exit;

        DB::beginTransaction();

        $password = $this->newPassword();
        $currentTime =  date('Y-m-d H:i:s');

        $ex = DB::table('milk_plants')->where(["contactNumber" => intval(request('contactNumber'), 10)])->get()->first();

        $exhead = DB::table('milk_plant_head')->where(["mobile" => intval(request('mobileNumber'), 10)])->get()->first();

        $exheade = DB::table('milk_plant_head')->where(["email" => request('email')])->get()->first();

        if($exhead!=null ){
            DB::rollback();
            return ["error" => true, "msg" => "Milk Plant head already registered with same Mobile number."];
        }
        if($exheade!=null){
            DB::rollback();
            return ["error" => true, "msg" => "Milk Plant head already registered with same Email."];
        }
        if($ex!=null){
            DB::rollback();
            return ["error" => true, "msg" => "Milk Plant already registered with same Mobile number."];
        }

        $parentPlantId = null;
        $isMainPlant = 0;
        if(!request('isMainPlant')){
            $isMainPlant = 0;
            if(request('mainPlant') == null){
                DB::rollback();
                return ["error" => true, "msg" => "Select Main plant."];    
            }else{
                $mp = DB::table('milk_plants')->where(["id" => request('mainPlant')])->get()->first();
                if($mp && $mp->isMainPlant == 0){
                    return ["error" => true, "msg" => "Selected plant is not Main plant."];
                }
                $parentPlantId = $mp->id;
            }
        }else{
            $isMainPlant = 1;
            $parentPlantId == null;
        }

        $submiteInfo = DB::table('milk_plants')->insertGetId([
            'status' => "true",
            'isMainPlant' => $isMainPlant,
            'parentPlantId' => $parentPlantId,
            'plantName' => request('plantName'),
            'contactNumber' => intval(request('contactNumber'), 10),
            'address' => request('address'),
            'state' => request('state'),
            'city' => request('city'),
            'pinCode' => request('pinCode'),
            'plantCode' => "",
            // 'openingBalance' => request('openingBalance'),
            // 'openingBalanceType' => request('openingBalanceType'),
            'created_at' => $currentTime,
        ]);
        if($submiteInfo == (false || null)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in milk plant registration."];
        }

        $head = DB::table('milk_plant_head')->insertGetId([
            'status'    => "true",
            'plantId'   => $submiteInfo,
            'headName'  => request('plantHeadName'),
            'gender'    => request('sex'),
            'email'     => request('email'),
            'mobile'    => intval(request('mobileNumber'), 10),
            'password'  => $password,
            'created_at'=> $currentTime
        ]);
        if(!$head){
            DB::rollback();
            return ["error" => true, "msg" => "Error in milk plant head registration."];
        }

        $ledgerId = DB::table('ledger')->insertGetId([
            'userId' => $submiteInfo,
            'dairyId' => "0",
            'userType' => "6",
            'ledgerType' => 'credit',
            'created_at' => $currentTime,
        ]);
        if($ledgerId == (false || null)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in milk plant registration."];
        }

        $plantCode = "PL".$submiteInfo;

        /* add opening balance in database current balance table  */
        $userCurrentBalance = DB::table('user_current_balance')->insertGetId([
            'ledgerId' => $ledgerId,
            'userId' => $submiteInfo,
            'userType' => '6' ,
            'openingBalance' => 0,
            'openingBalanceType' => 'credit',
            'created_at' => $currentTime,
        ]);
        if($userCurrentBalance == (false || null)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in milk plant registration complete."];
        }

        $u = DB::table('milk_plants')
            ->where('id', $submiteInfo)
            ->update(['ledgerId' => $ledgerId, 'plantCode' => $plantCode]);
        if($u == (false || null)){
            DB::rollback();
            return ["error" => true, "msg" => "Error in milk plant registration complete."];
        }
        
        DB::commit();
        return ["error" => false, "msg" => "Milk plant registered successfully.", 'pass' => $password];
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
    
    public function milkPlantAddRequest()
    {
        $dairy = DB::table('dairy_info')->where(["id" => session()->get('loginUserInfo')->dairyId])->get()->first();
        if($dairy == null){
            Session::flash('msg', "Please login again");
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if(!request('plant')){
            Session::flash('msg', "Select Milkplant.");
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
        $pl = DB::table('milk_plants')->where(["id" => request('plant')])->get()->first();
        if($pl == null){
            Session::flash('msg', "Milk plant not found.");
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $res = DB::table('plantdairymap')->where(["plantId" => request('plant'), "dairyid" => $dairy->id])->get()->first();
        if($res && $res->status == "true"){
            Session::flash('msg', "Milk plant already addedd.");
            Session::flash('alert-class', 'alert-danger');
            return false;
        }elseif($res && $res->status == "false"){
            $update = DB::table('plantdairymap')->where(["plantId" => request('plant'), "dairyid" => $dairy->id])
                        ->update(["status" => "true"]);
            if($update){
                Session::flash('msg', "Milk Plant successfully added.");
                Session::flash('alert-class', 'alert-success');
                return true;
            }else{
                Session::flash('msg', "There is an error occured.");
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        $res = DB::table('plantdairymap')->insertGetId([
            "plantId"   => request("plant"),
            "dairyId"   => $dairy->id,
            "status"    => "true",
            "isActivated" => 0
        ]);

        if($res){
            $this->makePlantAddNotification($pl, $dairy);

            Session::flash('msg', "Milk Plant successfully added.");
            Session::flash('alert-class', 'alert-success');
            return true;
        }else{
            Session::flash('msg', "There is an error");
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
    }


    public function makePlantAddNotification($pl, $dairy)
    {
        $data = [
            "ledgerId"      => $pl->ledgerId,
            "notification"  => "Verify new dairy - <b>".$dairy->dairyName." (Society Code: ".$dairy->society_code.")</b><br/><a href='".url('plant/requestToAdd')."'>Show</a>",
            "created_at"    => date("Y-m-d H:i:s")
        ];

        return DB::table('notifications')->insertGetId($data);
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
