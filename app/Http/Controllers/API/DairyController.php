<?php

namespace App\Http\Controllers\API;

use App\APIHelperModel;
use App\APIValidationModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DairyController extends Controller
{
    public $dairyId;

    // public function __construct()
    // {

    // }

    public function showProfile(){
        $res = [
            "error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => [],
        ];

        $r = APIHelperModel::isAuthReq();

        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $dairy = DB::table("dairy_info")->where("id", session()->get('colMan')->dairyId)->get()->first();

        $prp = DB::table("dairy_propritor_info")->where("dairyId", session()->get('colMan')->dairyId)->get()->first();

        $res["data"]["id"] = $dairy->id;
        $res["data"]["dairyName"] = $dairy->dairyName;
        $res["data"]["society_code"] = $dairy->society_code;
        $res["data"]["dairyMobile"] = $dairy->mobile;
        $res["data"]["dairyAddress"] = $dairy->dairyAddress;
        $res["data"]["stateName"] = DB::table("states")->where("id", $dairy->state)->pluck("name")->first();
        $res["data"]["cityName"] = DB::table("city")->where("id", $dairy->city)->pluck("name")->first();

        $res["data"]["propritorName"] = $prp->dairyPropritorName;
        $res["data"]["propritorMobile"] = $prp->PropritorMobile;
        $res["data"]["propritorEmail"] = $prp->dairyPropritorEmail;
        $res["data"]["propritorAddress"] = $prp->dairyPropritorAddress;
        $res["data"]["propritorStateName"] = DB::table("states")->where("id", $prp->dairyPropritorState)->pluck("name")->first();
        $res["data"]["propritorCityName"] = DB::table("city")->where("id", $prp->dairyPropritorCity)->pluck("name")->first();
        $res["data"]["propritorPin"] = $prp->dairyPropritorPincode;

        $res["status_code"] = "200";
        $res["status"] = "OK";
        return response()->json($res, 200);
    }

    public function updateProfile(){
        $res = [
            "error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => [],
        ];

        $r = APIHelperModel::isAuthReq();

        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::dairyProfileUpdate();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        // $colMan = Session::get("colMan");

        DB::beginTransaction();

        $u = DB::table("dairy_info")->where(["id" => request('id')])
                ->update([
                    "dairyName"     => request("dairyName"),
                    "society_code"  => request("society_code"),
                    "mobile"        => request("dairyMobile"),
                    "dairyAddress"  => request("dairyAddress"),
                    "state"         => request("stateName"),
                    "city"          => request("cityName"),
                    "updated_at"    => date("Y-m-d H:i:s")
                ]);

        if(!$u){
            DB::rollBack();
            $res["error_message"] = "There is an error occured.";
            $res["status_code"] = "202";
            $res["status"] = "ERROR";
            return response()->json($res, 200);
        }

        $u = DB::table("dairy_propritor_info")->where(["dairyId" => request('id')])
                ->update([
                    "dairyPropritorName"     => request("propritorName"),
                    "dairyPropritorAddress"  => request("propritorAddress"),
                    "dairyPropritorState"    => request("propritorStateName"),
                    "dairyPropritorCity"     => request("propritorCityName"),
                    "dairyPropritorDistrict" => request("propritorCityName"),
                    "dairyPropritorPincode"  => request("propritorPin"),
                    "updated_at"             => date("Y-m-d H:i:s")
                ]);

        if(!$u){
            DB::rollBack();
            $res["error_message"] = "There is an error occured.";
            $res["status_code"] = "202";
            $res["status"] = "ERROR";
            return response()->json($res, 200);
        }

        DB::commit();

        $res["success_message"] = "Details updated successfully.";
        $res["status_code"] = "200";
        $res["status"] = "OK";
        return response()->json($res, 200);
    }

    public function getStatesCities()
    {
        $res = [
            "error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => [],
        ];

        $city = DB::table("city")->get();
        $state = DB::table("states")->get();

        if ($city != (null || false || "") || $state != (null || false || "")) {
            $res['data'] = ["cities" => $city, "states" => $state];
            $res['success_message'] = "data collected.";
            $res['status'] = "OK";
            $res['status_code'] = "200";
            return response()->json($res, 200);
        } else {
            $res['error_message'] = "something went wrong.";
            return response()->json($res, 200);
        }
    }

    public function getStates()
    {
        $res = [
            "error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => [],
        ];

        $state = DB::table("states")->get();

        if ($state != (null || false || "")) {
            $res['data'] = $state;
            $res['success_message'] = "data collected.";
            $res['status'] = "OK";
            $res['status_code'] = "200";
            return response()->json($res, 200);
        } else {
            $res['error_message'] = "something went wrong.";
            return response()->json($res, 200);
        }
    }

    public function getCities()
    {
        $res = [
            "error_message" => "",
            "status" => "ERROR",
            "status_code" => "202",
            "success_message" => "",
            "data" => [],
        ];

        if(request('stateid')){
            $city = DB::table("city")->where("state_id", request('stateid'))->get();
        }else{
            $city = DB::table("city")->get();
        }

        if ($city != (null || false || "")) {
            $res['data'] = $city;
            $res['success_message'] = "data collected.";
            $res['status'] = "OK";
            $res['status_code'] = "200";
            return response()->json($res, 200);
        } else {
            $res['error_message'] = "something went wrong.";
            return response()->json($res, 200);
        }
    }

    public function dairy_summary()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getDairySummary($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }

    public function memberCreditAndMilkCollection()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getmemberCreditMilkCollection($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }

    public function memberList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getMembers($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }

    public function memberDetail()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getMember($this->dairyId);

        if($data["status_code"] == "200")
            return response()->json($data, 200);
        else
            return response()->json($data, 200);
    }

    public function memberEdit()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::memberEditValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::memberUpdate();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function memberNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::memberAddValidation($this->dairyId);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::memberNew($this->dairyId, $r['data']->colMan);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    
    public function memberDelete()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::memberDelete($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }


    public function customerList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getCustomer($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }

    
    public function customerDetail()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getCustomerSingle($this->dairyId);

        if($data["status_code"] == "200")
            return response()->json($data, 200);
        else
            return response()->json($data, 200);
    }


    public function customerEdit()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::customerEditValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::customerUpdate();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function customerNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::customerAddValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::customerNew($this->dairyId, $r['data']->colMan);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function supplierList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getSuppliers($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }

    public function supplierDetail()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getSupplier($this->dairyId);

        if($data["status_code"] == "200")
            return response()->json($data, 200);
        else
            return response()->json($data, 200);
    }

    public function supplierEdit()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::supplierEditValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::supplierUpdate();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function supplierNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::supplierAddValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::supplierNew($this->dairyId, $r['data']->colMan);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function milkPlantListForDairy()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::milkPlantListForDairy($this->dairyId, $r['data']->colMan);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);        
    }

    public function mainMilkPlantList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::mainMilkPlants();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function milkPlantList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::milkPlantsList();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function addMilkPlantToDairy()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::milkPlantAddToDairy($this->dairyId, $r['data']->colMan);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    
    public function productList()
    {
        $r = APIHelperModel::isAuthOnly();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getProducts($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }

    
    public function productDetail()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getProduct($this->dairyId);

        if($data["status_code"] == "200")
            return response()->json($data, 200);
        else
            return response()->json($data, 200);
    }


    public function productEdit()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::productEditValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::productUpdate();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function productStockAdd()
    {
        
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::productStockValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::productStockUpdate($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function productNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::productAddValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::productNew($this->dairyId, $r['data']->colMan);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function expenseList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getExpense($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }

    public function expenseNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::expenseAddValidation();

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::expenseNew($this->dairyId, $r['data']->colMan);

        return response()->json($res, 200);
    }


    public function expenseHeads()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $data = APIHelperModel::getExpenseHeads($this->dairyId);

        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$data,
        ];

        return response()->json($res, 200);
    }
    
    public function expenseHeadNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::expenseHeadAddValidation($this->dairyId);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::expenseHeadNew($this->dairyId);

        return response()->json($res, 200);
    }
    
    

    public function milkCollectionList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        // $validate = APIValidationModel::milkCollectionValidation($this->dairyId, $r['data']->colMan);

        // if ($validate['status_code'] != "200") {
        //     return response()->json($validate, 200);
        // }

        $res = APIHelperModel::milkCollectionList($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    
    public function milkCollection()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $validate = APIValidationModel::milkCollectionValidation($this->dairyId, $r['data']->colMan);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::milkCollectionAdd($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function milkCollection_delete()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        if(request('memberCode') == null || request('dailyTransactionId') == null){
            $response = array("error_message" => "dailyTransactionId, memberCode required.",
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" =>  "",
                "data"      => []
            );
            return response()->json($response, 200);
        }

        request()->request->add(["transId" => request('dailyTransactionId')]);

        // $validate = APIValidationModel::milkCollectionValidation($this->dairyId, $r['data']->colMan);
        $submitClass = new \App\dailyTransaction();
        $submitReturn = $submitClass->DailyTransactionDelete(request());

        if ($submitReturn) {
            $response = array("error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" =>  session()->get('msg'),
                "data"      => []
            );
            return response()->json($response, 200);

        } else {
            $response = array("error_message" => session()->get('msg'),
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" =>  "",
                "data"      => []
            );
            return response()->json($response, 200);
        }

    }


    public function milkCollectionFatSnfValue()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $validate = APIValidationModel::fatSnfValidation($this->dairyId, $r['data']->colMan);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        return response()->json($validate, 200);
    }

    public function localSale()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $validate = APIValidationModel::localSaleValidation($this->dairyId, $r['data']->colMan);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::localSale();

        return response()->json($res, 200);
    }
    
    public function localSaleList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        // $validate = APIValidationModel::localSaleListValidation();

        // if ($validate['status_code'] != "200") {
        //     return response()->json($validate, 200);
        // }

        $res = APIHelperModel::getLocalSale($this->dairyId);

        return response()->json($res, 200);
    }

    public function productSale()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $validate = APIValidationModel::productSaleValidation($this->dairyId, $r['data']->colMan);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::productSale($this->dairyId);

        return response()->json($res, 200);
    }

    public function productSaleList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        // $validate = APIValidationModel::productSaleListValidation();

        // if ($validate['status_code'] != "200") {
        //     return response()->json($validate, 200);
        // }

        $res = APIHelperModel::getProductSale($this->dairyId);

        return response()->json($res, 200);
    }

    public function plantSaleList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }
        $res = APIHelperModel::getPlantSale($this->dairyId);

        return response()->json($res, 200);
    }

    public function plantSale()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
            request()->request->add(['unit' => 'Ltr']);
            request()->request->add(['discount' => '0']);
        }

        $validate = APIValidationModel::plantSaleValidation($this->dairyId, $r['data']->colMan);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::plantSale($this->dairyId);
        
        return response()->json($res, 200);
    }

    public function advanceList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        // $validate = APIValidationModel::advanceListValidation();

        // if ($validate['status_code'] != "200") {
        //     return response()->json($validate, 200);
        // }

        $res = APIHelperModel::getAdvance($this->dairyId);

        return response()->json($res, 200);
    }

    public function creditList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        // $validate = APIValidationModel::creditListValidation();

        // if ($validate['status_code'] != "200") {
        //     return response()->json($validate, 200);
        // }

        $res = APIHelperModel::getCredit($this->dairyId);

        return response()->json($res, 200);
    }

    public function advanceAdd()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $validate = APIValidationModel::advanceAddValidation($r['data']->dairyId);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::addAdvance($this->dairyId, $r['data']->colMan);

        return response()->json($res, 200);
    }


    public function creditAdd()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $validate = APIValidationModel::creditAddValidation($r['data']->dairyId);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::addCredit($this->dairyId, $r['data']->colMan);

        return response()->json($res, 200);
    }

    public function milkRequestList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::milkRequestList($this->dairyId, $r['data']->colMan);

        return response()->json($res, 200);
    }
    
    public function productDlvryReqList()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::productDlvryReqList($this->dairyId, $r['data']->colMan);

        return response()->json($res, 200);
    }

    public function requestComplete(){
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }
        
        $validate = APIValidationModel::requestCompleteValidation($this->dairyId);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::requestComplete($this->dairyId, $r['data']->colMan);

        return response()->json($res, 200);
    }



    public function rateCardDetails_old()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::getRatecardDetails($this->dairyId, $r['data']->colManId);
        
        if($res["error"]){
            $response = array("error_message" => $res["msg"],
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
            );
            return $response;
        }else{
            $response = array("error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "Ok",
                "data"      => $res['data']
            );
        }

        return response()->json($response, 200);
    }

    public function rateCardDetails()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $dairyId = $r['data']->dairyId;
            $colManId = $r['data']->colManId;

        }

        if($dairyId == null || $colManId == null){
            return ["error" => true, "msg" => "No dairy or user found, please login again."];
        }

        if(request("rateCardId") == null){
            return ["error" => true, "msg" => "Rate Card id required."];
        }

        $rtcd = DB::table('ratecardshort')->where(["id" => request("rateCardId")])->get()->first();

        if($rtcd == null){
            return ["error" => true, "msg" => "Rate Card not found."];
        }

        $rateCard = DB::table('fat_snf_ratecard')
                ->where('rateCardShortId', request("rateCardId"))
                ->orderBy('fatRange')
                ->get();

        $rangeList = DB::table("rangelist")
                    ->where("rateCardId", request("rateCardId"))
                    ->get();
                                                                                                                                                                                                                                                                                                                                                                                                        
        $shortCard = DB::table('ratecardshort')
                ->where('id', request("rateCardId"))
                ->get()->first();


        $cardFor = "";
        if($shortCard->rateCardFor=="buffalo")
            $cardFor = "buff";
        if($shortCard->rateCardFor=="cow")
            $cardFor = "cow";
        if($shortCard->rateCardFor=="both")
            $cardFor = "both";

        return response()->view('modelRateCardList',
                    ['rateCard' => $rateCard, "shortCard"=>$shortCard, "rangeList"=>$rangeList, "cardFor"=>$cardFor],
                    200);
        
        // return response()->json($response, 200);
    }
    


    public function memDashboardSummary()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['memberCode' => $r['data']->memberCode]);
        }

        $res = APIHelperModel::memDashboardSummary($r['data']);

        $response = array("error_message" => "",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "Ok",
                "data"      => $res
            );

        return response()->json($response, 200);
    }

    public function memDailyTransaction()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['memberCode' => $r['data']->memberCode]);
        }

        $res = APIHelperModel::memDailyTransactionList($r['data']);

        return response()->json($res, 200);
    }

    public function memPurchaseHistory()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['memberCode' => $r['data']->memberCode]);
        }

        $res = APIHelperModel::memPurchaseHistory($r['data']);

        return response()->json($res, 200);
    }

    
    public function memPayments()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['memberCode' => $r['data']->memberCode]);
        }

        $res = APIHelperModel::memPayments($r['data']);

        return response()->json($res, 200);
    }
    
    public function memMilkReqList()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['memberCode' => $r['data']->memberCode]);
        }

        $res = APIHelperModel::memMilkReqList($r['data']);

        return response()->json($res, 200);
    }
    
    public function memProdReqList()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['memberCode' => $r['data']->memberCode]);
        }

        $res = APIHelperModel::memProdReqList($r['data']);

        return response()->json($res, 200);
    }

    public function memMilkProdReqSend()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['memberCode' => $r['data']->memberCode]);
        }

        $validate = APIValidationModel::memMilkReqSendValidation($this->dairyId);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::memMilkReqSend($r['data'], $this->dairyId, $validate['rate']);

        return response()->json($res, 200);
    }

    public function getMilkPrice()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::getMilkPrice($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }


    public function productPurchaseHistory()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::productPurchaseHistory($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function collectionManagers()
    {        
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::collectionManagers($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function addCollectionManager()
    {        
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $validate = APIValidationModel::addCollectionManagerValidation($this->dairyId);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::addCollectionManager($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }

    public function dairyBalance()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::dairyBalance($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }
    
    public function dairySubscription()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $res = APIHelperModel::dairySubscription($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }
    
    public function getUserDetail()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
        }

        $dairyId = $this->dairyId;

        switch (request('user')) {
            case "member":{
                    if (request('qtype') == "name") {
                        $member = DB::table('member_personal_info')
                            ->where('memberPersonalName', request('q'))->where('dairyId', $dairyId)
                            ->get()->first();
                        if ($member == (null || false)) {
                            $res = ["error" => true, "msg" => "No member found like ".request('q')]; GOTO HERE;
                        }
                    } else {
                        $member = DB::table('member_personal_info')
                            ->where(['memberPersonalCode' => request('q'), 'dairyId' => $dairyId])
                            ->get()->first();
                        if ($member == (null || false)) {
                            $res = ["error" => true, "msg" => "No member found with code ".request('q')]; GOTO HERE;
                        }
                    }
                    unset($member->password);
                    $bl = DB::table("user_current_balance")->where("ledgerId", $member->ledgerId)->get()->first();
                    if ($bl == (null || false)) {
                        $res = ["error" => true, "msg" => "Member Balance Acc not found."]; GOTO HERE;
                    }

                    $res = ["error" => false, "data" => ["name" => $member->memberPersonalName, "code" => $member->memberPersonalCode,
                        "bal" => number_format($bl->openingBalance, 2, ".", ""), "balType" => strtolower($bl->openingBalanceType), "isCash" => false,
                        "ledgerId" => $member->ledgerId]];



                    if((request('milk') == "true") AND request('date') AND request('shift')){
                                        
                            $trans = DB::table('daily_transactions')->where("memberCode", $member->memberPersonalCode)
                                ->where("date", date("Y-m-d", strtotime(request('date'))))
                                ->where("status", "true")->where("dairyId", $dairyId)
                                ->where("shift", request('shift'))
                                ->get();
                    
                            if (count($trans) > 0) {
                                $res["data"]["transactions"] = $trans;
                            }else{
                                $res["data"]["transactions"] = [];
                            }
                    
                    }


                    GOTO HERE;
                    break;
                }

            case "customer":{
                    if (request('qtype') == "name") {
                        $customer = DB::table('customer')
                            ->where('customerName', request('q'))->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($customer == (null || false)) {
                            $res = ["error" => true, "msg" => "No customer found like ".request('q')]; GOTO HERE;
                        }
                    } else {
                        $customer = DB::table('customer')
                            ->where('customerCode', request('q'))->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($customer == (null || false)) {
                            $res = ["error" => true, "msg" => "No customer found with code ".request('q')]; GOTO HERE;
                        }
                    }
                    if ($customer->customerCode == $dairyId . "C1") {
                        $cashuser = true;
                    } else {
                        $cashuser = false;
                    }

                    $bl = DB::table("user_current_balance")->where("ledgerId", $customer->ledgerId)->get()->first();
                    if ($bl == (null || false)) {
                        $res = ["error" => true, "msg" => "Customer Balance Acc not found."]; GOTO HERE;
                    }

                    unset($customer->password);
                    $res = ["error" => false, "data" => ["name" => $customer->customerName, "code" => $customer->customerCode,
                        "bal" => number_format($bl->openingBalance, 2, ".", ""), "balType" => strtolower($bl->openingBalanceType), "isCash" => $cashuser]]; 
                    GOTO HERE;
                    break;
                }

            case "supplier":{
                    if (request('qtype') == "name") {
                        $supplier = DB::table('suppliers')
                            ->where('supplierFirmName', request('q'))->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($supplier == (null || false)) {
                            $res = ["error" => true, "msg" => "No Supplier found like ".request('q')]; GOTO HERE;
                        }
                    } else {
                        $supplier = DB::table('suppliers')
                            ->where('supplierCode', request('q'))->where('dairyId', $dairyId)
                            ->where('status', "true")->get()->first();
                        if ($supplier == (null || false)) {
                            $res = ["error" => true, "msg" => "No Supplier found with code ".request('q')]; GOTO HERE;
                        }
                    }

                    $bl = DB::table("user_current_balance")->where("ledgerId", $supplier->ledgerId)->get()->first();
                    if ($bl == (null || false)) {
                        $res = ["error" => true, "msg" => "Supplier Balance Acc not found."]; GOTO HERE;
                    }

                    unset($supplier->password);
                    $res = ["error" => false, "data" => ["name" => $supplier->supplierFirmName, "code" => $supplier->supplierCode,
                        "bal" => number_format($bl->openingBalance, 2, ".", ""), "balType" => strtolower($bl->openingBalanceType), "isCash" => false]];
                    GOTO HERE;
                    break;
                }

            default:
                $res = ["error" => true, "msg" => "Something bed."];
                GOTO HERE;
        }
        
        HERE:
        
        $colMan = session()->get('colMan');

        if ($colMan->rateCardIdForBuffalo == null) {
            $milkType = "cow";
            $rateCardId = $colMan->rateCardIdForCow;
        } else {
            $rateCardId = $colMan->rateCardIdForBuffalo;
            $milkType = "buffalo";
        }

        if ($rateCardId == (null || '')) {
            $rtcard = false;
        }else{
            $rtcard = $rateCardId;
        }

        $ratecardshort = DB::table('ratecardshort')
            ->where('id', $rateCardId)
            ->get()->first();

        if ($ratecardshort == (null || '')) {
            $rtcard = false;
        }else{
            $rtcard = $ratecardshort->rateCardType;
        }


        if(isset($res)){
            if($res['error']){
                $response = array("error_message" => $res['msg'],
                        "status" => "ERROR",
                        "status_code" => "202",
                        "success_message" => "",
                        "data"      => ['rateCardType' => $rtcard]
                    );
            }else{
                $res['data']['rateCardType'] = $rtcard;

                $response = array("error_message" => "",
                        "status" => "OK",
                        "status_code" => "200",
                        "success_message" => "",
                        "data"      => $res['data']
                    );
            }
            
            return response()->json($response, 200);
        }else{
            
            $response = array(
                "status" => "ERROR",
                "status_code" => "202",
                "success_message" => "",
                "error_message" => "Something bed.",
                "data"      => ['rateCardType' => $rtcard]
            );
            return response()->json($response, 200);
        }
    }

    public function milkCollection_edit()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $validate = APIValidationModel::milkCollectionEditValidation($this->dairyId, $r['data']->colMan);

        if ($validate['status_code'] != "200") {
            return response()->json($validate, 200);
        }

        $res = APIHelperModel::milkCollectionEdit($this->dairyId);

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        return response()->json($res, 200);
    }


    public function creditMembers()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::creditMembers($this->dairyId);
        $r['data'] = $res;
        $r['status_code'] = "200";
        $r['status'] = "OK";
        $r['error_message'] = "";
        $r['success_message'] = "";

        return response()->json($r, 200);

    }

    public function debitMembers()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::debitMembers($this->dairyId);
        $r['data'] = $res;
        $r['status_code'] = "200";
        $r['status'] = "OK";
        $r['error_message'] = "";
        $r['success_message'] = "";

        return response()->json($r, 200);

    }

    public function creditSuppliers()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::creditSuppliers($this->dairyId);
        $r['data'] = $res;
        $r['status_code'] = "200";
        $r['status'] = "OK";
        $r['error_message'] = "";
        $r['success_message'] = "";

        return response()->json($r, 200);
    }

    public function debitSuppliers()
    {
        
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::debitSuppliers($this->dairyId);
        $r['data'] = $res;
        $r['status_code'] = "200";
        $r['status'] = "OK";
        $r['error_message'] = "";
        $r['success_message'] = "";

        return response()->json($r, 200);
    }

    public function creditCustomers()
    {
        
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::creditCustomers($this->dairyId);
        $r['data'] = $res;
        $r['status_code'] = "200";
        $r['status'] = "OK";
        $r['error_message'] = "";
        $r['success_message'] = "";

        return response()->json($r, 200);
    }

    public function debitCustomers()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {
            $this->dairyId = $r['data']->dairyId;
            request()->request->add(['dairyId' => $this->dairyId]);
            request()->request->add(['colMan' => $r['data']->colMan]);
        }

        $res = APIHelperModel::debitCustomers($this->dairyId);
        $r['data'] = $res;
        $r['status_code'] = "200";
        $r['status'] = "OK";
        $r['error_message'] = "";
        $r['success_message'] = "";

        return response()->json($r, 200);
    }

    //new
    
    public function active_inactiveMember(){


        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 200);
        } else {

            if(request('type')==""||request('type')==null){
                $res = [
                    "error_message" => "Type is required",
                    "status" => "OK",
                    "status_code" => "200",
                    "success_message" => "",
                    "data" => '',
                ];
                return response()->json($res, 200);
            }

            $this->dairyId = $r['data']->dairyId;
        }
        $date= request('type')==1?date("Y-m-d", strtotime("-5 days")):date("Y-m-d", strtotime("+5 days"));
        if(request('type')=='1'){
      


        $members= DB::table('daily_transactions')

        ->select("member_personal_info.id", "member_personal_info.ledgerId", "memberPersonalCode as memberCode", "memberPersonalregisterDate as registerDate", "memberPersonalName as name", "memberPersonalFatherName as fatherName",
        "memberPersonalGender as gender", "memberPersonalEmail as email", "memberPersonalAadarNumber as aadharNumber", "memberPersonalMobileNumber as mobileNumber", "memberPersonalAddress as address",
        "states.name as state", "city.name as city", "memberPersonalDistrictVillage as districtVillage", "memberPersonalMobilePincode as pin",
        "user_current_balance.openingBalance", "user_current_balance.openingBalanceType",
        "member_personal_info.created_at","daily_transactions.memberCode","daily_transactions.dairyId","daily_transactions.status")

        ->join("member_personal_info", "member_personal_info.memberPersonalCode", "=", "daily_transactions.memberCode")
        ->where(["member_personal_info.dairyId" => $this->dairyId, "daily_transactions.dairyId" => $this->dairyId, "daily_transactions.status" => "true",
        "member_personal_info.status"=>"true"])
        ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
        ->leftJoin("city", "city.id", "=", "member_personal_info.memberPersonalCity")
     
        ->leftJoin("states", "states.id", "=", "member_personal_info.memberPersonalState")
        ->whereDate("daily_transactions.date", ">", date("Y-m-d", strtotime("-5 days")))
        ->groupBy('daily_transactions.memberCode')
        ->get();
       
       
        }
        else{

            $m = DB::table('daily_transactions')
            ->select("daily_transactions.memberCode")
            ->groupBy('daily_transactions.memberCode')
            ->where(["daily_transactions.dairyId" => $this->dairyId, "daily_transactions.status" => "true"])
            ->whereDate("daily_transactions.date", ">", date("Y-m-d", strtotime("-5 days")))->pluck('memberCode')->toArray();
        $members = DB::table('member_personal_info')
        ->select("member_personal_info.id", "member_personal_info.ledgerId", "memberPersonalCode as memberCode", "memberPersonalregisterDate as registerDate", "memberPersonalName as name", "memberPersonalFatherName as fatherName",
        "memberPersonalGender as gender", "memberPersonalEmail as email", "memberPersonalAadarNumber as aadharNumber", "memberPersonalMobileNumber as mobileNumber", "memberPersonalAddress as address",
        "states.name as state", "city.name as city", "memberPersonalDistrictVillage as districtVillage", "memberPersonalMobilePincode as pin",
        "user_current_balance.openingBalance", "user_current_balance.openingBalanceType",
        "member_personal_info.created_at")
            ->join("user_current_balance", "user_current_balance.ledgerId", "=", "member_personal_info.ledgerId")
            ->where(["member_personal_info.dairyId" => $this->dairyId,
                "member_personal_info.status" => "true"])
            ->whereNotIn("member_personal_info.memberPersonalCode", $m)

            ->leftJoin("city", "city.id", "=", "member_personal_info.memberPersonalCity")
            ->leftJoin("states", "states.id", "=", "member_personal_info.memberPersonalState")
  
            ->groupby("member_personal_info.memberPersonalCode")->get();
        }
        // ->distinct('memberCode')->count('memberCode');
    if (count($members)<1){
        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "No member found",
            "data" => '',
        ];
        return response()->json($res, 200);
    }
    // $members= count($members);
        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "data collected",
            "data" => (object)$members,
        ];

        return response()->json($res, 200);
     }

     public function deleteMember(){
        if(request('id')==null||request('id')==""){
            $res = [
                "error_message" => "Id is required",
                "status" => "OK",
                "status_code" => "200",
                "success_message" => "",
                "data" => '',
            ];
            return response()->json($res, 200);

        }
        $user= DB::table('member_personal_info')
        ->where('id', request('id'))
        ->update([
            'status' => "false",
       ]);

       if($user){

       
        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "Member Successfully Delated",
            "data" => '',
        ];
    }
    else{
        $res = [
            "error_message" => "",
            "status" => "OK",
            "status_code" => "200",
            "success_message" => "Some error occured",
            "data" => '',
        ];
    }
        return response()->json($res, 200);
     }
    
    //end
}
