<?php

namespace App\Http\Controllers;

use App\sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PushNotification;
use function GuzzleHttp\json_encode;

class SalesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    
    // Request $request


   /* Sale List */
    public function saleList(Request $request){
        $sales = DB::table('sales')
                        ->where(['dairyId' => session()->get('loginUserInfo')->dairyId, "status" => "true"])
                        ->get();

        return view('saleList', ['sales' => $sales, "activepage"=>"localSale"]);
    }

   /* local Sale form */
   public function SaleForm(Request $request){
        if($request->date!=(null||"")){
            $date=date("Y-m-d", strtotime($request->date));
        }else{$date=date("Y-m-d");}
        if($request->party!=(null||"")){
            $party=$request->party;
        }else{$party="customer";}

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $customers = DB::table('customer')
            ->where('dairyId', $dairyId)
            ->where('status', "true")
            ->get();


        $memberPersonalInfo = DB::table('member_personal_info') 
            ->where('dairyId', $dairyId)
            ->where('status', "true")
            ->get();

        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $msc = DB::table("daily_transactions")->where("dairyId", $dairyId)
                                                                    ->where("date", date("Y-m-d"))->where("shift", "morning")
                                                                    ->sum("milkQuality");

        $esc = DB::table("daily_transactions")->where("dairyId", $dairyId)
                                                                    ->where("date", date("Y-m-d"))->where("shift", "evening")
                                                                    ->sum("milkQuality");
        $tsale = DB::table("sales")->where("dairyId", $dairyId)->where("saleType", "local_sale")
                                    ->where("saleDate", date("Y-m-d"))->sum('productQuantity');

        $currnetData = array($customers, $memberPersonalInfo);
        $noCustomer = false;
        if(count($customers) <= 1){
            $noCustomer = true;
        }

        return view('localSale', ['currnetData' => $currnetData, "dairyInfo"=>$dairyInfo, "msc"=> number_format($msc, 2, ".", ""), "esc"=>number_format($esc, 2, ".", ""), 
                                  "tsale"=>$tsale, "date"=>$date, "party"=>$party, "activepage"=>"localSale", "noCustomer" => $noCustomer]);
    }

    public function getLocalSaleAjax(Request $req){
        $sales = DB::table('sales')
            ->where(['dairyId' => session()->get('loginUserInfo')->dairyId,
                        'saleType' => "local_sale", 
                        'status' => "true"])
            ->orderby("created_at", "DESC")
            ->get();
            $data= [];
            $i=0;
        foreach($sales as $s){
            $d = [ 
                $i,
                $s->partyCode,
                $s->partyName,
				$s->milkType,
				date("d-m-Y", strtotime($s->saleDate)),
                $s->productPricePerUnit,
                $s->productQuantity,
                "&#8377; ".$s->amount,
                $s->discount,
                $s->finalAmount,
                $s->paidAmount,
                ucfirst($s->amountType),
                "<a href='javascript:void(0);' onclick='editSale(".$s->id.")' ><i class='fa fa-edit'></i></a>"
            ];
            $data[] = $d;
            $i++;
        }
        return ["data"=>$data];
    }

    public function getProductSaleAjax(Request $req){
        $sales = DB::table('sales')
            ->select("sales.id as id", "sales.partyCode as partyCode", "sales.partyName as partyName", "sales.saleDate as saleDate", "sales.productType",
                     "sales.productPricePerUnit as productPricePerUnit",
                     "sales.purchaseAmount as purchaseAmountS", "sales.productQuantity as productQuantity", "sales.amount as amount",
                     "products.purchaseamount as purchaseamount",
                     "sales.discount as discount", "sales.paidAmount as paidAmount", "sales.finalAmount as finalAmount", "sales.amountType as amountType",
                     "products.productName as productName")
            ->where(['sales.dairyId' => session()->get('loginUserInfo')->dairyId,
                    'sales.saleType' => "product_sale", "sales.status" => "true"])
            ->leftjoin("products", "products.productCode", "=", "sales.productType")
            ->orderby("sales.created_at", "DESC")
            ->get();
            
            $data= [];
            $i=0;
        foreach($sales as $s){
            // $p = DB::table("products")->select("productName")->where("productCode", $s->productType)->get()->first();
            // if($p!=(null||false||"")) $pt = $p->productName; else $pt = $s->productType;
            $d = [
                $i,
                $s->partyCode,
                $s->partyName,
                date("d-m-Y", strtotime($s->saleDate)),
                ($s->productName)?$s->productName:$s->productType,
                $s->productPricePerUnit,
                $s->productQuantity,
                "&#8377; ".$s->amount,
                "&#8377; ".$s->purchaseAmountS, //$s->purchaseamount,
                $s->discount,
                "&#8377; ".$s->paidAmount,
                "&#8377; ".number_format($s->finalAmount, 2),
				ucfirst($s->amountType),
                "<a href='javascript:void(0);' onclick='editSale(".$s->id.")' ><i class='fa fa-edit'></i></a>"
            ];
            $data[] = $d;
            $i++;
        }
        return ["data"=>$data];
    }

    public function getPlantSaleAjax(Request $req){
        
        if($req->plant==null){
            $sales = DB::table('sales')
                ->where('dairyId', session()->get('loginUserInfo')->dairyId)
                ->where('saleType', "plant_sale")
                ->orderBy('saleFromDate', "desc")
                ->get();
        }else{
            $sales = DB::table('sales')
                ->where('dairyId', session()->get('loginUserInfo')->dairyId)
                ->where('partyType', "plant")
                ->where('partyCode', $req->plant)
                ->orderBy('saleFromDate', "desc")
                ->get();
        }
        
        $data = [];

        foreach($sales as $s){
            $d = [
                $s->partyName,
                ucfirst($s->productType),
				date("d-m-Y", strtotime($s->saleFromDate)),
				date("d-m-Y", strtotime($s->saleDate)),
				$s->productQuantity,
                "&#8377; ".$s->amount,
                "&#8377; ".$s->paidAmount,
                $s->amountType
            ];
            $data[] = $d;
        }
        return ["data"=>$data];
    }

   public function getProductUnit(Request $req){
    //    return $req->dairyId;

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $product = DB::table('products')
            ->where('dairyId', $req->dairyId)
            ->where('productCode', $req->productCode)
            ->where('status', "true" )
            ->get()->first();
        return ["unit" => $product->productUnit, "rate"=>$product->amount, "stock"=> $product->productUnit];
    }

   public function localSaleFormSubmit(Request $req){
        // return $req->all();

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairy = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        $appSettings = DB::table('androidappsetting')->get()->first();

        $validatedData = $req->validate([
            'partyName' => 'required',
            'partyType' => 'required',
            'sale_type' => 'required',
            'product' => 'required',
            'unit' => 'required',
            'quantity' => 'required',
            'PricePerUnit' => 'required',
            'amount' => 'required',
            'remark'    => "max:300"
        ]);

        $submitClass = new sales();
        $submitReturn = $submitClass->localSaleFormSubmit($req);

        if($submitReturn && request('sale_type') == "product_sale"){

            $product = DB::table('products')
                        ->where('dairyId', $dairyId)
                        ->where('productCode', request('product'))
                        ->get()->first();

                    
            $mobile = null;$name=null;
            if(request("partyType") == "member"){
                $m = DB::table("member_personal_info")->where(['memberPersonalCode' => request('memberCode'), "status" => "true", "dairyId" => $dairyId])->get()->first();
                if($m!=null){
                    $app_data = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $m->id])->get();

                    if($app_data==null){
                        $memberToken = null;
                    }else{
                        $memberToken = $app_data->pluck("token_key");
                    }

                    $alerts = DB::table("member_other_info")->where(["memberId" => $m->id])->get()->first();
                    $mobile = $m->memberPersonalMobileNumber;
                    $name = $m->memberPersonalName;
                    $ub =  DB::table("user_current_balance")
                                ->where('ledgerId', $m->ledgerId)
                                ->get()->first();

                }else{
                    goto SKIPSMS;
                }
            }else{
                goto SKIPSMS;
            }

            if(isset($ub) && $ub){
                if($ub->openingBalanceType == "credit")
                    $bal = number_format($ub->openingBalance, 2, ".", ""). " CR";
                else
                    $bal = number_format($ub->openingBalance, 2, ".", ""). " DR";
            }else
               goto SKIPSMS;


            $a = number_format((request('quantity') * $product->amount) - request('discount'), 2, ".", "");
            $f = number_format($a, 2, ".", "");
            $pa = number_format("0".request("paidAmount"), 2, ".", "");

            
            $tempName = explode(" ", $name);
            if(isset($tempName[1])){
                $name = $tempName[0].$tempName[1];
            }else{
                $name = $tempName[0]; 
            }



            if($mobile==null)
                goto SKIPSMS;

            if($alerts->alert_sms != "true"){
                goto SKIPSMS;
            }

            $newLine = "%0A";
            $data = ["message" => "Dear $name,".$newLine.
                    "Date: ".request('date').$newLine.
                    "Product: ".$product->productName.$newLine.
                    "Qty: ".request('quantity').$newLine.
                    "Rate: ".$product->amount.$newLine.
                    "Discount: ".request('discount').$newLine.
                    "Paid Amt: ".$pa.$newLine.
                    "Final Amt: ".$f.$newLine.
                    "Current Balance: $bal".$newLine,
                    "numbers" => $mobile,
                    "messageType" => "productSale"
                    ];
                    $sms = new \App\Sms();
            
                    // $sms->saveToQueue($data, $dairyId);
                    $sms->send($data, $dairyId);
            
        }

        SKIPSMS:

        if(Session::has('noti_message') && Session::has('token_key')){
            $noti = PushNotification::setService('fcm')
                ->setMessage([
                'data' => [
                        "message" =>Session::get('noti_message')
                            ]
                        ])
                ->setApiKey($appSettings->server_api_key)
                ->setDevicesToken(Session::get('token_key'))
                ->send()
                ->getFeedback();

                session()->forget('token_key');
                session()->forget('noti_message');
            // Session::flash('msg', json_encode($noti));
        }

        // Session::flash('msg', json_encode($noti));

        if($req->activetab){
            session()->put('saleActiveTab', $req->activetab);
        }
        if($req->returnurl){
            return redirect($req->returnurl);
        }

        return redirect('localSaleForm');
    }


   	/* Plant Sale */
    public function plantSaleForm(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $milk_plants = DB::table('plantdairymap')
                                ->select("*", "plantdairymap.plantId as id")
                                ->where(["plantdairymap.dairyId" => $dairyId])
                                ->join("milk_plants", "plantdairymap.plantId", "=", "milk_plants.id")
                                ->get();
        
        $noPlant = false;
        if(count($milk_plants)==0){
            $noPlant = true;
        }

        return view('plantSale', ['milkPlants' => $milk_plants, "noPlant"=>$noPlant, "activepage"=>"plantSale"]);
   	}

   	public function plantSaleFormSubmit(Request $req){

   		$validatedData = $req->validate([
            'plantCode' => 'required',
            'toDate' => 'required',
            'fromDate' => 'required',
            'partyType' => 'required',
            'product' => 'required',
            'quantity' => 'required',
            'amount' => 'required',
            'remark'    => "max:300"
        ]);
        
        $submitClass = new sales();
        $res = $submitClass->localSaleFormSubmit($req);

        if($req->activetab){
            session()->put('saleActiveTab', $req->activetab);
        }
        if($req->returnurl){
            return redirect($req->returnurl);
        }
        return redirect("plantSaleForm");
	}

	/* member Sale Form */
	public function memberSaleForm(Request $request){
        $dairyId = session()->get('loginUserInfo')->dairyId ;
        $customers = DB::table('customer')
            ->where('dairyId', $dairyId )
            ->where('status', "true" )
            ->get();

        $memberPersonalInfo = DB::table('member_personal_info') 
            ->where('dairyId', $dairyId )
            ->where('status', "true" )
            ->get();

        $product = DB::table('products') 
            ->where('dairyId', $dairyId )
            ->where('status', "true" )
            ->orderby("productName")
            ->get();

        if(count($product)==0) $noproduct = true; else $noproduct = false;

        $currnetData = array($customers,"",$memberPersonalInfo, $product);

		return view('memberSale', ["currnetData"=>$currnetData, "noproduct" => $noproduct, "activepage"=>"memberSale"]);
	}

	public function memberSaleFormSubmit(Request $request){

		$validatedData = $request->validate([
            'memberCode' => 'required',
            'memberName' => 'required',
            'date' => 'required',
            'product' => 'required',
            'unit' => 'required',
            'quantity' => 'required',
            'PricePerUnit' => 'required',
            'amount' => 'required',
            // 'remark'    => "max:300"
        ]);
        
	    $submitClass = new sales();
        $submitReturn = $submitClass->memberSaleFormSubmit($request); 

        $submitClass = new sales();
        $submitReturn = $submitClass->localSaleFormSubmit($request); 

        $customers = DB::table('customer') 
                  ->where('dairyId', $request->dairyId)
                  ->where('status', "true" )
                  ->get();
        

        $milkPlants = DB::table('milk_plants') 
                ->where('dairyId', $request->dairyId)
                ->where('status', "true" )
                ->get();

        $memberPersonalInfo = DB::table('member_personal_info') 
                ->where('dairyId', $request->dairyId)
                ->where('status', "true")
                ->get();

        $currnetData = array($customers,$milkPlants,$memberPersonalInfo);

        return view('localSale', ['currnetData' => $currnetData]);
	}

  /*get User Name By ledger*/
  public function getUserNameByledger(Request $request){

     if($request->mainValue == "Customer"){

            $customer = DB::table('customer')
                    ->where('ledgerId', $request->ledgerId )
                    ->get();
            if(empty($customer[0])){
                return "false";
            }else{
                return $customer[0]->customerName ;
            }


        }elseif($request->mainValue == "Milk Plants"){
           
            $customer = DB::table('milk_plants')
                    ->where('ledgerId', $request->ledgerId )
                    ->get();
            if(empty($customer[0])){
                return "false";
            }else{
                return $customer[0]->plantName ;
            }
          
        }elseif($request->mainValue == "Member"){

            $customer = DB::table('member_personal_info')
                    ->where('ledgerId', $request->ledgerId)
                    ->get();
            if(empty($customer[0])){
                return "false";
            }else{
                return $customer[0]->memberPersonalName ;
            }
          
        }elseif($request->mainValue == "Supplier"){

            $customer = DB::table('suppliers')
                    ->where('ledgerId', $request->ledgerId )
                    ->get();
            if(empty($customer[0])){
                return "false";
            }else{
                return $customer[0]->supplierFirmName ;
            }
          
        }else{
          return "false"; 
        }

  }

  /* get Ledger Id By Name */
  public function getLedgerIdByName(Request $req){
    //    return $request->all();

        if($req->type == "customer"){
            $customer = DB::table('customer')
                    ->where('customerCode', $req->userCode)
                    ->where('status', "true")
                    ->get()->first();
            if(!empty($customer)){
                return ["error"=>false, "ledgerId" => $customer->ledgerId, "name"=>$customer->customerName];
            }else{
                return ["error"=>true, "msg"=>"Customer Not Found"];
            }

        }elseif($req->type == "milk_plant"){
            $customer = DB::table('milk_plants')
                    ->where('id', $req->userCode)
                    ->get()->first();
            if(empty($customer)){
                return ["error"=>true, "msg"=>"Milk Plant Not Found"];
            }else{
                return ["error"=>false, "ledgerId" => $customer->ledgerId, "name"=>$customer->plantName];
            }
          
        }elseif($req->type == "member"){
            $customer = DB::table('member_personal_info')
                    ->where('memberPersonalCode', $req->userCode)
                    ->where('status', "true")
                    ->get()->first();
            if(empty($customer)){
                return ["error"=>true, "msg"=>"Member Not Found"];
            }else{
                return ["error"=>false, "ledgerId" => $customer->ledgerId, "name"=>$customer->memberPersonalName];
            }

        }elseif($req->type == "supplier"){
            $customer = DB::table('suppliers')
                        ->where('supplierCode', $code)
                        ->where('status', "true")
                        ->get()->first();
            if(empty($customer)){
                return ["error"=>true, "msg"=>"Supplier Not Found"];
            }else{
                return ["error"=>false, "ledgerId" => $customer->ledgerId, "name"=>$customer->supplierPersonName];
            }
          
        }else{
            return ["error"=>true, "msg"=>"Bed Request."];
        }
    }

    public function getSaleDetails()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        
        $dairyInfo = Db::table('dairy_info')->where(["id" => $dairyId])->get()->first();
        $sale = DB::table("sales")->where(["id" => request('id'), "dairyId" => $dairyId, "status" => "true"])->get()->first();

        if($sale==null){
            return ["error" => true, "msg" => "Record not found."];
        }

        if($sale->saleType == "local_sale"){
            $cont = view('localSaleEditModel', ["sale" => $sale, "dairyInfo" => $dairyInfo]);
        }elseif($sale->saleType == "product_sale"){
            $prods = DB::table("products")->where(["dairyId" => $dairyId, "status" => "true"])->get();
            $cont = view('memberSaleEditModel', ["sale" => $sale, "prods" => $prods, "dairyInfo" => $dairyInfo]);
        }else{
            return ["error" => true, "msg" => "Record not found"];
        }
        return ["error" => false, "data" => (string)$cont];
    }

    public function localSaleEditSubmitAj()
    {
        $validatedData = request()->validate([
            'saleId'    => "required",
            'partyName' => 'required',
            'partyType' => 'required',
            'sale_type' => 'required',
            'product'   => 'required',
            'unit'      => 'required',
            'quantity'  => 'required',
            'PricePerUnit' => 'required',
            'amount'    => 'required',
            'remark'    => 'max:300',
        ]);

        $submitClass = new sales();
        $submitReturn = $submitClass->localSaleEditClearOldEntry(request());
        if(!$submitReturn["error"]){
            $insertEdited = $submitClass->localSaleFormSubmit(request());
            return ["error" => !$insertEdited, "msg" => Session::get("msg")];
        }
        else{
            return $submitReturn;
        }
    }

    public function deleteSaleAjax()
    {
        $validatedData = request()->validate([
            'saleId'    => "required",
        ]);

        if(request("saleType") == "local_sale"){
            $sales = new sales();
            $submitReturn = $sales->localSaleEditClearOldEntry(request());
            return $submitReturn;    
        }elseif(request("saleType") == "product_sale"){
            $sales = new sales();
            $submitReturn = $sales->productSaleEditClearOldEntry(request());
            return $submitReturn;    
        }else{
            return ["error" => true, "msg" => "Error in Sale type."];
        }
    }

    public function productSaleEditSubmitAj()
    {
        
        $validatedData = request()->validate([
            'saleId'    => "required",
            'partyName' => 'required',
            'partyType' => 'required',
            'sale_type' => 'required',
            'product'   => 'required',
            'unit'      => 'required',
            'quantity'  => 'required',
            'PricePerUnit' => 'required',
            'amount'    => 'required',
            'remark'    => 'max:300',
        ]);

        $submitClass = new sales();
        $submitReturn = $submitClass->productSaleEditClearOldEntry(request());
        if(!$submitReturn["error"]){
            $insertEdited = $submitClass->localSaleFormSubmit(request());
            return ["error" => !$insertEdited, "msg" => Session::get("msg")];
        }
        else{
            return $submitReturn;
        }
    }

}
