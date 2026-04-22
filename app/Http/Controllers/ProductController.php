<?php

namespace App\Http\Controllers;

use App\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use View;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('Auth');
    }

    /*Product Code Validation  */
    public function productCodeValidation(Request $request)
    {

        $productCode = DB::table('products')
            ->where('productCode', $request->product_code)
            ->get();

        if (!(empty($productCode[0]))) {
            return "true";
        } else {
            return "false";
        }
    }

    public function productSetup()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $suppliers = DB::table("suppliers")
            ->where("dairyId", $dairyId)
            ->where("status", "true")
            ->get();
        if (count($suppliers) == 0) {
            $nosupplier = true;
        } else {
            $nosupplier = false;
        }

        return view('productSetup', ["suppliers" => $suppliers, "nosupplier" => $nosupplier, "activepage" => "ProductForm"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
            /* product code form validation */
             $productCode = DB::table('products')
            ->where('productCode', $request->productCode)
            ->get();

        if (!(empty($productCode[0]))) {
            Session::flash('msg', 'Product Code is already being used.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("productList");
        }

        /* product setup form validation */
        $validatedData = $request->validate([
            'productCode' => 'required',
            'productName' => 'required',
            'productUnit' => 'required',
            'productAmount' => 'required',
            'supplier' => 'required',
            'purchaseAmount' => 'required',
            'paidAmount' => 'required',
        ]);

        /* product setup form submti in model file */
        $submitClass = new product();
        $submitReturn = $submitClass->productSubmit($request);

        return redirect("productList");
    }

    public function apiCreate(Request $request)
    {
        /* product code form validation */
        $productCode = DB::table('products')
            ->where('productCode', $request->productCode)
            ->get();

        if (!(empty($productCode[0]))) {
            $returnSuccessArray = array("Success" => "False", "Message" => "Product Code is already being used.");
            $returnSuccessJson = json_encode($returnSuccessArray);
            return $returnSuccessJson;
        }

        /* product setup form submti in model file */
        $submitClass = new product();
        $submitReturn = $submitClass->productSubmit($request);

        $productsList = DB::table('products')
            ->where('dairyId', $request->dairyId)
            ->where('status', 'true')
            ->get();

        $returnSuccessArray = array("Success" => "True", "Message" => "Product Successfully Register", "Product id" => $submiteInfo);
        $returnSuccessJson = json_encode($returnSuccessArray);
        return $returnSuccessJson;

    }

    /* display product list by dairy id */
    public function show(Request $request)
    {

        $dairyId = session()->get("loginUserInfo")->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        $products = DB::table('products')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->get();
            
       
        foreach ($products as $key => $product) {
            $initial_quantity = DB::table('purchase_setups')
                ->select('quantity')
                ->where('dairyId', $dairyId)
                ->where('productCode', $product->productCode)
                ->where('supplierCode', $product->supplierId)
                ->where('status', 'true')
                ->where('amount', $product->purchaseAmount)
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->first();
            if(!empty($initial_quantity)){
                $product->initial_quantity = $initial_quantity->quantity;
                $product->single_purchase_amount = $product->initial_quantity > 0 ? number_format(($product->purchaseAmount/$product->initial_quantity), 1, ".", "") : 0;
            }
             if($_SERVER['REMOTE_ADDR'] == '171.79.152.250' && $dairyId == '11'){
                // echo $product->productCode;
                // print_r($initial_quantity);
                // echo '<br>';    
            }
        }

        $suppliers = DB::table("suppliers")
            ->where("dairyId", $dairyId)
            ->where("status", "true")
            ->count();
        if ($suppliers == 0) {
            Session::flash('msg', 'There are no supplier registered.');
            Session::flash('alert-class', 'alert-info');
        }

        return view('productList', ['products' => $products, "dairyInfo" => $dairyInfo, "activepage" => "productList"]);
    }

    public function setMilkPrice(Request $request)
    {
        $updateReturn = DB::table('dairy_info')
            ->where('id', session()->get("loginUserInfo")->dairyId)
            ->update([
                "cowMilkPrice" => $request->cowMilkPrice,
                "buffaloMilkPrice" => $request->buffaloMilkPrice,
                'updated_at' => date("Y-m-d H:i:s", time()),
            ]);

        Session::flash('msg', 'Milk Price Updated.');
        Session::flash('alert-class', 'alert-success');
        return redirect("productList");
    }

    public function productStockAdd()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        // return request()->all();

        $pro = DB::table("products")->where(["dairyId" => $dairyId, "id" => request("productid")])->get()->first();

        $suppliers = DB::table("suppliers")
            ->where("dairyId", $dairyId)
            ->where("status", "true")
            ->get();

        if ($pro == (null || false)) {
            Session::flash('msg', 'Product Not Found.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("productList");
        }

        if ($pro->productUnit >= 1) {
            Session::flash('msg', 'Product Stock is not empty.');
            Session::flash('alert-class', 'alert-danger');
            return redirect("productList");
        }

        return view("productStockAdd", ["products" => $pro, "suppliers" => $suppliers]);
    }

    /* display product list by dairy id */
    public function Apishow(Request $request)
    {
        $products = DB::table('products')
            ->where('dairyId', $request->dairyId)
            ->where('status', 'true')
            ->get();

        // return view('productList', ['products' => $products]);
        return $products;
    }

    public function productSupply()
    {
        $dairyInfo = DB::table("dairy_info")->where("id", session()->get("loginUserInfo")->dairyId)->get()->first();
        $products = DB::table('products')
            ->where('dairyId', session()->get("loginUserInfo")->dairyId)
            ->where('status', 'true')
            ->get();

        $purchase = DB::table('purchase_setups')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->orderBy("created_at", "desc")
            ->get();

        $from = DB::table('purchase_setups')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->MIN("date");
        $to = DB::table('purchase_setups')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->MAX("date");

        $supp = DB::table("suppliers")->where('dairyId', session()->get('loginUserInfo')->dairyId)->where('status', 'true')->get();
        return view('productSupply', ['products' => $products, "dairyInfo" => $dairyInfo, "purchase" => $purchase,
            "suppliers" => $supp, "activepage" => "productStock", "from" => date("d-m-Y", strtotime($from)), "to" => date("d-m-Y", strtotime($to))]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $products = DB::table('products')
            ->where('id', $request->productid)
            ->get();
        $suppliers = DB::table("suppliers")
            ->where("dairyId", $dairyId)
            ->where("status", "true")
            ->get();

        return view('productEdit', ['products' => $products[0], "suppliers" => $suppliers, "activepage" => 'productList']);
    }

    public function apiEdit(Request $request)
    {
        $products = DB::table('products')
            ->where('id', $request->productid)
            ->get();

        return view('productEdit', ['products' => $products[0], "activepage" => "productList"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // return $request;
        /* product setup form submti in model file */
        $submitClass = new product();
        $submitReturn = $submitClass->productEditSubmit($request);

        return redirect('productList');
    }

    public function productStockSubmit()
    {
        $validatedData = request()->validate([
            'productId' => 'required',
            'productCode' => 'required',
            'productUnit' => 'required|numeric',
            'productAmount' => 'required|numeric',
            'supplier' => 'required',
            'purchaseAmount' => 'required|numeric',
            'paidAmount' => 'required|numeric',
        ]);

        if (request('paidAmount') > request('purchaseAmount')) {
            Session::flash('msg', 'Amount paid to supplier &#8377; ' . request('paidAmount') . ' can not greater than purchase amount of product &#8377; ' . request('purchaseAmount'));
            Session::flash('alert-class', 'alert-danger');
            return redirect('productStockAdd');
        }

        // return $request;
        /* product setup form submti in model file */
        $submitClass = new product();
        $submitReturn = $submitClass->productStockSubmit();

        return redirect('productList');
    }

    public function apiUpdate(Request $request)
    {
        /* product setup form submti in model file */
        $submitClass = new product();
        $submitReturn = $submitClass->productEditSubmit($request);

        $productsList = DB::table('products')
            ->where('dairyId', $request->dairyId)
            ->where('status', 'true')
            ->get();

        $returnSuccessArray = array("Success" => "True", "Message" => "Product Successfully Edited");
        $returnSuccessJson = json_encode($returnSuccessArray);
        return $returnSuccessJson;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $prod = DB::table('products')
            ->where('id', $request->productId)->get()->first();

        $updateReturn = DB::table('products')
            ->where('id', $request->productId)->delete();

        $updateReturn = DB::table('purchase_setups')
            ->where(["productCode" => $prod->productCode, "dairyId" => $prod->dairyId])->delete();

        return "Product Successfully Delated";
    }

    /* product delete api */
    public function apiDestroy(Request $request)
    {
        $updateReturn = DB::table('products')
            ->where('id', $request->productId)
            ->update([
                'status' => "false",
            ]);

        $returnSuccessArray = array("Success" => "True", "Message" => "Product Successfully Delated");
        $returnSuccessJson = json_encode($returnSuccessArray);
        return $returnSuccessJson;

    }

    public function getPurchaseHistoryByDate()
    {
        if (request('from') == null) {
            $from = date("Y-m-d");
        } else {
            $from = request('from');
        }
        if (request('to') == null) {
            $to = date("Y-m-d");
        } else {
            $to = request('to');
        }

        $q = DB::table('purchase_setups');

        if (request("supplierId") && request("supplierId") != "all") {
            $q = $q->where("supplierCode", request("supplierId"));
        }

        $purchase = $q->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('status', 'true')
            ->whereBetween("date", [date("Y-m-d", strtotime(request("from"))), date("Y-m-d", strtotime(request("to")))])
            ->orderBy("created_at", "desc")
            ->get();

        $cont = View::make('productSupplyFilterModel', ["purchase" => $purchase]);

        return ["cont" => (string) $cont, "from" => date("d-m-Y", strtotime($from)), "to" => date("d-m-Y", strtotime($to))];
    }

}
