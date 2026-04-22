<?php



namespace App\Http\Controllers;



use App\AdvanceCredit;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;

use PushNotification;



class AdvanceCreditController extends Controller

{



    public function __construct()

    {

        $this->middleware('Auth');

    }

    

    public function advanceForm()

    {

        $dairyId = session()->get('loginUserInfo')->dairyId;



        $mem = DB::table('member_personal_info')

            ->where('dairyId', $dairyId)->where("status", "true")

            ->get();



        $customer = DB::table('customer')

            ->where('dairyId', $dairyId)->where("status", "true")

            ->get();

        

        $sup = DB::table('suppliers')

            ->where('dairyId', $dairyId)->where("status", "true")

            ->get();



        return view("advance", ["members" => $mem, "customer" => $customer, "suppliers"=>$sup, "activepage" => "advanceForm"]);

    }



    public function advanceSubmit(Request $req)

    {

        $dairyId = session()->get('loginUserInfo')->dairyId;

        $dairy = Db::table("dairy_info")->where(["id" => $dairyId])->get()->first();



        $validatedData = $req->validate([

            'date'      => 'required|date',

            'partyCode' => 'required',

            'partyName' => 'required',

            'partyType' => 'required',

            'amount'    => 'required',

        ]);



        $model = new AdvanceCredit();

        $res = $model->addAdvance($req);



        if($res){



            $mobile = null;$name=null;

            if(request("partyType") == "member"){

                $m = DB::table("member_personal_info")->where(['memberPersonalCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();

                if($m!=null){

                    $app_data = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $m->id])->get();

                    $appSettings = DB::table('androidappsetting')->get()->first();

        

                    if($app_data==null){

                        $memberToken = null;

                    }else{

                        $memberToken = $app_data->pluck('token_key');

                    }



                    $name = $m->memberPersonalName;

                    $mobile = $m->memberPersonalMobileNumber;

                    $ub =  DB::table("user_current_balance")

                                ->where('ledgerId', $m->ledgerId)

                                ->get()->first();

                }else{

                    goto SKIPSMS;

                }

            }else{

                goto SKIPSMS;

            }

    

            if(isset($ub) && $ub){

                $ub->openingBalance = number_format($ub->openingBalance, 2, ".", "");

                if($ub->openingBalanceType == "credit")

                    $bal = $ub->openingBalance." CR";

                else

                    $bal = $ub->openingBalance." DR";

            }else

                goto SKIPSMS;





            if($mobile==null)

                goto SKIPSMS;



    

                

            $tempName = explode(" ", $name);

            if(isset($tempName[1])){

                $name = $tempName[0].$tempName[1];  

            }else{

                $name = $tempName[0];  

            }





            $newLine = "\n";

            $message = "Dear $name,". $newLine.$dairy->society_code." - ".$dairy->dairyName.$newLine.

                        "Date: ".request('date').$newLine.

                        "Dabit Amount: ".number_format(request('amount'), 2, ".", "").$newLine.

                        "Remarks: ".request("remark").$newLine.

                        "Current Balance: ".$bal.$newLine;


                        

                if(count($memberToken) > 0){

                    foreach($memberToken as $tkn){
                        $response[] = PushNotification::setService('fcm')
                            ->setMessage([
                                'data' => [
                                    "message" => $message,
                                ],
                            ])
                            ->setApiKey($appSettings->server_api_key)
                            ->setDevicesToken($tkn)
                            ->send()
                            ->getFeedback();
                    }
            
                }else{
                    
                    $response[] = PushNotification::setService('fcm')
                        ->setMessage([
                            'data' => [
                                "message" => $message,
                            ],
                        ])
                        ->setApiKey($appSettings->server_api_key)
                        ->setDevicesToken($memberToken)
                        ->send()
                        ->getFeedback();
        
                }


            $alerts = DB::table("member_other_info")->where(["memberId" => $m->id])->get()->first();

            if($alerts->alert_sms != "true"){

                goto SKIPSMS;

            }



            $newLine = "%0A";

            $data = ["message" => "Dear $name,". $newLine.$dairy->society_code." - ".$dairy->dairyName.$newLine.

                "Date: ".request('date').$newLine.

                "Dabit Amount: ".number_format(request('amount'), 2, ".", "").$newLine.

                "Remarks: ".request("remark").$newLine.

                "Current Balance: ".$bal.$newLine,

                "numbers" => $mobile,
                "messageType" => "advance"

            ];

            $sms = new \App\Sms();

    

            // $sms->saveToQueue($data, $dairyId);

            $sms->send($data, $dairyId);

        }

        

        SKIPSMS:

        if($req->activetab){

            session()->put('AdvanceActiveTab', $req->activetab);

        }



        return redirect("advanceForm");

    }



    public function getAdvanceData()

    {

        $advance = DB::table('advance')

            ->where('dairyId', session()->get('loginUserInfo')->dairyId)

            ->orderby("created_at", "desc")

            ->get();



        $data = [];

        foreach ($advance as $a) {

            // $cb = DB::table('user_current_balance')->where('ledgerId', $a->ledgerId)->get()->first();

            // if ($cb->openingBalanceType == "credit") {

            //     $bal = "<span class='credit'> " . number_format((float)str_replace("-","",$cb->openingBalance),2) . "</span>";

            // } else {

            //     $bal = "<span class='debit'> " . number_format((float)$cb->openingBalance,2) . "</span>";

            // }

            $d = [

                $a->partyCode,

                $a->partyName,

                date("d-m-Y", strtotime($a->date)),

                "<b>" . $a->amount . "</b>",

                // $bal,

                $a->remark,

            ];

            $data[] = $d;

        }

        return ["data" => $data];

    }



    public function getCreditData()

    {

        $credit = DB::table('credit')

            ->where('dairyId', session()->get('loginUserInfo')->dairyId)->orderby("created_at", "desc")

            ->get();



        $data = [];

        foreach ($credit as $a) {

            // $cb = DB::table('user_current_balance')->where('ledgerId', $a->ledgerId)->get()->first();

            // if ($cb->openingBalanceType == "credit") {

            //     $bal = "<span class='credit'> " . number_format((float)str_replace("-","",$cb->openingBalance),2) . "</span>";

            // } else {

            //     $bal = "<span class='debit'> " . number_format((float)$cb->openingBalance,2) . "</span>";

            // }

            $d = [

                $a->partyCode,

                $a->partyName,

                date("d-m-Y", strtotime($a->date)),

                "<b>" . $a->amount . "</b>",

                // $bal,

                $a->remark,

            ];

            $data[] = $d;

        }

        return ["data" => $data];

    }



    public function creditForm()

    {

        $dairyId = session()->get('loginUserInfo')->dairyId;



        $mem = DB::table('member_personal_info')

            ->where('dairyId', $dairyId)->where("status", "true")

            ->get();



        $customer = DB::table('customer')

            ->where('dairyId', $dairyId)->where("status", "true")

            ->get();



        $sup = DB::table('suppliers')

                ->where('dairyId', $dairyId)->where("status", "true")

                ->get();

        

        return view("credit", ["members" => $mem, "customer" => $customer, "suppliers"=> $sup, "activepage" => "creditForm"]);

    }



    public function creditSubmit(Request $req)

    {



        $dairyId = session()->get('loginUserInfo')->dairyId;

        $dairy = Db::table("dairy_info")->where(["id" => $dairyId])->get()->first();

        

        $validatedData = $req->validate([

            'date' => 'required|date',

            'partyType' => 'required',

            'partyCode' => 'required',

            'partyName' => 'required',

            'credit' => 'required',

        ]);



        $model = new AdvanceCredit();

        $res = $model->addCredit($req);





        if($res){



            $mobile = null;$name=null;

            if(request("partyType") == "member"){

                $m = DB::table("member_personal_info")->where(['memberPersonalCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();

                if($m!=null){

                    $app_data = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $m->id])->get();

                    $appSettings = DB::table('androidappsetting')->get()->first();



                    if($app_data==null){

                        $memberToken = null;

                    }else{

                        $memberToken = $app_data->pluck('token_key');

                    }



                    $name = $m->memberPersonalName;

                    $mobile = $m->memberPersonalMobileNumber;

                    $ub =  DB::table("user_current_balance")

                                ->where('ledgerId', $m->ledgerId)

                                ->get()->first();

                }else{

                    goto SKIPSMS;

                }

            }else{

                goto SKIPSMS;

            }

    

            if(isset($ub) && $ub){

                $ub->openingBalance = number_format($ub->openingBalance, 2, ".", "");

                if($ub->openingBalanceType == "credit")

                    $bal = $ub->openingBalance." CR";

                else

                    $bal = $ub->openingBalance." DR";

            }else

                goto SKIPSMS;





            if($mobile==null)

                goto SKIPSMS;





            $tempName = explode(" ", $name);

            if(isset($tempName[1])){

                $name = $tempName[0].$tempName[1];  

            }else{

                $name = $tempName[0];  

            }



            $newLine = "\n";

            $message = "Dear $name,". $newLine.$dairy->society_code." - ".$dairy->dairyName.$newLine.

                        "Date: ".request('date').$newLine.

                        "Credited Amount: ".number_format(request('credit'), 2, ".", "").$newLine.

                        "Remark: ".request('remark').$newLine.

                        "Current Balance: $bal".$newLine;



                if(count($memberToken) > 0){

                    foreach($memberToken as $tkn){
                        $response[] = PushNotification::setService('fcm')
                            ->setMessage([
                                'data' => [
                                    "message" => $message,
                                ],
                            ])
                            ->setApiKey($appSettings->server_api_key)
                            ->setDevicesToken($tkn)
                            ->send()
                            ->getFeedback();
                    }
            
                }else{
                    
                    $response[] = PushNotification::setService('fcm')
                        ->setMessage([
                            'data' => [
                                "message" => $message,
                            ],
                        ])
                        ->setApiKey($appSettings->server_api_key)
                        ->setDevicesToken($memberToken)
                        ->send()
                        ->getFeedback();
        
                }



            $alerts = DB::table("member_other_info")->where(["memberId" => $m->id])->get()->first();

            if($alerts->alert_sms != "true"){

                goto SKIPSMS;

            }



            $newLine = "%0A";

            $data = ["message" => "Dear $name,". $newLine.$dairy->society_code." - ".$dairy->dairyName.$newLine.

                "Date: ".request('date').$newLine.
    
                "Credited Amount: ".number_format(request('credit'), 2, ".", "").$newLine.
    
                "Remark: ".request('remark').$newLine.
    
                "Current Balance: $bal".$newLine,
    
                "numbers" => $mobile,
                "messageType" => "credit"

                ];

            $sms = new \App\Sms();

    

            // $sms->saveToQueue($data, $dairyId);

            $sms->send($data, $dairyId);

        }



        SKIPSMS:

        if($req->activetab){

            session()->put('creditActiveTab', $req->activetab);

        }

        return redirect("creditForm");

    }



    public function validateUser(Request $req){



        $dairyId = session()->get('loginUserInfo')->dairyId;

        $user = null;



        if($req->type == "customer"){

            $user = DB::table('customer')

                ->where('customerCode', $req->code)

                ->get()->first();

        }

        if($req->type == "member"){

            $user = DB::table('member_personal_info')

                ->where("dairyid", $dairyId)

                ->where('memberPersonalCode', $req->code)

                ->get()->first();

        }



        if ($user == (null || false)) {

            return ["error" => true, "msg"=> "No user found."];

        }

        

        return ["error" => false, "msg" => ""];



    }

}

