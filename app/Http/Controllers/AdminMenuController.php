<?php

namespace App\Http\Controllers;

use App\adminMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMenuController extends Controller{

    
    public function __construct()
    {
        $this->middleware('Auth');
    }

    public function adminMenuForm(){
         $loginUserInfo = session()->get('loginUserInfo');
        
         // return view('theme.sidebar'
        $adminMenus = DB::table('admin_menus')->get();
          
        return view('menuSetup', ['adminMenus' => $adminMenus]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //menuSetup
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'menuTitle' => 'required',
            'menuUrl' => 'required',
        ]);

        $submitClass = new adminMenu();
        $submitReturn = $submitClass->adminMenuSubmit($request); 
        $adminMenus = DB::table('admin_menus')->get();
        return view('menuSetup', ['message' => "Menu succfully created",'adminMenus' => $adminMenus]);
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
     * @param  \App\adminMenu  $adminMenu
     * @return \Illuminate\Http\Response
     */
    public function show(adminMenu $adminMenu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\adminMenu  $adminMenu
     * @return \Illuminate\Http\Response
     */
    public function edit(adminMenu $adminMenu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\adminMenu  $adminMenu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, adminMenu $adminMenu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\adminMenu  $adminMenu
     * @return \Illuminate\Http\Response
     */
    public function destroy(adminMenu $adminMenu)
    {
        //
    }
}
