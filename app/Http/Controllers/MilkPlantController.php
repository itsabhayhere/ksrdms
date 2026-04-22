<?php


namespace App\Http\Controllers;

use App\milkPlant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MilkPlantController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
     /* return to supplier form with all states */
    public function milkPlantForm(){
        $states = DB::table('states')->get();
        return view('milkPlantSetup', ['states' => $states, "activepage"=>"milkPlants"]);
    }

    /* check milk plant email  */
    public function checkMilkPlantEmail(Request $request){

        $email = DB::table('milk_plants')
                            ->where('mobile', $request->mobileNumber )
                            ->get();
       
        if(!(empty($email[0]))){
            return "true";
        }else{
            return "false";
        }

    }

    /* milk plant contact number validation */
    public function checkMilkPlantContactNumberValidation(Request $request)
    {
        $email = DB::table('milk_plants')
                            ->where('contactNumber', $request->contactNumber )
                            ->get();
       
        if(!(empty($email[0]))){
            return "true";
        }else{
            return "false";
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
    }

    /** show milk plant list */
    public function show(Request $request)
    {
        if(request("filter") == null){
            $filter = "my";
        }else{
            $filter = request("filter");
        }

        $q = DB::table('plantdairymap');
        // if($filter == "my"){
        //     $q->where(["plantdairymap.isActivated" => 1]);
        // }
        // if($filter == "requested"){
        //     $q->where(["plantdairymap.isActivated" => 0]);
        // }

        $dairy = DB::table('dairy_info')->where(["id" => session()->get('loginUserInfo')->dairyId])->get()->first();

        $milkPlant = $q->where(['plantdairymap.dairyId' => session()->get('loginUserInfo')->dairyId])
                      ->leftjoin("milk_plants", "plantdairymap.plantId", "=", "milk_plants.id")
                      ->leftjoin("milk_plant_head", "milk_plants.id", "=", "milk_plant_head.plantId")
                      ->get();

        $mainPlants = DB::table('milk_plants')->where(["milk_plants.status" => "true", "milk_plants.isMainPlant" => 1])
                    ->get();

        return view('milkPlantList', ['milkPlant' => $milkPlant, "mainPlants" => $mainPlants, 
                                    "allPlants" => [], "filter" => $filter, "activepage"=>"milkPlants"]);
        //milkPlantList
    }

    public function getChildMilkPlants()
    {
        $plants = DB::table('milk_plants')->where(["milk_plants.status" => "true", "milk_plants.parentPlantId" => request('milkPlantId')])
                        ->select("*", "milk_plants.id as id", "city.name as city", "states.name as state")
                        ->leftjoin("states", "states.id", "=", "milk_plants.state")
                        ->leftjoin("city", "city.id", "=", "milk_plants.city")
                        ->get();

        return ["error" => false, "plants" => $plants];
    }

    public function milkPlantAddRequest()
    {
        $plantModel = new milkPlant();
        
        $r = $plantModel->milkPlantAddRequest();
        if($r){
            return redirect('milkPlantList');
        }else{
            return redirect('milkPlantList')->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\milkPlant  $milkPlant
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
          $milkPlants = DB::table('milk_plants')
            ->where('id', $request->milkPlantId )
            ->get();
         $states = DB::table('states')->get();    

           /* city */
            $getCityName =  DB::table('city')
            ->where( 'id', $milkPlants[0]->city )
            ->get()->first();
            $milkPlants[0]->city = $getCityName->name ;

         //city
        return view('milkPlantEdit', ['milkPlant' => $milkPlants[0]], ['states' => $states]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\milkPlant  $milkPlant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {   

        $submitClass = new milkPlant();
        $submitReturn = $submitClass->editMilkPlant($request); 
       return redirect('milkPlantList?dairyId='.session()->get('loginUserInfo')->dairyId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\milkPlant  $milkPlant
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $updateReturn = DB::table('plantdairymap')
                            ->where(['dairyId'=> $dairyId, 'plantId' => request('milkPlantId')])
                            ->update(['status' => "false"]);
        return  ["error" => false, "msg" => "Milk Plant Successfully Delated"];
    }

    /* milk Plant Edit Email validation */
    public function milkPlantEditEmail(Request $request){

        $milkPlant = DB::table('milk_plants')
            ->where( 'mobile', $request->mobileNumber )
            ->get();

        if(!(empty($milkPlant[0]))){
            if($milkPlant[0]->id == $request->milkPlantId ){
                return "false";
            }else{
                return "true";
            }
        }else{
            return "false";
        }
    }

    /* contact number validation in edit */

    public function milkPlantEditContactNumber(Request $request)
    {
         $milkPlant = DB::table('milk_plants')
            ->where( 'contactNumber', $request->contactNumber )
            ->get();

        if(!(empty($milkPlant[0]))){
            if($milkPlant[0]->id == $request->milkPlantId ){
                return "false";
            }else{
                return "true";
            }
        }else{
            return "false";
        }
    }

    public function plantAddRequestNotification($dairy, $pl)
    {
        $msg = "You have new request from Dairy. Dairy want to add your plant."
        ."<br/>
        <div class='fl'>
            <span class='light'><a href='".url('plant/requestToAdd')."'>click to see</a></span>
        </div>";

        $data = [
            "ledgerId"      => $pl->ledgerId,
            "notification"  => $msg,
            "created_at"    => date("Y-m-d H:i:s")
        ];

        $res = DB::table('notifications')->insertGetId($data);
        if($res){
            return true;
        }else{
            return false;
        }
    }
}
