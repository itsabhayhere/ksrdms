<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UtilitySetup extends Model
{
    public function utilitySubmit($dairyId){

        $currentTime =  date('Y-m-d H:i:s');


        $mUtility = DB::table('utility_setup')
                ->where('dairyId', $dairyId)
                ->where('status','true')
                ->where("machinType", "milk")
                ->get()->first();

        $wUtility = DB::table('utility_setup')
                ->where('dairyId', $dairyId)
                ->where('status','true')
                ->where("machinType", "weight")
                ->get()->first();

        $md = ['dairyId'           => $dairyId,
                'status'            => "true",
                'machinType'        => "milk",
                'communicationPort' => request("milkComPort"),
                'maxSpeed'          => request("milkMaxSpeed"),
                'echo'              => "off",
                'connectionPerferenceDataBits'  => request("mDataBits"),
                'connectionPerferenceParity'    => request("mParity"),
                'connectionPerferenceStopBits'  => request("mStopBits"),
                'flowControl'           => "None",
                'weightMode'            => "FAT",
                'weightMode_auto_tare'  => "",
                'weightMode_no_training' => "",
                'weightMode_weight_in_doublke_decimal' => "",
                'weightMode_write_in'   => "",
                'isActive'              => request("milkUtilityActive")?request("milkUtilityActive"):0,
                'created_at'            => $currentTime];
        
        if($mUtility == null){
            $submiteInfo = DB::table('utility_setup')->insertGetId($md);
        }else{
            $submiteInfo = DB::table('utility_setup')->where(["dairyId" => $dairyId, "machinType" => "milk"])->update($md);
        }

        $wd = [
            'dairyId'           => $dairyId,
            'status'            => "true",
            'machinType'        => "weight",
            'communicationPort' => request("weightComPort"),
            'maxSpeed'          => request("weightMaxSpeed"),
            'echo'              => "off",
            'connectionPerferenceDataBits'  => request("wDataBits"),
            'connectionPerferenceParity'    => request("wParity"),
            'connectionPerferenceStopBits'  => request("wStopBits"),
            'flowControl'           => "None",
            'weightMode'            => request("weightMode"),
            'weightMode_auto_tare'  => "",
            'weightMode_no_training' => "",
            'weightMode_weight_in_doublke_decimal' => "",
            'weightMode_write_in'   => "",
            'isActive'              => request("weightUtilityActive")?request("weightUtilityActive"):0,
            'decimal_digit'         => request('wDecimal_digit'),
            'created_at'            => $currentTime,];

        if($wUtility == null){
            $submiteInfo = DB::table('utility_setup')->insertGetId($wd);
        }else{
            $submiteInfo = DB::table('utility_setup')->where(["dairyId" => $dairyId, "machinType" => "weight"])->update($wd);
        }

        return ["error" => false, "msg" => "Port setting updated."];
    }

    public function portEditSubmit($request){

        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
        $updateReturn = DB::table('utility_setup')
            ->where('id', $request->utilityId)
            ->update([
                'machinType' => $request->machinType,
                'communicationPort' => $request->communicationPort,
                'maxSpeed' => $request->maxSpeed,
                'echo' => $request->echo,
                'connectionPerferenceDataBits' => $request->connectionPerferenceDataBits,
                'connectionPerferenceParity' => $request->connectionPerferenceParity, 
                'connectionPerferenceStopBits' => $request->connectionPerferenceStopBits, 
                'flowControl' => $request->flowControl ,
                'weightMode' => $request->weightMode,
                'weightMode_auto_tare' => $request->weightMode_auto_tare,
                'weightMode_no_training' => $request->weightMode_no_training,
                'weightMode_weight_in_doublke_decimal' => $request->weightMode_weight_in_doublke_decimal,
                'weightMode_write_in' => $request->weightMode_write_in,
                'updated_at' => $currentTime,
            ]);
         
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"Utility Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;
        }

    }
}
