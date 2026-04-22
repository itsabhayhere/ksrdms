<?php

namespace App\Http\Controllers;

use App\rateCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use PDF;
use App;
use PDO;

class RateCardController extends Controller
{   
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    // public function fatSnfRateCardShow(Request $request){
    //     return view('rateCardList', ['activepage'=>"rateCard"]);
    // }

    /* fat Snf Single Range Edit Submit */
    public function fatSnfSingleRangeEditSubmit(Request $request){
        
            DB::table('fat_snf_runtime')
            ->where('id', $request->currnetRangeId )
            ->update([
                'minFatRange' => $request->fatSnfMinFatValueRange ,
                'maxFatRange' => $request->fatSnfMaxFatValueRange ,
                'rateIncreseByFatIncrese' => $request->fatSnfRateIncreaseByFat ,
                'rateIncreseBySnfIncrese' => $request->fatSnfRateIncreaseBySnf ,
                'rateDecreaseByFatDecrease' => $request->fatSnfRateDecreaseByFat ,
                'rateDecreaseBySnfDecrease' => $request->fatSnfRateDecreaseBySnf ,
                'MidPointFat' => $request->midRangeOfFat ,
                'MidPointSnf' => $request->midRangeOfSnf ,
                'RateByMidPoint' => $request->midRangeOfFatSnfPrice ,
            ]);

            $singleFatSnfRange = DB::select("select * from fat_snf_runtime where dairyId = '".$request->DairyIdFat ."' ");
            return $singleFatSnfRange ;

    }

    /* function for gat single range of fat and snf */
    public function fatSnfSingleRange(Request $request){
       
        $singleFatSnfRange = DB::select("select * from fat_snf_runtime where id = '".$request->dataId ."' ");
        return $singleFatSnfRange ;
    
    }

    /* set rate by fat submit */
    public function rateCardFetSubmit(Request $request){

        $minFatValue = $request->minFatValue ;
        $maxFatValue = $request->maxFatValue ;
        $rateForMinFatValue = $request->rateForMinFatValue ;
        $rateIncreseForFat = $request->rateIncreseForFat ;

        $rangeArray = array();
        $rateArray = array();

        for ($x = $minFatValue; $x <= $maxFatValue; $x = $x + 0.1) {
            if($x == $minFatValue){
                $rangeArray[] = (string)$x ;
                $rateArray[] = (string)$rateForMinFatValue ; 
            }else{
               $rangeArray[] = (string)$x ;
                $newRate = $rateForMinFatValue + $rateIncreseForFat;
                $rateForMinFatValue = $newRate ;
                $rateArray[] = (string)$newRate ;
            }
        }  

        $submitClass = new rateCard();
        $submitReturn = $submitClass->rateCardByFatWeb($request,$rangeArray,$rateArray); 
        if($submitReturn == True){

            $returnArray = array('rangeArray' => $rangeArray,'rateArray' => $rateArray);
            return $returnArray ;
           // return view('rateCard', ['rangeArray' => $rangeArray],['rateArray' => $rateArray]);
        }else{
            $returnArray = array('message' => 'Something is not valid.');
             return $returnArray ;
           // return redirect()->back()->with("message", "Something is not valid.");
        }

    }   

    /* edit price by fat range */
    public function editFatPrice(Request $request){

        $dairySocietyCode = DB::table('dairy_info')
            ->where('society_code', $request->society_code)
            ->get();
    }

    /* get current rate card id */
    public function submitFatPriceEdit(Request $request){
        
        $rateCardId = DB::select("select * from rate_card where dairyId = '".$request->DairyIdFat ."' ORDER BY created_at DESC");
        $rateCardId = DB::select("select * from rate_on_fet where id = '".$rateCardId[0]->id."'");
        $currentRateId = $rateCardId[0]->id ;

        $rateAll = explode(" ",$rateCardId[0]->rate) ;
      
        $rateAll[$request->EditRateByFatPosition] = $request->EditRateByFat;
        $rateAll = implode(" ",$rateAll) ;

        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
         DB::table('rate_on_fet')
            ->where('id', $currentRateId )
            ->update([
                'rate' => $rateAll ,
                'updated_at' => $currentTime,
            ]);
        
        $returnArray = array('Message' => 'Current Range Successfully Edited.',"Success" => "True");
             return $returnArray ;
    }

    /* rate card form web */
    public function rateCardForm(){
      
        $collactionManager = DB::select("SELECT roles_setups.id, roles_setups.role, other_users.userName, other_users.id, other_users.roleId FROM roles_setups, other_users WHERE roles_setups.role = 'collaction manager' AND roles_setups.id = other_users.roleId");
       
        // DB::table('fat_snf_runtime')->where('dairyId', '=', session()->get('loginUserInfo')->dairyId )->delete();

        $dairyInfo = DB::table('dairy_info')
                ->where('id', session()->get('loginUserInfo')->dairyId)
                ->get()
                ->first();

        return view('rateCard', ['dairyInfo' => $dairyInfo,'collactionManager' => $collactionManager, 'activepage'=>"rateCard"]);
        
    }

    public function rateCardNew(){
        
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
            return redirect('rateCardShowNew');
        }

        $dairyInfo = DB::table('dairy_info')
                ->where('id', $dairyId)
                ->get()
                ->first();

        return view('rateCardNew', ['dairyInfo' => $dairyInfo,'colManager' => $colManager, 'activepage'=>"rateCard"]);
        
    }

    public function saveRateCardNew(Request $request){

        $res['st'] = microtime(true);

        $request = (Object)json_decode($request->getContent(), true);
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
                "description" => $request->description,
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
            
            
            // DB::rollback();
            // return json_encode(["error" => true, "rangeId" => $rangeId])."lkjrower";

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

    public function rateCardShowNew(){
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $colMan = session()->get('colMan');
        // return [$colMan];
        
        $rateCards = DB::table('ratecardshort')
                        ->select("ratecardshort.id as id", "ratecardshort.rateCardType as rateCardType", "ratecardshort.minFat as minFat", 
                                "ratecardshort.maxFat as maxFat", "ratecardshort.minSnf as minSnf", "ratecardshort.maxSnf as maxSnf", "rateCardFor",
                                "other_users.id as colManId","rateCardIdForCow", "rateCardIdForBuffalo", "ratecardshort.description as description")
                        ->join("other_users", "other_users.id", "=", "ratecardshort.collectionManager")
                        ->where('ratecardshort.dairyId', $dairyId)
                        ->where('ratecardshort.collectionManager', $colMan->id)
                        ->get();

        $defaultRateCard = DB::table("other_users")->where("dairyId", $dairyId)->where("userName", $colMan->userName)->get()->first();

        if($defaultRateCard->rateCardIdForBuffalo == (null||'') || $defaultRateCard->rateCardIdForCow == (null||'')){
            Session::flash('msg', 'Rate Card not applied for cow and buffalo.');
            Session::flash('alert-class', 'alert-info');
        }

        $dairyInfo = DB::table("dairy_info")->where('id', $dairyId)->get()->first();

        return view('rateCardList', ['rateCardShort' => $rateCards, 'dairyInfo'=>$dairyInfo, "defaultRateCard"=>$defaultRateCard, 'activepage'=>"rateCard"]);
    }

    public function getRateCardList(Request $request){
        $res['error'] = true;
        if(!$request->shortid){
            $res['msg'] = "There are some error occured.";
            $res['etype'] = "danger";
            return response()->json($res);
        }

        $rangeList = DB::table("rangelist")
            ->where("rateCardId", $request->shortid)
            ->get();

        $shortCard = DB::table('ratecardshort')
            ->where('id', $request->shortid)
            ->get()->first();

        $rateCard = DB::table('fat_snf_ratecard')
                ->where('rateCardShortId', $request->shortid)
                // ->where('rangeListId', $rangeList->pluck('id'))
                ->orderBy('fatRange')
                ->orderBy('snfRange')
                ->get();
        
        // return $rateCard;

        $cardFor = "";
        if($shortCard->rateCardFor=="buffalo")
            $cardFor = "buff";
        if($shortCard->rateCardFor=="cow")
            $cardFor = "cow";
        if($shortCard->rateCardFor=="both")
            $cardFor = "both";
    
        return view('modelRateCardList', ['rateCard' => $rateCard, "shortCard"=>$shortCard, "rangeList"=>$rangeList, "cardFor"=>$cardFor]);
    }

    public function deleteRateCard(Request $request){
        $res['error'] = true;
        if(!$request->shortid){
            $res['msg'] = "There are some error occured.";
            return response()->json($res);
        }

        $rateCard = DB::table('fat_snf_ratecard')
                ->where('rateCardShortId', $request->shortid)
                ->delete();
        $shortCard = DB::table('ratecardshort')
                ->where('id', $request->shortid)
                ->delete();

        $rangeList = DB::table('rangelist')
                ->where('rateCardId', $request->shortid)
                ->delete();

        $res['error'] = false;
        $res['msg'] = "Ratecard successfuly deleted.";
        return response()->json($res);
    }

    public function updateRateCardNew(Request $request){
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


    public function applyRatecard(Request $request){
        $res['error'] = true;

        if(!$request->shortCardId && !$request->rateCardType && !$request->type){
            Session::flash('msg', 'There is an error occured.'); 
            Session::flash('alert-class', 'alert-danger');
            return redirect("rateCardShowNew");
        }
        
        $dairyId = session()->get('loginUserInfo')->dairyId;

        if($dairyId==(null||'')){
            Session::flash('msg', "<a href='dairy-login'>You must login first!</a>."); 
            Session::flash('alert-class', 'alert-danger');
            return redirect("dairy-login");
        }

        $shortCard = DB::table("ratecardshort")->where("id", $request->shortCardId)->get()->first();
        $rfor = $shortCard->rateCardFor;
        
        
        $dt["updated_at"] = date("Y-m-d H:i:s", time());
        if($request->type=="cow"){
            $rfor=='buffalo'?$rfor=='both':$rfor = "cow";
            $dt['rateCardIdForCow'] = $request->shortCardId;
            $dt['rateCardTypeForCow'] = $request->rateCardType;
        }elseif($request->type=="buffalo"){
            $rfor=='cow'?$rfor=='both':$rfor = "buffalo";
            $dt['rateCardIdForBuffalo'] = $request->shortCardId;
            $dt['rateCardTypeForBuffalo'] = $request->rateCardType;
        }else{
            $rfor = "both";
            $dt['rateCardIdForCow'] = $request->shortCardId;
            $dt['rateCardIdForBuffalo'] = $request->shortCardId;
            $dt['rateCardTypeForBuffalo'] = $request->rateCardType;
            $dt['rateCardTypeForCow'] = $request->rateCardType;
        }
        
        $resp = DB::table("other_users")->where("id", $request->collectionManager)
                                        ->where("dairyId", $dairyId)
                                        ->update($dt);

        $res = DB::table("ratecardshort")->where("id", $request->shortCardId)->update(["rateCardFor"=>$rfor]);

        if($resp){
            Session::flash('msg', 'Rate card Applied Successfully.');
            Session::flash('alert-class', 'alert-success');
            return redirect("rateCardShowNew");
        }else{
            Session::flash('msg', 'There is an error occured.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("rateCardShowNew");
        }
        
    }

    /* rate card api */
    public function fatRateCardSubmit(Request $request){
        
        $minFatValue = $request->minFatValue ;
        $maxFatValue = $request->maxFatValue ;
        $rateForMinFatValue = $request->rateForMinFatValue ;
        $rateIncreseForFat = $request->rateIncreseForFat ;
    
        $rangeArray = array();
        $rateArray = array();

        for ($x = $minFatValue; $x <= $maxFatValue; $x = $x + 0.1) {
            if($x == $minFatValue){
                $rangeArray[] = (string)$x ;
                $rateArray[] = (string)$rateForMinFatValue ; 
            }else{
               $rangeArray[] = (string)$x ;
                $newRate = $rateForMinFatValue + $rateIncreseForFat;
                $rateForMinFatValue = $newRate ;
                $rateArray[] = (string)$newRate ;
            }
            
        }  

        $submitClass = new rateCard();
        $submitReturn = $submitClass->rateCardByFatApi($request,$rangeArray,$rateArray); 
        return $submitReturn;
        
    }

    /* add fat and snf range for get rate table */

    public function addFatSnfRange(Request $request){

        /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');

        $submitId = DB::table('fat_snf_runtime')->insertGetId(
            [
                'dairyId' => $request->dairyId,
                'minFatRange' => $request->fatSnfMinFatValueRange,
                'maxFatRange' => $request->fatSnfMaxFatValueRange,
                'rateIncreseByFatIncrese' =>  $request->fatSnfRateIncreaseByFat,
                'rateIncreseBySnfIncrese' => $request->fatSnfRateIncreaseBySnf,
                'rateDecreaseByFatDecrease' => $request->fatSnfRateDecreaseByFat,
                'rateDecreaseBySnfDecrease' => $request->fatSnfRateDecreaseBySnf,
                'MidPointFat' =>  $request->midRangeOfFat,
                'MidPointSnf' =>  $request->midRangeOfSnf,
                'RateByMidPoint' => $request->midRangeOfFatSnfPrice,
                'created_at' => $currentTime,
            ]
        );
        return $submitId ;
    
    }

    /*get Fat Snf Range Table*/
    public function getFatSnfRangeTable(Request $request){
        $rateCardId = DB::select("select * from fat_snf_runtime where dairyId = '".$request->dairyId ."' ");
        return $rateCardId;
    }

    /* Fat Snf Range Table Update */
    public function fatSnfRangeTableUpdate(Request $request){

        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
         DB::table('fat_snf_runtime')
            ->where('id', $request->currentRangeId )
            ->update([
                'minFatRange' => $request->minFatRange,
                'maxFatRange' => $request->maxFatRange,
                'rateIncreseByFatIncrese' =>  $request->rateIncreseByFatIncrese,
                'rateIncreseBySnfIncrese' => $request->rateIncreseBySnfIncrese,
                'rateDecreaseByFatDecrease' => $request->rateDecreaseByFatDecrease,
                'rateDecreaseBySnfDecrease' => $request->rateDecreaseBySnfDecrease,
                'MidPointFat' =>  $request->MidPointFat,
                'MidPointSnf' =>  $request->MidPointSnf,
                'RateByMidPoint' => $request->RateByMidPoint,
                'updated_at' => $currentTime,
        ]);
    }

    public function deleteFatSnfRange($value='')
    {
        $collactionManager = DB::select("SELECT roles_setups.id, roles_setups.role, other_users.userName, other_users.id, other_users.roleId FROM roles_setups, other_users WHERE roles_setups.role = 'collaction manager' AND roles_setups.id = other_users.roleId"); 
       
        DB::table('fat_snf_runtime')->where('dairyId', '=', session()->get('loginUserInfo')->dairyId )->delete();

        $dairyInfo = DB::table('dairy_info')
                ->where('id', session()->get('loginUserInfo')->dairyId)
                ->get()
                ->first();

        return view('rateCard', ['dairyInfo' => $dairyInfo,'collactionManager' => $collactionManager, 'activepage'=>"rateCard"]);
    }



    public function fatSnfRateCardSubmit(Request $request){

        $currentTime =  date('Y-m-d H:i:s');

        $submitId = DB::table('fat_snf_ratecard')->insertGetId(
            [
                'dairyId' => $request->dairyId,
                'fatRange' => $request->fatRange,
                'snfRange' => $request->snfRange,
                'amount' => $request->amount,
                'created_at' => $currentTime,
            ]
        );
        return $submitId ;
        // return view('rateCardList');

    }

    public function getFatSnfRangeData(Request $request){

        $fatSnfRatecard = DB::table('fat_snf_ratecard')
                        ->where('dairyId', $request->dairyId)
                        ->get();
        $snfRange = [];
        $fatRange = [] ;
        // echo "<pre>";
        foreach ($fatSnfRatecard as $fatSnfRatecardData) {
            if (!(in_array($fatSnfRatecardData->snfRange, $snfRange))) {
                $snfRange[] = $fatSnfRatecardData->snfRange ;
            }
            if (!(in_array($fatSnfRatecardData->fatRange, $fatRange))) {
                $fatRange[] = $fatSnfRatecardData->fatRange ;
            }
        }
        sort($snfRange);
        $currentSnfRange = [] ;
        $snfCount = count($snfRange);
        for($x = 0; $x < $snfCount; $x++) {
            $currentSnfRange[] = $snfRange[$x] ;
        }

        sort($fatRange);
        $currentFatRange = [] ;
        $fatCount = count($fatRange);
        for($x = 0; $x < $fatCount; $x++) {
            $currentFatRange[] = $fatRange[$x] ;
        }
       
        $rangeRate = [] ;
        for($i=0;$i<$fatCount;$i++){
            for($j=0;$j<$snfCount;$j++){
             
                $currentFatSnfRate = DB::table('fat_snf_ratecard')
                ->where('dairyId', $request->dairyId)
                ->where('fatRange', $currentFatRange[$i])
                ->where('snfRange', $currentSnfRange[$j])
                ->get()->first();                
                $rangeRate[] = $currentFatSnfRate ;
            }
        }


       
        $mainValue = [] ;
        $rangeRateCount = count($rangeRate) ;
        $loopCount = 0 ;
        $fatRateRange = [] ;
        for($x = 0; $x < $fatCount; $x++) {
            $mainValueArray = [] ;
            for($i=0;$i<$rangeRateCount;$i++){
                if(!empty($rangeRate[$i]) && $fatRange[$x] == $rangeRate[$i]->fatRange ){
                    $mainValueArray[] = $rangeRate[$i]->amount  ; 
                }
            }   
            $mainValue[] = $mainValueArray  ;
            $fatRateRange[] = $fatRange[$x]  ;
            $loopCount++ ;
        }
        

        $returnArray = array($currentSnfRange,$mainValue,$loopCount,$fatRateRange) ;
      /*  echo "<pre>";
        print_r($returnArray);
        die;*/
        return $returnArray ;
     
        // return view('rateCardList', ['currentSnfRange' => $currentSnfRange],['mainValue' => $mainValue]);
    }

       public function getFatSnfRangeDataPdf(Request $request){
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->rateCardTable);
        return $pdf->download('Rate Card.pdf');
   }

    /* get fat/snf rate card api */
    public function getFatSnfRangeDataApi(Request $request){

        $fatSnfRatecard = DB::table('fat_snf_ratecard')
                        ->where('dairyId', $request->dairyId)
                        ->get();
        $snfRange = [];
        $fatRange = [] ;
        
        foreach ($fatSnfRatecard as $fatSnfRatecardData) {
            if (!(in_array($fatSnfRatecardData->snfRange, $snfRange))) {
                $snfRange[] = $fatSnfRatecardData->snfRange ;
            }
            if (!(in_array($fatSnfRatecardData->fatRange, $fatRange))) {
                $fatRange[] = $fatSnfRatecardData->fatRange ;
            }
        }
        sort($snfRange);
        $currentSnfRange = [] ;
        $snfCount = count($snfRange);
        for($x = 0; $x < $snfCount; $x++) {
            $currentSnfRange[] = $snfRange[$x] ;
        }

        sort($fatRange);
        $currentFatRange = [] ;
        $fatCount = count($fatRange);
        for($x = 0; $x < $fatCount; $x++) {
            $currentFatRange[] = $fatRange[$x] ;
        }
       
        $rangeRate = [] ;
        for($i=0;$i<$fatCount;$i++){
            for($j=0;$j<$snfCount;$j++){
             
                $currentFatSnfRate = DB::table('fat_snf_ratecard')
                ->where('dairyId', $request->dairyId)
                ->where('fatRange', $currentFatRange[$i])
                ->where('snfRange', $currentSnfRange[$j])
                ->get()->first();                
                $rangeRate[] = $currentFatSnfRate ;
            }
        }
               
        $mainValue = [] ;
        $rangeRateCount = count($rangeRate) ;
        for($x = 0; $x < $fatCount; $x++) {
            $mainValueArray = [] ;
            for($i=0;$i<$rangeRateCount;$i++){
                if(!empty($rangeRate[$i]) && $fatRange[$x] == $rangeRate[$i]->fatRange ){
                    $mainValueArray[] = $rangeRate[$i]  ; 
                }
            }   
            $mainValue[$fatRange[$x]] = $mainValueArray  ;
        }
       
        $returnArray = array("snf"=>$currentSnfRange, "range"=>$mainValue) ;
        return $returnArray ;
    }

    public function getFatSnfRangeDataEdit(Request $request){

        $currentTime =  date('Y-m-d H:i:s');
         DB::table('fat_snf_ratecard')
            ->where('id', $request->rangeId )
            ->update([
                'amount' => $request->amount ,
                'updated_at' => $currentTime,
            ]);
        
        $returnArray = array('Message' => 'Current Range Successfully Edited.',"Success" => "True");
        return $returnArray ;
    
    }

    public function fatSnfRateCardvalue(Request $request){
        // return ["req"=>$request->all()];
        $colMan = session()->get("colMan");
        $memberId = DB::table("member_personal_info")->where(["memberPersonalCode" => $request->memberCode, "dairyId" => $colMan->dairyId])->get()->first();

        if($memberId==(null||"")){
            return ["error"=>true, "msg"=>"MEMBER_ID_NULL"];
        }
        
        $colMan = DB::table("other_users")->where("dairyId", $colMan->dairyId)
                    ->where("userName", $colMan->userName)
                    ->get()->first();

        if($request->fat<=5){
            $milkType = "cow";
            $rateCardId = $colMan->rateCardIdForCow;
            $rateCardType = $colMan->rateCardTypeForCow;
        }else{
            $rateCardId = $colMan->rateCardIdForBuffalo;
            $rateCardType = $colMan->rateCardTypeForBuffalo;
            $milkType = "buffalo";
        }


        if($rateCardId==(null||'')){
            return ["error"=>true, "msg"=>"RATECARD_NOT_FOUND"];
        }

        $ratecardshort = DB::table('ratecardshort')
                ->where('id', $rateCardId)
                ->get()->first();

        if($ratecardshort==(null||'')){
            return ["error"=>true, "msg"=>"RATECARD_NOT_FOUND_404"];
        }
        
        if($ratecardshort->rateCardType == "fat"){
            if($ratecardshort->minFat > $request->fat || $ratecardshort->maxFat < $request->fat ){
                return ["error"=>true, "rateCardType" => $rateCardType, "milkType" => $milkType,
                        "msg"=>"Please enter correct values, <br/> Min Fat: <b>".$ratecardshort->minFat."</b> &nbsp;&nbsp; Max Fat: <b>".$ratecardshort->maxFat."</b><br/>"];
            }
        }else{
            if($ratecardshort->minFat > $request->fat || $ratecardshort->maxFat < $request->fat || $ratecardshort->minSnf > $request->snf || $ratecardshort->maxSnf < $request->snf ){
                return ["error"=>true, "rateCardType" => $rateCardType, "milkType" => $milkType,
                        "msg"=>"Please enter correct values, <br/> Min Fat: <b>".$ratecardshort->minFat."</b> &nbsp;&nbsp; Max Fat: <b>".$ratecardshort->maxFat."</b><br/> Min SNF: <b>".$ratecardshort->minSnf."</b> &nbsp;&nbsp; Max SNF: <b>".$ratecardshort->maxSnf."</b>"];
            }
        }
        
        $fatSnfRatecard = DB::table('fat_snf_ratecard')
                ->where('dairyId', $colMan->dairyId)
                ->where("rateCardShortId", $rateCardId)
                ->where('fatRange', $request->fat)
                ->where('snfRange', $request->snf)
                ->get()->first();
        
        if($fatSnfRatecard==(null||"")){
            return ["error"=>true, "msg"=>"RATECARD_HAS_NO_VALUE", "rateCardType" => $rateCardType, "milkType" => $milkType];
        }else{
            return ["error"=>false, 'amount' => $fatSnfRatecard->amount,
                    "rateCardType" => $rateCardType, "milkType" => $milkType];
        }
    }

    public function getTransValues(Request $req){
        // return ["23"=>$req->all()];
        if($req->transId==(null||"")){
            return ["error"=>true, "msg"=>"BED_REQUEST"];
        }

        $data = DB::table("daily_transactions")->where("id", $req->transId)->get()->first();
        if($data==(null||"")){
            return ["error"=>true, "msg"=>"NO_TRANSACTION_FOUND"];            
        }

        return ["error"=>false, "trans"=>$data];

    }

    public function rateChart(){
        return view('admin.rateChart', ['activepage'=>"rateCard"]);
    }
}
