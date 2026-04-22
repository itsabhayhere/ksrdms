<?php

namespace App\Http\Controllers;

use App\otherUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OtherUserController extends Controller
{   

    public function __construct()
    {
        $this->middleware('Auth');
    }
    

    /* other Use rList */
    public function otherUserList(Request $request){

        $dairyId = session()->get('loginUserInfo')->dairyId ;

        $otherUsers = DB::table('other_users')
            ->where('dairyId', $dairyId )   
            ->where('status','true')
            ->get();
        // $returnSuccessJson =  json_encode($supplier);

         $count = 0 ;
        foreach($otherUsers as $otherUsersData){
            /* states */
            $getSatatNme =  DB::table('states')
            ->where( 'id', $otherUsersData->state )
            ->get()->first();
            $otherUsers[$count]->state = $getSatatNme->name ;

            /* city */
            $getCityName =  DB::table('city')
            ->where( 'id', $otherUsersData->city )
            ->get()->first();
            //  $otherUsers[$count]->city = $getCityName->name ;

            $count++ ;
        }

 
        return view('otherUserList', ['otherUsers' => $otherUsers]);
    }

    /* Other User Form */
    public function otherUsuerForm(Request $request){
        $dairyId = session()->get('loginUserInfo')->dairyId ;
        $states = DB::table('states')->get();
        $role = DB::table('roles_setups')
                            ->where('dairyId', $dairyId )
                            ->get();


        return view('OtherUser', ['states' => $states,'role' => $role]);
    }

    /* Check user email */
    public function checkOtherUserEmail(Request $request){

          $suppliorCode = DB::table('other_users')
                            ->where('mobileNumber', $request->otherUserNumber )
                            ->get();
       
        if(!(empty($suppliorCode[0]))){
            return "true";
        }else{
            return "false";
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      
        $validatedData = $request->validate([
            'otherUserName' => 'required',
            'otherUserFatherName' => 'required',
            'otherUserAadharNumber' => 'required',
            'otherUserEmail' => 'required',
            'gender' => 'required',
            'otherUserMobileNumber' => 'required',
            'otherUserAddress' => 'required',
            'otherUserState' => 'required',
            'otherUserCity' => 'required',
            'otherUserVillageDistrict' => 'required',
            'otherUserPincode' => 'required',
            // 'openingBalance' => 'required',
            // 'openingBalanceType' => 'required',
        ]);
       if(!empty($request->selectedMenu)){
            $request->selectedMenu = implode(",",$request->selectedMenu);
       }
     
        $submitClass = new otherUser();
        $submitReturn = $submitClass->otherUserSubmit($request); 

        return redirect('otherUserList?dairyId='.session()->get('loginUserInfo')->dairyId);
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
     * @param  \App\otherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
         // echo $request->supplierId ;
        $otherUsers = DB::table('other_users')
            ->where('id', $request->otherUserId )
            ->get();

        $states = DB::table('states')->get();   

        $getCityName =  DB::table('city')
            ->where( 'id', $otherUsers[0]->city )
            ->get()->first();
            $otherUsers[0]->city = $getCityName->name ;

        $dairyId = session()->get('loginUserInfo')->dairyId ;
        $role = DB::table('roles_setups')
                            ->where('dairyId', $dairyId )
                            ->get();

        $otherUserData = array($otherUsers[0],$role);

        /*echo "<pre>";
        print_r($otherUserData);
        die;*/

        return view('otherUserEdit', ['otherUserData' => $otherUserData], ['states' => $states]);
    }

    /* Other User Edit Submit */
    public function otherUserEditSubmit(Request $request){
        
        $submitClass = new otherUser();
        $submitReturn = $submitClass->editOtherUser($request); 
        return redirect('otherUserList?dairyId='.session()->get('loginUserInfo')->dairyId);

    }


        /* edit form email validation */
    public function UserEditEmailValidation(Request $request){
           $suppliorCode = DB::table('other_users')
                            ->where('mobileNumber', $request->otherUserNumber )
                            ->get();
            
        if(!(empty($suppliorCode[0]))){
            if($suppliorCode[0]->id == $request->otherUserId ){
                return "false";
            }else{
                return "true";
            }
        }else{
            return "ture";
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\otherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function edit(otherUser $otherUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\otherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, otherUser $otherUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\otherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(otherUser $otherUser)
    {
         $updateReturn = DB::table('other_users')
            ->where('id', $request->otherUserId)
            ->update([
                'status' => "false",
            ]);
         return redirect('otherUserList?dairyId='.session()->get('loginUserInfo')->dairyId);

    }
}
