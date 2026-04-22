<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mail;

class dailyTransaction extends Model
{
    public function DailyTransactionSubmit($request)
    {
        DB::beginTransaction();
        $currentTime = date('Y-m-d H:i:s');
        $colMan = Session::get("colMan");
        $dairyInfo = DB::table("dairy_info")
            ->where('id', $request->dairyId)
            ->get()->first();

        if ($colMan == null || $dairyInfo == (null || "" || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: DAIRY_UNKNOWN"];
        }

        $ratecard = new \App\Http\Controllers\RateCardController();
        $ratecartrate = $ratecard->fatSnfRateCardvalue($request);
        if ($ratecartrate['error']) {
            DB::rollback();
            return ["error" => true, "msg" => $ratecartrate['msg']];
        } else {
            $request->price = $ratecartrate['amount'];
            $request->milkType = $ratecartrate['milkType'];
        }

        $amount = number_format((float) $request->price * (float) $request->quantity, 2, ".", "");

        $fatkg = $request->quantity * $request->fat / 100;
        $snkg  = $request->quantity * $request->snf / 10000;

        $submiteInfoDailyTransactions = DB::table('daily_transactions')->insertGetId([
            'dairyId'     => $request->dairyId,
            'status'      => "true",
            'memberCode'  => $request->memberCode,
            'memberName'  => $request->memberName,
            'date'        => date("Y-m-d", strtotime($request->date)),
            'milkType'    => $request->milkType,
            'milkQuality' => $request->quantity,
            'shift'       => strtolower($request->dailyShift),
            'fat'         => $request->fat,
            'snf'         => $request->snf,
            'rate'        => $request->price,
            'amount'      => $amount,
            'fatkg'       => $fatkg,
            'snfkg'       => $snkg,
            'created_at'  => $currentTime,
        ]);

        $memberInfo = DB::table("member_personal_info")
            ->where('memberPersonalCode', $request->memberCode)
            ->where("dairyId", $dairyInfo->id)
            ->get()->first();
        if ($submiteInfoDailyTransactions == (null || "") || $memberInfo == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: daily_transactions_NOT_FINISHED"];
        }

        $userCurrentBalance = DB::table("user_current_balance")
            ->where('ledgerId', $memberInfo->ledgerId)
            ->get()->first();
        $currentDairyBalance = DB::table("user_current_balance")
            ->where('ledgerId', $dairyInfo->ledgerId)
            ->get()->first();
        if ($userCurrentBalance == (null || "") || $currentDairyBalance == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: CURRENT_BALANCE_NOT_FOUND"];
        }

        /* accouting calculation */
        /* user account changes */
        $newUserBalance = '';
        $minBalType = '';
        if ($userCurrentBalance->openingBalanceType == "debit") {
            $newUserBalance = number_format((float) $userCurrentBalance->openingBalance - (float) $amount, 2, ".", "");
            if ($newUserBalance < 0) {
                $newUserBalance = str_replace("-", "", $newUserBalance);
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $memberInfo->ledgerId)
                    ->update([
                        'openingBalance' => round($newUserBalance, 2),
                        'openingBalanceType' => "credit",
                        "updated_at" => $currentTime,
                    ]);
                    $minBalType = "cr";
            } else {
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $memberInfo->ledgerId)
                    ->update([
                        'openingBalance' => $newUserBalance,
                        'openingBalanceType' => "debit",
                    ]);
                    $minBalType = "dr";
            }
        } else {
            $newUserBalance = number_format((float) $userCurrentBalance->openingBalance + (float) $amount, 2, ".", "");
            $updateReturn = DB::table('user_current_balance')
                ->where('ledgerId', $memberInfo->ledgerId)
                ->update([
                    'openingBalance' => round($newUserBalance, 2),
                    'openingBalanceType' => "credit",
                ]);
            $minBalType = "cr";
        }

        /* dairy account changes */
        if ($currentDairyBalance->openingBalanceType == "debit") {
            $newDairyBalance = number_format((float) $currentDairyBalance->openingBalance + (float) $amount, 2, ".", "");
            $updateReturn = DB::table('user_current_balance')
                ->where('ledgerId', $dairyInfo->ledgerId)
                ->update([
                    'openingBalance' => round($newDairyBalance, 2),
                    'openingBalanceType' => "debit",
                    "updated_at" => $currentTime,
                ]);
        } else {
            $newDairyBalance = number_format((float) $currentDairyBalance->openingBalance - (float) $amount, 2, ".", "");
            if ($newDairyBalance < 0) {
                $currentBalace = str_replace("-", "", $newDairyBalance);
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $dairyInfo->ledgerId)
                    ->update([
                        'openingBalance' => round($currentBalace, 2),
                        'openingBalanceType' => "debit",
                        "updated_at" => $currentTime,
                    ]);
            } else {
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $dairyInfo->ledgerId)
                    ->update([
                        'openingBalance' => round($newDairyBalance, 2),
                        'openingBalanceType' => "credit",
                        "updated_at" => $currentTime,
                    ]);
            }
        }

        if (!isset($updateReturn) || $updateReturn == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: CURRENT_BALANCE_UPDATE_ERROR"];
        }

        /* balance sheet entry */
        /* user entry in balance sheet */
        $submiteInfo = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $memberInfo->ledgerId,
            'dairyId' => $request->dairyId,
            'transactionId' => $submiteInfoDailyTransactions,
            'srcDest' => $dairyInfo->ledgerId,
            'colMan' => $colMan->userName,
            'transactionType' => 'daily_transactions',
            'remark' => 'Milk collection (' . $request->quantity . ' ltr)',
            'amountType' => 'credit',
            'finalAmount' => $amount,
            'currentBalance' => number_format($newUserBalance,"2",".","")." ".$minBalType,
            'created_at' => $currentTime,
        ]);


        if ($submiteInfo == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: balance_sheet_UPDATE_ERROR_1"];
        }

        $dairyPropritorInfo = DB::table("dairy_propritor_info")
            ->where('dairyId', $request->dairyId)
            ->get()->first();

        DB::commit();

        $message = "<html><head></head><body><table> <tr><td> Type Of Milk &nbsp; :- &nbsp; </td><td>" . $request->milkType . "</td></tr><tr><td> Transactions Amount &nbsp; :- &nbsp; </td><td>" . $amount . "</td></tr><tr><td> Transactions Date &nbsp; :- &nbsp;</td><td>" . $request->date . "</td></tr><tr><td> Transactions Member Name &nbsp; :- &nbsp; </td><td>" . $memberInfo->memberPersonalName . "</td></tr><tr><td> Dairy Name &nbsp; :- &nbsp; </td><td>" . $dairyInfo->society_name . "</td></tr></table></body></html>";

        $to = $memberInfo->memberPersonalEmail;
        $subject = "Daily Transactions";
        $txt = $message;
        $headers = $dairyPropritorInfo->dairyPropritorEmail;

        // mail($to,$subject,$txt,$headers);

        $ub = DB::table("user_current_balance")
            ->where('ledgerId', $memberInfo->ledgerId)
            ->get()->first();

        $ub->openingBalance = number_format($ub->openingBalance, 2, ".", "");
        if ($ub->openingBalanceType == "credit") {
            $bal = $ub->openingBalance . " CR";
        } else {
            $bal = $ub->openingBalance . " DR";
        }

        $this->markActiveMember($memberInfo, $dairyInfo);

        return ["error" => false, "msg" => "Transaction Added.", "memId" => $memberInfo->id, "transId" => $submiteInfoDailyTransactions,
            "amount" => $amount, "mobile" => $memberInfo->memberPersonalMobileNumber, "rate" => $request->price,
            "memberName" => $memberInfo->memberPersonalName, "currentBalance" => $bal, "memberId" => $memberInfo->id,
        ];
    }

    public function markActiveMember($mem, $dairy)
    {
        $res = DB::table('inactive_members')->where(["ledgerId" => $mem->ledgerId])->delete();
        if ($res) {
            $data = ["ledgerId" => $dairy->ledgerId,
                "notification" => $mem->memberPersonalName . " - " . $mem->memberPersonalCode . " has now started milk collection.",
                "type" => "activeMember",
                "created_at" => date("Y-m-d H:i:s")];
            DB::table('notifications')->insertGetId($data);
        }
    }

    public function DailyTransactionEditSubmit($request)
    {
        $currentTime = date('Y-m-d H:i:s');

        if ($request->dailyTransactionId == (null || "")) {
            Session::flash('msg', 'There are some error occured. Error: TRANSACTION_ID_NOT_FOUND');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $colMan = Session::get("colMan");

        $dairyInfo = DB::table("dairy_info")
            ->where('id', $request->dairyId)
            ->get()->first();

        DB::beginTransaction();

        $ratecard = new \App\Http\Controllers\RateCardController();
        $ratecartrate = $ratecard->fatSnfRateCardvalue($request);
        if ($ratecartrate['error']) {
            DB::rollback();
            return ["error" => true, "msg" => $ratecartrate['msg']];
        } else {
            $request->price = $ratecartrate['amount'];
            $request->milkType = $ratecartrate['milkType'];
        }

        $amount = number_format((float) $request->price * (float) $request->quantity, 2, ".", "");

        $oldTrans = DB::table('daily_transactions')
            ->where('id', $request->dailyTransactionId)
            ->get()->first();

        if ($oldTrans == (null || "")) {
            DB::rollBack();

            Session::flash('msg', 'There are some error occured. Error: TRANSACTION_HISTORY_NOT_FOUND');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $fatkg = $request->quantity * $request->fat / 100;
        $snkg  = $request->quantity * $request->snf / 10000;
        $updateReturn = DB::table('daily_transactions')
            ->where('id', $request->dailyTransactionId)
            ->update([
                'milkQuality' => $request->quantity,
                'fat' => $request->fat,
                'snf' => $request->snf,
                'rate' => $request->price,
                'milkType' => $request->milkType,
                'amount' => $amount,
            'fatkg'       => $fatkg,
            'snfkg'       => $snkg,
                'updated_at' => $currentTime,
            ]);
        if ($updateReturn == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: TRANSACTION_NOT_UPDATED');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $memberInfo = DB::table("member_personal_info")
            ->where('memberPersonalCode', $request->memberCode)
            ->where("dairyId", $dairyInfo->id)
            ->get()->first();

        $userCurrentBalance = DB::table("user_current_balance")
            ->where('ledgerId', $memberInfo->ledgerId)
            ->get()->first();

        $currentDairyBalance = DB::table("user_current_balance")
            ->where('ledgerId', $dairyInfo->ledgerId)
            ->get()->first();

        if ($memberInfo == (null || "") || $userCurrentBalance == (null || "") || $currentDairyBalance == (null || "")) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: BALANCE_NOT_AVAILABLE');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $diffAmount = (float) $amount - (float) $oldTrans->amount;

        /* user account changes */
        $newUserBalance = '';
        if ($userCurrentBalance->openingBalanceType == "debit") {
            $newUserBalance = -(float) $userCurrentBalance->openingBalance + (float) $diffAmount;
        } else {
            $newUserBalance = (float) $userCurrentBalance->openingBalance + (float) $diffAmount;
        }

        $minBalType = '';
        if ($newUserBalance < 0) {
            $newUserBalance = str_replace("-", "", $newUserBalance);
            $bType = "debit";
            $minBalType = "dr";
        } else {
            $bType = "credit";
            $minBalType = "cr";
        }
        $updateReturn = DB::table('user_current_balance')
            ->where('ledgerId', $memberInfo->ledgerId)
            ->update([
                'openingBalance' => round($newUserBalance, 2),
                'openingBalanceType' => $bType,
                "updated_at" => $currentTime,
            ]);
        if ($updateReturn == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: USER_CURRENT_BALANCE_UPDATE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        /* balance sheet entry */
        /* user entry in balance sheet */
        $oldBalSheet = DB::table("balance_sheet")->where(["transactionId" => $request->dailyTransactionId,
            "dairyId" => $dairyInfo->id,
            "ledgerId" => $memberInfo->ledgerId,
            "transactionType" => "daily_transactions", "status" => "true"])->get()->first();
        if ($oldBalSheet == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: TRANSACTION_NOT_FOUND');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }
        $updateReturn1 = DB::table('balance_sheet')
            ->where(["transactionId" => $request->dailyTransactionId,
                "dairyId" => $dairyInfo->id,
                "ledgerId" => $memberInfo->ledgerId,
                "transactionType" => "daily_transactions"])
            ->update([
                'status' => "false",
                'updated_at' => $currentTime,
            ]);

        $oldBalSheet->finalAmount = $amount;
        $oldBalSheet->currentBalance = number_format($newUserBalance,"2",".","")." ".$minBalType;
        $oldBalSheet->created_at = $currentTime;
        $oldBalSheet->colMan = $colMan->userName;
        $oldBalSheet->remark = "Milk Collection (" . $request->quantity . " ltr)";

        unset($oldBalSheet->id, $oldBalSheet->updated_at);

        $updateReturn2 = DB::table('balance_sheet')->insertGetId((array) $oldBalSheet);

        if ($updateReturn1 == (null || false) || $updateReturn2 == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: USER_BALANCE_SHEET_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        Session::flash('msg', 'Transaction updated with new data.');
        Session::flash('alert-class', 'alert-success');
        DB::commit();

        /* get dairy info */
        // $dairyInfo = DB::table("dairy_info")
        //       ->where('id', $request->dairyId )
        //       ->get()->first();

        // $dairyPropritorInfo = DB::table("dairy_propritor_info")
        //       ->where('dairyId', $request->dairyId )
        //       ->get()->first();

        // $message = "<html><head></head><body><table> <tr><td> Type Of Milk &nbsp; :- &nbsp; </td><td>". $request->milkType ."</td></tr><tr><td> Transactions Amount &nbsp; :- &nbsp; </td><td>". $request->amount ."</td></tr><tr><td> Transactions Date &nbsp; :- &nbsp;</td><td>". $request->date ."</td></tr><tr><td> Transactions Member Name &nbsp; :- &nbsp; </td><td>". $memberInfo->memberPersonalName ."</td></tr><tr><td> Dairy Name &nbsp; :- &nbsp; </td><td>". $dairyInfo->society_name ."</td></tr></table></body></html>" ;

        // $to = $memberInfo->memberPersonalEmail;
        // $subject = "Daily Transactions";
        // $txt = $message ;
        // $headers = $dairyPropritorInfo->dairyPropritorEmail;

        // mail($to,$subject,$txt,$headers);

        return true;

    }

    public function DailyTransactionDelete($request)
    {
        // echo json_encode($request->all()); exit();
        $currentTime = date("Y-m-d");

        DB::beginTransaction();

        $dairyId = session()->get('loginUserInfo')->dairyId;

        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $memberInfo = DB::table("member_personal_info")
            ->where('memberPersonalCode', $request->memberCode)
            ->where("dairyId", $dairyId)
            ->first();

        if ($memberInfo == (null || "")) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error: DATA_NOT_RETRIVED2');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        $userCurrentBalance = DB::table("user_current_balance")
            ->where('ledgerId', $memberInfo->ledgerId)
            ->get()->first();

        $currentDairyBalance = DB::table("user_current_balance")
            ->where('ledgerId', $dairyInfo->ledgerId)
            ->get()->first();

        $currentTransaction = DB::table("daily_transactions")
            ->where('id', $request->transId)
            ->get()->first();

        $updateReturn = DB::table('daily_transactions')
            ->where('id', $request->transId)
            ->update([
                'status' => 'false',
            ]);

        if ($memberInfo == (null || "") || $userCurrentBalance == (null || "") || $currentDairyBalance == (null || "") ||
            $currentTransaction == (null || "") || $updateReturn == (null || false)) {
            DB::rollback();
            Session::flash('msg', 'There are some error occured. Error: DATA_NOT_RETRIVED');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        /* user account changes */
        if ($userCurrentBalance->openingBalanceType == "credit") {
            $newUserBalance = (float) $userCurrentBalance->openingBalance - (float) $currentTransaction->amount;
            if ($newUserBalance < 0) {
                $newUserBalance = str_replace("-", "", $newUserBalance);
                $bType = "debit";
            } else { $bType = "credit";}
        } else {
            $newUserBalance = (float) $userCurrentBalance->openingBalance + (float) $currentTransaction->amount;
            $bType = "debit";
        }

        $updateReturn = DB::table('user_current_balance')
            ->where('ledgerId', $memberInfo->ledgerId)
            ->update([
                'openingBalance' => round($newUserBalance, 2),
                'openingBalanceType' => $bType,
                "updated_at" => $currentTime,
            ]);
        if ($updateReturn == (null || false)) {
            DB::rollBack();
            Session::flash('msg', 'There are some error occured. Error: USER_CURRENT_BALANCE_ERROR');
            Session::flash('alert-class', 'alert-danger');
            return false;
        }

        DB::commit();

        Session::flash('msg', 'Delete Successfully');
        Session::flash('alert-class', 'alert-success');
        return true;

        // dd($request->transactionId);

        // /* get dairy info */
        // $dailyTransactionsInfo = DB::table("daily_transactions")
        //     ->where('id', $request->transactionId)
        //     ->get()->first();

        // $dairyInfo = DB::table("dairy_info")
        //     ->where('id', $request->dairyId)
        //     ->get()->first();

        // $dairyPropritorInfo = DB::table("dairy_propritor_info")
        //     ->where('dairyId', $request->dairyId)
        //     ->get()->first();

        // $message = "<html><head></head><body><table> <tr><td> Type Of Milk &nbsp; :- &nbsp; </td><td>" . $dailyTransactionsInfo->milkType . "</td></tr><tr><td> Transactions Amount &nbsp; :- &nbsp; </td><td>" . $dailyTransactionsInfo->amount . "</td></tr><tr><td> Transactions Date &nbsp; :- &nbsp;</td><td>" . $dailyTransactionsInfo->date . "</td></tr><tr><td> Transactions Member Name &nbsp; :- &nbsp; </td><td>" . $memberInfo->memberPersonalName . "</td></tr><tr><td> Dairy Name &nbsp; :- &nbsp; </td><td>" . $dairyInfo->society_name . "</td></tr></table></body></html>";

        // $to = $memberInfo->memberPersonalEmail;
        // $subject = "Daily Transactions";
        // $txt = $message;
        // $headers = $dairyPropritorInfo->dairyPropritorEmail;

        // mail($to, $subject, $txt, $headers);

        // $returnSuccessArray = array("Success" => "True", "Message" => "Daily Transaction Entry Successfully Delated");
        // $returnSuccessJson = json_encode($returnSuccessArray);
        // return $returnSuccessJson;
    }

    public function DailyTransactionResendNoti($request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        /* get dairy info */
        $memberInfo = DB::table("member_personal_info")
            ->where('memberPersonalCode', $request->memberCode)
            ->where('dairyId', $dairyId)
            ->get()->first();

        $dailyTransactionsInfo = DB::table("daily_transactions")
            ->where('id', $request->transactionId)
            ->get()->first();

        $dairyPropritorInfo = DB::table("dairy_propritor_info")
            ->where('dairyId', $request->dairyId)
            ->get()->first();
        // dd();

        $message = "<html><head></head><body><table> <tr><td> Type Of Milk &nbsp; :- &nbsp; </td><td>" . $dailyTransactionsInfo->milkType . "</td></tr><tr><td> Transactions Amount &nbsp; :- &nbsp; </td><td>" . $dailyTransactionsInfo->amount . "</td></tr><tr><td> Transactions Date &nbsp; :- &nbsp;</td><td>" . $dailyTransactionsInfo->date . "</td></tr><tr><td> Transactions Member Name &nbsp; :- &nbsp; </td><td>" . $memberInfo->memberPersonalName . "</td></tr><tr><td> Dairy Name &nbsp; :- &nbsp; </td><td>" . $dairyInfo->society_name . "</td></tr></table></body></html>";

        $to = $memberInfo->memberPersonalEmail;
        $subject = "Daily Transactions";
        $txt = $message;

        // $headers =  'MIME-Version: 1.0' . "\r\n";
        // $headers .= 'From: '.$dairyInfo->society_name.' <'.$dairyPropritorInfo->dairyPropritorEmail.'>' . "\r\n";
        // $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // $headers = $dairyPropritorInfo->dairyPropritorEmail;

        // mail($to,$subject,$txt,$headers);

        $data = [
            'txt' => $message,
        ];

        Mail::send('test', $data, function ($message) {
            // $message->from($dairyPropritorInfo->dairyPropritorEmail, $dairyInfo->society_name);
            $message->from('shivamsharma2096@gmail.com', 'shivam sharma');
            $message->subject("asfasdf");
            $message->to('shivamsharma2096@gmail.com');
        });
    }

    public function DailyTransactionSubmitAPI($request)
    {
        DB::beginTransaction();
        $currentTime = date('Y-m-d H:i:s');
        $colMan = Session::get("colMan");
        $dairyInfo = DB::table("dairy_info")
            ->where('id', $request->dairyId)
            ->get()->first();

        if ($colMan == null || $dairyInfo == (null || "" || false)) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: DAIRY_UNKNOWN"];
        }

        $ratecard = new \App\Http\Controllers\RateCardController();
        $ratecartrate = $ratecard->fatSnfRateCardvalue($request);
        if ($ratecartrate['error']) {
            DB::rollback();
            return ["error" => true, "msg" => $ratecartrate['msg']];
        } else {
            $request->price = $ratecartrate['amount'];
            $request->milkType = $ratecartrate['milkType'];
        }

        $amount = number_format((float) $request->price * (float) $request->quantity, 2, ".", "");

        $submiteInfoDailyTransactions = DB::table('daily_transactions')->insertGetId([
            'dairyId' => $request->dairyId,
            'status' => "true",
            'memberCode' => $request->memberCode,
            'memberName' => $request->memberName,
            // 'date' =>  date("H:i", strtotime($request->date)),
            'date' => date("Y-m-d", strtotime($request->date)),
            'milkType' => $request->milkType,
            'milkQuality' => $request->quantity,
            'shift' => strtolower($request->dailyShift),
            'fat' => $request->fat,
            'snf' => $request->snf,
            'rate' => $request->price,
            'amount' => $amount,
            'created_at' => $currentTime,
        ]);
        $memberInfo = DB::table("member_personal_info")
            ->where('memberPersonalCode', $request->memberCode)
            ->where("dairyId", $dairyInfo->id)
            ->get()->first();
        if ($submiteInfoDailyTransactions == (null || "") || $memberInfo == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: daily_transactions_NOT_FINISHED"];
        }

        $userCurrentBalance = DB::table("user_current_balance")
            ->where('ledgerId', $memberInfo->ledgerId)
            ->get()->first();
        $currentDairyBalance = DB::table("user_current_balance")
            ->where('ledgerId', $dairyInfo->ledgerId)
            ->get()->first();
        if ($userCurrentBalance == (null || "") || $currentDairyBalance == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: CURRENT_BALANCE_NOT_FOUND"];
        }

        /* accouting calculation */
        /* user account changes */
        $newUserBalance = '';
        $minBalType = '';
        if ($userCurrentBalance->openingBalanceType == "debit") {
            $newUserBalance = (float) $userCurrentBalance->openingBalance - (float) $amount;
            if ($newUserBalance < 0) {
                $newUserBalance = str_replace("-", "", $newUserBalance);
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $memberInfo->ledgerId)
                    ->update([
                        'openingBalance' => round($newUserBalance, 2),
                        'openingBalanceType' => "credit",
                        "updated_at" => $currentTime,
                    ]);
                $minBalType = "cr";
            } else {
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $memberInfo->ledgerId)
                    ->update([
                        'openingBalance' => $newUserBalance,
                        'openingBalanceType' => "debit",
                    ]);
                $minBalType = "dr";
            }
        } else {
            $newUserBalance = (float) $userCurrentBalance->openingBalance + (float) $amount;
            $updateReturn = DB::table('user_current_balance')
                ->where('ledgerId', $memberInfo->ledgerId)
                ->update([
                    'openingBalance' => round($newUserBalance, 2),
                    'openingBalanceType' => "credit",
                ]);
            $minBalType = "cr";
        }

        /* dairy account changes */
        if ($currentDairyBalance->openingBalanceType == "debit") {
            $newDairyBalance = (float) $currentDairyBalance->openingBalance + (float) $amount;
            $updateReturn = DB::table('user_current_balance')
                ->where('ledgerId', $dairyInfo->ledgerId)
                ->update([
                    'openingBalance' => round($newDairyBalance, 2),
                    'openingBalanceType' => "debit",
                    "updated_at" => $currentTime,
                ]);
        } else {
            $newDairyBalance = (float) $currentDairyBalance->openingBalance - (float) $amount;
            if ($newDairyBalance < 0) {
                $currentBalace = str_replace("-", "", $newDairyBalance);
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $dairyInfo->ledgerId)
                    ->update([
                        'openingBalance' => round($currentBalace, 2),
                        'openingBalanceType' => "debit",
                        "updated_at" => $currentTime,
                    ]);
            } else {
                $updateReturn = DB::table('user_current_balance')
                    ->where('ledgerId', $dairyInfo->ledgerId)
                    ->update([
                        'openingBalance' => round($newDairyBalance, 2),
                        'openingBalanceType' => "credit",
                        "updated_at" => $currentTime,
                    ]);
            }
        }

        if (!isset($updateReturn) || $updateReturn == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: CURRENT_BALANCE_UPDATE_ERROR"];
        }

        /* balance sheet entry */
        /* user entry in balance sheet */
        $submiteInfo = DB::table('balance_sheet')->insertGetId([
            'ledgerId' => $memberInfo->ledgerId,
            'dairyId' => $request->dairyId,
            'transactionId' => $submiteInfoDailyTransactions,
            'srcDest' => $dairyInfo->ledgerId,
            'colMan' => $colMan->userName,
            'transactionType' => 'daily_transactions',
            'remark' => 'Milk collection (' . $request->quantity . ' ltr)',
            'amountType' => 'credit',
            'finalAmount' => $amount,
            'currentBalance' => number_format($newUserBalance,"2",".","")." ".$minBalType,
            'created_at' => $currentTime,
        ]);

        if ($submiteInfo == (null || "")) {
            DB::rollback();
            return ["error" => true, "msg" => "some error has occured, Error: balance_sheet_UPDATE_ERROR_1"];
        }

        $dairyPropritorInfo = DB::table("dairy_propritor_info")
            ->where('dairyId', $request->dairyId)
            ->get()->first();

        DB::commit();

        $message = "<html><head></head><body><table> <tr><td> Type Of Milk &nbsp; :- &nbsp; </td><td>" . $request->milkType . "</td></tr><tr><td> Transactions Amount &nbsp; :- &nbsp; </td><td>" . $amount . "</td></tr><tr><td> Transactions Date &nbsp; :- &nbsp;</td><td>" . $request->date . "</td></tr><tr><td> Transactions Member Name &nbsp; :- &nbsp; </td><td>" . $memberInfo->memberPersonalName . "</td></tr><tr><td> Dairy Name &nbsp; :- &nbsp; </td><td>" . $dairyInfo->society_name . "</td></tr></table></body></html>";

        $to = $memberInfo->memberPersonalEmail;
        $subject = "Daily Transactions";
        $txt = $message;
        $headers = $dairyPropritorInfo->dairyPropritorEmail;

        // mail($to,$subject,$txt,$headers);

        return ["error" => false, "msg" => "Transaction Added.", "memId" => $memberInfo->id, "transId" => $submiteInfoDailyTransactions];
    }

}
