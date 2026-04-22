<?php

namespace App\Http\Controllers;

use App\Sms;
use App\superAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mail;

include('Crypto.php');

class SuperAdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('SuperAdmin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     return view("spradmin.login");
    // }

    public function showDashbord(Request $request)
    {
        // echo "asdfasd";
        // die;
        return redirect('sa/dairyList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dairySetup()
    {
        $states = DB::table('states')->get();

        $subsPlans = DB::table("subscription_plan")->get();

        return view("spradmin.dairysetup", ["states" => $states, "subsPlans" => $subsPlans, "activepage" => "dairySetup"]);
    }

    // add dairy admin user function
    public function create(Request $req)
    {
        // return $request->all();

        $validator = Validator::make(request()->all(), [
            "pricePlanId" => 'bail|required',
            "name" => 'bail|required',
            "code" => 'bail|required',
            "mobile" => 'required|digits:10|numeric',
            "owname" => 'bail|required',
            "owmobile" => 'required|digits:10|numeric',
            "owemail" => 'bail|required|email',
            "owaddress" => 'bail|required',
            "owstate" => 'bail|required',
            "owdistrict" => 'bail|required',
            'owpin' => "bail|required",
            'priceMonthlyOrYearly' => "bail|required|in:monthly,yearly",
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => true, "msg" => $validator->errors()->first()]);
        }

        $admin = new superAdmin();

        $res = $admin->newDairySetup($req);

        if (!$res['error']) {
            $data = [
                "sysName" => "DMS",
                "dairyAdmin" => "Yatindra Soni",
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
                    "message" => "Thanks for choosing us,".$newLine.
                    "Dairy registered successfuly,".$newLine.
                    "Login: " . url("/") .$newLine.
                    "ID: " . $res['id'] . $newLine.
                    "Password: " . $res['pass'] .$newLine.$newLine.
                    "Thanks!",
                    "numbers" => $req->owmobile,
                    "messageType" => "dairyRegistration"
                ], $res['dairyId']);
        }

        return $res;
        // return redirect('/my-home');
    }


    public function showMilkPlants()
    {
        $filter = "all";
        
        $q = DB::table('milk_plants');
        if(request('filter') == 'main'){
            $filter = 'main';
            $q = $q->where("milk_plants.isMainPlant", "1");
            $filtermainPlantId = '';
        }
        if(request('filter') == 'mainPlant'){
            $filter = 'mainPlant';
            $q = $q->where("milk_plants.parentPlantId", request('filtermainPlantId'));
            $filtermainPlantId = request('filtermainPlantId');
        }

        $plants = $q
                ->where(["milk_plants.status" => "true"])
                ->select("*", "milk_plants.id as id", "city.name as city", "states.name as state")
                ->leftjoin("milk_plant_head", "milk_plant_head.plantId", "=", "milk_plants.id")
                ->leftjoin("city", "city.id", "=", "milk_plants.city")
                ->leftjoin("states", "states.id", "=", "milk_plants.state")
                ->get();

        $mainPlants = DB::table('milk_plants')->where(["isMainPlant" => 1])->get();

        return view("spradmin.milkPlants", ["plants" => $plants, "filter" => $filter, "mainPlants" => $mainPlants,
                                "filtermainPlantId" => $filtermainPlantId, "activepage" => "milkPlants"]);
    }

    public function addNewPlant()
    {
        $mainPlants = DB::table('milk_plants')->where(["isMainPlant" => 1])->get();
        $states = DB::table('states')->get();
        return view('spradmin.milkPlantSetup', ['states' => $states, "mainPlants" => $mainPlants, "activepage"=>"milkPlants"]);
    }

    public function newMilkPlantAdd()
    {
        $validatedData = request()->validate([
            'plantName' => 'required',
            'contactNumber' => 'required|numeric|digits:10',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pinCode' => 'required|numeric',
            'plantHeadName' => 'required',
            'email' => 'required|email',
            'mobileNumber' => 'required|numeric|digits:10',
        ]);
        
        $submitClass = new \App\milkPlant();
        $res = $submitClass->milkPlantSubmit();
        if($res['error']){
            Session::flash('msg', $res['msg']);
            Session::flash('alert-class', 'alert-danger');
            return redirect('sa/addNewPlant')->withInput();
        }else{
            $newLine = "%0A";
            $msg = "Dear ".request("plantHeadName").$newLine."You have successfully registered with DMS.".
                        $newLine." Now you can login with us. ".$newLine."ID: "
                        .request('mobileNumber').$newLine."Password: ".$res['pass'].$newLine." Login: ". url();

            Session::flash('msg', "Milk Plant registered successfully.");
            Session::flash('alert-class', 'alert-success');
            return redirect('sa/milkPlants');
        }
    }

    public function milkPlantDelete()
    {
        // return request()->all();
        if(request('action') != "delete" || request('plantId') == null){
            return ["error" => true, "msg" => "Error!"];
        }

        DB::beginTransaction();
        $plant = DB::table('milk_plants')->where(['id' => request('plantId')])->update([
            "status" => "false",
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        $head = DB::table('milk_plant_head')->where(['plantId' => request('plantId')])->update([
            "status" => "false",
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        if(!$plant){
            DB::rollback();
            return ["error" => true, "msg" => "Error in deleting."];
        }else{
            DB::commit();
            return ["error" => false, "msg" => "Deleted successfully."];
        }
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
     * @param  \App\superAdmin  $superAdmin
     * @return \Illuminate\Http\Response
     */
    public function dairyList()
    {

        $sq = DB::table("subscribe");

        // if(request("filter") == null|| request("filter") == "all"){
        //     ;
        // }else{
        //     $sq = $sq->where("isActivated", 1);
        // }

        if (request("filter") == "recent") {
            $sq = $sq->whereBetween("dateOfSubscribe", [date("Y-m-d", strtotime("-15 days")), date("Y-m-d", strtotime("+1 day"))]);
            $sq = $sq->where("isActivated", 1);
        }
        if (request("filter") == "duePayment") {
            $sq = $sq->whereNotIn("isPaymentDone", [1]);
            $sq = $sq->where("isActivated", 1);
        }
        if (request("filter") == "expired") {
            $sq = $sq->where("expiryDate", "<", date("Y-m-d H:i:s"));
            $sq = $sq->where("isActivated", 1);
        }
        if (request("filter") == "trial") {
            $sq = $sq->where("expiryDate", ">=", date("Y-m-d H:i:s"));
            $sq = $sq->where("isPaymentDone", 0);
            $sq = $sq->where("isActivated", 1);
        }
        if (request("filter") == "deactivated") {
            $sq = $sq->where("isActivated", 0);
        }

        $sq = $sq->join("dairy_info", "dairy_info.id", "=", "subscribe.dairyId")
            ->select("dairy_info.id as id", "dairy_info.remainingSms as remainingSms", "createBySuperAdmin", "dairyName", "dairy_propritor_info.password as pass", "mobile", "society_code",
                "dairy_info.created_at as regTime", "dairyPropritorName as owname", "PropritorMobile as owmobile", "dairyPropritorEmail as owemail",
                "subscription_plan.name as planName", "subscription_plan.trial_time as trial_time", "subscribe.dateOfSubscribe as dateOfSubscribe",
                "subscribe.isPaymentDone as isPaymentDone", "subscribe.isActivated as isActivated");
        $sq = $sq->join("subscription_plan", "subscribe.pricePlanId", "=", "subscription_plan.id");

        $dairies = $sq->join('dairy_propritor_info', 'dairy_info.id', '=', 'dairy_propritor_info.dairyId')
            ->orderBy("subscribe.created_at")
            ->get();

        return view("spradmin.dairiesList", ["dairies" => $dairies, "filter" => request("filter"), "activepage" => "dairyList"]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\superAdmin  $superAdmin
     * @return \Illuminate\Http\Response
     */
    public function edit(superAdmin $superAdmin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\superAdmin  $superAdmin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, superAdmin $superAdmin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\superAdmin  $superAdmin
     * @return \Illuminate\Http\Response
     */
    public function destroy(superAdmin $superAdmin)
    {
        //
    }

    public function test()
    {
        return date("Y-m-d", strtotime("2018-11-27 12:10:05 +1 year"));

        $str = 'order_id=1543318133&tracking_id=107477737555&bank_ref_no=20181127111212800110168286447112092&order_status=Success&failure_message=&payment_mode=Wallet&card_name=Paytm&status_code=null&status_message=Txn Success¤cy=INR&amount=1.0&billing_name=Yatindra soni&billing_address=72/171, krishna marg, near Kake di hatti, patel marg&billing_city=Mansarovar&billing_state=Rajasthan&billing_zip=302020&billing_country=India&billing_tel=9509751250&billing_email=yatindrasoni13013@gmail.com&delivery_name=Yatindra soni&delivery_address=72/171, krishna marg, near Kake di hatti, patel marg&delivery_city=Mansarovar&delivery_state=Rajasthan&delivery_zip=302020&delivery_country=India&delivery_tel=9509751250&merchant_param1=&merchant_param2=&merchant_param3=&merchant_param4=&merchant_param5=&vault=N&offer_type=null&offer_code=null&discount_value=0.0&mer_amount=1.0&eci_value=null&retry=N&response_code=0&billing_notes=Testing&trans_date=27/11/2018 17:00:25&bin_country=';

        $decryptValues = explode('&', $str);
        $dataSize = sizeof($decryptValues);
        for ($i = 0; $i < $dataSize; $i++) {
             $t = explode('=', $decryptValues[$i]);
             $information[$t[0]] = $t[1];
            // if ($i == 3) {
            //     $order_status = $information[1];
            // }
        }
        if(isset($information)){
            return json_encode($information);
        }else{
            return "sfsdf";
        }
    }

    public function testSms(Request $req)
    {
        $sms = new Sms();
        return $sms->send(["message" => "Hello from DMS", "numbers" => $req->num]);
    }

    public function testEmail(Request $req)
    {

        $data = [
            "sysName" => "DMS",
            "dairyAdmin" => "Yatindra Soni",
            "user" => [
                "username" => "John Doe",
                "id" => "982739586",
                "pass" => "678ads87",
            ],
            "loginlink" => url("dairy-login"),
        ];

        Mail::send('emails.welcome', $data, function ($m) use ($req) {
            $m->from('dms.online.2018@gmail.com', 'DMS ADMIN');
            $m->to("Yannisoni@gmail.com", "Yanni Soni")->subject('Welcome to DMS!');
        });

        return view("emails.welcome", $data);
    }

    public function pricePlan()
    {
        $pp = DB::table("subscription_plan")->get();

        return view("spradmin.pricePlan", ["pp" => $pp, "activepage" => "pricePlan"]);
    }

    public function createPricePlan(Request $req)
    {
        // return [$req->all()];

        if ($req->unlimitMem) {
            $mem = 5000;
        } else {
            $mem = $req->noOfMem;
        }

        $sp = db::table("subscription_plan")->insertgetid([
            "status" => "true",
            "name" => $req->name,
            "noOfMem" => $mem,
            "noOfSms" => $req->noOfSms,
            "monthlyPrice" => $req->pricePM,
            "yearlyPrice" => $req->pricePY,
            "is_trial" => 1,
            "trial_time" => 30,
            "created_at" => date("Y-m-d H:i:s"),
        ]);

        if ($sp) {
            Session::flash('msg', 'Plan created succeessfuly.');
            Session::flash('alert-class', 'alert-success');
            return redirect("sa/pricePlan");
        } else {
            Session::flash('msg', 'There is an error occured.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("sa/pricePlan");
        }
    }

    public function deactivateDairy()
    {
        if (request('dairyId') == null || request('action') == null) {
            return ["error" => true, "msg" => "Dairy id required."];
        }

        if (request("action") == "activate") {
            $d = DB::table("subscribe")->where(["dairyId" => request("dairyId")])->update(["isActivated" => 1]);
            if ($d) {
                return ["error" => false, "msg" => "Dairy deactivated successfully."];
            } else {
                return ["error" => true, "msg" => "an error has occured while deactivate dairy."];
            }
        } else {
            $d = DB::table("subscribe")->where(["dairyId" => request("dairyId")])->update(["isActivated" => 0]);
            if ($d) {
                return ["error" => false, "msg" => "Dairy deactivated successfully."];
            } else {
                return ["error" => true, "msg" => "an error has occured while deactivate dairy."];
            }
        }
    }

    public function DeleteDairyCompletely()
    {
        if (!request("master-pin") || !request("dairyId")) {
            // return redirect("sa/dairyList");
            return ["error" => true, "msg" => "1"];
        } else {
            if (request('master-pin') != "iouaroiuqtoerhfglkfglshglldglskglkshdg") {
                // return redirect("sa/dairyList");
                return ["error" => true, "msg" => "2"];
            }
        }

        DB::beginTransaction();

        $ad = DB::table("advance")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $al = DB::table("app_logins")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $bs = DB::table("balance_sheet")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $cr = DB::table("credit")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $cs = DB::table("customer")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $dt = DB::table("daily_transactions")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $ex = DB::table("expenses")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $es = DB::table("expense_setups")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $rc = DB::table("fat_snf_ratecard")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $mems = DB::table("member_personal_info")->where([
            "dairyId" => request('dairyId'),
        ])->select("id")->get()->pluck("id");

        $mi = DB::table("member_personal_info")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $mp = DB::table("member_personal_bank_info")->whereIn("memberPersonalUserId", $mems)->delete();

        $mo = DB::table("member_other_info")->whereIn("memberId", $mems)->delete();

        $mr = DB::table("milkrequest")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $pd = DB::table("products")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $ps = DB::table("purchase_setups")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $rl = DB::table("rangelist")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $rs = DB::table("ratecardshort")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $sl = DB::table("sales")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        $su = DB::table("suppliers")->where([
            "dairyId" => request('dairyId'),
        ])->delete();

        // $ld = DB::table("ledger")->where([
        //     "dairyId" => request('dairyId'),
        // ])->delete();

        // $sb = DB::table("subscribe")->where([
        //     "dairyId" => request('dairyId'),
        // ])->delete();
        // $df = DB::table("dairy_info")->where([
        //     "id" => request('dairyId'),
        // ])->delete();

        // $dp = DB::table("dairy_propritor_info")->where([
        //     "dairyId" => request('dairyId'),
        // ])->delete();

        // $ou = DB::table("other_users")->where([
        //     "dairyId" => request('dairyId'),
        // ])->delete();

        // $uc = DB::table("user_current_balance")->where([
        //     "dairyId"   => request('dairyId')
        // ])->delete();

        DB::commit();

        // return redirect("sa/dairyList");
        return ["error" => false, "msg" => "3"];
    }

    public function pay()
    {
        $pp = DB::table("subscription_plan")->get();
        $states = DB::table('states')->get();

        return view("spradmin.pay", ["pp" => $pp, "states" => $states]);
    }

    public function saveDairyAndPay(Request $req)
    {

        $validator = Validator::make(request()->all(), [
            "pricePlanId" => 'bail|required',
            "name" => 'bail|required',
            "code" => 'bail|required',
            "mobile" => 'bail|required|numeric|digits:10',
            "owname" => 'bail|required',
            "owmobile" => 'bail|required|numeric|digits:10',
            "owemail" => 'bail|required|email',
            "owaddress" => 'bail|required',
            "owstate" => 'bail|required',
            "owdistrict" => 'bail|required',
            'owpin' => "bail|required",
            'priceMonthlyOrYearly' => "bail|required|in:monthly,yearly",
        ]);

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

        return $res;
        // $data = [
        //     "order_id" => request("order_id"),
        //     "merchant_id" => request("merchant_id"),
        //     "redirect_url" => request("redirect_url"),
        //     "cancel_url" => request("cancel_url"),
        //     "language" => request("language"),
        //     "currency" => request("currency"),
        //     "tid" => request("tid"),
        // ];
        // return view("buy.ccavRequestHandler", $data);
    }


    public function appSettings()
    {
        $settings = DB::table('androidappsetting')->get()->first();
        return view("spradmin.appSettings", ["settings" => $settings, "activepage" => "appSettings"]); 
    }
    public function updateAppSetting()
    {
        $validator = $this->validate(request(), [
            "server_api_key" => "required",
            "updated_at"     => date("Y-m-d H:i:s")
        ]);

        $res = DB::table('androidappsetting')->where(["id" => 1])->update(["server_api_key" => request("server_api_key")]);
        
        if($res){
            Session::flash('msg', "Settings updated successfully.");
            Session::flash('alert-class', 'alert-success');
            return redirect('sa/appSettings');
        }else{
            Session::flash('msg', "There is an error, while processing your request.");
            Session::flash('alert-class', 'alert-danger');
            return redirect('sa/appSettings')->withInput();
        }
    }
}
