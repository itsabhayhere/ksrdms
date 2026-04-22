<?php

namespace App\Http\Controllers;

use App\ApiMilkPlant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ApiMilkPlantController extends Controller
{


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create(Request $request)
    {
       
        $email = DB::table('milk_plants')
                            ->where('mobile', $request->mobileNumber )
                            ->get();

        if(!(empty($email[0]))){
            $returnSuccessArray = array("Success"=>"False","Message"=>"This mobile number is already being used.");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson  ; 
        }


         $contactNumberInfo = DB::table('milk_plants')
                            ->where('contactNumber', $request->contactNumber )
                            ->get();

        if(!(empty($contactNumberInfo[0]))){
            $returnSuccessArray = array("Success"=>"False","Message"=>"This contact number is already being used.");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson  ; 
        }
        
        $submitClass = new ApiMilkPlant();
        $submitReturn = $submitClass->milkPlantSubmit($request); 

        return $submitReturn ;
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

    /** show milk plant list */
    public function show(Request $request)
    {
        $milkPlant = DB::table('milk_plants')
                      ->where('dairyId', $request->dairyId )   
                      ->where('status','true')
                      ->get();

        $returnSuccessJson =  json_encode($milkPlant);
        return $returnSuccessJson ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ApiMilkPlant  $apiMilkPlant
     * @return \Illuminate\Http\Response
     */
    public function edit(ApiMilkPlant $apiMilkPlant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ApiMilkPlant  $apiMilkPlant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
   
        $email = DB::table('milk_plants')
                            ->where('mobile', $request->mobileNumber )
                            ->get();

        if(!(empty($email[0]))){
            $returnSuccessArray = array("Success"=>"False","Message"=>"This mobile number is already being used.");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson  ; 
        }


         $contactNumberInfo = DB::table('milk_plants')
                            ->where('contactNumber', $request->contactNumber )
                            ->get();

        if(!(empty($contactNumberInfo[0]))){
            $returnSuccessArray = array("Success"=>"False","Message"=>"This contact number is already being used.");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson  ; 
        }
        
       

        $submitClass = new ApiMilkPlant();
        $submitReturn = $submitClass->editMilkPlant($request); 
        return $submitReturn ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ApiMilkPlant  $apiMilkPlant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $updateReturn = DB::table('milk_plants')
                        ->where('id', $request->milkPlantId)
                        ->update([
                            'status' => "false",
                        ]);
        $returnSuccessArray = array("Success"=>"True","Message"=>"Milk Plant Successfully Delated");
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson ;
    }
}
