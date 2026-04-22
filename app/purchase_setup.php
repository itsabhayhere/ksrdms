<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class purchase_setup extends Model
{
    public function purchaseFormSubmit($request){

        $currentTime =  date('Y-m-d H:i:s');
        $date=date_create($request->date);
        $relatedTableName = "" ;
        $paymentType = "" ;

        if($request->productType == "Customer"){
            $paymentType = "customer_payment";
            $relatedTableName = "customer";
        }elseif($request->productType == "Milk Plants"){
            $paymentType = "plant_payment";
            $relatedTableName = "milk_plants";
        }elseif($request->productType == "Member"){
            $paymentType = "member_payment";
            $relatedTableName = "member_personal_bank_info";
        }elseif($request->productType == "Supplier"){
            $paymentType = "supplier_payment";
            $relatedTableName = "suppliers";
        }else{
            $relatedTableName = "other";
            $paymentType = "other" ;
        }


        $currentBalace = "" ;
        $currentTable = "";
        if($relatedTableName != "other"){

            if($relatedTableName == "member_personal_bank_info"){
                $getMemberLedgerid = DB::table("member_personal_info") 
                  ->where('ledgerId', $request->ledgerName )
                  ->get();
                $userInfo =  DB::table("member_personal_bank_info") 
                  ->where('memberPersonalUserId', $getMemberLedgerid[0]->id )
                  ->get();
                  $currentTable = "member_personal_bank_info";
              
            }else{
                $userInfo = DB::table($relatedTableName) 
                  ->where('ledgerId', $request->ledgerName )
                  ->get();
                  $currentTable = $relatedTableName ;
            }

            $currentBalaceType = $userInfo[0]->openingBalanceType ;

            if($currentBalaceType == "debit" && $request->paymentMode == "credit" ){
                $currentBalace = $userInfo[0]->openingBalance - $request->amount ;
                if( $currentBalace < 0 ) {
                    $currentBalace = str_replace("-","", $currentBalace);
                    $updateReturn = DB::table($currentTable)
                                ->where('id', $userInfo[0]->id)
                                ->update([
                                    'openingBalance' => $currentBalace ,
                                    'openingBalanceType' => "credit",
                                ]);
                    }else{
                    $updateReturn = DB::table($currentTable)
                                ->where('id', $userInfo[0]->id)
                                ->update([
                                    'openingBalance' => $currentBalace ,
                                    'openingBalanceType' => "debit",
                                ]);
                    }
            }elseif($currentBalaceType == "credit" && $request->paymentMode == "credit"){
                $currentBalace = $userInfo[0]->openingBalance + $request->amount ;
                $updateReturn = DB::table($currentTable)
                                ->where('id', $userInfo[0]->id)
                                ->update([
                                    'openingBalance' => $currentBalace ,
                                    'openingBalanceType' => "credit",
                                ]);
            }
        }
      
        $submiteInfo = DB::table('purchase_setups')->insertGetId(
              [
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'ledgerId' => $request->ledgerName,
                'partyName' => $request->partyName,
                'date' => $request->date,
                'time' => $request->time,
                'unit' => $request->unit,
                'itemsPurchased' => $request->itemsPurchased, 
                'quantity' => $request->quantity, 
                'PricePerUnit' => $request->PricePerUnit, 
                'purchaseType' => $request->paymentMode, 
                'amount' => $request->amount, 
 				'created_at' => $currentTime,
              ]
        );

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
                'ledgerId' => $request->ledgerName,
                'transactionId' => $submiteInfo,
                'srcDest'       => "-",
                'dairyId' => $request->dairyId,
                'transactionType' => 'purchase',
                'amountType' =>  $request->paymentMode ,
                'finalAmount' => $request->amount ,
                'created_at' => $currentTime,
              ]);

        $products = DB::table('products') 
                      ->where('id', $request->itemsPurchased )
                      ->where('status', "true" )
                      ->get()->first();
        $productsNewInfo = $products->productUnit - $request->quantity ;
        
        
        $updateReturn = DB::table('products')
                            ->where('id', $request->itemsPurchased )
                            ->update([
                                'productUnit' => $productsNewInfo ,
                            ]);

        
		$returnSuccessArray = array("Success"=>"True","Message"=>"Purchase Submited");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;
	}
}
