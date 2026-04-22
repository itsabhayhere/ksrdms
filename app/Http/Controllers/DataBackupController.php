<?php

namespace App\Http\Controllers;

use App\data_backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DataBackupController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('Auth');
    }
    

    public function dataBackupForm(Request $request){
        return view('databackup');
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $submitClass = new data_backup();
         

        if($request->tabs == "daily"){
            $submitReturn = $submitClass->dailyDataBackupSubmit($request);
        }
        if($request->tabs == "weekly"){
            $submitReturn = $submitClass->weeklyDataBackupSubmit($request);
        }
        if($request->tabs == "monthly"){
            $submitReturn = $submitClass->monthlyDataBackupSubmit($request);
        }

        return $submitReturn ;
    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\data_backup  $data_backup
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // $request->databackupId ;

        $dataBackupMain = DB::table('data_backup')
            ->where('id', $request->databackupId )
            ->get();

        $dataBackup = "" ;

        if($dataBackupMain[0]->seheduleType == "daily"){
            $dataBackup = DB::table('data_backup_day')
            ->where('id', $dataBackupMain[0]->dataBackupId  )
            ->get();
        }

        if($dataBackupMain[0]->seheduleType == "weekly"){
            $dataBackup = DB::table('data_backup_weekly')
            ->where('id', $dataBackupMain[0]->dataBackupId )
            ->get();
        }    

        if($dataBackupMain[0]->seheduleType == "monthly"){
            $dataBackup = DB::table('data_backup_monthly')
            ->where('id', $dataBackupMain[0]->dataBackupId )
            ->get();
        }    

        
        return view('databackupEdit', ['dataBackup' => $dataBackup[0]], ['dataBackupMain' => $dataBackupMain[0]]);
    }

    public function dataBackupListDay(Request $request){

        $dataBackup = DB::table('data_backup')
            ->where('dairyId', $request->dairyId )
            ->where('seheduleType', $request->backupType )
            ->get();

       echo "<pre>";
       print_r($dataBackup);
       die;
        
        $dataBackupValue = [] ;
        foreach ($dataBackup as $dataBackupArray) {
            $dataBackup = DB::table('data_backup_day')
            ->where('id', $dataBackupArray->dataBackupId  )
            ->get();
          
            if(empty($dataBackup[0]->monday )){
                $dataBackup[0]->monday = "no";
            }
             if(empty($dataBackup[0]->tuesday )){
                $dataBackup[0]->tuesday = "no";
            }
             if(empty($dataBackup[0]->wednedsay )){
                $dataBackup[0]->wednedsay = "no";
            }
            if(empty($dataBackup[0]->thursday )){
                $dataBackup[0]->thursday = "no";
            }
            if(empty($dataBackup[0]->friday )){
                $dataBackup[0]->friday = "no";
            }
            if(empty($dataBackup[0]->sterday )){
                $dataBackup[0]->sterday = "no";
            }
            if(empty($dataBackup[0]->sunday )){
                $dataBackup[0]->sunday = "no";
            }
            $dataBackupid=array("dataBackupid", $dataBackupArray->id );
            // print_r($dataBackup[0]);
            // print_r($dataBackupArray);
            $dataBackupValue[] = array("dataBackup" => $dataBackup[0],"dataBackupid" => $dataBackupid)  ;
        }
       
            print_r($dataBackupValue);
            die ;
        // die;

       // return view('dataBackupListDay', ['dataBackupValue' => $dataBackupValue]);
    }

    public function dataBackupListWeek(){

        $dataBackup = DB::table('data_backup')
            ->where('dairyId', $request->dairyId )
            ->where('seheduleType', $request->backupType )
            ->get();
        
        $dataBackupValue = [] ;
        foreach ($dataBackup as $dataBackupArray) {
            $dataBackup = DB::table('data_backup_day')
            ->where('id', $dataBackupArray->dataBackupId  )
            ->get();
            $dataArray = array("backupMainInfo"=> $dataBackupArray , "backupInfo" => $dataBackup[0] );
            $dataBackupValue[] = $dataArray ;
        }
        // return ;
    }

     public function dataBackupListMonth(){
            $dataBackup = DB::table('data_backup')
            ->where('dairyId', $request->dairyId )
            ->where('seheduleType', $request->backupType )
            ->get();
     
            $dataBackupValue = [] ;
            foreach ($dataBackup as $dataBackupArray) {
                $dataBackup = DB::table('data_backup_day')
                ->where('id', $dataBackupArray->dataBackupId  )
                ->get();
                $dataArray = array("backupMainInfo"=> $dataBackupArray , "backupInfo" => $dataBackup[0] );
                $dataBackupValue[] = $dataArray ;
            }
        // return ;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\data_backup  $data_backup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $submitClass = new data_backup();
        


        if($request->tabs == "daily"){
            $submitReturn = $submitClass->dailyDataBackupEditSubmit($request);
        }
        if($request->tabs == "weekly"){
            $submitReturn = $submitClass->weeklyDataBackupEditSubmit($request);
        }
        if($request->tabs == "monthly"){
            $submitReturn = $submitClass->monthlyDataBackupEditSubmit($request);
        }
       
        return $submitReturn ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\data_backup  $data_backup
     * @return \Illuminate\Http\Response
     */
    public function destroy(data_backup $data_backup)
    {
         $updateReturn = DB::table('data_backup')
            ->where('id', $request->backupId)
            ->update([
                'status' => "false",
            ]);
            //return view('supplierList', ['message' => "Supplier Successfully Delated" ]);
            return "Supplier Successfully Delated";
    }
}
