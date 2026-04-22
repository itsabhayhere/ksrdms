<?php


namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use App\Item;
use Auth;
use Illuminate\Support\Facades\DB;
class UserController extends Controller

{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    

    /**
        function for logout user
    */

    public function userLogout()

    {
        // auth logout for user logout
        Auth::logout();
        return redirect('/');

    }

    public function sidebarMenu()
    {
        $dairyId = session()->get('loginUserInfo');
        $loginUserType = session()->get('loginUserType');

        
        if($loginUserType == "user" || !empty($dairyId->menuId)){
          
            $meneIdMain = explode(",",$dairyId->menuId);
            $meneIdMainCount = count($meneIdMain);
            $mainMenuArray = [];
            $subMenuArray = [];
            for($i=0;$i<$meneIdMainCount;$i++){
                if(strlen($meneIdMain[$i]) > 2){
                    $subMenu = "";
                    $mainMenu = "";
                    if(strlen($meneIdMain[$i]) == 4){
                         $subMenu =  substr($meneIdMain[$i], 2);
                         $mainMenu = substr($meneIdMain[$i], 0, -3 );
                    }else{
                        $subMenu =  substr($meneIdMain[$i], -1);    
                        $mainMenu = substr($meneIdMain[$i], 0, -2);
                    }
                   
                    if (array_key_exists($mainMenu,$subMenuArray)){
                        $subMenuArray[$mainMenu][] = $subMenu;
                    }
                    else{
                        $subMenuArray[$mainMenu][] = $subMenu;
                    }

                    if (!(in_array($mainMenu , $mainMenuArray))){
                        $mainMenuArray[] =  $mainMenu ;
                    }
                    
                }else{
                     if (!(in_array($meneIdMain[$i] , $mainMenuArray))){
                        $mainMenuArray[] =  $meneIdMain[$i] ;
                    } 
                }
            }
            $mainMenuData = implode(",",$mainMenuArray );
            $retrunData = DB::select('SELECT * FROM admin_menus WHERE id IN ('. $mainMenuData.') ');
            return $retrunData ;
        }else{
            $retrunData = DB::select('SELECT * FROM admin_menus ');
            return $retrunData ;
        } 
        
        session()->put('subMenuArray', $subMenuArray);
        
    }
    public function sidebarSubMenu(Request $request)
    {

        $subMenuData = DB::table('submenu')
                        ->where('parentMenuId', $request->parentMenuId )
                        ->get();

        $subMenuList = [] ;
        $loginUserType = session()->get('loginUserType');
        if($loginUserType == "user"){
            $subMenuArray = session()->get('subMenuArray');
            $menuId = $request->parentMenuId;
            foreach($subMenuData as $subMenuDataValue){
                if (in_array($subMenuDataValue->id , $subMenuArray[$menuId])){
                    $subMenuList[] = $subMenuDataValue ;
                } 
            }
            return $subMenuList ;
        }else{
           return $subMenuData ;
        }
    
    }

}