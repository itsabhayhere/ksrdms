<?php

namespace App\Http\Controllers;

use App\Sms;
use App\superAdmin;
use App\User;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mail;
use NumberToWords\NumberToWords;
use PDF;

include 'Crypto.php';

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {

    }

    public function getPDFfromHTML()
    {
        if (request('content') != null && request('fileName') != null) {
            $fname = request('fileName') . time() . '.pdf';

            // return view('pdf.cmsubsidary', ["content" => request('content')]);

            $pdf = PDF::loadView('pdf.cmsubsidary', ["content" => request('content')]);
            $pdf->save("download/pdfs" . $fname)->download($fname);

            return ["error" => false, "msg" => "", "url" => "download/pdfs".$fname];

        } else {
            return ["error" => true, "msg" => "fields are required"];
        }
    }

    public function getPDFinvoice()
    {
        $loginUserInfo = session()->get('loginUserInfo');
        $loginUserType = session()->get('loginUserType');

        $dairy = DB::table('dairy_info')
            ->where('dairy_info.id', $loginUserInfo->dairyId)
            ->select("*", "city.name as cityName", "states.name as stateName")
            ->leftjoin("city", "city.id", "=", "dairy_info.city")
            ->leftjoin("states", "states.id", "=", "dairy_info.state")
            ->get()->first();

        $rcvdString = 'order_id=154331813319&tracking_id=107477737555&bank_ref_no=20181127111212800110168286447112092&order_status=Success&failure_message=&payment_mode=Wallet&card_name=Paytm&status_code=null&status_message=Txn Success¤cy=INR&amount=1.0&billing_name=Yatindra soni&billing_address=72/171, krishna marg, near Kake di hatti, patel marg&billing_city=Mansarovar&billing_state=Rajasthan&billing_zip=302020&billing_country=India&billing_tel=9509751250&billing_email=yatindrasoni13013@gmail.com&delivery_name=Yatindra soni&delivery_address=72/171, krishna marg, near Kake di hatti, patel marg&delivery_city=Mansarovar&delivery_state=Rajasthan&delivery_zip=302020&delivery_country=India&delivery_tel=9509751250&merchant_param1=&merchant_param2=&merchant_param3=&merchant_param4=&merchant_param5=&vault=N&offer_type=null&offer_code=null&discount_value=0.0&mer_amount=1.0&eci_value=null&retry=N&response_code=0&billing_notes=Testing&trans_date=08/12/2018 17:00:25&bin_country=&merchant_param1=monthly&merchant_param2=8';
        $decryptValues = explode('&', $rcvdString);
        $dataSize = sizeof($decryptValues);

        for ($i = 0; $i < $dataSize; $i++) {
            $t = explode('=', $decryptValues[$i]);
            $info[$t[0]] = $t[1];
        }

        $cgst_per = 18;
        $sgst_per = 0;
        $igst_per = 0;
        $igst = 0;

        $rate = number_format(($info['amount'] / (1 + ($igst_per / 100))), 2, ".", "");
        $igst = number_format($info['amount'] - $rate, 2, ".", "");

        if ($dairy->state == 13) { ///if state is hariyana
            $sgst_per = 9;
            $cgst_per = 9;
            $igst_per = 0;

            $sgst = $cgst = number_format($igst / 2, 2, ".", "");
            $igst = 0;
        }

        $plan = DB::table('subscription_plan')->where(["id" => $info['merchant_param2']])->get()->first();

        $data = ["dairy" => $dairy, "payment" => (object) $info, 'plan' => $plan, "rate" => $rate,
            "cgst_per" => $cgst_per, "sgst_per" => $sgst_per, "igst_per" => $igst_per, "cgst" => $cgst, "sgst" => $sgst, "igst" => $igst];

        // return view("pdf.invoice", $data);
        $pdf = PDF::setOptions(['dpi' => 250, 'defaultFont' => 'sans-serif'])->loadView('pdf.invoice', $data);
        return $pdf->download('invoice.pdf');

    }

    public function getPricePlanDetails()
    {
        // return request()->all();
        $sp = db::table("subscription_plan")->where(["id" => request("planId"), "status" => "true"])->get()->first();
        if ($sp) {
            return ["error" => false, "plan" => $sp];
        } else {
            return ["error" => true, "msg" => "plan not found."];
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**

     * Show the application dashboard.

     *

     * @return \Illuminate\Http\Response

     */

    public function myHome()
    {
        $loginUserInfo = session()->get('loginUserInfo');
        $loginUserType = session()->get('loginUserType');

        $loginUserDairy = DB::table('dairy_info')
            ->where('id', $loginUserInfo->dairyId)
            ->get()->first();

        $loginUserData = array("loginUserInfo" => $loginUserInfo, "loginUserType" => $loginUserType, "loginUserDairy" => $loginUserDairy);
        $loginUserData = json_encode($loginUserData);
        return view('myHome', ['loginUserData' => $loginUserData]);

    }

    public function buy()
    {
        $pp = DB::table("subscription_plan")->get();
        $states = DB::table('states')->get();

        return view("buy.buy", ["pp" => $pp, "states" => $states, "activepage" => "buy"]);
    }

    public function registerNewDairy(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "pricePlanId" => 'required',
            "name" => 'required',
            "code" => 'required',
            "mobile" => 'required|digits:10|numeric',
            "owname" => 'required',
            "owmobile" => 'required|digits:10|numeric',
            "owemail" => 'required|email',
            "owaddress" => 'required',
            "owstate" => 'required',
            //"owdistrict" => 'required',
            'owpin' => "required",
            'priceMonthlyOrYearly' => "required|in:monthly,yearly",
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => true, "msg" => $validator->errors()->first()]);
        }

        $req->createBySuperAdmin = 0;

        $admin = new superAdmin();

        $res = $admin->newDairySetup($req);

        if (!$res['error']) {
            $data = [
                "sysName" => "DMS",
                "dairyAdmin" => "Vikram Saraswat",
                "user" => [
                    "username" => $req->owname,
                    "id" => $res['id'],
                    "pass" => $res['pass'],
                ],
                "loginlink" => url("dairy-login"),
            ];

            Mail::send('emails.welcome', $data, function ($m) use ($req) {
                $m->from('director@ksrservices.in', 'KSR Services Admin');
                $m->to($req->owemail, $req->owname)->subject('Welcome to DMS!');
            });

            $newLine = "%0A";
            $sms = new Sms();
            $rs = $sms->send(
                [
                    "message" => "Thanks for choosing us," . $newLine .
                    "Dairy registered successfuly," . $newLine .
                    "Login: " . url("/") . $newLine .
                    "ID: " . $res['id'] . $newLine .
                    "Password: " . $res['pass'] . $newLine . $newLine .
                    "Thanks!",
                    "numbers" => $req->owmobile,
                    "messageType" => "dairyRegistration"
                ], $res['dairyId']);

            Session::flash('msg', 'You have succesfully registered youir dairy. You will recieve an confirmation email shortly.');
            Session::flash('alert-class', 'alert-success');
        }

        return $res;
    }

    /**

     * Show the my users page.

     *

     * @return \Illuminate\Http\Response

     */

    public function myUsers()
    {
        $userJson = User::all();
        $user = json_decode($userJson);
        $count = 0;
        for ($i = 0; $i < count($user); $i++) {
            $UserDataFiledCount = count($user);
            if (!empty($user[$i])) {
                $count++;
            }

            $dateAfterChangeFormat = date('d-M-y', strtotime($user[$i]->created_at));
            $user[$i]->created_at = $dateAfterChangeFormat;
        }

        return view('myUsers')->with('user', $user);

    }

    /* user login form */

    public function loginForm()
    {

        $loginType = Session::get('loginUserType');
        if ($loginType == "customer") {
            return redirect("customer/dashboard");
        }
        if ($loginType == "supplier") {
            return redirect("supplier/dashboard");
        }
        if ($loginType == "member") {
            return redirect("member/dashboard");
        }
        if ($loginType == "dairy") {
            return redirect("DairyAdminDashbord");
        }
        if ($loginType == "sa") {
            return redirect("sa/dashboard");
        }

        $rolesSetups = DB::table('roles_setups')->get();
        return view('myLogin')->with('rolesSetups', $rolesSetups);
    }

    public function superAdminLogin()
    {

        $loginType = Session::get('loginUserType');
        if ($loginType == "customer") {
            return redirect("customer/dashboard");
        }
        if ($loginType == "supplier") {
            return redirect("supplier/dashboard");
        }
        if ($loginType == "member") {
            return redirect("member/dashboard");
        }
        if ($loginType == "dairy") {
            return redirect("DairyAdminDashbord");
        }
        if ($loginType == "sa") {
            return redirect("sa/dashboard");
        }

        return view("spradmin.login", ["activepage" => "login"]);
    }

    /* dairy login form */
    public function DiryLoginForm()
    {

        $loginSessCookie = Cookie::get('loginSessCookie');
        // return $loginSessCookie;

        if ($loginSessCookie != null) {
            $loginSessCookie = json_decode($loginSessCookie, false);
            session()->flush();

            foreach ($loginSessCookie as $key => $value) {
                session()->put($key, $value);
            }
        }

        $loginType = Session::get('loginUserType');
        if ($loginType == "customer") {
            return redirect("customer/dashboard");
        }
        if ($loginType == "supplier") {
            return redirect("supplier/dashboard");
        }
        if ($loginType == "member") {
            return redirect("member/dashboard");
        }
        if ($loginType == "dairy") {
            return redirect("DairyAdminDashbord");
        }
        if ($loginType == "sa") {
            return redirect("sa/dashboard");
        }

        return view('DairyLoginForm', ["activepage" => "login"]);
    }

    /* user login submit */
    public function loginFormSubmit(Request $request)
    {

        $loginResult = null;
        $help = new \App\APIHelperModel();

        $loginResult = DB::table('other_users')
            ->where('mobileNumber', $request->username)
            ->where('password', $request->password)
            ->get()->first();

        if ($loginResult != (null || false)) {
            unset($loginResult->password);

            // $isSub = $help->checkSubscription($loginResult->dairyId);
            // if($isSub["error"]){
            //     $returnData = array("success" => "true", "message" => $isSub["msg"], "url" => url("expiredPage"));
            //     return $returnData;
            // }

            session()->put('colMan', $loginResult);
            session()->put('loginUserType', "dairy");
            session()->put('loginUserInfo', $loginResult);

            $dairy = DB::table("dairy_info")->where("id", $loginResult->dairyId)->get()->first();
            unset($dairy->password);
            session()->put('dairyInfo', $dairy);

            // $this->createLoginCookie();
            Cookie::queue("loginSessCookie", session()->all(), 10080);

            $response = new Response(["success" => "true", "message" => "Successfully Login", "url" => url("DairyAdminDashbord")]);
            return $response;
        }

        $loginResult = DB::table('member_personal_info')
            ->where('memberPersonalMobileNumber', $request->username)
            ->where('password', $request->password)
            ->where('status', "true")
            ->get()->first();

        if ($loginResult != (null || false)) {
            unset($loginResult->password);

            // $isSub = $help->checkSubscription($loginResult->dairyId);
            // if($isSub["error"]){
            //     $returnData = array("success" => "true", "message" => $isSub["msg"], "url" => url("expiredPage"));
            //     return $returnData;
            // }

            $dairy = DB::table("dairy_info")->where("id", $loginResult->dairyId)->get()->first();

            $loginResult->dairyName = $dairy->dairyName;
            $loginResult->society_code = $dairy->society_code;
            session()->put('loginUserInfo', $loginResult);
            session()->put('loginUserType', "member");

            // $this->createLoginCookie();
            Cookie::queue("loginSessCookie", session()->all(), 10080);

            $response = new Response(["success" => "true", "message" => "Successfully Login", "url" => url("member/dashboard")]);
            return $response;
        }

        $loginResult = DB::table('milk_plant_head')
            ->where('mobile', $request->username)
            ->where('password', $request->password)
            ->where('status', "true")
            ->get()->first();

        // if ($loginResult != (null || false)) {
        //     unset($loginResult->password);
        //     $plant = DB::table("milk_plants")->where(["id" => $loginResult->plantId])->get()->first();
        //     session()->put('loginUserInfo', $loginResult);
        //     session()->put('plantInfo', $plant);

        //     session()->put('loginUserType', "plant");

        //     // $this->createLoginCookie();
        //     Cookie::queue("loginSessCookie", session()->all(), 10080);

        //     $response = new Response(["success" => "true", "message" => "Successfully Login", "url" => url("plant/dashboard")]);
        //     return $response;
        
         //my work
                $plant_dt= DB::table('milk_plants')
                            ->where('plantCode','PL'.$loginResult->id)->first();
                
                            
            //end

        if ($loginResult != (null || false)) {
            unset($loginResult->password);
            $plant = DB::table("milk_plants")->where(["id" => $loginResult->plantId])->get()->first();
            session()->put('loginUserInfo', $loginResult);
            session()->put('plantInfo', $plant);

            session()->put('loginUserType', "plant");

            // $this->createLoginCookie();
            Cookie::queue("loginSessCookie", session()->all(), 10080);
            if($plant_dt->isMainPlant!='1'){
                $response = new Response(["success" => "true", "message" => "Successfully Login", "url" => url("plant/dashboard")]);
                return $response;

            }
            else{
                $response = new Response(["success" => "true", "message" => "Successfully Login", "url" => url("headPlant/dashboard")]);
                return $response;
            }

        }
        
 
        $loginResult = DB::table('suppliers')
            ->where('supplierMobileNumber', $request->username)
            ->where('password', $request->password)
            ->where('status', "true")
            ->get()->first();

        if ($loginResult != (null || false)) {
            unset($loginResult->password);

            // $isSub = $help->checkSubscription($loginResult->dairyId);
            // if($isSub["error"]){
            //     $returnData = array("success" => "true", "message" => $isSub["msg"], "url" => url("expiredPage"));
            //     return $returnData;
            // }

            session()->put('loginUserInfo', $loginResult);
            session()->put('loginUserType', "supplier");

            // $this->createLoginCookie();

            Cookie::queue("loginSessCookie", session()->all(), 10080);

            $response = new Response(["success" => "true", "message" => "Successfully Login", "url" => url("supplier/dashboard")]);
            return $response;
        }

        $loginResult = DB::table('customer')
            ->where('customerMobileNumber', $request->username)
            ->where('password', $request->password)
            ->where('status', "true")
            ->get()->first();

        if ($loginResult != (null || false)) {
            unset($loginResult->password);

            // $isSub = $help->checkSubscription($loginResult->dairyId);
            // if($isSub["error"]){
            //     $returnData = array("success" => "true", "message" => $isSub["msg"], "url" => url("expiredPage"));
            //     return $returnData;
            // }

            session()->put('loginUserInfo', $loginResult);
            session()->put('loginUserType', "customer");

            // $this->createLoginCookie();
            Cookie::queue("loginSessCookie", session()->all(), 10080);

            $response = new Response(["success" => "true", "message" => "Successfully Login", "url" => url("customer/dashboard")]);
            return $response;
        }

        return ["success" => "false", "message" => "Login failed, invalid Mobile number or password"];
    }

    /* SuperAdmin login submit */
    public function saLoginFormSubmit(Request $request)
    {

        $loginResult = DB::table('superadmin')
            ->where('username', $request->username)
            ->where('password', $request->password)
            ->get()->first();

        if ($loginResult != (null || false)) {
            unset($loginResult->password);
            session()->put('loginUserInfo', $loginResult);
            session()->put('loginUserType', "sa");

            // $this->createLoginCookie();
            Cookie::queue("loginSessCookie", session()->all(), 10080);

            $response = new Response(["success" => "true", "message" => "Successfully Login"]);
            return $response;
        } else {
            return array("success" => "false", "message" => "Enter Valid Login Info");
        }
    }

    public function sendLoginOtp()
    {
        
       
                
        // return ["error" => false, "msg" => "Otp sent successfuly. OTP is valid only for 5 Min."];

        if (request('username') != (null || "")) {


            $mem = DB::table('member_personal_info')
                ->where('memberPersonalMobileNumber', request('username'))
                ->where('status', "true")
                ->get()->first();
            if ($mem != (null || false)) {
                if (time() - strtotime($mem->otpTime) < 60) {
                    return ["error" => true, "msg" => "Otp not sent. please wait for " . 60 - (time() - strtotime($mem->otpTime))];
                }
                $otp = $this->intCodeRandom(5);
                $otpupdt = DB::table("member_personal_info")->where(["dairyId" => $mem->dairyId, "memberPersonalMobileNumber" => request('username')])
                    ->update(["otp" => $otp, "otpTime" => date("Y-m-d H:i:s")]);
                if ($otpupdt == false) {
                    return ["error" => true, "msg" => "An error has occured. OTP not sent."];
                }

                $sms = new Sms();
                $smreturn = $sms->send(
                    [
                        "message" => $otp . " is the one time password for your dairy login as Member. OTP is valid ony for 5 min. http://ksrdms.com",
                        "numbers" => request('username'),
                        "messageType" => "otp"
                    ], $mem->dairyId);
                
                if ($smreturn["error"]) {
                    return ["error" => true, "msg" => "Otp not sent. please try again."];
                } else {
                    return ["error" => false, "msg" => "Otp sent successfuly. OTP is valid only for 5 Min."];
                }
            }

            $colMan = DB::table('other_users')
                ->where('mobileNumber', request('username'))
                ->get()->first();

            if ($colMan != (null || false)) {

                if (time() - strtotime($colMan->otpTime) < 60) {
                    return ["error" => true, "msg" => "Otp not sent. please wait for " . 60 - (time() - strtotime($mem->otpTime))];
                }

                $otp = $this->intCodeRandom(5);
                $otpupdt = DB::table("other_users")->where(["dairyId" => $colMan->dairyId, "mobileNumber" => request('username')])
                    ->update(["otp" => $otp, "otpTime" => date("Y-m-d H:i:s")]);
                if ($otpupdt == false) {
                    return ["error" => true, "msg" => "An error has occured. OTP not sent."];
                }

                if ($colMan->userName == "DAIRYADMIN") {
                    $rl = "DAIRYADMIN";
                } else {
                    $rl = "Collection Manager";
                }

                $sms = new Sms();
                $smreturn = $sms->send(
                    [
                        "message" => $otp . " is the one time password for your dairy login as $rl. OTP is valid ony for 5 min.  http://ksrdms.com",
                        "numbers" => request('username'),
                        "messageType" => "otp"
                    ],
                    $colMan->dairyId
                );
                
                if ($smreturn["error"]) {
                    return ["error" => true, "msg" => "Otp not sent. please try again."];
                } else {
                    return ["error" => false, "msg" => "Otp sent successfuly. OTP is valid only for 5 Min."];
                }
            } else {
                return ["error" => true, "msg" => "No user found, registered with this mobile number."];
            }

        } else {
            return ["error" => true, "msg" => "Please enter number."];
        }
    }

    public function loginOtp()
    {
        if (request('username') == (null || "") || request('otp') == (null || "")) {
            return ["error" => true, "msg" => "Please enter OTP and mobile number."];
        }

        $loginResult = DB::table('member_personal_info')
            ->where('memberPersonalMobileNumber', request('username'))
            ->where('otp', request('otp'))
            ->where('status', "true")
            ->get()->first();

        if ($loginResult != (null || false)) {

            if (time() - strtotime($loginResult->otpTime) > 300) {
                return ["error" => true, "msg" => "Your OTP is expired. please try again."];
            }
            unset($loginResult->password);

            $dairy = DB::table("dairy_info")->where("id", $loginResult->dairyId)->get()->first();

            $loginResult->dairyName = $dairy->dairyName;
            $loginResult->society_code = $dairy->society_code;
            session()->put('loginUserInfo', $loginResult);
            session()->put('loginUserType', "member");

            // $this->createLoginCookie();
            Cookie::queue("loginSessCookie", session()->all(), 10080);

            $response = new Response(["error" => false, "msg" => "Successfully Login", "url" => url("member/dashboard")]);
            return $response;
        }

        $loginResult = DB::table('other_users')
            ->where('mobileNumber', request('username'))
            ->where('otp', request('otp'))
            ->where('status', "true")
            ->get()->first();

        if ($loginResult != (null || false)) {
            if (time() - strtotime($loginResult->otpTime) > 300) {
                return ["error" => true, "msg" => "Your OTP is expired. please try again."];
            }
            unset($loginResult->password);

            $dairy = DB::table("dairy_info")->where("id", $loginResult->dairyId)->get()->first();

            $loginResult->dairyName = $dairy->dairyName;
            $loginResult->society_code = $dairy->society_code;
            session()->put('loginUserInfo', $loginResult);
            session()->put('colMan', $loginResult);
            session()->put('loginUserType', "dairy");

            // $this->createLoginCookie();
            Cookie::queue("loginSessCookie", session()->all(), 10080);

            $response = new Response(["error" => false, "msg" => "Successfully Login", "url" => url("/")]);
            return $response;
        } else {
            return ["error" => true, "OTP does not match."];
        }
    }

    public function createLoginCookie()
    {
        Cookie::queue("loginSessCookie", session()->all(), 10080);
        // setcookie("loginSessCookie", session()->all(), time()+3600*24*7, '/', url());
    }

    public function change_password()
    {
        $this->middleware('Auth');

        return view("change_password");
    }

    public function checkPassword()
    {
        $this->middleware('Auth');

        if (request("pass") == null) {
            return ["error" => true, "msg" => "Please enter current password."];
        }

        $loginType = Session::get('loginUserType');

        switch ($loginType) {
            case "dairy":
                {
                    return $this->checkDairyPassword();
                    break;
                }
            case "member":
                {
                    return $this->checkMemberPassword();
                    break;
                }
            default:
                {
                    return ["error" => true, "msg" => "Error occured. you must login first."];
                }
        }

        return ["error" => true, "msg" => "Error occured. you must login first."];

    }

    public function checkMemberPassword()
    {
        $member = Session::get("loginUserInfo");

        $loginResult = DB::table('member_personal_info')
            ->where(['memberPersonalMobileNumber' => $member->memberPersonalMobileNumber, 'password' => request('pass'), 'status' => "true"])
            ->get()->first();

        if ($loginResult == (null || false)) {
            return ["error" => true, "msg" => "Please enter correct password. You entered wrong."];
        } else {
            return ["error" => false, "msg" => "Please enter new password."];
        }
    }

    public function checkDairyPassword()
    {
        $colMan = Session::get("colMan");

        $loginResult = DB::table('other_users')
            ->where('mobileNumber', $colMan->mobileNumber)
            ->where('password', request('pass'))
            ->get()->first();

        if ($loginResult == (null || false)) {
            return ["error" => true, "msg" => "Please enter correct password. You entered wrong."];
        } else {
            return ["error" => false, "msg" => "Please enter new password."];
        }
    }

    public function setNewPassword()
    {
        $this->middleware('Auth');

        if (request("pass") == null) {
            return ["error" => true, "msg" => "Some fields are missing in form, please try again."];
        }

        if (request("npass") == null || request("cnpass") == null) {
            return ["error" => true, "msg" => "Please enter new password and confirm password."];
        }

        if (request("npass") != request("cnpass")) {
            return ["error" => true, "msg" => "New password and confirm password must be same."];
        }

        $loginType = Session::get('loginUserType');

        switch ($loginType) {
            case "dairy":
                {
                    return $this->setDairyPasswordNew();
                    break;
                }
            case "member":
                {
                    return $this->setMemberPasswordNew();
                    break;
                }
            default:
                {
                    return ["error" => true, "msg" => "Error occured. you must login first."];
                }
        }

    }

    public function setMemberPasswordNew()
    {
        $member = Session::get("loginUserInfo");

        $loginResult = DB::table('member_personal_info')
            ->where(['memberPersonalMobileNumber' => $member->memberPersonalMobileNumber, 'password' => request('pass'), 'status' => "true"])
            ->get()->first();

        if ($loginResult == (null || false)) {
            return ["error" => true, "msg" => "Please enter correct password. You entered wrong."];
        } else {

            $up = DB::table('member_personal_info')
                ->where('id', $member->id)
                ->update(["password" => request("npass")]);

            $u = DB::table("app_logins")->where(["dairyId" => $member->dairyId, "userType" => "4", "userId" => $member->id])
                ->update(["token_key" => "Token_is_reseted_please_login_again"]);

            Session::flash('msg', 'Your password has been successfully changed.');
            Session::flash('alert-class', 'alert-success');
            return ["error" => false, "msg" => "Your password has been successfully changed."];
        }
    }

    public function setDairyPasswordNew()
    {
        $colMan = Session::get("colMan");

        $loginResult = DB::table('other_users')
            ->where('mobileNumber', $colMan->mobileNumber)
            ->where('password', request('pass'))
            ->get()->first();

        if ($loginResult == (null || false)) {
            return ["error" => true, "msg" => "Please enter correct password. You entered wrong."];
        } else {

            $up = DB::table('other_users')
                ->where('id', $colMan->id)
                ->update(["password" => request("npass")]);

            $u = DB::table("app_logins")->where(["dairyId" => $colMan->dairyId, "userType" => "1", "userId" => $colMan->id])
                ->update(["token_key" => "Token_is_reseted_please_login_again"]);

            Session::flash('msg', 'Your password has been successfully changed.');
            Session::flash('alert-class', 'alert-success');
            return ["error" => false, "msg" => "Your password has been successfully changed."];
        }
    }

    /* user logout */
    public function logoutUser()
    {
        session()->forget('loginUserInfo');
        session()->forget('loginUserType');
        session()->forget('subMenuArray');
        session()->forget('dairyInfo');

        session()->flush();

        Cookie::queue("loginSessCookie", session()->all(), 0);

        Session::flash('msg', 'Successfully logout.');
        Session::flash('alert-class', 'alert-success');
        return redirect('/dairy-login');
    }

    public function intCodeRandom($length = 8)
    {
        $intMin = (10 ** $length) / 10; // 100...
        $intMax = (10 ** $length) - 1; // 999...

        $codeRandom = mt_rand($intMin, $intMax);

        return $codeRandom;
    }

    public function saveDairyAndPay2(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "pricePlanId" => 'bail|required',
            "name" => 'bail|required',
            "code" => 'bail|required',
            "mobile" => 'bail|required|numeric|digits:10',
            "owname" => 'bail|required',
            "owmobile" => 'bail|required|numeric|digits:10',
            "owemail" => 'bail|required|email',
            "owaddress" => 'bail|required',
            "owstate" => 'bail|required',
           // "owdistrict" => 'bail|required',
            'owpin' => "bail|required",
            'priceMonthlyOrYearly' => "bail|required|in:monthly,yearly",
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => true, "msg" => $validator->errors()->first()]);
        }

        $req->createBySuperAdmin = 0;

        $admin = new superAdmin();

        $res = $admin->newDairySetup($req);

        if (!$res['error']) {
            $data = [
                "sysName" => "DMS",
                "dairyAdmin" => "Vikram Saraswat",
                "user" => [
                    "username" => $req->owname,
                    "id" => $res['id'],
                    "pass" => $res['pass'],
                ],
                "loginlink" => url("dairy-login"),
            ];

            Mail::send('emails.welcome', $data, function ($m) use ($req) {
                $m->from('director@ksrservices.in', 'KSR Services Admin');
                $m->to($req->owemail, $req->owname)->subject('Welcome to DMS!');
            });

            $sms = new Sms();
            $sms->send(
                [
                    "message" => "Thanks for choosing us,
                    Dairy registered successfuly,
                    Login: " . url("dairy-login") . "
                    ID: " . $res['id'] . "
                    pass: " . $res['pass'] . "
                    Thanks!",
                    "numbers" => $req->num,
                    "messageType" => "dairyRegistration"
                ], $res['dairyId']);
        }

        if (request("submit") != 'buy') {
            return $res;
        } else {
            return $this->ccavRequestHandler();
        }
    }

    public function proceedToCheckOut()
    {
        $this->middleware('Auth');

        $_POST['merchant_param1'] = request('priceMonthlyOrYearly');
        $_POST['merchant_param2'] = request('pricePlanId');
        return $this->ccavRequestHandler();

        // $colMan = Session::get("colMan");

        // if(request('priceMonthlyOrYearly')){
        //     DB::table('subscribe')->where(["dairyId" => $colMan->id])
        //             ->update(["planType" => request('priceMonthlyOrYearly'),
        //                     "pricePlanId" => request('pricePlanId')]);
        //     return $this->ccavRequestHandler();
        // }else{
        //     Session::flash('msg', 'There is an error occured.');
        //     Session::flash('alert-class', 'alert-danger');
        //     return redirect("buy.renewPage");
        // }
    }

    public function ccavRequestHandler()
    {
        $this->middleware('Auth');

        $merchant_data = '';
        $working_key = '91957048B1CB6499FEE654426B2F84C3'; //Shared by CCAVENUES
        $access_code = 'AVOK81FK42AF50KOFA'; //Shared by CCAVENUES

        foreach ($_POST as $key => $value) {
            $merchant_data .= $key . '=' . $value . '&';
        }
        // dd($merchant_data);
        $encrypted_data = encrypt_($merchant_data, $working_key); // Method for encrypting the data.
        $ord = time() . $res['dairyId'];

        return view("buy.ccavRequestHandler",
            ["encrypted_data" => $encrypted_data, "working_key" => $working_key, "access_code" => $access_code, "order_id" => $ord]);
    }

    public function ccavResponseHandler()
    {
        $this->middleware('Auth');

        $workingKey = '91957048B1CB6499FEE654426B2F84C3'; //Working Key should be provided here.
        $encResponse = $_POST["encResp"]; //This is the response sent by the CCAvenue Server
        $rcvdString = decrypt_($encResponse, $workingKey); //Crypto Decryption used as per the specified working key.
        $order_status = "";

        // $rcvdString = 'order_id=15433181331&tracking_id=107477737555&bank_ref_no=20181127111212800110168286447112092&order_status=Success&failure_message=&payment_mode=Wallet&card_name=Paytm&status_code=null&status_message=Txn Success¤cy=INR&amount=1.0&billing_name=Yatindra soni&billing_address=72/171, krishna marg, near Kake di hatti, patel marg&billing_city=Mansarovar&billing_state=Rajasthan&billing_zip=302020&billing_country=India&billing_tel=9509751250&billing_email=yatindrasoni13013@gmail.com&delivery_name=Yatindra soni&delivery_address=72/171, krishna marg, near Kake di hatti, patel marg&delivery_city=Mansarovar&delivery_state=Rajasthan&delivery_zip=302020&delivery_country=India&delivery_tel=9509751250&merchant_param1=monthly&merchant_param2=8&merchant_param3=&merchant_param4=&merchant_param5=&vault=N&offer_type=null&offer_code=null&discount_value=0.0&mer_amount=1.0&eci_value=null&retry=N&response_code=0&billing_notes=Testing&trans_date=08/12/2018 17:00:25&bin_country=';
        $decryptValues = explode('&', $rcvdString);
        $dataSize = sizeof($decryptValues);

        for ($i = 0; $i < $dataSize; $i++) {
            $t = explode('=', $decryptValues[$i]);
            $info[$t[0]] = $t[1];
        }

        if (isset($info) && $info['order_status'] == "Success" && $info['order_id'] != null) {

            $dairyId = substr($info['order_id'], 10);

            $dairy = DB::table('dairy_info')
                ->where('dairy_info.id', $dairyId)
                ->select("*", "city.name as cityName", "states.name as stateName")
                ->leftjoin("city", "city.id", "=", "dairy_info.city")
                ->leftjoin("states", "states.id", "=", "dairy_info.state")
                ->get()->first();

            $dairy_propritor_info = DB::table('dairy_propritor_info')
                ->where('dairy_propritor_info.dairyId', $dairyId)
                ->get()->first();

            $plan = DB::table('subscription_plan')->where(["id" => $info['merchant_param2']])->get()->first();

            $delivery_address = $info["delivery_name"] . " " . $info["delivery_address"] . " " . $info["delivery_city"] . " " . $info["delivery_state"] . " "
                . $info["delivery_country"] . " " . $info["delivery_zip"] . " " . $info["delivery_tel"] . " ";
            $billing_address = $info["billing_name"] . " " . $info["billing_address"] . " " . $info["billing_city"] . " " . $info["billing_state"] . " "
                . $info["billing_country"] . " " . $info["billing_zip"] . " " . $info["billing_tel"] . " " . $info["billing_email"] . " ";

            $subscribe = DB::table('subscribe')->where(["dairyId" => $dairyId])->get()->first();
            if ($subscribe == null) {
                return ["error" => true, "msg" => "error while processing your request."];
            }

            $info['trans_date'] = str_replace("/", "-", $info['trans_date']);

            $pdata = [
                "dairyId" => $dairyId,
                "amount" => $info['amount'],
                "trans_date" => date("Y-m-d H:i:s", strtotime($info['trans_date'])),
                "pricePlanId" => $subscribe->pricePlanId,
                "tracking_id" => $info['tracking_id'],
                "status_message" => $info['status_message'],
                "payment_mode" => $info['payment_mode'],
                "offer_code" => $info['offer_code'],
                "offer_type" => $info['offer_type'],
                "discount_value" => $info['discount_value'],
                "card_name" => $info['card_name'],
                "delivery_address" => $delivery_address,
                "billing_address" => $billing_address,
                "bank_ref_no" => $info['bank_ref_no'],
                "mer_amount" => $info['mer_amount'],
                "created_at" => date("Y-m-d H:i:s"),
                "invoiceFile" => "",
            ];

            DB::beginTransaction();
            $paymentId = DB::table("payments")->insertGetId($pdata);

            $invoiceFile = $this->createInvoice($info, $dairy, $plan, $paymentId);

            $t = DB::table("payments")->where(["id" => $paymentId])->update(["invoiceFile" => $invoiceFile]);

            if (strtotime($subscribe->expiryDate) > time()) {
                $oldExp = $subscribe->expiryDate;

                $datediff = strtotime($subscribe->expiryDate) - strtotime($subscribe->trialEndDate);
                if ($datediff > 0) {
                    $day = round($datediff / (60 * 60 * 24));
                } else {
                    $day = 0;
                }

            } else {
                $oldExp = date("Y-m-d H:i:s");
                $day = 0;
            }

            if ($subscribe->planType == "monthly") {
                $expDt = date("Y-m-d H:i:s", strtotime($oldExp . " +1 month"));
            } else {
                $expDt = date("Y-m-d H:i:s", strtotime($oldExp . " +1 year"));
            }

            // $help = new \App\APIHelperModel();
            // $subscribe = $help->updateTrialTime($subscribe);
            $trialEndDate = date("Y-m-d H:i:s", strtotime($subscribe->trialEndDate . " +" . $day));

            $subUp = DB::table('subscribe')->where("dairyId", $dairyId)->update([
                "paymentId" => $paymentId,
                "planType" => $info['merchant_param1'],
                "pricePlanId" => $info['merchant_param2'],
                "dateOfSubscribe" => date("Y-m-d H:i:s", strtotime($info['trans_date'])),
                "dateOfPayment" => date("Y-m-d H:i:s", strtotime($info['trans_date'])),
                "expiryDate" => $expDt,
                "isPaymentDone" => 1,
                "amount" => $info['amount'],
                "trialEndDate" => $trialEndDate,
                "updated_at" => date("Y-m-d H:i:s"),
            ]);

            if (!$subUp) {
                DB::rollback();
                Session::flash('msg', 'Error while updating payment status.');
                Session::flash('alert-class', 'alert-danger');
                return view("buy.ccavResponseHandler", ["order_status" => $order_status,
                    "dataSize" => $dataSize,
                    "decryptValues" => $decryptValues,
                ]);
            }

            DB::table("dairy_info")->where(["id" => $dairyId])->update([
                "remainingSms" => $plan->noOfSms,
            ]);

            DB::commit();

            if ($info['merchant_param1'] == "monthly") {
                $adv = "30 days Subscription";
            } else {
                $adv = "1 year Subscription";
            }
            $data = [
                "email" => $dairy_propritor_info->dairyPropritorEmail,
                "name" => $dairy_propritor_info->dairyPropritorName,
                "dairyAdmin" => "Vikram Saraswat",
                "planName" => $plan->name,
                "planType" => $info['merchant_param1'],
                "price" => "&#8377; " . $info['amount'],
                "advantage" => $adv,
                "expieryDate" => $expDt,
                "invoiceFile" => $invoiceFile,
                "user" => [
                    "username" => $dairy_propritor_info->dairyPropritorName,
                ],
            ];

            $this->sendInvoice($data);

            Session::flash('msg', 'Your Payment has been done.');
            Session::flash('alert-class', 'alert-success');
        } else {
            Session::flash('msg', 'Your Payment Failed.');
            Session::flash('alert-class', 'alert-danger');
            return view("buy.ccavResponseHandler",
                ["order_status" => $order_status,
                    "dataSize" => $dataSize,
                    "decryptValues" => $decryptValues,
                ]);
        }

        return view("buy.ccavResponseHandler",
            ["order_status" => $order_status,
                "dataSize" => $dataSize,
                "decryptValues" => $decryptValues,
            ]);
    }

    public function checkSubscription()
    {
        $this->middleware('Auth');

        $help = new \App\APIHelperModel();
        return $help->checkSubscription(session()->get('loginUserInfo')->dairyId);
    }

    public function expiredPage()
    {
        $this->middleware('Auth');

        $loginUserInfo = session()->get('loginUserInfo');
        $s = DB::table("subscribe")->where(["dairyId" => $loginUserInfo->dairyId])
            ->join("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")->get()->first();

        return view("buy.expiredPage", ["d" => $s]);
    }

    public function renewPage()
    {
        
        $r = \App\APIHelperModel::isAuthReq();
        // if ($r['status_code'] != "200") {
        //     Session::flash('msg', 'You are not authorised to access this page, please login first.');
        //     Session::flash('alert-class', 'alert-danger');
        //     return redirect("dairy-login");
        // }

        $this->middleware('Auth');

        $colMan = session()->get('colMan');
        $loginType = session()->get('loginUserType');
        $dairy = session()->get('dairyInfo');

        if ($loginType != 'dairy') {
            return view("contactDairyadmin");
        }

        $s = DB::table("subscribe")->where(["dairyId" => $dairy->id])
            ->join("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")->get()->first();

        $pp = DB::table("subscription_plan")->get();
        $states = DB::table('states')->get();

        $priceplan = [];
        if (strtolower($s->name) == "deluxe") {
            foreach ($pp as $p) {
                if (strtolower($p->name) == "standard") {
                    continue;
                }
                $priceplan[] = $p;
            }
        }

        if (strtolower($s->name) == "super deluxe") {
            foreach ($pp as $p) {
                if (strtolower($p->name) == "standard") {
                    continue;
                }
                if (strtolower($p->name) == "deluxe") {
                    continue;
                }
                $priceplan[] = $p;
            }
        }

        if (strtolower($s->name) == "standard") {
            foreach ($pp as $p) {
                $priceplan[] = $p;
            }
        }

        $data = [
            "merchant_id" => "196388",
            "order_id" => time() . $colMan->dairyId,
            "cancel_url" => url('sa/ccavResponseHandler'),
            "redirect_url" => url('sa/ccavResponseHandler'),
            "states" => $states,
            "pp" => $priceplan,
            "dairy" => $dairy,
        ];

        return view("buy.renewPage", $data);
    }

    public function subscriptionHistory()
    {
        $this->middleware('Auth');

        $loginUserInfo = session()->get('loginUserInfo');
        $s = DB::table("subscribe")->where(["dairyId" => $loginUserInfo->dairyId])
            ->join("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")
            ->get()->first();

        $payments = DB::table("payments")->where(["dairyId" => $loginUserInfo->dairyId])
            ->join("subscription_plan", "subscription_plan.id", "=", "payments.pricePlanId")
            ->get();
        return view("buy.subscriptionHistory", ["d" => $s, "payments" => $payments]);
    }

    public function createInvoice($info, $dairy, $plan, $paymentId)
    {
        // return base_path();

        $cgst_per = 0;
        $sgst_per = 0;
        $igst_per = 18;
        $igst = 0;
        $sgst = 0;
        $cgst = 0;

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $info['amountinwords'] = ucwords($numberTransformer->toWords($info['amount']));

        $rate = number_format(($info['amount'] / (1 + ($igst_per / 100))), 2, ".", "");
        $igst = number_format($info['amount'] - $rate, 2, ".", "");

        if ($dairy->state == 13) { ///if state is hariyana
            $sgst_per = 9;
            $cgst_per = 9;
            $igst_per = 0;

            $sgst = $cgst = number_format($igst / 2, 2, ".", "");
            $igst = 0;
        }

        $pdfdata = ["dairy" => $dairy, "payment" => (object) $info, 'plan' => (object) $plan, "rate" => $rate, "paymentId" => $paymentId,
            "cgst_per" => $cgst_per, "sgst_per" => $sgst_per, "igst_per" => $igst_per, "cgst" => $cgst, "sgst" => $sgst, "igst" => $igst];

        $pdfName = "download/pdfs/invoicepdf_" . md5($info["order_id"]) . "_" . $info["order_id"] . ".pdf";
        // return view("pdf.invoice", $data);
        $pdf = PDF::setOptions(['dpi' => 250, 'defaultFont' => 'sans-serif'])->loadView('pdf.invoice', $pdfdata);
        $pdf->save($pdfName);

        return $pdfName;
    }

    public function sendInvoice($data)
    {
        Mail::send('emails.invoice', $data, function ($m) use ($data) {
            $m->from('director@ksrservices.in', 'KSR Services Admin');
            $m->to($data['email'], $data['name'])->subject('DMS Invoice');
        });
    }

    public function download()
    {
        $this->middleware('Auth');

        return response()->download(request("file"));
    }

    public function contactUs()
    {
        $name = "";
        $email = "";
        $phone = "";
        $dairyName = "";
        $queryFrom = "Visitor";
        $layout = "layouts.app";

        if (session()->get('loginUserType') == "dairy") {
            $name = session()->get('loginUserInfo')->userName;
            $phone = session()->get('loginUserInfo')->mobileNumber;
            $email = session()->get('loginUserInfo')->userEmail;
            $dairyName = session()->get('dairyInfo')->dairyName;
            $queryFrom = "Dairy";
            $layout = "theme.default";
        }
        if (session()->get('loginUserType') == "member") {
            $name = session()->get('loginUserInfo')->memberPersonalName;
            $phone = session()->get('loginUserInfo')->memberPersonalMobileNumber;
            $email = session()->get('loginUserInfo')->memberPersonalEmail;
            $dairyName = session()->get('loginUserInfo')->dairyName;
            $queryFrom = "Member";
            $layout = "member.layout";
        }
        if (session()->get('loginUserType') == "plant") {
            $name = session()->get('loginUserInfo')->headName;
            $phone = session()->get('loginUserInfo')->mobile;
            $email = session()->get('loginUserInfo')->email;
            $queryFrom = "Plant";
            $layout = "plant.layout";
        }

        $data = ["name" => $name, "email" => $email, "phone" => $phone, "dairyName" => $dairyName, "queryFrom" => $queryFrom, "layout" => $layout];

        return view("contactUs", $data);
    }

    public function contactMail()
    {
        $recaptcha = $_POST['g-recaptcha-response'];
        $res = $this->reCaptcha($recaptcha);
        
        if(!isset($res['success']) || $res['success'] != 1 ){
            
            Session::flash('msg', 'Captcha not verified! Please try again.');
            Session::flash('alert-class', 'alert-danger');
            
            return redirect("contactUs");
        }
        
        exit();
    
        $data = ["name" => request("name"),
            "phone" => request("phone"),
            "email" => request("email"),
            "dairyName" => request("dairyName"),
            "msg" => request("message"),
            "queryFrom" => request("queryFrom")];

        $sub = "DMS: Query recieved from " . request("queryFrom");

        if (request("dairyName") != null) {
            $sub .= ", Dairy Name: " . request("dairyName");
        }

        $data["sub"] = $sub;

        Mail::send('emails.contactUs', $data, function ($m) use ($data) {
            $m->from('director@ksrservices.in', 'KSR Services Admin');
            $m->to('director@ksrservices.in', 'KSR Services Admin')->subject($data['sub']);
        });

        Session::flash('msg', 'Your message has been sent.');
        Session::flash('alert-class', 'alert-success');

        return redirect("contactUs");
    }

    public function testInvoicePdf()
    {
        // return base_path();
        $info['amount'] = 51790;
        $info['billing_address'] = "Banwala";
        $info['billing_city'] = "Sirsa";
        $info['billing_zip'] = "125103";
        $info['billing_state'] = "Haryana";
        $info['billing_country'] = "India";
        $info['billing_tel'] = "9991707888";
        $info['billing_email'] = "sendtovikramsarswat@gmail.com";
        $info['billing_name'] = "The Mahila Banwala MPCS";
        $info['trans_date'] = "2019-01-03 10:02:01.427";

        $info['merchant_param1'] = "monthly";
        $dairy['dairyName'] = "The Mahila Banwala MPCS";
        $dairy['cityName'] = "Sirsa";
        $dairy['stateName'] = "Haryana (HR)";
        $dairy['state'] = 13;
        $dairy['pincode'] = "125103";
        $plan['name'] = "Delux";
        $paymentId = 1;

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $info['amountinwords'] = ucwords($numberTransformer->toWords($info['amount']));

        $cgst_per = 0;
        $sgst_per = 0;
        $igst_per = 18;
        $igst = 0;
        $sgst = 0;
        $cgst = 0;

        $rate = number_format(($info['amount'] / (1 + ($igst_per / 100))), 2, ".", "");
        $igst = number_format($info['amount'] - $rate, 2, ".", "");

        if (true) { ///if state is hariyana
            $sgst_per = 9;
            $cgst_per = 9;
            $igst_per = 0;

            $sgst = $cgst = number_format($igst / 2, 2, ".", "");
            $igst = 0;
        }

        $pdfdata = ["dairy" => (object) $dairy, "payment" => (object) $info, 'plan' => (object) $plan, "rate" => $rate, "paymentId" => $paymentId,
            "cgst_per" => $cgst_per, "sgst_per" => $sgst_per, "igst_per" => $igst_per, "cgst" => $cgst, "sgst" => $sgst, "igst" => $igst];

        $pdfName = "download/pdfs/invoicepdf_" . md5($info["order_id"]) . "_" . $info["order_id"] . ".pdf";

        // $pdf = PDF::setOptions(['dpi' => 250, 'defaultFont' => 'sans-serif'])->loadView('pdf.invoice', $pdfdata);
        // return $pdf->download('invoice.pdf');
        return view('pdf.invoice', $pdfdata);

    }

    public function markasReadNotification()
    {
        // return request()->all();
        if (request('notiIds')) {
            $r = DB::table('notifications')->whereIn('id', request('notiIds'))->update(["opened" => 1]);
            if ($r) {
                return ["error" => true];
            }
            return ["error" => false];
        } else {
            return ["error" => false];
        }
    }


    public function termsCond()
    {
        return view("buy.termscon");
    }
    public function privacyPolicy()
    {
        return view("buy.privacyPolicy");
    }
    public function aboutUs()
    {
        return view("buy.aboutUs");
    }

    public function refund()
    {
        return view("buy.refundcancel");
    }
    public function disclaimer()
    {
        return view("buy.disclaimer");
    }

    public function testBulkSms()
    {
        // $sms = new Sms();
        //     echo $rs = $sms->send(["message" => "Test",

        //         "numbers" => 9509751250], 1);

        $message = urlencode("kjshd%0Awekr 
        
        whe wer wer
        sdfrewrwe");


        // $url = "http://login.yourbulksms.com/api/sendhttp.php?authkey=11249ABPPIYXMUkMH5c8f350c&mobiles=9509751250&message=Hello%20Test%20SMS%20from%20KSRDMS&sender=KSRDMS&route=4&country=IN";
        $url = "http://login.yourbulksms.com/api/sendhttp.php?authkey=11249ABPPIYXMUkMH5c8f350c&mobiles=9294676338&message=$message&sender=KSRDMS&route=4&response=json&country=IN";
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 2,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_URL => $url,
        ));

        echo curl_exec($curl);
        $err = curl_error($curl);
        echo "error: ". $err;
    }
    
    public function reCaptcha($recaptcha){
      $secret = '6LfwRIAaAAAAAEqMWs1qTvcLRmnzuyPBTCXSGEKk';
      $ip = $_SERVER['REMOTE_ADDR'];
    
      $postvars = array("secret"=>$secret, "response"=>$recaptcha, "remoteip"=>$ip);
      $url = "https://www.google.com/recaptcha/api/siteverify";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
      $data = curl_exec($ch);
      curl_close($ch);
    
      return json_decode($data, true);
    }

}

