<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class expense_setup extends Model
{
    public function expenseFormSubmit($req)
    {
        $currentTime = date('Y-m-d H:i:s');

        $colMan = Session::get("colMan");

        $dairyInfo = DB::table("dairy_info")->where("id", $colMan->dairyId)->get()->first();

        $expt = DB::table("expenses")->where(["dairyId" => $colMan->dairyId, "id" => $req->expenseType])->get()->first();
        if ($expt == (null || false)) {
            $returnSuccessArray = array("Success" => "False", "Message" => "Expense not found");
            $returnSuccessJson = json_encode($returnSuccessArray);
            return $returnSuccessJson;
        }

        $userCurrentBalance = DB::table("user_current_balance")
            ->where('ledgerId', $req->ledgerId)
            ->get()->first();

        $newUserBalance = '';
        $minBalType = '';
        if ($userCurrentBalance->openingBalanceType == "credit") {
            $newUserBalance = $userCurrentBalance->openingBalance - $req->amount;
            if ($newUserBalance < 0) {
                $newUserBalance = str_replace("-", "", $newUserBalance);
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $req->ledgerId)
                    ->update([
                        'openingBalance' => $newUserBalance,
                        'openingBalanceType' => "debit",
                    ]);
                $minBalType = "dr";
            } else {
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $req->ledgerId)
                    ->update([
                        'openingBalance' => $newUserBalance,
                        'openingBalanceType' => "credit",
                    ]);
                $minBalType = "cr";
            }
        } else {
            $newUserBalance = $userCurrentBalance->openingBalance + $req->amount;
            /* update new amount in use account */
            $updateReturn = DB::table('user_current_balance')
                ->where('ledgerId', $req->ledgerId)
                ->update([
                    'openingBalance' => $newUserBalance,
                    'openingBalanceType' => "debit",
                ]);
            $minBalType = "dr";
        }

        $submiteInfo = DB::table('expense_setups')->insertGetId([
            'dairyId' => $req->dairyId,
            'status' => $req->status,
            'ledgerName' => $req->ledgerId,
            'partyName' => $req->partyName,
            'date' => date("Y-m-d", strtotime($req->date)),
            'time' => $req->time,
            'expenseType' => $req->expenseType,
            'paymentMode' => $req->paymentMode,
            'amount' => $req->amount,
            'remarks' => $req->remarks,
            'created_at' => $currentTime,
        ]);

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $req->ledgerId,
            'transactionId' => $submiteInfo,
            'dairyId' => $req->dairyId,
            'transactionType' => 'expense_setups',
            'srcDest' => $req->ledgerId,
            'colMan' => $colMan->userName,
            'remark' => $expt->expenseHeadName,
            'amountType' => $req->paymentMode,
            'finalAmount' => $req->amount,
            'currentBalance' => number_format($newUserBalance,"2",".","")." ".$minBalType,
            'created_at' => $currentTime,
        ]);

        if ($req->paymentMode == 'cash') {
            $cashinhand = number_format($dairyInfo->cash_in_hand - $req->amount, 2, ".", "");
            $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);
        }

        $returnSuccessArray = array("Success" => "True", "Message" => "Expense Submited");
        $returnSuccessJson = json_encode($returnSuccessArray);
        return $returnSuccessJson;
    }

    public function expenseNewAPI($dairyId, $colMan)
    {
        $currentTime = date('Y-m-d H:i:s');

        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $userCurrentBalance = DB::table("user_current_balance")
            ->where('ledgerId', $dairyInfo->ledgerId)
            ->get()->first();

        $newUserBalance = '';
        $minBalType = '';
        if ($userCurrentBalance->openingBalanceType == "credit") {
            $newUserBalance = $userCurrentBalance->openingBalance - request("amount");
            if ($newUserBalance < 0) {
                $newUserBalance = str_replace("-", "", $newUserBalance);
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $dairyInfo->ledgerId)
                    ->update([
                        'openingBalance' => $newUserBalance,
                        'openingBalanceType' => "debit",
                    ]);
                $minBalType = "dr";
            } else {
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $dairyInfo->ledgerId)
                    ->update([
                        'openingBalance' => $newUserBalance,
                        'openingBalanceType' => "credit",
                    ]);
                $minBalType = "cr";
            }
        } else {
            $newUserBalance = $userCurrentBalance->openingBalance + request("amount");
            /* update new amount in use account */
            $updateReturn = DB::table('user_current_balance')
                ->where('ledgerId', $dairyInfo->ledgerId)
                ->update([
                    'openingBalance' => $newUserBalance,
                    'openingBalanceType' => "debit",
                ]);
            $minBalType = "dr";
        }

        $submiteInfo = DB::table('expense_setups')->insertGetId([
            'dairyId' => $dairyId,
            'status' => "true",
            'ledgerName' => $dairyInfo->ledgerId,
            'partyName' => $dairyInfo->dairyName,
            'date' => date("Y-m-d", strtotime(request("date"))),
            'time' => request("time"),
            'expenseType' => request("expenseHeadId"),
            'paymentMode' => strtolower(request("paymentMode")),
            'amount' => request("amount"),
            'created_at' => $currentTime,
        ]);

        $balanceSheetSubmit = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $dairyInfo->ledgerId,
            'transactionId' => $submiteInfo,
            'dairyId' => $dairyId,
            'transactionType' => 'expense_setups',
            'srcDest' => $dairyInfo->ledgerId,
            'colMan' => $colMan,
            'remark' => request("expenseHeadId"),
            'amountType' => strtolower(request("paymentMode")),
            'finalAmount' => request("amount"),
            'currentBalance' => number_format($newUserBalance,"2",".","")." ".$minBalType,
            'created_at' => $currentTime,
        ]);

        if (strtolower(request("paymentMode")) == 'cash') {
            $cashinhand = number_format($dairyInfo->cash_in_hand - request("amount"), 2, ".", "");
            $dbalup = DB::table('dairy_info')->where('id', $dairyInfo->id)->update(['cash_in_hand' => $cashinhand]);
        }

        return ["error" => false, "msg" => ""];
    }
}
