<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdvanceCredit extends Model
{
    //
    public function addAdvance($req)
    {
        DB::beginTransaction();

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        $colMan = Session::get('colMan');

        $user = null;
        if ($req->partyType == "member") {
            $user = DB::table("member_personal_info")->where(['memberPersonalCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "customer") {
            $user = DB::table("customer")->where(['customerCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "supplier") {
            $user = DB::table("suppliers")->where(['supplierCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }

        if ($user == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: USER-404');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $advanceId = DB::table('advance')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'partyCode' => $req->partyCode,
            'partyName' => $req->partyName . "",
            'partyType' => $req->partyType,
            'amount' => $req->amount,
            'date' => date("Y-m-d", strtotime($req->date)),
            'remark' => $req->remark . "",
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        if ($advanceId == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: SALE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        //current balance
        $cb = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->get()->first();

        if ($cb == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: USERCURRENTBALANCE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($cb->openingBalanceType == "debit") {
            $cb->openingBalance = -1 * $cb->openingBalance;
        }

        $bal = (float) $cb->openingBalance - (float) $req->amount;
        if ($bal > 0) {
            $balType = "credit";
            $minBalType = "cr";
        } else { 
            $balType = "debit";
            $minBalType = "dr";
            $bal = str_replace("-", "", $bal);
        }

        $bs = DB::table('balance_sheet')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'srcDest' => $dairyInfo->ledgerId,
            'colMan' => $colMan->userName,
            'transactionId' => $advanceId,
            'transactionType' => "advance",
            'amountType' => "debit",
            'finalAmount' => $req->amount,
            'currentBalance' => number_format($bal, 2, ".", "")." ".$minBalType,
            'remark' => "withdraw from dairy (" . $req->remark . ")",
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        $u = DB::table("advance")->where("id", $advanceId)->update(["txnId" => $bs]);

        if ($bs == (null || false) || $u == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: BALANCESHEET-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $cbId = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->update([
            'ledgerId' => $user->ledgerId,
            'openingBalance' => number_format($bal, 2, ".", ""),
            'openingBalanceType' => $balType,
            'updated_at' => date("Y-m-d H:i:s", time()),
        ]);

        $cashinhand = number_format($dairyInfo->cash_in_hand - $req->amount, 2, ".", "");
        $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);

        if (!$dbalup) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($cbId) {
            DB::commit();
            Session::flash('msg', 'Advance added..');
            Session::flash('alert-class', 'alert-success');
            return true;
        } else {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

    }

    public function addCredit($req)
    {
        DB::beginTransaction();

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        $colMan = Session::get('colMan');

        $user = null;

        if ($req->partyType == "member") {
            $user = DB::table("member_personal_info")->where(['memberPersonalCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "customer") {
            $user = DB::table("customer")->where(['customerCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "supplier") {
            $user = DB::table("suppliers")->where(['supplierCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }

        if ($user == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: MEMBER-404');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $crid = DB::table('credit')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'partyCode' => $req->partyCode,
            'partyName' => $req->partyName . "",
            'partyType' => $req->partyType,
            'amount' => $req->credit,
            'remark' => $req->remark . "",
            'date' => date("Y-m-d", strtotime($req->date)),
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        if ($crid == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: SALE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $cb = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->get()->first();

        if ($cb == (null || false) ) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: USERCURRENTBALANCE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if($cb->openingBalanceType == "debit") {
            $cb->openingBalance = -1 * $cb->openingBalance;
        }

        $bal = (float) $cb->openingBalance + (float) $req->credit;
        if ($bal > 0) {
            $balType = "credit";
            $minBalType = "cr";
        } else { 
            $balType = "debit";
            $minBalType = "dr";
            $bal = str_replace("-", "", $bal);
        }

        $bs = DB::table('balance_sheet')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'srcDest' => $dairyInfo->ledgerId,
            'colMan' => $colMan->userName,
            'transactionId' => $crid,
            'transactionType' => "credit",
            'amountType' => "credit",
            'finalAmount' => $req->credit,
            'currentBalance' => number_format($bal, 2, ".", "")." ".$minBalType,
            'remark' => "Credit to dairy (" . $req->remark . ")",
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        $u = DB::table("credit")->where("id", $crid)->update(["txnId" => $bs]);

        if ($bs == (null || false) || $u == (null || false) ) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: BALANCESHEET-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }



        $cbId = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->update([
            'ledgerId' => $user->ledgerId,
            'openingBalance' => number_format($bal, 2, ".", ""),
            'openingBalanceType' => $balType,
            'updated_at' => date("Y-m-d H:i:s", time()),
        ]);

        $cashinhand = number_format($dairyInfo->cash_in_hand + $req->credit, 2, ".", "");
        $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);

        if (!$dbalup) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($cbId) {
            DB::commit();
            Session::flash('msg', 'Credited Successfuly');
            Session::flash('alert-class', 'alert-success');
            return true;
        } else {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
    }

    public function addAdvanceAPI($req, $colMan)
    {

        DB::beginTransaction();

        $dairyId = $req->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $user = null;
        if ($req->partyType == "member") {
            $user = DB::table("member_personal_info")->where(['memberPersonalCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "customer") {
            $user = DB::table("customer")->where(['customerCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "supplier") {
            $user = DB::table("suppliers")->where(['supplierCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }

        if ($user == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: USER-404');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $advanceId = DB::table('advance')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'partyCode' => $req->partyCode,
            'partyName' => $req->partyName . "",
            'partyType' => $req->partyType,
            'amount' => $req->amount,
            'date' => date("Y-m-d", strtotime($req->date)),
            'remark' => $req->remark . "",
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        if ($advanceId == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: SALE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $cb = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->get()->first();

        if ($cb == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: USERCURRENTBALANCE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($cb->openingBalanceType == "debit") {
            $cb->openingBalance = -1 * $cb->openingBalance;
        }

        $bal = (float) $cb->openingBalance - (float) $req->amount;
        if ($bal > 0) {
            $balType = "credit";
            $minBalType = "cr";
        } else { 
            $balType = "debit";
            $minBalType = "dr";
            $bal = str_replace("-", "", $bal);
        }

        $bs = DB::table('balance_sheet')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'srcDest' => $dairyInfo->ledgerId,
            'colMan' => $colMan,
            'transactionId' => $advanceId,
            'transactionType' => "advance",
            'amountType' => "debit",
            'finalAmount' => $req->amount,
            'currentBalance' => number_format($bal, 2, ".", "")." ".$minBalType,
            'remark' => "withdraw from dairy (" . $req->remark . ")",
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        $u = DB::table("advance")->where("id", $advanceId)->update(["txnId" => $bs]);

        if ($bs == (null || false) || $u == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: BALANCESHEET-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $cbId = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->update([
            'ledgerId' => $user->ledgerId,
            'openingBalance' => number_format($bal, 2, ".", ""),
            'openingBalanceType' => $balType,
            'updated_at' => date("Y-m-d H:i:s", time()),
        ]);

        $cashinhand = number_format($dairyInfo->cash_in_hand - $req->amount, 2, ".", "");
        $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);

        if (!$dbalup) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($cbId) {
            DB::commit();
            Session::flash('msg', 'Advance added..');
            Session::flash('alert-class', 'alert-success');
            return true;
        } else {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
    }

    public function addCreditAPI($req, $colMan)
    {

        DB::beginTransaction();

        $dairyId = $req->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $user = null;

        if ($req->partyType == "member") {
            $user = DB::table("member_personal_info")->where(['memberPersonalCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "customer") {
            $user = DB::table("customer")->where(['customerCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }
        if ($req->partyType == "supplier") {
            $user = DB::table("suppliers")->where(['supplierCode' => $req->partyCode, "dairyId" => $dairyId])->get()->first();
        }

        if ($user == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: MEMBER-404');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $crid = DB::table('credit')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'partyCode' => $req->partyCode,
            'partyName' => $req->partyName . "",
            'partyType' => $req->partyType,
            'amount' => $req->credit,
            'remark' => $req->remark . "",
            'date' => date("Y-m-d", strtotime($req->date)),
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        if ($crid == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: SALE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $cb = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->get()->first();

        if ($cb == (null || false) ) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: USERCURRENTBALANCE-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($cb->openingBalanceType == "debit") {
            $cb->openingBalance = -1 * $cb->openingBalance;
        }

        $bal = (float) $cb->openingBalance + (float) $req->amount;
        if ($bal > 0) {
            $balType = "credit";
            $minBalType = "cr";
        } else { 
            $balType = "debit";
            $minBalType = "dr";
            $bal = str_replace("-", "", $bal);
        }

        $bs = DB::table('balance_sheet')->insertGetId([
            'dairyId' => $req->dairyId,
            'ledgerId' => $user->ledgerId,
            'srcDest' => $dairyInfo->ledgerId,
            'colMan' => $colMan->userName,
            'transactionId' => $crid,
            'transactionType' => "credit",
            'amountType' => "credit",
            'finalAmount' => $req->credit,
            'currentBalance' => number_format($bal, 2, ".", "")." ".$minBalType,
            'remark' => "Credit to dairy (" . $req->remark . ")",
            'created_at' => date("Y-m-d H:i:s", time()),
        ]);

        $u = DB::table("advance")->where("id", $advanceId)->update(["txnId" => $bs]);

        if ($bs == (null || false) || $u == (null || false) ) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error Code: BALANCESHEET-225');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $cbId = DB::table('user_current_balance')->where('ledgerId', $user->ledgerId)->update([
            'ledgerId' => $user->ledgerId,
            'openingBalance' => number_format($bal, 2, ".", ""),
            'openingBalanceType' => $balType,
            'updated_at' => date("Y-m-d H:i:s", time()),
        ]);

        $cashinhand = number_format($dairyInfo->cash_in_hand + $req->credit, 2, ".", "");
        $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);

        if (!$dbalup) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        if ($cbId) {
            DB::commit();
            Session::flash('msg', 'Credited Successfuly');
            Session::flash('alert-class', 'alert-success');
            return true;
        } else {
            DB::rollback();
            Session::flash('msg', 'There are some error occured.');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
    }

}
