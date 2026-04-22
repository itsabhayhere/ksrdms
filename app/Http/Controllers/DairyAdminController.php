<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DairyAdminController extends Controller
{
    
    // public function __construct()
    // {
    //     $this->middleware('Auth');
    // }
    
    // get all states name and id 
    public function allStates(){
        $states = DB::table('states')->get();
        return view('dairySetup', ['states' => $states]);
        // return view('addDairyAdmin', ['states' => $states]);
    }

    // get all city by states 
    public function allCitys(Request $request){
        $cities = DB::table('city')->get()->where('state_id', $request->state_id);
        return response()->json($cities);
    }

    //check Society Validate
    public function SocietyValidate(Request $request){
        $dairySocietyCode = DB::table('dairy_info')
                                    ->where('society_code', $request->society_code)
                                    ->get();
        if(!(empty($dairySocietyCode[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    function numberValidate(Request $request){

        $dairyEmail = DB::table('dairy_propritor_info')
                                    ->where('PropritorMobile', $request->PropritorMobile )
                                    ->get();
        if(!(empty($dairyEmail[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    //member personal code validation
    public function memberPersonalCodeValidation(Request $request){
        $personalCode = DB::table('member_personal_info')
                                    ->where('memberPersonalCode', $request->personal_code )
                                    ->get();
        if(!(empty($personalCode[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    // member email validation
    public function memberEmailValidation(){
        $memberEmail = DB::table('member_personal_info')
                                    ->where('memberPersonalEmail', $request->member_email )
                                    ->get();
        if(!(empty($memberEmail[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    function newPassword(){
            $password = "";
            $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            
            for($i = 0; $i < 8; $i++)
            {
                $random_int = mt_rand();
                $password .= $charset[$random_int % strlen($charset)];
            }
            // echo $password, "n";
            return $password ;
    }

        // add dairy admin user function
    public function create(Request $request){

        return $request->all();
        
    }

    public function editDairyInfo(Request $request){
    
        
        $dairy_info = DB::table('dairy_info')
            ->where('id', session()->get('loginUserInfo')->dairyId)
            ->get();

        $dairy_propritor_info = DB::table('dairy_propritor_info')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->get();

        $shift_timing = DB::table('shift_timing')
            ->where('shiftDairyId', session()->get('loginUserInfo')->dairyId)
            ->get();

        $states = DB::table('states')->get();

        return view('dairyEdit', ['dairy_info' => $dairy_info[0],'dairy_propritor_info' => $dairy_propritor_info[0], 'states'=> $states]);
      
    }

    /* edit dairy submit */
    public function editDairyInfoSubmit(Request $request){
   

            $dairy_propritor_info = DB::table('dairy_propritor_info')
                ->where('dairyId', $request->dairyId)
                ->get();
                
          
            $submitedCity = "";
            if($request->dairyPropritorCity == "-- citys --"){

                $submitedCity = $dairy_propritor_info[0]->dairyPropritorCity;
            }else{
                $submitedCity = $request->dairyPropritorCity ;
            }
        // if($request->dairyId == )
        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        
       
        DB::table('dairy_info')
            ->where('id', $request->dairyId)
            ->update([
                'society_name' => $request->society_name,
                'dairyAddress' => $request->dairyInfoAddressId,
                'state' => $request->state,
                'city' => $request->city,
                'district' => $request->district, 
                'pincode' => $request->pincode,
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
            'dairyPropritorCity' => $submitedCity, 
            'dairyPropritorDistrict' => $request->dairyPropritorDistrict, 
            'dairyPropritorPincode' => $request->dairyPropritorPincode, 
            'updated_at' => $currentTime,
         ]);

        $returnSuccessArray = array("Success"=>"True","Message"=>"Dairy Successfully Updated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        // return $returnSuccessJson;
        return redirect('DairyAdminDashbord');
        // return view('DairyAdminDashbord') ;
    }

    /* editDairyEmailValidation */
    public function editDairyEmailValidation(Request $request){
        $dairyEmail = DB::table('dairy_propritor_info')
                                    ->where('dairyPropritorEmail', $request->dairyPropritorEmail )
                                    ->get();
          if(!(empty($dairyEmail[0]))){
            // return "true";
            if($dairyEmail[0]->dairyId == $request->dairyId ){
               return "false";
            }else{
                return "true";
            }
        }else{
            return "false";
        }

    }

    public function editMemberInfo(Request $request){

        if($request->restore == "true"){
            $restore = DB::table('member_personal_info')
                    ->where('id', $request->member_id)
                    ->update(["status" => "true"]);

            Session::flash('msg', 'Member Restored.'); 
            Session::flash('alert-class', 'alert-success');
        }

        $member_personal_info = DB::table('member_personal_info')
            ->where('id', $request->member_id)
            ->get();

        $member_personal_bank_info = DB::table('member_personal_bank_info')
            ->where('memberPersonalUserId', $request->member_id)
            ->get();

        $member_other_info = DB::table('member_other_info')
            ->where('memberId', $request->member_id)
            ->get();

		$cities = DB::table('city')->get()->where('state_id', $member_personal_info[0]->memberPersonalState);

        $states = DB::table('states')->get();
        $returnData = array($member_personal_info[0] ,$member_personal_bank_info[0] ,$member_other_info[0] );
       
        return view('editMember', ['member_info' => $returnData, 'states'=> $states, 'cities'=>$cities, "activepage"=>"members"]);

        // return view('editMember');
    }

    /* Edit member info submit */
    public function editMemberInfoSubmit(Request $request){

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
            'openingBalance' => $request->memberPersonalOpeningBalance,
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

        $returnSuccessArray = array("Success"=>"True","Message"=>"Member Successfully Updated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;
    }

/* edit member email validation */
    public function editMemberEmailValidation(Request $request){

      $member_email = DB::table('member_personal_info')
                                    ->where('memberPersonalEmail', $request->member_email )
                                    ->get();
       
          if(!(empty($member_email[0]))){
            // return "true";
            if($member_email[0]->id == $request->member_id ){
               return "false";
            }else{
                return "true";
            }
        }else{
            return "false";
        } 
    }

    public function dairyDetails()
    {
        $colMan = Session::get("colMan");
        $colMan = DB::table("other_users")->where(["id" => $colMan->id])->get()->first();

        $dairy = DB::table("dairy_info")->where(["id" => $colMan->dairyId])->get()->first();

        $propritor = DB::table("dairy_propritor_info")->where(["dairyId" => $colMan->dairyId])->get()->first();

        $states = DB::table("states")->get();
        $city = DB::table("city")->where("state_id", $propritor->dairyPropritorState)->get();
        $dcity = DB::table("city")->where("state_id", $dairy->state)->get();

        return view("dairyDetails", ["states" => $states, "city" => $city, "dcity" => $dcity, "dairy" => $dairy, "colMan" => $colMan, "propritor" => $propritor]);
    }

    public function updateDairyDetails()
    {
        $validatedData = Validator::make(request()->all(), [
            'name'      => 'required',
            'code'      => 'required',
            'mobile'    => 'required|numeric|digits:10',
            'address'   => 'required',
            'state'     => 'required',
            'district'  => 'required',
            'owname'    => 'required',
            'owemail'   => 'required|email',
            'owaddress' => 'required',
            'owstate'   => 'required',
            'owdistrict'=> 'required',
            'owpin'     => 'required',
        ]);

        $colMan = Session::get("colMan");

        DB::beginTransaction();

        $u = DB::table("dairy_info")->where(["id" => $colMan->dairyId])
                ->update([
                    "dairyName"     => request("name"),
                    "society_code"  => request("code"),
                    "mobile"        => request("mobile"),
                    "dairyAddress"  => request("address"),
                    "state"         => request("state"),
                    "city"          => request("district"),
                    "updated_at"    => date("Y-m-d H:i:s")
                ]);

        if(!$u){
            DB::rollBack();
            Session::flash('msg', 'There is an error occured.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairyDetails");
        }

        $u = DB::table("dairy_propritor_info")->where(["dairyId" => $colMan->dairyId])
                ->update([
                    "dairyPropritorName"     => request("owname"),
                    "dairyPropritorAddress"  => request("owaddress"),
                    "dairyPropritorState"    => request("owstate"),
                    "dairyPropritorCity"     => request("owdistrict"),
                    "dairyPropritorDistrict" => request("owdistrict"),
                    "dairyPropritorPincode"  => request("owpin"),
                    "updated_at"             => date("Y-m-d H:i:s")
                ]);

        if(!$u){
            DB::rollBack();
            Session::flash('msg', 'There is an error occured.'); 
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairyDetails");
        }

        DB::commit();
        Session::flash('msg', 'Details updated successully.'); 
        Session::flash('alert-class', 'alert-success');
        return redirect("dairyDetails");
    }
}