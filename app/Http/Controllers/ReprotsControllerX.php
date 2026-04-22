<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use View;

class ReprotsController extends Controller
{

    public function __construct()
    {
        $this->middleware('Auth');
        set_time_limit(300);
        ini_set('memory_limit', '-1');
    }

    public function report(Request $req)
    {
        if ($req->type) {
            $repType = $req->type;
        } else {
            $repType = "memberList";
        }

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairy = DB::table("dairy_info")->where('id', $dairyId)->get()->first();

        $memberPersonalInfo = DB::table('member_personal_info')
            ->where('dairyId', $dairyId)
            ->whereNotNull('ledgerId')
            ->where('status', "true")
            ->get();

        $salesData = DB::table('sales')
            ->where('dairyId', $dairyId)
            ->whereNotNull('ledgerId')
            ->where('status', "true")
            ->get();

        $cust = DB::table("customer")->where(["status" => "true", "dairyId" => $dairyId])->get();
        $suppliers = DB::table("suppliers")->where(["status" => "true", "dairyId" => $dairyId])->get();

        $returnData = array($memberPersonalInfo, $salesData, $cust, $suppliers);

        return view('report.report', ['returnData' => $returnData, "repType" => $repType, "activepage" => $repType, "dairy" => $dairy]);
    }

    /* Get Sale Report */

    /* Get All Party Name */
    public function getAllParty(Request $request)
    {
        $salesData = DB::table('sales')
            ->where('dairyId', $request->dairyId)
            ->whereNotNull('ledgerId')
            ->where('status', "true")
            ->get();

        return $salesData;
    }

    public function getSaleReport(Request $request)
    {
        // return $request->all();

        $dairy = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where('id', $dairy->dairyId)->get()->first();

        $from = date("Y-m-d", strtotime($request->saleStartDate));
        $to = date("Y-m-d", strtotime($request->saleEndDate));

        $query = DB::table('sales');
        if (!empty($request->SaleReportType)) {
            $query = $query->where('saleType', $request->SaleReportType);
        }

        if (!empty($request->saleAmountType)) {
            $query = $query->where('amountType', $request->saleAmountType);
        }

        if (!empty($request->userType)) {
            $query = $query->where('partyType', $request->userType);
        }

        if (!empty($request->saleStartDate) && !empty($request->saleEndDate)) {
            $query = $query->whereBetween('saleDate', [$from, $to]);
        }

        $query = $query->where('dairyId', $dairyInfo->id);
        $query = $query->where('status', "true");
        $query = $query->orderBy('created_at', 'desc');
        $queryData = $query->get();
        $sum = $query->sum("finalAmount");
        
        $report = View::make("report.salesReportModel", ["data" => $queryData, "sum" => $sum]);

        $data = ["data" => $queryData, "sum" => $sum, "headings" => [
            "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
            "report" => "Sales Report", "from" =>  date("d-m-Y", strtotime($from)), "to" =>  date("d-m-Y", strtotime($to)),
        ]];

        $pdf = \PDF::loadView('pdf.salesReportPdf', $data)->setPaper('A4', 'landscape');
        $filename = "download/reports/" . $dairyInfo->society_code . "_Sales_Report_" . $from . "_" . $to . ".pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Sales_Report_" . $from . "_" . $to . ".xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\SalesReportExport(["data" => $queryData, "sum" => $sum]), $filename_excel);
        
        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Sales Report", "from" =>  date("d-m-Y", strtotime($from)), "to" =>  date("d-m-Y", strtotime( $to)),
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    /* get Sale Report Pdf */
    public function getSaleReportPdf(Request $request)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->printPdfDara);
        return $pdf->download('Sale Report.pdf');
    }

    /* get member report */
    public function getMemberReport(Request $request)
    {
        $dairy = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where("id", $dairy->dairyId)->get()->first();

        $query = DB::table('member_personal_info');

        if (!empty($request->memberCode)) {
            $query = $query->where('memberPersonalCode', $request->memberCode);
        }

        // if (!empty($request->memberStartDate) && !empty($request->memberEndDate)) {
        //     $query = $query->whereBetween('memberPersonalregisterDate', [$request->memberStartDate, $request->memberEndDate]);
        // }

        $query = $query->where('dairyId', $dairy->dairyId);
        $query = $query->where('status', $request->status);
        $query = $query->where('ledgerId', "!=", "");
        $queryData = $query->get();

        $report = View::make("report.memberListReport", ["queryData" => $queryData]);

        $pdf = \PDF::loadView('pdf.memberListReportPdf', ["queryData" => $queryData, "headings" => [
            "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
            "report" => "Member report", "from" => "", "to" => "",
        ]])->setPaper('A4', 'landscape');
        $filename = "download/reports/" . $dairyInfo->society_code . "_Member_Report.pdf";
        $pdf->save($filename);


        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Member_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemberListReportExport(["queryData" => $queryData]), $filename_excel);


        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Member report", "from" => "", "to" => "",
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    /* get member report pdf */
    public function getMemberReportPdf(Request $request)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->MemberprintPdfData);
        return $pdf->download('Member Report.pdf');
    }

    /* get shift report */
    public function getShiftReport(Request $request)
    {
        $dairy = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where("id", $dairy->dairyId)->get()->first();

        if (!empty($request->shiftType)) {
            $q = DB::table('daily_transactions')
                ->where('shift', $request->shiftType)
                ->where('date', date("Y-m-d", strtotime($request->shiftDate)))
                ->where('dairyId', $request->dairyId)
                ->where('status', "true")
                ->orderByRaw('LENGTH(memberCode)', 'ASC')
                // ->orderBy('created_at', 'desc')->get();
                ->orderBy('memberCode','ASC')->get();

            $report = View::make("report.shiftSummaryReport", ["data1" => $q, "shift1" => $request->shiftType]);

            $pdf = \PDF::loadView('pdf.shiftSummaryReportPdf', [
                "data1" => $q, "shift1" => $request->shiftType,
                "headings" => [
                    "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                    "report" => "Shift Summary", "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)), "shiftType" => $request->shiftType,
                ],
            ]);
            $filename = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.pdf";
           $pdf->save($filename);


            $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.xlsx";
            $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\ShiftReportExport(["data1" => $q, "shift1" => $request->shiftType]), $filename_excel);


            return [
                "content" => (string) $report, "headings" => [
                    "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                    "report" => "Shift Summary", "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)), "shiftType" => $request->shiftType,
                ], "filename" => $filename,
                "filename_excel" => Storage::url($filename_excel)
            ];
        }

        $mq = DB::table('daily_transactions')
            ->where('date', date("Y-m-d", strtotime($request->shiftDate)))
            ->where('shift', 'morning')
            ->where('dairyId', $dairy->dairyId)
            ->where('status', "true")
            ->orderBy('created_at', 'desc')->get();

        $eq = DB::table('daily_transactions')
            ->where('date', date("Y-m-d", strtotime($request->shiftDate)))
            ->where('shift', 'evening')
            ->where('dairyId', $dairy->dairyId)
            ->where('status', "true")
            ->orderBy('created_at', 'desc')->get();

        $report = View::make("report.shiftSummaryReport", ["data1" => $mq, "data2" => $eq, "shift1" => "Morning Shift", "shift2" => "Evening Shift"]);

        $pdf = \PDF::loadView('pdf.shiftSummaryReportPdf', [
            "data1" => $mq, "data2" => $eq, "shift1" => "Morning Shift", "shift2" => "Evening Shift",
            "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Shift report", "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)), "shiftType" => $request->shiftType,
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.pdf";
        $pdf->save($filename);


        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\ShiftReportExport(["data1" => $mq, "data2" => $eq, "shift1" => "Morning Shift", "shift2" => "Evening Shift"]), $filename_excel);

        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Shift report", "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)), "shiftType" => $request->shiftType,
            ],
            "filename" => $filename,
           "filename_excel" => Storage::url($filename_excel)
        ];
    }

    public function getShiftReportPdf(Request $request)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->shiftprintPdfData);
        return $pdf->download('Shift Report.pdf');
    }

    /* get member passbook report */
    public function getMemberPassbookReport(Request $req)
    {
        $dairy = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where("id", $dairy->dairyId)->get()->first();

        $member = DB::table('member_personal_info')
            ->where('dairyId', $dairy->dairyId)
            ->where('memberPersonalCode', $req->memberCode)
            ->where('status', $req->status)
            ->get()->first();

        /* morning shift collaction */
        $q = DB::table('daily_transactions');
        if (!empty($req->memberPassbookStartDate) && !empty($req->memberPassbookEndDate)) {
            $q = $q->whereBetween('date', [date("Y-m-d", strtotime($req->memberPassbookStartDate)), date("Y-m-d", strtotime($req->memberPassbookEndDate))]);
        }
        $shift = $q->where([
            'dairyId' => $dairy->dairyId,
            'status' => "true",
            'memberCode' => $member->memberPersonalCode,
        ])
        ->orderBy('date', 'desc')->get()->groupBy(['date', 'shift']);

        // return $shift;

        // /* evening shift collaction */
        // $esq = DB::table('daily_transactions') ;
        // if (!empty($req->memberPassbookStartDate) && !empty($req->memberPassbookEndDate) ) {
        //     $esq = $esq->whereBetween('date', [$req->memberPassbookStartDate, $req->memberPassbookEndDate]);
        // }
        // $eveningShift = $esq->where('shift', 'evening')
        //                     ->where('dairyId', $req->dairyId)
        //                     ->where('status', $req->status)
        //                     ->where('memberCode', $member->memberPersonalCode)
        //                     ->orderBy('created_at', 'desc')->get();

        // $queryData = array($morningShift ,$eveningShift);

        // return $shift;
        $report = view::make("report.memberPassbook", ["shift" => $shift]);

        $pdf = \PDF::loadView('pdf.memberPassbookPdf', [
            "shift" => $shift,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "dairyId" =>$dairy->dairyId,
                "report" => "Member passbook report", "from" => date("d-m-Y", strtotime($req->memberPassbookStartDate)), "to" => date("d-m-Y", strtotime($req->memberPassbookEndDate)),
                'memberCode' => $member->memberPersonalCode,
                'memberName' => $member->memberPersonalName,
            ],
        ])->setOptions(["dpi" => 110]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_Member_Passbook_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Member_Passbook_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemberPassbookReportExport(["shift" => $shift]), $filename_excel);

        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Member passbook report", "from" => date("d-m-Y", strtotime($req->memberPassbookStartDate)), "to" =>  date("d-m-Y", strtotime($req->memberPassbookEndDate)),
                'memberCode' => $member->memberPersonalCode,
                'memberName' => $member->memberPersonalName,
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    /* get member passbook pdf */
    public function getMemberPassbookReportPdf(Request $request)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->memberPassbookprintPdfData);
        return $pdf->download('Member Passbook Report.pdf');
    }

    /* get Balance Sheet Report */
    public function getBalanceSheetReport(Request $req)
    {
        $dairy = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where("id", $dairy->dairyId)->get()->first();

        $dailyTrns = null;

        $dailyTrns = DB::table("daily_transactions")
            ->selectRaw("SUM(amount) as amount, AVG(fat) as fat, AVG(snf) as snf, SUM(milkQuality) as qty, count(*) as noOfShift, memberCode, memberName")
            ->where("dairyId", $dairy->dairyId)
            ->where("status", "true")
            ->whereBetween('date', [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->groupBy("memberCode")
            ->orderByRaw("CAST(memberCode AS unsigned)")
            ->get();

        $report = view::make("report.paymentRegister", ["dailyTrns" => $dailyTrns]);
        
        $pdf = \PDF::loadView('pdf.paymentRegisterPdf', [
            "dailyTrns" => $dailyTrns,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Payment Register Report", "from" =>  date("d-m-Y", strtotime($req->balanceSheetStartDate)), "to" =>  date("d-m-Y", strtotime($req->balanceSheetEndDate)),
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_payment_register_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_payment_register_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\PaymentRegisterReportExport(["dailyTrns" => $dailyTrns]), $filename_excel);

        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Payment Register Report", "from" =>  date("d-m-Y", strtotime($req->balanceSheetStartDate)), "to" =>  date("d-m-Y", strtotime($req->balanceSheetEndDate)),
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];

        // return $dailyTrns;

        // $query = DB::table('balance_sheet');

        // if (!empty($req->balanceSheetAmountType)) {
        //     $query = $query->where('amountType', $req->balanceSheetAmountType);
        // }
        // if (!empty($req->balanceSheetStartDate) && !empty($req->balanceSheetEndDate)) {
        //     $query = $query->whereBetween('saleDate', [$req->balanceSheetStartDate, $req->balanceSheetEndDate]);
        // }

        // $query = $query->where('dairyId', $req->dairyId);
        // $queryData = $query->get();

        // $dairyInfo = DB::table('dairy_info')
        //     ->where('id', $req->dairyId)
        //     ->get()->first();

        // $totalSale = 0;
        // $totalPayment = 0;
        // $totalPurchase = 0;
        // $totalExpense = 0;
        // foreach ($queryData as $queryDataValue) {

        //     if ($queryDataValue->transactionType == "sale") {
        //         $totalSale = $totalSale + $queryDataValue->finalAmount;
        //     }
        //     if ($queryDataValue->transactionType == "payment") {
        //         $totalPayment = $totalPayment + $queryDataValue->finalAmount;
        //     }
        //     if ($queryDataValue->transactionType == "purchase") {
        //         $totalPurchase = $totalPurchase + $queryDataValue->finalAmount;
        //     }
        //     if ($queryDataValue->transactionType == "expense") {
        //         $totalExpense = $totalExpense + $queryDataValue->finalAmount;
        //     }
        // }

        // $AccountTotal = $totalSale - ($totalPayment + $totalPurchase + $totalExpense);

        // $returnValur = array("ledgerId" => $dairyInfo->ledgerId, "totalSale" => $totalSale, "totalPayment" => $totalPayment, "totalPurchase" => $totalPurchase, "totalExpense" => $totalExpense, "AccountTotal" => $AccountTotal);

        // return $returnValur;
    }

    /* get Balance Sheet Report Pdf */
    public function getBalanceSheetReportPdf(Request $request)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->balanceSheetPdfData);
        return $pdf->download('Balance Sheet Report.pdf');
    }

    /* get ledger report */
    public function getLedgerReport(Request $request)
    {

        $dairy = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where("id", $dairy->dairyId)->get()->first();
        // return $request->all();

        $query = DB::table('ledger');
        $query = $query->orderBy('created_at', 'desc');
        if (!empty($request->leaderFor) || $request->leaderFor != null) {
            $query = $query->where('userType', $request->leaderFor);
        }

        $query = $query->where('dairyId', $request->dairyId);
        $queryData = $query->get();

        // return $queryData;
        $report = view::make("report.ledgerReport", ["queryData" => $queryData]);

        $pdf = \PDF::loadView('pdf.ledgerReportPdf', ["queryData" => $queryData, "headings" => [
            "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
            "report" => "Ledger Report",
        ]]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_ledger_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_ledger_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\LedgerReportExport(["queryData" => $queryData]), $filename_excel);

        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Ledger Report",
            ], "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    public function getLedgerReportReportPdf(Request $request)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->ledgerReportprintPdfData);
        return $pdf->download('Ladger Report.pdf');
    }

    public function getCmSubsidiaryReport(Request $request)
    {
        // return [$request->all()];
        $dairyId = session()->get('loginUserInfo')->dairyId;
        /* ledger info */
        $mems = DB::table('member_personal_info')
            ->where('dairyId', $dairyId)
            ->get();

        $from = date("Y-m-d", strtotime($request->startDate));
        $to = date("Y-m-d", strtotime($request->endDate));

        $lwamt = 4;
        $hghamt = 5;
        if (request('amountLow') != null) {
            $lwamt = request('amountLow');
        }
        if (request('amountHigh') != null) {
            $hghamt = request('amountHigh');
        }


        /* related dairy info */
        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->first();

        $q = DB::select(DB::raw("SELECT  p.amount35_50,p.fat35_50,p.qty35_50,p.memberCode3, p.Date_2, s.Date_1, s.fat50__, s.memberCode2, s.qty50__, s.qty50__*$hghamt as amount50__,
mb.memberPersonalAccountName as memberName, mi.memberPersonalAadarNumber as adharNo, mb.memberPersonalBankName as bankName,
mb.memberPersonalIfsc as ifscCode, mb.memberPersonalAccountNumber as accNo, mi.memberPersonalCode as memberCode
,mi.status, mi.dairyId
FROM `member_personal_info` mi


LEFT JOIN (SELECT (SUM(d.fat*d.milkQuality))/SUM(d.milkQuality) as fat50__, SUM(d.milkQuality) as qty50__, d.memberCode as memberCode2,
                    d.dairyId,d.status, d.date as Date_1

FROM `daily_transactions` d WHERE d.date BETWEEN '$from' AND '$to' AND d.fat>5.0  AND d.dairyId='$dairyId' AND d.status='true'

GROUP BY d.memberCode) s
ON mi.memberPersonalCode=s.memberCode2


LEFT JOIN member_personal_bank_info mb ON mi.id=mb.memberPersonalUserId AND mi.dairyId='$dairyId'

LEFT JOIN (SELECT (SUM(t.fat*t.milkQuality))/SUM(t.milkQuality) as fat35_50, SUM(t.milkQuality) as qty35_50, t.memberCode as memberCode3, SUM(t.milkQuality)*$lwamt as amount35_50,
             t.dairyId, t.status, t.date as Date_2

FROM `daily_transactions` t WHERE t.date BETWEEN '$from' AND '$to' AND t.fat BETWEEN 3.5 AND 5.0  AND t.dairyId='$dairyId' AND t.status='true'
GROUP BY t.memberCode) p
ON mi.memberPersonalCode=p.memberCode3 AND mi.dairyId='$dairyId'
WHERE mi.status='true' AND mi.dairyId='$dairyId' AND s.Date_1 BETWEEN '$from' AND '$to' OR
p.Date_2 BETWEEN '$from' AND '$to'
GROUP BY p.memberCode3, s.memberCode2 ORDER BY CAST(memberCode AS unsigned)"));
        $cont = view::make("report.cmSubsidiary", [
            "data" => $q, 'dairyId' => $dairyId, 'dairyName' => $dairyInfo->dairyName, 'dairyCode' => $dairyInfo->society_code,
            "from" => $from, "to" => $to
        ])->render();

        $pdf = \PDF::loadView('pdf.cmsubsidary', ["content" => $cont])->setPaper('A4', 'landscape')->setOptions(["dpi" => 170]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_cmSubsidy_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_cmSubsidy_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\CmSubsidiaryReportExport([
            "data" => $q, 'dairyId' => $dairyId, 'dairyName' => $dairyInfo->dairyName, 'dairyCode' => $dairyInfo->society_code,
            "from" => $from, "to" => $to
        ]), $filename_excel);


        return [
            "error" => false, "content" => $cont, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "CmSubsidy Report", "from" =>  date("d-m-Y", strtotime($from)), "to" =>  date("d-m-Y", strtotime($to))
            ], "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    public function getRateCardReport(Request $req)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = DB::table("dairy_info")->where('id', $dairyId)->get()->first();
        $colMan = DB::table("other_users")->where("dairyId", $dairyId)->where("userName", "DAIRYADMIN")->get()->first();

        if ($colMan == (null || "" || false)) {
            return ["error" => true, "msg" => "An error has occured. Error: COLLECTION_MANAGER_NOT_FOUND"];
        }

        if ($req->rateCardFor == ("cow")) {
            $shortCardId = $colMan->rateCardIdForCow;
            $cardFor = "cow";
            $for = "COW";
        } else {
            $shortCardId = $colMan->rateCardIdForBuffalo;
            $cardFor = "buff";
            $for = "BUFFALO";
        }

        $res['error'] = true;
        if (!$shortCardId) {
            $res['msg'] = "There are some error occured.";
            $res['etype'] = "danger";
            return response()->json($res);
        }

        $rateCard = DB::table('fat_snf_ratecard')
            ->where('rateCardShortId', $shortCardId)
            ->orderBy('fatRange')
            ->orderBy('snfRange')
            ->get();

        $rangeList = DB::table("rangelist")
            ->where("rateCardId", $shortCardId)
            ->get();

        $shortCard = DB::table('ratecardshort')
            ->where('id', $shortCardId)
            ->get()->first();

        $view = View::make('report.rateChartReport', [
            'rateCard' => $rateCard, "shortCard" => $shortCard, "rangeList" => $rangeList, "cardFor" => $cardFor,
        ]);

        $pdf = \PDF::loadView('pdf.rateChartReportPdf', [
            'rateCard' => $rateCard, "shortCard" => $shortCard, "rangeList" => $rangeList, "cardFor" => $cardFor,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Rate Card (" . $for . ")",
            ],
        ])->setPaper('A4', 'landscape')->setOptions(["dpi" => 150]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_ratecard_Report_($for).pdf";
        $pdf->save($filename);
        
        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_ratecard_Report_($for).xlsx";
        
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\RateChartReportExport([
            'rateCard' => $rateCard, "shortCard" => $shortCard, "rangeList" => $rangeList, "cardFor" => $cardFor,
        ]), $filename_excel);


        exit();
        return [
            "error" => false, "view" => $view->render(), "for" => $for, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Rate Card (" . $for . ")",
            ], "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    public function memberProfile(Request $req)
    {

        $dairyId = session()->get('loginUserInfo')->dairyId;

        $mem = DB::table("member_personal_info")
            ->where("dairyId", $dairyId)->where("memberPersonalCode", $req->memberCode)
            ->get()->first();

        $bank = DB::table("member_personal_bank_info")
            ->where("memberPersonalUserId", $mem->id)
            ->get()->first();
        $dailyTrns = DB::table("daily_transactions")
            ->selectRaw("SUM(amount) as amount, AVG(fat) as fat, AVG(snf) as snf, SUM(milkQuality) as qty, count(*) as noOfShift")
            ->whereBetween('date', [date("Y-m-d", strtotime($mem->memberPersonalregisterDate)), date("Y-m-d")])
            ->where("memberCode", $mem->memberPersonalCode)
            ->groupby("memberCode")
            ->get()->first();

        return view("report.memberProfile", ["mem" => $mem, "bank" => $bank, "dailyTrns" => $dailyTrns]);
    }

    // public function payments(){
    //     $u = Session::get("loginUserInfo");
    //     $balSheet = DB::table("balance_sheet")->where("ledgerId", $u->ledgerId)
    //                     ->orderby("created_at")->get();

    //     return view("member.payments", ["balSheet" => $balSheet, "mem" => $u, "activepage" => "payments"]);
    // }

    public function getMemStatementReport(Request $req)
    {

        $u = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where("id", $u->dairyId)->get()->first();

        $mem = DB::table("member_personal_info")->where("dairyId", $u->dairyId)->where("memberPersonalCode", $req->memberCode)->get()->first();
        if ($mem == (false || null)) {
            return ["error" => true, "msg" => "No member found"];
        }

        $ubal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->get()->first();

        $opb = DB::table("balance_sheet")->where(["ledgerId" => $mem->ledgerId, "transactionType" => "member_personal_info"])->get()->first();
//         if($req->groupByDate){
//             $balSheet = DB::table("balance_sheet")->where("ledgerId", $mem->ledgerId)
//                                 ->whereBetween("created_at", [date("Y-m-d", strtotime($req->startDate))." 00:00:00", date("Y-m-d", strtotime($req->endDate))." 23:59:59"])
// //                                ->where('transactionType', 'daily_transactions')
//                                 ->orderby("created_at")->get();
// //print_r($balSheet);
//             $report = view::make('report.memStatementReport', ['balSheet' => $balSheet, "ubal" => $ubal]);

//             return ["content" => (string)$report, "headings" => ["dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
//                 "report" => "Member Statement Report", "from" => (string)$req->startDate, "to" => (string)$req->endDate]];
//         }else{

        $ranges = $this->getDateranges($req->startDate, $req->endDate);

        $i = 0;
        foreach ($ranges as $r) {

            $milkCollection = DB::table("daily_transactions")
                ->where(['dairyId' => $u->dairyId, 'memberCode' => $mem->memberPersonalCode, "status" => "true"])
                ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->sum("amount");
         

            $localsalefinal = DB::table("sales")->where([
                "dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                "status" => "true", "saleType" => "local_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->sum('finalAmount');
            $localsalepaid = DB::table("sales")->where([
                "dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                "status" => "true", "saleType" => "local_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->sum('paidAmount');

            $productsale = DB::table("sales")->where([
                "dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                "status" => "true", "saleType" => "product_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();

            $productsaleCurrentBalance = DB::table("balance_sheet")
            ->where(['ledgerId' => $mem->ledgerId, "status" => "true"])
            ->where("transactionType", 'sales')
            // ->whereBetween("created_at", [date("Y-m-d h:i:s", strtotime($r['s'])), date("Y-m-d h:i:s", strtotime($r['e']))])
            ->whereDate("created_at", date("Y-m-d h:i:s", strtotime($r['e'])))
            ->select('currentBalance')
            ->get();
//print_r($productsale);

            $advance = DB::table("advance")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode])
                ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();

            $credit = DB::table("credit")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode])
                ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();

            $balSheet[$r['s']]['range'] = date("d-m-Y", strtotime($r['s'])) . " to " . date("d-m-Y", strtotime($r['e']));
            $balSheet[$r['s']]['milkCollection'] = number_format($milkCollection, 2, ".", "");
            $balSheet[$r['s']]['localsaleFinal'] = number_format($localsalefinal, 2, ".", "");
            $balSheet[$r['s']]['localsalePaid'] = number_format($localsalepaid, 2, ".", "");
            $balSheet[$r['s']]['productsale'] = $productsale;
            $balSheet[$r['s']]['advance'] = $advance;
            $balSheet[$r['s']]['credit'] = $credit;
            $i++;
        }

        // return $balSheet;

        $report = view::make('report.memStatementReport2', ['balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb,'type'=>'memStatementReport2']);

        $pdf = \PDF::loadView('pdf.memStatementPdf', [
            'balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.pdf";
//        echo $filename;
        $pdf->save($filename);


        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemStatementReportExport(['balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb]), $filename_excel);


        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];

        // return view('report.memStatementReport2', ['balSheet' => $balSheet]);
        // }
    }

    public function getMemStatementReport2(Request $req)
    {

        $u = Session::get("loginUserInfo");
        $dairyInfo = DB::table("dairy_info")->where("id", $u->dairyId)->get()->first();

        $mem = DB::table("member_personal_info")->where("dairyId", $u->dairyId)->where("memberPersonalCode", $req->memberCode)->get()->first();
        if ($mem == (false || null)) {
            return ["error" => true, "msg" => "No member found"];
        }

        $ubal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->get()->first();

        $opb = DB::table("balance_sheet")->where(["ledgerId" => $mem->ledgerId, "transactionType" => "member_personal_info"])->get()->first();
//         if($req->groupByDate){
//             $balSheet = DB::table("balance_sheet")->where("ledgerId", $mem->ledgerId)
//                                 ->whereBetween("created_at", [date("Y-m-d", strtotime($req->startDate))." 00:00:00", date("Y-m-d", strtotime($req->endDate))." 23:59:59"])
// //                                ->where('transactionType', 'daily_transactions')
//                                 ->orderby("created_at")->get();
// //print_r($balSheet);
//             $report = view::make('report.memStatementReport', ['balSheet' => $balSheet, "ubal" => $ubal]);

//             return ["content" => (string)$report, "headings" => ["dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
//                 "report" => "Member Statement Report", "from" => (string)$req->startDate, "to" => (string)$req->endDate]];
//         }else{

//        $ranges = $this->getDateranges($req->startDate, $req->endDate);
        $r['s'] = $req->startDate;
        $r['e'] = $req->endDate;
// print_r($ranges);
// exit;
        $i = 0;
//        foreach ($ranges as $r) {

            $milkCollection = DB::table("daily_transactions")
                ->where(['dairyId' => $u->dairyId, 'memberCode' => $mem->memberPersonalCode, "status" => "true"])
                ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();
//                ->sum("amount");
         

            $localsalefinal = DB::table("sales")->where([
                "dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                "status" => "true", "saleType" => "local_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();
//                ->sum('finalAmount');
            $localsalepaid = DB::table("sales")->where([
                "dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                "status" => "true", "saleType" => "local_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->sum('paidAmount');

            $productsale = DB::table("sales")->where([
                "dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode,
                "status" => "true", "saleType" => "product_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();

            $productsaleCurrentBalance = DB::table("balance_sheet")
            ->where(['ledgerId' => $mem->ledgerId, "status" => "true"])
            ->where("transactionType", 'sales')
            // ->whereBetween("created_at", [date("Y-m-d h:i:s", strtotime($r['s'])), date("Y-m-d h:i:s", strtotime($r['e']))])
            ->whereDate("created_at", date("Y-m-d h:i:s", strtotime($r['e'])))
            ->select('currentBalance')
            ->get();
//print_r($productsale);

            $advance = DB::table("advance")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode])
                ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();

            $credit = DB::table("credit")->where(["dairyId" => $u->dairyId, "partyCode" => $mem->memberPersonalCode])
                ->whereBetween("date", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->get();

            $balSheet['range'] = date("d-m-Y", strtotime($r['s'])) . " to " . date("d-m-Y", strtotime($r['e']));
            $balSheet['milkCollection'] = $milkCollection;
            $balSheet['localsaleFinal'] =$localsalefinal;
            $balSheet['localsalePaid'] =$localsalepaid;
            $balSheet['productsale'] = $productsale;
            $balSheet['advance'] = $advance;
            $balSheet['credit'] = $credit;
            $balSheet['type'] = 'memStatementReport3';
        //     $i++;
        // }

        // return $balSheet;

        $report = view::make('report.memStatementReport3', ['balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb]);

        // $pdf = \PDF::loadView('pdf.memStatementPdf', [
        //     'balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb,
        //     "headings" => [
        //         "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
        //         "report" => "Member Statement Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
        //     ],
        // ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.pdf";
//        echo $filename;
//       $pdf->save($filename);


        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemStatementReportExport(['balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb]), $filename_excel);


        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];

        // return view('report.memStatementReport2', ['balSheet' => $balSheet]);
        // }
    }
    public function getDateranges($from, $to)
    {

        $s = $from;
        $e = $to;
        $sd = date("Y-m-d", strtotime($s));
        $ed = date("Y-m-d", strtotime($e));
        $r = [];
        $i = 0;
        $rflag = 0;

        $loop = true;
        $tsd = $sd;

        // echo date("d", strtotime($tsd)) + (10 - (date("d", strtotime($tsd)) % 10)) ; exit;

        while ($loop) {
            $startday = 0;

            if ($i == 0) {
                $startday = 0;
            } else {
                $startday = 1;
            }

            $number = cal_days_in_month(CAL_GREGORIAN, date("m", strtotime($tsd . " +$startday day")), date("Y", strtotime($tsd . " +$startday day")));

            $__dt = date("d", strtotime($tsd . " +$startday day"));

            if ($number == 31 && $__dt <= 31 && $__dt > 20) {
                if ($__dt == 31) {
                    $day = 0;
                } elseif ($__dt == 30) {
                    $day = 1;
                } else {
                    $day = (11 - ($__dt % 10));
                }
            } elseif ($number == 28 && $__dt <= 31 && $__dt > 20) {
                $day = (8 - ($__dt % 10));
            } elseif ($number == 29 && $__dt <= 31 && $__dt > 20) {
                $day = (9 - ($__dt % 10));
            } else {
                if ($__dt == 30) {
                    $day = 0;
                } else {
                    $day = (10 - ($__dt % 10));
                }
            }

            $r[$i]["s"] = date("Y-m-d", strtotime($tsd . " +$startday day"));

            if (strtotime($tsd) < strtotime($ed)) {
                $r[$i]["e"] = date("Y-m-d", strtotime($tsd . " +" . ($day + $startday) . " day"));
                $tsd = date("Y-m-d", strtotime($tsd . " +" . ($day + $startday) . " day"));
            }

            if (strtotime($tsd) >= strtotime($ed)) {
                $loop = false;
                // $tsd = date("Y-m-d", strtotime($ed));
                // $r[$i+1]["s"] = date("Y-m-d", strtotime($tsd." +$startday day"));
                $r[$i]["e"] = date("Y-m-d", strtotime($ed));
                break;
            }

            $i++;
        }

        return $r;
    }

    public function getCustomerSalseReport(Request $req)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $cust = DB::table("customer")
            ->where("dairyId", $dairyId)->where("customerCode", $req->customerCode)
            ->get()->first();

        $opb = DB::table("balance_sheet")->where(["ledgerId" => $cust->ledgerId, "transactionType" => "customer", "status" => "true"])->get()->first();

        // $sale = DB::table(DB::raw('sales, advance, credit'))

        // ->where(["sales.dairyId" => $dairyId, "sales.partyCode" => $req->customerCode, "sales.status" => "true",
        //         "advance.dairyId" => $dairyId, "advance.partyCode" => $req->customerCode, "advance.status" => "true",
        //         "credit.dairyId" => $dairyId, "credit.partyCode" => $req->customerCode, "credit.status" => "true",
        // ])
        // ->whereBetween("sales.saleDate", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
        // ->whereBetween("advance.date", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
        // ->whereBetween("credit.date", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
        // ->get();

        $sale = DB::table("sales")
            ->selectRaw("dairyId, partyCode, partyName, remark, productPricePerUnit, productQuantity, productType, finalAmount, paidAmount, saleDate as date, CONCAT('debit') as type")
            ->where(["dairyId" => $dairyId, "partyCode" => $req->customerCode, "partyType" => "customer", "status" => "true"])
            ->whereBetween("saleDate", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))]);

        $advance = DB::table("advance")
            ->selectRaw("dairyId, partyCode, partyName, remark, '' as productPricePerUnit, '' as productQuantity, '' as productType, amount as finalAmount, '' as paidAmount, date, CONCAT('debit') as type")
            ->where(["dairyId" => $dairyId, "partyCode" => $req->customerCode, "partyType" => "customer"])
            ->whereBetween("date", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))]);

        $credit = DB::table("credit")
            ->selectRaw("dairyId, partyCode, partyName, remark, '' as productPricePerUnit, '' as productQuantity, '' as productType, amount as finalAmount, '' as paidAmount, date, CONCAT('credit') as type")
            ->where(["dairyId" => $dairyId, "partyCode" => $req->customerCode, "partyType" => "customer"])
            ->whereBetween("date", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
            ->unionAll($sale)
            ->unionAll($advance)
            ->get();

        $ubal = DB::table("user_current_balance")->where("ledgerId", $cust->ledgerId)->get()->first();

        $report = view::make("report.customerSalseReport", ["report" => $credit, "cust" => $cust, "ubal" => $ubal, "opb" => $opb]);

        $pdf = \PDF::loadView('pdf.customerSalsePdf', [
            "report" => $credit, "cust" => $cust, "ubal" => $ubal, "opb" => $opb,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Customer Sales Report", "from" => (string) $req->startDate, "to" => (string) $req->endDate,
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_customer_account_statement_report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_customer_account_statement_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\CustomerSalseReportExport(["report" => $credit, "cust" => $cust, "ubal" => $ubal, "opb" => $opb]), $filename_excel);

        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Customer Sales Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    public function getSupplier(Request $req)
    {

        // return $req->assll();
        $dairy = session()->get('dairyInfo');

        $dairyInfo = DB::table('dairy_info')->where("id", $dairy->id)->get()->first();

        $from = date('Y-m-d', strtotime($req->startDate));
        $to = date('Y-m-d', strtotime($req->endDate));

        $credit = DB::table("credit")->selectRaw('dairyId,ledgerId,"" as status,partyName,partyType,partyCode,date,"" as cash,"" as credit, amount as debit, "" as type, remark, CONCAT("credit") as type')->whereBetween("date", [$from, $to])
            ->where("partyCode", $req->suppliercode);

        $advance = DB::table("advance")->selectRaw('dairyId,ledgerId,"" as status,partyName,partyType,partyCode,date,"" as cash,"" as credit,amount as debit, "" as type, remark, CONCAT("debit") as type')->whereBetween("date", [$from, $to])
            ->where("partyCode", $req->suppliercode);

        $supplierdata = DB::table("purchase_setups")->selectRaw('dairyId,ledgerId,status,supplierName as partyName, "" as partyType,supplierCode as partyCode,date,amount as cash,paidAmount as credit,"" as debit, "" as type,itemPurchased as remark, CONCAT("debit") as type')
            ->where('supplierCode', $req->suppliercode)
            ->whereBetween('date', [$from, $to])
            ->unionAll($credit)
            ->unionAll($advance)
            ->orderBy('date', 'ASC')
            ->get();

        $report = view::make("report.supplierReport", compact('supplierdata'));

        $pdf = \PDF::loadView('pdf.supplierReportPdf', [
            "supplierdata" => $supplierdata,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Supplier Account Statement Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_supplier_account_statement_report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_supplier_account_statement_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\SupplierReportExport(compact('supplierdata')), $filename_excel);

        return [
            "content" => (string) $report, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Supplier Account Statement Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    public function getProfitLossReport(Request $req)
    {
        // echo $_SERVER['REMOTE_ADDR'];
        // return [$req->all()];

        $dairy = session()->get('dairyInfo');
        $dairyInfo = DB::table('dairy_info')->where("id", $dairy->id)->get()->first();

        $milk_collection = DB::table("daily_transactions")
            ->whereBetween("date", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
            ->where(["dairyId" => $dairy->id, "status" => "true"])
            ->sum('amount');

        $local_sale = DB::table("sales")
            ->whereBetween("saleDate", [date("Y-m-d 00:00:00", strtotime($req->startDate)), date("Y-m-d 23:59:59", strtotime($req->endDate))])
            ->where(["dairyId" => $dairy->id, "status" => "true", "saleType" => "local_sale"])
            ->sum('finalAmount');

        $plant_sale = DB::table("sales")
            ->whereBetween("saleDate", [date("Y-m-d 00:00:00", strtotime($req->startDate)), date("Y-m-d 23:59:59", strtotime($req->endDate))])
            ->where(["dairyId" => $dairy->id, "status" => "true", "saleType" => "plant_sale"])
            ->sum('finalAmount');

        $pro_sale = DB::table("sales")
            ->whereBetween("saleDate", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
            ->where(["dairyId" => $dairy->id, "status" => "true", "saleType" => "product_sale"])
            ->sum('finalAmount');

        $expense = DB::table("expense_setups")
            ->whereBetween("date", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
            ->where(["dairyId" => $dairy->id, "status" => "true"])
            ->sum('amount');

        $prods = DB::table("products")->select("productCode")->where(["dairyId" => $dairy->id])->get();
        $purchase = 0;
        foreach ($prods as $p) {
            $prodsetup = DB::table("purchase_setups")->select("productCode", "date", "time", "amount", "quantity")
                ->where(["dairyId" => $dairy->id, "status" => "true", "productCode" => $p->productCode])->orderby("date")->groupBy('productCode')->get()->toArray();

            $loopBreak = false;
            $loopStarted = false;
            for ($i = 0; $i < count($prodsetup); $i++) {

                $d1 = date("Y-m-d 00:00:00", strtotime($prodsetup[$i]->date));
                if (!isset($prodsetup[$i + 1])) {
                    $d2 = date("Y-m-d 23:59:59", strtotime($req->endDate));
                } else {
                     /* Starts - Commented, Sept 12, 2020, S */
                    // $d2 = date("Y-m-d 23:59:59", strtotime($prodsetup[$i + 1]->date . " -1 day"));
                    /* Ends - Commented, Sept 12, 2020, S */
                    /* Starts - Added, Sept 12, 2020, S */
                    $d2 = date("Y-m-d 23:59:59", strtotime($prodsetup[$i + 1]->date));
                    /* Ends - Added, Sept 12, 2020, S */
                }

                if (!$loopStarted && strtotime($req->startDate) >= strtotime($prodsetup[$i]->date)) {
                    if (strtotime($req->startDate) <= strtotime($d2)) {
                        $d1 = date("Y-m-d 00:00:00", strtotime($req->startDate));
                        $loopStarted = true;
                    } else {
                        continue;
                    }
                }

                if ($loopStarted && strtotime($req->endDate) > strtotime($prodsetup[$i]->date)) {
                    if (strtotime($req->endDate) <= strtotime($d2)) {
                        /* Starts - Commented, Sept 12, 2020, S */
                        // $d2 = date("Y-m-d 23:59:59", strtotime($req->endDate . " -1 day"));
                        /* Ends - Commented, Sept 12, 2020, S */
                        /* Starts - Added, Sept 12, 2020, S */
                        $d2 = date("Y-m-d 23:59:59", strtotime($req->endDate));
                        $loopBreak = true;
                        /* Ends - Added, Sept 12, 2020, S */
                    }
                }
                
                // if($_SERVER['REMOTE_ADDR'] == '106.207.158.150'){
                //     echo 'Pcode:'.$p->productCode;
                //     echo '<br>';
                //     echo 'D1:'.$d1;
                //     echo '<br>';
                //     echo 'D12:'.$d2;
                //     echo '<br>';
                // }
                
                $proq = DB::table("sales")
                    ->whereBetween("sales.saleDate", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
//                    ->whereBetween("sales.saleDate", [$d1, $d2])
                     ->where(['sales.saleType' => "product_sale", "sales.status" => "true","sales.dairyId" => $dairy->id,"sales.productType" => $prodsetup[$i]->productCode])
                     ->join("products", "products.productCode", "=", "sales.productType")
                    ->selectRaw('SUM(sales.purchaseAmount * sales.productQuantity) as total')->first();
                if ($prodsetup[$i]->quantity > 0 && $proq)  {
                    $singlerate =$proq->total; // $prodsetup[$i]->amount / $prodsetup[$i]->quantity;
                } else {
                    $singlerate = 0;
                }
                $purchase += $singlerate;

                // echo $prodsetup[$i]->productCode."  ".$d1." ".$d2."<br/>";
                // echo $p->productCode." ".$singlerate."  $proq<br/>";
                if ($loopBreak) {
                    break;
                }
            }
            // echo json_encode($prodsetup);

        }
        
        // if($_SERVER['REMOTE_ADDR'] == '106.207.158.150'){
        
        //     die;
        // }
        
        // echo $purchase;
        // return;
        // $purchase = DB::table("purchase_setups")
        //         ->whereBetween("date", [date("Y-m-d", strtotime($req->startDate)), date("Y-m-d", strtotime($req->endDate))])
        //         ->where(["dairyId" => $dairy->id, "status" => "true"])
        //         ->sum('amount');

        $cont = View::make("report.profitLossReport", [
            "local_sale" => $local_sale, "plant_sale" => $plant_sale, "pro_sale" => $pro_sale,
            "milk_collection" => $milk_collection,
            "dairyInfo" => $dairyInfo, "expense" => $expense, "purchase" => $purchase,
        ]);

        // $pdf = \PDF::loadView('pdf.profitLossReportPdf', [
        //     "local_sale" => $local_sale, "plant_sale" => $plant_sale, "pro_sale" => $pro_sale,
        //     "milk_collection" => $milk_collection,
        //     "dairyInfo" => $dairyInfo, "expense" => $expense, "purchase" => $purchase,
        //     "headings" => [
        //         "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
        //         "report" => "Profit Loss Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
        //     ],
        // ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_profit_loss_report.pdf";
       // $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_profit_loss_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\ProfitLossReportExport([
            "local_sale" => $local_sale, "plant_sale" => $plant_sale, "pro_sale" => $pro_sale,
            "milk_collection" => $milk_collection,
            "dairyInfo" => $dairyInfo, "expense" => $expense, "purchase" => $purchase,
        ]), $filename_excel);

        return [
            "content" => (string) $cont, "headings" => [
                "dairyName" => $dairyInfo->dairyName, "society_code" => $dairyInfo->society_code,
                "report" => "Profit Loss Report", "from" =>  date("d-m-Y", strtotime($req->startDate)), "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ], "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];
    }

    public function test()
    {
        // return $this->getDateranges(request("s"), request("e"));

        $s = request("s");
        $e = request("e");
        $sd = date("Y-m-d", strtotime($s));
        $ed = date("Y-m-d", strtotime($e));
        $r = [];
        $i = 0;
        $rflag = 0;

        $loop = true;
        $tsd = $sd;

        // echo date("d", strtotime($tsd)) + (10 - (date("d", strtotime($tsd)) % 10)) ; exit;

        while ($loop) {
            $startday = 0;

            if ($i == 0) {
                $startday = 0;
            } else {
                $startday = 1;
            }

            $number = cal_days_in_month(CAL_GREGORIAN, date("m", strtotime($tsd . " +$startday day")), date("Y", strtotime($tsd . " +$startday day")));

            $__dt = date("d", strtotime($tsd . " +$startday day"));

            if ($number == 31 && $__dt <= 31 && $__dt > 20) {
                if ($__dt == 31) {
                    $day = 0;
                } elseif ($__dt == 30) {
                    $day = 1;
                } else {
                    $day = (11 - ($__dt % 10));
                }
            } elseif ($number == 28 && $__dt <= 31 && $__dt > 20) {
                $day = (8 - ($__dt % 10));
            } elseif ($number == 29 && $__dt <= 31 && $__dt > 20) {
                $day = (9 - ($__dt % 10));
            } else {
                if ($__dt == 30) {
                    $day = 0;
                } else {
                    $day = (10 - ($__dt % 10));
                }
            }

            $r[$i]["s"] = date("Y-m-d", strtotime($tsd . " +$startday day"));

            if (strtotime($tsd) < strtotime($ed)) {
                $r[$i]["e"] = date("Y-m-d", strtotime($tsd . " +" . ($day + $startday) . " day"));
                $tsd = date("Y-m-d", strtotime($tsd . " +" . ($day + $startday) . " day"));
            }

            if (strtotime($tsd) >= strtotime($ed)) {
                $loop = false;
                // $tsd = date("Y-m-d", strtotime($ed));
                // $r[$i+1]["s"] = date("Y-m-d", strtotime($tsd." +$startday day"));
                $r[$i]["e"] = date("Y-m-d", strtotime($ed));
                break;
            }

            $i++;
        }

        return $r;
    }
}
