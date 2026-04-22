<?php

namespace App\Http\Controllers;

use App\payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    public function paymentForm(Request $request){
        
        $dairyId = session()->get('loginUserInfo')->dairyId ;
        $customers = DB::table('customer') 
                      ->where('dairyId', $dairyId )
                      ->where('status', "true" )
                      ->whereNotNull('ledgerId')
                      ->get();
      
        $milkPlants = DB::table('milk_plants') 
                      ->where('dairyId', $dairyId )
                      ->where('status', "true" )
                      ->whereNotNull('ledgerId')
                      ->get();

        $memberPersonalInfo = DB::table('member_personal_info') 
                      ->where('dairyId', $dairyId )
                      ->whereNotNull('ledgerId')
                      ->where('status', "true" )
                      ->get();

        $supplierInfo = DB::table('suppliers') 
                      ->where('dairyId', $dairyId )
                      ->whereNotNull('ledgerId')
                      ->where('status', "true" )
                      ->get();
        
        $currnetData = array($customers,$milkPlants,$memberPersonalInfo,$supplierInfo);
        return view('payment', ['currnetData' => $currnetData]);
    }   

    /* function for get payment list */
    public function paymentList(Request $request){
        $dairyId = session()->get('loginUserInfo')->dairyId ;
        $payment = DB::table('payment')
                    ->where('dairyId', $dairyId )
                    ->get();
     
        return view('paymentList', ['payment' => $payment]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       
        $validatedData = $request->validate([
            'ledgerName' => 'required',
            'partyName' => 'required',
            'date' => 'required',
            'time' => 'required',
            'paymentType' => 'required',
            'paymentMode' => 'required',
            'paymentAmount' => 'required',
        ]);
        
        $submitClass = new payment();
        $submitReturn = $submitClass->paymentFormSubmit($request); 

        // return $submitReturn ;
        return redirect('paymentList?dairyId='.session()->get('loginUserInfo')->dairyId);
    }

    /* payment submit api */
    public function Apicreate(Request $request){
      
        $submitClass = new payment();
        $submitReturn = $submitClass->paymentFormSubmit($request); 

        return $submitReturn ;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(payment $payment)
    {
        //
    }
}
