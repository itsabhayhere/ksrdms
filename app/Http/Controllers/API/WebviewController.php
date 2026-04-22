<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\APIHelperModel;
use App\APIValidationModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use View;

class WebviewController extends Controller
{
    public $dairyId;

    public function __construct()
    {

    }

    public function login()
    {
        $res = APIValidationModel::loginRequestValidation();

        if ($res['status_code'] != "200") {
            return response()->json($res, 200);
        }

        $res = APIHelperModel::loginCheck();

        if ($res['status_code'] != "200") {
            if($res['userType'] == "DAIRYADMIN"){
                session()->put('loginUserInfo', $res['userInfo']);
                session()->put('loginUserType', "dairy");
                session()->put('colMan', $res['userInfo']);
                session()->put('dairyInfo', $res['dairyInfo']);
            }elseif($res['userType'] == "MEMBER"){
                session()->put('loginUserInfo', $res['memberInfo']);
                session()->put('loginUserType', "member");
                session()->put('dairyInfo', $res['dairyInfo']);
            }
            return response()->json($res, 200);
        } else {
            return response()->json($res, 200);
        }

    }

    public function isAuthReq()
    {
        if(Session::has('loginUserType') && Session::get('loginUserType') == "dairy"){
            $colMan = DB::table('other_users')->where(["id" => session()->get('colMan')->id])->get()->first();
            if($colMan==null){
                return ["error" => true, "msg" => "Auth failed."];
            }

            $dairyInfo = DB::table('dairy_info')->where(["id" => session()->get('dairyInfo')->id])->get()->first();

            if($dairyInfo==null){
                return ["error" => true, "msg" => "Auth failed."];
            }

            return ["error" => false, "dairyInfo" => $dairyInfo, "colMan" => $colMan];
        }elseif(Session::has('loginUserType') && Session::get('loginUserType') == "member"){
            $member = DB::table('member_personal_info')->where(["id" => session()->get('loginUserInfo')->id])->get()->first();
            if($member==null){
                return ["error" => true, "msg" => "Auth failed."];
            }

            $dairyInfo = DB::table('dairy_info')->where(["id" => session()->get('dairyInfo')->id])->get()->first();

            if($dairyInfo==null){
                return ["error" => true, "msg" => "Auth failed."];
            }

            return ["error" => false, "dairyInfo" => $dairyInfo, "member" => $member];
        }else{
            return ["error" => true, "msg" => "Auth failed."];
        }
    }



    public function rateCardShow()
    {
        // return Session::all();

        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }
        
        $dairyId = $r['data']->dairyId;
        $colMan = $r['data']->colMan;
        $colManId = $r['data']->colManId;

        $rateCards = DB::table('ratecardshort')
                ->select("ratecardshort.id as id", "ratecardshort.description as description", "ratecardshort.rateCardType as rateCardType", "ratecardshort.minFat as minFat", 
                        "ratecardshort.maxFat as maxFat", "ratecardshort.minSnf as minSnf", "ratecardshort.maxSnf as maxSnf", "rateCardFor",
                        "other_users.id as colManId","rateCardIdForCow", "rateCardIdForBuffalo")
                ->join("other_users", "other_users.id", "=", "ratecardshort.collectionManager")
                ->where('ratecardshort.dairyId', $dairyId)
                ->where('ratecardshort.collectionManager', $colManId)
                ->get();

        $defaultRateCard = DB::table("other_users")->where("dairyId", $dairyId)->where("userName", $colMan)->get()->first();

        // dd($defaultRateCard); exit;
        
        if($defaultRateCard->rateCardIdForBuffalo == (null||'') || $defaultRateCard->rateCardIdForCow == (null||'')){
            Session::flash('msg', 'Rate Card not applied for cow and buffalo.');
            Session::flash('alert-class', 'alert-info');
        }

        $dairyInfo = DB::table('dairy_info')->where(["id" => $dairyId])->get()->first();

        $device_token = request("device_token");

        return response()->view('webviews.rateCardShow',
                ['rateCardShort' => $rateCards, 'dairyInfo'=>$dairyInfo, "defaultRateCard"=>$defaultRateCard,
                "device_token" => $device_token, 'activepage'=>"rateCard"],
                200);
    }

    public function rateCardDetails(Request $request)
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $dairyId = $r['data']->dairyId;
        $colMan = $r['data']->colMan;

        $res['error'] = true;
        if(!$request->shortid){
            $res['msg'] = "There are some error occured.";
            $res['etype'] = "danger";
            return response()->json($res);
        }

        $rateCard = DB::table('fat_snf_ratecard')
                ->where('rateCardShortId', $request->shortid)
                ->orderBy('fatRange')
                ->get();

        $rangeList = DB::table("rangelist")
                    ->where("rateCardId", $request->shortid)
                    ->get();
                                                                                                                                                                                                                                                                                                                                                                                                         
        $shortCard = DB::table('ratecardshort')
                ->where('id', $request->shortid)
                ->get()->first();

        $defaultRateCard = DB::table("other_users")->where("dairyId", $dairyId)->where("userName", $colMan)->get()->first();

        $cardFor = "";
        if($shortCard->id==$defaultRateCard->rateCardIdForBuffalo)
            $cardFor = "buff";
        if($shortCard->id==$defaultRateCard->rateCardIdForCow)
            $cardFor = "cow";
        if($shortCard->id==$defaultRateCard->rateCardIdForCow && $shortCard->id==$defaultRateCard->rateCardIdForBuffalo )
            $cardFor = "both";
    
        return response()->view('webviews.modelRateCardList', ['rateCard' => $rateCard, "shortCard"=>$shortCard, "rangeList"=>$rangeList, 
                                            "cardFor"=>$cardFor, "device_token" => request('device_token')], 200);
    
    }

    public function rateCardNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }
        $device_token = request("device_token");
        
        $dairyId = session()->get('loginUserInfo')->dairyId;
        
        $colman = session()->get('colMan');

        if($colman->userName == "DAIRYADMIN"){
            $cm = DB::table("other_users")->where("dairyId", $dairyId)->select("id", "userName")->get();
            $colManager[] = (object)["id" => "all", "userName" => "All"];
            foreach($cm as $c){ $colManager[] = $c; }
        }else{
            $cm = DB::table("other_users")->where("dairyId", $dairyId)->where('userName', $colman->userName)->get();
            foreach($cm as $c){ $colManager[] = $c; };
        }
        // $colManager = DB::select("SELECT roles_setups.id, roles_setups.role, other_users.userName, other_users.id, other_users.roleId FROM roles_setups, other_users WHERE roles_setups.role = 'collaction manager' AND roles_setups.id = other_users.roleId");

        $totalRateCards = DB::table("ratecardshort")->where('dairyId', $dairyId)
                                ->where('ratecardshort.collectionManager', $colman->id)
                                ->count();
        if($totalRateCards==4){
            Session::flash('msg', 'You can only add 4 RateCards, to add a new rate card please delete 1 Ratecard from your list.'); 
            Session::flash('alert-class', 'alert-danger');
            return $this->rateCardShow();
        }

        $dairyInfo = DB::table('dairy_info')
                ->where('id', $dairyId)
                ->get()
                ->first();

        
        return response()->view('webviews.rateCardNew', ['dairyInfo' => $dairyInfo,'colManager' => $colManager, 'activepage'=>"rateCard",
                                                     "device_token" => $device_token], 200);   
    }

    public function saveRateCardNew()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $res['st'] = microtime(true);

        $request = (Object)json_decode(request()->getContent(), true);
        // return [$request->collectionManager];

        $res['error'] = true;
        $res['msg'] = "";
        $count = 0;

        $dairyId = session()->get('loginUserInfo')->dairyId;
        
        if($dairyId==(null||'')){
            $res['msg'] = "<a href='dairy-login'>You must login first!</a>";
            $res['etype'] = "danger";
            return response()->json($res);
        }

        if(!$request->data && $request->data==(null||'')){
            $res['msg'] = "Something Wrong, please try again.";
            $res['etype'] = "danger";
            return response()->json($res);
        }

        DB::beginTransaction();

        if($request->collectionManager == "all"){
            $colmans = DB::table("other_users")->where("dairyId", $dairyId)->get();
        }else{
            $colmans = DB::table("other_users")->where("dairyId", $dairyId)
                                ->where("id", $request->collectionManager)->get();
        }
        if($colmans == (null||false) || count($colmans) == 0) GOTO ERROR;

        foreach($colmans as $col){
            $shortId = DB::table('ratecardshort')->insertGetId([
                "dairyId" => $dairyId,
                "rateCardType"=> $request->rateType,
                "collectionManager" => $col->id,
                "minFat" => $request->range['minFat'],
                "maxFat" => $request->range['maxFat'],
                "minSnf" => $request->range['minSnf'],
                "maxSnf" => $request->range['maxSnf'],
                "created_at" => date("Y-m-d H:i:s", time()),
            ]);
    
            foreach($request->rangeList as $key => $rlist){
                foreach($rlist['snfRanges'] as $sKey => $slist){
                    $rangeId[$col->id." ".$key." ". $sKey] = DB::table('rangelist')->insertGetId([
                        "dairyId" => $dairyId,
                        "rateCardId"=> $shortId,
                        "mnFat" => $rlist['mnFat'],
                        "mxFat" => $rlist['mxFat'],
                        "mnSnf" => $slist['mnSnf'],
                        "mxSnf" => $slist['mxSnf'],
                        "rDecFat" => $rlist['rInFat'],
                        "rDecSnf" => $slist['rInSnf'],
                        "rIncFat" => $rlist['rInFat'],
                        "rIncSnf" => $slist['rInSnf'],
                        "rAvgFatSnf" => $slist['rFatSnf'],
                        "avgFat" => $rlist['rFat'],
                        "created_at" => date("Y-m-d H:i:s", time()),
                    ]);

                    if($rangeId[$col->id." ".$key." ". $sKey] == (null||false)) GOTO ERROR;
                }
            }

            foreach($request->data as $data){
    
                $updatedAt = null;
                if($data['isUpdated']=="true"){
                    $updatedAt = date("Y-m-d H:i:s", time());
                }
    
                $insert[] = [
                    "fatRange" => $data['f'],
                    "snfRange" => $data['s'],
                    "amount"   => $data['rate'],
                    "dairyId"  => $dairyId,
                    "rateCardShortId" => $shortId,
                    "rangeListId" => $rangeId[$col->id." ".$data['rangeListKey']],
                    "created_at" => date("Y-m-d H:i:s", time()),
                    "updated_at" => $updatedAt
                ];
            }
    
            if(isset($insert)){
                $res['res'] = DB::table('fat_snf_ratecard')->insert($insert);
                if($res['res'] == (null||false)) GOTO ERROR;
            }
            else GOTO ERROR;

            unset($rangeId);
            unset($shortId);
            unset($insert);
            $count++;
        }

        DB::commit();
        $res['error'] = false;
        $res['msg'] = "Your rate card successfuly saved.";
        $res['count'] = $count;
        $res['end'] = microtime(true);
        return response()->json($res);

        ERROR:
        DB::rollback();
        $res['error'] = true;
        $res['msg'] = "An error has occured.";
        $res['end'] = microtime(true);
        return response()->json($res);
    }

    public function deleteRateCard()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $res['error'] = true;
        if(!request("shortid")){
            $res['msg'] = "There are some error occured.";
            return response()->json($res);
        }

        $rateCard = DB::table('fat_snf_ratecard')
                ->where('rateCardShortId', request("shortid"))
                ->delete();
        $shortCard = DB::table('ratecardshort')
                ->where('id', request("shortid"))
                ->delete();

        $rangeList = DB::table('rangelist')
                ->where('rateCardId', request("shortid"))
                ->delete();

        $res['error'] = false;
        $res['msg'] = "Ratecard successfuly deleted.";
        return response()->json($res);
    }

    public function applyRatecard()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $res['error'] = true;

        if(!request('shortCardId') && !request('rateCardType') && !request('type')){
            Session::flash('msg', 'There is an error occured.'); 
            Session::flash('alert-class', 'alert-danger');
            return $this->rateCardShow();
        }
        
        $dairyId = session()->get('loginUserInfo')->dairyId;

        if($dairyId==(null||'')){
            Session::flash('msg', "<a href='dairy-login'>You must login first!</a>."); 
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairy-login");
        }

        $shortCard = DB::table("ratecardshort")->where("id", request('shortCardId'))->get()->first();
        $rfor = $shortCard->rateCardFor;
        
        
        $dt["updated_at"] = date("Y-m-d H:i:s", time());
        if(request('type')=="cow"){
            $rfor=='buffalo'?$rfor=='both':$rfor = "cow";
            $dt['rateCardIdForCow'] = request('shortCardId');
            $dt['rateCardTypeForCow'] = request('rateCardType');
        }elseif(request('type')=="buffalo"){
            $rfor=='cow'?$rfor=='both':$rfor = "buffalo";
            $dt['rateCardIdForBuffalo'] = request('shortCardId');
            $dt['rateCardTypeForBuffalo'] = request('rateCardType');
        }else{
            $rfor = "both";
            $dt['rateCardIdForCow'] = request('shortCardId');
            $dt['rateCardIdForBuffalo'] = request('shortCardId');
            $dt['rateCardTypeForBuffalo'] = request('rateCardType');
            $dt['rateCardTypeForCow'] = request('rateCardType');
        }
        
        $resp = DB::table("other_users")->where("id", request('collectionManager'))
                                        ->where("dairyId", $dairyId)
                                        ->update($dt);

        $res = DB::table("ratecardshort")->where("id", request('shortCardId'))->update(["rateCardFor"=>$rfor]);

        if($resp){
            Session::flash('msg', 'Rate card Applied Successfully.');
            Session::flash('alert-class', 'alert-success');
            return $this->rateCardShow();
        }else{
            Session::flash('msg', 'There is an error occured.');
            Session::flash('alert-class', 'alert-danger');
            return $this->rateCardShow();
        }
    }

    public function updateRateCardNew(Request $request){

        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }
        
        $res['error'] = true;
        $res['msg'] = "";

        $dairyId = session()->get('loginUserInfo')->dairyId;
        if($dairyId==(null||'')){
            $res['msg'] = "<a href='dairy-login'>You must login first!</a>";
            return response()->json($res);
        }

        if(!$request->data && $request->data==(null||'')){
            $res['msg'] = "Something Wrong, please try again.";
            $res['etype'] = "danger";
            return response()->json($res);
        }

        $rateCardShortId = $request->rateCardShortId;
        $updatedAt = date("Y-m-d H:i:s", time());
        $count=0;
        foreach($request->data as $data){
            $f = $data['f'];
            $s = $data['s'];
            $rate = $data['rate'];
            $rateCardId = $data['rcid'];
            
            $r = DB::table('fat_snf_ratecard')->where('id', $rateCardId)->update([
                "amount"   => $rate,
                "updated_at" => $updatedAt
            ]);
            if($r){
                $count++;
            }
        }

        $r2 = DB::table('ratecardshort')->where('id', $rateCardShortId)->update([
            "updated_at" => $updatedAt
        ]);

        $res['error'] = false;
        $res['msg'] = "Your rate card successfuly updated.";
        $res['count'] = $count;
        $res['r'] = $r;
        $res['r2'] = $r2;

        return response()->json($res);
    }


    public function reports(Request $req)
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        if ($req->type) {
            $repType = $req->type;
        } else {
            $repType = "memberList";
        }

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $memberPersonalInfo = DB::table('member_personal_info')
                ->where('dairyId', $dairyId)
                ->whereNotNull('ledgerId')
                ->where('status', "true")
                ->get();

        $salesData = DB::table('sales')
                ->where('dairyId', $dairyId)
                ->whereNotNull('ledgerId')
                ->where('status', "true")
                ->get();

        $cust = DB::table("customer")->where(["status" => "true", "dairyId" => $dairyId])->get();

        $returnData = array($memberPersonalInfo, $salesData, $cust);

        return view('webviews.report', ['returnData' => $returnData, "device_token" => request('device_token'), "repType" => $repType, "activepage" => $repType]);

    }


    public function getProfitLossReport(Request $req)
    {
        // return $req->all();
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }
        
        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getProfitLossReport(request());
    }

    public function getCustomerSalseReport(Request $req){

        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getCustomerSalseReport(request());
        
    }

    public function getUserDetail(Request $req)
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\DailyTransactionController();
        return $report->getUserDetail(request());

    }

    public function getMemStatementReport(){

        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getMemStatementReport(request());
    }

    public function getCmSubsidiaryReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getCmSubsidiaryReport(request());
    }

    public function getLedgerReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getLedgerReport(request());
    }

    public function getMemberPassbookReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getMemberPassbookReport(request());
    }
    
    public function getBalanceSheetReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getBalanceSheetReport(request());        
    }

    public function getShiftReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getShiftReport(request());        
    }

    public function getRateCardReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getRateCardReport(request());        
    }

    public function getMemberReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getMemberReport(request());        
    }

    public function getSaleReport()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $report = new \App\Http\Controllers\ReprotsController();
        return $report->getSaleReport(request());
    }


    
    public function memStatement(){
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        return response()->view("webviews.memStatement",["device_token" => request("device_token")], 200);
    }

    public function memStatementListAjax()
    {
        $r = APIHelperModel::isAuthReqMem();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $member = new \App\Http\Controllers\member\MemberController();
        return $member->statementListAjax(request());
    }



    public function milkCollection()
    {
        $r = APIHelperModel::isAuthReq();
        if ($r['status_code'] != "200") {
            return response()->json($r, 403);
        }

        $dairyId = $r['data']->dairyId;
        $colMan = $r['data']->colMan;

    }
}
