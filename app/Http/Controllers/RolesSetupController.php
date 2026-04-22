<?php

namespace App\Http\Controllers;

use App\rolesSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class RolesSetupController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    
    /* role setup form */
    public function roleSetupForm(Request $request){
            return view('roleSetup');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'roleName' => 'required',
        ]);

        $submitClass = new rolesSetup();
        $submitReturn = $submitClass->roleSetupFormSubmit($request); 
        return view('roleSetup', ['message' => "Role succfully created"]);
    }

    public function roleList(Request $request){
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\rolesSetup  $rolesSetup
     * @return \Illuminate\Http\Response
     */
    public function show(rolesSetup $rolesSetup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\rolesSetup  $rolesSetup
     * @return \Illuminate\Http\Response
     */
    public function edit(rolesSetup $rolesSetup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\rolesSetup  $rolesSetup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, rolesSetup $rolesSetup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\rolesSetup  $rolesSetup
     * @return \Illuminate\Http\Response
     */
    public function destroy(rolesSetup $rolesSetup)
    {
        //
    }
}
