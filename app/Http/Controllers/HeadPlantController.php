<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;
use Validator;
use View;
use Log;

class HeadPlantController extends Controller
{

    public function __construct()
    {
        $this->middleware('PlantAuth');
    }

    public function checkNotification()
    {
        $pl = session()->get("plantInfo");

        $not = DB::table('notifications')
            ->selectRaw("*, DATE_FORMAT(created_at,'%h:%i %p %d %b %Y') as created_at")
            ->where(["ledgerId" => $pl->ledgerId])->orderby("id", "DESC")->limit(30)->get();

        if (count($not) > 0) {
            return ["error" => false, "data" => $not];
        } else {
            return ["error" => true, "msg" => "No new notification."];
        }
    }

    public function deleteNotification()
    {
        $pl = session()->get("plantInfo");

        $not = DB::table('notifications')->where(["ledgerId" => $pl->ledgerId, "id" => request("notiId")])->delete();

        if ($not) {
            return ["error" => false, "msg" => "Notification deleted"];
        } else {
            return ["error" => true, "msg" => "Some error has occured while removing notification."];
        }
    }

    public function dashboard()
    {
        $pl = session()->get("loginUserInfo")->plantId;

        $currentDate = date('Y-m-d');
        $YesterdayDate = date('Y-m-d', strtotime("-1 days"));
        $total_verified = 0;
        $total_members = 0;
        $total_active_members = 0;
        $total_cow_yesterday = 0;
        $total_cow_today = 0;
        $total_buf_yesterday = 0;
        $total_buf_today = 0;


        $plants = DB::table("milk_plants")->where(["milk_plants.parentPlantId" => $pl, "milk_plants.status" => "true"])
            ->select("*", "milk_plants.id as id", "city.name as city", "states.name as state")
            ->leftJoin("milk_plant_head", "milk_plant_head.plantId", "=", "milk_plants.id")
            ->leftJoin("city", "city.id", "=", "milk_plants.city")
            ->leftJoin("states", "states.id", "=", "milk_plants.state")->get();

        foreach ($plants as &$pl) {
            $pl->registered_dairies = DB::table("plantdairymap")->where(["plantId" =>  $pl->id, "status" => "true"])->count();
            $pl->activated_dairies = DB::table("plantdairymap")->where(["plantId" =>  $pl->id, "status" => "true", "isActivated" => "1"])->count();

            $total_verified += $pl->activated_dairies;

            $verifiedDairyIds = DB::table("plantdairymap")
                ->where(["plantdairymap.plantId" => $pl->id, "isActivated" => 1])
                ->get()->pluck("dairyId");

            $total_active_members += DB::table("daily_transactions")
                ->whereIn("dairyId", $verifiedDairyIds)
                ->where(["status" => "true"])
                ->whereDate("date", ">=", date("Y-m-d", strtotime("-5 days")))
                ->distinct('memberCode')->count('memberCode');
            $total_members += DB::table("member_personal_info")
                ->whereIn("dairyId", $verifiedDairyIds)
                ->where(["status" => "true"])->count();



            /* Cow milk Collected today */
            $cowMilkToday = DB::table('daily_transactions')
                ->whereIn('dairyId', $verifiedDairyIds)
                ->where('status', 'true')
                ->where('milkType', 'cow')
                ->where('date', $currentDate)
                ->get();

            $cowMilkTodayQty = 0;
            foreach ($cowMilkToday as $cowMilkTodayData) {
                $cowMilkTodayQty = (float) $cowMilkTodayQty + (float) $cowMilkTodayData->milkQuality;
            }

            /* Cow milk Collected Yesterday */
            $cowMilkYesterday = DB::table('daily_transactions')
                ->whereIn('dairyId', $verifiedDairyIds)
                ->where('status', 'true')
                ->where('milkType', 'cow')
                ->where('date', $YesterdayDate)
                ->get();
            $cowMilkYesterdayQty = 0;
            foreach ($cowMilkYesterday as $cowMilkYesterdayData) {
                $cowMilkYesterdayQty = (float) $cowMilkYesterdayQty + (float) $cowMilkYesterdayData->milkQuality;
            }

            /* Buffalo milk Collected today */
            $buffaloMilkToday = DB::table('daily_transactions')
                ->whereIn('dairyId', $verifiedDairyIds)
                ->where('status', 'true')
                ->where('milkType', 'buffalo')
                ->where('date', $currentDate)
                ->get();
            $buffaloMilkTodayQty = 0;
            foreach ($buffaloMilkToday as $buffaloMilkTodayData) {
                $buffaloMilkTodayQty = (float) $buffaloMilkTodayQty + (float) $buffaloMilkTodayData->milkQuality;
            }

            /* Buffalo milk Collected Yesterday */
            $buffaloMilkYesterday = DB::table('daily_transactions')
                ->whereIn('dairyId', $verifiedDairyIds)
                ->where('status', 'true')
                ->where('milkType', 'buffalo')
                ->where('date', $YesterdayDate)
                ->get();

            $buffaloMilkYesterdayQty = 0;
            foreach ($buffaloMilkYesterday as $buffaloMilkYesterdayData) {
                $buffaloMilkYesterdayQty = (float) $buffaloMilkYesterdayQty + (float) $buffaloMilkYesterdayData->milkQuality;
            }


            $pl->buffaloMilkYesterdayQty = round($buffaloMilkYesterdayQty);
            $pl->cowMilkYesterdayQty = round($cowMilkYesterdayQty);
            $pl->buffaloMilkTodayQty = round($buffaloMilkTodayQty);
            $pl->cowMilkTodayQty = round($cowMilkTodayQty);

            $total_cow_yesterday += $buffaloMilkYesterdayQty;
            $total_cow_today += $cowMilkYesterdayQty;
            $total_buf_yesterday += $buffaloMilkTodayQty;
            $total_buf_today += $cowMilkTodayQty;
        }

        $total = [
            "total_verified" => $total_verified,
            "total_members" => $total_members,
            "total_active_members" => $total_active_members,
            "total_cow_yesterday" => round($total_cow_yesterday),
            "total_cow_today" => round($total_cow_today),
            "total_buf_yesterday" => round($total_buf_yesterday),
            "total_buf_today" => round($total_buf_today),
        ];


        return view("headPlant.dashboard", ['activepage' => "dashboard", "plants" => $plants, 'total' => $total]);
    }

    public function requestToAdd()
    {
        $pl = session()->get("loginUserInfo")->plantId;

        $req = DB::table('plantdairymap')->where(["plantId" => $pl])
            ->select("*", "city.name as city", "states.name as state")
            ->where("plantdairymap.isActivated", 0)
            ->join("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->leftJoin("city", "city.id", "=", "dairy_info.city")
            ->leftJoin("states", "states.id", "=", "dairy_info.state")
            ->leftJoin("dairy_propritor_info", "dairy_propritor_info.dairyId", "=", "dairy_info.id")
            ->get();

        return view("headPlant.request", ["requests" => $req, 'activepage' => "request"]);
    }

    public function plantAddRequestComplete()
    {
        $pl = session()->get("plantInfo");

        if (request("action") == "Accept") {
            $res = DB::table("plantdairymap")->where(["plantId" => $pl->id, "dairyId" => request("dairyId")])->update(['isActivated' => 1]);
            $this->makeDairyVerifiedNotification($pl);
            return ["error" => false, "msg" => "Request Accepted."];
        } elseif (request("action") == "Decline") {
            $res = DB::table("plantdairymap")->where(["plantId" => $pl->id, "dairyId" => request("dairyId")])->delete();
            $this->makeDairyVerifiedNotification($pl);
            return ["error" => false, "msg" => "Request declined."];
        } else {
            return ["error" => true, "msg" => "Something is wrong."];
        }
    }

    public function makeDairyVerifiedNotification($pl)
    {
        $dairy = DB::table('dairy_info')->where(["id" => request('dairyId')])->get()->first();

        if (request('action') == "Accept") {
            $msg = "Plant (" . $pl->plantName . ") has verified your dairy.";
        } else {
            $msg = "Plant (" . $pl->plantName . ") has remove your dairy from verified dairy list.";
        }

        $data = [
            "ledgerId" => $dairy->ledgerId,
            "notification" => $msg,
            "created_at" => date("Y-m-d H:i:s"),
        ];

        return DB::table('notifications')->insertGetId($data);
    }

    public function plants()
    {

        $pl = session()->get("loginUserInfo")->plantId;


        $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])
            ->select("*", "city.name as city", "states.name as state")
            ->leftJoin("milk_plant_head", "milk_plant_head.plantId", "=", "milk_plants.id")
            ->leftJoin("city", "city.id", "=", "milk_plants.city")
            ->leftJoin("states", "states.id", "=", "milk_plants.state")->get();



        return view("headPlant.plants", ["plants" => $plants, "activepage" => "plants"]);
    }



    public function subplant_dairies()
    {
        $pl = session()->get("loginUserInfo")->plantId;
        $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])->get();
        $dairies = DB::table("milk_plants")->where(["parentPlantId" => $pl])
            // ->select("plantdairymap.dairyId", "plantdairymap.status", "dairy_info.society_code","dairy_info.dairyName", "city.name as city",
            //     "states.name as state", "dairy_propritor_info.dairyPropritorName")
            ->select(
                "plantdairymap.dairyId",
                "dairy_info.id",
                "dairy_info.mobile",
                "dairy_info.dairyAddress",
                "dairy_info.society_code",
                "dairy_info.dairyName",
                "city.name as city",
                "states.name as state",
                "dairy_propritor_info.dairyPropritorName",
                "dairy_propritor_info.PropritorMobile",
                "dairy_propritor_info.dairyPropritorEmail"
            )
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->leftJoin("city", "city.id", "=", "dairy_info.city")
            ->leftJoin("states", "states.id", "=", "dairy_info.state")
            ->leftJoin("dairy_propritor_info", "dairy_propritor_info.dairyId", "=", "dairy_info.id")
            ->get();

        // dd($dairies);

        return view("headPlant.dairies", ["dairies" => $dairies, "activepage" => "dairies", "plants" => $plants]);
    }

    public function dairyByPlantId()
    {
        $plant = DB::table('milk_plants')->where('plantCode', request('plant_code'))->first();

        if ($plant != null) {

            $dairies = DB::table("plantdairymap")->where(["plantId" => $plant->id, "isActivated" => 1])
                ->select(
                    "plantdairymap.dairyId",
                    "dairy_info.id",
                    "dairy_info.mobile",
                    "dairy_info.dairyAddress",
                    "dairy_info.society_code",
                    "dairy_info.dairyName",
                    "city.name as city",
                    "states.name as state",
                    "dairy_propritor_info.dairyPropritorName",
                    "dairy_propritor_info.PropritorMobile",
                    "dairy_propritor_info.dairyPropritorEmail"
                )

                ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
                ->leftJoin("city", "city.id", "=", "dairy_info.city")
                ->leftJoin("states", "states.id", "=", "dairy_info.state")
                ->leftJoin("dairy_propritor_info", "dairy_propritor_info.dairyId", "=", "dairy_info.id")
                ->get();


            return view("headPlant._dairies", ["dairies" => $dairies, "activepage" => "dairies"]);
        }

        return view("headPlant._dairies", ["activepage" => "dairies"]);
    }

    public function getDairies_by_plant()
    {
        $pl = session()->get("loginUserInfo")->plantId;
        $plant_id = request('plant_id');

        if ($plant_id != 'all') {

            $dairies = DB::table("plantdairymap")->where(["plantId" => $plant_id, "isActivated" => 1])
                ->select(
                    "plantdairymap.dairyId",
                    "dairy_info.id",
                    "dairy_info.mobile",
                    "dairy_info.dairyAddress",
                    "dairy_info.society_code",
                    "dairy_info.dairyName",
                    "city.name as city",
                    "states.name as state",
                    "dairy_propritor_info.dairyPropritorName",
                    "dairy_propritor_info.PropritorMobile",
                    "dairy_propritor_info.dairyPropritorEmail"
                )

                ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
                ->leftJoin("city", "city.id", "=", "dairy_info.city")
                ->leftJoin("states", "states.id", "=", "dairy_info.state")
                ->leftJoin("dairy_propritor_info", "dairy_propritor_info.dairyId", "=", "dairy_info.id")
                ->groupBy('plantdairymap.dairyId')
                ->get();

            return ['error' => false, "dairies" => $dairies, "plant_id" => $plant_id, "pl" => $pl];
        } else {

            $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])
                ->select("*", "milk_plants.id", "city.name as city", "states.name as state")
                ->leftJoin("milk_plant_head", "milk_plant_head.plantId", "=", "milk_plants.id")
                ->leftJoin("city", "city.id", "=", "milk_plants.city")
                ->leftJoin("states", "states.id", "=", "milk_plants.state")->get();

            $plant_ids = $plants->pluck('id');

            $dairies = DB::table("plantdairymap")
                ->whereIn('plantId', $plant_ids)
                ->where(["isActivated" => 1])
                ->select(
                    "plantdairymap.dairyId",
                    "dairy_info.id",
                    "dairy_info.mobile",
                    "dairy_info.dairyAddress",
                    "dairy_info.society_code",
                    "dairy_info.dairyName",
                    "city.name as city",
                    "states.name as state",
                    "dairy_propritor_info.dairyPropritorName",
                    "dairy_propritor_info.PropritorMobile",
                    "dairy_propritor_info.dairyPropritorEmail"
                )
                ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
                ->leftJoin("city", "city.id", "=", "dairy_info.city")
                ->leftJoin("states", "states.id", "=", "dairy_info.state")
                ->leftJoin("dairy_propritor_info", "dairy_propritor_info.dairyId", "=", "dairy_info.id")
                ->groupBy('plantdairymap.dairyId')
                ->get();

            return ['error' => false, "dairies" => $dairies, "plant_ids" => $plant_ids];
        }
    }

    public function allmember()
    {

        $pl = session()->get("loginUserInfo")->plantId;


        $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])
            ->select("*", "milk_plants.id", "city.name as city", "states.name as state")
            ->leftJoin("milk_plant_head", "milk_plant_head.plantId", "=", "milk_plants.id")
            ->leftJoin("city", "city.id", "=", "milk_plants.city")
            ->leftJoin("states", "states.id", "=", "milk_plants.state")->get();


        $members = DB::table("milk_plants")->where(["parentPlantId" => $pl])
            ->select(

                "dairy_info.society_name",
                "dairy_info.society_code",

                "member_personal_info.memberPersonalName",
                "member_personal_info.memberPersonalCode",
                "member_personal_info.memberPersonalAadarNumber",
                "member_personal_info.memberPersonalMobileNumber",
                "member_personal_info.memberPersonalFatherName",
                "member_personal_bank_info.memberPersonalBankName",
                "member_personal_bank_info.memberPersonalAccountName",
                "member_personal_bank_info.memberPersonalAccountNumber",
                "member_personal_bank_info.memberPersonalIfsc"
            )
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->leftJoin("member_personal_info", "member_personal_info.dairyId", "=", "dairy_info.id")
            ->where('member_personal_info.status', 'true')
            ->leftJoin("member_personal_bank_info", "member_personal_bank_info.memberPersonalUserId", "=", "member_personal_info.id")
            ->leftJoin("city", "city.id", "=", "member_personal_info.memberPersonalCity")
            ->leftJoin("states", "states.id", "=", "member_personal_info.memberPersonalState")
            ->get();
        $mem_cnt = count($members);
        // Log::info($mem_cnt);
        $dairies = DB::table('milk_plants')->where('parentPlantId', $pl)
            ->select('dairy_info.society_code', 'dairy_info.dairyName')
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->get();

        return view("headPlant.plantmemberList", ["plants" => $plants, "members" => $members, "activepage" => "dairy_report", "dairy" => $dairies]);
    }




    public function getAllMemberReportPdf(Request $request)
    {

        $pl = session()->get("loginUserInfo")->plantId;

        $members = DB::table("milk_plants")->where(["parentPlantId" => $pl])
            ->select(

                "dairy_info.society_name",
                "dairy_info.society_code",

                "member_personal_info.memberPersonalName",
                "member_personal_info.memberPersonalCode",
                "member_personal_info.memberPersonalAadarNumber",
                "member_personal_info.memberPersonalMobileNumber",
                "member_personal_info.memberPersonalFatherName",
                "member_personal_bank_info.memberPersonalBankName",
                "member_personal_bank_info.memberPersonalAccountName",
                "member_personal_bank_info.memberPersonalAccountNumber",
                "member_personal_bank_info.memberPersonalIfsc"
            )
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->leftJoin("member_personal_info", "member_personal_info.dairyId", "=", "dairy_info.id")
            ->where('member_personal_info.status', 'true')
            ->leftJoin("member_personal_bank_info", "member_personal_bank_info.memberPersonalUserId", "=", "member_personal_info.id")
            ->leftJoin("city", "city.id", "=", "member_personal_info.memberPersonalCity")
            ->leftJoin("states", "states.id", "=", "member_personal_info.memberPersonalState")
            ->get();
        // dd($members);
        $time = time();
        $pdf = PDF::loadView('pdfheadPlant.memberReport', ["members" => $members, "headings" => ["dairyName" => "All Verified Dairy", "report" => "Member Report", "society_code" => "123"]]);
        return $pdf->download('member' . $time . '.pdf');
    }

    public function memberByDairy()
    {

        $members =
            DB::table('dairy_info')->where('society_code', request('societyCode'))
            ->select(
                "dairy_info.society_name",
                "dairy_info.society_code",
                "member_personal_info.memberPersonalName",
                "member_personal_info.memberPersonalCode",
                "member_personal_info.memberPersonalAadarNumber",
                "member_personal_info.memberPersonalMobileNumber",
                "member_personal_info.memberPersonalFatherName",
                "member_personal_bank_info.memberPersonalBankName",
                "member_personal_bank_info.memberPersonalAccountName",
                "member_personal_bank_info.memberPersonalAccountNumber",
                "member_personal_bank_info.memberPersonalIfsc"
            )
            ->leftJoin("member_personal_info", "member_personal_info.dairyId", "=", "dairy_info.id")
            ->where('member_personal_info.status', 'true')
            ->leftJoin("member_personal_bank_info", "member_personal_bank_info.memberPersonalUserId", "=", "member_personal_info.id")
            ->leftJoin("city", "city.id", "=", "member_personal_info.memberPersonalCity")
            ->leftJoin("states", "states.id", "=", "member_personal_info.memberPersonalState")
            ->get();


        $report = view::make('headPlant._plantMemberList', ['members' => $members, "activepage" => "dairies"]);

        // $pdf = \PDF::loadView('pdfheadPlant.memberReport', ["members" => $members,

        //     "headings" => ["dairyName" => $societyName, "report" => "Member Report", "society_code" => request('societyCode')],

        // ])->setPaper('A4', 'landscape');
        // $filename = "download/reports/" . request('societyCode') . "_member_report.pdf";
        // $pdf->save($filename);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => request('societyName'), "society_code" => request('societyCode'),
                "report" => "Member report"
            ],
            // "filename" => $filename,
        ];
    }

    public function payment_register()
    {
        $pl = session()->get("loginUserInfo")->plantId;

        $from_date = request('from_date');
        if (empty($from_date)) {
            $from_date = date("d-m-Y");
        }

        $to_date = request('to_date');
        if (empty($to_date)) {
            $to_date = date("d-m-Y");
        }

        $plant_id = request("plant_id");


        $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])
            ->select("*", "milk_plants.id", "city.name as city", "states.name as state")
            ->leftJoin("milk_plant_head", "milk_plant_head.plantId", "=", "milk_plants.id")
            ->leftJoin("city", "city.id", "=", "milk_plants.city")
            ->leftJoin("states", "states.id", "=", "milk_plants.state")->get();


        if (empty($plant_id) || $plant_id == 'all') {
            $plant_ids = $plants->pluck('id');
        } else {
            $plant_ids = [$plant_id];
        }


        $dairies = DB::table('milk_plants')
            ->whereIn('milk_plants.id', $plant_ids)
            ->select('dairy_info.society_code', 'dairy_info.dairyName')
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->groupBy('plantdairymap.dairyId')
            ->get();


        $dairy_code = request('dairy_code');
        $dairy = null;
        if ((!empty($dairy_code) || $dairy_code != 'all') && !empty($dairies->where('society_code', $dairy_code)->all())) {
            $dairy = DB::table('dairy_info')
                ->select('dairy_info.id', 'dairy_info.society_code', 'dairy_info.dairyName')
                ->where("dairy_info.society_code", $dairy_code)
                ->first();
        } else {
            $dairy_code = '';
        }

        $q = DB::table("milk_plants")->whereIn("milk_plants.id", $plant_ids)

            ->selectRaw("SUM(daily_transactions.amount) as amount, AVG(daily_transactions.fat) as fat,count(daily_transactions.id) as noOfShift, AVG(daily_transactions.snf) as snf,
        SUM(daily_transactions.milkQuality) as qty, daily_transactions.dairyId as dt_dairyId,
         daily_transactions.memberCode, daily_transactions.memberName,
       dairy_info.society_code,dairy_info.dairyName")
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin('dairy_info', 'plantdairymap.dairyId', '=', 'dairy_info.id')
            ->leftjoin("daily_transactions", "dairy_info.id", "=", "daily_transactions.dairyId")
            ->where("daily_transactions.status", "true");
        if (!empty($dairy)) {
            $q->where("daily_transactions.dairyId", $dairy->id);
        }
        $dailyTrns = $q->whereBetween('daily_transactions.date', [date("Y-m-d", strtotime($from_date)), date("Y-m-d", strtotime($to_date))])
            ->groupby("daily_transactions.memberCode")
            ->get();


        return view("headPlant.plantPaymentRegister", ["plants" => $plants, "plant_id" => $plant_id, "dailyTrns" => $dailyTrns, "activepage" => "payment_register", "dairy" => $dairies, "dairy_code" => $dairy_code,  "from_date" => $from_date, "to_date" => $to_date]);
    }

    public function getPaymentRegisterReportPdf(Request $request)
    {

        $pl = session()->get("loginUserInfo")->plantId;

        $dailyTrns =
            DB::table("milk_plants")->where(["milk_plants.parentPlantId" => $pl])

            ->selectRaw("SUM(daily_transactions.amount) as amount, AVG(daily_transactions.fat) as fat,count(daily_transactions.id) as noOfShift, AVG(daily_transactions.snf) as snf,
        SUM(daily_transactions.milkQuality) as qty, daily_transactions.dairyId as dt_dairyId,
         daily_transactions.memberCode, daily_transactions.memberName,
       dairy_info.society_code,dairy_info.dairyName")
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin('dairy_info', 'plantdairymap.dairyId', '=', 'dairy_info.id')
            ->leftjoin("daily_transactions", "dairy_info.id", "=", "daily_transactions.dairyId")
            ->where("daily_transactions.status", "true")
            ->groupby("daily_transactions.memberCode")
            ->get();
        $random = rand(10, 000);
        $pdf = PDF::loadView('pdfheadPlant.paymentRegisterReport', ["dailyTrns" => $dailyTrns, "headings" => [
            "dairyName" => "All Verified Dairy",
            "report" => "Payment Register Report", "society_code" => ""
        ]])->setPaper('A4', 'landscape');
        return $pdf->download('payment_' . $random . '_register.pdf');
    }

    public function paymentByDairy()
    {

        $dailyTrns =
            DB::table("dairy_info")->where("dairy_info.society_code", request('societyCode'))

            ->selectRaw("SUM(daily_transactions.amount) as amount, AVG(daily_transactions.fat) as fat,count(daily_transactions.id) as noOfShift, AVG(daily_transactions.snf) as snf,
        SUM(daily_transactions.milkQuality) as qty, daily_transactions.dairyId as dt_dairyId,
         daily_transactions.memberCode, daily_transactions.memberName,
        dairy_info.society_code, dairy_info.dairyName")
            ->leftjoin("daily_transactions", "dairy_info.id", "=", "daily_transactions.dairyId")
            ->where("daily_transactions.status", "true")
            ->groupby("daily_transactions.memberCode")
            ->get();
        $time = time();
        $report = view::make('headPlant._paymentRegisterData', ['dailyTrns' => $dailyTrns, "activepage" => "dairies"]);

        // $pdf = \PDF::loadView('pdfheadPlant.paymentRegisterReport', ["dailyTrns" => $dailyTrns,

        //     "headings" => ["dairyName" => $societyName, "report" => "Payment Register Report", "society_code" => request('societyCode')],

        // ])->setPaper('A4', 'landscape');
        // $filename = "download/reports/" . $time . "_payment_register_report.pdf";
        // $pdf->save($filename);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => request('societyName'), "society_code" => request('societyCode'),
                "report" => "Payment Regster Report"
            ],

            // "filename" => $filename,
        ];
    }

    public function shift_summary(Request $request)
    {
        $pl = session()->get("loginUserInfo")->plantId;


        $plant_id = request("plant_id");

        $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])
            ->select("*", "milk_plants.id", "city.name as city", "states.name as state")
            ->leftJoin("milk_plant_head", "milk_plant_head.plantId", "=", "milk_plants.id")
            ->leftJoin("city", "city.id", "=", "milk_plants.city")
            ->leftJoin("states", "states.id", "=", "milk_plants.state")->get();

        if (empty($plant_id) || $plant_id == 'all') {
            $plant_ids = $plants->pluck('id');
        } else {
            $plant_ids = [$plant_id];
        }

        $dairyId = DB::table('milk_plants')->whereIn('milk_plants.id', $plant_ids)
            ->select('dairy_info.society_code', 'dairy_info.dairyName', 'plantdairymap.dairyId')
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->groupBy("plantdairymap.dairyId")
            ->get();

        $dairy_id = [];
        foreach ($dairyId as $d) {
            $dairy_id[] = $d->dairyId;
        }
        $dairy_ids = implode(',', $dairy_id);
        $buffalo_qty = DB::table('daily_transactions')->whereIn("dairyId", $dairy_id)->where(["status" => "true", "milkType" => "buffalo"])->sum("milkQuality");
        $cow_qty = DB::table('daily_transactions')->whereIn("dairyId", $dairy_id)->where(["status" => "true", "milkType" => "cow"])->sum("milkQuality");


        $q1 = DB::select(DB::raw("SELECT b.averageFat,b.averageSnf,b.MilkCollected, c.averageFatCow, c.averageSnfCow, c.MilkCollectedCow
                            ,di.id, di.society_code
        FROM `dairy_info` di

LEFT JOIN (SELECT (SUM((dt.milkQuality*dt.fat))/SUM(dt.milkQuality)) as averageFat, SUM((dt.milkQuality*dt.snf))/SUM(dt.milkQuality) as averageSnf, SUM(dt.milkQuality)as MilkCollected,
                        dt.dairyId,dt.status,dt.shift,dt.milkType
            FROM `daily_transactions` dt
            WHERE dt.status='true'
                AND dt.milkType='buffalo'
                AND dt.dairyId IN ($dairy_ids)
            GROUP BY dt.dairyId) b

    ON di.id=b.dairyId
    LEFT JOIN (SELECT (SUM((dtc.milkQuality*dtc.fat))/SUM(dtc.milkQuality)) as averageFatCow, SUM((dtc.milkQuality*dtc.snf))/SUM(dtc.milkQuality) as averageSnfCow,
                    SUM(dtc.milkQuality)as MilkCollectedCow,dtc.status,dtc.shift,dtc.milkType,dtc.dairyId,dtc.date
                FROM `daily_transactions` dtc 
                WHERE dtc.status='true'
                AND dtc.milkType='cow' 
                AND dtc.dairyId IN ($dairy_ids)
                GROUP BY dtc.dairyId) c

    ON di.id=c.dairyId
    WHERE di.id IN ($dairy_ids)
    GROUP BY di.id"));

        return view("headPlant.plantShiftSummary", ["plants" => $plants, "plant_id" => $plant_id, "data1" => $q1, "activepage" => "shift_report", "dairy" => $dairyId]);
    }

    public function getShiftSummaryPdf()
    {

        // $pl = session()->get("loginUserInfo")->plantId;
        // $dairyId = DB::table('milk_plants')->where('parentPlantId', $pl)
        //     ->select('dairy_info.society_code', 'dairy_info.dairyName', 'plantdairymap.dairyId')
        //     ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
        //     ->where("plantdairymap.isActivated", 1)
        //     ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
        //     ->get();

        // $dairy_id = [];
        // foreach ($dairyId as $d) {

        //     $dairy_id[] = $d->dairyId;
        // }
        $pl = session()->get("loginUserInfo")->plantId;

        if (empty(request('plant_id'))) {
            $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])->get();
            $plant_ids = $plants->pluck('id');
        } else {
            $plant_ids = [request('plant_id')];
        }

        if (!empty(request('society_Code'))) {
            $dairy = DB::table('dairy_info')->where('society_code', request('society_Code'))->first();
            $dairy_ids = $dairy->id;
        } else {
            $dairies = DB::table('milk_plants')
                ->whereIn('milk_plants.id', $plant_ids)
                ->select('dairy_info.id', 'dairy_info.society_code', 'dairy_info.dairyName')
                ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
                ->where("plantdairymap.isActivated", 1)
                ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
                ->groupBy('plantdairymap.dairyId')
                ->get();
            // $dairy_ids = $dairies->pluck('id');

            $dairy_ids = [];
            foreach ($dairies as $d) {
                $dairy_ids[] = $d->id;
            }
            $dairy_ids = implode(",", $dairy_ids);
        }

        if (empty($dairy_ids)) {
            $q1 = [];
        } else {

            $q1 = DB::select(DB::raw("SELECT b.averageFat,b.averageSnf,b.MilkCollected, c.averageFatCow, c.averageSnfCow, c.MilkCollectedCow
        ,di.id, di.society_code
FROM `dairy_info` di


LEFT JOIN (SELECT (AVG(dt.milkQuality*dt.fat))as averageFat,AVG(dt.milkQuality*dt.snf) as averageSnf, SUM(dt.milkQuality)as MilkCollected,
                        dt.dairyId,dt.status,dt.shift,dt.milkType

FROM `daily_transactions` dt WHERE dt.status='true'   AND dt.milkType='buffalo'

GROUP BY dt.dairyId)b
ON di.id=b.dairyId

LEFT JOIN (SELECT (AVG(dtc.milkQuality*dtc.fat)) as averageFatCow,AVG(dtc.milkQuality*dtc.snf) as averageSnfCow,
                    SUM(dtc.milkQuality)as MilkCollectedCow,dtc.status,dtc.shift,dtc.milkType,dtc.dairyId,dtc.date

FROM `daily_transactions` dtc WHERE dtc.status='true'   AND dtc.milkType='cow'
GROUP BY dtc.dairyId)c
ON di.id=c.dairyId
WHERE di.id IN ($dairy_ids)
GROUP BY di.id"));
        }

        // $random = rand(10, 000);
        $pdf = PDF::loadView('pdfheadPlant.shiftSummaryReport', ["data" => $q1, "flag" => "false", "headings" => [
            "dairyName" => "All Verified Dairy",
            "report" => "Shift Summary Report", "society_code" => ""
        ]])->setPaper('A4', 'landscape');
        return $pdf->download('shiftSummary.pdf');
    }
    public function ShiftSummaryByDairy(Request $request)
    {


        // $dairy = DB::table('dairy_info')->where('society_code', $request->society_Code)->first();
        // $q = DB::table('daily_transactions')
        //     ->where('shift', $request->shiftType)
        //     ->where('date', date("Y-m-d", strtotime($request->shiftDate)))
        //     ->where('dairyId', $dairy->id)
        //     ->where('status', "true")
        //     ->orderBy('created_at', 'desc')->get();
        
        $pl = session()->get("loginUserInfo")->plantId;

        if (empty(request('plant_id'))) {
            $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])->get();
            $plant_ids = $plants->pluck('id');
        } else {
            $plant_ids = [request('plant_id')];
        }

        if (!empty(request('societyCode'))) {
            $dairy = DB::table('dairy_info')->where('society_code', request('societyCode'))->first();
            $dairy_ids = $dairy->id;
        } else {
            $dairies = DB::table('milk_plants')
                ->whereIn('milk_plants.id', $plant_ids)
                ->select('dairy_info.id', 'dairy_info.society_code', 'dairy_info.dairyName')
                ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
                ->where("plantdairymap.isActivated", 1)
                ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
                ->groupBy('plantdairymap.dairyId')
                ->get();
            // $dairy_ids = $dairies->pluck('id');

            $dairy_ids = [];
            foreach ($dairies as $d) {
                $dairy_ids[] = $d->id;
            }
            $dairy_ids = implode(",", $dairy_ids);
        }

        if (empty($dairy_ids)) {
            $q = [];
        } else {

            $q = DB::select(DB::raw("SELECT b.averageFat,b.averageSnf,b.MilkCollected, c.averageFatCow, c.averageSnfCow, c.MilkCollectedCow
        ,di.id, di.society_code
FROM `dairy_info` di


LEFT JOIN (SELECT (AVG(dt.milkQuality*dt.fat))as averageFat,AVG(dt.milkQuality*dt.snf) as averageSnf, SUM(dt.milkQuality)as MilkCollected,
                        dt.dairyId,dt.status,dt.shift,dt.milkType

FROM `daily_transactions` dt WHERE dt.status='true'   AND dt.milkType='buffalo'

GROUP BY dt.dairyId)b
ON di.id=b.dairyId

LEFT JOIN (SELECT (AVG(dtc.milkQuality*dtc.fat)) as averageFatCow,AVG(dtc.milkQuality*dtc.snf) as averageSnfCow,
                    SUM(dtc.milkQuality)as MilkCollectedCow,dtc.status,dtc.shift,dtc.milkType,dtc.dairyId,dtc.date

FROM `daily_transactions` dtc WHERE dtc.status='true'   AND dtc.milkType='cow'
GROUP BY dtc.dairyId)c
ON di.id=c.dairyId
WHERE di.id IN ($dairy_ids)
GROUP BY di.id"));
        }

        $report = view::make("headPlant._shiftSummaryData", ["data" => $q, "activepage" => "shift_report"]);

        // $pdf = \PDF::loadView('pdfheadPlant.shiftSummaryReport', ["data" => $q, "flag" => $flag,

        //     "headings" => ["dairyName" => $societyName, "report" => "Shift Summary Report", "society_code" => request('societyCode')],

        // ])->setPaper('A4', 'landscape');
        // $time = time();
        // $filename = "download/reports/" . time() . "_shift_summary_report.pdf";
        // $pdf->save($filename);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" =>  request('societyName'), "society_code" => request('society_Code'),
                "report" => "Shift Summary Report"
            ],
            // "filename" => $filename,
        ];
    }

    public function cm_subsidiary(Request $request)
    {

        $from = date('Y-m-01');
        $to = date('Y-m-t');

        $pl = session()->get("loginUserInfo")->plantId;

        $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])->get();

        $plant_ids = $plants->pluck('id');

        $dairies = DB::table('milk_plants')
            ->whereIn('milk_plants.id', $plant_ids)
            ->select('dairy_info.id', 'dairy_info.society_code', 'dairy_info.dairyName')
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->groupBy('plantdairymap.dairyId')
            ->get();


        // $dairyId = DB::table('milk_plants')->where('parentPlantId', $pl)
        //     ->select('dairy_info.society_code', 'dairy_info.dairyName', 'plantdairymap.dairyId')
        //     ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
        //     ->where("plantdairymap.isActivated", 1)
        //     ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
        //     ->get();

        $dairy_id = [];
        foreach ($dairies as $d) {
            $dairy_id[] = $d->id;
        }
        $dairy_ids = implode(',', $dairy_id);

        if (empty($dairy_ids)) {
            $q = [];
        } else {


            $q = DB::select(DB::raw("SELECT  p.amount35_50,p.fat35_50,p.qty35_50,p.memberCode3, p.Date_2, s.Date_1, s.fat50__, s.memberCode2, s.qty50__, s.qty50__*5 as amount50__,
        mb.memberPersonalAccountName as memberName, mi.memberPersonalAadarNumber as adharNo, mb.memberPersonalBankName as bankName,
        mb.memberPersonalIfsc as ifscCode, mb.memberPersonalAccountNumber as accNo, mi.memberPersonalCode as memberCode
        ,mi.status, mi.dairyId
        FROM `member_personal_info` mi


        LEFT JOIN (SELECT (SUM(d.fat*d.milkQuality))/SUM(d.milkQuality) as fat50__, SUM(d.milkQuality) as qty50__, d.memberCode as memberCode2,
                            d.dairyId,d.status, d.date as Date_1

        FROM `daily_transactions` d WHERE d.date BETWEEN '$from' AND '$to' AND d.fat>5.0  AND d.dairyId IN ($dairy_ids) AND d.status='true'

        GROUP BY d.memberCode) s
        ON mi.memberPersonalCode=s.memberCode2


        LEFT JOIN member_personal_bank_info mb ON mi.id=mb.memberPersonalUserId AND mi.dairyId IN ($dairy_ids)

        LEFT JOIN (SELECT (SUM(t.fat*t.milkQuality))/SUM(t.milkQuality) as fat35_50, SUM(t.milkQuality) as qty35_50, t.memberCode as memberCode3, SUM(t.milkQuality)*3.5 as amount35_50,
                     t.dairyId, t.status, t.date as Date_2

        FROM `daily_transactions` t WHERE t.date BETWEEN '$from' AND '$to' AND t.fat BETWEEN 3.5 AND 5.0  AND t.dairyId IN ($dairy_ids) AND t.status='true'
        GROUP BY t.memberCode) p
        ON mi.memberPersonalCode=p.memberCode3 AND mi.dairyId IN ($dairy_ids)
        WHERE mi.status='true' AND mi.dairyId IN ($dairy_ids) AND s.Date_1 BETWEEN '$from' AND '$to' OR
        p.Date_2 BETWEEN '$from' AND '$to'
        GROUP BY p.memberCode3, s.memberCode2"));
        }


        return View('headPlant.plantCmSubsidiary', [
            "data" => $q, 'dairyId' => "", 'dairyName' => "", 'dairyCode' => "",
            "from" => $from, "to" => $to, "dairy" => $dairies, "plants" => $plants
        ]);
    }

    public function cm_subsidiaryByDairy(Request $request)
    {
        $validator = Validator::make($request->all(), ["amountLow" => ["required"], "amountHigh" => ["required"]]);
        if ($validator->fails()) {

            return $validator->errors()->first();
        }
        $pl = session()->get("loginUserInfo")->plantId;

        if (empty(request('plant_id'))) {
            $plants = DB::table("milk_plants")->where(["parentPlantId" => $pl])->get();
            $plant_ids = $plants->pluck('id');
        } else {
            $plant_ids = [request('plant_id')];
        }

        if (!empty(request('society_Code'))) {
            $dairy = DB::table('dairy_info')->where('society_code', $request->society_Code)->first();
            $dairy_ids = $dairy->id;
        } else {
            $dairies = DB::table('milk_plants')
                ->whereIn('milk_plants.id', $plant_ids)
                ->select('dairy_info.id', 'dairy_info.society_code', 'dairy_info.dairyName')
                ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
                ->where("plantdairymap.isActivated", 1)
                ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
                ->groupBy('plantdairymap.dairyId')
                ->get();
            // $dairy_ids = $dairies->pluck('id');

            $dairy_ids = [];
            foreach ($dairies as $d) {
                $dairy_ids[] = $d->id;
            }
            $dairy_ids = implode(",", $dairy_ids);
        }


        // $dairyId = DB::table('milk_plants')->where('parentPlantId', $pl)
        //     ->select('dairy_info.society_code', 'dairy_info.dairyName', 'plantdairymap.dairyId')
        //     ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
        //     ->where("plantdairymap.isActivated", 1)
        //     ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
        //     ->get();

        // $dairy_id = [];
        // foreach ($dairies as $d) {
        //     $dairy_id[] = $d->id;
        // }


        if (empty($dairy_ids)) {
            $q = [];
        } else {


            // $dairy = DB::table('dairy_info')->where('society_code', $request->society_Code)->first();

            $q = DB::select(DB::raw("SELECT  p.amount35_50,p.fat35_50,p.qty35_50,p.memberCode3, p.Date_2, s.Date_1, s.fat50__, s.memberCode2, s.qty50__, s.qty50__*'$request->amountHigh' as amount50__,
        mb.memberPersonalAccountName as memberName, mi.memberPersonalAadarNumber as adharNo, mb.memberPersonalBankName as bankName,
        mb.memberPersonalIfsc as ifscCode, mb.memberPersonalAccountNumber as accNo, mi.memberPersonalCode as memberCode
        ,mi.status, mi.dairyId
        FROM `member_personal_info` mi


        LEFT JOIN (SELECT (SUM(d.fat*d.milkQuality))/SUM(d.milkQuality) as fat50__, SUM(d.milkQuality) as qty50__, d.memberCode as memberCode2,
                            d.dairyId,d.status, d.date as Date_1

        FROM `daily_transactions` d WHERE d.date BETWEEN '$request->startDate' AND '$request->endDate' AND d.fat>5.0  AND d.dairyId IN ($dairy_ids) AND d.status='true'

        GROUP BY d.memberCode) s
        ON mi.memberPersonalCode=s.memberCode2


        LEFT JOIN member_personal_bank_info mb ON mi.id=mb.memberPersonalUserId AND mi.dairyId IN ($dairy_ids)

        LEFT JOIN (SELECT (SUM(t.fat*t.milkQuality))/SUM(t.milkQuality) as fat35_50, SUM(t.milkQuality) as qty35_50, t.memberCode as memberCode3, SUM(t.milkQuality)*'$request->amountLow' as amount35_50,
                     t.dairyId, t.status, t.date as Date_2

        FROM `daily_transactions` t WHERE t.date BETWEEN '$request->startDate' AND '$request->endDate' AND t.fat BETWEEN 3.5 AND 5.0  AND t.dairyId IN ($dairy_ids)  AND t.status='true'
        GROUP BY t.memberCode) p
        ON mi.memberPersonalCode=p.memberCode3 AND mi.dairyId IN ($dairy_ids)
        WHERE mi.status='true' AND mi.dairyId IN ($dairy_ids)  AND s.Date_1 BETWEEN '$request->startDate' AND '$request->endDate' OR
        p.Date_2 BETWEEN '$request->startDate' AND '$request->endDate'
        GROUP BY p.memberCode3, s.memberCode2"));
        }


        $report = view::make("headPlant._cmSubsidiaryData", [
            "data" => $q, "activepage" => "cm_subsidiary", 'dairyId' => "", 'dairyName' => "",
            'dairyCode' => request('society_Code'),
            "from" => $request->startDate, "to" => $request->endDate
        ]);

        // $pdf = \PDF::loadView('pdfheadPlant.cmSubsidiaryReport', ["data" => $q, 'dairyId' => "", 'dairyName' => "",
        //     'dairyCode' => request('society_Code'),
        //     "from" => $request->startDate, "to" => $request->endDate, 

        //     "headings" => ["dairyName" => $societyName, "report" => "Shift Summary Report", "society_code" => request('society_Code')],

        // ])->setPaper('A4', 'landscape');
        // $time = time();
        // $filename = "download/reports/" . time() . "_cm_subsidiary_report.pdf";
        // $pdf->save($filename);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $request->societyName, "society_code" => $request->society_Code,
                "report" => "Member report", "from" => $request->startDate, "to" => $request->endDate
            ],
            // "filename" => $filename,
        ];
    }
}
