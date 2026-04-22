<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class rolesSetup extends Model
{
    public function roleSetupFormSubmit($request)
    {
        $currentTime = date('Y-m-d H:i:s');

        $submiteInfo = DB::table('roles_setups')->insertGetId([
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'role' => $request->roleName,
            ]);
        $returnSuccessArray = array("Success" => "True", "Message" => "Supplier Successfully Register", "supplier id" => $submiteInfo);
        $returnSuccessJson = json_encode($returnSuccessArray);
        return $returnSuccessJson;
    }
}
