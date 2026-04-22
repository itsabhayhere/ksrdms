<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class adminMenu extends Model
{
    public function adminMenuSubmit($request){
    	date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');

        if(!empty($request->subMenu)){
            
            $submiteInfo = DB::table('submenu')->insertGetId(
                [
                    'title' => $request->menuTitle,
                    'url' => $request->menuUrl,
                    'parentMenuId' => $request->subMenu,
                ]
            );
            DB::table('admin_menus')
                ->where('id', $request->subMenu)
                ->update([
                    'subMenu' => "1",
                ]);
        
        }else{
            $submiteInfo = DB::table('admin_menus')->insertGetId(
                [
                    'title' => $request->menuTitle,
                    'url' => $request->menuUrl,
                    'subMenu' => "0",
                ]
            );
        }
		
     
 		$returnSuccessArray = array("Success"=>"True","Message"=>"Supplier Successfully Register","supplier id"=> $submiteInfo);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;
    }



}
