<?php

namespace App\Http\Controllers;

use App\expense_setup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ExpenseSetupController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    public function expenseForm(Request $request){

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = DB::table('dairy_info') 
                      ->where('id', $dairyId )
                      // ->where('status', "true")
                      ->whereNotNull('ledgerId')
                      ->get()->first();

        $expenses = DB::table('expenses') 
                      ->where('dairyId', $dairyId )
                      ->where('status', "true" )
                      ->get();

        return view('expenseForm', ['dairyInfo' => $dairyInfo,'expenses' => $expenses]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
         $validatedData = $request->validate([
            'date' => 'required',
            'time' => 'required',
            'expenseType' => 'required',
            'paymentMode' => 'required',
            'amount' => 'required'
        ]);
        
        $submitClass = new expense_setup();
        $submitReturn = $submitClass->expenseFormSubmit($request); 

        return redirect('expenseSetupList');
    }

    public function expenseTypeForm(Request $req){
        return view("expenseTypeSetup");
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createApi(Request $request)
    {
        $submitClass = new expense_setup();
        $submitReturn = $submitClass->expenseFormSubmit($request); 

        return $submitReturn ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function expenseSetupList(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $expenseList = DB::table('expense_setups')
                            ->where('dairyId', $dairyId)
                             ->orderBy('date', 'desc')
                            ->get();

        $dairyInfo = DB::table('dairy_info') 
                      ->where('id', $dairyId)
                      ->get()->first();

        $expenses = DB::table('expenses') 
            ->where('dairyId', $dairyId)
            ->where('status', "true")
            ->get();
            

        return view('expenseSetupList', ['expenseList' => $expenseList, "dairyInfo"=>$dairyInfo, "expenses"=>$expenses, "activepage"=>"expenses"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\expense_setup  $expense_setup
     * @return \Illuminate\Http\Response
     */
    public function show(expense_setup $expense_setup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\expense_setup  $expense_setup
     * @return \Illuminate\Http\Response
     */
    public function edit(expense_setup $expense_setup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\expense_setup  $expense_setup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, expense_setup $expense_setup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\expense_setup  $expense_setup
     * @return \Illuminate\Http\Response
     */
    public function destroy(expense_setup $expense_setup)
    {
        //
    }
}
