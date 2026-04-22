<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use View;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('Auth');
    }


    public function categorySetup()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        return view('categorySetup', ["activepage" => "CategoryForm"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        /* category code form validation */
        $categoryCode = DB::table('categories')
            ->where("dairyId", $request->dairyId)
            ->where('name', $request->categoryName)
            ->get();

        if (!(empty($categoryCode[0]))) {
            Session::flash('msg', 'Category Name is already being used.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("categoryList");
        }

        /* category setup form validation */
        $validatedData = $request->validate([
            'categoryName' => 'required',
            'categoryPrice' => 'required',
        ]);

        /* category setup form submti in model file */
        $submitClass = new Category();
        $submitReturn = $submitClass->categorySubmit($request);

        return redirect("categoryList");
    }

    /* display category list by dairy id */
    public function show(Request $request)
    {

        $dairyId = session()->get("loginUserInfo")->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        $categorys = DB::table('categories')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->get();
            
        return view('categoryList', ['categorys' => $categorys, "dairyInfo" => $dairyInfo, "activepage" => "categoryList"]);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $categorys = DB::table('categories')
            ->where('id', $request->categoryid)
            ->get();
        return view('categoryEdit', ['categorys' => $categorys[0], "activepage" => 'categoryList']);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return $request;
        /* category setup form submti in model file */
        $submitClass = new Category();
        $submitReturn = $submitClass->categoryEditSubmit($request);

        return redirect('categoryList');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $updateReturn = DB::table('categories')
            ->where('id', $request->categoryId)->delete();

        return "Category Successfully Delated";
    }

}
