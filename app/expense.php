<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class expense extends Model
{
    public function expenseSubmit($request)
    {
        $col = Session::get("loginUserInfo");

        $currentTime = date('Y-m-d H:i:s');
        $submiteInfo = DB::table('expenses')->insertGetId([
            'dairyId' => $col->dairyId,
            'status' => "true",
            'expenseHeadCode' => $request->expenseCode,
            'expenseHeadName' => $request->expenseName,
            'expenseDescription' => $request->expenseDesc . "",
            'created_at' => $currentTime,
        ]);

        if ($submiteInfo) {
            return true;
        } else {
            return false;
        }
    }

    public function expenseHeadSubmitAPI($request, $dairyId)
    {
        $currentTime = date('Y-m-d H:i:s');
        $submiteInfo = DB::table('expenses')->insertGetId([
            'dairyId' => $dairyId,
            'status' => "true",
            'expenseHeadCode' => $request->expenseCode,
            'expenseHeadName' => $request->expenseName,
            'expenseDescription' => $request->expenseDesc . "",
            'created_at' => $currentTime,
        ]);

        if ($submiteInfo) {
            return true;
        } else {
            return false;
        }
    }

    public function expenseEditSubmit($request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('Y-m-d H:i:s');

        $updateReturn = DB::table('expenses')
            ->where('id', $request->expenseId)
            ->update([
                'expenseHeadName' => $request->expenseHeadName,
                'expenseDescription' => $request->expenseDescription,
                'updated_at' => $currentTime,
            ]);
        $returnSuccessArray = array("Success" => "True", "Message" => "Expense Edited");
        $returnSuccessJson = json_encode($returnSuccessArray);
        return $returnSuccessArray;
    }
}
