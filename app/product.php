<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class product extends Model
{
    /* product Submit */
    public function productSubmit($req)
    {
        DB::beginTransaction();

        $dairy = DB::table("dairy_info")->where("id", session()->get("loginUserInfo")->dairyId)->get()->first();

        $currentTime = date('Y-m-d H:i:s');
        $productCode = $dairy->id . "P" . $req->productCode;
        $colMan = session()->get("colMan");

        $creditAmount = (float) $req->purchaseAmount - (float) $req->paidAmount;
        $amountType = "credit";
        if ($creditAmount == 0) {
            $amountType = "cash";
        }

        $supp = DB::table("suppliers")->where('id', $req->supplier)->get()->first();
        if ($supp == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: SUPPLIER-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $submiteInfo = DB::table('products')->insertGetId([
            'dairyId' => $req->dairyId,
            'status' => $req->status,
            'productCode' => $productCode,
            'productName' => $req->productName,
            'productUnit' => $req->productUnit,
            'amount' => $req->productAmount,
            'supplierId' => $req->supplier,
            'purchaseAmount' => $req->purchaseAmount,
            'created_at' => $currentTime,
        ]);

        if ($submiteInfo == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PRODUCT');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        // purchase entry
        $purchaseId = DB::table('purchase_setups')->insertGetId([
            'dairyId' => $req->dairyId,
            'status' => $req->status,
            'ledgerId' => $supp->ledgerId,
            'supplierName' => $supp->supplierFirmName,
            'itemPurchased' => $req->productName,
            'quantity' => $req->productUnit,
            'productCode' => $productCode,
            'amount' => $req->purchaseAmount,
            'paidAmount' => $req->paidAmount,
            'supplierCode' => $req->supplier,
            'purchaseType' => $amountType,
            'date' => date("Y-m-d"),
            'time' => date("H:i:s"),
            'created_at' => $currentTime,
        ]);
        if ($purchaseId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PURCHASE_SETUP');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($creditAmount != 0) {
            $bs1 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $req->dairyId,
                'ledgerId' => $dairy->ledgerId,
                'srcDest' => $supp->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "debit",
                'finalAmount' => $creditAmount,
                'remark' => "product supply from supplier to dairy",
                'created_at' => $currentTime,
            ]);
            if ($bs1 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_BALANCE_SHEET-225');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $bs2 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $req->dairyId,
                'ledgerId' => $supp->ledgerId,
                'srcDest' => $dairy->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "credit",
                'finalAmount' => $creditAmount,
                'remark' => "product supply from supplier to dairy ",
                'created_at' => $currentTime,
            ]);
            if ($bs2 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: SUPP_BALANCE_SHEET-225');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        if ($req->paidAmount != 0) {
            $bs1 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $req->dairyId,
                'ledgerId' => $dairy->ledgerId,
                'srcDest' => $supp->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "cash",
                'finalAmount' => $req->paidAmount,
                'remark' => "product supply from supplier to dairy",
                'created_at' => $currentTime,
            ]);
            if ($bs1 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_BALANCE_SHEET-226');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $bs2 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $req->dairyId,
                'ledgerId' => $supp->ledgerId,
                'srcDest' => $dairy->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "cash",
                'finalAmount' => $req->paidAmount,
                'remark' => "product supply from supplier to dairy ",
                'created_at' => $currentTime,
            ]);
            if ($bs2 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: SUPP_BALANCE_SHEET-226');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            
            $cashInHand = $dairy->cash_in_hand - $req->paidAmount;
            $up = DB::table("dairy_info")->where("id", $dairy->id)->update(["cash_in_hand" => $cashInHand]);
            if ($up == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_CASHUPDATE');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        // dairy balance updation
        $dairyCb = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->get()->first();
        if ($dairyCb == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225' . json_encode($suppCb));
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($dairyCb->openingBalanceType == "debit") {
            $dairyCb->openingBalance -= 2 * $dairyCb->openingBalance;
        }

        $dbal = (float) $dairyCb->openingBalance - (float) $creditAmount;

        if ($dbal > 0) {$dbalType = "credit";} else { $dbal = str_replace("-", "", $dbal);
            $dbalType = "debit";}

        $dcbId = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->update([
            'openingBalance' => $dbal,
            'openingBalanceType' => $dbalType,
            'updated_at' => $currentTime,
        ]);
        if ($dcbId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_CURRENTBALANCE_UPDATE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        // supplier balance updation
        $suppCb = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->get()->first();
        if ($suppCb == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225' . json_encode($suppCb));
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($suppCb->openingBalanceType == "debit") {
            $suppCb->openingBalance -= 2 * $suppCb->openingBalance;
        }

        $sbal = (float) $suppCb->openingBalance + (float) $creditAmount;

        if ($sbal > 0) {$sbalType = "credit";} else { $sbal = str_replace("-", "", $sbal);
            $sbalType = "debit";}

        $scbId = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->update([
            'openingBalance' => $sbal,
            'openingBalanceType' => $sbalType,
            'updated_at' => $currentTime,
        ]);
        if ($scbId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: SUPP_CURRENTBALANCE_UPDATE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        DB::commit();

        Session::flash('msg', 'Product added Successfuly');
        Session::flash('alert-class', 'alert-success');
        return true;
    }


    public function productStockSubmit(){
        DB::beginTransaction();

        $dairyId = session()->get("colMan")->dairyId;
        $dairy = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $currentTime = date('Y-m-d H:i:s');
        $colMan = session()->get("colMan");

        $pro = DB::table("products")->where(["id" => request("productId"), "dairyId" => $dairyId])->get()->first();
        if ($pro == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PRODUCT-404');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $creditAmount = (float) request('purchaseAmount') - (float) request('paidAmount');
        $amountType = "credit";
        if ($creditAmount == 0) {
            $amountType = "cash";
        }

        $supp = DB::table("suppliers")->where('id', request('supplier'))->get()->first();
        if ($supp == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: SUPPLIER-404');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $submiteInfo = DB::table('products')->where(["id" => request("productId"), "dairyId" => $dairyId])->update([
            'status' => "true",
            'productUnit' => number_format(request('productUnit'), 1, ".", ""),
            'amount' => request('productAmount'),
            'supplierId' => request('supplier'),
            'purchaseAmount' => request('purchaseAmount'),
            'updated_at' => $currentTime,
        ]);

        if ($submiteInfo == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PRODUCT');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        // purchase entry
        $purchaseId = DB::table('purchase_setups')->insertGetId([
            'dairyId' => $dairyId,
            'status' => "true",
            'ledgerId' => $supp->ledgerId,
            'supplierName' => $supp->supplierFirmName,
            'itemPurchased' => $pro->productName,
            'quantity' => number_format(request('productUnit'), 1, ".", ""),
            'productCode' => $pro->productCode,
            'amount' => request('purchaseAmount'),
            'paidAmount' => request('paidAmount'),
            'supplierCode' => request('supplier'),
            'purchaseType' => $amountType,
            'date' => date("Y-m-d"),
            'time' => date("H:i:s"),
            'created_at' => $currentTime,
        ]);
        if ($purchaseId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PURCHASE_SETUP');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($creditAmount != 0) {
            $bs1 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $dairy->ledgerId,
                'srcDest' => $supp->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "debit",
                'finalAmount' => $creditAmount,
                'remark' => "product supply from supplier to dairy",
                'created_at' => $currentTime,
            ]);
            if ($bs1 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_BALANCE_SHEET-225');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $bs2 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $supp->ledgerId,
                'srcDest' => $dairy->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "credit",
                'finalAmount' => $creditAmount,
                'remark' => "product supply from supplier to dairy ",
                'created_at' => $currentTime,
            ]);
            if ($bs2 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: SUPP_BALANCE_SHEET-225');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        if (request('paidAmount') != 0) {
            $bs1 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $dairy->ledgerId,
                'srcDest' => $supp->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "cash",
                'finalAmount' => request('paidAmount'),
                'remark' => "product supply from supplier to dairy",
                'created_at' => $currentTime,
            ]);
            if ($bs1 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_BALANCE_SHEET-226');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $bs2 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $supp->ledgerId,
                'srcDest' => $dairy->ledgerId,
                'colMan' => $colMan->userName,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "cash",
                'finalAmount' => request('paidAmount'),
                'remark' => "product supply from supplier to dairy ",
                'created_at' => $currentTime,
            ]);
            if ($bs2 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: SUPP_BALANCE_SHEET-226');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            
            $cashInHand = $dairy->cash_in_hand - request('paidAmount');
            $up = DB::table("dairy_info")->where("id", $dairyId)->update(["cash_in_hand" => $cashInHand]);
            if ($up == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_CASHUPDATE');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        // dairy balance updation
        $dairyCb = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->get()->first();
        if ($dairyCb == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($dairyCb->openingBalanceType == "debit") {
            $dairyCb->openingBalance -= 2 * $dairyCb->openingBalance;
        }

        $dbal = (float) $dairyCb->openingBalance - (float) $creditAmount;

        if ($dbal > 0) {$dbalType = "credit";} else { $dbal = str_replace("-", "", $dbal);
            $dbalType = "debit";}

        $dcbId = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->update([
            'openingBalance' => $dbal,
            'openingBalanceType' => $dbalType,
            'updated_at' => $currentTime,
        ]);
        if ($dcbId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_CURRENTBALANCE_UPDATE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        // supplier balance updation
        $suppCb = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->get()->first();
        if ($suppCb == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225' . json_encode($suppCb));
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($suppCb->openingBalanceType == "debit") {
            $suppCb->openingBalance -= 2 * $suppCb->openingBalance;
        }

        $sbal = (float) $suppCb->openingBalance + (float) $creditAmount;

        if ($sbal > 0) {$sbalType = "credit";} else { $sbal = str_replace("-", "", $sbal);
            $sbalType = "debit";}

        $scbId = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->update([
            'openingBalance' => $sbal,
            'openingBalanceType' => $sbalType,
            'updated_at' => $currentTime,
        ]);
        if ($scbId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: SUPP_CURRENTBALANCE_UPDATE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        DB::commit();

        Session::flash('msg', 'Product added Successfuly');
        Session::flash('alert-class', 'alert-success');
        return true;
    }


    /* product Edit Submit */
    public function productEditSubmit($req)
    {
        DB::beginTransaction();

        $currentTime = date('Y-m-d H:i:s');

        $dairy = DB::table("dairy_info")->where("id", session()->get("loginUserInfo")->dairyId)->get()->first();

        $pro = DB::table('products')->where(['productCode' => $req->productCode, "dairyId" => $dairy->id])->update([
            'productName' => $req->productName,
            'amount' => $req->productAmount,
            'updated_at' => $currentTime,
        ]);
        if ($pro == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PRODUCT_UPDATE');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        // if ($req->productUnit == (null || "") || $req->purchaseAmount == (null || "")) {
        //     DB::rollBack();
        //     Session::flash('msg', 'Product not updated');
        //     Session::flash('alert-class', 'alert-danger');
        //     return false;
        // }
        // $prod = DB::table('products')->where('productCode', $req->productCode)->get()->first();

        // $pro = DB::table('products')->where('productCode', $req->productCode)->update([
        //     // 'productUnit' => (int)$prod->productUnit+(int)$req->productUnit,
        //     'productUnit' => (int) $req->productUnit,
        //     // 'supplierId' => $req->supplier,
        //     'purchaseAmount' => $req->purchaseAmount,
        //     'updated_at' => $currentTime,
        // ]);

        // $supp = DB::table("suppliers")->where('id', $req->supplier)->get()->first();
        // if ($supp == (null || false)) {
        //     DB::rollBack();
        //     Session::flash('msg', 'There are some error occured. Error Code: SUPPLIER-225');
        //     Session::flash('alert-class', 'alert-danger');
        //     return false;
        // }

        // purchase entry
        // $purchaseId = DB::table('purchase_setups')->insertGetId([
        //     'dairyId' => $dairy->id,
        //     'status' => "true",
        //     'ledgerId' => $supp->ledgerId,
        //     'supplierName' => $supp->supplierFirmName,
        //     'itemPurchased' => $req->productName,
        //     'quantity' => $req->productUnit,
        //     'productCode' =>$req->productCode,
        //     'amount' => $req->purchaseAmount,
        //     'supplierCode' => $req->supplier,
        //     'purchaseType' => $req->paymentMethod,
        //     'date'  => date("Y-m-d"),
        //     'time'  => date("H:i:s"),
        //     'created_at' => $currentTime,
        // ]);
        // if ($purchaseId == (null || false)) {
        //     DB::rollBack();
        //     Session::flash('msg', 'There are some error occured. Error Code: PURCHASE_SETUP');
        //     Session::flash('alert-class', 'alert-danger');
        //     return false;
        // }

        // entry for dairy
        // $bs1 = DB::table('balance_sheet')->insertGetId([
        //     'dairyId' => $req->dairyId,
        //     'ledgerId' => $dairy->ledgerId,
        //     'srcDest' => $dairy->id,
        //     'transactionId' => $purchaseId,
        //     'transactionType' => "purchase_product",
        //     'amountType' => "dabit",
        //     'finalAmount' => $req->purchaseAmount,
        //     'remark' => "product supply to dairy from supplier",
        //     'created_at' => $currentTime,
        // ]);
        // if ($bs1 == (null || false)) {
        //     DB::rollBack();
        //     Session::flash('msg', 'There are some error occured. Error Code: DAIRY_BALANCE_SHEET-225');
        //     Session::flash('alert-class', 'alert-danger');
        //     return false;
        // }

        // // entry for supplier
        // if($req->paymentMethod =="dabit"){
        //     $bs2 = DB::table('balance_sheet')->insertGetId([
        //         'dairyId' => $req->dairyId,
        //         'ledgerId' => $supp->ledgerId,
        //         'srcDest' => $dairy->id,
        //         'transactionId' => $purchaseId,
        //         'transactionType' => "purchase_product",
        //         'amountType' => "credit",
        //         'finalAmount' => $req->purchaseAmount,
        //         'remark' => "product supply to dairy from supplier",
        //         'created_at' => $currentTime,
        //     ]);
        //     if ($bs2 == (null || false)) {
        //         DB::rollBack();
        //         Session::flash('msg', 'There are some error occured. Error Code: SUPP_BALANCE_SHEET-225');
        //         Session::flash('alert-class', 'alert-danger');
        //         return false;
        //     }
        // }

        // dairy balance updation
        // $dairyCb = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->get()->first();
        // if ($dairyCb == (null || false)) {
        //     DB::rollBack();
        //     Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225'.json_encode($suppCb));
        //     Session::flash('alert-class', 'alert-danger');
        //     return false;
        // }
        // $dbal = (float) $dairyCb->openingBalance - (float) $req->purchaseAmount;
        // if ($dbal > 0) {$dbalType = "debit";} else { $dbalType = "credit";}

        // $dcbId = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->update(
        //     [
        //         'openingBalance' => $dbal,
        //         'openingBalanceType' => $dbalType,
        //         'updated_at' => $currentTime,
        //     ]);
        // if ($dcbId == (null || false)) {
        //     DB::rollBack();
        //     Session::flash('msg', 'There are some error occured. Error Code: DAIRY_CURRENTBALANCE_UPDATE-225');
        //     Session::flash('alert-class', 'alert-danger');
        //     return false;
        // }

        // // supplier balance updation
        // if($req->paymentMethod =="dabit"){
        //     $suppCb = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->get()->first();
        //     if ($suppCb == (null || false)) {
        //         DB::rollBack();
        //         Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225'.json_encode($suppCb));
        //         Session::flash('alert-class', 'alert-danger');
        //         return false;
        //     }
        //     $sbal = (float) $suppCb->openingBalance + (float) $req->purchaseAmount;
        //     if ($sbal > 0) {$sbalType = "debit";} else { $sbalType = "credit";}

        //     $scbId = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->update(
        //         [
        //             'openingBalance' => $sbal,
        //             'openingBalanceType' => $sbalType,
        //             'updated_at' => $currentTime,
        //         ]);
        //     if ($scbId == (null || false)) {
        //         DB::rollBack();
        //         Session::flash('msg', 'There are some error occured. Error Code: SUPP_CURRENTBALANCE_UPDATE-225');
        //         Session::flash('alert-class', 'alert-danger');
        //         return false;
        //     }
        // }

        DB::commit();

        Session::flash('msg', 'Product rate updated successfuly');
        Session::flash('alert-class', 'alert-success');
        return true;
    }

    /* product Submit */
    public function productNewAPI($dairyId, $colMan)
    {

        DB::beginTransaction();

        $dairy = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $currentTime = date('Y-m-d H:i:s');
        $productCode = $dairyId . "P" . request('productCode');

        $creditAmount = (float) request('purchaseAmount') - (float) request('paidAmount');
        $amountType = "credit";
        if ($creditAmount == 0) {
            $amountType = "cash";
        }

        $supp = DB::table("suppliers")->where('id', request('supplierId'))->get()->first();
        if ($supp == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: SUPPLIER-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $submiteInfo = DB::table('products')->insertGetId([
            'dairyId' => $dairyId,
            'status' => 'true',
            'productCode' => $productCode,
            'productName' => request('productName'),
            'productUnit' => request('stock'),
            'amount' => request('sellingPrice'),
            'supplierId' => request('supplierId'),
            'purchaseAmount' => request('purchasePrice'),
            'created_at' => $currentTime,
        ]);

        if ($submiteInfo == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PRODUCT');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        // purchase entry
        $purchaseId = DB::table('purchase_setups')->insertGetId([
            'dairyId' => $dairyId,
            'status' => 'true',
            'ledgerId' => $supp->ledgerId,
            'supplierName' => $supp->supplierFirmName,
            'itemPurchased' => request('productName'),
            'quantity' => request('stock'),
            'productCode' => $productCode,
            'amount' => request('purchasePrice'),
            'paidAmount' => request('paidAmount'),
            'supplierCode' => request('supplierId'),
            'purchaseType' => $amountType,
            'date' => date("Y-m-d"),
            'time' => date("H:i:s"),
            'created_at' => $currentTime,
        ]);
        if ($purchaseId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: PURCHASE_SETUP');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($creditAmount != 0) {
            $bs1 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $dairy->ledgerId,
                'srcDest' => $supp->ledgerId,
                'colMan' => $colMan,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "debit",
                'finalAmount' => $creditAmount,
                'remark' => "product supply from supplier to dairy",
                'created_at' => $currentTime,
            ]);
            if ($bs1 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_BALANCE_SHEET-225');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $bs2 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $supp->ledgerId,
                'srcDest' => $dairy->ledgerId,
                'colMan' => $colMan,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "credit",
                'finalAmount' => $creditAmount,
                'remark' => "product supply from supplier to dairy ",
                'created_at' => $currentTime,
            ]);
            if ($bs2 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: SUPP_BALANCE_SHEET-225');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        if (request('paidAmount') != 0) {
            $bs1 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $dairy->ledgerId,
                'srcDest' => $supp->ledgerId,
                'colMan' => $colMan,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "cash",
                'finalAmount' => request('paidAmount'),
                'remark' => "product supply from supplier to dairy",
                'created_at' => $currentTime,
            ]);
            if ($bs1 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: DAIRY_BALANCE_SHEET-226');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
            $bs2 = DB::table('balance_sheet')->insertGetId([
                'dairyId' => $dairyId,
                'ledgerId' => $supp->ledgerId,
                'srcDest' => $dairy->ledgerId,
                'colMan' => $colMan,
                'transactionId' => $purchaseId,
                'transactionType' => "purchase_product",
                'amountType' => "cash",
                'finalAmount' => request('paidAmount'),
                'remark' => "product supply from supplier to dairy ",
                'created_at' => $currentTime,
            ]);
            if ($bs2 == (null || false)) {
                DB::rollBack();
                Session::flash('msg', 'There are some error occured. Error Code: SUPP_BALANCE_SHEET-226');
                Session::flash('alert-class', 'alert-danger');
                return false;
            }
        }

        // dairy balance updation
        $dairyCb = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->get()->first();
        if ($dairyCb == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225' . json_encode($suppCb));
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($dairyCb->openingBalanceType == "debit") {
            $dairyCb->openingBalance -= 2 * $dairyCb->openingBalance;
        }

        $dbal = (float) $dairyCb->openingBalance - (float) $creditAmount;

        if ($dbal > 0) {$dbalType = "credit";} else { $dbal = str_replace("-", "", $dbal);
            $dbalType = "debit";}

        $dcbId = DB::table('user_current_balance')->where('ledgerId', $dairy->ledgerId)->update([
            'openingBalance' => $dbal,
            'openingBalanceType' => $dbalType,
            'updated_at' => $currentTime,
        ]);
        if ($dcbId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_CURRENTBALANCE_UPDATE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        // supplier balance updation
        $suppCb = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->get()->first();
        if ($suppCb == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: DAIRY_SUPP_CURRENTBALANCE-225' . json_encode($suppCb));
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($suppCb->openingBalanceType == "debit") {
            $suppCb->openingBalance -= 2 * $suppCb->openingBalance;
        }

        $sbal = (float) $suppCb->openingBalance + (float) $creditAmount;

        if ($sbal > 0) {$sbalType = "credit";} else { $sbal = str_replace("-", "", $sbal);
            $sbalType = "debit";}

        $scbId = DB::table('user_current_balance')->where('ledgerId', $supp->ledgerId)->update([
            'openingBalance' => $sbal,
            'openingBalanceType' => $sbalType,
            'updated_at' => $currentTime,
        ]);
        if ($scbId == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error Code: SUPP_CURRENTBALANCE_UPDATE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        DB::commit();

        Session::flash('msg', 'Product added Successfuly');
        Session::flash('alert-class', 'alert-success');
        return true;
    }

}
