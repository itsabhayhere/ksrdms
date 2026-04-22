<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Debugbar;

class sales extends Model
{
   public function localSaleFormSubmit($request){
        DB::beginTransaction();
        $product = [];
        $currentTime = date('Y-m-d H:i:s');
        $saleType = $request->sale_type;
        $userName = "";
        $productName = $request->product;
        $dairyInfo = DB::table("dairy_info")->where("id", Session::get("dairyInfo")->id)->get()->first();
        $colMan = Session::get("colMan");

        $newLine = '\n';

        if($saleType == "product_sale"){
            $product = DB::table("products")->where(["productCode" =>  $request->product, "dairyId" => $dairyInfo->id])->get()->first();
            if($product==(null||false)){
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: PRODUCT_ERROR:_NO_SUCH_PRODUCT_EXIST');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            if($product->productUnit <= 0 || ($request->quantity > $product->productUnit)){
                DB::rollBack();
                Session::flash('msg', 'Product is not in stock Or product stock is low.');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $productName = $product->productName;
            $PricePerUnit = $product->amount;

            $noti_message = "Dear ".request('partyName').$newLine.
            "Date: ".$request->date.$newLine.
            "Product: ".$productName.$newLine.
            "Qty: ".$request->quantity.$newLine.
            "Rate: ".$PricePerUnit.$newLine.
            "Discount: ".$request->discount.$newLine;

        }elseif($saleType == "local_sale"){
             $category = DB::table("categories")->where(["id" =>  $request->product, "dairyId" => $dairyInfo->id])->get()->first();
              if($category){ $productName = $category->name; $PricePerUnit = $category->price;}

            if($request->partyType == "customer"){

                $PricePerUnit = $request->PricePerUnit;
            }elseif($request->partyType == "member"){
                $PricePerUnit = $request->PricePerUnit;
            

            // if($request->product == "cowMilk"){
            //     $PricePerUnit = $dairyInfo->cowMilkPrice;
            // }elseif($request->product == "buffaloMilk"){
            //     $PricePerUnit = $dairyInfo->buffaloMilkPrice;
            }else{
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: PRODUCT_ERROR:MILK_ERROR');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }


            $noti_message = "Dear ".request('partyName').$newLine.
            "Date: ".$request->date.$newLine.
            "Product: ".$productName.$newLine.
            "Qty: ".$request->quantity.$newLine.
            "Rate: ".$PricePerUnit.$newLine.
            "Discount: ".$request->discount.$newLine;

        }elseif($saleType == "plant_sale"){
            $PricePerUnit = 0;
        }else{
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: PRODUCT_ERROR:MILK_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if($PricePerUnit > 0){
            $amount = (float)$request->quantity * (float)$PricePerUnit;
            $finalAmount = number_format(($amount - (float)$request->discount), 2, ".", "");
        }else{
            // $finalAmount = (request("finalAmount") == null)? 0 : number_format(request("finalAmount"), 2, ".", "");
            $amount = (request("amount") == null)? 0 : number_format(request("amount"), 2, ".", "");
            $finalAmount = $amount;
        }
        $pamount =  $request->paidAmount/$request->totalProduct;

        $usrDebitAmt = (float)number_format((float)$finalAmount - (float)($pamount), 2, ".", "");
        $amountType = "credit";
        if($usrDebitAmt==0){
            $amountType = "cash";
        }
        
        if($request->partyType == "customer"){
            $ledgerId = DB::table("customer")->where(['customerCode' => $request->customerCode,
                                                    "status" => "true", "dairyId" => $dairyInfo->id])->get()->first();
            $saleFromDate = NULL; if(isset($ledgerId->customerName)) $userName = $ledgerId->customerName; 
            $saleDate = date("Y-m-d", strtotime($request->date));
            $partyCode = $ledgerId->customerCode; $partyName = $ledgerId->customerName;
        }elseif($request->partyType == "member"){
            $ledgerId = DB::table("member_personal_info")->where(['memberPersonalCode' => $request->memberCode, 
                                                                "status" => "true", "dairyId" => $dairyInfo->id])->get()->first();
            // dd([$request->memberCode, $dairyInfo->id]);
            $saleFromDate = NULL; if(isset($ledgerId->memberPersonalName)) $userName = $ledgerId->memberPersonalName;
            $saleDate = date("Y-m-d", strtotime($request->date));
            $partyCode = $ledgerId->memberPersonalCode; $partyName = $ledgerId->memberPersonalName;
        }elseif($request->partyType == "plant"){
            $ledgerId = DB::table("milk_plants")->where(['id' => $request->plantCode])->get()->first();
            $PricePerUnit = 0;  if(isset($ledgerId->plantName)) {$request->partyName = $ledgerId->plantName; $userName = $ledgerId->plantName;};
            $saleFromDate = date("Y-m-d", strtotime($request->fromDate));
            $saleDate = date("Y-m-d", strtotime($request->toDate));
            $partyCode = $ledgerId->id; $partyName = $ledgerId->plantName;
        }else{
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: BIG_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if($ledgerId==(null||false||"")){
            DB::rollBack(); 
            Session::flash('msg', 'There are some error occured. Error: LEDGER_NOT_FOUND');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $userCurrentBalance = DB::table("user_current_balance")
                                    ->where('ledgerId', $ledgerId->ledgerId)
                                    ->get()->first();
        
        // dd($userCurrentBalance);exit;

        $currentDairyBalance = DB::table("user_current_balance")
                                    ->where('ledgerId', $dairyInfo->ledgerId)
                                    ->get()->first();

        if($userCurrentBalance==null || $currentDairyBalance==null){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: USER_OR_DAIRY_BALANCE_NOT_FOUND');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }


        if($userCurrentBalance->openingBalanceType == "credit"){
            $newUserBalance = (float)$userCurrentBalance->openingBalance - (float)$usrDebitAmt;
        }else{
            $newUserBalance = - ((float)$userCurrentBalance->openingBalance + (float)$usrDebitAmt);
        }

        $ub = $newUserBalance;

        if( $newUserBalance < 0 ) {
            /* update new amount in user account */
            $newUserBalance = str_replace("-","", $newUserBalance);
            $type="debit";
            $minBalType = "dr";
        }else{
            $type="credit";
            $minBalType = "cr";
        }

        $updateReturn = DB::table('user_current_balance')
                            ->where('ledgerId', $ledgerId->ledgerId )
                            ->update([
                                'openingBalance' => number_format($newUserBalance, 2, ".", ""),
                                'openingBalanceType' => $type,
                                'updated_at'=> $currentTime
                            ]);

        if($updateReturn==(false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: USER_BALANCE_UPDATE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            exit;
            return false;
        }

        /* update new amount in dairy account */
        if($currentDairyBalance->openingBalanceType == "credit"){
            $newDairyBalance = (float)$currentDairyBalance->openingBalance + (float)$usrDebitAmt;
        }else{
            $newDairyBalance = - (float)$currentDairyBalance->openingBalance - (float)$usrDebitAmt;
        }

        if( $newDairyBalance < 0 ) {
            /* update new amount in dairy account */
            $newDairyBalance = str_replace("-","", $newDairyBalance);
            $type="debit";
        }else{
            $type="credit";
        }

        $updateReturn = DB::table('user_current_balance')
                                ->where('ledgerId', $dairyInfo->ledgerId)
                                ->update([
                                    'openingBalance' => number_format($newDairyBalance, 2, ".", ""),
                                    'openingBalanceType' => $type,
                                    'updated_at'=> $currentTime
                                    ]);
        
        // dd(DB::getQueryLog(), var_dump($updateReturn)); exit;

        if($updateReturn==(false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error 2: DAIRY_BALANCE_UPDATE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $single_purchase_amount =0;
        if($product){
            $initial_quantity = DB::table('purchase_setups')
                ->select('quantity')
                ->where('dairyId', $request->dairyId)
                ->where('productCode', $product->productCode)
                ->where('supplierCode', $product->supplierId)
                ->where('amount', $product->purchaseAmount)
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->first();
            if(!empty($initial_quantity)){
                $initial_quantity = $initial_quantity->quantity;
                $single_purchase_amount = $initial_quantity > 0 ? number_format(($product->purchaseAmount/$initial_quantity), 1, ".", "") : 0;
            }
        }
       
        $saleId = DB::table('sales')->insertGetId([
                'dairyId'              =>  $request->dairyId,
                'status'               =>  "true",
                'ledgerId'             =>  $ledgerId->ledgerId,
                'partyCode'            =>  $partyCode,
                'partyName'            =>  $partyName,
                'partyType'            =>  $request->partyType,
                'productType'          =>  $request->product,
                'otherproductType'     =>  $request->product == "other" ? $request->otherProduct : null,
                'milkType'             =>  $request->product == "cowMilk" ? "cow" : ($request->product == "buffaloMilk" ? "buffalo": $productName),
                'productQuantity'      =>  (float)$request->quantity,
                'unit'                 =>  $request->unit,
                'otherUnit'            =>  $request->unit == "specify" ? $request->otherUnit : null,
                'productPricePerUnit'  =>  $PricePerUnit,
                'saleDate'             =>  $saleDate,
                'saleFromDate'         =>  $saleFromDate,
                'saleType'             =>  $saleType,
                'amountType'           =>  $amountType,
                'amount'               =>  $amount,
                'purchaseAmount'       =>  $single_purchase_amount,
                'discount'             =>  $request->discount,
                'finalAmount'          =>  $finalAmount,
                'paidAmount'           =>  ($request->paidAmount/$request->totalProduct),
                'remark'               =>  (isset($request->remark))?$request->remark."":"",
                'created_at'           =>  $currentTime,
              ]);

        if($saleId==(false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: SALES_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }


        if($usrDebitAmt != (0||null)){
            /* user entry in balance sheet */
            $submiteInfoBL = DB::table('balance_sheet')->insertGetId([
                    'ledgerId'          => $ledgerId->ledgerId,
                    'dairyId'           => $request->dairyId,
                    'transactionId'     => $saleId,
                    'srcDest'           => $dairyInfo->ledgerId,
                    'colMan'            => $colMan->userName,
                    'transactionType'   => 'sales',
                    'remark'            => 'purchase '.$productName.' ('.$request->quantity." Unit)",
                    'amountType'        => 'debit',
                    'finalAmount'       => $usrDebitAmt,
                    'currentBalance'    =>number_format($newUserBalance, 2, ".", "").' '.$minBalType,
                    'created_at'        => $currentTime,
                ]);
        }

        if($request->paidAmount != (0||null)){
            /* user entry in balance sheet */
            $submiteInfoBL = DB::table('balance_sheet')->insertGetId([
                'ledgerId'          => $ledgerId->ledgerId,
                'dairyId'           => $request->dairyId,
                'transactionId'     => $saleId,
                'srcDest'           => $dairyInfo->ledgerId,
                'colMan'            => $colMan->userName,
                'transactionType'   => 'sales',
                'remark'            => 'purchase '.$productName.' ('.$request->quantity." Unit)",
                'amountType'        => 'cash',
                'finalAmount'       => ($request->paidAmount/$request->totalProduct),
                'currentBalance'    =>number_format($newUserBalance, 2, ".", "").' '.$minBalType,
                'created_at'        => $currentTime,
            ]);
        }

        if($request->paidAmount == 0 && $usrDebitAmt == 0){
            $submiteInfoBL = DB::table('balance_sheet')->insertGetId([
                'ledgerId'          => $ledgerId->ledgerId,
                'dairyId'           => $request->dairyId,
                'transactionId'     => $saleId,
                'srcDest'           => $dairyInfo->ledgerId,
                'colMan'            => $colMan->userName,
                'transactionType'   => 'sales',
                'remark'            => 'purchase '.$productName.' ('.$request->quantity.") Discount: ".$request->discount,
                'amountType'        => 'cash',
                'finalAmount'       => ($request->paidAmount/$request->totalProduct),
                'currentBalance'    =>number_format($newUserBalance, 2, ".", "").' '.$minBalType,
                'created_at'        => $currentTime,
            ]);
        }

        if(!isset($submiteInfoBL) || $submiteInfoBL == (false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: BALANCE_UPDATE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if($saleType=="product_sale"){
            $prodUnit = DB::table("products")->where(["productCode" => $request->product, "dairyId" => $dairyInfo->id])
                                            ->update([
                                                "productUnit" => (float)$product->productUnit - (float)$request->quantity,
                                            ]);

            if($prodUnit==(false||null) ){
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: DAIRY_BALANCE_UPDATE_ERROR');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }
        $pamount =($request->paidAmount/$request->totalProduct); 
        $cashinhand = (float)round($dairyInfo->cash_in_hand + $pamount, 2);
        if($cashinhand != $dairyInfo->cash_in_hand){
            $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);
            if($dbalup == (false||null)){
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: DAIRY_CASH_INHAND_UPDATE_ERROR');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        DB::commit();

        // if (!$res['error']) {
        //     $data = [
        //         "sysName" => $dairyInfo->dairyName,
        //         "dairyAdmin" => "Dairy Admin",
        //         "user" => [
        //             "username" => $userName,
        //         ],
        //         "loginlink" => url("dairy-login"),
        //     ];

        //     Mail::send('emails.welcome', $data, function ($m) use ($req) {
        //         $m->from('dms.online.2018@gmail.com', 'DMS ADMIN');
        //         $m->to("Yannisoni@gmail.com", "Yanni Soni")->subject('Welcome to DMS!');
        //     });
        // }
        if(isset($noti_message)){
            $noti_message .= "Paid Amt: ".$request->paidAmount.$newLine.
            "Final Amt: ".$finalAmount.$newLine.
            "Current Balance: $ub".$newLine;
        }else{
            $noti_message = null;
        }
        if($request->partyType == "member"){
            $app_data = DB::table('app_logins')->where(["dairyId" => $dairyInfo->id, "userType" => "4", "userId" => $ledgerId->id])->get();
            if($app_data){
                Session::put('token_key', $app_data->pluck('device_token'));
            }
        }
        Session::put('noti_message', $noti_message);

        Session::flash('msg', 'Sale Successfully saved.');
        Session::flash('alert-class', 'alert-success');
        return true;
      
    }



    public function localSaleFormSubmitAPI($request){
        DB::beginTransaction();

        $currentTime = date('Y-m-d H:i:s');
        $saleType = $request->sale_type;
        $userName = "";
        $productName = $request->product;
        $dairyInfo = DB::table("dairy_info")->where("id", $request->dairyId)->get()->first();
        $colMan = DB::table("other_users")->where(["userName" => $request->colMan, "dairyId" => $request->dairyId])->get()->first();

        $newLine = '\n';

        if($saleType == "product_sale"){
            $product = DB::table("products")->where("productCode", $request->product)->get()->first();
            if($product==(null||false)){
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: PRODUCT_ERROR:_NO_SUCH_PRODUCT_EXIST');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            if($product->productUnit <= 0 || ($request->quantity > $product->productUnit)){
                DB::rollBack();
                Session::flash('msg', 'Product is not in stock Or product stock is low.');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $productName = $product->productName;
            $PricePerUnit = $product->amount;

            $noti_message = "Dear ".request('partyName').$newLine.
            "Date: ".request('date').$newLine.
            "Product: ".$productName.$newLine.
            "Qty: ".request('quantity').$newLine.
            "Rate: ".$PricePerUnit.$newLine.
            "Discount: ".request('discount').$newLine;

        }elseif($saleType == "local_sale"){
            if($request->product == "cowMilk"){
                $PricePerUnit = $dairyInfo->cowMilkPrice;
            }elseif($request->product == "buffaloMilk"){
                $PricePerUnit = $dairyInfo->buffaloMilkPrice;
            }else{
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: PRODUCT_ERROR:MILK_ERROR');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            
            $noti_message = "Dear ".request('partyName').$newLine.
            "Date: ".request('date').$newLine.
            "Product: ".request('product').$newLine.
            "Qty: ".request('quantity').$newLine.
            "Rate: ".$PricePerUnit.$newLine.
            "Discount: ".request('discount').$newLine;

        }elseif($saleType == "plant_sale"){
            $PricePerUnit = 0;
        }else{
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: PRODUCT_ERROR:MILK_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if($PricePerUnit > 0){
            $amount = (float)$request->quantity * (float)$PricePerUnit;
            $finalAmount = number_format(($amount - (float)$request->discount), 2, ".", "");
        }else{
            // $finalAmount = (request("finalAmount") == null)? 0 : number_format(request("finalAmount"), 2, ".", "");
            $amount = (request("amount") == null)? 0 : number_format(request("amount"), 2, ".", "");
            $finalAmount = $amount;
        }

        $usrDebitAmt = (float)number_format((float)$finalAmount - (float)$request->paidAmount, 2, ".", "");
        $amountType = "credit";
        if($usrDebitAmt==0){
            $amountType = "cash";
        }
        
        if($request->partyType == "customer"){
            $ledgerId = DB::table("customer")->where(['customerCode' => $request->customerCode, "dairyId" => $dairyInfo->id])->get()->first();
            if($ledgerId==null){                
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: Customer Not Found');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $saleFromDate = NULL; if(isset($ledgerId->customerName)) $userName = $ledgerId->customerName;
            $saleDate = date("Y-m-d", strtotime($request->date));
            $partyCode = $ledgerId->customerCode; $partyName = $ledgerId->customerName;
        }elseif($request->partyType == "member"){
            $ledgerId = DB::table("member_personal_info")->where(['memberPersonalCode' => $request->memberCode, "dairyId" => $dairyInfo->id])->get()->first();
            if($ledgerId==null){                
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: Member Not Found');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $saleFromDate = NULL; if(isset($ledgerId->memberPersonalName)) $userName = $ledgerId->memberPersonalName;
            $saleDate = date("Y-m-d", strtotime($request->date));
            $partyCode = $ledgerId->memberPersonalCode; $partyName = $ledgerId->memberPersonalName;
        }elseif($request->partyType == "plant"){
            $ledgerId = DB::table("milk_plants")->where(['plantCode' => $request->plantCode])->get()->first();
            if($ledgerId==null){                
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: Plant Not Found');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $PricePerUnit = 0;  if(isset($ledgerId->plantName)) $userName = $ledgerId->plantName;
            $saleFromDate = date("Y-m-d", strtotime($request->fromDate));
            $saleDate = date("Y-m-d", strtotime($request->toDate));
            $partyCode = $ledgerId->id; $partyName = $ledgerId->plantName;
        }else{
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: BIG_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if($ledgerId==(null||false||"")){
            DB::rollBack(); 
            Session::flash('msg', 'There are some error occured. Error: LEDGER_NOT_FOUND');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $userCurrentBalance = DB::table("user_current_balance")
                                    ->where('ledgerId', $ledgerId->ledgerId)
                                    ->get()->first();
        
        // dd($userCurrentBalance);exit;

        $currentDairyBalance = DB::table("user_current_balance")
                                    ->where('ledgerId', $dairyInfo->ledgerId)
                                    ->get()->first();

        if($userCurrentBalance==null || $currentDairyBalance==null){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: USER_OR_DAIRY_BALANCE_NOT_FOUND');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }


        if($userCurrentBalance->openingBalanceType == "credit"){
            $newUserBalance = (float)$userCurrentBalance->openingBalance - (float)$usrDebitAmt;
        }else{
            $newUserBalance = - ((float)$userCurrentBalance->openingBalance + (float)$usrDebitAmt);
        }

        $ub = $newUserBalance;
        
        if( $newUserBalance < 0 ) {
            /* update new amount in user account */
            $newUserBalance = str_replace("-","", $newUserBalance);
            $type="debit";
        }else{
            $type="credit";
        }

        $updateReturn = DB::table('user_current_balance')
                            ->where('ledgerId', $ledgerId->ledgerId )
                            ->update([
                                'openingBalance' => number_format($newUserBalance, 2, ".", ""),
                                'openingBalanceType' => $type,
                                'updated_at'=> $currentTime
                            ]);

        if($updateReturn==(false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: USER_BALANCE_UPDATE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            exit;
            return false;
        }

        /* update new amount in dairy account */
        if($currentDairyBalance->openingBalanceType == "credit"){
            $newDairyBalance = (float)$currentDairyBalance->openingBalance + (float)$usrDebitAmt;
        }else{
            $newDairyBalance = - (float)$currentDairyBalance->openingBalance - (float)$usrDebitAmt;
        }

        if( $newDairyBalance < 0 ) {
            /* update new amount in dairy account */
            $newDairyBalance = str_replace("-","", $newDairyBalance);
            $type="debit";
        }else{
            $type="credit";
        }

        $updateReturn = DB::table('user_current_balance')
                                ->where('ledgerId', $dairyInfo->ledgerId)
                                ->update([
                                    'openingBalance' => number_format($newDairyBalance, 2, ".", ""),
                                    'openingBalanceType' => $type,
                                    'updated_at'=> $currentTime
                                    ]);
        
        // dd(DB::getQueryLog(), var_dump($updateReturn)); exit;

        if($updateReturn==(false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error 2: DAIRY_BALANCE_UPDATE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

       
        $saleId = DB::table('sales')->insertGetId([
                'dairyId'              =>  $request->dairyId,
                'status'               =>  "true",
                'ledgerId'             =>  $ledgerId->ledgerId,
                'partyCode'            =>  $partyCode,
                'partyName'            =>  $partyName,
                'partyType'            =>  $request->partyType,
                'productType'          =>  $request->product,
                'otherproductType'     =>  $request->product == "other" ? $request->otherProduct : null,
                'milkType'             =>  $request->product == "cowMilk" ? "cow" : ($request->product == "buffaloMilk" ? "buffalo": null),
                'productQuantity'      =>  (float)$request->quantity,
                'unit'                 =>  $request->unit,
                'otherUnit'            =>  $request->unit == "specify" ? $request->otherUnit : null,
                'productPricePerUnit'  =>  $PricePerUnit,
                'saleDate'             =>  $saleDate,
                'saleFromDate'         =>  $saleFromDate,
                'saleType'             =>  $saleType,
                'amountType'           =>  $amountType,
                'amount'               =>  $amount,
                'discount'             =>  $request->discount,
                'finalAmount'          =>  $finalAmount,
                'paidAmount'           =>  $request->paidAmount,
                'created_at'           =>  $currentTime,
              ]);

        if($saleId==(false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: SALES_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }


        if($usrDebitAmt != (0||null)){
            /* user entry in balance sheet */
            $submiteInfoBL = DB::table('balance_sheet')->insertGetId([
                    'ledgerId'          => $ledgerId->ledgerId,
                    'dairyId'           => $request->dairyId,
                    'transactionId'     => $saleId,
                    'srcDest'           => $dairyInfo->ledgerId,
                    'colMan'            => $colMan->userName,
                    'transactionType'   => 'sales',
                    'remark'            => 'purchase '.$productName.' ('.$request->quantity." Unit)",
                    'amountType'        => 'debit',
                    'finalAmount'       => $usrDebitAmt,
                    'created_at'        => $currentTime,
                ]);
        }

        if($request->paidAmount != (0||null)){
            /* user entry in balance sheet */
            $submiteInfoBL = DB::table('balance_sheet')->insertGetId([
                'ledgerId'          => $ledgerId->ledgerId,
                'dairyId'           => $request->dairyId,
                'transactionId'     => $saleId,
                'srcDest'           => $dairyInfo->ledgerId,
                'colMan'            => $colMan->userName,
                'transactionType'   => 'sales',
                'remark'            => 'purchase '.$productName.' ('.$request->quantity." Unit)",
                'amountType'        => 'cash',
                'finalAmount'       => $request->paidAmount,
                'created_at'        => $currentTime,
            ]);
        }

        if($request->paidAmount == 0 && $usrDebitAmt == 0){
            $submiteInfoBL = DB::table('balance_sheet')->insertGetId([
                'ledgerId'          => $ledgerId->ledgerId,
                'dairyId'           => $request->dairyId,
                'transactionId'     => $saleId,
                'srcDest'           => $dairyInfo->ledgerId,
                'colMan'            => $colMan->userName,
                'transactionType'   => 'sales',
                'remark'            => 'purchase '.$productName.' ('.$request->quantity.") Discount: ".$request->discount,
                'amountType'        => 'cash',
                'finalAmount'       => $request->paidAmount,
                'created_at'        => $currentTime,
            ]);
        }

        if(!isset($submiteInfoBL) || $submiteInfoBL == (false||null)){
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: BALANCE_UPDATE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if($saleType=="product_sale"){
            $prodUnit = DB::table("products")->where(["productCode" => $request->product, "dairyId" => $dairyInfo->id])
                                            ->update([
                                                "productUnit" => (float)$product->productUnit - (float)$request->quantity,
                                            ]);

            if($prodUnit==(false||null) ){
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: DAIRY_BALANCE_UPDATE_ERROR');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        $cashinhand = (float)round($dairyInfo->cash_in_hand + $request->paidAmount, 2);
        if($cashinhand != $dairyInfo->cash_in_hand){
            $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);
            if($dbalup == (false||null)){
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error: DAIRY_CASH_INHAND_UPDATE_ERROR');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        DB::commit();

        // if (!$res['error']) {
        //     $data = [
        //         "sysName" => $dairyInfo->dairyName,
        //         "dairyAdmin" => "Dairy Admin",
        //         "user" => [
        //             "username" => $userName,
        //         ],
        //         "loginlink" => url("dairy-login"),
        //     ];

        //     Mail::send('emails.welcome', $data, function ($m) use ($req) {
        //         $m->from('dms.online.2018@gmail.com', 'DMS ADMIN');
        //         $m->to("Yannisoni@gmail.com", "Yanni Soni")->subject('Welcome to DMS!');
        //     });
        // }

        if(isset($noti_message)){
            $noti_message .= "Paid Amt: ".$request->paidAmount.$newLine.
            "Final Amt: ".$finalAmount.$newLine.
            "Current Balance: $ub".$newLine;
        }else{
            $noti_message = null;
        }
        if($request->partyType == "member"){
            $app_data = DB::table('app_logins')->where(["dairyId" => $dairyInfo->id, "userType" => "4", "userId" => $ledgerId->id])->get();
            if($app_data){
                Session::put('token_key', $app_data->pluck('device_token'));
            }
        }
        Session::put('noti_message', $noti_message);

        Session::flash('msg', 'Sale Successfully saved.');
        Session::flash('alert-class', 'alert-success');
        return true;
      
    }




    public function localSaleEditClearOldEntry($request){
        DB::beginTransaction();

        $dairyInfo = DB::table("dairy_info")->where("id", Session::get("dairyInfo")->id)->get()->first();
        $colMan = Session::get("colMan");
        $sale = DB::table("sales")->where(["dairyId" => $dairyInfo->id, "id" => request('saleId')])->get()->first();
        $currentTime = date('Y-m-d H:i:s');

        if($sale == null || $sale->saleType != "local_sale"){
            DB::rollBack();
            return ["error" => true, 'msg' => 'No record found.'];
        }

        $blsht = DB::table("balance_sheet")->where(["transactionType" => "sales", "transactionId" => request("saleId"), "dairyId" => $dairyInfo->id])
                        ->get();
        $sUpdate = DB::table("sales")->where(["dairyId" => $dairyInfo->id, "id" => request('saleId')])->update(["status" => "false"]);
        $blshtupdt = DB::table("balance_sheet")->where(["transactionType" => "sales", "transactionId" => request("saleId"), "dairyId" => $dairyInfo->id])
                                            ->update(["status" => "false"]);
        if($sUpdate == false || $blshtupdt == false){
            DB::rollBack();
            return ["error" => true, 'msg' => 'An error has occured while updating record.1'];
        }

        foreach($blsht as $bs){
            // if($bs->amountType == "cash"){
            //     $cshbalupdate = DB::table("dairy_info")->where("id", $dairyInfo->id)
            //         ->update(["cash_in_hand" => number_format($dairyInfo->cash_in_hand - $bs->finalAmount, 2, ".", "")]);
            // }else
            if($bs->amountType == ("debit" || "cash")){ 
                $ub =  DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])->get()->first();
                if($ub->openingBalanceType == "credit"){
                    $newub = (float)$ub->openingBalance + (float)$bs->finalAmount;
                }else{
                    $newub = - ((float)$ub->openingBalance - (float)$bs->finalAmount);
                }
        
                if( $newub < 0 ) {
                    $newub = str_replace("-","", $newub);
                    $type="debit";
                }else{
                    $type="credit";
                }
        
                $ubupdt = DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])
                    ->update(["openingBalance" => $newub, 'openingBalanceType' => $type, 'updated_at'=> $currentTime]);

                    if($ubupdt == false ){
                        DB::rollBack();
                        return ["error" => true, 'msg' => 'An error has occured while updating record.4'];
                    }

            }elseif($bs->amountType == "credit"){
                $ub =  DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])->get()->first();
                if($ub->openingBalanceType == "credit"){
                    $newub = (float)$ub->openingBalance - (float)$bs->finalAmount;
                }else{
                    $newub = - ((float)$ub->openingBalance + (float)$bs->finalAmount );
                }

                if( $newub < 0 ) {
                    $newub = str_replace("-","", $newub);
                    $type="debit";
                }else{
                    $type="credit";
                }

                $ubupdt = DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])
                    ->update(["openingBalance" => $newub, 'openingBalanceType' => $type, 'updated_at'=> $currentTime]);

                    if($ubupdt == false ){
                        DB::rollBack();
                        return ["error" => true, 'msg' => 'An error has occured while updating record.5'];
                    }
            }else{
                DB::rollBack();
                return ["error" => true, 'msg' => 'An error has occured while updating record.6'];
            }
        }

        DB::commit();
        return ["error" => false, 'msg' => 'Record Deleted Successfully.'];
    }


    public function productSaleEditClearOldEntry($request)
    {
        DB::beginTransaction();

        $dairyInfo = DB::table("dairy_info")->where("id", Session::get("dairyInfo")->id)->get()->first();
        $colMan = Session::get("colMan");
        $sale = DB::table("sales")->where(["dairyId" => $dairyInfo->id, "id" => request('saleId')])->get()->first();
        $currentTime = date('Y-m-d H:i:s');

        // echo json_encode($sale); exit();

        if($sale == null || $sale->saleType != "product_sale"){
            DB::rollBack();
            return ["error" => true, 'msg' => 'No record found.'];
        }

        $blsht = DB::table("balance_sheet")->where(["transactionType" => "sales", "transactionId" => request("saleId"), "dairyId" => $dairyInfo->id])
                        ->get();
        $sUpdate = DB::table("sales")->where(["dairyId" => $dairyInfo->id, "id" => request('saleId')])->update(["status" => "false"]);
        $blshtupdt = DB::table("balance_sheet")->where(["transactionType" => "sales", "transactionId" => request("saleId"), "dairyId" => $dairyInfo->id])
                                            ->update(["status" => "false"]);
        if($sUpdate == false || $blshtupdt == false){
            DB::rollBack();
            return ["error" => true, 'msg' => 'An error has occured while updating record.1'];
        }

        foreach($blsht as $bs){
            // if($bs->amountType == "cash"){
            //     $cshbalupdate = DB::table("dairy_info")->where("id", $dairyInfo->id)
            //         ->update(["cash_in_hand" => number_format($dairyInfo->cash_in_hand - $bs->finalAmount, 2, ".", "")]);
            // }else
            if($bs->amountType == ("debit" || "cash")){ 
                $ub =  DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])->get()->first();
                if($ub->openingBalanceType == "credit"){
                    $newub = (float)$ub->openingBalance + (float)$bs->finalAmount;
                }else{
                    $newub = - ((float)$ub->openingBalance - (float)$bs->finalAmount);
                }
        
                if( $newub < 0 ) {
                    $newub = str_replace("-","", $newub);
                    $type="debit";
                }else{
                    $type="credit";
                }
        
                $ubupdt = DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])
                    ->update(["openingBalance" => $newub, 'openingBalanceType' => $type, 'updated_at'=> $currentTime]);

                    if($ubupdt == false ){
                        DB::rollBack();
                        return ["error" => true, 'msg' => 'An error has occured while updating record.4'];
                    }

            }elseif($bs->amountType == "credit"){
                $ub =  DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])->get()->first();
                if($ub->openingBalanceType == "credit"){
                    $newub = (float)$ub->openingBalance - (float)$bs->finalAmount;
                }else{
                    $newub = - ((float)$ub->openingBalance + (float)$bs->finalAmount );
                }

                if( $newub < 0 ) {
                    $newub = str_replace("-","", $newub);
                    $type="debit";
                }else{
                    $type="credit";
                }

                $ubupdt = DB::table("user_current_balance")->where(["ledgerId" => $bs->ledgerId])
                    ->update(["openingBalance" => $newub, 'openingBalanceType' => $type, 'updated_at'=> $currentTime]);

                    if($ubupdt == false ){
                        DB::rollBack();
                        return ["error" => true, 'msg' => 'An error has occured while updating record.5'];
                    }
            }else{
                DB::rollBack();
                return ["error" => true, 'msg' => 'An error has occured while updating record.6'];
            }
        }

        $pro = DB::table("products")->where(["productCode" => $sale->productType, "dairyId" => $dairyInfo->id])->get()->first();
        if($pro == null){
            DB::rollBack();
            return ["error" => true, 'msg' => 'An error has occured while updating record, Product not found.'];
        }

        $newStock = (float)($pro->productUnit + $sale->productQuantity);
        if($newStock != $pro->productUnit){
            $pUpdate = DB::table("products")->where(["id" => $pro->id])
                        ->update(["productUnit" => ($pro->productUnit + $sale->productQuantity),
                                    "updated_at" => $currentTime]);
            if($pUpdate == false){
                DB::rollBack();
                return ["error" => true, 'msg' => 'An error has occured while updating record, stock update error.'];
            }    
        }

        DB::commit();
        return ["error" => false, 'msg' => 'Record Deleted Successfully.'];
    }


    /* plant Sale */

    // public function plantSaleFormSubmit($request){

    // 	/* get current time */
    //     $currentTime =  date('Y-m-d H:i:s');
    //     DB::beginTransaction();
 
    //     // $submiteInfo = DB::table('plant_sale')->insertGetId([
    //     //         'plantId' => $request->plantName,
    //     //         'plantOther' => $request->plantName == "other" ? $request->otherPlantName : null,
    //     //         'date' => $request->date,
    //     //         'milkType' => $request->product,
    //     //         'quantity' => $request->quantity,
    //     //         'amount' => $request->amount,
 	// 	// 		'created_at' => $currentTime,
    //     //       ]);
        
    //     $plant = DB::table("milk_plants")->where("id", $request->plantCode)->get()->first();

    //     $saleId = DB::table('sales')->insertGetId([
    //             'dairyId' => $request->dairyId,
    //             'status' => $request->status,
    //             'partyCode'=>$request->plantCode,
    //             'partyName' => $plant->plantName,
    //             'partyType' => $plant->partyType,
    //             'saleType' => "plant_sale",
    //             'productType' => 'milk',
    //             'productQuantity' => (float)$request->quantity,
    //             'productPricePerUnit' => number_format((float)$request->amount/(float)$request->quantity,2),
    //             'unit' => 'Ltr',
    //             'milkType' => $request->product,
    //             'amount' => $request->amount,
    //             'amountType' => $request->amountType,
    //             'ledgerId' => $plant->ledgerId,
    //             'saleFromDate' => date("Y-m-d", strtotime($request->fromDate)),
    //             'saleDate' => date("Y-m-d", strtotime($request->toDate)),
 	// 			'created_at' => $currentTime,
    //           ]);
       
    //     $returnSuccessArray = array("Success"=>"True","Message"=>"Plant Sale Submited","Sale id"=> $saleId);
    //     $returnSuccessJson =  json_encode($returnSuccessArray);
    //     return $returnSuccessJson;

    // }

    /* member sale */
    // public function memberSaleFormSubmit($request){

    // 	   /* get current time */
    //     date_default_timezone_set('Asia/Kolkata');
    //     $currentTime =  date('Y-m-d H:i:s');
 
    //     $submiteInfo = DB::table('member_sale')->insertGetId([
    //             'memberCode' => $request->memberCode,
    //             'memberName' => $request->memberName  ,
    //             'date' => $request->date,
    //             'product' => $request->product,
    //             'productOther' => $request->product == "other" ? $request->otherProduct : null  ,
    //             'unit' => $request->unit,
    //             'unitSpecify' =>  $request->unit == "specify" ? $request->otherUnit : null  ,
    //             'quantity' => (float)$request->quantity,
    //             'PricePerUnit' => $request->PricePerUnit,
    //             'amount' => $request->amount, 
 	// 			'created_at' => $currentTime,
    //           ]);

    //     $saleId = DB::table('sales')->insertGetId(
    //           [
    //             'dairyId' => $request->dairyId,
    //             'status' => $request->status,
    //             'saleType' => "member_sale",
    //             'saleId' => $submiteInfo,
 	// 			'created_at' => $currentTime,
    //           ]
    //     );
       
    //     $returnSuccessArray = array("Success"=>"True","Message"=>"Member Sale Submited","Sale id"=> $saleId);
    //     $returnSuccessJson =  json_encode($returnSuccessArray);
    //     return $returnSuccessJson  ;

    // }
    
}
