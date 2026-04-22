<?php

namespace App\Http\Controllers\API;

use App\APIHelperModel;
use App\APIValidationModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class LoginController extends Controller
{
    
    //
    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */

    public function login(Request $req)
    {

        // if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
        //     $user = Auth::user();
        //     $success['token'] =  $user->createToken('MyApp')->accessToken;
        //     return response()->json(['success' => $success], $this->successStatus);
        // }
        // else{
        //     return response()->json(['error'=>'Unauthorised'], 401);
        // }

        $res = APIValidationModel::loginRequestValidation();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        $res = APIHelperModel::loginCheck();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        } else {
            return response()->json($res, 200);
        }

    }

    public function sendLoginOtp()
    {
        $res = APIValidationModel::sendLoginOtpValidation();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        $hc = new \App\Http\Controllers\HomeController();

        $res = $hc->sendLoginOtp();

        if ($res['error']) {
            $t = ["error_message" => $res['msg'],
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
            return response()->json($t, 200);
        } else {            
            $t = ["error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => $res['msg'],
            ];
            return response()->json($t, 200);
        }
    }

    public function otpLogin()
    {
        $res = APIValidationModel::otpLoginValidation();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        $res = APIHelperModel::otpLogin();

        return response()->json($res, 200);
    }

    public function setNewPass()
    {
        
        $appLogin = DB::table("app_logins")->where("token_key", request("device_token"))->get()->first();

        if ($appLogin==null) {

            $t = ["error_message" => "Not authorized.",
                    "status" => "ERROR",
                    "status_code" => "202",
                    "success_message" => "",
                ];
                return response()->json($t, 200);
        }

        $res = APIValidationModel::newPassValidation($appLogin);

        if ($res['error']) {
                    
        $t = ["error_message" => $res['msg'],
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
            return response()->json($t, 200);
        }

        if(request("userType") == "dairy"){
            $up = DB::table('other_users')
                ->where('id', $appLogin->userId)
                ->update(["password" => request("newPass")]);
        }else{
            $up = DB::table('member_personal_info')
                ->where(["id" => $appLogin->userId])
                ->update(["password" => request("newPass")]);
        }
        
        $t = ["error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "Password updated successfully",
            ];
        return response()->json($t, 200);
    }


    public function logout()
    {
        $r = APIHelperModel::isAuthOnly();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            DB::table('app_logins')->where(["id" => $r['data']->id])->delete();
            
            $t = ["error_message" => "",
                    "status" => "OK",
                    "status_code" => "200",
                    "success_message" => "You have logged out successfully",
                ];
            return response()->json($t, 200);
        }
    }
}
