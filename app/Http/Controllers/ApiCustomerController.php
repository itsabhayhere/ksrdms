<?php

namespace App\Http\Controllers;

use App\ApiCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ApiCustomerController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        $mobileNumber = DB::table('customer')
                        ->where('customerMobileNumber', $request->customerMobileNumber )
                        ->get();

        if(!(empty($mobileNumber[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This number is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
        }
       
        $customerCode = DB::table('customer')
                            ->where('customerCode', $request->customerCode )
                            ->get();
     
        
         if(!(empty($customerCode[0]))){
            $retrunArray = array("Success"=>"False","message"=>"This code is already being used");
            $returnJson = json_encode($retrunArray);
            return $returnJson  ;
            
        }

        
        $submitClass = new ApiCustomer();
        $submitReturn = $submitClass->customerSubmit($request); 
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
        
        $customerMobileNumber = DB::table('customer')
                                 ->where('customerMobileNumber', $request->customerMobileNumber )
                                ->get();


       
       if(!(empty($customerMobileNumber[0]))){
            // return "true";
            if($customerEmail[0]->id != $request->customerId ){
                $retrunArray = array("Success"=>"False","message"=>"This number is already being used");
                $returnJson = json_encode($retrunArray);
                return $returnJson  ;
                die;  
            }
        } 

        $submitClass = new ApiCustomer();
        $submitReturn = $submitClass->editCustomer($request); 
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
       $updateReturn = DB::table('customer')
            ->where('id', $request->customerId)
            ->update([
                'status' => "false",
            ]);
        $returnSuccessArray = array("Success"=>"True","Message"=>"Customer Successfully Delated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;
    }

    /* customer list */
    public function customerList(Request $request){
        $allCoustomer = DB::table('customer')
            ->where( 'dairyId', $request->dairyId )
             ->where( 'status', 'true' )
            ->get();

        return $allCoustomer ;
    }   
}
