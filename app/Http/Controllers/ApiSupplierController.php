<?php

namespace App\Http\Controllers;

use App\ApiSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ApiSupplierController extends Controller
{
  

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
       
        $suppliorMobileNumber = DB::table('suppliers')
                                    ->where('supplierMobileNumber', $request->supplierMobileNumber )
                                    ->get();
       

         if(!(empty($suppliorMobileNumber[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This number is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
        }
       
        $suppliorCode = DB::table('suppliers')
                                    ->where('supplierCode', $request->supplierCode )
                                    ->get();
     
        
         if(!(empty($suppliorCode[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This code is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            
        }

        
        $submitClass = new ApiSupplier();
        $submitReturn = $submitClass->supplierSubmit($request); 
        return $submitReturn ;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ApiSupplier  $apiSupplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $suppliorMobileNumber = DB::table('suppliers')
                                ->where('supplierMobileNumber', $request->supplierMobileNumber )
                                ->get();

        if(!(empty($suppliorMobileNumber[0]))){
            $retrunArray = array("Success"=>"False","Message"=>"This number is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
        }

        $submitClass = new ApiSupplier();
        $submitReturn = $submitClass->editSupplier($request); 
        return $submitReturn ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ApiSupplier  $apiSupplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       $updateReturn = DB::table('suppliers')
            ->where('id', $request->supplierId)
            ->update([
                'status' => "false",
            ]);
        $returnSuccessArray = array("Success"=>"True","Message"=>"Supplier Successfully Delated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;
    }

    /* get suppler list  */
    public function supplierList(Request $request){
          $supplier = DB::table('suppliers')
            ->where('dairyId', $request->dairyId )   
            ->where('status','true')
            ->get();
         $returnSuccessJson =  json_encode($supplier);
        return $returnSuccessJson ;
        //orWhere
    }


}
