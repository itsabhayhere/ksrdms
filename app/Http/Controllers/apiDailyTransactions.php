<?php

namespace App\Http\Controllers;

use App\dailyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class apiDailyTransactions extends Controller
{


    /* Function for Daily Transaction List */
    public function DailyTransactionList(Request $request){
       
        $dailyTransactions = DB::table('daily_transactions')
                            ->where('dairyId', $request->dairyId )
                            ->get();
        
        return $dailyTransactions ;
      
    }


    /* get member name by member code */
    public function DailyTransactionMemberCode(Request $request){
        // return $request->member_code ;
        $memberInfo = DB::table('member_personal_info')
                            ->where('memberPersonalCode', $request->member_code )
                            ->get();

        
        if(!(empty($memberInfo[0]))){
            $returnArray = array("success"=>"true","memberName" => $memberInfo[0]->memberPersonalName );
            return $returnArray ;
        }else{
            $returnArray = array("success"=>"false","message" => "This Member Code is not valid." );
            return $returnArray ;
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $submitClass = new dailyTransaction();
        $submitReturn = $submitClass->DailyTransactionSubmit($request); 
        
        return  $submitReturn ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\dailyTransaction  $dailyTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(dailyTransaction $dailyTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\dailyTransaction  $dailyTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(dailyTransaction $dailyTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\dailyTransaction  $dailyTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, dailyTransaction $dailyTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\dailyTransaction  $dailyTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(dailyTransaction $dailyTransaction)
    {
        //
    }
}
