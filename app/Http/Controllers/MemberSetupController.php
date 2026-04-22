<?php

namespace App\Http\Controllers;
use App\Sms;

use App\memberSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class MemberSetupController extends Controller
{   
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    /* member list */
    public function memberList(Request $req){

        $dairyId = session()->get('loginUserInfo')->dairyId;

        $members = DB::table('member_personal_info')->where('dairyId', $dairyId )->where("status","true")->get();

        $count = 0 ;
        foreach($members as $membersData){
            /* states */
            $getSatatNme = DB::table('states')
                                ->where('id', $membersData->memberPersonalState)
                                ->get()->first();
            if($getSatatNme){
                $members[$count]->memberPersonalState = $getSatatNme->name;
            }else{
                $members[$count]->memberPersonalState = "";
            }

            /* city */
            $getCityName = DB::table('city')
                        ->where('id', $membersData->memberPersonalCity)
                        ->get()->first();
            if($getCityName){
                $members[$count]->memberPersonalCity = $getCityName->name;
            }else{
                $members[$count]->memberPersonalCity = "";
            }
                        
            $count++ ;
        }

        return view('memberList', ['members' => $members, 'activepage'=>"members"]);
    }

    /* member setup form */
    public function memberSetupForm(){
        
       
        $colMan = Session::get('colMan');
        $mem = DB::table("member_personal_info")->where(["dairyId" => $colMan->dairyId, "status" => "true"])->count();

        $s = DB::table("subscribe")->where(["dairyId" => $colMan->dairyId])
                ->leftjoin("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")
                ->get()->first();

        if(!$this->checkMemberLimit($colMan->dairyId, $mem)){
            Session::flash('msg', 'You have reach your subscription plan limit, please upgrade your subscription plan.'.$s->noOfMem);
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberList");
        }

        $states = DB::table('states')->get();
        
        return view('addMember', ['states' => $states, 'activepage'=>"members"]);
    }

        //member personal code validation
    public function memberPersonalCodeValidation(Request $request){
        $personalCode = DB::table('member_personal_info')
                                    ->where(['memberPersonalCode' => $request->personal_code,
                                            "dairyId"   => session()->get('loginUserInfo')->dairyId,
                                            "status"    => "true"])
                                    ->get();
        if(!(empty($personalCode[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    /* number validation */

    public function memberNumberValidation(Request $request){
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $member = DB::table('member_personal_info')
                                    ->where(['memberPersonalMobileNumber' => $request->memberNumber,
                                                "dairyId"   => $dairyId,
                                                "status"    => "true"])
                                    ->get();
        if(!(empty($member[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    /* member name validation */
    public function memberNameValidation(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $memberEmail = DB::table('member_personal_info')
                                    ->where(['memberPersonalName'=> $request->memberName,
                                    "dairyId"   => $dairyId,
                                    "status"    => "true"])
                                    ->get();
        if(!(empty($memberEmail[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    /* member aadhar number validation */
    public function memberAadharNumberValidation(Request $request){

        $memberEmail = DB::table('member_personal_info')
                                    ->where('memberPersonalAadarNumber', $request->aadharNumber )
                                    ->get();

        if(!(empty($memberEmail[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    public function deleted_member_list()
    {
        $colMan = Session::get('colMan');

        if(session('members')){
            return view("deleted_member_list", ["members" => session('members')]);
        }

        $mems = DB::table('member_personal_info')->where([
                    "dairyId" => $colMan->dairyId, "status" => "false"
                ])->get();

        return view("deleted_member_list", ["members" => $mems]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        /* password */
        $password = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        
        for($i = 0; $i < 8; $i++){
            $random_int = mt_rand();
            $password .= $charset[$random_int % strlen($charset)];
        }
        

        $colMan = Session::get('colMan');
        $currentTime =  date('Y-m-d H:i:s');
        $mem = DB::table("member_personal_info")->where(["dairyId" => $colMan->dairyId, "status" => "true"])->count();

        if(!$this->checkMemberLimit($colMan->dairyId, $mem)){
            Session::flash('msg', 'You have reach your subscription plan limit, please upgrade your subscription plan.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberList");
        }

        DB::beginTransaction();

        $exist = DB::table('member_personal_info')->where([
            "dairyId" => $colMan->dairyId, "status" => "true", "memberPersonalCode" => $request->memberPersonalCode
        ])->get()->first();

        $exist1 = DB::table('member_personal_info')->where([
            "dairyId" => $colMan->dairyId, "status" => "true", "memberPersonalMobileNumber" => $request->memberPersonalMobileNumber
        ])->get()->first();

        $exist2 = DB::table('member_personal_info')->where([
            "dairyId" => $colMan->dairyId, "status" => "true", "memberPersonalName" => $request->memberPersonalName
        ])->get()->first();


        if($exist || $exist1 || $exist2){
            DB::rollback();
            Session::flash('msg', 'Member Code or Member Name or Mobile Number already Exist.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberSetupForm");
        }

        $existDel = DB::table('member_personal_info')->where([
            "dairyId" => $colMan->dairyId, "status" => "false", "memberPersonalCode" => $request->memberPersonalCode
        ])->get();

        if(count($existDel) > 0){
            DB::rollback();
            Session::flash('msg', 'Record found with same Member Code that was deleted.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("deleted_member_list")->with('members', $existDel);
        }

        $memberId = DB::table('member_personal_info')->insertGetId([
                'dairyId' => $request->dairyId,
                'status' =>$request->status,
                'password' => $password,
                'memberPersonalCode' => $request->memberPersonalCode,
                'memberPersonalregisterDate' => $request->memberPersonalregisterDate,
                'memberPersonalName' => $request->memberPersonalName,
                'memberPersonalFatherName' => $request->memberPersonalFatherName,
                'memberPersonalGender' => $request->memberPersonalGender,
                'memberPersonalEmail' => $request->memberPersonalEmail,
                'memberPersonalAadarNumber' => $request->memberPersonalAadarNumber."",
                'memberPersonalMobileNumber' => $request->memberPersonalMobileNumber,
                'memberPersonalAddress' => $request->memberPersonalAddress."",
                'memberPersonalState' => $request->memberPersonalState."",
                'memberPersonalCity' => $request->memberPersonalCity."",
                'memberPersonalDistrictVillage' => $request->memberPersonalDistrictVillage."",
                'memberPersonalMobilePincode' => $request->memberPersonalMobilePincode."",
                'memberPersonalCategory' => $request->memberPersonalCategory."",
                'is_andtodya' => $request->is_andtodya."",
                'memberpp_familyid' => $request->memberpp_familyid."",
                'Verified_income' => $request->Verified_income."",
                'created_at' => $currentTime,
            ]);

        if($memberId==(false||null||"")){
            DB::rollback();
            Session::flash('msg', 'There is a problem, Error Code: ERROR_IN_INSERTING');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberSetupForm");
        }

        $ledgerId = DB::table('ledger')->insertGetId([
                'userId'     => $memberId,
                'dairyId'    => $request->dairyId,
                'userType'   => "4",
                'ledgerType' => $request->openingBalanceType,
                'created_at' => $currentTime,
            ]);

        if($ledgerId==(false||null||"")){
            DB::rollback();
            Session::flash('msg', 'There is a problem, Error Code: ERROR_IN_INSERTING_2');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberSetupForm");
        }

        $dairy_info = DB::table('member_personal_bank_info')->insertGetId([
                'memberPersonalUserId' => $memberId,
                'memberPersonalBankName' => $request->memberBankName."",
                'memberPersonalAccountName' => $request->memberAccName."",
                'memberPersonalAccountFName'=> $request->memberAccFName."",
                'memberPersonalAccountNumber' => $request->memberBankNumber."",
                'memberPersonalIfsc' => $request->memberBankIfsc."",
                'created_at' => $currentTime,
            ]);
        if($dairy_info==(false||null||"")){
            DB::rollback();
            Session::flash('msg', 'There is a problem, Error Code: ERROR_IN_INSERTING_4');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberSetupForm");
        }

        $dairy_info = DB::table('member_other_info')->insertGetId([
                'memberId'          => $memberId,
                // 'milkeType'         => $request->milkType,
                'alert_print_slip'  => $request->aleryPrintSlip,
                'alert_sms'         => $request->alerySms,
                'alert_email'       => $request->aleryEmail,
                'created_at'        => $currentTime,
            ]);
        if($dairy_info==(false||null||"")){
            DB::rollback();
            Session::flash('msg', 'There is a problem, Error Code: ERROR_IN_INSERTING_5');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberSetupForm");
        }

        
        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId'      => $ledgerId,
            'transactionId' => $memberId,
            'srcDest'       => $ledgerId,
            'dairyId'       => $request->dairyId,
            'colMan'        => $colMan->userName,
            'transactionType' => 'member_personal_info',
            'remark'        => "Opening balance",
            'amountType'    => $request->openingBalanceType,
            'finalAmount'   => $request->memberPersonalOpeningBalance,
            'created_at'    => $currentTime,
          ]);

    
        /* add opening balance in database current balance table  */
        $userCurrentBalance = DB::table('user_current_balance')->insertGetId([
                'ledgerId'              => $ledgerId,
                'userId'                => $memberId,
                'userType'              => '4' ,
                'openingBalance'        => $request->memberPersonalOpeningBalance,
                'openingBalanceType'    => $request->openingBalanceType,
                'created_at'            => $currentTime,
            ]);

        if($userCurrentBalance==(false||null||"") || $balanceSheetSubmit==(false||null||"")){
            DB::rollback();
            Session::flash('msg', 'There is a problem, Error Code: ERROR_IN_OPENING_BALANCE');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberSetupForm");
        }

        $u = DB::table('member_personal_info')
            ->where('id', $memberId)->update(['ledgerId'  => $ledgerId, "txnId" => $balanceSheetSubmit]);
        if($u==(false||null||"")){
            DB::rollback();
            Session::flash('msg', 'There is a problem, Error Code: ERROR_IN_INSERTING_3');
            Session::flash('alert-class', 'alert-danger');
            return redirect("memberSetupForm");
        }


        DB::commit();

        // Mail::send('emails.welcomeMember', ['user' => $user], function ($m) use ($req) {
        //     $m->from('dms.online.2018@gmail.com', 'DMS ADMIN');
        //     $m->to("Yannisoni@gmail.com", "Yanni Soni")->subject('Welcome to DMS!');
        // });

        $dairyInfo = DB::table("dairy_info")->where("id", $request->dairyId)->get()->first();

        $tempName = explode(" ", $request->memberPersonalName);
        if(isset($tempName[1])){
            $memName = $tempName[0].$tempName[1];  
        }else{
            $memName = $tempName[0];  
        }


        $sms = new Sms();
        $newLine = "%0A";
        $smsRes = $sms->send(
            [
                "message" => "Dear, ".$memName.", you have registered at ".$dairyInfo->dairyName.$newLine.
                "Member Code: ". $request->memberPersonalCode.$newLine.
                "Id: ".$request->memberPersonalMobileNumber.$newLine."Password: ".$password.$newLine."login with OTP: http://bit.ly/2EFLIjX",
                "numbers" => $request->memberPersonalMobileNumber,
                "messageType" => "newMember"
            ], $dairyInfo->id);

        if(isset($smsRes["error"]) && !$smsRes['error']){
            $msg = "SMS Sent.";
        }else{
            $msg = "Error in sending SMS.";   
        }
        Session::flash('msg', 'Member Successfully Added. '.$msg);
        Session::flash('alert-class', 'alert-success');
        return redirect("memberList");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\memberSetup  $memberSetup
     * @return \Illuminate\Http\Response
     */
    public function show(memberSetup $memberSetup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\memberSetup  $memberSetup
     * @return \Illuminate\Http\Response
     */
    public function edit(memberSetup $memberSetup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\memberSetup  $memberSetup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, memberSetup $memberSetup)
    {
        //
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




    /* edit member email validation */
    public function editMemberNumberValidation(Request $request){
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $member_email = DB::table('member_personal_info')
                            ->where(['memberPersonalMobileNumber' => $request->memberNumber,
                                    "status"    => "true",
                                    "dairyId"   => $dairyId])
                            ->get();

        if(!(empty($member_email[0]))){
          
            if($member_email[0]->id == $request->memberId ){
               return "false";
            }else{
                return "true";
            }
        }else{
            return "false";
        } 
    }

    /* edit aadhar number validation */
    public function editMemberAadharNumberValidation(Request $request){
        $member_email = DB::table('member_personal_info')
                            ->where('memberPersonalAadarNumber', $request->aadharNumber)
                            ->get();

        if(!(empty($member_email[0]))){
            if($member_email[0]->id == $request->memberId ){
               return "false";
            }else{
                return "true";
            }
        }else{
            return "false";
        } 
    }

    /* edit name validation */

    public function editMemberNameValidation(Request $request){
        $member_email = DB::table('member_personal_info')
                            ->where('memberPersonalName', $request->memberName )
                            ->get();

        if(!(empty($member_email[0]))){
            if($member_email[0]->id == $request->memberId ){
               return "false";
            }else{
                return "true";
            }
        }else{
            return "false";
        } 
    }
// member name

     /* Edit member info submit */
    public function editMemberInfoSubmit(Request $request){

         /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
       
        DB::table('member_personal_info')
            ->where('id', $request->memberId)
            ->update([
                'memberPersonalregisterDate' => $request->memberPersonalregisterDate,
                'memberPersonalName' => $request->memberPersonalName."",
                'memberPersonalFatherName' => $request->memberPersonalFatherName."",
                'memberPersonalGender' => $request->memberPersonalGender."",
                'memberPersonalEmail' => $request->memberPersonalEmail."",
                'memberPersonalAadarNumber' => $request->memberPersonalAadarNumber."",
                'memberPersonalMobileNumber' => $request->memberPersonalMobileNumber,
                'memberPersonalAddress' => $request->memberPersonalAddress."",
                'memberPersonalState' => $request->memberPersonalState."",
                'memberPersonalCity' => $request->memberPersonalCity."",
                'memberPersonalDistrictVillage' => $request->memberPersonalDistrictVillage."",
                'memberPersonalMobilePincode' => $request->memberPersonalMobilePincode."",
                'memberPersonalCategory' => $request->memberPersonalCategory."",
                'is_andtodya' => $request->is_andtodya."",
                'memberpp_familyid' => $request->memberpp_familyid."",
                'Verified_income' => $request->Verified_income."",
                'updated_at' => $currentTime,
            ]);

        DB::table('member_personal_bank_info')
            ->where('memberPersonalUserId', $request->memberId)
            ->update([
                'memberPersonalBankName' => $request->memberBankName."",
                'memberPersonalAccountName' => $request->memberAccName."",
                'memberPersonalAccountFName'=> $request->memberAccFName."",
                'memberPersonalAccountNumber' => $request->memberBankNumber."",
                'memberPersonalIfsc' => $request->memberBankIfsc."",
                'updated_at' => $currentTime,
            ]);

        $updt = DB::table('member_other_info')
        ->where('memberId', $request->memberId)
        ->update([
            // 'milkeType' => $request->milkType,
            'alert_print_slip' => $request->aleryPrintSlip,
            'alert_sms' => $request->alerySms,
            'alert_email' => $request->aleryEmail,
            'updated_at' => $currentTime,
       ]);

       if($updt){
            Session::flash('msg', 'Member Successfully Updated'); 
            Session::flash('alert-class', 'alert-success');
       }else{
            Session::flash('msg', 'Member not updated, Some error occured.'); 
            Session::flash('alert-class', 'alert-danger');
       }


        // $returnSuccessArray = array("Success"=>"True","Message"=>"Member Successfully Updated");
        // $returnSuccessJson =  json_encode($returnSuccessArray);
        // return $returnSuccessJson ;
        return redirect()->action('MemberSetupController@memberList');

    }

    public function checkMemberLimit($dairyId, $mem)
    {
        $s = DB::table("subscribe")->where(["dairyId" => $dairyId])
                ->join("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")
                ->get()->first();
        if($s==null){
            return false;
        }
        if($mem < (int)$s->noOfMem){
            return true;
        }else{
            return false;            
        }
    }
}
