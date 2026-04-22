<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class rateCard extends Model
{
    /* rate card by fat api */
    public function rateCardByFatApi($request,$rangeArray,$rateArray){
   
		 /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');

		$id = DB::table('rate_on_fet')->insertGetId(
    		[
    			'fat' => implode(",",$rangeArray),
    			'rate' =>  implode(" ",$rateArray),
    			 'created_at' => $currentTime,
    		]
		);

		$submit = DB::table('rate_card')->insertGetId(
    		[
    			'dairyId' => $request->dairyId,
    			'collectionManager' => $request->CollectionManager,
    			'rateType' => $request->rateType,
    			'rateCard' => $id,
    			 'created_at' => $currentTime,
    		]
		);

		$returnValue = array( "success" => "True", "message" => "Rate genrated by fat only","rangeArray"=>$rangeArray ,"rateArray"=> $rateArray);
		return $returnValue ;
    }

    /* rate Card By Fat Web */
    public function rateCardByFatWeb($request,$rangeArray,$rateArray){
   
		 /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');

		$id = DB::table('rate_on_fet')->insertGetId(	
    		[
    			'fat' => implode(",",$rangeArray),
    			'rate' =>  implode(" ",$rateArray),
    			 'created_at' => $currentTime,
    		]
		);
		$submit = DB::table('rate_card')->insertGetId(
    		[
    			'dairyId' => $request->dairyId,
    			'collectionManager' => $request->CollectionManager,
    			'rateType' => $request->rateType,
    			'rateCard' => $id,
    			 'created_at' => $currentTime,
    		]
		);
	

		return True  ;
    }

}
