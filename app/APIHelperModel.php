<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PushNotification;
use function GuzzleHttp\json_encode;

class APIHelperModel extends Model
{
    //

    public static function loginCheck()
    {
        $res = DB::table('other_users')
            ->where('mobileNumber', request('username'))
            ->where('password', request('password'))
            ->select("id", "dairyId", "userName")
            ->get()->first();

        if ($res != (null && "")) {
            $u['ut'] = 1;
            $u['ui'] = $res->id;
            $u['dairyId'] = $res->dairyId;

            $token = APIHelperModel::createToken($u);
            $dairy = DB::table("dairy_info")->where("id", $res->dairyId)->select("dairyName", "society_code", "mobile", "dairyAddress", "cash_in_hand")->get()->first();
            unset($res->password);

            $response = [
                "token_key" => $token,
                "userType" => "DAIRYADMIN",
                "userInfo" => $res,
                "dairyInfo" => $dairy,
            ];

            return [
                "error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "login success.",
                "data" => (object) $response,
            ];

        } else {
            goto MEMBER_LOGIN_CHECK;

            return ["error_message" => "login failed due to invalid username or password.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }

        MEMBER_LOGIN_CHECK:

        $res = DB::table('member_personal_info')
            ->where('memberPersonalMobileNumber', request('username'))
            ->where('password', request('password'))
            ->where('status', "true")
            ->get()->first();

        if ($res != (null && "")) {
            $u['ut'] = 4;
            $u['ui'] = $res->id;
            $u['dairyId'] = $res->dairyId;

            $token = APIHelperModel::createToken($u);
            $dairy = DB::table("dairy_info")->where("id", $res->dairyId)->select("dairyName", "society_code", "mobile", "dairyAddress")->get()->first();

            $mem = DB::table("member_personal_info")->where(['member_personal_info.id' => $res->id])
                ->select("member_personal_info.id as id", "ledgerId", "memberPersonalCode as memberCode", "memberPersonalregisterDate as registerDate", "memberPersonalName as name", "memberPersonalFatherName as fatherName",
                    "memberPersonalGender as gender", "memberPersonalEmail as email", "memberPersonalAadarNumber as aadharNumber", "memberPersonalMobileNumber as mobileNumber", "memberPersonalAddress as address",
                    "states.name as state", "city.name as city", "memberPersonalDistrictVillage as districtVillage", "memberPersonalMobilePincode as pin", "created_at")
                ->leftJoin("city", "city.id", "=", "memberPersonalCity")
                ->leftJoin("states", "states.id", "=", "memberPersonalState")
                ->get()->first();

            $response = [
                "token_key" => $token,
                "userType" => "MEMBER",
                "dairyInfo" => $dairy,
                "memberInfo" => $mem,
            ];

            return [
                "error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "login success.",
                "data" => (object) $response,
            ];
        } else {
            return ["error_message" => "login failed due to invalid username or password.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }

    }

    public static function otpLogin()
    {
        $res = DB::table('other_users')
            ->where('mobileNumber', request('username'))
            ->where('otp', request('otp'))
            ->select("id", "dairyId", "userName", "otpTime")
            ->get()->first();

        if ($res != (null && "")) {

            if (time() - strtotime($res->otpTime) > 300) {
                return ["error_message" => "OTP you entered has expired, please try again.",
                    "status" => "ERROR",
                    "status_code" => "202",
                    "success_message" => "",
                ];
            }

            $u['ut'] = 1;
            $u['ui'] = $res->id;
            $u['dairyId'] = $res->dairyId;

            $token = APIHelperModel::createToken($u);
            $dairy = DB::table("dairy_info")->where("id", $res->dairyId)->select("dairyName", "society_code", "mobile", "dairyAddress", "cash_in_hand")->get()->first();
            unset($res->password);

            $response = [
                "token_key" => $token,
                "userType" => "DAIRYADMIN",
                "userInfo" => $res,
                "dairyInfo" => $dairy,
            ];

            return [
                "error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "login success.",
                "data" => (object) $response,
            ];

        } else {
            goto MEMBER_LOGIN_CHECK;

            return ["error_message" => "OTP you entered is invalid.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }

        MEMBER_LOGIN_CHECK:

        $res = DB::table('member_personal_info')
            ->where('memberPersonalMobileNumber', request('username'))
            ->where('otp', request('otp'))
            ->where('status', "true")
            ->get()->first();

        if ($res != (null && "")) {

            if (time() - strtotime($res->otpTime) > 300) {
                return ["error_message" => "OTP you entered has expired, please try again.",
                    "status" => "ERROR",
                    "status_code" => "202",
                    "success_message" => "",
                ];
            }

            $u['ut'] = 4;
            $u['ui'] = $res->id;
            $u['dairyId'] = $res->dairyId;

            $token = APIHelperModel::createToken($u);
            $dairy = DB::table("dairy_info")->where("id", $res->dairyId)->select("dairyName", "society_code", "mobile", "dairyAddress")->get()->first();

            $mem = DB::table("member_personal_info")->where(['member_personal_info.id' => $res->id])
                ->select("member_personal_info.id as id", "ledgerId", "memberPersonalCode as memberCode", "memberPersonalregisterDate as registerDate", "memberPersonalName as name", "memberPersonalFatherName as fatherName",
                    "memberPersonalGender as gender", "memberPersonalEmail as email", "memberPersonalAadarNumber as aadharNumber", "memberPersonalMobileNumber as mobileNumber", "memberPersonalAddress as address",
                    "states.name as state", "city.name as city", "memberPersonalDistrictVillage as districtVillage", "memberPersonalMobilePincode as pin", "created_at")
                ->leftJoin("city", "city.id", "=", "memberPersonalCity")
                ->leftJoin("states", "states.id", "=", "memberPersonalState")
                ->get()->first();

            $response = [
                "token_key" => $token,
                "userType" => "MEMBER",
                "dairyInfo" => $dairy,
                "memberInfo" => $mem,
            ];

            return [
                "error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "login success.",
                "data" => (object) $response,
            ];
        } else {
            return ["error_message" => "OTP you entered is invalid.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }

    }

    public static function createToken($u)
    {
        if (request('device_token')) {
            $token = request('device_token');
        } else {
            $token = Str::random(256);
        }

        $appLogin = DB::table("app_logins")->where("token_key", $token)->get()->first();

        if ($appLogin != (null || "")) {
            DB::table("app_logins")->where("token_key", $token)->update([
                "userType" => $u['ut'],
                "userId" => $u['ui'],
                "dairyId" => $u['dairyId'],
                "token_key" => $token,
                "last_login" => date("Y-m-d H:i:s"),
                "last_active" => date("Y-m-d H:i:s"),
            ]);
        } else {
            DB::table("app_logins")->insert([
                "userType" => $u['ut'],
                "userId" => $u['ui'],
                "dairyId" => $u['dairyId'],
                "token_key" => $token,
                "last_login" => date("Y-m-d H:i:s"),
                "last_active" => date("Y-m-d H:i:s"),
                "created_at" => date("Y-m-d H:i:s"),
            ]);
        }

        return $token;
    }

    public static function isAuthReq()
    {
        if (request("device_token") == (null || "")) {
            return [
                "error_message" => "you are not authorized, device_token missing or empty.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }

        $appLogin = DB::table("app_logins")->where(["token_key" => request("device_token"), "userType" => 1])->get()->first();
        if ($appLogin != (null || "")) {
            DB::table("app_logins")->where("token_key", request("device_token"))->update([
                "last_active" => date("Y-m-d H:i:s"),
            ]);

            $sub = new \App\APIHelperModel();
            $s = $sub->checkSubscription($appLogin->dairyId);

            if ($s['error']) {
                return [
                    "error_message" => "Your Dairy Subscription has expired, please contact Dairy Administrator Or Super Administrator.",
                    "status" => "ERROR",
                    "status_code" => "202",
                    "success_message" => "",
                    "data" => (object) $s,
                ];
            }

            switch ($appLogin->userType) {
                case "1":{
                        $u = DB::table("other_users")->where("id", $appLogin->userId)->get()->first();
                        $appLogin->colMan = $u->userName;
                        $appLogin->colManId = $u->id;
                        // session(["colMan" => $u]);
                        session()->put('colMan', $u);
                        session()->put('loginUserType', "dairy");
                        session()->put('loginUserInfo', $u);

                        $dairy = DB::table("dairy_info")->where("id", $u->dairyId)->get()->first();
                        unset($dairy->password);
                        session()->put('dairyInfo', $dairy);

                        break;
                    }
                default:
                    $colMan = "";
            }

            return [
                "error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "request authorized.",
                "data" => (object) $appLogin,
            ];
        } else {
            return [
                "error_message" => "unauthorized request(token invalid), please login again",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }
    }

    public static function isAuthOnly()
    {
        if (request("device_token") == (null || "")) {
            return [
                "error_message" => "you are not authorized, device_token missing or empty.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }

        $appLogin = DB::table("app_logins")->where("token_key", request("device_token"))->get()->first();
        if ($appLogin != (null || "")) {
            DB::table("app_logins")->where("token_key", request("device_token"))->update([
                "last_active" => date("Y-m-d H:i:s"),
            ]);

            switch ($appLogin->userType) {
                case "1":{
                        $u = DB::table("other_users")->where("id", $appLogin->userId)->get()->first();
                        $appLogin->colMan = $u->userName;
                        $appLogin->colManId = $u->id;
                        // session(["colMan" => $u]);
                        session()->put('colMan', $u);
                        session()->put('loginUserType', "dairy");
                        session()->put('loginUserInfo', $u);

                        $dairy = DB::table("dairy_info")->where("id", $u->dairyId)->get()->first();
                        unset($dairy->password);
                        session()->put('dairyInfo', $dairy);

                        break;
                    }
                case "4":{

                        $u = DB::table("member_personal_info")->where("id", $appLogin->userId)->get()->first();
                        $appLogin->memberCode = $u->memberPersonalCode;
                        $appLogin->ledgerId = $u->ledgerId;
                        session(["loginUserInfo" => $u]);
                        session()->put('loginUserType', "member");

                        $dairy = DB::table("dairy_info")->where("id", $u->dairyId)->get()->first();
                        unset($dairy->password);
                        session()->put('dairyInfo', $dairy);

                        break;
                    }
                default:
                    return [
                        "error_message" => "unauthorized request(token invalid), please login again",
                        "status" => "ERROR",
                        "status_code" => "202",
                        "success_message" => "",
                    ];
            }

            return [
                "error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "request authorized.",
                "data" => (object) $appLogin,
            ];
        } else {
            return [
                "error_message" => "unauthorized request(token invalid), please login again",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }
    }

    public static function isAuthReqMem()
    {
        if (request("device_token") == (null || "")) {
            return [
                "error_message" => "you are not authorized, device_token missing or empty.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }

        $appLogin = DB::table("app_logins")->where(["token_key" => request("device_token"), "userType" => "4"])->get()->first();
        if ($appLogin != (null || "")) {
            DB::table("app_logins")->where("token_key", request("device_token"))->update([
                "last_active" => date("Y-m-d H:i:s"),
            ]);

            $u = DB::table("member_personal_info")->where("id", $appLogin->userId)->get()->first();
            $appLogin->memberCode = $u->memberPersonalCode;
            $appLogin->ledgerId = $u->ledgerId;
            session(["loginUserInfo" => $u]);
            session()->put('loginUserType', "member");

            $dairy = DB::table("dairy_info")->where("id", $u->dairyId)->get()->first();
            unset($dairy->password);
            session()->put('dairyInfo', $dairy);

            return [
                "error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "request authorized as member.",
                "data" => (object) $appLogin,
            ];
        } else {
            return [
                "error_message" => "unauthorized request(token invalid), please login again",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }
    }

    public function checkSubscription($dairyId = null)
    {
        // return ["error" => false, "msg" => "Dairy not Found."];
        ///////////////////////////////////////////////////////

        if ($dairyId == null) {
            return ["error" => true, "msg" => "Dairy not Found."];
        }

        $s = DB::table("subscribe")->where(["dairyId" => $dairyId])->get()->first();
     
        // echo json_encode($s); exit;

        if ($s == null) {
            return ["error" => true, "msg" => "Some thing is wrong, contact your Super Administrator. Error: Dairy Not Subscribed.", "trial" => false, "takeToExp" => false, "takeToLogin" => true, "takeToDeactivatedDairy" => false];
        }

        if (!$s->isActivated) {
            return ["error" => true, "msg" => "Dairy is not activated, contact your Super Administrator.", "trial" => false, "takeToExp" => false, "takeToLogin" => false, "takeToDeactivatedDairy" => true];
        }

        // $s = $this->updateTrialTime($s);

        if (strtotime($s->trialEndDate) < time()) {
            $trial = false;
        } else {
            $trial = true;
        }
        if ($s->isPaymentDone) {
            if (strtotime($s->expiryDate) < time()) {
                $expired = true;
            } else {
                $expired = false;
            }
        } else {
            $expired = true;
        }

        if ($expired && !$trial) {
            $trialTime = 0;
            return ["error" => true, "msg" => "Your plan is expired. please renew it", "trial" => false, "takeToExp" => true,
                "takeToLogin" => false, "takeToDeactivatedDairy" => false, "s" => $s, "trialTime" => $trialTime];
        } elseif ($expired && $trial) {
            $datediff = strtotime($s->trialEndDate) - time();
            $trialTime = round($datediff / (60 * 60 * 24));
            return ["error" => false, "msg" => "Your subscription is on trial please buy now.", "trial" => true, "takeToExp" => false,
                "takeToLogin" => false, "takeToDeactivatedDairy" => false, "s" => $s, "trialTime" => $trialTime];
        } else {
            $datediff = strtotime($s->trialEndDate) - strtotime($s->expiryDate);
            $trialTime = round($datediff / (60 * 60 * 24));
            return ["error" => false, "msg" => "", "trial" => false, "takeToExp" => false, "takeToLogin" => false,
                "takeToDeactivatedDairy" => false, "s" => $s, "trialTime" => $trialTime];
        }

        return ["error" => false, "msg" => "", "trial" => false, "takeToExp" => false, "takeToLogin" => false,
            "takeToDeactivatedDairy" => false, "s" => $s, "trialTime" => $trialTime];
    }

    // public function updateTrialTime($s)
    // {
    //     if(strtotime($s->trialStartDate) <= time()){
    //         $datediff = time() - strtotime($s->trialStartDate);
    //         $day = round($datediff / (60 * 60 * 24));
    //         if($day >= 31){
    //             $curTrial = 0;
    //             $isOnTrial = 0;
    //         }else{
    //             $curTrial = 31 - (int)$day;
    //             $isOnTrial = 1;
    //         }

    //         $updt = DB::table('subscribe')->where(["dairyId" => $s->dairyId])->update([
    //             "trialTime"  => $curTrial,
    //             "isOnTrial"  => $isOnTrial,
    //             "updated_at" => date("Y-m-d H:i:s")
    //         ]);

    //         return DB::table('subscribe')->where(["dairyId" => $s->dairyId])->get()->first();
    //     }
    //     return $s;
    // }

    public static function memDashboardSummary($d)
    {
        $curDate = date('Y-m-d');
        $yesDate = date('Y-m-d', strtotime("-1 days"));

        /* Cow milk Collected today */
        $cowMilkToday = DB::table('daily_transactions')
            ->where('dairyId', $d->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where("memberCode", $d->memberCode)
            ->where('date', $curDate)
            ->get();

        $cowMilkTodayQty = 0;
        foreach ($cowMilkToday as $cowMilkTodayData) {
            $cowMilkTodayQty = (float) $cowMilkTodayQty + (float) $cowMilkTodayData->milkQuality;
        }

        /* Cow milk Collected Yesterday */
        $cowMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', $d->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where("memberCode", $d->memberCode)
            ->where('date', $yesDate)
            ->get();

        $cowMilkYesterdayQty = 0;
        foreach ($cowMilkYesterday as $cowMilkYesterdayData) {
            $cowMilkYesterdayQty = (float) $cowMilkYesterdayQty + (float) $cowMilkYesterdayData->milkQuality;
        }

        /* Buffalo milk Collected today */
        $buffaloMilkToday = DB::table('daily_transactions')
            ->where('dairyId', $d->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where("memberCode", $d->memberCode)
            ->where('date', $curDate)
            ->get();
        $buffaloMilkTodayQty = 0;
        foreach ($buffaloMilkToday as $buffaloMilkTodayData) {
            $buffaloMilkTodayQty = (float) $buffaloMilkTodayQty + (float) $buffaloMilkTodayData->milkQuality;
        }

        /* Buffalo milk Collected Yesterday */
        $buffaloMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', $d->dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where("memberCode", $d->memberCode)
            ->where('date', $yesDate)
            ->get();

        $buffaloMilkYesterdayQty = 0;
        foreach ($buffaloMilkYesterday as $buffaloMilkYesterdayData) {
            $buffaloMilkYesterdayQty = (float) $buffaloMilkYesterdayQty + (float) $buffaloMilkYesterdayData->milkQuality;
        }

        $mem = DB::table("member_personal_info")->where("memberPersonalCode", $d->memberCode)
            ->where("dairyId", $d->dairyId)->get()->first();
        $curBal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->get()->first();

        if ($curBal->openingBalanceType == "debit") {
            $balType = " DR.";
        } else {
            $balType = " CR.";
        }

        $data = [
            "cowMilkTodayQty" => number_format($cowMilkTodayQty, 1, ".", ""),
            "cowMilkYesterdayQty" => number_format($cowMilkYesterdayQty, 1, ".", ""),
            "buffaloMilkTodayQty" => number_format($buffaloMilkTodayQty, 1, ".", ""),
            "buffaloMilkYesterdayQty" => number_format($buffaloMilkYesterdayQty, 1, ".", ""),
            "currentBalance" => number_format($curBal->openingBalance, 2, ".", "") . $balType,
        ];

        return $data;
    }

    public static function getDairySummary($dairyId)
    {
        $countMembers = DB::table("member_personal_info")->where("dairyId", $dairyId)->count();
        $activeMembers = DB::table("daily_transactions")->where(["dairyId" => $dairyId, "status" => "true"])
            ->whereDate("date", ">", date("Y-m-d", strtotime("-5 days")))
            ->distinct('memberCode')->count('memberCode');

        $creditMembers = DB::table('user_current_balance')
            ->join("member_personal_info", "member_personal_info.ledgerId", "=", "user_current_balance.ledgerId")
            ->where('member_personal_info.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->count();

        // $countSuppliers = DB::table("suppliers")->where("dairyId", $dairyId)->count();
        // $countCustomers = DB::table("customer")->where("dairyId", $dairyId)->count();
        // $countMilkPlants = DB::table("plantdairymap")->where("dairyId", $dairyId)->count();
        // $countProducts = DB::table("products")->where("dairyId", $dairyId)->count();
        // $countManager = DB::table("other_users")->where("dairyId", $dairyId)->count();

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));

        /* Cow milk Collected today */
        $cowMilkTodayQty = 0;
        $cowMilkToday = DB::table('daily_transactions')
            ->where('dairyId', $dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where('date', $today)
            ->get();
        foreach ($cowMilkToday as $cowMilkTodayData) {
            $cowMilkTodayQty = (float) $cowMilkTodayQty + (float) $cowMilkTodayData->milkQuality;
        }

        /* Cow milk Collected Yesterday */
        $cowMilkYesterdayQty = 0;
        $cowMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', $dairyId)
            ->where('status', 'true')
            ->where('milkType', 'cow')
            ->where('date', $yesterday)
            ->get();
        foreach ($cowMilkYesterday as $cowMilkYesterdayData) {
            $cowMilkYesterdayQty = (float) $cowMilkYesterdayQty + (float) $cowMilkYesterdayData->milkQuality;
        }

        /* Buffalo milk Collected today */
        $buffaloMilkTodayQty = 0;
        $buffaloMilkToday = DB::table('daily_transactions')
            ->where('dairyId', $dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where('date', $today)
            ->get();
        foreach ($buffaloMilkToday as $buffaloMilkTodayData) {
            $buffaloMilkTodayQty = (float) $buffaloMilkTodayQty + (float) $buffaloMilkTodayData->milkQuality;
        }

        /* Buffalo milk Collected Yesterday */
        $buffaloMilkYesterdayQty = 0;
        $buffaloMilkYesterday = DB::table('daily_transactions')
            ->where('dairyId', $dairyId)
            ->where('status', 'true')
            ->where('milkType', 'buffalo')
            ->where('date', $yesterday)
            ->get();

        foreach ($buffaloMilkYesterday as $buffaloMilkYesterdayData) {
            $buffaloMilkYesterdayQty = (float) $buffaloMilkYesterdayQty + (float) $buffaloMilkYesterdayData->milkQuality;
        }

        //-------------------------------------------
        /* local Sale */
        $localSaleDataCount = 0;
        $localSale = DB::table('sales')
            ->where('dairyId', $dairyId)
            ->where('saleType', 'local_sale')
            ->where('saleDate', $today)
            ->get();
        foreach ($localSale as $localSaleData) {
            $localSaleDataCount = $localSaleDataCount + $localSaleData->amount;
        }

        /* plant sale */
        $plantSaleDataCount = 0;
        // $plantSale = DB::table('sales')
        //     // ->where('dairyId', $dairyId)
        //     ->where('saleType', 'plant_sale')
        //     ->where('saleDate', $today)
        //     ->get();
        // foreach ($plantSale as $plantSaleData) {
        //     $plantSaleDataCount = $plantSaleDataCount + $plantSaleData->amount;
        // }

        /* product sale */
        $productSaleDataCount = 0;
        $productSale = DB::table('sales')
            ->where('dairyId', $dairyId)
            ->where('saleType', 'product_sale')
            ->where('saleDate', $today)
            ->get();
        foreach ($productSale as $productSaleData) {
            $productSaleDataCount = $productSaleDataCount + $productSaleData->amount;
        }

        //-----------Credit-Debit Datas--------------------------------

        /* Credits of Members */
        $memberOnCredit = DB::table('member_personal_info')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'member_personal_info.ledgerId')
            ->where('member_personal_info.status', 'true')
            ->where('user_current_balance.userType', 4)
            ->where('member_personal_info.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select("member_personal_info.id as id", "memberPersonalCode as memberCode", "memberPersonalName as memberName",
                "memberPersonalMobileNumber as mobile", 'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $memberCredit = 0;
        $creditMemberCount = 0;
        foreach ($memberOnCredit as $memberOnCreditData) {
            $memberCredit = $memberCredit + $memberOnCreditData->openingBalance;
            $creditMemberCount++;
        }
        // unset($memberOnCredit);

        /* Credits of Customer  */
        $customerOnCredit = DB::table('customer')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'customer.ledgerId')
            ->where('customer.status', 'true')
            ->where('user_current_balance.userType', 2)
            ->where('customer.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select("customer.id as id", "customer.customerCode", "customerName", "customerMobileNumber",
                'user_current_balance.openingBalance', "user_current_balance.openingBalanceType")
            ->groupBy("user_current_balance.ledgerId")
            ->get();
        $customerCredit = 0;
        $creditCustomerCount = 0;
        foreach ($customerOnCredit as $c) {
            $customerCredit = $customerCredit + $c->openingBalance;
            $creditCustomerCount++;
        }
        // unset($customerOnCredit);

        /* credits of  supplier */
        $supplierOnCredit = DB::table('suppliers')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'suppliers.ledgerId')
            ->where('suppliers.status', 'true')
            ->where('user_current_balance.userType', 3)
            ->where('suppliers.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select("suppliers.id as id", "supplierCode", "supplierFirmName", "supplierPersonName", "supplierMobileNumber",
                'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $supplierCredit = 0;
        $creditSuppCount = 0;
        foreach ($supplierOnCredit as $s) {
            $supplierCredit = $supplierCredit + $s->openingBalance;
            $creditSuppCount++;
        }
        // unset($supplierOnCredit);

        /* Debits of Members */
        $memberOnDebit = DB::table('member_personal_info')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'member_personal_info.ledgerId')
            ->where('member_personal_info.status', 'true')
            ->where('user_current_balance.userType', 4)
            ->where('member_personal_info.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select("member_personal_info.id as id", "memberPersonalCode as memberCode", "memberPersonalName as memberName",
                "memberPersonalMobileNumber as mobile",
                'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $memberDebit = 0;
        $debitMemberCount = 0;
        foreach ($memberOnDebit as $memberOnDebitData) {
            $memberDebit = $memberDebit + $memberOnDebitData->openingBalance;
            $debitMemberCount++;
        }
        // unset($memberOnDebit);

        /* Debits of Customers*/
        $customerOnDebit = DB::table('customer')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'customer.ledgerId')
            ->where('customer.status', 'true')
            ->where('user_current_balance.userType', 2)
            ->where('customer.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select("customer.id as id", "customer.customerCode", "customerName", "customerMobileNumber",
                'user_current_balance.openingBalance', "user_current_balance.openingBalanceType")
            ->get();
        $customerDebit = 0;
        $debitCustomerCount = 0;
        foreach ($customerOnDebit as $c) {
            $customerDebit = $customerDebit + $c->openingBalance;
            $debitCustomerCount++;
        }
        // unset($customerOnDebit);

        /* Debits of suppliers */
        $supplierOnDebit = DB::table('suppliers')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'suppliers.ledgerId')
            ->where('suppliers.status', 'true')
            ->where('user_current_balance.userType', 3)
            ->where('suppliers.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select("suppliers.id as id", "supplierCode", "supplierFirmName", "supplierPersonName", "supplierMobileNumber",
                'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $supplierDebit = 0;
        $debitSuppCount = 0;
        foreach ($supplierOnDebit as $s) {
            $supplierDebit = $supplierDebit + $s->openingBalance;
            $debitSuppCount++;
        }
        // unset($supplierOnDebit);

        $activeMemberList = DB::table('daily_transactions')
            ->select("member_personal_info.id as id", "memberPersonalCode as memberCode", "memberPersonalName as memberName",
                "memberPersonalMobileNumber as mobile",
                'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "daily_transactions.memberCode")
            ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
            ->groupBy('daily_transactions.memberCode')
            ->where(["member_personal_info.dairyId" => $dairyId, "daily_transactions.dairyId" => $dairyId, "daily_transactions.status" => "true"])
            ->whereDate("daily_transactions.date", ">", date("Y-m-d", strtotime("-5 days")))->get();

        $m = DB::table('daily_transactions')
            ->select("daily_transactions.memberCode")
            ->groupBy('daily_transactions.memberCode')
            ->where(["daily_transactions.dairyId" => $dairyId, "daily_transactions.status" => "true"])
            ->whereDate("daily_transactions.date", ">", date("Y-m-d", strtotime("-5 days")))->pluck('memberCode')->toArray();
        $inactiveMemberList = DB::table('member_personal_info')
            ->select("member_personal_info.id as id", "memberPersonalCode as memberCode", "memberPersonalName as memberName",
                    "memberPersonalMobileNumber as mobile",
                    'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
            ->where(["member_personal_info.dairyId" => $dairyId,
                "member_personal_info.status" => "true"])
            ->whereNotIn("member_personal_info.memberPersonalCode", $m)
            ->groupby("member_personal_info.memberPersonalCode")->get();

        return [
            "dairySummary" => [
                "totalMembers" => $countMembers,
                "activeMembers" => $activeMembers,
                "inactiveMembers" => $countMembers - $activeMembers,
                "creditMembers" => $creditMembers,
                "debitMembers" => $countMembers - $creditMembers,
            ],
            "milkCollection" => [
                "cowMilkToday" => round($cowMilkTodayQty),
                "cowMilkYesterday" => round($cowMilkYesterdayQty),
                "buffaloMilkToday" => round($buffaloMilkTodayQty),
                "buffaloMilkYesterday" => round($buffaloMilkYesterdayQty),
            ],
            "todaysSale" => [
                "localSale" => $localSaleDataCount,
                "plantSale" => $plantSaleDataCount,
                "productSale" => $productSaleDataCount,
            ],
            "credit-debit" => [
                "memberCredit" => number_format($memberCredit, 1, ".", ""),
                "creditMemberCount" => $creditMemberCount,
                "memberDebit" => number_format($memberDebit, 1, ".", ""),
                "debitMemberCount" => $debitMemberCount,
                "customerCredit" => number_format($customerCredit, 1, ".", ""),
                "creditCustomerCount" => $creditCustomerCount,
                "customerDebit" => number_format($customerDebit, 1, ".", ""),
                "debitCustomerCount" => $debitCustomerCount,
                "supplierCredit" => number_format($supplierCredit, 1, ".", ""),
                "creditSupplierCount" => $creditSuppCount,
                "supplierDebit" => number_format($supplierDebit, 1, ".", ""),
                "debitSupplierCount" => $debitSuppCount,
            ],
            "creditDebitDetailed" => [
                "creditMembers" => $memberOnCredit,
                "debitMembers" => $memberOnDebit,
                "creditCustomer" => $customerOnCredit,
                "debitCustomer" => $customerOnDebit,
                "creditSupplier" => $supplierOnCredit,
                "debitSupplier" => $supplierOnDebit,
            ],
            "activeMemberList" => $activeMemberList,
            "inactiveMemberList" => $inactiveMemberList
        ];

    }

    public static function creditMembers($dairyId)
    {
        //-----------Credit-Debit Datas--------------------------------

        /* Credits of Members */
        $memberOnCredit = DB::table('member_personal_info')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'member_personal_info.ledgerId')
            ->where('member_personal_info.status', 'true')
            ->where('user_current_balance.userType', 4)
            ->where('member_personal_info.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select("member_personal_info.id as id", "memberPersonalCode as memberCode", "memberPersonalName as memberName",
                "memberPersonalMobileNumber as mobile", 'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $memberCredit = 0;
        $creditMemberCount = 0;
        foreach ($memberOnCredit as $memberOnCreditData) {
            $memberCredit = $memberCredit + $memberOnCreditData->openingBalance;
            $creditMemberCount++;
        }

        return [
            "data" => $memberOnCredit,
        ];

    }

    public static function debitMembers($dairyId)
    {
        /* Debits of Members */
        $memberOnDebit = DB::table('member_personal_info')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'member_personal_info.ledgerId')
            ->where('member_personal_info.status', 'true')
            ->where('user_current_balance.userType', 4)
            ->where('member_personal_info.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select("member_personal_info.id as id", "memberPersonalCode as memberCode", "memberPersonalName as memberName",
                "memberPersonalMobileNumber as mobile",
                'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $memberDebit = 0;
        $debitMemberCount = 0;
        foreach ($memberOnDebit as $memberOnDebitData) {
            $memberDebit = $memberDebit + $memberOnDebitData->openingBalance;
            $debitMemberCount++;
        }
        // unset($memberOnDebit);

        return [
            "data" => $memberOnDebit,
        ];
    }

    public static function creditCustomers($dairyId)
    {
        /* Credits of Customer  */
        $customerOnCredit = DB::table('customer')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'customer.ledgerId')
            ->where('customer.status', 'true')
            ->where('user_current_balance.userType', 2)
            ->where('customer.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select("customer.id as id", "customer.customerCode", "customerName", "customerMobileNumber",
                'user_current_balance.openingBalance', "user_current_balance.openingBalanceType")
            ->groupBy("user_current_balance.ledgerId")
            ->get();
        $customerCredit = 0;
        $creditCustomerCount = 0;
        foreach ($customerOnCredit as $c) {
            $customerCredit = $customerCredit + $c->openingBalance;
            $creditCustomerCount++;
        }
        // unset($customerOnCredit);

        return [
            "data" => $customerOnCredit,
        ];
    }

    public static function debitCustomers($dairyId)
    {

        /* Debits of Customers*/
        $customerOnDebit = DB::table('customer')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'customer.ledgerId')
            ->where('customer.status', 'true')
            ->where('user_current_balance.userType', 2)
            ->where('customer.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select("customer.id as id", "customer.customerCode", "customerName", "customerMobileNumber",
                'user_current_balance.openingBalance', "user_current_balance.openingBalanceType")
            ->get();
        $customerDebit = 0;
        $debitCustomerCount = 0;
        foreach ($customerOnDebit as $c) {
            $customerDebit = $customerDebit + $c->openingBalance;
            $debitCustomerCount++;
        }
        return [
            "data" => $customerOnDebit,
        ];

    }

    
    public static function creditSuppliers($dairyId)
    {
        /* credits of  supplier */
        $supplierOnCredit = DB::table('suppliers')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'suppliers.ledgerId')
            ->where('suppliers.status', 'true')
            ->where('user_current_balance.userType', 3)
            ->where('suppliers.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select("suppliers.id as id", "supplierCode", "supplierFirmName", "supplierPersonName", "supplierMobileNumber",
                'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $supplierCredit = 0;
        $creditSuppCount = 0;
        foreach ($supplierOnCredit as $s) {
            $supplierCredit = $supplierCredit + $s->openingBalance;
            $creditSuppCount++;
        }
        return [
            "data" => $supplierOnCredit,
        ];
    }

    public static function debitSuppliers($dairyId)
    {

        /* Debits of suppliers */
        $supplierOnDebit = DB::table('suppliers')
            ->join('user_current_balance', 'user_current_balance.ledgerId', '=', 'suppliers.ledgerId')
            ->where('suppliers.status', 'true')
            ->where('user_current_balance.userType', 3)
            ->where('suppliers.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select("suppliers.id as id", "supplierCode", "supplierFirmName", "supplierPersonName", "supplierMobileNumber",
                'user_current_balance.openingBalance', 'user_current_balance.openingBalanceType')
            ->get();
        $supplierDebit = 0;
        $debitSuppCount = 0;
        foreach ($supplierOnDebit as $s) {
            $supplierDebit = $supplierDebit + $s->openingBalance;
            $debitSuppCount++;
        }
        return [
            "data" => $supplierOnDebit,
        ];

    }

    public static function getmemberCreditMilkCollection($dairyId)
    {
        /* Member on credit */
        $creditMemberBal = 0;
        $memberOnCredit = DB::table('user_current_balance')
            ->join('ledger', 'user_current_balance.ledgerId', '=', 'ledger.id')
            ->where('user_current_balance.userType', 4)
            ->where('ledger.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'credit')
            ->select('user_current_balance.*', 'ledger.*')
            ->get();
        foreach ($memberOnCredit as $memberOnCreditData) {
            $creditMemberBal = $creditMemberBal + $memberOnCreditData->openingBalance;
        }
        unset($memberOnCredit);

        /* Member on debit */
        $debitMemberBal = 0;
        $memberOnDebit = DB::table('user_current_balance')
            ->join('ledger', 'user_current_balance.ledgerId', '=', 'ledger.id')
            ->where('user_current_balance.userType', 4)
            ->where('ledger.dairyId', $dairyId)
            ->where('user_current_balance.openingBalanceType', 'debit')
            ->select('user_current_balance.*', 'ledger.*')
            ->get();

        foreach ($memberOnDebit as $memberOnDebitData) {
            $debitMemberBal = $debitMemberBal + $memberOnDebitData->openingBalance;
        }
        unset($memberOnDebit);

        $year = date("Y");
        $month = date("m");
        $Data = [];

        for ($i = 1; $i <= $month; $i++) {
            $currentLoopMonth = "";
            if ($i <= 9) {
                $currentLoopMonth = "0" . $i;
            } else {
                $currentLoopMonth = $i;
            }
            $startDate = $year . '-' . $currentLoopMonth . '-01';
            $endDate = $year . '-' . $currentLoopMonth . '-31';

            $singleFatSnfRange = DB::select("SELECT * FROM daily_transactions WHERE dairyId='$dairyId' AND date BETWEEN '" . $startDate . "' AND '" . $endDate . "'");
            $cowCount = 0;
            $buffaloCount = 0;
            foreach ($singleFatSnfRange as $singleFatSnfRangeData) {
                if ($singleFatSnfRangeData->milkType == "cow") {
                    $cowCount = $cowCount + $singleFatSnfRangeData->milkQuality;
                } else {
                    $buffaloCount = $buffaloCount + $singleFatSnfRangeData->milkQuality;
                }
            }
            $Data[] = array("cow" => $cowCount, "buffalo" => $buffaloCount);

        }

        $loopCount = count($Data);
        $returnValue = [];
        for ($i = 0; $i < 12; $i++) {
            if (!empty($Data[$i])) {
                $returnValue[$i + 1] = $Data[$i];
            } else {
                $returnValue[$i + 1] = array("cow" => 0, "buffalo" => 0);
            }
        }

        return [
            "memberCreditReport" => [
                "creditMemberBal" => number_format($creditMemberBal, 2, ".", ""),
                "debitMemberBal" => number_format($debitMemberBal, 2, ".", ""),
            ],
            "monthlyMilkCollection" => (object) $returnValue,
        ];
    }

    public static function getMembers($dairyId)
    {
        $mem = DB::table("member_personal_info")->where("dairyId", $dairyId)->where("status", "true")
            ->select("member_personal_info.id", "member_personal_info.ledgerId", "memberPersonalCode as memberCode", "memberPersonalregisterDate as registerDate", "memberPersonalName as name", "memberPersonalFatherName as fatherName",
                "memberPersonalGender as gender", "memberPersonalEmail as email", "memberPersonalAadarNumber as aadharNumber", "memberPersonalMobileNumber as mobileNumber", "memberPersonalAddress as address",
                "states.name as state", "city.name as city", "memberPersonalDistrictVillage as districtVillage", "memberPersonalMobilePincode as pin",
                "user_current_balance.openingBalance", "user_current_balance.openingBalanceType",
                "member_personal_info.created_at")
            ->leftJoin("city", "city.id", "=", "memberPersonalCity")
            ->leftJoin("states", "states.id", "=", "memberPersonalState")
            ->leftJoin("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
            ->get();
        if ($mem == (null || "" || false)) {
            return [];
        }
        return $mem;
    }

    public static function getMember($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        if (request("memberId") == null) {
            $response["error_message"] = "Member Id Required to get member details.";
            return $response;
        }

        $mem = DB::table("member_personal_info")->where(["member_personal_info.dairyId" => $dairyId, "member_personal_info.id" => request('memberId'), "member_personal_info.status" => "true"])
            ->select("member_personal_info.id as id", "member_personal_info.ledgerId", "memberPersonalCode as memberCode", "memberPersonalregisterDate as registerDate", "memberPersonalName as name", "memberPersonalFatherName as fatherName",
                "memberPersonalGender as gender", "memberPersonalEmail as email", "memberPersonalAadarNumber as aadharNumber", "memberPersonalMobileNumber as mobileNumber", "memberPersonalAddress as address",
                "states.name as state", "city.name as city", "memberPersonalDistrictVillage as districtVillage", "memberPersonalMobilePincode as pin",
                "member_personal_bank_info.memberPersonalBankName as bankName", "member_personal_bank_info.memberPersonalAccountNumber as accountNo",
                "memberPersonalAccountName as accHolderName", "memberPersonalIfsc as ifscCode", "alert_print_slip as printSlip", "alert_sms as alert_sms",
                "alert_email as alert_email", "member_personal_info.created_at",
                "user_current_balance.openingBalance", "user_current_balance.openingBalanceType")
            ->leftJoin("member_personal_bank_info", "member_personal_bank_info.memberPersonalUserId", "=", "member_personal_info.id")
            ->leftJoin("member_other_info", "member_other_info.memberId", "=", "member_personal_info.id")
            ->leftJoin("city", "city.id", "=", "memberPersonalCity")
            ->leftJoin("states", "states.id", "=", "memberPersonalState")
            ->leftJoin("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
            ->get()->first();

        if ($mem == (null || "" || false)) {
            $response["error_message"] = "Member not found.";
            return $response;
        }

        $response["status_code"] = "200";
        $response["status"] = "OK";
        $response["data"] = (object) $mem;

        return $response;
    }

    public static function memberUpdate()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $mem = DB::table("member_personal_info")->where("id", request('id'))->get()->first();
        if ($mem == (null || "" || false)) {
            $response['error_message'] = "requsted member not found with id: " . request('id');
            return $response;
        }
        if ($mem->memberPersonalCode != request("memberCode")) {
            $response['error_message'] = "member code cannot be change.";
            return $response;
        }

        $res = DB::table("member_personal_info")->where("id", request('id'))->update([
            "memberPersonalName" => request("name"),
            "memberPersonalFatherName" => request("fatherName"),
            "memberPersonalGender" => strtolower(request("gender")),
            "memberPersonalEmail" => request("email"),
            "memberPersonalAadarNumber" => request("aadharNumber"),
            "memberPersonalMobileNumber" => request("mobileNumber"),
            "memberPersonalAddress" => request("address"),
            "memberPersonalState" => request("state"),
            "memberPersonalCity" => request("city"),
            "memberPersonalDistrictVillage" => request("districtVillage"),
            "memberPersonalMobilePincode" => request("pin"),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $res1 = DB::table('member_personal_bank_info')
            ->where('memberPersonalUserId', request('id'))
            ->update([
                'memberPersonalBankName' => request('bankName') . "",
                'memberPersonalAccountName' => request('accHolderName') . "",
                'memberPersonalAccountNumber' => request('accNumber') . "",
                'memberPersonalIfsc' => request('ifscCode') . "",
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $res2 = DB::table('member_other_info')
            ->where('memberId', request('id'))
            ->update([
                'alert_sms' => request('alertSms'),
                'alert_email' => request('alertEmail'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($res || $res1 || $res2) {
            $response["data"] = [];
            $response["status"] = "OK";
            $response["status_code"] = "200";
            $response["success_message"] = "Member info updated successfuly.";
            return $response;
        }
        $response['error_message'] = "some error has occured during updating member.";
        return $response;
    }

    public static function memberNew($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $mem = DB::table("member_personal_info")->where(
            ["memberPersonalCode" => request('memberCode'),
                "dairyId" => $dairyId])->get()->first();
        if ($mem != (null || "" || false)) {
            $response['error_message'] = "Member Code is already being used: " . request('memberCode');
            return $response;
        }

        $mem = DB::table("member_personal_info")->where(["memberPersonalMobileNumber" => request('mobileNumber'),
            "dairyId" => $dairyId])->get()->first();
        if ($mem != (null || "" || false)) {
            $response['error_message'] = "Mobile Number is already being used: " . request('mobileNumber');
            return $response;
        }

        $m = new memberSetup();
        $r = $m->memberAdd($dairyId, $colMan);

        if ($r['error']) {
            $response['error_message'] = $r['msg'];
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = $r['msg'];
        return $response;
    }

    public static function memberDelete($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        DB::table('member_personal_info')
            ->where('id', request('memberId'))
            ->where('dairyId', $dairyId)
            ->update([
                'status' => "false",
        ]);

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Member deleted successfully.";
        return $response;
    }

    public static function getCustomer($dairyId)
    {
        $customer = DB::table("customer")->where("customer.dairyId", $dairyId)->where("customer.status", "true")
            ->select("customer.id as id", "customer.ledgerId as ledgerId", "customerCode", "customerName", "gender", "customerEmail",
                "customerMobileNumber as mobileNumber", "customerAddress as address", "states.name as state",
                "city.name as city", "customerVillageDistrict as districtVillage", "customerPincode as pin", "customer.created_at",
                "user_current_balance.openingBalance", "user_current_balance.openingBalanceType")
            ->leftJoin("city", "city.id", "=", "customerCity")
            ->leftJoin("states", "states.id", "=", "customerState")
            ->leftJoin("user_current_balance", "user_current_balance.ledgerId", "=", "customer.ledgerId")
            ->get();
        if ($customer == (null || "" || false)) {
            return [];
        }
        return $customer;
    }

    public static function getCustomerSingle($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        if (request("customerId") == null) {
            $response["error_message"] = "Customer Id Required to get customer details.";
            return $response;
        }

        $customer = DB::table("customer")->where(["customer.dairyId" => $dairyId, "customer.id" => request("customerId"), "customer.status" => "true"])
            ->select("customer.id as id", "customer.ledgerId", "customerCode", "customerName", "gender", "customerEmail",
                "customerMobileNumber as mobileNumber", "customerAddress as address", "states.name as state",
                "city.name as city", "customerVillageDistrict as districtVillage", "customerPincode as pin", "customer.created_at",
                "user_current_balance.openingBalance", "user_current_balance.openingBalanceType")
            ->leftJoin("city", "city.id", "=", "customerCity")
            ->leftJoin("states", "states.id", "=", "customerState")
            ->leftJoin("user_current_balance", "user_current_balance.ledgerId", "=", "customer.ledgerId")
            ->get()->first();

        if ($customer == (null || "" || false)) {
            $response["error_message"] = "Customer not found.";
            return $response;
        }

        $response["status_code"] = "200";
        $response["status"] = "OK";
        $response["data"] = (object) $customer;

        return $response;
    }

    public static function customerUpdate()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $cust = DB::table("customer")->where("id", request('id'))->get()->first();
        if ($cust == (null || "" || false)) {
            $response['error_message'] = "requsted customer not found with id: " . request('id');
            return $response;
        }
        if ($cust->customerCode != request("customerCode")) {
            $response['error_message'] = "customer code cannot be change.";
            return $response;
        }

        $res = DB::table("customer")->where("id", request('id'))->update([
            "customerName" => request("customerName"),
            "gender" => strtolower(request("gender")) . "",
            "customerEmail" => request("customerEmail") . "",
            "customerMobileNumber" => request("mobileNumber"),
            "customerAddress" => request("address") . "",
            "customerState" => request("state") . "",
            "customerCity" => request("city") . "",
            "customerVillageDistrict" => request("districtVillage") . "",
            "customerPincode" => request("pin") . "",
        ]);
        if ($res == (null || false)) {
            $response['error_message'] = "some error has occured during updating customer.";
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "customer updated successfuly.";
        return $response;
    }

    public static function customerNew($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $mem = DB::table("customer")->where(
            ["customerCode" => $dairyId . "C" . request('customerCode'),
                "dairyId" => $dairyId])->get()->first();
        if ($mem != (null || "" || false)) {
            $response['error_message'] = "Custoemr Code is already being used: " . request('customerCode');
            return $response;
        }

        $mem = DB::table("customer")->where(["customerMobileNumber" => request('mobileNumber'),
            "dairyId" => $dairyId])->get()->first();
        if ($mem != (null || "" || false)) {
            $response['error_message'] = "Mobile Number is already being used: " . request('mobileNumber');
            return $response;
        }

        $m = new Customer();
        $r = $m->customerNewApi($dairyId, $colMan);

        if ($r["error"]) {
            $response['error_message'] = $r['msg'];
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "customer inserted successfuly.";
        return $response;
    }

    public static function getSuppliers($dairyId)
    {
        $sup = DB::table("suppliers")->where("suppliers.dairyId", $dairyId)->where("suppliers.status", "true")
            ->select("suppliers.id as id", "suppliers.ledgerId", "supplierCode", "supplierFirmName", "gender", "supplierPersonName",
                "supplierEmail as email", "supplierMobileNumber as mobileNumber", "supplierGstin as gstin",
                "supplierAddress as address", "states.name as state", "city.name as city", "supplierVillageDistrict as villageDistrict",
                "supplierPincode as pin", "suppliers.created_at",
                "user_current_balance.openingBalance", "user_current_balance.openingBalanceType")
            ->leftJoin("city", "city.id", "=", "supplierCity")
            ->leftJoin("states", "states.id", "=", "supplierState")
            ->leftJoin("user_current_balance", "user_current_balance.ledgerId", "=", "suppliers.ledgerId")
            ->get();
        if ($sup == (null || "" || false)) {
            return [];
        }
        return $sup;
    }

    public static function getSupplier($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        if (request("supplierId") == null) {
            $response["error_message"] = "Supplier Id Required to get supplier details.";
            return $response;
        }

        $supplier = DB::table("suppliers")->where(["suppliers.dairyId" => $dairyId, "suppliers.id" => request("supplierId"), "suppliers.status" => "true"])
            ->select("suppliers.id as id", "suppliers.ledgerId", "supplierCode", "supplierFirmName", "gender", "supplierPersonName",
                "supplierEmail as email", "supplierMobileNumber as mobileNumber", "supplierGstin as gstin",
                "supplierAddress as address", "states.name as state", "city.name as city", "supplierVillageDistrict as villageDistrict",
                "supplierPincode as pin", "suppliers.created_at",
                "user_current_balance.openingBalance", "user_current_balance.openingBalanceType")
            ->leftJoin("city", "city.id", "=", "supplierCity")
            ->leftJoin("states", "states.id", "=", "supplierState")
            ->leftJoin("user_current_balance", "user_current_balance.ledgerId", "=", "suppliers.ledgerId")
            ->get()->first();

        if ($supplier == (null || "" || false)) {
            $response["error_message"] = "Supplier not found.";
            return $response;
        }

        $response["status_code"] = "200";
        $response["status"] = "OK";
        $response["data"] = (object) $supplier;

        return $response;
    }

    public static function supplierUpdate()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $sup = DB::table("suppliers")->where("id", request('id'))->get()->first();
        if ($sup == (null || "" || false)) {
            $response['error_message'] = "requsted supplier not found with id: " . request('id');
            return $response;
        }
        if ($sup->supplierCode != request("supplierCode")) {
            $response['error_message'] = "supplier code cannot be change.";
            return $response;
        }

        $res = DB::table("suppliers")->where("id", request('id'))->update([
            "supplierCode" => request("supplierCode"),
            "supplierFirmName" => request("supplierFirmName"),
            "gender" => strtolower(request("gender")) . "",
            "supplierEmail" => request("email") . "",
            "supplierPersonName" => request("supplierPersonName"),
            "supplierMobileNumber" => request("mobileNumber"),
            "supplierGstin" => request("gstin") . "",
            "supplierAddress" => request("address") . "",
            "supplierState" => request("state") . "",
            "supplierCity" => request("city") . "",
            "supplierVillageDistrict" => request("villageDistrict") . "",
            "supplierPincode" => request("pin") . "",
        ]);
        if ($res == (null || false)) {
            $response['error_message'] = "some error has occured during updating supplier.";
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "supplier updated successfuly.";
        return $response;
    }

    public static function supplierNew($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $sup = DB::table("suppliers")->where(
            ["supplierCode" => $dairyId . "S" . request('supplierCode'),
                "dairyId" => $dairyId])->get()->first();
        if ($sup != (null || "" || false)) {
            $response['error_message'] = "Supplier Code is already being used: " . request('supplierCode');
            return $response;
        }

        $sup = DB::table("suppliers")->where(["supplierMobileNumber" => request('mobileNumber'),
            "dairyId" => $dairyId])->get()->first();
        if ($sup != (null || "" || false)) {
            $response['error_message'] = "Mobile Number is already being used: " . request('mobileNumber');
            return $response;
        }

        $m = new supplier();
        $r = $m->supplierNewApi($dairyId, $colMan);

        if ($r["error"]) {
            $response['error_message'] = $r['msg'];
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "supplier inserted successfuly.";
        return $response;
    }

    public static function milkPlantListForDairy($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "",
        );
        $response["data"] = (object) DB::table('plantdairymap')->where(['plantdairymap.dairyId' => $dairyId])
            ->select("milk_plants.id as plantId", "milk_plants.plantCode", "milk_plants.plantName", "milk_plants.contactNumber",
                "milk_plants.address", "states.name as state", "city.name as city", "milk_plants.pincode", "milk_plant_head.id as plantHeadId", "milk_plant_head.headName",
                "milk_plant_head.email as headEmail", "milk_plant_head.mobile as headMobile")
            ->leftjoin("milk_plants", "plantdairymap.plantId", "=", "milk_plants.id")
            ->leftjoin("milk_plant_head", "milk_plants.id", "=", "milk_plant_head.plantId")
            ->leftjoin("city", "milk_plants.city", "=", "city.id")
            ->leftjoin("states", "milk_plants.state", "=", "states.id")
            ->get();

        return $response;
    }

    public static function mainMilkPlants()
    {
        $response = array("error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "",
        );

        $response["data"] = (object) DB::table('milk_plants')
            ->where(["milk_plants.status" => "true", "milk_plants.isMainPlant" => 1])
            ->select("milk_plants.id", "milk_plants.status", "isMainPlant", "parentPlantId", "plantCode",
                "plantName", "contactNumber", "address", "states.name as state", "city.name as city", "pincode")
            ->leftjoin("city", "milk_plants.city", "=", "city.id")
            ->leftjoin("states", "milk_plants.state", "=", "states.id")
            ->get();
        return $response;
    }

    public static function milkPlantsList()
    {
        $response = array("error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "",
        );

        $q = DB::table('milk_plants');
        if (request('mainPlantId')) {
            $q->where(["milk_plants.parentPlantId" => request("mainPlantId")]);
        }

        $response["data"] = $q->where(["milk_plants.status" => "true", "isMainPlant" => 0])
            ->select("*", "milk_plants.id as id", "city.name as city", "states.name as state")
            ->leftjoin("states", "states.id", "=", "milk_plants.state")
            ->leftjoin("city", "city.id", "=", "milk_plants.city")
            ->get();

        return $response;
    }

    public static function milkPlantAddToDairy()
    {
        $response = array("error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "",
            "data" => (object) []
        );

        $plantModel = new milkPlant();

        $r = $plantModel->milkPlantAddRequest();
        if ($r) {
            $response["success_message"] = Session::get("msg");
        } else {
            $response["error_message"] = Session::get("msg");
            $response['status_code'] = 202;
            $response['status'] = "ERROR";
        }
        return $response;
    }

    public static function getProducts($dairyId)
    {
        $pro = DB::table("products")->where("products.dairyId", $dairyId)->where("products.status", "true")
            ->select("products.id as id", "productCode", "productName", "productUnit as stock", "suppliers.supplierFirmName as supplierFirmName",
                "suppliers.supplierCode as supplierCode", "purchaseAmount", "amount as sellingPrice", "products.created_at as created_at")
            ->join("suppliers", "products.supplierId", "=", "suppliers.id")
            ->get();
        if ($pro == (null || "" || false)) {
            return [];
        }
        return $pro;
    }

    public static function getProduct($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        if (request("productId") == null) {
            $response["error_message"] = "Product Id Required to get product details.";
            return $response;
        }

        $pro = DB::table("products")->where(["products.dairyId" => $dairyId, "products.id" => request('productId'), "products.status" => "true"])
            ->select("products.id as id", "productCode", "productName", "productUnit as stock", "suppliers.supplierFirmName as supplierFirmName",
                "suppliers.supplierCode as supplierCode", "purchaseAmount", "amount as sellingPrice", "products.created_at as created_at")
            ->join("suppliers", "products.supplierId", "=", "suppliers.id")
            ->get()->first();

        if ($pro == (null || "" || false)) {
            $response["error_message"] = "Supplier not found.";
            return $response;
        }

        $response["status_code"] = "200";
        $response["status"] = "OK";
        $response["data"] = (object) $pro;

        return $response;
    }

    public static function productUpdate()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $pro = DB::table("products")->where("id", request('id'))->get()->first();
        if ($pro == (null || "" || false)) {
            $response['error_message'] = "requsted product not found with id: " . request('id');
            return $response;
        }

        $res = DB::table("products")->where("id", request('id'))->update([
            "productName" => request("productName"),
            "amount" => request("sellingPrice"),
        ]);
        if ($res == (null || false)) {
            $response['error_message'] = "some error has occured during updating product.";
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "product updated successfuly.";
        return $response;
    }

    public static function productNew($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $pro = DB::table("products")->where(
            ["productCode" => $dairyId . "P" . request('productCode'),
                "dairyId" => $dairyId])->get()->first();
        if ($pro != (null || "" || false)) {
            $response['error_message'] = "Product Code is already being used: " . request('productCode');
            return $response;
        }

        $m = new product();
        $r = $m->productNewAPI($dairyId, $colMan);

        if ($r["error"]) {
            $response['error_message'] = $r['msg'];
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Product inserted successfuly.";
        return $response;
    }

    public static function productStockUpdate($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $pro = DB::table("products")->where(["productCode" => $dairyId . "P" . request('productCode'),
            "dairyId" => $dairyId])->get()->first();
        if ($pro != (null || "" || false)) {
            $response['error_message'] = "Product Code is already being used: " . request('productCode');
            return $response;
        }

        request()->request->add(['productUnit' => request('quantity')]);
        request()->request->add(['productAmount' => request('sellingPrice')]);
        request()->request->add(['supplier' => request('supplierId')]);
        request()->request->add(['productAmount' => request('sellingPrice')]);

        $submitClass = new product();
        $r = $submitClass->productStockSubmit();

        if (!$r) {
            $response['error_message'] = Session::get("msg");
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = Session::get("msg");
        return $response;
    }

    public static function getExpense($dairyId)
    {
        $expenses = DB::table('expense_setups')
            ->select("expense_setups.id", "expense_setups.date", "expense_setups.time", "expense_setups.expenseType as expenseDesc", "expenses.expenseHeadName",
                "expense_setups.expenseType as expenseHeadId", "expense_setups.paymentMode", "expense_setups.amount", "expense_setups.created_at")
            ->where('expense_setups.dairyId', $dairyId)
            ->where('expense_setups.status', "true")
            ->leftJoin("expenses", "expenses.id", "=", "expense_setups.expenseType")
            ->orderBy("created_at", "desc")
            ->get();

        if ($expenses == (null || "" || false)) {
            return [];
        }
        return $expenses;
    }

    public static function getExpenseHeads($dairyId)
    {
        $head = DB::table('expenses')
            ->select("id", "expenseHeadCode", "expenseHeadName", "expenseDescription", "created_at")
            ->where('dairyId', $dairyId)
            ->where('status', "true")
            ->get();

        if ($head == (null || "" || false)) {
            return [];
        }
        return $head;
    }

    public static function expenseHeadNew($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $submitClass = new expense();
        $r = $submitClass->expenseHeadSubmitAPI((object) request()->all(), $dairyId);

        if (!$r) {
            $response['error_message'] = "An error while saving your data.";
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "expense inserted successfuly.";
        return $response;
    }

    public static function expenseNew($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $ex = new expense_setup();
        $r = $ex->expenseNewAPI($dairyId, $colMan);

        if ($r["error"]) {
            $response['error_message'] = $r['msg'];
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "expense inserted successfuly.";
        return $response;
    }

    public static function milkCollectionList($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $q = DB::table('daily_transactions');
        $q = $q->where(['dairyId' => $dairyId, "status" => "true"]);
        if (request("date") != null) {
            $date = date("Y-m-d", strtotime(request('date')));
            $q->where("date", $date);
        } else {
            $q->where("date", ">", date("Y-m-d", strtotime("-3 day")));
            $date = date("Y-m-d", time());
        }
        if (request('shift') != null) {
            $q->where("shift", strtolower(request('shift')));
        }

        $dailyTransactions = $q->orderBy('created_at', 'desc')->get();

        if (!$dailyTransactions) {
            $response['error_message'] = "Something happend! An error occured.";
            return $response;
        }

        $msc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d", strtotime($date)))->where("shift", "morning")
            ->where('status', "true")
            ->sum("milkQuality");

        $esc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d", strtotime($date)))->where("shift", "evening")
            ->where('status', "true")
            ->sum("milkQuality");

        $data = [
            "dailyTransactions" => $dailyTransactions,
            "milkCollectionDetails" => [
                "date" => $date,
                "morningCollection" => number_format($msc,2,".",""),
                "eveningCollection" => number_format($esc,2,".",""),
            ],
        ];

        $response["data"] = (object) $data;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Success.";
        return $response;
    }

    public static function milkCollectionAdd($dairyId = null)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $dairy = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        if ($dairy == null) {
            $response['error_message'] = 'Some error occured while processing your request.';
            return $response;
        }

        $mc = new dailyTransaction();
        $r = $mc->DailyTransactionSubmit(request());

        if ($r["error"]) {
            $response['error_message'] = $r['msg'];
            return $response;
        } else {
            $app_data = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $r['memberId']])->get();
            $appSettings = DB::table('androidappsetting')->get()->first();

            if ($app_data == null) {
                $memberToken = null;
            } else {
                $memberToken = $app_data->pluck('token_key');
            }

            // echo json_encode($r); exit;

            $data = [
                "dairyId" => $dairyId,
                "memberId" => $r['memId'],
                "mobile" => $r["mobile"],
                "society_code" => $dairy->society_code,
                "society_name" => $dairy->dairyName,
                "memberName" => $r['memberName'],
                "dailyShift" => request("dailyShift"),
                "date" => request('date'),
                "quantity" => request('quantity'),
                "fat" => request('fat'),
                "snf" => request('snf'),
                "rate" => $r['rate'],
                "amount" => $r['amount'],
                "bal" => $r['currentBalance'],
                "memberToken" => $memberToken,
                "serverKey" => $appSettings->server_api_key,
            ];

            $d = new \App\Http\Controllers\DailyTransactionController();

            if ($dairyId != null) {
                $alerts = DB::table("member_other_info")->where(["memberId" => $r["memId"]])->get()->first();
                if ($alerts->alert_sms == "true") {
                    $d->dailyTransactionSmsTemplate($data);
                }
            }

            $noti = $d->dailyTransactionPushNoti($data);

        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Milk collection record save successfuly.";
        return $response;
    }

    public static function milkCollectionEdit($dairyId = null)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => []
        );

        $dairy = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        if ($dairy == null) {
            $response['error_message'] = 'Some error occured while processing your request.';
            return $response;
        }

        $updateClass = new dailyTransaction();
        $r = $updateClass->DailyTransactionEditSubmit(request());

        if ($r) {

            $response['status'] = "OK";
            $response['status_code'] = "200";
            $response['success_message'] = "Milk collection edited successfully.";

            return $response;
        } else {
            $response['error_message'] = Session::get('msg');
            return $response;

        }

    }

    public static function localSale()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $sl = new sales();
        $r = $sl->localSaleFormSubmitAPI(request());

        if ($r == false) {
            $response['error_message'] = Session::get("msg");
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "local sale successfuly done.";
        return $response;
    }

    public static function getLocalSale($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $q = DB::table('sales');
        $q = $q->select("id", "partyType", "partyCode", "partyName", "productType", "productQuantity", "productPricePerUnit", "saleDate", "amountType", "amount", "discount", "finalAmount", "paidAmount")
            ->where(['dairyId' => $dairyId, 'status' => "true", "saleType" => "local_sale"]);
        if (request("to") != null && request("from") != null) {
            $q = $q->whereBetween('saleDate', [date("Y-m-d", strtotime(request("from"))), date("Y-m-d", strtotime(request("to")))]);
        } else {
            $q = $q->limit(50);
        }

        $ls = $q->orderby("created_at", "desc")->get();

        if ($ls == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) $ls;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function getPlantSale($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        if (request('plantCode') == null) {
            $response['data'] = DB::table('sales')
                ->select("partyCode", "partyName", "partyType", "productType", "milkType", "productQuantity", "saleFromDate", "saleDate", "saleType",
                    "amountType", "amount", "discount", "finalAmount", "paidAmount", "remark", "created_at")
                ->where('dairyId', session()->get('loginUserInfo')->dairyId)
                ->where('saleType', "plant_sale")
                ->limit(50)
                ->orderBy('saleFromDate', "desc")
                ->get();
        } else {
            $response['data'] = DB::table('sales')
                ->select("partyCode", "partyName", "partyType", "productType", "milkType", "productQuantity", "saleFromDate", "saleDate", "saleType",
                    "amountType", "amount", "discount", "finalAmount", "paidAmount", "remark", "created_at")
                ->where('dairyId', session()->get('loginUserInfo')->dairyId)
                ->where('partyType', "plant")
                ->where('partyCode', request('plantCode'))
                ->orderBy('saleFromDate', "desc")
                ->limit(50)
                ->get();
        }
        return $response;
    }

    public static function plantSale()
    {
        // \Log::error(json_encode(request()->all()));

        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        request()->request->add(['sale_type' => "plant_sale"]);

        $submitClass = new sales();
        $res = $submitClass->localSaleFormSubmitAPI((object) request()->all());

        if ($res) {
            $response['success_message'] = Session::get("msg");
            $response['status_code'] = "200";
            $response['status'] = "OK";
        } else {
            $response['error_message'] = Session::get("msg");
        }
        return $response;
    }

    public static function productSale($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $sl = new sales();
        $r = $sl->localSaleFormSubmitAPI(request());

        if ($r == false) {
            $response['error_message'] = Session::get("msg");
            return $response;
        } else {
            $mobile = null;
            $bal = null;
            $name = null;
            if (request("partyType") == "member") {
                $m = DB::table("member_personal_info")->where(['memberPersonalCode' => request('partyCode'), "dairyId" => $dairyId])->get()->first();
                if ($m != null) {
                    $mobile = $m->memberPersonalMobileNumber;
                    $name = $m->memberPersonalName;
                    $ub = DB::table("user_current_balance")
                        ->where('ledgerId', $m->ledgerId)
                        ->get()->first();
                    $alerts = DB::table("member_other_info")->where(["memberId" => $m->id])->get()->first();
                    if ($alerts->alert_sms != "true") {
                        goto SKIPSMS;
                    }
                } else {
                    goto SKIPSMS;
                }
            } else {
                goto SKIPSMS;
            }

            if ($ub) {
                $ub->openingBalance = number_format($ub->openingBalance, 2, ".", "");
                if ($ub->openingBalanceType == "credit") {
                    $bal = $ub->openingBalance . " CR";
                } else {
                    $bal = $ub->openingBalance . " DR";
                }

            } else {
                goto SKIPSMS;
            }

            if ($mobile == null) {
                goto SKIPSMS;
            }

            $product = DB::table('products')
                ->where('dairyId', $dairyId)
                ->where('productCode', request('product'))
                ->get()->first();

            $a = number_format((request('quantity') * $product->amount) - request('discount'), 2, ".", "");
            $f = number_format($a, 2, ".", "");
            $pa = number_format("0" . request("paidAmount"), 2, ".", "");
            $dairy = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

            $tempName = explode(" ", $name);
            if (isset($tempName[1])) {
                $name = $tempName[0] . $tempName[1];
            } else {
                $name = $tempName[0];
            }

            $newLine = "%0A";
            $data = [
                "message" => "Dear $name," . $newLine . $dairy->society_code . " - " . $dairy->dairyName . $newLine .
                "Date: " . request('date') . $newLine .
                "Product: " . $product->productName . $newLine .
                "Qty: " . request('quantity') . $newLine .
                "Rate: " . $product->amount . $newLine .
                // "Amount: " . $a . $newLine .
                "Discount: " . request('discount') . $newLine .
                "Paid Amt: " . $pa . $newLine .
                "Final Amt: " . $f . $newLine .
                "Current Balance: $bal" . $newLine,
                "numbers" => $mobile,
                "messageType" => "productSale"
            ];
            $sms = new \App\Sms();

            // $sms->saveToQueue($data, $dairyId);
            $sms->send($data, $dairyId);
        }

        SKIPSMS:
        $appSettings = DB::table('androidappsetting')->get()->first();

        if(Session::has('noti_message') && Session::has('token_key')){
            $noti = PushNotification::setService('fcm')
                ->setMessage([
                'data' => [
                        "message" =>Session::get('noti_message')
                            ]
                        ])
                ->setApiKey($appSettings->server_api_key)
                ->setDevicesToken(Session::get('token_key'))
                ->send()
                ->getFeedback();

                session()->forget('token_key');
                session()->forget('noti_message');
            // Session::flash('msg', json_encode($noti));
        }

        $response['data'] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Product sale successfuly done.";
        return $response;
    }

    public static function getProductSale($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $q = DB::table('sales');
        $q = $q->select("id", "partyType", "partyCode", "partyName", "productType as productCode", "productQuantity", "productPricePerUnit", "saleDate", "amountType", "amount", "discount", "finalAmount", "paidAmount")
            ->where(['dairyId' => $dairyId, 'status' => "true", "saleType" => "product_sale"]);
        if (request("to") != null && request("from") != null) {
            $q = $q->whereBetween('saleDate', [date("Y-m-d", strtotime(request("from"))), date("Y-m-d", strtotime(request("to")))]);
        } else {
            $q = $q->limit(50);
        }

        $pros = $q->orderby("created_at", "desc")->get();

        // $pros = DB::table('sales')
        //             ->select("id", "partyType", "partyCode", "partyName", "productType as productCode", "productQuantity", "productPricePerUnit", "saleDate", "amountType", "amount", "discount", "finalAmount", "paidAmount")
        //             ->where(['dairyId' => $dairyId, 'status' => "true", "saleType" => "product_sale"])
        //             ->whereBetween('saleDate', [date("Y-m-d", strtotime(request("from"))), date("Y-m-d", strtotime(request("to")))])
        //             ->get();

        if ($pros == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }
        foreach ($pros as &$s) {
            $p = DB::table("products")->select("productName")->where("productCode", $s->productCode)->get()->first();
            if ($p != (null || false || "")) {
                $s->productName = $p->productName;
            } else {
                $s->productName = "-";
            }

        }

        $response['data'] = (object) $pros;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function getAdvance($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $advance = DB::table('advance')
            ->select("id", "date", "partyType", "partyCode", "partyName", "amount", "remark")
            ->where('dairyId', $dairyId)
            ->orderby('created_at', "desc")
            ->get();

        if ($advance == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) $advance;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function getCredit($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $credit = DB::table('credit')
            ->select("id", "date", "partyType", "partyCode", "partyName", "amount", "remark")
            ->where('dairyId', $dairyId)
            ->orderby("created_at", "desc")
            ->get();

        if ($credit == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) $credit;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function addAdvance($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $ad = new AdvanceCredit();
        $r = $ad->addAdvanceAPI(request(), $colMan);

        if ($r) {
            $dairy = DB::table('dairy_info')->where(["id" => $dairyId])->get()->first();
            $mobile = null;
            $name = null;
            if (request("partyType") == "member") {
                $m = DB::table("member_personal_info")->where(['memberPersonalCode' => request('partyCode'), "dairyId" => $dairyId])->get()->first();
                if ($m != null) {
                    $app_data = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $m->id])->get();
                    $appSettings = DB::table('androidappsetting')->get()->first();

                    if ($app_data == null) {
                        $memberToken = null;
                    } else {
                        $memberToken = $app_data->pluck('token_key');
                    }

                    $name = $m->memberPersonalName;
                    $mobile = $m->memberPersonalMobileNumber;
                    $ub = DB::table("user_current_balance")
                        ->where('ledgerId', $m->ledgerId)
                        ->get()->first();
                } else {
                    goto SKIPSMS;
                }
            } else {
                goto SKIPSMS;
            }

            if (isset($ub) && $ub) {
                $ub->openingBalance = number_format($ub->openingBalance, 2, ".", "");
                if ($ub->openingBalanceType == "credit") {
                    $bal = $ub->openingBalance . " CR";
                } else {
                    $bal = $ub->openingBalance . " DR";
                }

            } else {
                goto SKIPSMS;
            }

            if ($mobile == null) {
                goto SKIPSMS;
            }

            $tempName = explode(" ", $name);
            if (isset($tempName[1])) {
                $name = $tempName[0] . $tempName[1];
            } else {
                $name = $tempName[0];
            }

            $newLine = "\n";
            $message = "Dear $name," . $newLine . $dairy->society_code . " - " . $dairy->dairyName . $newLine .
            "Date: " . request('date') . $newLine .
            "Dabit Amount: " . number_format(request('amount'), 2, ".", "") . $newLine .
            "Remarks: " . request("remark") . $newLine .
                "Current Balance: " . $bal . $newLine;

            
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
            if ($alerts->alert_sms != "true") {
                goto SKIPSMS;
            }

            $newLine = "%0A";
            $data = [
                "message" => "Dear $name," . $newLine . $dairy->society_code . " - " . $dairy->dairyName . $newLine .
                "Date: " . request('date') . $newLine .
                "Dabit Amount: " . number_format(request('amount'), 2, ".", "") . $newLine .
                "Remarks: " . request("remark") . $newLine .
                "Current Balance: " . $bal . $newLine,
                "numbers" => $mobile,
                "messageType" => "advance"
            ];
            $sms = new \App\Sms();

            // $sms->saveToQueue($data, $dairyId);
            $sms->send($data, $dairyId);
        }

        SKIPSMS:

        if ($r == false) {
            $response['error_message'] = Session::get("msg");
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Advance completed successfuly.";
        return $response;
    }

    public static function addCredit($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $ad = new AdvanceCredit();
        $r = $ad->addCreditAPI(request(), $colMan);

        if ($r) {
            $dairy = DB::table('dairy_info')->where(["id" => $dairyId])->get()->first();
            $mobile = null;
            $name = null;
            if (request("partyType") == "member") {
                $m = DB::table("member_personal_info")->where(['memberPersonalCode' => request("partyCode"), "dairyId" => $dairyId])->get()->first();
                if ($m != null) {
                    $app_data = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $m->id])->get();
                    $appSettings = DB::table('androidappsetting')->get()->first();

                    if ($app_data == null) {
                        $memberToken = null;
                    } else {
                        $memberToken = $app_data->pluck('token_key');
                    }

                    $name = $m->memberPersonalName;
                    $mobile = $m->memberPersonalMobileNumber;
                    $ub = DB::table("user_current_balance")
                        ->where('ledgerId', $m->ledgerId)
                        ->get()->first();
                } else {
                    goto SKIPSMS;
                }
            } else {
                goto SKIPSMS;
            }

            if (isset($ub) && $ub) {
                $ub->openingBalance = number_format($ub->openingBalance, 2, ".", "");
                if ($ub->openingBalanceType == "credit") {
                    $bal = $ub->openingBalance . " CR";
                } else {
                    $bal = $ub->openingBalance . " DR";
                }

            } else {
                goto SKIPSMS;
            }

            if ($mobile == null) {
                goto SKIPSMS;
            }

            $tempName = explode(" ", $name);
            if (isset($tempName[1])) {
                $name = $tempName[0] . $tempName[1];
            } else {
                $name = $tempName[0];
            }

            $newLine = "\n";
            $message = "Dear $name," . $newLine . $dairy->society_code . " - " . $dairy->dairyName . $newLine .
            "Date: " . request('date') . $newLine .
            "Credited Amount: " . number_format(request('credit'), 2, ".", "") . $newLine .
            "Remark: " . request('remark') . $newLine .
                "Current Balance: $bal" . $newLine;

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
            if ($alerts->alert_sms != "true") {
                goto SKIPSMS;
            }

            $newLine = "%0A";
            $data = [
                "message" => "Dear $name," . $newLine . $dairy->society_code . " - " . $dairy->dairyName . $newLine .
                "Date: " . request('date') . $newLine .
                "Credited Amount: " . number_format(request('credit'), 2, ".", "") . $newLine .
                "Remark: " . request('remark') . $newLine .
                "Balance: $bal" . $newLine,
                "numbers" => $mobile,
                "messageType" => "credit"
            ];
            $sms = new \App\Sms();

            // $sms->saveToQueue($data, $dairyId);
            $sms->send($data, $dairyId);
        }

        SKIPSMS:

        if ($r == false) {
            $response['error_message'] = Session::get("msg");
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Credited successfuly.";
        return $response;
    }

    public static function memDailyTransactionList($u)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $q = DB::table('daily_transactions');

        if (request("from") && request("to")) {
            $q = $q->where("date", ">=", date("Y-m-d", strtotime(request("from"))))
                ->where("date", "<=", date("Y-m-d", strtotime(request("to"))));
            $from = date("Y-m-d", strtotime(request("from")));
            $to = date("Y-m-d", strtotime(request("to")));
        } else {
            $q = $q->limit(20);
            $from = $to = date("Y-m-d");
        }

        $dailyTransactions = $q->where([
            "dairyId" => $u->dairyId,
            "status" => "true",
            "memberCode" => $u->memberCode,
        ])
            ->selectRaw("id, date, milkType, milkQuality as qty, shift, FORMAT(fat, 1) as fat, FORMAT(rate, 2) as rate, FORMAT(amount, 2) as amount")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($dailyTransactions == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) ["date" => $from, "to" => $to, 'dailyTxn' => $dailyTransactions];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function memPurchaseHistory($u)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $q = DB::table('sales');

        if (request("from") && request("to")) {
            $q = $q->where("sales.saleDate", ">=", date("Y-m-d", strtotime(request("from"))))
                ->where("sales.saleDate", "<=", date("Y-m-d", strtotime(request("to"))));
            $from = date("Y-m-d", strtotime(request("from")));
            $to = date("Y-m-d", strtotime(request("to")));
        } else {
            $q = $q->limit(50);
            $from = $to = date("Y-m-d");
        }

        $purchaseHistory = $q->where(['sales.dairyId' => $u->dairyId,
            "sales.partyCode" => $u->memberCode,
            "sales.status" => "true"])
            ->selectRaw("sales.id as id, sales.productType as productType, IFNULL(products.productName, sales.productType) AS productName, saleDate, saleType, sales.productQuantity as qty, " .
                "sales.productPricePerUnit as rate, FORMAT(sales.amount, 2) as amount, FORMAT(discount, 2) as discount, FORMAT(finalAmount, 2) as finalAmount, amountType, FORMAT(paidAmount, 2) as paidAmount")
            ->leftJoin('products', 'products.productCode', '=', 'sales.productType')
            ->orderBy('sales.created_at', 'desc')
            ->get();

        if ($purchaseHistory == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) ["date" => $from, "to" => $to, 'purchaseHistory' => $purchaseHistory];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function memPayments($u)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        if (request("date")) {
            $date = date("Y-m-d", strtotime(request("date")));
        } else {
            $date = date("Y-m-d");
        }

        $balSheet = DB::table("balance_sheet")->where("ledgerId", $u->ledgerId)
            ->where("created_at", date("Y-m-d", strtotime($date)))
            ->orderby("created_at")->get();

        if ($balSheet == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) ["date" => $date, 'payments' => $balSheet];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function memMilkReqList($u)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $milkReq = DB::table("milkrequest")->where("dairyId", $u->dairyId)->where("memberCode", $u->memberCode)
            ->select("id", "colMan as collectionManager", "date as reqDate", "shift as reqShift", "type as reqType", "comment", "isSeen",
                "seen_at", "productCode", "qty", "isDeliverd", "resText as response", "response_at", "created_at")
            ->whereIn("type", ["cowmilk", "buffalomilk", "milk"])
            ->limit(20)
            ->orderby("created_at", "desc")->get();

        if ($milkReq == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) $milkReq;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function memProdReqList($u)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $prodReq = DB::table("milkrequest")->where("milkrequest.dairyId", $u->dairyId)->where("milkrequest.memberCode", $u->memberCode)
            ->select("milkrequest.id", "milkrequest.colMan as collectionManager", "milkrequest.date as reqDate", "milkrequest.shift as reqShift", "milkrequest.type as reqType", "milkrequest.comment", "milkrequest.isSeen",
                "milkrequest.seen_at", "milkrequest.productCode", "products.productName", "milkrequest.qty", "milkrequest.isDeliverd", "milkrequest.resText as response", "milkrequest.response_at", "milkrequest.created_at")
            ->where("milkrequest.type", "product")
            ->leftjoin("products", "products.productCode", "=", "milkrequest.productCode")
            ->limit(20)
            ->orderby("milkrequest.created_at", "desc")->get();

        if ($prodReq == (null || "" || false)) {
            $response["error_message"] = "No data available.";
            return $response;
        }

        $response['data'] = (object) $prodReq;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function memMilkReqSend($u, $dairyId, $rate)
    {

        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $id = DB::table("milkrequest")->insert([
            "dairyId" => $dairyId,
            "memberCode" => $u->memberCode,
            "colMan" => "DAIRYADMIN",
            "date" => date("Y-m-d", strtotime(request('date'))),
            "type" => strtolower(request('type')),
            "productCode" => request('productCode'),
            "qty" => request('qty'),
            "rate" => $rate,
            "shift" => strtolower(request('shift')),
            "comment" => request('comment') . "",
            "isDeliverd" => 0,
            "isSeen" => "false",
            "resText" => "",
            "created_at" => date("Y-m-d H:i:s"),
        ]);

        if ($id == (null || false || "")) {
            $response['error_message'] = "There are some error occured.";
            return $response;
        }

        $response["data"] = [];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Request sent successfuly.";
        return $response;
    }

    public static function milkRequestList($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $mr = DB::table('milkrequest')
            ->select("milkrequest.id", "memberCode", "memberPersonalName as memberName", "colMan as collectionManager", "milkrequest.date as reqDate", "shift as reqShift", "type as reqType", "comment", "isSeen",
                "seen_at", "qty", "isDeliverd", "resText as response", "response_at", "milkrequest.created_at")
            ->where(['milkrequest.dairyId' => $dairyId, "colMan" => $colMan, "isDeliverd" => 0])
            ->whereIn("type", ["milk", "cowMilk", "buffaloMilk"])
            ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "milkrequest.memberCode")
            ->where(['member_personal_info.dairyId' => $dairyId, "status" => "true"])
            ->limit(50)->get();

        $mrDelivered = DB::table('milkrequest')
            ->select("milkrequest.id", "memberCode", "memberPersonalName as memberName", "colMan as collectionManager", "milkrequest.date as reqDate", "shift as reqShift", "type as reqType", "comment", "isSeen",
                "seen_at", "qty", "isDeliverd", "resText as response", "response_at", "milkrequest.created_at")
            ->where(['milkrequest.dairyId' => $dairyId, "colMan" => $colMan, "isDeliverd" => 1])
            ->whereIn("type", ["milk", "cowMilk", "buffaloMilk"])
            ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "milkrequest.memberCode")
            ->where(['member_personal_info.dairyId' => $dairyId, "status" => "true"])
            ->limit(100)->get();

        $mrDeclined = DB::table('milkrequest')
            ->select("milkrequest.id", "memberCode", "memberPersonalName as memberName", "colMan as collectionManager", "milkrequest.date as reqDate", "shift as reqShift", "type as reqType", "comment", "isSeen",
                "seen_at", "qty", "isDeliverd", "resText as response", "response_at", "milkrequest.created_at")
            ->where(['milkrequest.dairyId' => $dairyId, "colMan" => $colMan, "isDeliverd" => 2])
            ->whereIn("type", ["milk", "cowMilk", "buffaloMilk"])
            ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "milkrequest.memberCode")
            ->where(['member_personal_info.dairyId' => $dairyId, "status" => "true"])
            ->limit(100)->get();

        $response['data'] = (object) ["upcoming" => $mr, "delivered" => $mrDelivered, "rejected" => $mrDeclined];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function productDlvryReqList($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $mr = DB::table('milkrequest')
            ->select("milkrequest.id", "memberCode", "memberPersonalName as memberName", "colMan as collectionManager", "date as reqDate", "shift as reqShift", "type as reqType", "comment", "isSeen",
                "seen_at", "milkrequest.productCode", "productName", "qty", "isDeliverd", "resText as response", "response_at", "milkrequest.created_at")
            ->where(['milkrequest.dairyId' => $dairyId, "colMan" => $colMan, "isDeliverd" => 0, "type" => "product"])
            ->join('products', "products.productCode", "=", "milkrequest.productCode")
            ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "milkrequest.memberCode")
            ->where(['member_personal_info.dairyId' => $dairyId, "member_personal_info.status" => "true"])
            ->limit(50)->get();

        $mrDelivered = DB::table('milkrequest')
            ->select("milkrequest.id","memberCode", "memberPersonalName as memberName", "colMan as collectionManager", "date as reqDate", "shift as reqShift", "type as reqType", "comment", "isSeen",
                "seen_at", "milkrequest.productCode", "productName", "qty", "isDeliverd", "resText as response", "response_at", "milkrequest.created_at")
            ->where(['milkrequest.dairyId' => $dairyId, "colMan" => $colMan, "isDeliverd" => 1, "type" => "product"])
            ->join('products', "products.productCode", "=", "milkrequest.productCode")
            ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "milkrequest.memberCode")
            ->where(['member_personal_info.dairyId' => $dairyId, "member_personal_info.status" => "true"])
            ->limit(100)->get();

        $mrDeclined = DB::table('milkrequest')
            ->select("milkrequest.id", "memberCode", "memberPersonalName as memberName", "colMan as collectionManager", "date as reqDate", "shift as reqShift", "type as reqType", "comment", "isSeen",
                "seen_at", "milkrequest.productCode", "productName", "qty", "isDeliverd", "resText as response", "response_at", "milkrequest.created_at")
            ->where(['milkrequest.dairyId' => $dairyId, "colMan" => $colMan, "isDeliverd" => 2, "type" => "product"])
            ->join('products', "products.productCode", "=", "milkrequest.productCode")
            ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "milkrequest.memberCode")
            ->where(['member_personal_info.dairyId' => $dairyId, "member_personal_info.status" => "true"])
            ->limit(100)->get();

        $response['data'] = (object) ["upcoming" => $mr, "delivered" => $mrDelivered, "rejected" => $mrDeclined];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Data collected.";
        return $response;
    }

    public static function requestComplete()
    {

        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $r = DB::table("milkrequest")->where("id", request("reqId"))->get()->first();

        // echo json_encode($r); exit;

        if (request("action") == "complete") {
            $m = DB::table("member_personal_info")->where(["memberPersonalCode" => $r->memberCode, "dairyId" => $r->dairyId])->get()->first();
            if ($m == (null || false)) {
                $response['error_message'] = "Error: Member not found";
                return $response;
            }
            if (strtolower($r->type) == "product") {
                $p = DB::table('products')->where(["dairyId" => $r->dairyId, "productCode" => $r->productCode])->get()->first();
                if ($p == null || $p->productUnit < $r->qty) {
                    $response['error_message'] = "Stock is not enough to product delivery.";
                    return $response;
                }

                $dt = [
                    'dairyId' => $r->dairyId,
                    'status' => "true",
                    'partyName' => $m->memberPersonalName,
                    'partyType' => 'member',
                    'memberCode' => $r->memberCode,
                    'product' => $r->productCode,
                    'unit' => 'Unit',
                    'quantity' => $r->qty,
                    'PricePerUnit' => $r->rate,
                    'amount' => '0',
                    'discount' => '0',
                    'finalAmount' => number_format((float) $r->qty * (float) $r->rate, 2, ".", ""),
                    'paidAmount' => '0',
                    'date' => date("Y-m-d"),
                    'sale_type' => 'product_sale',
                ];

                $sale = new \App\sales();
                $saleRes = $sale->localSaleFormSubmit((object) $dt);

                if (!$saleRes) {
                    $response['error_message'] = "There is an error occured.";
                    return $response;
                }

                $res = DB::table("milkrequest")->where("id", request("reqId"))->update(["isDeliverd" => 1]);

                $response["status"] = "OK";
                $response["status_code"] = "200";
                $response["success_message"] = "Product delivery done, amount debited";
                return $response;
            }

            if (strtolower($r->type) == ("milk" || "cowmilk" || "buffalomilk")) {
                $res = DB::table("milkrequest")->where("id", request("reqId"))->update(["isDeliverd" => 1]);
                if ($res == null) {
                    $response['error_message'] = "Error in updating.";
                    return $response;
                } else {
                    $response["status"] = "OK";
                    $response["status_code"] = "200";
                    $response["success_message"] = "Milk request completed.";
                    return $response;
                }
            }

            $response['error_message'] = "Something went wrong.";
            return $response;
        } elseif (request("action") == "decline") {
            $res = DB::table("milkrequest")->where("id", request("reqId"))->update(["isDeliverd" => 2]);
            $response["status"] = "OK";
            $response["status_code"] = "200";
            $response["success_message"] = "Request has been decliend.";
            return $response;
        } else {
            $response['error_message'] = "request can't be completed, bed request.";
            return $response;
        }
    }

    public static function getRatecards($dairyId, $colManId)
    {
        if ($dairyId == null || $colManId == null) {
            return ["error" => true, "msg" => "No dairy or user found, please login again."];
        }

        $rateCards = DB::table('ratecardshort')
            ->select("ratecardshort.id as id", "other_users.id as colllectionManagerId",
                "ratecardshort.rateCardType as rateCardType", "ratecardshort.minFat as minFat",
                "ratecardshort.maxFat as maxFat", "ratecardshort.minSnf as minSnf", "ratecardshort.maxSnf as maxSnf")
            ->join("other_users", "other_users.id", "=", "ratecardshort.collectionManager")
            ->where('ratecardshort.dairyId', $dairyId)
            ->where('ratecardshort.collectionManager', $colManId)
            ->get();

        $defaultRc = DB::table("other_users")
            ->select("rateCardIdForCow", "rateCardIdForBuffalo", "rateCardTypeForCow", "rateCardTypeForBuffalo")
            ->where(["dairyId" => $dairyId, "id" => $colManId])->get()->first();

        foreach ($rateCards as &$r) {
            $r->defaultForCow = false;
            $r->defaultForBuffalo = false;

            if ($r->id == $defaultRc->rateCardIdForCow) {
                $r->defaultForCow = true;
            }
            if ($r->id == $defaultRc->rateCardIdForBuffalo) {
                $r->defaultForBuffalo = true;
            }
        }

        return [
            "error" => false,
            "data" => (object) $rateCards,
        ];
    }

    public static function getRatecardDetails($dairyId, $colManId)
    {
        if ($dairyId == null || $colManId == null) {
            return ["error" => true, "msg" => "No dairy or user found, please login again."];
        }

        if (request("rateCardId") == null) {
            return ["error" => true, "msg" => "Rate Card id required."];
        }

        $rtcd = DB::table('ratecardshort')->where(["id" => request("rateCardId")])->get()->first();

        if ($rtcd == null) {
            return ["error" => true, "msg" => "Rate Card not found."];
        }

        $ratecardRanges = DB::table('rangelist')->where(["rateCardId" => request("rateCardId")])
            ->select("id", "mnFat", "mxFat", "rDecFat", "rDecSnf", "rIncFat", "rIncSnf", "rAvgFatSnf",
                "rAvgFatSnf as rateForMinFatSnf")
            ->orderby("mnFat")->get();

        $rateCard = DB::table('fat_snf_ratecard')
            ->where('rateCardShortId', request("rateCardId"))
            ->select("id", "rangeListId", "fatRange", "snfRange", "amount")
            ->orderBy('fatRange')
            ->get();

        $data = [
            "rateCardRanges" => $ratecardRanges,
            "rateCard" => $rateCard,
        ];
        return [
            "error" => false,
            "data" => (object) $data,
        ];
    }

    public static function getMilkPrice($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $price = DB::table("dairy_info")->where("id", $dairyId)->select("cowMilkPrice", "buffaloMilkPrice")->get()->first();

        if (!$price) {
            $response['error_message'] = "An error occured while processing your request.";
            return $response;
        }

        $response["data"] = (object) $price;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Success.";
        return $response;
    }

    public static function productPurchaseHistory($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $purchase = DB::table('purchase_setups')
            ->select("id", "supplierCode", "supplierName", "date", "time", "productCode", "itemPurchased as productName", "quantity", "purchaseType", "amount", "paidAmount", "created_at")
            ->where('dairyId', $dairyId)
            ->where('status', 'true')
            ->orderBy("id", "desc")
            ->limit(100)
            ->get();

        if (!$purchase) {
            $response['error_message'] = "An error while processing your request.";
            return $response;
        }

        $response["data"] = (object) $purchase;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Success.";
        return $response;
    }

    public static function collectionManagers($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $colMans = DB::table("other_users")
            ->select("id", "userName", "fatherName", "aadharNumber", "userEmail", "gender", "mobileNumber", "address", "state", "city", "villageDistrict", "pincode", "created_at")
            ->where("dairyId", $dairyId)->get();

        if (!$colMans) {
            $response['error_message'] = "An error while processing your request.";
            return $response;
        }

        $response["data"] = (object) $colMans;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Success.";
        return $response;
    }

    public static function addCollectionManager($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []
        );

        $colMan = new \App\Http\Controllers\ColmanController();
        $res = $colMan->createNewColMan((object) request());

        if (!$res) {
            $response['error_message'] = Session::get('msg');
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = Session::get('msg');
        return $response;
    }

    public static function dairyBalance($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $dairyInfo = DB::table("dairy_info")->select("cash_in_hand")->where("id", $dairyId)
            ->get()->first();

        if (!$dairyInfo) {
            $response['error_message'] = "An error while processing your request.";
            return $response;
        }

        $response["data"] = (object) $dairyInfo;
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Success.";
        return $response;
    }

    public static function dairySubscription($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
        );

        $subsc = DB::table("subscribe")
            ->select("subscribe.dairyId", "subscribe.planType", "dateOfSubscribe", "dateOfPayment", "expiryDate", "isPaymentDone",
                "subscribe.amount", "trialEndDate", "subscription_plan.name as planName")
            ->where(["dairyId" => $dairyId])
            ->join("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")
            ->get()->first();

        $payments = DB::table("payments")
            ->select("payments.id", "trans_date", "name", "amount", "status_message", "invoiceFile")->where(["dairyId" => $dairyId])
            ->join("subscription_plan", "subscription_plan.id", "=", "payments.pricePlanId")
            ->get();

        foreach ($payments as &$p) {
            $p->invoiceFile = url($p->invoiceFile);
        }

        if (!$subsc) {
            $response['error_message'] = "An error while processing your request.";
            return $response;
        }

        $response["data"] = (object) ["subscription" => $subsc, "payment_history" => $payments];
        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response["success_message"] = "Success.";
        return $response;
    }
}
