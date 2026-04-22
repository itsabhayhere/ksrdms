<?php

namespace App\Http\Controllers;

use App\sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class apiSales extends Controller
{

    /* get milk plant */
    public function getMilkPlant(Request $request){
        $milk_plants = DB::table('milk_plants')
                        ->where('dairyId', $request->dairyId )
                        ->get();
        return $milk_plants ;
    }
    
    /* Sale List */
    public function saleList(Request $request){
      
        $sales = DB::table('sales')
                    ->where('dairyId', $request->dairyId )
                    ->get();

       return $sales ;
      // return view('plantSale', ['sales' => $sales]); 
    }


    public function localSaleFormSubmit(Request $request){

        $validatedData = $request->validate([
            'paymentMode' => 'required',
            'product' => 'required',
            'unit' => 'required',
            'quantity' => 'required',
            'PricePerUnit' => 'required',
            'amount' => 'required',
        ]);
        
        $submitClass = new sales();
        $submitReturn = $submitClass->localSaleFormSubmit($request); 
        return $submitReturn ;

   }

   /* Plant Sale */
    public function milkPlantList(Request $request){

        $milk_plants = DB::table('milk_plants')
                        ->where('dairyId', $request->dairyId )
                        ->get();
       
        return $milk_plants;
        
    }

    public function plantSaleFormSubmit(Request $request){
        $submitClass = new sales();
        $submitReturn = $submitClass->plantSaleFormSubmit($request); 
        return $submitReturn ;
    }

    /* member Sale Form */
public function memberSaleFormSubmit(Request $request){
        $submitClass = new sales();
        $submitReturn = $submitClass->memberSaleFormSubmit($request); 
        return $submitReturn ;
    }

    /* get form pre fill data */
    public function getSalaFormPreFillData(Request $request){

            $customers = DB::table('customer') 
                  ->where('dairyId', $request->dairyId )
                  ->whereNotNull('ledgerId')
                  ->where('status', "true" )
                  ->get();
          

            $milkPlants = DB::table('milk_plants') 
                  ->where('dairyId', $request->dairyId )
                  ->whereNotNull('ledgerId')
                  ->where('status', "true" )
                  ->get();

            $memberPersonalInfo = DB::table('member_personal_info') 
                  ->where('dairyId', $request->dairyId )
                  ->whereNotNull('ledgerId')
                  ->where('status', "true" )
                  ->get();


        $supplierInfo = DB::table('suppliers') 
                      ->where('dairyId', $request->dairyId )
                      ->whereNotNull('ledgerId')
                      ->where('status', "true" )
                      ->get();

            $currnetData = array("customers"=>$customers,"milkPlants" => $milkPlants,"memberPersonalInfo" => $memberPersonalInfo,"supplierInfo"=>$supplierInfo);
           

          return $currnetData ;
    }
}
