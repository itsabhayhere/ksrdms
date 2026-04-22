<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class APIValidationModel extends Model
{
    //

    public static function loginRequestValidation()
    {

        if (request('username') == (null || "")) {
            return $res = ["error_message" => "username required.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }
        if (request('password') == (null || "")) {
            return $res = ["error_message" => "password required.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }
        return $res = ["error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "request validated",
            "data" => (object) [],
        ];

    }

    public static function sendLoginOtpValidation()
    {
        if (request('username') == (null || "")) {
            return $res = ["error_message" => "Mobile no. required.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        } else {
            return $res = ["error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "request validated",
                "data" => (object) [],
            ];
        }
    }

    public static function otpLoginValidation()
    {
        if (request('username') == (null || "")) {
            return $res = ["error_message" => "Mobile no. required.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        }
        if (request('otp') == (null || "")) {
            return $res = ["error_message" => "Otp required.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            ];
        } else {
            return $res = ["error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "request validated",
                "data" => (object) [],
            ];
        }
    }

    public static function newPassValidation($d)
    {
        // echo \json_encode($d); exit;
        if (request("userType") == null || request("currentPass") == null || request("newPass") == null) {
            return ["error" => true, "msg" => "please provide all information."];
        }

        if (request("userType") == "dairy") {
            $c = DB::table("other_users")->where(["id" => $d->userId])->get()->first();
            if ($c == null || $c->password != request("currentPass")) {
                return ["error" => true, "msg" => "Current password doesn't match."];
            }
        } elseif (request("userType") == "member") {
            $m = DB::table("member_personal_info")->where(["id" => $d->userId])->get()->first();
            if ($m == null || $m->password != request("currentPass")) {
                return ["error" => true, "msg" => "Current password doesn't match."];
            }
        } else {
            return ["error" => true, "msg" => "User Type is invalid."];
        }
        return ["error" => false, "msg" => ""];
    }

    public static function memberEditValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'id' => 'bail|required',
            'memberCode' => 'bail|required',
            'name' => 'bail|required',
            'fatherName' => '',
            'gender' => 'bail|in:male,female,other,Male,Female,Other',
            // 'email' => 'bail|required|email',
            // 'aadharNumber' => 'bail|numeric|digits:12',
            'mobileNumber' => 'bail|required|numeric|digits:10',
            'address' => '',
            'state' => '',
            'city' => '',
            'districtVillage' => '',
            'pin' => '',
            'bankName' => '',
            'accHolderName' => '',
            'accNumber' => '',
            'ifscCode' => '',
            'alertEmail' => 'in:true,false',
            'alertSms' => 'in:true,false'
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }
        return $response;
    }


    public static function dairyProfileUpdate()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'id' => 'bail|required',
            'dairyName' => 'bail|required',
            'society_code' => 'bail|required',
            'dairyMobile' => 'bail|required',
            'dairyAddress' => 'required',
            'stateName' => 'required',
            'cityName' => 'required',
            'propritorName' => 'required',
            'propritorMobile' => 'required',
            'propritorEmail' => 'required',
            'propritorAddress' => 'required',
            'propritorStateName' => 'required',
            'propritorCityName' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }
        return $response;
    }

    public static function memberAddValidation($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'memberCode' => 'bail|required',
            'name' => 'bail|required',
            'fatherName' => '',
            'gender' => 'in:male,female,other,Male,Female,Other',
            // 'email' => '',
            // 'aadharNumber' => 'numeric|digits:12',
            'mobileNumber' => 'bail|required|numeric|digits:10',
            'address' => '',
            'state' => '',
            'city' => '',
            'districtVillage' => '',
            'pin' => 'numeric',
            'bankName' => "",
            'accHolderName' => "",
            'accNumber' => "",
            'ifscCode' => "",
            'openingBalance' => "bail|required|numeric",
            'openingBalanceType' => "bail|required|in:debit,credit,Debit,Credit",
            'alertEmail' => 'in:true,false',
            'alertSms' => 'in:true,false'
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $mem = DB::table("member_personal_info")->where("dairyId", $dairyId)->count();

        $s = DB::table("subscribe")->where(["dairyId" => $dairyId])
            ->join("subscription_plan", "subscription_plan.id", "=", "subscribe.pricePlanId")
            ->get()->first();
        if ($s == null) {
            $response['error_message'] = "Something is wrong, please contact Super Admin.";
            return $response;
        }
        if ($mem < $s->noOfMem) {
            $response["status"] = "OK";
            $response["status_code"] = "200";
            return $response;
        } else {
            $response['error_message'] = "You have reach your subscription plan limit, please upgrade your subscription plan.";
            return $response;
        }
    }

    public static function customerEditValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'id' => 'bail|required',
            'customerCode' => 'bail|required',
            'customerName' => 'bail|required',
            'gender' => 'bail|in:male,female,other,Male,Female,Other',
            'customerEmail' => '',
            'mobileNumber' => 'bail|required|numeric|digits:10',
            'address' => '',
            'state' => '',
            'city' => '',
            'districtVillage' => '',
            'pin' => '',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }
        return $response;
    }

    public static function customerAddValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            "customerCode" => 'bail|required',
            "name" => 'bail|required',
            "gender" => 'bail',
            // "email" => 'bail|email',
            "mobileNumber" => 'bail|required|numeric|digits:10',
            "address" => '',
            "state" => '',
            "city" => '',
            "villageDistrict" => '',
            "pin" => '',
            'openingBalance' => "bail|required|numeric",
            'openingBalanceType' => "bail|required|in:debit,credit,Credit,Debit",
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }
        return $response;
    }

    public static function supplierEditValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'id' => 'bail|required',
            'supplierCode' => 'bail|required',
            'supplierFirmName' => 'bail|required',
            'gender' => 'bail|in:male,female,other,Male,Female,Other',
            'supplierPersonName' => 'bail|required',
            // 'email' => 'bail|email',
            'mobileNumber' => 'bail|required|numeric|digits:10',
            'gstin' => '',
            'address' => '',
            'state' => '',
            'city' => '',
            'villageDistrict' => '',
            'pin' => '',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }
        return $response;
    }

    public static function supplierAddValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'supplierCode' => 'bail|required',
            'supplierFirmName' => 'bail|required',
            'gender' => 'bail|in:male,female,other,Male,Female,Other',
            'supplierPersonName' => 'bail|required',
            // 'email' => 'bail|email',
            'mobileNumber' => 'bail|required|numeric|digits:10',
            'gstin' => '',
            'address' => '',
            'state' => '',
            'city' => '',
            'villageDistrict' => '',
            'pin' => '',
            'openingBalanceType' => 'bail|required|in:credit,debit,Debit,Credit',
            'openingBalance' => 'bail|required|numeric',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }
        return $response;

    }

    public static function productEditValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'id' => 'bail|required',
            'productName' => 'bail|required',
            'sellingPrice' => 'bail|required|numeric',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }
        return $response;
    }

    public static function productAddValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'productCode' => 'bail|required',
            'productName' => 'bail|required',
            'stock' => 'bail|required|numeric',
            'sellingPrice' => 'bail|required|numeric',
            'purchasePrice' => 'bail|required|numeric',
            'supplierId' => 'bail|required',
            "paidAmount" => 'bail|required',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
            return $response;
        }

        $sup = DB::table("suppliers")->where("id", request("supplierId"))->where("status", "true")->get()->first();
        if ($sup == (null || false || "")) {
            $response['error_message'] = "No supplier found.";
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }

        return $response;
    }

    public static function productStockValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'productId' => 'required',
            'productCode' => 'required',
            'quantity' => 'required|numeric',
            'sellingPrice' => 'required|numeric',
            'supplierId' => 'required',
            'purchaseAmount' => 'required|numeric',
            'paidAmount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
            return $response;
        }

        if (request('paidAmount') > request('purchaseAmount')) {
            $response['error_message'] = "Paid Amount can not be grater from purchase amount.";
            return $response;
        }

        $sup = DB::table("suppliers")->where("id", request("supplierId"))->where("status", "true")->get()->first();
        if ($sup == (null || false || "")) {
            $response['error_message'] = "No supplier found.";
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }

        return $response;
    }

    public static function plantSaleValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'plantCode' => 'required',
            'toDate' => 'required',
            'fromDate' => 'required',
            'partyType' => 'required',
            'product' => 'required',
            'quantity' => 'required',
            'amount' => 'required',
            'paidAmount' => 'required',
            'remark' => 'max:300',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
            return $response;
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }

        return $response;
    }

    public static function addCollectionManagerValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'userName' => 'required',
            'registerDate' => 'required|date',
            'email' => 'required|email',
            'gender' => 'required',
            'address' => 'required',
            'mobileNumber' => 'bail|required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
            return $response;
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }

        return $response;
    }

    public static function expenseAddValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'date' => 'bail|required|date',
            'time' => 'bail|required',
            'expenseDesc' => '',
            'expenseHeadId' => 'bail|required',
            'paymentMode' => 'bail|required|in:cash,credit,Cash,Credit',
            'amount' => 'bail|required|numeric',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
        }

        return $response;
    }

    public static function expenseHeadAddValidation($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'expenseCode' => 'required',
            'expenseName' => 'required',
            'expenseDesc' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            // $response["data"] = $validator->messages();
            return $response;
        }

        $expense_head_code = DB::table('expenses')
            ->where(['expenseHeadCode' => request('expenseCode'), "dairyId" => $dairyId])
            ->get()->first();

        if ($expense_head_code != null) {
            $response['error_message'] = 'There is a problem, Expense head is already in records.';
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function milkCollectionValidation($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'memberCode' => 'bail|required',
            'date' => 'bail|required|date',
            'quantity' => 'bail|required',
            'dailyShift' => 'bail|required|in:morning,evening,Morning,Evening',
            'fat' => 'bail|required',
            'snf' => '',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $mem = DB::table("member_personal_info")->where(["memberPersonalCode" => request("memberCode"),
            "dairyId" => $dairyId,
            "status" => "true"])->get()->first();
        if ($mem == (null || false || "")) {
            $response['error_message'] = "No member found.";
            return $response;
        }
        request()->request->add(['memberName' => $mem->memberPersonalName]);

        $colMan = DB::table("other_users")->where(["dairyId" => request('dairyId'), "userName" => request('colMan')])
            ->get()->first();

        if (request('fat') <= 5) {
            $milkType = "cow";
            $rateCardId = $colMan->rateCardIdForCow;
            $rateCardType = $colMan->rateCardTypeForCow;
        } else {
            $rateCardId = $colMan->rateCardIdForBuffalo;
            $rateCardType = $colMan->rateCardTypeForBuffalo;
            $milkType = "buffalo";
        }
        request()->request->add(['milkType' => $milkType]);

        if ($rateCardId == (null || '')) {
            $response['error_message'] = "RATECARD_NOT_FOUND.";
            return $response;
        }

        $ratecardshort = DB::table('ratecardshort')
            ->where('id', $rateCardId)
            ->get()->first();

        if ($ratecardshort == (null || '')) {
            $response['error_message'] = "RATECARD_NOT_FOUND_404";
            return $response;
        }

        if ($ratecardshort->rateCardType == "fat") {
            if ($ratecardshort->minFat > request('fat') || $ratecardshort->maxFat < request('fat')) {
                $response['error_message'] = "Please enter correct values, Min Fat: " . $ratecardshort->minFat . " & Max Fat: " . $ratecardshort->maxFat;
                return $response;
            }
        } else {
            if ($ratecardshort->minFat > request('fat') || $ratecardshort->maxFat < request('fat') || $ratecardshort->minSnf > request('snf') || $ratecardshort->maxSnf < request('snf')) {
                $response['error_message'] = "Please enter correct values, Min Fat: " . $ratecardshort->minFat . " & Max Fat: " . $ratecardshort->maxFat . " and Min SNF: " . $ratecardshort->minSnf . " & Max SNF: " . $ratecardshort->maxSnf;
                return $response;
            }
        }

        $fatSnfRatecard = DB::table('fat_snf_ratecard')
            ->where(['dairyId' => request('dairyId'),
                'rateCardShortId' => $rateCardId,
                'fatRange' => request('fat'),
                'snfRange' => request('snf')])
            ->get()->first();

        if ($fatSnfRatecard == (null || "")) {
            $response['error_message'] = "RATECARD_HAS_NO_VALUE";
            return $response;
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";

            request()->request->add(['price' => $fatSnfRatecard->amount]);
            return $response;
        }
    }

    public static function milkCollectionEditValidation($dairyId, $colMan)
    {
        
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            "dailyTransactionId" => "required",
            'memberCode' => 'bail|required',
            'quantity' => 'bail|required',
            'fat' => 'bail|required',
            'snf' => '',
        ]);


        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $mem = DB::table("member_personal_info")->where(["memberPersonalCode" => request("memberCode"),
            "dairyId" => $dairyId,
            "status" => "true"])->get()->first();
        if ($mem == (null || false || "")) {
            $response['error_message'] = "No member found.";
            return $response;
        }
        
        request()->request->add(['memberName' => $mem->memberPersonalName]);

        $colMan = DB::table("other_users")->where(["dairyId" => request('dairyId'), "userName" => request('colMan')])
            ->get()->first();

        if (request('fat') <= 5) {
            $milkType = "cow";
            $rateCardId = $colMan->rateCardIdForCow;
            $rateCardType = $colMan->rateCardTypeForCow;
        } else {
            $rateCardId = $colMan->rateCardIdForBuffalo;
            $rateCardType = $colMan->rateCardTypeForBuffalo;
            $milkType = "buffalo";
        }
        request()->request->add(['milkType' => $milkType]);

        if ($rateCardId == (null || '')) {
            $response['error_message'] = "RATECARD_NOT_FOUND.";
            return $response;
        }

        $ratecardshort = DB::table('ratecardshort')
            ->where('id', $rateCardId)
            ->get()->first();

        if ($ratecardshort == (null || '')) {
            $response['error_message'] = "RATECARD_NOT_FOUND_404";
            return $response;
        }

        if ($ratecardshort->rateCardType == "fat") {
            if ($ratecardshort->minFat > request('fat') || $ratecardshort->maxFat < request('fat')) {
                $response['error_message'] = "Please enter correct values, Min Fat: " . $ratecardshort->minFat . " & Max Fat: " . $ratecardshort->maxFat;
                return $response;
            }
        } else {
            if ($ratecardshort->minFat > request('fat') || $ratecardshort->maxFat < request('fat') || $ratecardshort->minSnf > request('snf') || $ratecardshort->maxSnf < request('snf')) {
                $response['error_message'] = "Please enter correct values, Min Fat: " . $ratecardshort->minFat . " & Max Fat: " . $ratecardshort->maxFat . " and Min SNF: " . $ratecardshort->minSnf . " & Max SNF: " . $ratecardshort->maxSnf;
                return $response;
            }
        }

        $fatSnfRatecard = DB::table('fat_snf_ratecard')
            ->where(['dairyId' => request('dairyId'),
                'rateCardShortId' => $rateCardId,
                'fatRange' => request('fat'),
                'snfRange' => request('snf')])
            ->get()->first();

        if ($fatSnfRatecard == (null || "")) {
            $response['error_message'] = "RATECARD_HAS_NO_VALUE";
            return $response;
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";

            request()->request->add(['price' => $fatSnfRatecard->amount]);
            return $response;
        }

    }

    public static function fatSnfValidation($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) ["rateCardType" => null]);

        $validator = Validator::make(request()->all(), [
            'memberCode' => 'bail|required',
            'fat' => 'bail|required',
        ]);

        if ($validator->fails()){
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $mem = DB::table("member_personal_info")->where(["memberPersonalCode" => request("memberCode"),
            "dairyId" => $dairyId,
            "status" => "true"])->get()->first();
        if ($mem == (null || false || "")) {
            $response['error_message'] = "No member found.";
            return $response;
        }

        $colMan = DB::table("other_users")->where(["dairyId" => request('dairyId'), "userName" => request('colMan')])
            ->get()->first();

        if (request('fat') <= 5) {
            $milkType = "cow";
            $rateCardId = $colMan->rateCardIdForCow;
            $rateCardType = $colMan->rateCardTypeForCow;
        } else {
            $rateCardId = $colMan->rateCardIdForBuffalo;
            $rateCardType = $colMan->rateCardTypeForBuffalo;
            $milkType = "buffalo";
        }

        if ($rateCardId == (null || '')) {
            $response['error_message'] = "RATECARD_NOT_FOUND.";
            return $response;
        }

        $ratecardshort = DB::table('ratecardshort')
            ->where('id', $rateCardId)
            ->get()->first();

        if ($ratecardshort == (null || '')) {
            $response['error_message'] = "RATECARD_NOT_FOUND_404";
            return $response;
        }

        $response['data'] = ["rateCardType" => $ratecardshort->rateCardType];

        if ($ratecardshort->rateCardType == "fat") {
            if ($ratecardshort->minFat > request('fat') || $ratecardshort->maxFat < request('fat')) {
                $response['error_message'] = "Please enter correct values, Min Fat: " . $ratecardshort->minFat . " & Max Fat: " . $ratecardshort->maxFat;
                return $response;
            }
        } else {
            if ($ratecardshort->minFat > request('fat') || $ratecardshort->maxFat < request('fat') || $ratecardshort->minSnf > request('snf') || $ratecardshort->maxSnf < request('snf')) {
                $response['error_message'] = "Please enter correct values, Min Fat: " . $ratecardshort->minFat . " & Max Fat: " . $ratecardshort->maxFat . " and Min SNF: " . $ratecardshort->minSnf . " & Max SNF: " . $ratecardshort->maxSnf;
                return $response;
            }
        }

        $fatSnfRatecard = DB::table('fat_snf_ratecard')
            ->where(['dairyId' => request('dairyId'),
                'rateCardShortId' => $rateCardId,
                'fatRange' => request('fat'),
                'snfRange' => request('snf')])
            ->get()->first();

        if ($fatSnfRatecard == (null || "")) {
            $response['error_message'] = "RATECARD_HAS_NO_VALUE";
            return $response;
        } else {
            $response["status"] = "OK";
            $response["status_code"] = "200";
            $response["data"] = ['price' => $fatSnfRatecard->amount, 'milkType' => $milkType];
            return $response;
        }
    }

    public static function localSaleListValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'from' => 'bail|required|date',
            'to' => 'bail|required|date',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function localSaleValidation($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            "partyCode" => "bail|required",
            "partyType" => "bail|required|in:member,customer",
            "sale_type" => "bail|required|in:local_sale",
            "milkType" => "bail|required|in:cowMilk,buffaloMilk",
            "quantity" => "bail|required|numeric",
            "discount" => "bail|required|numeric",
            "date" => "bail|required|date",
            "paidAmount" => "bail|required",
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        request()->request->add(['product' => request('milkType')]);

        if (request("partyType") == "member") {
            request()->request->add(['memberCode' => request("partyCode")]);
        } elseif (request("partyType") == "customer") {
            request()->request->add(['customerCode' => request("partyCode")]);
        } else {
            $response['error_message'] = "An error has occured, in validating data.";
            return $response;
        }
        request()->request->add(['unit' => "Ltr"]);

        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        if ($dairyInfo->cowMilkPrice == ("" || null || 0) || $dairyInfo->buffaloMilkPrice == ("" || null || 0)) {
            $response['error_message'] = "please set milk price first.";
            return $response;
        }

        if (request("product") == "cowMilk") {
            $price = $dairyInfo->cowMilkPrice;
        } else {
            $price = $dairyInfo->buffaloMilkPrice;
        }

        $amount = number_format((float) request("quantity") * (float) $price, 2, ".", "");
        if ($amount < request('discount')) {
            $response['error_message'] = "Discount must be less then or equal to the amount.";
            return $response;
        }

        if (($amount - (float) request('discount')) < (float) request("paidAmount")) {
            $response['error_message'] = "Paid amount must be less then or equal to the amount.";
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function productSaleListValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'from' => 'bail|required|date',
            'to' => 'bail|required|date',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function productSaleValidation($dairyId, $colMan)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            "partyCode" => "bail|required",
            "partyType" => "bail|required|in:member,customer",
            "sale_type" => "bail|required|in:product_sale",
            "product" => "bail|required|",
            "quantity" => "bail|required|numeric",
            "discount" => "bail|required|numeric",
            "date" => "bail|required|date",
            "paidAmount" => "bail|required",
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        if (request("partyType") == "member") {
            request()->request->add(['memberCode' => request("partyCode")]);
        } elseif (request("partyType") == "customer") {
            request()->request->add(['customerCode' => request("partyCode")]);
        } else {
            $response['error_message'] = "An error has occured, in validating data.";
            return $response;
        }
        request()->request->add(['unit' => "Unit"]);

        $product = DB::table("products")->where("productCode", request('product'))->get()->first();
        if ($product == (null || false)) {
            $response['error_message'] = "No such product exist.";
            return $response;
        }
        $price = $product->amount;

        $amount = number_format((float) request("quantity") * (float) $price, 2, ".", "");
        if ($amount < request('discount')) {
            $response['error_message'] = "Discount must be less then or equal to the amount.";
            return $response;
        }

        if (($amount - (float) request('discount')) < (float) request("paidAmount")) {
            $response['error_message'] = "Paid amount must be less then or equal to the amount.";
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function advanceListValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'from' => 'bail|required|date',
            'to' => 'bail|required|date',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function creditListValidation()
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'from' => 'bail|required|date',
            'to' => 'bail|required|date',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function advanceAddValidation($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'date' => 'bail|required|date',
            'partyCode' => 'bail|required',
            'partyType' => 'bail|required|in:member,customer,supplier',
            'remarks' => 'bail|required',
            'amount' => 'bail|required|numeric',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $user = null;
        if (request('partyType') == "member") {
            $user = DB::table("member_personal_info")
                ->where(['memberPersonalCode' => request('partyCode'), "dairyId" => $dairyId])
                ->get()->first();
        }
        if (request('partyType') == "customer") {
            $user = DB::table("customer")
                ->where(['customerCode' => request('partyCode'), "dairyId" => $dairyId])
                ->get()->first();
        }
        if (request('partyType') == "supplier") {
            $user = DB::table("suppliers")
                ->where(['supplierCode' => request('partyCode'), "dairyId" => $dairyId])
                ->get()->first();
        }

        if ($user == (null || false)) {
            $response['error_message'] = "No user found";
            return $response;
        }

        if (request('partyType') == "member") {
            request()->request->add(['partyName' => $user->memberPersonalName]);
        }
        if (request('partyType') == "customer") {
            request()->request->add(['partyName' => $user->customerName]);
        }
        if (request('partyType') == "supplier") {
            request()->request->add(['partyName' => $user->supplierFirmName]);
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function creditAddValidation($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'date' => 'bail|required|date',
            'partyCode' => 'bail|required',
            'partyType' => 'bail|required|in:member,customer,supplier',
            'remarks' => 'bail|required',
            'credit' => 'bail|required|numeric',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $user = null;
        if (request('partyType') == "member") {
            $user = DB::table("member_personal_info")
                ->where(['memberPersonalCode' => request('partyCode'), "dairyId" => $dairyId])
                ->get()->first();
        }
        if (request('partyType') == "customer") {
            $user = DB::table("customer")
                ->where(['customerCode' => request('partyCode'), "dairyId" => $dairyId])
                ->get()->first();
        }
        if (request('partyType') == "supplier") {
            $user = DB::table("suppliers")
                ->where(['supplierCode' => request('partyCode'), "dairyId" => $dairyId])
                ->get()->first();
        }

        if ($user == (null || false)) {
            $response['error_message'] = "No user found";
            return $response;
        }

        if (request('partyType') == "member") {
            request()->request->add(['partyName' => $user->memberPersonalName]);
        }
        if (request('partyType') == "customer") {
            request()->request->add(['partyName' => $user->customerName]);
        }
        if (request('partyType') == "supplier") {
            request()->request->add(['partyName' => $user->supplierFirmName]);
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

    public static function memMilkReqSendValidation($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'date' => 'bail|required|date',
            'shift' => 'bail|in:morning,evening,Morning,Evening',
            'type' => 'bail|required|in:milk,product',
            'qty' => 'bail|numeric',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        if (request("type") == "product") {
            $pro = DB::table("products")->where(["productCode" => request('productCode'), "dairyId" => $dairyId])
                ->get()->first();
            if ($pro == (null || "" || false)) {
                $response['error_message'] = "No Product found.";
                return $response;
            }

            if ($pro->productUnit < request('qty')) {
                $response['error_message'] = "Product stock is low.";
                return $response;
            }
            $rate = $pro->amount;
        } else {
            $rate = null;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        $response['rate'] = $rate;
        return $response;
    }

    public static function requestCompleteValidation($dairyId)
    {
        $response = array("error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => (object) []);

        $validator = Validator::make(request()->all(), [
            'reqId' => 'bail|required',
            'action' => 'bail|required|in:complete,decline,Complete,Decline',
        ]);

        if ($validator->fails()) {
            $response['error_message'] = $validator->errors()->first();
            return $response;
        }

        $req = DB::table("milkrequest")->where(["id" => request('reqId'), "dairyId" => $dairyId])->get()->first();

        if ($req == null || false) {
            $response['error_message'] = "Request Not Found.";
            return $response;
        }
        if ($req->isDeliverd == 1) {
            $response['error_message'] = "Request already completed.";
            return $response;
        }
        if ($req->isDeliverd == 2) {
            $response['error_message'] = "Request already declined.";
            return $response;
        }

        $response["status"] = "OK";
        $response["status_code"] = "200";
        return $response;
    }

}
