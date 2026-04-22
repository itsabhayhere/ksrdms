<?php

namespace App\Http\Controllers;

use App\UtilitySetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UtilitySetupController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    /* Utility List */
    public function utilityList(Request $request){

        $utility = DB::table('utility_setup')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId )   
            ->where('status','true')
            ->get();

        
        foreach ($utility as  $utilityData) {
            
            if(empty($utilityData->weightMode_auto_tare)){
                $utilityData->weightMode_auto_tare = "NULL";
            }
            if(empty($utilityData->weightMode_no_training)){
                $utilityData->weightMode_no_training = "NULL";
            }
            if(empty($utilityData->weightMode_weight_in_doublke_decimal)){
                $utilityData->weightMode_weight_in_doublke_decimal = "NULL";
            }
            if(empty($utilityData->weightMode_write_in)){
                $utilityData->weightMode_write_in = "NULL";
            }

        }
       
        return view('utilityList', ['utility' => $utility]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function utilitySetupForm()
    {
        $t = (object)[
            "dairyId"   => "",
            "status"    => "",
            "machinType"    => "",
            "communicationPort" => "",
            "maxSpeed"  => "",
            "echo"  => "",
            "connectionPerferenceDataBits"  => "",
            "connectionPerferenceParity"    => "",
            "connectionPerferenceStopBits"  => "",
            "flowControl"   => "",
            "weightMode"    => "",
            "weightMode_auto_tare"  => "",
            "weightMode_no_training"    => "",
            "weightMode_weight_in_doublke_decimal"  => "",
            "weightMode_write_in"   => "",
            "isActive"   => "0",
            "created_at"  => "",
            "decimal_digit"=>1,
        ];

        $mUtility = DB::table('utility_setup')
                ->where('dairyId', session()->get('colMan')->dairyId)
                ->where('status','true')
                ->where("machinType", "milk")
                ->get()->first();

        $wUtility = DB::table('utility_setup')
            ->where('dairyId', session()->get('colMan')->dairyId)
            ->where('status','true')
            ->where("machinType", "weight")
            ->get()->first();

        if($mUtility == null) $mUtility = $t;
        if($wUtility == null) $wUtility = $t;


        return view('utilitySetup', ["m" => $mUtility, "w" => $wUtility]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $colMan = Session::get('colMan');

        $validatedData = $request->validate([
                'weightComPort' => 'required',
                'milkComPort'   => 'required',
                'weightMaxSpeed'=> 'required',
                'milkMaxSpeed'  => 'required',
                'mDataBits'     => 'required',
                'mParity'       => 'required',
                'mStopBits'     => 'required',
                'wDataBits'     => 'required',
                'wParity'       => 'required',
                'wStopBits'     => 'required',
                'weightMode'    => 'required',
                //   "decimal_digit"=>"required",
                // 'milkUtilityActive' => 'required',
                // 'weightUtilityActive' => 'required'
            ]);
        

        if(request("milkComPort") == request("weightComPort")){
            Session::flash('msg', "Communication Port can not be same for both machine.");
            Session::flash('alert-class', 'alert-danger');
            return redirect("utilitySetupForm");
        }
 
        $submitClass = new UtilitySetup();
        $submitReturn = $submitClass->utilitySubmit($colMan->dairyId);

        
        Session::flash('msg', $submitReturn["msg"]);
        Session::flash('alert-class', 'alert-success');
        return redirect("dairy-settings");
        // return redirect('/utilitySetup')->with('success', 'Utility Generated'); 
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\UtilitySetup  $utilitySetup
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $utility = DB::table('utility_setup')
                    ->where('id', $request->utilityId )
                    ->get();


        if (!empty($utility[0]->weightMode_two)) {
            $utility[0]->weightMode_two =  explode(",",$utility[0]->weightMode_two);
        }
      
        return view('utilityEdit', ['utility' => $utility[0]]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UtilitySetup  $utilitySetup
     * @return \Illuminate\Http\Response
     */
    public function edit(UtilitySetup $utilitySetup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UtilitySetup  $utilitySetup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if($request->machinType == "Milk Tester"){
           $request->weightMode_auto_tare =   null ;
           $request->weightMode_no_training =  null;
           $request->weightMode_weight_in_doublke_decimal =  null;
           $request->weightMode_write_in =  null;
        }
        

        $submitClass = new UtilitySetup();
        $submitReturn = $submitClass->portEditSubmit($request); 
        return redirect('utilityList?dairyId='.session()->get('loginUserInfo')->dairyId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UtilitySetup  $utilitySetup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       $updateReturn = DB::table('utility_setup')
                        ->where('id', $request->utilityId)
                        ->update([
                            'status' => "false",
                        ]);
        
        $returnSuccessArray = array("Success"=>"True","Message"=>"Utility Successfully Delated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;
    }
}
