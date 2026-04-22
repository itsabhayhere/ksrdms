<?php

namespace App\Http\Controllers;

use App\purchase_setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PurchaseSetupController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
        public function purchaseForm(Request $request){
  
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

        $products = DB::table('products') 
                      ->where('dairyId', $dairyId )
                      ->where('status', "true" )
                      ->get();
        
        // echo "<pre>";
        // print_r($products);
        // die;
          
        $currnetData = array($customers,$milkPlants,$memberPersonalInfo,$supplierInfo,$products);
        return view('purchaseSetup', ['currnetData' => $currnetData]);

        
        // return view('purchaseSetup');
        //purchaseSetup
    }

    /* get supplier name by supplier code  */
    public function getSupplierName(Request $request){
            $supplierCode = DB::table('suppliers')
                    ->where('supplierCode', $request->supplierCode )
                    ->get();
            
                    if(!empty($supplierCode[0]->supplierFirmName)){
                        return $supplierCode[0]->supplierFirmName ;
                    }else{
                        return "";
                    }
    }

    public function create(Request $request)
    {

        $validatedData = $request->validate([
            'ledgerName' => 'required',
            'partyName' => 'required',
            'date' => 'required',
            'time' => 'required',
            'itemsPurchased' => 'required',
            'quantity' => 'required',
            'PricePerUnit' => 'required',
            'amount' => 'required'
        ]);
        

        $submitClass = new purchase_setup();
        $submitReturn = $submitClass->purchaseFormSubmit($request); 

         $purchaseList = DB::table('purchase_setups')
                            ->where('dairyId', $request->dairyId )
                            ->get();
       
        return view('purchaseList', ['purchaseList' => $purchaseList]);
    }


    public function createApi(Request $request)
    {

        $submitClass = new purchase_setup();
        $submitReturn = $submitClass->purchaseFormSubmit($request); 

        return $submitReturn ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function purchaseList(Request $request)
    {

        $dairyId = session()->get('loginUserInfo')->dairyId ;
        $purchaseList = DB::table('purchase_setups')
                            ->where('dairyId', $dairyId )
                            ->get();
       
        return view('purchaseList', ['purchaseList' => $purchaseList]);
    }

     public function purchaseListApi(Request $request)
    {   
       
          $purchaseList = DB::table('purchase_setups')
                            ->where('dairyId', $request->dairyId )
                            ->get();
                            
          return $purchaseList ;
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\purchase_setup  $purchase_setup
     * @return \Illuminate\Http\Response
     */
    public function show(purchase_setup $purchase_setup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\purchase_setup  $purchase_setup
     * @return \Illuminate\Http\Response
     */
    public function edit(purchase_setup $purchase_setup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\purchase_setup  $purchase_setup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, purchase_setup $purchase_setup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\purchase_setup  $purchase_setup
     * @return \Illuminate\Http\Response
     */
    public function destroy(purchase_setup $purchase_setup)
    {
        //
    }
}
