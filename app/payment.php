<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class payment extends Model
{
    //H:i:s
    public function paymentFormSubmit($request){
		date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $date=date_create($request->date);

        $paymentId = DB::table('payment')->insertGetId([
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'ledgerId' => $request->ledgerName,
                'partyName' => $request->partyName,
                'paymentDate' =>  $request->date,
                'paymentTime' =>  $request->time, 
                'paymentType' =>  $request->paymentType ,
                'paymentMode' =>  $request->paymentMode ,
                'paymentAmount' =>  $request->paymentAmount,
                'created_at' => $currentTime,
            ]);

		$userCurrentBalance = DB::table("user_current_balance") 
					  ->where('ledgerId', $request->ledgerName )
					  ->get()->first();


		$currentDairyBalance = DB::table("user_current_balance") 
			  ->where('userId', $request->dairyId )
			  ->get()->first();
					  
		if($request->paymentMode == "credit"){ 
			if($request->paymentType == "acceptAmount"){
		          
                  /* user account changes */
				if($userCurrentBalance->openingBalanceType == "debit" ){
						$newUserBalance = $userCurrentBalance->openingBalance + $request->paymentAmount ;
                        $updateReturn = DB::table('user_current_balance')
                                ->where('ledgerId', $request->ledgerName )
                                ->update([
                                    'openingBalance' => $newUserBalance  ,
                                    'openingBalanceType' => "debit",
                                ]);
		        }else{
                    $newUserBalance = $userCurrentBalance->openingBalance - $request->paymentAmount ;
                    if( $newUserBalance < 0 ) {
                        $currentBalace = str_replace("-","", $newUserBalance);
                            $updateReturn = DB::table('user_current_balance')
                                ->where('ledgerId', $request->ledgerName )
                                ->update([
                                    'openingBalance' => $currentBalace ,
                                    'openingBalanceType' => "debit",
                                ]);
                                
                    }else{
                        $updateReturn = DB::table('user_current_balance')
                            ->where('ledgerId', $request->ledgerName )
                            ->update([
                                'openingBalance' => $newUserBalance ,
                                'openingBalanceType' => "credit",
                            ]);
                                
                    }
                }

                /* dairy account changes */
                if($currentDairyBalance->openingBalanceType == "debit" ){
                    $newDairyBalance = $currentDairyBalance->openingBalance - $request->paymentAmount ;
                    if( $newDairyBalance < 0 ) {
                                    $currentBalace = str_replace("-","", $newDairyBalance);
                                    $updateReturn = DB::table('user_current_balance')
                                        ->where('userId', $request->dairyId )
                                        ->update([
                                        'openingBalance' => $currentBalace ,
                                        'openingBalanceType' => "credit",
                                    ]);
                    }else{
                        $updateReturn = DB::table('user_current_balance')
                            ->where('userId', $request->dairyId )
                            ->update([
                            'openingBalance' => $newDairyBalance ,
                            'openingBalanceType' => "debit",
                        ]);
                    }
                }else{
                    $newDairyBalance = $currentDairyBalance->openingBalance + $request->paymentAmount ;

                        $updateReturn = DB::table('user_current_balance')
                            ->where('userId', $request->dairyId )
                            ->update([
                            'openingBalance' => $newDairyBalance ,
                            'openingBalanceType' => "credit",
                        ]);
                }


                    /* balance sheet entry */
                    /* dairy entry in balance sheet */
                    $submiteInfo = DB::table('balance_sheet')->insertGetId([
                            'ledgerId'          => $currentDairyBalance->ledgerId, 
                            'dairyId'           => $request->dairyId,
                            'transactionId'     => $paymentId,
                            'srcDest'           => $userCurrentBalance->ledgerId,
                            'transactionType'   => 'payment',
                            'amountType'        => 'debit' ,
                            'finalAmount'       => $request->paymentAmount ,
                            'created_at'        => $currentTime,
                          ]);

                    /* user entry in balance sheet */                    
                    $submiteInfo = DB::table('balance_sheet')->insertGetId([
                            'ledgerId' => $userCurrentBalance->ledgerId,
                            'dairyId' => $request->dairyId,
                            'transactionId' => $paymentId,
                            'srcDest' => $currentDairyBalance->ledgerId,
                            'transactionType' => 'payment',
                            'amountType' =>  'credit' ,
                            'finalAmount' => $request->paymentAmount ,
                            'created_at' => $currentTime,
                    ]);
                
            }else{
                    /* user account changes */
                if($userCurrentBalance->openingBalanceType == "debit" ){
                    $newUserBalance = $userCurrentBalance->openingBalance - $request->paymentAmount ;
                    if( $newUserBalance < 0 ) {
                        $currentBalace = str_replace("-","", $newUserBalance);
                            $updateReturn = DB::table('user_current_balance')
                                ->where('ledgerId', $request->ledgerName )
                                ->update([
                                    'openingBalance' => $currentBalace ,
                                    'openingBalanceType' => "credit",
                                ]);
                                
                    }else{
                        $updateReturn = DB::table('user_current_balance')
                            ->where('ledgerId', $request->ledgerName )
                            ->update([
                                'openingBalance' => $newUserBalance ,
                                'openingBalanceType' => "debit",
                            ]);
                                
                    }

                }else{
                    $newUserBalance = $userCurrentBalance->openingBalance + $request->paymentAmount ;
                    $updateReturn = DB::table('user_current_balance')
                            ->where('ledgerId', $request->ledgerName )
                            ->update([
                                'openingBalance' => $newUserBalance  ,
                                'openingBalanceType' => "credit",
                            ]);
                }

            /* dairy account changes */
                if($currentDairyBalance->openingBalanceType == "debit" ){
                    $newDairyBalance = $currentDairyBalance->openingBalance + $request->paymentAmount ;
                    $updateReturn = DB::table('user_current_balance')
                        ->where('userId', $request->dairyId )
                        ->update([
                        'openingBalance' => $newDairyBalance ,
                        'openingBalanceType' => "debit",
                    ]);
                }else{
                    $newDairyBalance = $currentDairyBalance->openingBalance - $request->paymentAmount ;
                    if( $newDairyBalance < 0 ) {
                        $currentBalace = str_replace("-","", $newDairyBalance);
                        $updateReturn = DB::table('user_current_balance')
                            ->where('userId', $request->dairyId )
                            ->update([
                            'openingBalance' => $currentBalace ,
                            'openingBalanceType' => "debit",
                        ]);
                    }else{
                        $updateReturn = DB::table('user_current_balance')
                            ->where('userId', $request->dairyId )
                            ->update([
                            'openingBalance' => $newDairyBalance ,
                            'openingBalanceType' => "credit",
                        ]);
                    }
                }

                 /* balance sheet entry */
                    /* user entry in balance sheet */
                    $submiteInfo = DB::table('balance_sheet')->insertGetId([
                            'ledgerId' => $userCurrentBalance->ledgerId,
                            'dairyId' => $request->dairyId,
                            'transactionId' => $paymentId,
                            'sendTo' => $currentDairyBalance->ledgerId,
                            'transactionType' => 'payment',
                            'amountType' =>  'debit' ,
                            'finalAmount' => $request->paymentAmount ,
                            'created_at' => $currentTime,
                          ]);
                    
                    /* dairy entry in balance sheet */
                    $submiteInfo = DB::table('balance_sheet')->insertGetId([
                            'ledgerId'          => $currentDairyBalance->ledgerId,
                            'dairyId'           => $request->dairyId,
                            'transactionId'     => $paymentId,
                            'srcDest'           => $userCurrentBalance->ledgerId,
                            'transactionType'   => 'payment',
                            'amountType'        =>  'credit' ,
                            'finalAmount'       => $request->paymentAmount ,
                            'created_at'        => $currentTime,
                          ]);
            }
        }else{
            if($request->paymentType == "acceptAmount"){
                /* balance sheet entry */
                /* user entry in balance sheet */
                $submiteInfo = DB::table('balance_sheet')->insertGetId([
                        'ledgerId'          => $userCurrentBalance->ledgerId,
                        'dairyId'           => $request->dairyId,
                        'transactionId'     => $paymentId,
                        'srcDest'           => $currentDairyBalance->ledgerId,
                        'transactionType'   => 'payment',
                        'amountType'        => 'cash' ,
                        'finalAmount'       => $request->paymentAmount ,
                        'created_at'        => $currentTime,
                      ]
                );
                
                /* dairy entry in balance sheet */
                $submiteInfo = DB::table('balance_sheet')->insertGetId([
                        'ledgerId'          => $currentDairyBalance->ledgerId,
                        'dairyId'           => $request->dairyId,
                        'transactionId'     => $paymentId,
                        'srcDest'           => $userCurrentBalance->ledgerId,
                        'transactionType'   => 'payment',
                        'amountType'        => 'cash' ,
                        'finalAmount'       => $request->paymentAmount ,
                        'created_at'        => $currentTime,
                      ]);

            }else{
                    /* balance sheet entry */
                    /* user entry in balance sheet */
                    $submiteInfo = DB::table('balance_sheet')->insertGetId([
                        'ledgerId'          => $userCurrentBalance->ledgerId,
                        'dairyId'           => $request->dairyId,
                        'transactionId'     => $paymentId,
                        'srcDest'           => $currentDairyBalance->ledgerId,
                        'transactionType'   => 'payment',
                        'amountType'        => 'cash' ,
                        'finalAmount'       => $request->paymentAmount ,
                        'created_at'        => $currentTime,
                    ]);
                    
                    /* dairy entry in balance sheet */
                    $submiteInfo = DB::table('balance_sheet')->insertGetId([
                        'ledgerId'          => $currentDairyBalance->ledgerId,
                        'dairyId'           => $request->dairyId,
                        'transactionId'     => $paymentId,
                        'srcDest'           => $userCurrentBalance->ledgerId,
                        'transactionType'   => 'payment',
                        'amountType'        => 'cash' ,
                        'finalAmount'       => $request->paymentAmount ,
                        'created_at'        => $currentTime,
                    ]);
            }

        }    

        $returnSuccessArray = array("Success"=>"True","Message"=>"Payment Submited","Payment id"=> $paymentId);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;

    }

}
