<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class data_backup extends Model
{
    public function dailyDataBackupSubmit($request){

    	date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');

        if(empty($request->monday)){
        	$request->monday = null ;
        }
        if(empty($request->tuesday)){
        	$request->tuesday = null ;
        }
        if(empty($request->wednedsay)){
        	$request->wednedsay = null ;
        }
        if(empty($request->thursday)){
        	$request->thursday = null ;
        }
        if(empty($request->friday)){
        	$request->friday = null ;
        }
        if(empty($request->sterday)){
        	$request->sterday = null ;
        }
        if(empty($request->sunday)){
        	$request->sunday = null ;
        }
        $date1=date_create($request->startTime);
        $dat2e=date_create($request->endTime);

        $submiteInfo = DB::table('data_backup_day')->insertGetId(
              [
                'startTime' =>   date_format($date1,"H:i:s") ,
                'endTime' =>   date_format($dat2e,"H:i:s") ,
                'monday' => $request->monday,
                'tuesday' => $request->tuesday,
                'wednedsay' => $request->wednedsay,
                'thursday' => $request->thursday,
                'friday' => $request->friday,
                'sterday' => $request->sterday, 
                'sunday' => $request->sunday, 
 				'created_at' => $currentTime,
              ]
        );

        $submitedBackup = DB::table('data_backup')->insertGetId(
              [
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'seheduleType' => $request->tabs,
                'dataBackupId' => $submiteInfo,
 				'created_at' => $currentTime,
              ]
        );
       
        $returnSuccessArray = array("Success"=>"True","Message"=>"Backup Infomatioin submited","info id"=> $submitedBackup);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;

    }	
	public function weeklyDataBackupSubmit($request){

		date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $date1=date_create($request->weeklyStartTime);
        $dat2e=date_create($request->weeklyEndTime);
 
        $submiteInfo = DB::table('data_backup_weekly')->insertGetId(
              [
                'startTime' => date_format($date1,"H:i:s"),
                'endTime' => date_format($dat2e,"H:i:s"),
                'week_day' => $request->week_day,
                'created_at' => $currentTime,
              ]
        );
       
        $submitedBackup = DB::table('data_backup')->insertGetId(
              [
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'seheduleType' => $request->tabs,
                'dataBackupId' => $submiteInfo,
 				'created_at' => $currentTime,
              ]
        );
       
        $returnSuccessArray = array("Success"=>"True","Message"=>"Backup Infomatioin submited","info id"=> $submitedBackup);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;

	}
	public function monthlyDataBackupSubmit($request){

		 date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $date1=date_create($request->yearStartTime);
        $dat2e=date_create($request->yearEndTime);
 
        $submiteInfo = DB::table('data_backup_monthly')->insertGetId(
              [
                'startTime' =>  date_format($date1,"H:i:s"),
                'endTime' =>  date_format($dat2e,"H:i:s"),
                'monte_date' => $request->monte_date,
                'created_at' => $currentTime,
              ]
        );
       
        $submitedBackup = DB::table('data_backup')->insertGetId(
              [
                'dairyId' => $request->dairyId,
                'status' => $request->status,
                'seheduleType' => $request->tabs,
                'dataBackupId' => $submiteInfo,
 				'created_at' => $currentTime,
              ]
        );
       
        $returnSuccessArray = array("Success"=>"True","Message"=>"Backup Infomatioin submited","info id"=> $submitedBackup);
        $returnSuccessJson =  json_encode($returnSuccessArray);
        return $returnSuccessJson  ;
	}

    public function dailyDataBackupEditSubmit($request){

         /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $date1=date_create($request->startTime);
        $dat2e=date_create($request->endTime);
       
        $updateReturn = DB::table('data_backup_day')
            ->where('id', $request->backupId)
            ->update([
                'startTime' =>   date_format($date1,"H:i:s") ,
                'endTime' =>   date_format($dat2e,"H:i:s") ,
                'monday' => $request->monday,
                'tuesday' => $request->tuesday,
                'wednedsay' => $request->wednedsay,
                'thursday' => $request->thursday,
                'friday' => $request->friday,
                'sterday' => $request->sterday, 
                'sunday' => $request->sunday, 
                'updated_at' => $currentTime,
            ]);
         
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"Customer Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;
        }

    }    

    public function weeklyDataBackupEditSubmit($request){

           /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $date1=date_create($request->weeklyStartTime);
        $dat2e=date_create($request->weeklyEndTime);
       
        $updateReturn = DB::table('data_backup_weekly')
            ->where('id', $request->backupId)
            ->update([
                'startTime' => date_format($date1,"H:i:s"),
                'endTime' => date_format($dat2e,"H:i:s"),
                'week_day' => $request->week_day,
                'updated_at' => $currentTime,
            ]);
         
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"Customer Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;
        }

    }
    public function monthlyDataBackupEditSubmit($request){

           /* get current time */
        date_default_timezone_set('Asia/Kolkata');
        $currentTime =  date('Y-m-d H:i:s');
        $date1=date_create($request->yearStartTime);
        $dat2e=date_create($request->yearEndTime);
       
        $updateReturn = DB::table('data_backup_monthly')
            ->where('id', $request->backupId)
            ->update([
                'startTime' =>  date_format($date1,"H:i:s"),
                'endTime' =>  date_format($dat2e,"H:i:s"),
                'monte_date' => $request->monte_date,
                'updated_at' => $currentTime,
            ]);
         
        if($updateReturn == 1){
            $returnSuccessArray = array("Success"=>"True","Message"=>"Customer Successfully Updated");
            $returnSuccessJson =  json_encode($returnSuccessArray);
            return $returnSuccessJson ;
        }
    }
}
