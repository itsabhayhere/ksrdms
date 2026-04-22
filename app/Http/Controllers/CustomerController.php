<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CustomerController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    /* Check Customer Code */
    public function checkCustomerCode(Request $request){
        //supplior_code

        $customerCode = DB::table('customer')
                            ->where('customerCode', $request->customer_code)
                            ->where('status', "true")
                            ->get()->first();

        if(!(empty($customerCode))){
            return "true";
        }else{
            return "false";
        }
    }

    /* Check Customer Email */
    public function CheckcustomerEmail(Request $request){

        $customerEmail = DB::table('customer')
                            ->where('customerMobileNumber', $request->mobileNumber )
                            ->get();
       
        if(!(empty($customerEmail[0]))){
            return "true";
        }else{
            return "false";
        }

    }

    /* return to Customer form with all states */
    public function customerForm(){
        $states = DB::table('states')->get();
        return view('customerSetup', ['states' => $states, 'activepage'=>"customers"]);
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
            'customerCode' => 'required',
            'customerName' => 'required',
            'customerMobileNumber' => 'required',   
            'customerOpeningBalance' => 'required',
            'openingBalanceType' => 'required',
        ]);
        
        $submitClass = new Customer();
        $submitReturn = $submitClass->CustomerSubmit($request); 

        return redirect('customerList?dairyId='.session()->get('loginUserInfo')->dairyId);
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
     * @param  \App\Customer  $Customer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // echo $request->CustomerId ;
        $customer = DB::table('customer')
            ->where('id', $request->CustomerId )
            ->get();
         $states = DB::table('states')->get();    

           /* city */
        $getCityName =  DB::table('city')
            ->where( 'id', $customer[0]->customerCity )
            ->get()->first();
        if($getCityName){
            $customer[0]->customerCity = $getCityName->name;
        }else{            
            $customer[0]->customerCity = "";
        }
        $customer[0]->city = $customer[0]->customerCity;

        return view('customerEdit', ['CustomerEdit' => $customer[0], 'states' => $states, 'activepage'=>"customers"]);
            //CustomerEdit
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $Customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $Customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $Customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $Customer)
    {
        $submitClass = new Customer();
        $submitReturn = $submitClass->editCustomer($request); 
        return redirect('customerList?dairyId='.session()->get('loginUserInfo')->dairyId);
    }

    /* edit form email validation */
    public function CustomerEditEmailValidation(Request $request){
           $customerCode = DB::table('customer')
                ->where( 'customerMobileNumber', $request->mobileNumber )
                ->get();
                
            if(!(empty($customerCode[0]))){
        		if($customerCode[0]->id == $request->CustomerId ){
	                return "false";
	            }else{
	                return "true";
	            }
        	}else{
        	    return "false";
        	}
    }

	/* customer list */
	public function customerList(Request $request){

		$allCoustomer = DB::table('customer')
            ->where( 'dairyId', session()->get('loginUserInfo')->dairyId )
            ->where( 'status', 'true' )
            ->get();

        $count = 0 ;
        foreach($allCoustomer as $allCoustomerData){
            /* states */
            $getSatatNme =  DB::table('states')
            ->where( 'id', $allCoustomerData->customerState )
            ->get()->first();
            $allCoustomer[$count]->customerState = isset($getSatatNme->name)?$getSatatNme->name:"";

            /* city */
            $getCityName =  DB::table('city')
            ->where( 'id', $allCoustomerData->customerCity )
            ->get()->first();
            $allCoustomer[$count]->customerCity = isset($getCityName->name)?$getCityName->name:"" ;

            $count++ ;
        }
       
        return view('customerList', ['allCoustomer' => $allCoustomer, 'activepage'=>"customers"]);
	}	

    public function destroy(Request $request)
    {
        $updateReturn = DB::table('customer')
            ->where('id', $request->customerId)
            ->update([
                'status' => "false",
            ]);
        return "Customer Successfully Delated";
       
    }
}
