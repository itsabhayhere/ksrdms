<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;
use View;

class PlantReportPdf extends Controller
{

    public function __construct()
    {
        $this->middleware('PlantAuth');
    }

public function CMSubsidiaryReportPdf()
    {

        $from = '2019-04-01';
        $to = '2019-06-02';

        $pl = session()->get("loginUserInfo")->plantId;
        $dairyId = DB::table('milk_plants')->where('parentPlantId', $pl)
            ->select('dairy_info.society_code', 'dairy_info.dairyName', 'plantdairymap.dairyId')
            ->leftjoin("plantdairymap", "plantdairymap.plantId", "=", "milk_plants.id")
            ->where("plantdairymap.isActivated", 1)
            ->leftjoin("dairy_info", "dairy_info.id", "=", "plantdairymap.dairyId")
            ->get();

        $dairy_id = [];
        foreach ($dairyId as $d) {

            $dairy_id[] = $d->dairyId;
        }

        $q = DB::select(DB::raw("SELECT  p.amount35_50,p.fat35_50,p.qty35_50,p.memberCode3, p.Date_2, s.Date_1, s.fat50__, s.memberCode2, s.qty50__, s.qty50__*5 as amount50__,
mb.memberPersonalAccountName as memberName, mi.memberPersonalAadarNumber as adharNo, mb.memberPersonalBankName as bankName,
mb.memberPersonalIfsc as ifscCode, mb.memberPersonalAccountNumber as accNo, mi.memberPersonalCode as memberCode
,mi.status, mi.dairyId
FROM `member_personal_info` mi


LEFT JOIN (SELECT (SUM(d.fat*d.milkQuality))/SUM(d.milkQuality) as fat50__, SUM(d.milkQuality) as qty50__, d.memberCode as memberCode2,
                    d.dairyId,d.status, d.date as Date_1

FROM `daily_transactions` d WHERE d.date BETWEEN '2019-04-01' AND '2019-06-02' AND d.fat>5.0  AND d.dairyId IN (" . implode(',', $dairy_id) . ") AND d.status='true'

GROUP BY d.memberCode) s
ON mi.memberPersonalCode=s.memberCode2


LEFT JOIN member_personal_bank_info mb ON mi.id=mb.memberPersonalUserId AND mi.dairyId IN (" . implode(',', $dairy_id) . ")

LEFT JOIN (SELECT (SUM(t.fat*t.milkQuality))/SUM(t.milkQuality) as fat35_50, SUM(t.milkQuality) as qty35_50, t.memberCode as memberCode3, SUM(t.milkQuality)*3.5 as amount35_50,
             t.dairyId, t.status, t.date as Date_2

FROM `daily_transactions` t WHERE t.date BETWEEN '2019-04-01' AND '2019-06-02' AND t.fat BETWEEN 3.5 AND 5.0  AND t.dairyId IN (" . implode(',', $dairy_id) . ") AND t.status='true'
GROUP BY t.memberCode) p
ON mi.memberPersonalCode=p.memberCode3 AND mi.dairyId IN (" . implode(',', $dairy_id) . ")
WHERE mi.status='true' AND mi.dairyId IN (" . implode(',', $dairy_id) . ") AND s.Date_1 BETWEEN '2019-04-01' AND '2019-06-02' OR
p.Date_2 BETWEEN '2019-04-01' AND '2019-06-02'
GROUP BY p.memberCode3, s.memberCode2"));

// dd($q);

        // return View('plant._cmSubsidiaryData', ["data" => $q, 'dairyId' => "", 'dairyName' => "", 'dairyCode' => "",
        //     "from" => $from, "to" => $to, "dairy" => $dairyId]);

        $time= time();
        $pdf = PDF::loadView('pdfPlant.cmSubsidiaryReport', ["data" => $q,"from" => $from, "to" => $to,"dairyName" => "All Verified Dairy","dairyCode" => "",
                         "headings" => ["dairyName" => "All Verified Dairy",
            "report" => "CM Subsidiary Report", "society_code" => ""]])->setPaper('A4', 'landscape');
        return $pdf->download('CM Subsidiary Report'.$time.'.pdf');
    }

}
