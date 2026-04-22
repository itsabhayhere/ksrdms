<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mail;
use App\Sms;

class ColmanController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    //
    public function colMans(){
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $colMans = DB::table("other_users")->where("dairyId", $dairyId)->get();

        return view("colMans", ["colMans" => $colMans]);
    }

    public function newColMan(){
        return view("newColMan");
    }

    public function colMansUserName(Request $req){
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $colMan = DB::table("other_users")->where("userName", $req->userName)
            ->where("dairyId", $dairyId)->get()->first();

        if ($colMan == (null || false)) {
            return ["error" => false, "msg" => ""];
        } else {
            return ["error" => true, "msg" => "Already registered with this name"];
        }
    }

    public function colManMobileNumberValidation(Request $req)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $colMan = DB::table("other_users")->where("mobileNumber", $req->mobileNumber)
            ->where("dairyId", $dairyId)->get()->first();

        if ($colMan == (null || false)) {
            return ["error" => false, "msg" => ""];
        } else {
            return ["error" => true, "msg" => "Already registered with this mobile number"];
        }
    }

    public function createColMan(Request $req)
    {
        $res = $this->createNewColMan($req);
        if($res){
            return redirect("colMans");
        }else{
            return redirect("newColMan");
        }
    }

    public function createNewColMan(Request $req)
    {
        
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = session()->get('dairyInfo');

        DB::beginTransaction();

        $validatedData = $req->validate([
            'userName' => 'required',
            'email' => 'required',
            'mobileNumber' => 'required',
            'address' => 'required',
            'gender' => 'required',
        ]);

        $ex = DB::table("other_users")->where("mobileNumber", $req->mobileNumber)->get()->first();
        if($ex != null){
            Session::flash('msg', 'Already registered with this mobile number');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $password = $this->newPassword();
        $currentTime = date('Y-m-d H:i:s');

        $colMan = DB::table('other_users')->insertGetId([
            'dairyId' => $dairyId,
            'status' => true,
            'roleId' => "1",
            'userName' => $req->userName,
            'userEmail' => $req->email,
            'mobileNumber' => $req->mobileNumber,
            'password' => $password,
            'address' => $req->address,
            'created_at' => $currentTime,
        ]);

        if ($colMan == (false || null)) {
            DB::rollback();
            Session::flash('msg', 'an error has occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $data = [
            "sysName" => $dairyInfo->dairyName,
            "dairyAdmin" => "DAIRY ADMIN",
            "user" => [
                "username" => $req->userName,
                "id" => $req->mobileNumber,
                "pass" => $password,
            ],
            "loginlink" => url("dairy-login"),
        ];

        try{
            // Mail::send('emails.welcome', $data, function ($m) use ($req, $dairyInfo) {
            //     $m->from('director@ksrservices.in', 'KSR Services Admin');
            //     $m->to($req->email, $req->userName)->subject('Your ID & Password as Collection Manager in ' . $dairyInfo->dairyName);
            // });
            // $sms = new Sms();
            // $sms->send(
            //     [
            //         "message" => "Your registration successfully as a collection manager in dairy: ".$dairyInfo->dairyName.",
            //         Login: " . url("dairy-login") . "
            //         ID: " . $data['user']['id'] . "
            //         pass: " . $data['user']['pass'] . "
            //         Thanks!",
            //         "numbers" => $req->mobileNumber,
            //         "messageType" => "collectionManagerRegistration"
            //     ], $dairyId);

        }catch(Exception $e){
            DB::rollback();
            Session::flash('msg', $e->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect("newColMan");
        }

        DB::commit();

        Session::flash('msg', 'Successfully registered.');
        Session::flash('alert-class', 'alert-success');
        return true;
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
