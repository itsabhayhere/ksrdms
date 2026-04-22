<?php

namespace App\Http\Controllers;

use App\supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    

     /* get suppler list  */
    public function supplierList(Request $request){
    // session()->get('loginUserInfo')->dairyId
        
        $dairyId = session()->get('loginUserInfo')->dairyId ;
        $supplier = DB::table('suppliers')
            ->where('dairyId', $dairyId )
            ->where('status','true')
            ->get();
        // $returnSuccessJson =  json_encode($supplier);

         $count = 0 ;
        foreach($supplier as $supplierData){
            /* states */
            $getSatatNme =  DB::table('states')
            ->where( 'id', $supplierData->supplierState )
            ->get()->first();

            $supplier[$count]->supplierState = isset($getSatatNme->name)?$getSatatNme->name:'';

            /* city */
            $getCityName =  DB::table('city')
            ->where( 'id', $supplierData->supplierCity )
            ->get()->first();

            $supplier[$count]->supplierCity = isset($getCityName->name)?$getCityName->name:'' ;

            $count++ ;
        }
 

        return view('supplierList', ['supplier' => $supplier, 'activepage'=>"suppliers"]);
    }


    /* Check Supplier Code */
    public function checkSupplierCode(Request $request){
        //supplior_code

        $suppliorCode = DB::table('suppliers')
                                    ->where('supplierCode', $request->supplior_code )
                                    ->get();
       
        if(!(empty($suppliorCode[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    /* Check Supplier Email */
    public function checkSupplierEmail(Request $request){

        $suppliorMobileNumber = DB::table('suppliers')
                                    ->where('supplierMobileNumber', $request->supplierMobileNumber )
                                    ->get();
       
        if(!(empty($suppliorMobileNumber[0]))){
            return "true";
        }else{
            return "false";
        }

    }

    /* return to supplier form with all states */
    public function supplierForm(){
        $states = DB::table('states')->get();
        return view('supplierSetup', ['states' => $states, 'activepage'=>"suppliers"]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $validatedData = $request->validate([
            'supplierCode' => 'required',
            'supplierFirmName' => 'required',
            'supplierPersonName' => 'required',
            'supplierMobileNumber' => 'required',
            'openingBalance' => 'required',
            'openingBalanceType' => 'required',
        ]);

        $submitClass = new supplier();
        $submitReturn = $submitClass->supplierSubmit($request);

        return redirect('supplierList?dairyId='.session()->get('loginUserInfo')->dairyId);
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
     * @param  \App\supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // echo $request->supplierId ;
        $supplior = DB::table('suppliers')
            ->where('id', $request->supplierId )
            ->get();
         $states = DB::table('states')->get();   

        $getCityName =  DB::table('city')
            ->where( 'id', $supplior[0]->supplierCity )
            ->get()->first();
            $supplior[0]->supplierCity = $getCityName->name ;

        return view('supplierEdit', ['supplierEdit' => $supplior[0], 'states' => $states, 'activepage'=>"suppliers"]);
            //supplierEdit
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, supplier $supplier)
    {
        $submitClass = new supplier();
        $submitReturn = $submitClass->editSupplier($request); 
        return redirect('supplierList?dairyId='.session()->get('loginUserInfo')->dairyId);
    }

    /* edit form email validation */
    public function supplierEditEmailValidation(Request $request){
           $suppliorCode = DB::table('suppliers')
                ->where( 'supplierMobileNumber', $request->supplierMobileNumber )
                ->get();
            
        if(!(empty($suppliorCode[0]))){
            if($suppliorCode[0]->id == $request->supplierId ){
                return "false";
            }else{
                return "true";
            }
        }else{
            return "ture";
        }
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
     //return view('supplierList', ['message' => "Supplier Successfully Delated" ]);
            return "Supplier Successfully Delated";
    
    }
}
