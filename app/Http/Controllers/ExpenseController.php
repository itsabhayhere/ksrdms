<?php

namespace App\Http\Controllers;

use App\expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ExpenseController extends Controller
{

    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    /*Product Code Validation  */
    public function expenseCodeValidation(Request $request){
       
        $expenseCode = DB::table('expenses')
                        ->where('expenseHeadCode', $request->expense_code )
                        ->get();
       
        if(!(empty($expenseCode[0]))){
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
        /* product setup form validation */
        $validatedData = $request->validate([
            'expenseCode' => 'required',
            'expenseName' => 'required',
            'expenseDesc' => 'required',
        ]);

        $expense_head_code = DB::table('expenses')
                        ->where(['expenseHeadCode' => $request->expenseCode, "dairyId" => session()->get("loginUserInfo")->dairyId])
                        ->get();
       
        if(!(empty($expense_head_code[0]))){
            Session::flash('msg', 'There is a problem, Error Expense head is already in records.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("expenseList");
        }

        /* product setup form submit in model file */
        $submitClass = new expense();
        $submitReturn = $submitClass->expenseSubmit($request);

        if($submitReturn){
            Session::flash('msg', 'Expense head created.');
            Session::flash('alert-class', 'alert-success');
            return redirect("expenseList");
        }else{
            Session::flash('msg', 'An error has occured while creating expense head.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("expenseList");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function apiCreate(Request $request)
    {
          /* product code form validation */

        $expense_head_code = DB::table('expenses')
                        ->where('expenseHeadCode', $request->expenseHeadCode )
                        ->get();
       
        if(!(empty($expense_head_code[0]))){
            $returnArray = array("Success"=>"False","Message"=>"Expense Code is already being used.");
            $returnJson =  json_encode($returnArray);
            return $returnJson  ; 
        }


        /* product setup form submit in model file */
        $submitClass = new expense();
        $submitReturn = $submitClass->expenseSubmit($request); 

        // $returnSuccessArray = array("Success"=>"True","Message"=>"Expense Successfully Added","id"=> $submitReturn);
        // $returnSuccessJson =  json_encode($returnSuccessArray);
        return $submitReturn  ; 
    }

    


    /* display product list by dairy id */
    public function show(Request $request)
    {

        $expenses = DB::table('expenses')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId )   
            ->where('status','true')
            ->get();
      
        return view('expenseList', ['expenses' => $expenses]);
         
    }


    /* display product list by dairy id */
    public function Apishow(Request $request)
    {

        $expenses = DB::table('expenses')
            ->where('dairyId', $request->dairyId )   
            ->where('status','true')
            ->get();
        return $expenses ;
         
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
       $expenses = DB::table('expenses')
        ->where('id', $request->expenseId )   
        ->get();

       return view('expenseEdit', ['expenses' =>  $expenses[0] ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $submitClass = new expense();
        $submitReturn = $submitClass->expenseEditSubmit($request); 
       return redirect('expenseList?dairyId='.session()->get('loginUserInfo')->dairyId);
    }

    public function apiUpdate(Request $request){

        $submitClass = new expense();
        $submitReturn = $submitClass->expenseEditSubmit($request); 
        // return redirect('expenseList?dairyId='.session()->get('loginUserInfo')->dairyId);
        return $submitReturn ;
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $updateReturn = DB::table('expenses')
            ->where('id', $request->expenseId)
            ->update([
                'status' => "false",
            ]);
        return "Expense Successfully Delated";
    }
}
