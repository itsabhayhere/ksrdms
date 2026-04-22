<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sms extends Model
{
    //
    public $fields = array(
        "sender_id" => "KSRSER",
        "message" => "This is Test message",
        "language" => "english",
        "route" => "4",
        "mobiles" => "8962239013,7773854335",
        // "flash" => "1",
    );

    public function saveToQueue($data, $dairyId)
    {

    
        if(!isset($data['message']) || $data['message'] == null){
            return ["error" => true, "msg" => "Message can't be empty."];
        }
        
        if(!isset($data['numbers']) || $data['numbers'] == null){
            return ["error" => true, "msg" => "Mobile number can't be empty."];
        }
        
        ///////Check for SMS Balance
        if($dairyId == null){
            $dairyId = session()->has("loginUserInfo")?session()->has("loginUserInfo")->dairyId:"";
        }

    
        $dairy = DB::table("dairy_info")->where(["id" => $dairyId])->get    ()->first();

        if($dairy==null){
            return ["error" => true, "msg" => "SMS not sent, SMS balance is low, contact Dairy Super Admin"];
        }
        if($dairy->remainingSms < 1){
            return ["error" => true, "msg" => "SMS not sent, SMS balance is low, contact Dairy Super Admin"];
        }

        $i = DB::table("queuesms")->insertGetId([
            "dairyId" => $dairyId,
            "to_"     => $data['numbers'],
            "senderId"=> "KSRDMS",
            "msg"     => $data['message'],
            "created_at" => date("Y-m-d H:i:s")
        ]);

        if($i){
            return ["error" => false, "msg" => ""];
        }else{
            return ["error" => true, "msg" => "an error has occured while sending sms."];
        }
    }

    public function send($data, $dairyId = null){

 
        ///////Check for SMS Balance
        if($dairyId == null){
            $dairyId = session()->has("loginUserInfo")?session()->get("loginUserInfo")->dairyId:null;
        }
  
        $dairy = DB::table("dairy_info")->where(["id" => $dairyId])->get()->first();
        
   
        if($dairy==null){
          return ["error" => true, "msg" => "SMS not sent, SMS balance is low, contact Dairy Super Admin"];
        }
        if($dairy->remainingSms < 1){
            return ["error" => true, "msg" => "SMS not sent, SMS balance is low, contact Dairy Super Admin"];
        }
     
        $smsBal = $dairy->remainingSms - count(array($data['numbers']));

        ////////////////////////////


        $senderId = "KSRDMS";
        $route = 4;
        $campaign = "KSR Services";
        $sms = array(
            'message' => $data['message'],
            'to' => array($data['numbers'])
            
        );
       
     
        $message = urlencode($data['message']);
        
        $templateId = $this->GetTemplateId($data['messageType']);

       // $data['numbers'] = "7976213802";
       
//        $url = 'http://sms.ssdindia.com/api/sendhttp.php?authkey=17942An1gra9u5eaeab59P20&mobiles=91'.$data['numbers'].'&message='.$message.'&sender=KSRDMS&route=4&country=91&DLT_TE_ID='.$templateId;
        $url = 'http://sms.ssdweb.in/api/sendhttp.php?authkey=360306AlWgU8O7I609535bfP1&mobiles=91'.$data['numbers'].'&message='.$message.'&sender=KSRDMS&route=4&country=91&DLT_TE_ID='.$templateId;
                
        //$url = 'http://login.yourbulksms.com/api/sendhttp.php?authkey=11249ABPPIYXMUkMH5c8f350c&mobiles=91'.$data['numbers'].'&message='.$message.'&sender=KSRDMS&route=4&country=91';
        
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => "0",
            CURLOPT_URL => $url,
        ));
        
        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

    #print_r($result);exit(0);
        
        if (!file_get_contents($url)) {
           
            return ["error" => true, "msg" => "cURL Error #:" . $err];
            $i = DB::table("smserrorlog")->insertGetId(["dairyId" => $dairyId, "error" => json_encode($err), "created_at" => date("Y-m-d H:i:s")]);
        } else {
            $result = "Message sent successfully";
            $d = DB::table("dairy_info")->where(["id" => $dairyId])->update(["remainingSms" => $smsBal]);
            return ["error" => false, "msg" => $result];
        }
        ///////////////// New API////////////////
    }


    public function checkSmsBalance($dairyId)
    {
        if($dairyId == null){
            $dairyId = session()->get("loginUserInfo")->dairyId;
        }

        $d = DB::table("dairy_info")->where(["id" => $dairyId])->get()->first();

        if($d==null){
            return false;
        }
        if($d->remainingSms > 0){
            return true;
        }else{
            return false;
        }
    }

    public function sendQueueSms()
    {
        DB::beginTransaction();
        $sms = DB::table("queuesms")->get();

        foreach($sms as $s){

            $data = [
                "numbers" => $s->to_,
                "message" => $s->msg,
            ];

            $res = $this->send($data, $s->dairyId);
            $dlt = DB::table("queuesms")->where(["id" => $s->id])->delete();
        }

        DB::commit();
    }
    
    private function GetTemplateId($_templateName) {
        $array = [
           'dairyRegistration' => '1407159858926294506',
           'collectionManagerRegistration' => '1407159858919900570',
           'productSale' => '1407159858901731420',
           'credit' => '1407159858888398467',
           'otp' => '1407159858870819224',
           // 'newMember' => '1407159858851952190',
           // 'advance' => '1407159858837397033',
           // 'milkCollectionWithoutSnf' => '1407159858830503220',
           // 'milkCollectionWithSnf' => '1407159858820924252'
           'newMember' => '1407165975961372792',
           'advance' => '1407159858837397033',
           'milkCollectionWithoutSnf' => '1407165984816000855',
           'milkCollectionWithSnf' => '1407165984831130161'
        ];

        return $array[$_templateName];
    }
}
