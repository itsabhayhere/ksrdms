<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use View;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;
use PDF;


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
            "dairyName" => $dairyInfo->dairyName,
            "society_code" => $dairyInfo->society_code,
            "report" => "Sales Report",
            "from" =>  date("d-m-Y", strtotime($from)),
            "to" =>  date("d-m-Y", strtotime($to)),
        ]];

        $pdf = \PDF::loadView('pdf.salesReportPdf', $data)->setPaper('A4', 'landscape');
        $filename = "download/reports/" . $dairyInfo->society_code . "_Sales_Report_" . $from . "_" . $to . ".pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Sales_Report_" . $from . "_" . $to . ".xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\SalesReportExport(["data" => $queryData, "sum" => $sum]), $filename_excel);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Sales Report",
                "from" =>  date("d-m-Y", strtotime($from)),
                "to" =>  date("d-m-Y", strtotime($to)),
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
            "dairyName" => $dairyInfo->dairyName,
            "society_code" => $dairyInfo->society_code,
            "report" => "Member report",
            "from" => "",
            "to" => "",
        ]])->setPaper('A4', 'landscape');
        $filename = "download/reports/" . $dairyInfo->society_code . "_Member_Report.pdf";
        $pdf->save($filename);


        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Member_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemberListReportExport(["queryData" => $queryData]), $filename_excel);


        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Member report",
                "from" => "",
                "to" => "",
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
                ->orderBy('memberCode', 'ASC')->get();

            

            $report = View::make("report.shiftSummaryReport", ["data1" => $q, "shift1" => $request->shiftType]);

            $pdf = \PDF::loadView('pdf.shiftSummaryReportPdf', [
                "data1" => $q,
                "shift1" => $request->shiftType,
                "headings" => [
                    "dairyName" => $dairyInfo->dairyName,
                    "society_code" => $dairyInfo->society_code,
                    "report" => "Shift Summary",
                    "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)),
                    "shiftType" => $request->shiftType,
                ],
            ]);
            $filename = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.pdf";
            $pdf->save($filename);


            $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.xlsx";
            $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\ShiftReportExport(["data1" => $q, "shift1" => $request->shiftType]), $filename_excel);


            return [
                "content" => (string) $report,
                "headings" => [
                    "dairyName" => $dairyInfo->dairyName,
                    "society_code" => $dairyInfo->society_code,
                    "report" => "Shift Summary",
                    "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)),
                    "shiftType" => $request->shiftType,
                ],
                "filename" => $filename,
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
            "data1" => $mq,
            "data2" => $eq,
            "shift1" => "Morning Shift",
            "shift2" => "Evening Shift",
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Shift report",
                "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)),
                "shiftType" => $request->shiftType,
            ],
        ]);

        $filename = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.pdf";
        $pdf->save($filename);


        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Shift_Summary_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\ShiftReportExport(["data1" => $mq, "data2" => $eq, "shift1" => "Morning Shift", "shift2" => "Evening Shift"]), $filename_excel);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Shift report",
                "shiftDate" => date("Y-m-d", strtotime($request->shiftDate)),
                "shiftType" => $request->shiftType,
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
        $q = DB::table('daily_transactions');
        if (!empty($req->memberPassbookStartDate) && !empty($req->memberPassbookEndDate)) {
            $q = $q->whereBetween('date', [date("Y-m-d", strtotime($req->memberPassbookStartDate)), date("Y-m-d", strtotime($req->memberPassbookEndDate))]);
        }
        $shift = $q->where([
            'dairyId' => $dairy->dairyId,
            'status' => "true",
            'memberCode' => $member->memberPersonalCode,
        ])
            ->orderBy('date', 'asc')
            ->get()->groupBy(['date', 'shift']);

         //dd($shift);

        //  return $shift;

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
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "dairyId" => $dairy->dairyId,
                "report" => "Member passbook report",
                "from" => date("d-m-Y", strtotime($req->memberPassbookStartDate)),
                "to" => date("d-m-Y", strtotime($req->memberPassbookEndDate)),
                'memberCode' => $member->memberPersonalCode,
                'memberName' => $member->memberPersonalName,
            ],
        ])->setOptions(["dpi" => 110]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_Member_Passbook_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_Member_Passbook_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemberPassbookReportExport(["shift" => $shift]), $filename_excel);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Member passbook report",
                "from" => date("d-m-Y", strtotime($req->memberPassbookStartDate)),
                "to" =>  date("d-m-Y", strtotime($req->memberPassbookEndDate)),
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
        $full=$req->full;


        $dailyTrns = null;

        $dailyTrns = DB::table("daily_transactions")
            ->selectRaw("SUM(amount) as amount, AVG(fat) as fat, AVG(snf) as snf, SUM(milkQuality) as qty, count(*) as noOfShift, memberCode, memberName")
            ->where("dairyId", $dairy->dairyId)
            ->where("status", "true")
            ->whereBetween('date', [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->groupBy("memberCode")
            ->orderByRaw("CAST(memberCode AS unsigned)")
            ->get();

           foreach($dailyTrns as $dailyTrn){

             $fatkg = DB::table('daily_transactions')
            ->select('daily_transactions.*')
            // ->addSelect(DB::raw('DATE(date) as only_date'))
            ->whereBetween('date', [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->where('dairyId', $dairy->dairyId)
            ->where('status', 'true')
            ->where('memberCode', $dailyTrn->memberCode)
            ->sum('fatkg');

            

            $snfkg = DB::table('daily_transactions')
            ->select('daily_transactions.*')
            // ->addSelect(DB::raw('DATE(date) as only_date'))
            ->whereBetween('date', [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])

            ->where('dairyId', $dairy->dairyId)
            ->where('status', 'true')
            ->where('memberCode', $dailyTrn->memberCode)
            ->sum('snfkg');
            $dailyTrn->fatkg =$fatkg;
            $dailyTrn->snfkg =$snfkg;
           }


    //   dd(DB::table('sales')->where('partyCode','01')->limit(10)->get());

        
        foreach ($dailyTrns as $val) {
             $val->local_sale = 0;
            $val->product_sale = 0;

            $val->credit = 0;
            $val->advance = 0;
            $val->currentBalance=0;
             $val->full=0;
          
 if($full==1){
            $local_sale = DB::table('sales')
                ->where('dairyId', $dairy->dairyId)
                ->where('partyCode', $val->memberCode)
                ->where('status', 'true')
                ->whereIn('saleType', ['local_sale'])
                ->whereBetween('saleDate', [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
                ->sum('finalAmount');
                //  dd(DB::table('credit')->first());
            $product_sale = DB::table('sales')
                 ->where('dairyId', $dairy->dairyId)
                ->where('partyCode', $val->memberCode)
                ->where('status', 'true')
                ->whereIn('saleType', ['product_sale'])
                ->whereBetween('saleDate', [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
                 ->sum('finalAmount');

            // Credit/Advance data
            $credit = DB::table('credit')
                 ->where('dairyId', $dairy->dairyId)
                ->where('partyCode', $val->memberCode)
                ->whereBetween('date', [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
                ->orderBy("created_at", "desc")
                 ->sum('amount');

            $advance = DB::table('advance')
                ->where('dairyId', $dairy->dairyId)
                ->where('partyCode', $val->memberCode)
                ->whereBetween('date',[date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
               // ->orderBy("created_at", "desc")
                ->sum('amount');

            $val->local_sale = $local_sale;
            $val->product_sale = $product_sale;

            $val->credit = $credit;
            $val->advance = $advance;
             
              
          
           $milkCollection1 = DB::table("daily_transactions as dt")
            ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "dt.id")
                    ->where("bs.transactionType", "daily_transactions");
            })
            ->where([
                "dt.dairyId" => $dairy->dairyId,
                "dt.memberCode" => $val->memberCode,
                "dt.status" => "true"
            ])
            ->whereBetween("dt.date", [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->get();

        $localsaleFinal1 = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $val->memberCode,
                "s.status" => "true",
                "s.saleType" => "local_sale"
            ])
            ->whereBetween("s.saleDate", [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->get();

        $localsalepaid = $localsaleFinal1->sum('paidAmount');

        $productsale1 = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", "p.productName", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->leftJoin("products as p", "p.productCode", "=", "s.productType")
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $val->memberCode,
                "s.status" => "true",
                "s.saleType" => "product_sale"
            ])
            ->whereBetween("s.saleDate", [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->get();
        $advance1 = DB::table("advance as a")
            ->select("a.*", "bs.currentBalance", DB::raw("'a' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "a.id")
                    ->where("bs.transactionType", "advance");
            })
            ->where([
                "a.dairyId" => $dairy->dairyId,
                "a.partyCode" => $val->memberCode
            ])
            ->whereBetween("a.date", [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->get();
        $credit1 = DB::table("credit as c")
            ->select("c.*", "bs.currentBalance", DB::raw("'c' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "c.id")
                    ->where("bs.transactionType", "credit");
            })
            ->where([
                "c.dairyId" => $dairy->dairyId,
                "c.partyCode" => $val->memberCode
            ])
            ->whereBetween("c.date", [date("Y-m-d", strtotime($req->balanceSheetStartDate)), date("Y-m-d", strtotime($req->balanceSheetEndDate))])
            ->get();

                $allTransactions1 = collect()
            ->merge($milkCollection1)
            ->merge($localsaleFinal1)
            ->merge($productsale1)
            ->merge($advance1)
            ->merge($credit1);

        $latestTransaction1 = $allTransactions1->sortByDesc('created_at')->first();
         $val->currentBalance= $latestTransaction1->currentBalance;
         $val->full=1;
            }
     
        }

        $report = view::make("report.paymentRegister", ["dailyTrns" => $dailyTrns,'full'=>$full]);

        $pdf = \PDF::loadView('pdf.paymentRegisterPdf', [
            "dailyTrns" => $dailyTrns,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Payment Register Report",
                "from" =>  date("d-m-Y", strtotime($req->balanceSheetStartDate)),
                "to" =>  date("d-m-Y", strtotime($req->balanceSheetEndDate)),
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_payment_register_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_payment_register_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\PaymentRegisterReportExport(["dailyTrns" => $dailyTrns]), $filename_excel);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Payment Register Report",
                "from" =>  date("d-m-Y", strtotime($req->balanceSheetStartDate)),
                "to" =>  date("d-m-Y", strtotime($req->balanceSheetEndDate)),
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
            "dairyName" => $dairyInfo->dairyName,
            "society_code" => $dairyInfo->society_code,
            "report" => "Ledger Report",
        ]]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_ledger_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_ledger_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\LedgerReportExport(["queryData" => $queryData]), $filename_excel);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Ledger Report",
            ],
            "filename" => $filename,
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
        $select_report = $request->select_report;
        $reportSelectcmSubsidiary = $request->reportSelectcmSubsidiary;
        $searchdata = '';

        if ($reportSelectcmSubsidiary == 'Antyodya') {
            $searchdata = " And mi.memberpp_familyid!='' ";
        }
        if ($reportSelectcmSubsidiary == 'Simple') {
            $searchdata = " And mi.memberpp_familyid is null ";
        }
        $lwamt = 4;
        $hghamt = 5;
        if (request('amountLow') != null) {
            $lwamt = request('amountLow');
        }
        if (request('amountHigh') != null) {
            $hghamt = request('amountHigh');
        }


        if ($select_report == 'Combine') {
            $hghamt = $lwamt;
        }

        /* related dairy info */
        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->first();

        $q = DB::select(DB::raw("SELECT  p.amount35_50,p.fat35_50,p.qty35_50,p.memberCode3, p.Date_2, s.Date_1, s.fat50__, s.memberCode2, s.qty50__, s.qty50__*$hghamt as amount50__,
mb.memberPersonalAccountName as memberName, mi.memberPersonalAadarNumber as adharNo, mb.memberPersonalBankName as bankName,
mb.memberPersonalIfsc as ifscCode, mb.memberPersonalAccountNumber as accNo, mi.memberPersonalCode as memberCode,mi.memberPersonalMobileNumber as mobileNo,mi.memberPersonalCategory as category,mi.is_andtodya as is_andtodya,mi.memberpp_familyid as pppid,mi.Verified_income as Verified_income,mb.memberPersonalAccountFName as FName,
mi.status, mi.dairyId
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
WHERE mi.status='true' AND mi.dairyId='$dairyId'  AND (s.Date_1 BETWEEN '$from' AND '$to' OR
p.Date_2 BETWEEN '$from' AND '$to'  ) $searchdata
GROUP BY p.memberCode3, s.memberCode2 ORDER BY CAST(memberCode AS unsigned)"));

        // foreach($q as $value){

        //     $value->reporttype=$reportSelectcmSubsidiary;

        // }
        /*echo "SELECT  p.amount35_50,p.fat35_50,p.qty35_50,p.memberCode3, p.Date_2, s.Date_1, s.fat50__, s.memberCode2, s.qty50__, s.qty50__*$hghamt as amount50__,
mb.memberPersonalAccountName as memberName, mi.memberPersonalAadarNumber as adharNo, mb.memberPersonalBankName as bankName,
mb.memberPersonalIfsc as ifscCode, mb.memberPersonalAccountNumber as accNo, mi.memberPersonalCode as memberCode,mi.memberPersonalMobileNumber as mobileNo,mi.memberPersonalCategory as category,mi.is_andtodya as is_andtodya,mi.memberpp_familyid as pppid,mb.memberPersonalAccountFName as FName,
mi.status, mi.dairyId
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
WHERE mi.status='true' AND mi.dairyId='$dairyId'  AND (s.Date_1 BETWEEN '$from' AND '$to' OR
p.Date_2 BETWEEN '$from' AND '$to'  ) $searchdata
GROUP BY p.memberCode3, s.memberCode2 ORDER BY CAST(memberCode AS unsigned)";
dd($q);*/

        if ($select_report == 'Seperate') {
            $cont = view::make("report.cmSubsidiary", [
                "data" => $q,
                'dairyId' => $dairyId,
                'dairyName' => $dairyInfo->dairyName,
                'dairyCode' => $dairyInfo->society_code,
                "from" => $from,
                "to" => $to,
                "reportType" => $reportSelectcmSubsidiary
            ])->render();
        } else {
            $cont = view::make("report.cmSubsidiary1", [
                "data" => $q,
                'dairyId' => $dairyId,
                'dairyName' => $dairyInfo->dairyName,
                'dairyCode' => $dairyInfo->society_code,
                "from" => $from,
                "to" => $to,
                "reportType" => $reportSelectcmSubsidiary
            ])->render();
        }
        $pdf = \PDF::loadView('pdf.cmsubsidary', ["content" => $cont])->setPaper('A4', 'landscape')->setOptions(["dpi" => 170]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_cmSubsidy_Report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_cmSubsidy_Report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\CmSubsidiaryReportExport([
            "data" => $q,
            'dairyId' => $dairyId,
            'dairyName' => $dairyInfo->dairyName,
            'dairyCode' => $dairyInfo->society_code,
            "from" => $from,
            "to" => $to,
            "reportType" => $reportSelectcmSubsidiary
        ]), $filename_excel);


        return [
            "error" => false,
            "content" => $cont,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "CmSubsidy Report",
                "from" =>  date("d-m-Y", strtotime($from)),
                "to" =>  date("d-m-Y", strtotime($to))
            ],
            "filename" => $filename,
            "reportType" => $reportSelectcmSubsidiary,
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
            'rateCard' => $rateCard,
            "shortCard" => $shortCard,
            "rangeList" => $rangeList,
            "cardFor" => $cardFor,
        ]);

        $pdf = \PDF::loadView('pdf.rateChartReportPdf', [
            'rateCard' => $rateCard,
            "shortCard" => $shortCard,
            "rangeList" => $rangeList,
            "cardFor" => $cardFor,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Rate Card (" . $for . ")",
            ],
        ])->setPaper('A4', 'landscape')->setOptions(["dpi" => 150]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_ratecard_Report_($for).pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_ratecard_Report_($for).xlsx";

        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\RateChartReportExport([
            'rateCard' => $rateCard,
            "shortCard" => $shortCard,
            "rangeList" => $rangeList,
            "cardFor" => $cardFor,
        ]), $filename_excel);


        exit();
        return [
            "error" => false,
            "view" => $view->render(),
            "for" => $for,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Rate Card (" . $for . ")",
            ],
            "filename" => $filename,
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
                "dairyId" => $u->dairyId,
                "partyCode" => $mem->memberPersonalCode,
                "status" => "true",
                "saleType" => "local_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->sum('finalAmount');
            $localsalepaid = DB::table("sales")->where([
                "dairyId" => $u->dairyId,
                "partyCode" => $mem->memberPersonalCode,
                "status" => "true",
                "saleType" => "local_sale",
            ])
                ->whereBetween("saleDate", [date("Y-m-d", strtotime($r['s'])), date("Y-m-d", strtotime($r['e']))])
                ->sum('paidAmount');

            $productsale = DB::table("sales")->where([
                "dairyId" => $u->dairyId,
                "partyCode" => $mem->memberPersonalCode,
                "status" => "true",
                "saleType" => "product_sale",
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
            // $balSheet[$r['s']]['milkCollection'] = round($milkCollection, 2, ".", "");
            // $balSheet[$r['s']]['localsaleFinal'] = round($localsalefinal, 2, ".", "");
            // $balSheet[$r['s']]['localsalePaid'] = round($localsalepaid, 2, ".", "");
            // $balSheet[$r['s']]['productsale'] = $productsale;
            // $balSheet[$r['s']]['advance'] = $advance;
            // $balSheet[$r['s']]['credit'] = $credit;
            $balSheet[$r['s']]['milkCollection'] = $milkCollection;
            $balSheet[$r['s']]['localsaleFinal'] = $localsalefinal;
            $balSheet[$r['s']]['localsalePaid'] = $localsalepaid;
            $balSheet[$r['s']]['productsale'] = $productsale;
            $balSheet[$r['s']]['advance'] = $advance;
            $balSheet[$r['s']]['credit'] = $credit;
            $i++;
        }

        // return $balSheet;

        $report = view::make('report.memStatementReport2', ['balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb, 'type' => 'memStatementReport2']);

        $pdf = \PDF::loadView('pdf.memStatementPdf', [
            'balSheet' => $balSheet,
            "ubal" => $ubal,
            "mem" => $mem,
            "opb" => $opb,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report",
                "from" =>  date("d-m-Y", strtotime($req->startDate)),
                "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.pdf";
        //        echo $filename;
        $pdf->save($filename);


        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemStatementReportExport(['balSheet' => $balSheet, "ubal" => $ubal, "mem" => $mem, "opb" => $opb]), $filename_excel);


        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report",
                "from" =>  date("d-m-Y", strtotime($req->startDate)),
                "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
            "filename" => $filename,
            "filename_excel" => Storage::url($filename_excel)
        ];

        // return view('report.memStatementReport2', ['balSheet' => $balSheet]);
        // }
    }

    /* public function getMemStatementReport2(Request $req)
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
    }*/
    public function getMemStatementReport2(Request $req)
    {
        $u = Session::get("loginUserInfo");

        $dairyInfo = DB::table("dairy_info")
            ->where("id", $u->dairyId)
            ->first();

        $mem = DB::table("member_personal_info")
            ->where("dairyId", $u->dairyId)
            ->where("memberPersonalCode", $req->memberCode)
            ->first();

        if (!$mem) {
            return ["error" => true, "msg" => "No member found"];
        }

        $ubal = DB::table("user_current_balance")
            ->where("ledgerId", $mem->ledgerId)
            ->first();

        $opb = DB::table("balance_sheet")
            ->where([
                "ledgerId" => $mem->ledgerId,
                "transactionType" => "member_personal_info"
            ])
            ->first();



        $startDate = date("Y-m-d", strtotime($req->startDate));
        $endDate = date("Y-m-d", strtotime($req->endDate));

        // Milk Collection
        $milkCollection = DB::table("daily_transactions as dt")
            ->select("dt.*", "bs.currentBalance")
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "dt.id")
                    ->where("bs.transactionType", "daily_transactions");
            })
            ->where([
                "dt.dairyId" => $u->dairyId,
                "dt.memberCode" => $mem->memberPersonalCode,
                "dt.status" => "true"
            ])
            ->whereBetween("dt.date", [$startDate, $endDate])
            ->get();

        // Local Sale
        $localsaleFinal = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance")
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->where([
                "s.dairyId" => $u->dairyId,
                "s.partyCode" => $mem->memberPersonalCode,
                "s.status" => "true",
                "s.saleType" => "local_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate, $endDate])
            ->get();

        $localsalepaid = $localsaleFinal->sum('paidAmount');

        // Product Sale
        $productsale = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", "p.productName")
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->leftJoin("products as p", "p.productCode", "=", "s.productType")
            ->where([
                "s.dairyId" => $u->dairyId,
                "s.partyCode" => $mem->memberPersonalCode,
                "s.status" => "true",
                "s.saleType" => "product_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate, $endDate])
            ->get();

        // Advance
        $advance = DB::table("advance as a")
            ->select("a.*", "bs.currentBalance")
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "a.id")
                    ->where("bs.transactionType", "advance");
            })
            ->where([
                "a.dairyId" => $u->dairyId,
                "a.partyCode" => $mem->memberPersonalCode
            ])
            ->whereBetween("a.date", [$startDate, $endDate])
            ->get();

        // Credit
        $credit = DB::table("credit as c")
            ->select("c.*", "bs.currentBalance")
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "c.id")
                    ->where("bs.transactionType", "credit");
            })
            ->where([
                "c.dairyId" => $u->dairyId,
                "c.partyCode" => $mem->memberPersonalCode
            ])
            ->whereBetween("c.date", [$startDate, $endDate])
            ->get();

        $balSheet = [
            'range' => date("d-m-Y", strtotime($startDate)) . " to " . date("d-m-Y", strtotime($endDate)),
            'milkCollection' => $milkCollection,
            'localsaleFinal' => $localsaleFinal,
            'localsalePaid' => $localsalepaid,
            'productsale' => $productsale,
            'advance' => $advance,
            'credit' => $credit,
            'type' => 'memStatementReport3'
        ];


        $report = view::make('report.memStatementReport3', [
            'balSheet' => $balSheet,
            'ubal' => $ubal,
            'mem' => $mem,
            'opb' => $opb
        ]);

        // $filename = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.pdf";
        $pdfFilename = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.pdf";
        $viewData = [
            'balSheet' => $balSheet,
            'ubal' => $ubal,
            'mem' => $mem,
            'opb' => $opb,
            'headings' => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report",
                "from" => date("d-m-Y", strtotime($req->startDate)),
                "to" => date("d-m-Y", strtotime($req->endDate)),
            ],
        ];

        Pdf::loadView('pdf.memStatementDetailsPdf', $viewData)->save($pdfFilename);
        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_member_account_statement_report.xlsx";
        \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\MemStatementReportExport([
            'balSheet' => $balSheet,
            'ubal' => $ubal,
            'mem' => $mem,
            'opb' => $opb
        ]), $filename_excel);

        return [
            "content" => (string)$report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Member Statement Report",
                "from" => date("d-m-Y", strtotime($req->startDate)),
                "to" => date("d-m-Y", strtotime($req->endDate)),
            ],
            "filename" => $pdfFilename,
            "filename_excel" => Storage::url($filename_excel)
        ];
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
            "report" => $credit,
            "cust" => $cust,
            "ubal" => $ubal,
            "opb" => $opb,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Customer Sales Report",
                "from" => (string) $req->startDate,
                "to" => (string) $req->endDate,
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_customer_account_statement_report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_customer_account_statement_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\CustomerSalseReportExport(["report" => $credit, "cust" => $cust, "ubal" => $ubal, "opb" => $opb]), $filename_excel);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Customer Sales Report",
                "from" =>  date("d-m-Y", strtotime($req->startDate)),
                "to" =>  date("d-m-Y", strtotime($req->endDate)),
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
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Supplier Account Statement Report",
                "from" =>  date("d-m-Y", strtotime($req->startDate)),
                "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
        ]);
        $filename = "download/reports/" . $dairyInfo->society_code . "_supplier_account_statement_report.pdf";
        $pdf->save($filename);

        $filename_excel = "download/reports/" . $dairyInfo->society_code . "_supplier_account_statement_report.xlsx";
        $excel = \Maatwebsite\Excel\Facades\Excel::store(new \App\Exports\SupplierReportExport(compact('supplierdata')), $filename_excel);

        return [
            "content" => (string) $report,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Supplier Account Statement Report",
                "from" =>  date("d-m-Y", strtotime($req->startDate)),
                "to" =>  date("d-m-Y", strtotime($req->endDate)),
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
                    ->where(['sales.saleType' => "product_sale", "sales.status" => "true", "sales.dairyId" => $dairy->id, "sales.productType" => $prodsetup[$i]->productCode])
                    ->join("products", "products.productCode", "=", "sales.productType")
                    ->selectRaw('SUM(sales.purchaseAmount * sales.productQuantity) as total')->first();
                if ($prodsetup[$i]->quantity > 0 && $proq) {
                    $singlerate = $proq->total; // $prodsetup[$i]->amount / $prodsetup[$i]->quantity;
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
            "local_sale" => $local_sale,
            "plant_sale" => $plant_sale,
            "pro_sale" => $pro_sale,
            "milk_collection" => $milk_collection,
            "dairyInfo" => $dairyInfo,
            "expense" => $expense,
            "purchase" => $purchase,
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
            "local_sale" => $local_sale,
            "plant_sale" => $plant_sale,
            "pro_sale" => $pro_sale,
            "milk_collection" => $milk_collection,
            "dairyInfo" => $dairyInfo,
            "expense" => $expense,
            "purchase" => $purchase,
        ]), $filename_excel);

        return [
            "content" => (string) $cont,
            "headings" => [
                "dairyName" => $dairyInfo->dairyName,
                "society_code" => $dairyInfo->society_code,
                "report" => "Profit Loss Report",
                "from" =>  date("d-m-Y", strtotime($req->startDate)),
                "to" =>  date("d-m-Y", strtotime($req->endDate)),
            ],
            "filename" => $filename,
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

    // public function exportMemberReport(Request $request)
    // {

    //     $partyCode = $request->partyCode;
    //     $status = $request->status;
    //     $startDate = $request->memberPassbookStartDate;
    //     $endDate = $request->memberPassbookEndDate;
    //     $dairy = Session::get("loginUserInfo");

    //     // Test

    //     $startDate = date("Y-m-d", strtotime($startDate));
    //     $endDate = date("Y-m-d", strtotime($endDate));

    //     // Milk Collection
    //     $milkCollection = DB::table("daily_transactions as dt")
    //         ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "dt.id")
    //                 ->where("bs.transactionType", "daily_transactions");
    //         })
    //         ->where([
    //             "dt.dairyId" => $dairy->dairyId,
    //             "dt.memberCode" => $partyCode,
    //             "dt.status" => "true"
    //         ])
    //         ->whereBetween("dt.date", [$startDate, $endDate])
    //         ->get();

    //     // Local Sale
    //     $localsaleFinal = DB::table("sales as s")
    //         ->select("s.*", "bs.currentBalance", DB::raw("'s' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "s.id")
    //                 ->where("bs.transactionType", "sales");
    //         })
    //         ->where([
    //             "s.dairyId" => $dairy->dairyId,
    //             "s.partyCode" => $partyCode,
    //             "s.status" => "true",
    //             "s.saleType" => "local_sale"
    //         ])
    //         ->whereBetween("s.saleDate", [$startDate, $endDate])
    //         ->get();

    //     $localsalepaid = $localsaleFinal->sum('paidAmount');

    //     // Product Sale
    //     $productsale = DB::table("sales as s")
    //         ->select("s.*", "bs.currentBalance", "p.productName", DB::raw("'s' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "s.id")
    //                 ->where("bs.transactionType", "sales");
    //         })
    //         ->leftJoin("products as p", "p.productCode", "=", "s.productType")
    //         ->where([
    //             "s.dairyId" => $dairy->dairyId,
    //             "s.partyCode" => $partyCode,
    //             "s.status" => "true",
    //             "s.saleType" => "product_sale"
    //         ])
    //         ->whereBetween("s.saleDate", [$startDate, $endDate])
    //         ->get();

    //     // Advance
    //     $advance = DB::table("advance as a")
    //         ->select("a.*", "bs.currentBalance", DB::raw("'a' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "a.id")
    //                 ->where("bs.transactionType", "advance");
    //         })
    //         ->where([
    //             "a.dairyId" => $dairy->dairyId,
    //             "a.partyCode" => $partyCode
    //         ])
    //         ->whereBetween("a.date", [$startDate, $endDate])
    //         ->get();

    //     // Credit
    //     $credit = DB::table("credit as c")
    //         ->select("c.*", "bs.currentBalance", DB::raw("'c' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "c.id")
    //                 ->where("bs.transactionType", "credit");
    //         })
    //         ->where([
    //             "c.dairyId" => $dairy->dairyId,
    //             "c.partyCode" => $partyCode
    //         ])
    //         ->whereBetween("c.date", [$startDate, $endDate])
    //         ->get();
    //     $allTransactions = collect()
    //         ->merge($milkCollection)
    //         ->merge($localsaleFinal)
    //         ->merge($productsale)
    //         ->merge($advance)
    //         ->merge($credit);
    //     $latestTransaction = $allTransactions->sortByDesc(function ($item) {
    //         return strtotime($item->date ?? $item->saleDate);
    //     })->first();

    //     $lastCurrentBalance = $latestTransaction->currentBalance ?? '0.00';
    //     $lastCurrentBalance = str_replace(' cr', '', $latestTransaction->currentBalance ?? '0.00');
    //     $balSheet = [
    //         'range' => date("d-m-Y", strtotime($startDate)) . " to " . date("d-m-Y", strtotime($endDate)),
    //         'milkCollection' => $milkCollection,
    //         'localsaleFinal' => $localsaleFinal,
    //         'localsalePaid' => $localsalepaid,
    //         'productsale' => $productsale,
    //         'advance' => $advance,
    //         'credit' => $credit,
    //         'lastCurrentBalance' => $lastCurrentBalance,
    //     ];

    //     // TestEnd

    //     $member = DB::table('member_personal_info')
    //         ->where('dairyId', $dairy->dairyId)
    //         ->where('memberPersonalCode', $partyCode)
    //         ->where('status', $status)
    //         ->first();
    //     if (!$member) {
    //         return response()->json(['error' => 'Member not found'], 404);
    //     }
    //     $query = DB::table('daily_transactions')
    //         ->where('dairyId', $dairy->dairyId)
    //         ->where('status', 'true')
    //         ->where('memberCode', $partyCode)
    //         ->orderBy('date', 'desc');

    //     if (!empty($startDate) && !empty($endDate)) {
    //         $query->whereBetween('date', [
    //             date("Y-m-d", strtotime($startDate)),
    //             date("Y-m-d", strtotime($endDate))
    //         ]);
    //     }

    //     $transactions = $query->get()->groupBy(['date', 'shift']);
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setTitle('Combined Report');

    //     $headerStyleArray = [
    //         'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    //         'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4B6075']],
    //         'alignment' => ['horizontal' => 'center'],
    //         'borders' => ['allBorders' => ['borderStyle' => 'thin']],
    //     ];

    //     $sheet->setCellValue("A1", "Dairy Management System");
    //     $sheet->mergeCells("A1:F1");
    //     $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    //     $sheet->setCellValue("A2", "The Mahila Banwala   Member passbook report from $startDate to $endDate");
    //     $sheet->mergeCells("A2:F2");
    //     $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(12);
    //     $sheet->getStyle("A2")->getAlignment()->setHorizontal('center');

    //     $sheet->setCellValue("A3", "$member->memberPersonalCode - $member->memberPersonalName");
    //     $sheet->mergeCells("A3:F3");
    //     $sheet->getStyle("A3")->getFont()->setBold(true)->setSize(11);
    //     $sheet->getStyle("A3")->getAlignment()->setHorizontal('center');
    //     $headerStyleArray = [
    //         'font' => ['bold' => true],
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID,
    //             'startColor' => ['rgb' => 'D9D9D9']
    //         ],
    //         'borders' => [
    //             'allBorders' => ['borderStyle' => Border::BORDER_THIN]
    //         ]
    //     ];
    //     $headers = ['Date', 'M Qty', 'M Fat', 'M SNF', 'M Rate', 'M Amt', 'E Qty', 'E Fat', 'E SNF', 'E Rate', 'E Amt', 'Total Qty', 'Total Amt'];
    //     $startRow = 6;
    //     $col = 'A';
    //     foreach ($headers as $header) {
    //         $sheet->setCellValue($col . $startRow, $header);
    //         $sheet->getStyle($col . $startRow)->applyFromArray($headerStyleArray);
    //         $sheet->getColumnDimension($col)->setAutoSize(false);
    //         $col++;
    //     }
    //     $rowIndex = $startRow + 1;
    //     $totalQtyMorning = $totalAmtMorning = $totalQtyEvening = $totalAmtEvening = 0;
    //     $fatPlusMorning = $snfPlusMorning = $fatPlusEvening = $snfPlusEvening = 0;

    //     foreach ($transactions as $date => $shifts) {
    //         $maxRows = max(count($shifts['morning'] ?? []), count($shifts['evening'] ?? []));
    //         for ($i = 0; $i < $maxRows; $i++) {
    //             $morning = $shifts['morning'][$i] ?? null;
    //             $evening = $shifts['evening'][$i] ?? null;

    //             $mQty = $mFat = $mSnf = $mRate = $mAmt = 0;
    //             $eQty = $eFat = $eSnf = $eRate = $eAmt = 0;

    //             if ($morning) {
    //                 $mQty = $morning->milkQuality;
    //                 $mFat = $morning->fat;
    //                 $mSnf = $morning->snf;
    //                 $mRate = $morning->rate;
    //                 $mAmt = $morning->amount;
    //                 $totalQtyMorning += $mQty;
    //                 $totalAmtMorning += $mAmt;
    //                 $fatPlusMorning += $mQty * $mFat;
    //                 $snfPlusMorning += $mQty * $mSnf;
    //             }

    //             if ($evening) {
    //                 $eQty = $evening->milkQuality;
    //                 $eFat = $evening->fat;
    //                 $eSnf = $evening->snf;
    //                 $eRate = $evening->rate;
    //                 $eAmt = $evening->amount;
    //                 $totalQtyEvening += $eQty;
    //                 $totalAmtEvening += $eAmt;
    //                 $fatPlusEvening += $eQty * $eFat;
    //                 $snfPlusEvening += $eQty * $eSnf;
    //             }

    //             $sheet->fromArray([
    //                 $i === 0 ? $date : '',
    //                 round($mQty, 1),
    //                 $mFat,
    //                 round($mSnf, 0),
    //                 round($mRate, 2),
    //                 round($mAmt, 2),
    //                 round($eQty, 1),
    //                 $eFat,
    //                 round($eSnf, 0),
    //                 round($eRate, 2),
    //                 round($eAmt, 2),
    //                 round($mQty + $eQty, 1),
    //                 round($mAmt + $eAmt, 2)
    //             ], null, "A{$rowIndex}");

    //             $rowIndex++;
    //         }
    //     }
    //     $avgFatM = $totalQtyMorning != 0 ? round($fatPlusMorning / $totalQtyMorning, 1) : "0.0";
    //     $avgSnfM = $totalQtyMorning != 0 ? round($snfPlusMorning / $totalQtyMorning, 0) : "0";
    //     $avgFatE = $totalQtyEvening != 0 ? round($fatPlusEvening / $totalQtyEvening, 1) : "0.0";
    //     $avgSnfE = $totalQtyEvening != 0 ? round($snfPlusEvening / $totalQtyEvening, 0) : "0";
    //     $sheet->setCellValue("A{$rowIndex}", "Total");
    //     $sheet->setCellValue("B{$rowIndex}", round($totalQtyMorning, 1));
    //     $sheet->setCellValue("C{$rowIndex}", $avgFatM);
    //     $sheet->setCellValue("D{$rowIndex}", $avgSnfM);
    //     $sheet->setCellValue("F{$rowIndex}", round($totalAmtMorning, 2));
    //     $sheet->setCellValue("G{$rowIndex}", round($totalQtyEvening, 1));
    //     $sheet->setCellValue("H{$rowIndex}", $avgFatE);
    //     $sheet->setCellValue("I{$rowIndex}", $avgSnfE);
    //     $sheet->setCellValue("K{$rowIndex}", round($totalAmtEvening, 2));
    //     $sheet->setCellValue("L{$rowIndex}", round($totalQtyMorning + $totalQtyEvening, 1));
    //     $sheet->setCellValue("M{$rowIndex}", round($totalAmtMorning + $totalAmtEvening, 2));
    //     $sheet->getStyle("A{$rowIndex}:M{$rowIndex}")->applyFromArray(['font' => ['bold' => true]]);


    //     // === MILK TYPE SUMMARY ===
    //     $milkTypeRow = $rowIndex + 2;
    //     $sheet->setCellValue("A{$milkTypeRow}", "Milk Type Summary");

    //     // Headers including Avg Rate
    //     $sheet->setCellValue("B{$milkTypeRow}", "Type");
    //     $sheet->setCellValue("C{$milkTypeRow}", "Total Quantity");
    //     $sheet->setCellValue("D{$milkTypeRow}", "Total Amount");
    //     $sheet->setCellValue("E{$milkTypeRow}", "Avg Fat");
    //     $sheet->setCellValue("F{$milkTypeRow}", "Avg SNF");
    //     $sheet->setCellValue("G{$milkTypeRow}", "Avg Rate");
    //     $sheet->getStyle("B{$milkTypeRow}:G{$milkTypeRow}")->applyFromArray($headerStyleArray);
    //     $buffaloQty = $buffaloAmount = $buffaloFat = $buffaloSnf = 0;
    //     $cowQty = $cowAmount = $cowFat = $cowSnf = 0;
    //     $allEntries = $transactions->flatMap(function ($shiftsByDate) {
    //         return collect($shiftsByDate)->flatMap(function ($shifts) {
    //             return collect($shifts);
    //         });
    //     });

    //     foreach ($allEntries as $entry) {
    //         if ($entry->milkType === 'buffalo') {
    //             $buffaloQty += $entry->milkQuality;
    //             $buffaloAmount += $entry->amount;
    //             $buffaloFat += $entry->milkQuality * $entry->fat;
    //             $buffaloSnf += $entry->milkQuality * $entry->snf;
    //         } elseif ($entry->milkType === 'cow') {
    //             $cowQty += $entry->milkQuality;
    //             $cowAmount += $entry->amount;
    //             $cowFat += $entry->milkQuality * $entry->fat;
    //             $cowSnf += $entry->milkQuality * $entry->snf;
    //         }
    //     }

    //     // Calculate Buffalo averages
    //     $avgBuffaloFat = $buffaloQty ? round($buffaloFat / $buffaloQty, 1) : "0.0";
    //     $avgBuffaloSnf = $buffaloQty ? round($buffaloSnf / $buffaloQty, 1) : "0.0";
    //     $avgBuffaloRate = $buffaloQty ? round($buffaloAmount / $buffaloQty, 2) : "0.00";

    //     // Calculate Cow averages
    //     $avgCowFat = $cowQty ? round($cowFat / $cowQty, 1) : "0.0";
    //     $avgCowSnf = $cowQty ? round($cowSnf / $cowQty, 1) : "0.0";
    //     $avgCowRate = $cowQty ? round($cowAmount / $cowQty, 2) : "0.00";

    //     // Buffalo Row
    //     $sheet->fromArray([
    //         "Buffalo",
    //         round($buffaloQty, 1),
    //         round($buffaloAmount, 2),
    //         $avgBuffaloFat,
    //         $avgBuffaloSnf,
    //         $avgBuffaloRate
    //     ], null, "B" . ($milkTypeRow + 1));

    //     // Cow Row
    //     $sheet->fromArray([
    //         "Cow",
    //         round($cowQty, 1),
    //         round($cowAmount, 2),
    //         $avgCowFat,
    //         $avgCowSnf,
    //         $avgCowRate
    //     ], null, "B" . ($milkTypeRow + 2));

    //     // Total Row
    //     $totalQty = $buffaloQty + $cowQty;
    //     $totalAmt = $buffaloAmount + $cowAmount;
    //     $totalFat = $buffaloFat + $cowFat;
    //     $totalSnf = $buffaloSnf + $cowSnf;
    //     $avgFat = $totalQty ? round($totalFat / $totalQty, 1) : "0.0";
    //     $avgSnf = $totalQty ? round($totalSnf / $totalQty, 1) : "0.0";
    //     $avgRate = $totalQty ? round($totalAmt / $totalQty, 2) : "0.00";

    //     $sheet->fromArray([
    //         "Total",
    //         round($totalQty, 1),
    //         round($totalAmt, 2),
    //         $avgFat,
    //         $avgSnf,
    //         $avgRate
    //     ], null, "B" . ($milkTypeRow + 3));

    //     $sheet->getStyle("B" . ($milkTypeRow + 3) . ":G" . ($milkTypeRow + 3))->applyFromArray([
    //         'font' => ['bold' => true],
    //         'fill' => [
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'startColor' => ['rgb' => 'E1E1E1']
    //         ]
    //     ]);


    //     // ========== SALES REPORT ==========
    //     $salesStartRow = $milkTypeRow + 4;
    //     $sheet->setCellValue("A{$salesStartRow}", "SALES REPORT");
    //     $from = date("Y-m-d", strtotime($request->saleStartDate));
    //     $to = date("Y-m-d", strtotime($request->saleEndDate));

    //     $query = DB::table('sales')
    //         ->where('dairyId', $dairy->dairyId)
    //         ->where('partyCode', $partyCode)
    //         ->where('status', 'true')
    //         ->whereIn('saleType', ['local_sale', 'product_sale']);

    //     if (!empty($startDate) && !empty($endDate)) {
    //         $query->whereBetween('saleDate', [
    //             date("Y-m-d", strtotime($startDate)),
    //             date("Y-m-d", strtotime($endDate))
    //         ]);
    //     }
    //     $sales = $query->get();
    //     $salesHeaders = ['Date', 'Sale Type', 'Product Name', 'Qty', 'Rate', 'Amount', 'Discount', 'Paid Amount', 'Final Amount'];
    //     $salesRow = $salesStartRow + 2;
    //     $col = 'A';
    //     foreach ($salesHeaders as $header) {
    //         $sheet->setCellValue($col . $salesRow, $header);
    //         $sheet->getStyle($col . $salesRow)->applyFromArray($headerStyleArray);
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //         $col++;
    //     }

    //     $row = $salesRow + 1;
    //     $salesTotal = 0;
    //     foreach ($sales as $data) {
    //         $product = ($data->productType == "cowMilk") ? "Cow Milk" : (($data->productType == "buffaloMilk") ? "Buffalo Milk" : "-");
    //         if ($product == "-") {
    //             $p = DB::table('products')->where('productCode', $data->productType)->first();
    //             $product = $p ? $p->productName : $data->productType;
    //         }
    //         $sheet->setCellValue("A{$row}", date("d-m-Y", strtotime($data->saleDate)));
    //         $sheet->setCellValue("B{$row}", $data->saleType);
    //         $sheet->setCellValue("C{$row}", $product);
    //         $sheet->setCellValue("D{$row}", $data->productQuantity);
    //         $sheet->setCellValue("E{$row}", $data->productPricePerUnit ?: '-');
    //         $amount = $data->productQuantity * $data->productPricePerUnit;
    //         $sheet->setCellValue("F{$row}", $amount);
    //         $sheet->setCellValue("G{$row}", $data->discount);
    //         $sheet->setCellValue("H{$row}", $data->paidAmount);
    //         $sheet->setCellValue("I{$row}", $data->finalAmount);
    //         $salesTotal += $data->finalAmount;
    //         $row++;
    //     }
    //     $sheet->setCellValue("A{$row}", "TOTAL");
    //     $sheet->mergeCells("A{$row}:H{$row}");
    //     $sheet->setCellValue("I{$row}", round($salesTotal, 2));
    //     $sheet->getStyle("A{$row}:I{$row}")->applyFromArray(['font' => ['bold' => true]]);
    //     // ======== DOWNLOAD FILE ========


    //     // ========== ADVANCE / CREDIT DETAILS ==========
    //     // openingBalance And Advance And credit  
    //     $credit = DB::table('credit')
    //         ->where('dairyId', session()->get('loginUserInfo')->dairyId)
    //         ->where('partyCode', $partyCode)
    //         ->whereBetween('date', [
    //             date("Y-m-d", strtotime($startDate)),
    //             date("Y-m-d", strtotime($endDate))
    //         ])
    //         ->orderBy("created_at", "desc")
    //         ->get();

    //     $advance = DB::table('advance')
    //         ->where('dairyId', session()->get('loginUserInfo')->dairyId)
    //         ->where('partyCode', $partyCode)
    //         ->whereBetween('date', [
    //             date("Y-m-d", strtotime($startDate)),
    //             date("Y-m-d", strtotime($endDate))
    //         ])
    //         ->orderBy("created_at", "desc")
    //         ->get();
    //     $dairyInfo = DB::table("dairy_info")->where("id", session()->get('loginUserInfo')->dairyId)->get()->first();
    //     $mem = DB::table("member_personal_info")->where("dairyId", session()->get('loginUserInfo')->dairyId)->where("memberPersonalCode", $partyCode)->get()->first();
    //     if ($mem == (false || null)) {
    //         return ["error" => true, "msg" => "No member found"];
    //     }
    //     $ubal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->get()->first();
    //     //openingBalance  End

    //     $creditAdvanceStartRow = $row + 3;
    //     $sheet->setCellValue("A{$creditAdvanceStartRow}", "Advance / Credit Details");
    //     $sheet->mergeCells("A{$creditAdvanceStartRow}:D{$creditAdvanceStartRow}");
    //     $sheet->getStyle("A{$creditAdvanceStartRow}")->applyFromArray([
    //         'font' => ['bold' => true, 'size' => 12]
    //     ]);
    //     $creditAdvanceHeaderRow = $creditAdvanceStartRow + 1;
    //     $headers = ['Date', 'Type', 'Amount', 'Remarks'];
    //     $col = 'A';
    //     foreach ($headers as $header) {
    //         $sheet->setCellValue("{$col}{$creditAdvanceHeaderRow}", $header);
    //         $sheet->getStyle("{$col}{$creditAdvanceHeaderRow}")->applyFromArray($headerStyleArray);
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //         $col++;
    //     }
    //     $details = [];
    //     foreach ($advance as $a) {
    //         $details[] = [
    //             'date' => date("d-m-Y", strtotime($a->created_at)),
    //             'type' => 'Advance',
    //             'amount' => $a->amount,
    //             'remarks' => $a->remarks ?? '',
    //         ];
    //     }
    //     foreach ($credit as $c) {
    //         $details[] = [
    //             'date' => date("d-m-Y", strtotime($c->created_at)),
    //             'type' => 'Credit',
    //             'amount' => $c->amount,
    //             'remarks' => $c->remarks ?? '',
    //         ];
    //     }
    //     usort($details, function ($a, $b) {
    //         return strtotime($a['date']) - strtotime($b['date']);
    //     });
    //     $currentRow = $creditAdvanceHeaderRow + 1;
    //     $totalAdvance = 0;
    //     $totalCredit = 0;
    //     foreach ($details as $entry) {
    //         $sheet->setCellValue("A{$currentRow}", $entry['date']);
    //         $sheet->setCellValue("B{$currentRow}", $entry['type']);
    //         $sheet->setCellValue("C{$currentRow}", $entry['amount']);
    //         $sheet->setCellValue("D{$currentRow}", $entry['remarks']);

    //         if ($entry['type'] == 'Advance') {
    //             $totalAdvance += $entry['amount'];
    //         } else {
    //             $totalCredit += $entry['amount'];
    //         }
    //         $currentRow++;
    //     }
    //     $balanceRow = $currentRow + 1;
    //     $sheet->setCellValue("A{$balanceRow}", "Balance");
    //     $sheet->setCellValue("C{$balanceRow}", $lastCurrentBalance);

    //     $currentBalanceRow = $balanceRow + 1;
    //     $sheet->setCellValue("A{$currentBalanceRow}", "Current Balance");
    //     $sheet->setCellValue("C{$currentBalanceRow}", $ubal->openingBalance);
    //     $sheet->getStyle("A{$balanceRow}:C{$balanceRow}")->applyFromArray(['font' => ['bold' => true]]);
    //     $sheet->getStyle("A{$currentBalanceRow}:C{$currentBalanceRow}")->applyFromArray(['font' => ['bold' => true]]);
    //     $fileName = 'Combined_Report_' . now()->format('Ymd_His') . '.xlsx';
    //     $writer = new Xlsx($spreadsheet);

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header("Content-Disposition: attachment; filename=\"$fileName\"");
    //     header('Cache-Control: max-age=0');

    //     $writer->save('php://output');
    //     exit;
    // }



    // public function exportMemberReport(Request $request)
    // {
    //     $partyCode = $request->partyCode;
    //     $status = $request->status;
    //     $startDate = $request->memberPassbookStartDate;
    //     $endDate = $request->memberPassbookEndDate;
    //     $dairy = Session::get("loginUserInfo");
    //     $startDate = date("Y-m-d", strtotime($startDate));
    //     $endDate = date("Y-m-d", strtotime($endDate));
    //     $milkCollection = DB::table("daily_transactions as dt")
    //         ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "dt.id")
    //                 ->where("bs.transactionType", "daily_transactions");
    //         })
    //         ->where([
    //             "dt.dairyId" => $dairy->dairyId,
    //             "dt.memberCode" => $partyCode,
    //             "dt.status" => "true"
    //         ])
    //         ->whereBetween("dt.date", [$startDate, $endDate])
    //         ->get();
    //     $localsaleFinal = DB::table("sales as s")
    //         ->select("s.*", "bs.currentBalance", DB::raw("'s' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "s.id")
    //                 ->where("bs.transactionType", "sales");
    //         })
    //         ->where([
    //             "s.dairyId" => $dairy->dairyId,
    //             "s.partyCode" => $partyCode,
    //             "s.status" => "true",
    //             "s.saleType" => "local_sale"
    //         ])
    //         ->whereBetween("s.saleDate", [$startDate, $endDate])
    //         ->get();

    //     $localsalepaid = $localsaleFinal->sum('paidAmount');
    //     $productsale = DB::table("sales as s")
    //         ->select("s.*", "bs.currentBalance", "p.productName", DB::raw("'s' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "s.id")
    //                 ->where("bs.transactionType", "sales");
    //         })
    //         ->leftJoin("products as p", "p.productCode", "=", "s.productType")
    //         ->where([
    //             "s.dairyId" => $dairy->dairyId,
    //             "s.partyCode" => $partyCode,
    //             "s.status" => "true",
    //             "s.saleType" => "product_sale"
    //         ])
    //         ->whereBetween("s.saleDate", [$startDate, $endDate])
    //         ->get();
    //     $advance = DB::table("advance as a")
    //         ->select("a.*", "bs.currentBalance", DB::raw("'a' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "a.id")
    //                 ->where("bs.transactionType", "advance");
    //         })
    //         ->where([
    //             "a.dairyId" => $dairy->dairyId,
    //             "a.partyCode" => $partyCode
    //         ])
    //         ->whereBetween("a.date", [$startDate, $endDate])
    //         ->get();
    //     $credit = DB::table("credit as c")
    //         ->select("c.*", "bs.currentBalance", DB::raw("'c' as source"))
    //         ->leftJoin("balance_sheet as bs", function ($join) {
    //             $join->on("bs.transactionId", "=", "c.id")
    //                 ->where("bs.transactionType", "credit");
    //         })
    //         ->where([
    //             "c.dairyId" => $dairy->dairyId,
    //             "c.partyCode" => $partyCode
    //         ])
    //         ->whereBetween("c.date", [$startDate, $endDate])
    //         ->get();
    //     $allTransactions = collect()
    //         ->merge($milkCollection)
    //         ->merge($localsaleFinal)
    //         ->merge($productsale)
    //         ->merge($advance)
    //         ->merge($credit);

    //     $latestTransaction = $allTransactions->sortByDesc(function ($item) {
    //         return strtotime($item->date ?? $item->saleDate);
    //     })->first();

    //     $lastCurrentBalance = str_replace(' cr', '', $latestTransaction->currentBalance ?? '0.00');
    //     $member = DB::table('member_personal_info')
    //         ->where('dairyId', $dairy->dairyId)
    //         ->where('memberPersonalCode', $partyCode)
    //         ->where('status', $status)
    //         ->first();

    //     if (!$member) {
    //         return response()->json(['error' => 'Member not found'], 404);
    //     }
    //     $query = DB::table('daily_transactions')
    //         ->where('dairyId', $dairy->dairyId)
    //         ->where('status', 'true')
    //         ->where('memberCode', $partyCode)
    //         ->orderBy('date', 'asc');
    //     if (!empty($startDate) && !empty($endDate)) {
    //         $query->whereBetween('date', [$startDate, $endDate]);
    //     }
    //     $transactions = $query->get()->groupBy(['date', 'shift']);

    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setTitle('Member Report');
    //     $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

    //     $titleStyle = [
    //         'font' => ['bold' => true, 'size' => 14],
    //         'alignment' => ['horizontal' => 'center'],
    //     ];

    //     $subtitleStyle = [
    //         'font' => ['bold' => true, 'size' => 12],
    //         'alignment' => ['horizontal' => 'center'],
    //     ];

    //     $headerStyle = [
    //         'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    //         'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
    //         'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
    //         'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    //     ];

    //     $totalStyle = [
    //         'font' => ['bold' => true],
    //         'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
    //     ];

    //     $numberStyle = [
    //         'alignment' => ['horizontal' => 'right'],
    //         'numberFormat' => ['formatCode' => '#,##0.00']
    //     ];
    //     foreach (range('A', 'M') as $col) {
    //         $sheet->getColumnDimension($col)->setWidth(10);
    //     }
    //     $sheet->getColumnDimension('D')->setWidth(6);
    //     $sheet->setCellValue("A1", "Dairy Management System");
    //     $sheet->mergeCells("A1:M1");
    //     $sheet->getStyle("A1")->applyFromArray($titleStyle);

    //     $sheet->setCellValue("A2", "Member Passbook Report from " . date("d-m-Y", strtotime($startDate)) . " to " . date("d-m-Y", strtotime($endDate)));
    //     $sheet->mergeCells("A2:M2");
    //     $sheet->getStyle("A2")->applyFromArray($subtitleStyle);

    //     $sheet->setCellValue("A3", "Member: $member->memberPersonalCode - $member->memberPersonalName");
    //     $sheet->mergeCells("A3:M3");
    //     $sheet->getStyle("A3")->getFont()->setBold(true);

    //     $sheet->setCellValue("A4", "Generated: " . date('d-m-Y H:i'));
    //     $sheet->mergeCells("A4:M4");
    //     $sheet->getStyle("A4")->getFont()->setItalic(true);

    //     $headers = ['Date', 'M Qty', 'M Fat', 'M SNF', 'M Rate', 'M Amt', 'E Qty', 'E Fat', 'E SNF', 'E Rate', 'E Amt', 'Total Qty', 'Total Amt'];
    //     $startRow = 6;

    //     foreach ($headers as $colIndex => $header) {
    //         // $col = chr(65 + $colIndex);
    //         $sheet->setCellValue($col . $startRow, $header);
    //         $sheet->getStyle($col . $startRow)->applyFromArray($headerStyle);
    //     }

    //     $rowIndex = $startRow + 1;
    //     $totalQtyMorning = $totalAmtMorning = $totalQtyEvening = $totalAmtEvening = 0;
    //     $fatPlusMorning = $snfPlusMorning = $fatPlusEvening = $snfPlusEvening = 0;

    //     foreach ($transactions as $date => $shifts) {
    //         $maxRows = max(count($shifts['morning'] ?? []), count($shifts['evening'] ?? []));

    //         for ($i = 0; $i < $maxRows; $i++) {
    //             $morning = $shifts['morning'][$i] ?? null;
    //             $evening = $shifts['evening'][$i] ?? null;
    //             $mQty = $mFat = $mSnf = $mRate = $mAmt = 0;
    //             $eQty = $eFat = $eSnf = $eRate = $eAmt = 0;

    //             if ($morning) {
    //                 $mQty = $morning->milkQuality;
    //                 $mFat = $morning->fat;
    //                 $mSnf = $morning->snf;
    //                 $mRate = $morning->rate;
    //                 $mAmt = $morning->amount;

    //                 $totalQtyMorning += $mQty;
    //                 $totalAmtMorning += $mAmt;
    //                 $fatPlusMorning += $mQty * $mFat;
    //                 $snfPlusMorning += $mQty * $mSnf;
    //             }

    //             if ($evening) {
    //                 $eQty = $evening->milkQuality;
    //                 $eFat = $evening->fat;
    //                 $eSnf = $evening->snf;
    //                 $eRate = $evening->rate;
    //                 $eAmt = $evening->amount;

    //                 $totalQtyEvening += $eQty;
    //                 $totalAmtEvening += $eAmt;
    //                 $fatPlusEvening += $eQty * $eFat;
    //                 $snfPlusEvening += $eQty * $eSnf;
    //             }

    //             $sheet->fromArray([
    //                 $i === 0 ? date("d-m-Y", strtotime($date)) : '',
    //                 $mQty ?: '',
    //                 $mQty ? $mFat : '',
    //                 $mQty ? round($mSnf, 1) : '',
    //                 $mQty ? round($mRate, 2) : '',
    //                 $mQty ? round($mAmt, 2) : '',
    //                 $eQty ?: '',
    //                 $eQty ? $eFat : '',
    //                 $eQty ? round($eSnf, 1) : '',
    //                 $eQty ? round($eRate, 2) : '',
    //                 $eQty ? round($eAmt, 2) : '',
    //                 round($mQty + $eQty, 1),
    //                 round($mAmt + $eAmt, 2)
    //             ], null, "A{$rowIndex}");
    //             $rowIndex++;
    //         }
    //     }

    //     // === Totals Row ===
    //     $avgFatM = $totalQtyMorning ? round($fatPlusMorning / $totalQtyMorning, 1) : "0.0";
    //     $avgSnfM = $totalQtyMorning ? round($snfPlusMorning / $totalQtyMorning, 1) : "0.0";
    //     $avgFatE = $totalQtyEvening ? round($fatPlusEvening / $totalQtyEvening, 1) : "0.0";
    //     $avgSnfE = $totalQtyEvening ? round($snfPlusEvening / $totalQtyEvening, 1) : "0.0";

    //     $sheet->setCellValue("A{$rowIndex}", "Total");
    //     $sheet->setCellValue("B{$rowIndex}", round($totalQtyMorning, 1));
    //     $sheet->setCellValue("C{$rowIndex}", $avgFatM);
    //     $sheet->setCellValue("D{$rowIndex}", $avgSnfM);
    //     $sheet->setCellValue("F{$rowIndex}", round($totalAmtMorning, 2));
    //     $sheet->setCellValue("G{$rowIndex}", round($totalQtyEvening, 1));
    //     $sheet->setCellValue("H{$rowIndex}", $avgFatE);
    //     $sheet->setCellValue("I{$rowIndex}", $avgSnfE);
    //     $sheet->setCellValue("K{$rowIndex}", round($totalAmtEvening, 2));
    //     $sheet->setCellValue("L{$rowIndex}", round($totalQtyMorning + $totalQtyEvening, 1));
    //     $sheet->setCellValue("M{$rowIndex}", round($totalAmtMorning + $totalAmtEvening, 2));
    //     $sheet->getStyle("A{$rowIndex}:M{$rowIndex}")->applyFromArray($totalStyle);

    //     // === Apply number formatting
    //     $numericCols = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
    //     foreach ($numericCols as $col) {
    //         $sheet->getStyle("{$col}7:{$col}{$rowIndex}")->applyFromArray($numberStyle);
    //     }

    //     // === Milk Type Summary Header
    //     $milkTypeRow = $rowIndex + 2;
    //     $sheet->setCellValue("A{$milkTypeRow}", "Milk Type Summary");
    //     $sheet->mergeCells("A{$milkTypeRow}:M{$milkTypeRow}");
    //     $sheet->getStyle("A{$milkTypeRow}")->getFont()->setBold(true)->setSize(12);
    //     $sheet->getStyle("A{$milkTypeRow}")->getAlignment()->setHorizontal('center');

    //     // === Milk Type Headers (merged cells)
    //     $headerRow = $milkTypeRow + 1;
    //     $milkHeaders = [
    //         'A' => ['C', "Type"],
    //         'D' => ['E', "Total Quantity"],
    //         'F' => ['G', "Total Amount"],
    //         'H' => ['I', "Avg Fat"],
    //         'J' => ['K', "Avg SNF"],
    //         'L' => ['M', "Avg Rate"]
    //     ];

    //     foreach ($milkHeaders as $startCol => [$endCol, $label]) {
    //         $sheet->mergeCells("{$startCol}{$headerRow}:{$endCol}{$headerRow}");
    //         $sheet->setCellValue("{$startCol}{$headerRow}", $label);
    //         $sheet->getStyle("{$startCol}{$headerRow}")->applyFromArray($headerStyle);
    //         $sheet->getStyle("{$startCol}{$headerRow}")->getAlignment()->setHorizontal('center');
    //     }

    //     // === Flatten and Calculate Milk Types
    //     $buffaloQty = $buffaloAmount = $buffaloFat = $buffaloSnf = 0;
    //     $cowQty = $cowAmount = $cowFat = $cowSnf = 0;

    //     $allEntries = $transactions->flatMap(
    //         fn($shiftsByDate) =>
    //         collect($shiftsByDate)->flatMap(fn($shifts) => collect($shifts))
    //     );

    //     foreach ($allEntries as $entry) {
    //         if ($entry->milkType === 'buffalo') {
    //             $buffaloQty += $entry->milkQuality;
    //             $buffaloAmount += $entry->amount;
    //             $buffaloFat += $entry->milkQuality * $entry->fat;
    //             $buffaloSnf += $entry->milkQuality * $entry->snf;
    //         } elseif ($entry->milkType === 'cow') {
    //             $cowQty += $entry->milkQuality;
    //             $cowAmount += $entry->amount;
    //             $cowFat += $entry->milkQuality * $entry->fat;
    //             $cowSnf += $entry->milkQuality * $entry->snf;
    //         }
    //     }

    //     function fillMilkRow($sheet, $row, $type, $qty, $amt, $fat, $snf)
    //     {
    //         $avgFat = $qty ? round($fat / $qty, 1) : "0.0";
    //         $avgSnf = $qty ? round($snf / $qty, 1) : "0.0";
    //         $avgRate = $qty ? round($amt / $qty, 2) : "0.00";

    //         $sheet->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", ucfirst($type));
    //         $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", round($qty, 1));
    //         $sheet->mergeCells("F{$row}:G{$row}")->setCellValue("F{$row}", round($amt, 2));
    //         $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", $avgFat);
    //         $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $avgSnf);
    //         $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $avgRate);
    //         $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
    //     }

    //     fillMilkRow($sheet, $milkTypeRow + 2, 'Buffalo', $buffaloQty, $buffaloAmount, $buffaloFat, $buffaloSnf);
    //     fillMilkRow($sheet, $milkTypeRow + 3, 'Cow', $cowQty, $cowAmount, $cowFat, $cowSnf);
    //     fillMilkRow(
    //         $sheet,
    //         $milkTypeRow + 4,
    //         'Total',
    //         $buffaloQty + $cowQty,
    //         $buffaloAmount + $cowAmount,
    //         $buffaloFat + $cowFat,
    //         $buffaloSnf + $cowSnf
    //     );
    //     $sheet->getStyle("A" . ($milkTypeRow + 4) . ":M" . ($milkTypeRow + 4))->getFont()->setBold(true);



    //     // === SALES REPORT ===
    //     $salesStartRow = $milkTypeRow + 6;
    //     $sheet->setCellValue("A{$salesStartRow}", "SALES REPORT");
    //     $sheet->mergeCells("A{$salesStartRow}:M{$salesStartRow}");
    //     $sheet->getStyle("A{$salesStartRow}")->getFont()->setBold(true)->setSize(12);
    //     $sheet->getStyle("A{$salesStartRow}")->getAlignment()->setHorizontal('center');

    //     // Fetch sales data
    //     $query = DB::table('sales')
    //         ->where('dairyId', $dairy->dairyId)
    //         ->where('partyCode', $partyCode)
    //         ->where('status', 'true')
    //         ->whereIn('saleType', ['local_sale', 'product_sale']);

    //     if (!empty($startDate) && !empty($endDate)) {
    //         $query->whereBetween('saleDate', [$startDate, $endDate]);
    //     }
    //     $sales = $query->get();

    //     // === Sales Headers ===
    //     $headerRow = $salesStartRow + 1;
    //     $salesHeaders = [
    //         'A' => ['B', 'Date'],
    //         'C' => ['D', 'Sale Type'],
    //         'E' => ['F', 'Product Name'],
    //         'G' => ['G', 'Qty'],
    //         'H' => ['H', 'Rate'],
    //         'I' => ['I', 'Amount'],
    //         'J' => ['J', 'Discount'],
    //         'K' => ['K', 'Paid Amount'],
    //         'L' => ['M', 'Final Amount']
    //     ];

    //     foreach ($salesHeaders as $startCol => [$endCol, $label]) {
    //         $sheet->mergeCells("{$startCol}{$headerRow}:{$endCol}{$headerRow}");
    //         $sheet->setCellValue("{$startCol}{$headerRow}", $label);
    //         $sheet->getStyle("{$startCol}{$headerRow}")->applyFromArray($headerStyle);
    //         $sheet->getStyle("{$startCol}{$headerRow}")->getAlignment()->setHorizontal('center');
    //     }

    //     // === Sales Data Rows ===
    //     $row = $headerRow + 1;
    //     $salesTotal = 0;

    //     foreach ($sales as $data) {
    //         // Get product name
    //         if ($data->productType == 'cowMilk') {
    //             $product = 'Cow Milk';
    //         } elseif ($data->productType == 'buffaloMilk') {
    //             $product = 'Buffalo Milk';
    //         } else {
    //             $product = DB::table('products')->where('productCode', $data->productType)->value('productName') ?: $data->productType;
    //         }

    //         $amount = $data->productQuantity * $data->productPricePerUnit;

    //         $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", date("d-m-Y", strtotime($data->saleDate)));
    //         $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", ucfirst(str_replace('_', ' ', $data->saleType)));
    //         $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", $product);
    //         $sheet->setCellValue("G{$row}", round($data->productQuantity, 2));
    //         $sheet->setCellValue("H{$row}", round($data->productPricePerUnit, 2));
    //         $sheet->setCellValue("I{$row}", round($amount, 2));
    //         $sheet->setCellValue("J{$row}", round($data->discount, 2));
    //         $sheet->setCellValue("K{$row}", round($data->paidAmount, 2));
    //         $sheet->setCellValue("M{$row}", round($data->finalAmount, 2));

    //         // Align all columns in row
    //         foreach (range('A', 'M') as $col) {
    //             $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal('center');
    //         }

    //         $salesTotal += $data->finalAmount;
    //         $row++;
    //     }

    //     // === Total Row ===
    //     $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", "TOTAL");
    //     $sheet->setCellValue("M{$row}", round($salesTotal, 2));
    //     $sheet->mergeCells("M{$row}:M{$row}");
    //     $sheet->getStyle("A{$row}:M{$row}")->getFont()->setBold(true);
    //     $sheet->getStyle("A{$row}:M{$row}")->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($totalStyle);



    //     // === ADVANCE/CREDIT DETAILS ===
    //     $creditAdvanceStartRow = $row + 3;
    //     $sheet->setCellValue("A{$creditAdvanceStartRow}", "Advance / Credit Details");
    //     $sheet->mergeCells("A{$creditAdvanceStartRow}:D{$creditAdvanceStartRow}");
    //     $sheet->getStyle("A{$creditAdvanceStartRow}")->getFont()->setBold(true)->setSize(12);
    //     $headers = ['Date', 'Type', 'Amount', 'Remarks'];
    //     $creditAdvanceHeaderRow = $creditAdvanceStartRow + 1;

    //     foreach ($headers as $colIndex => $header) {
    //         $col = chr(65 + $colIndex); // A, B, C, D
    //         $sheet->setCellValue($col . $creditAdvanceHeaderRow, $header);
    //         $sheet->getStyle($col . $creditAdvanceHeaderRow)->applyFromArray($headerStyle);
    //     }

    //     $sheet->getColumnDimension('A')->setWidth(12);
    //     $sheet->getColumnDimension('B')->setWidth(12);
    //     $sheet->getColumnDimension('C')->setWidth(12); 
    //     $sheet->getColumnDimension('D')->setWidth(30); 
    //     $credit = DB::table('credit')
    //         ->where('dairyId', session()->get('loginUserInfo')->dairyId)
    //         ->where('partyCode', $partyCode)
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->orderBy("created_at", "desc")
    //         ->get();

    //     $advance = DB::table('advance')
    //         ->where('dairyId', session()->get('loginUserInfo')->dairyId)
    //         ->where('partyCode', $partyCode)
    //         ->whereBetween('date', [$startDate, $endDate])
    //         ->orderBy("created_at", "desc")
    //         ->get();
    //     $dairyInfo = DB::table("dairy_info")->where("id", session()->get('loginUserInfo')->dairyId)->first();
    //     $mem = DB::table("member_personal_info")->where("dairyId", session()->get('loginUserInfo')->dairyId)
    //         ->where("memberPersonalCode", $partyCode)->first();
    //     if (!$mem) {
    //         return ["error" => true, "msg" => "No member found"];
    //     }
    //     $ubal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->first();
    //     $details = [];
    //     foreach ($advance as $a) {
    //         $details[] = [
    //             'date' => date("d-m-Y", strtotime($a->created_at)),
    //             'type' => 'Advance',
    //             'amount' => $a->amount,
    //             'remarks' => $a->remarks ?? '',
    //         ];
    //     }
    //     foreach ($credit as $c) {
    //         $details[] = [
    //             'date' => date("d-m-Y", strtotime($c->created_at)),
    //             'type' => 'Credit',
    //             'amount' => $c->amount,
    //             'remarks' => $c->remarks ?? '',
    //         ];
    //     }
    //     usort($details, function ($a, $b) {
    //         return strtotime($a['date']) - strtotime($b['date']);
    //     });
    //     $currentRow = $creditAdvanceHeaderRow + 1;
    //     $totalAdvance = 0;
    //     $totalCredit = 0;
    //     foreach ($details as $entry) {
    //         $sheet->setCellValue("A{$currentRow}", $entry['date']);
    //         $sheet->setCellValue("B{$currentRow}", $entry['type']);
    //         $sheet->setCellValue("C{$currentRow}", $entry['amount']);
    //         $sheet->setCellValue("D{$currentRow}", $entry['remarks']);
    //         if ($entry['type'] == 'Advance') {
    //             $totalAdvance += $entry['amount'];
    //         } else {
    //             $totalCredit += $entry['amount'];
    //         }
    //         $currentRow++;
    //     }
    //     $balanceRow = $currentRow + 1;
    //     $typeLabel = $lastCurrentBalance >= 0 ?  'Credit' : 'Debit';
    //     $sheet->setCellValue("A{$balanceRow}", "Balance");
    //     $sheet->setCellValue("C{$balanceRow}", $lastCurrentBalance);
    //     $sheet->setCellValue("D{$balanceRow}", $typeLabel);
    //     $currentBalanceRow = $balanceRow + 1;
    //       $typeLabel2 = $ubal->openingBalance >= 0 ? 'Credit' : 'Debit';
    //     $sheet->setCellValue("A{$currentBalanceRow}", "Current Balance");
    //     $sheet->setCellValue("C{$currentBalanceRow}", $ubal ? $ubal->openingBalance : '0.00');
    //      $sheet->setCellValue("D{$currentBalanceRow}", $typeLabel);
    //     $sheet->getStyle("A{$balanceRow}:C{$balanceRow}")->applyFromArray($totalStyle);
    //     $sheet->getStyle("A{$currentBalanceRow}:C{$currentBalanceRow}")->applyFromArray($totalStyle);
    //     $lastRow = $currentBalanceRow;


    //     $sheet->getStyle("A6:M{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    //     // Set print settings
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    //     $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    //     $sheet->getPageSetup()->setFitToWidth(1);
    //     $sheet->getPageSetup()->setFitToHeight(0);
    //     $sheet->getPageSetup()->setHorizontalCentered(true);
    //     $sheet->getPageSetup()->setPrintArea("A1:M{$lastRow}");

    //     // Freeze header row
    //     $sheet->freezePane('A7');

    //     // Generate and download file
    //     $fileName = 'Member_Report_' . $member->memberPersonalCode . '_' . date('Ymd_His') . '.xlsx';
    //     $writer = new Xlsx($spreadsheet);

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header("Content-Disposition: attachment; filename=\"$fileName\"");
    //     header('Cache-Control: max-age=0');

    //     $writer->save('php://output');
    //     exit;
    // }


    public function exportMemberReport(Request $request)
    {



        $partyCode = $request->partyCode;
        $status = $request->status;
        $startDate = $request->memberPassbookStartDate;
        $endDate = $request->memberPassbookEndDate;
        $dairy = Session::get("loginUserInfo");
        $startDate = date("Y-m-d", strtotime($startDate));
        $startDate1 = Carbon::parse($startDate)->subDay(10)->toDateString();
        $endDate1 = Carbon::parse($startDate)->subDay()->toDateString();
        $endDate = date("Y-m-d", strtotime($endDate));

        // $openingBalancep = $this->fetchPreviousTransactions($dairy, $partyCode, $startDate);
        $milkCollection = DB::table("daily_transactions as dt")
            ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "dt.id")
                    ->where("bs.transactionType", "daily_transactions");
            })
            ->where([
                "dt.dairyId" => $dairy->dairyId,
                "dt.memberCode" => $partyCode,
                "dt.status" => "true"
            ])
            ->whereBetween("dt.date", [$startDate, $endDate])
            ->get();

        $localsaleFinal = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "local_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate, $endDate])
            ->get();

        $localsalepaid = $localsaleFinal->sum('paidAmount');

        $productsale = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", "p.productName", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->leftJoin("products as p", "p.productCode", "=", "s.productType")
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "product_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate, $endDate])
            ->get();
        $advance = DB::table("advance as a")
            ->select("a.*", "bs.currentBalance", DB::raw("'a' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "a.id")
                    ->where("bs.transactionType", "advance");
            })
            ->where([
                "a.dairyId" => $dairy->dairyId,
                "a.partyCode" => $partyCode
            ])
            ->whereBetween("a.date", [$startDate, $endDate])
            ->get();
        $credit = DB::table("credit as c")
            ->select("c.*", "bs.currentBalance", DB::raw("'c' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "c.id")
                    ->where("bs.transactionType", "credit");
            })
            ->where([
                "c.dairyId" => $dairy->dairyId,
                "c.partyCode" => $partyCode
            ])
            ->whereBetween("c.date", [$startDate, $endDate])
            ->get();


        $milkCollection1 = DB::table("daily_transactions as dt")
            ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "dt.id")
                    ->where("bs.transactionType", "daily_transactions");
            })
            ->where([
                "dt.dairyId" => $dairy->dairyId,
                "dt.memberCode" => $partyCode,
                "dt.status" => "true"
            ])
            ->whereBetween("dt.date", [$startDate1, $endDate1])
            ->get();

        $localsaleFinal1 = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "local_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate1, $endDate1])
            ->get();

        $localsalepaid1 = $localsaleFinal1->sum('paidAmount');

        $productsale1 = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", "p.productName", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->leftJoin("products as p", "p.productCode", "=", "s.productType")
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "product_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate1, $endDate1])
            ->get();
        $advance1 = DB::table("advance as a")
            ->select("a.*", "bs.currentBalance", DB::raw("'a' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "a.id")
                    ->where("bs.transactionType", "advance");
            })
            ->where([
                "a.dairyId" => $dairy->dairyId,
                "a.partyCode" => $partyCode
            ])
            ->whereBetween("a.date", [$startDate1, $endDate1])
            ->get();
        $credit1 = DB::table("credit as c")
            ->select("c.*", "bs.currentBalance", DB::raw("'c' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "c.id")
                    ->where("bs.transactionType", "credit");
            })
            ->where([
                "c.dairyId" => $dairy->dairyId,
                "c.partyCode" => $partyCode
            ])
            ->whereBetween("c.date", [$startDate1, $endDate1])
            ->get();

        $previousDay = Carbon::parse($startDate)->subDay()->toDateString();

        /* 

// Raw subquery to get the latest balance_sheet record for the given day
$previousbal = DB::table('balance_sheet as bs')
    ->join('member_personal_info as c', 'bs.ledgerId', '=', 'c.ledgerId')
  ->whereDate('bs.created_at', '<=', $previousDay)  
  ->where('c.dairyId', $dairy->dairyId)
    ->where('c.memberPersonalCode', $partyCode)
    ->orderByDesc('bs.created_at')
    ->select('bs.*')
    ->first();
//dd($previousbal);*/

        $allTransactions = collect()
            ->merge($milkCollection)
            ->merge($localsaleFinal)
            ->merge($productsale)
            ->merge($advance)
            ->merge($credit);

        $latestTransaction = $allTransactions->sortByDesc('created_at')->first();

        //$lastCurrentBalance = str_replace(' cr', '', $latestTransaction->currentBalance ?? '0.00');
        $allTransactions1 = collect()
            ->merge($milkCollection1)
            ->merge($localsaleFinal1)
            ->merge($productsale1)
            ->merge($advance1)
            ->merge($credit1);

        $latestTransaction1 = $allTransactions1->sortByDesc('created_at')->first();

        //$prevBalance = str_replace(' cr', '', $latestTransaction1->currentBalance ?? '0.00');
        //test
        // test

        $member = DB::table('member_personal_info')
            ->where('dairyId', $dairy->dairyId)
            ->where('memberPersonalCode', $partyCode)
            ->where('status', $status)
            ->first();
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $query = DB::table('daily_transactions')
            ->select('daily_transactions.*')
            ->addSelect(DB::raw('DATE(date) as only_date'))
            ->where('dairyId', $dairy->dairyId)
            ->where('status', 'true')
            ->where('memberCode', $partyCode)
            ->orderBy('date', 'asc');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        }

        $transactions = $query->get()
            ->groupBy(function ($item) {
                return $item->only_date;
            })
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('shift');
            });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Member Report');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ];

        $subtitleStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => 'center'],
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
        ];

        $numberStyle = [
            'alignment' => ['horizontal' => 'right'],
            'numberFormat' => ['formatCode' => '#,##0.00']
        ];

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(8);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(8);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(12);
        $sheet->getColumnDimension('M')->setWidth(14);

        $sheet->setCellValue("A1", "Dairy Management System");
        $sheet->mergeCells("A1:M1");
        $sheet->getStyle("A1")->applyFromArray($titleStyle);
        $sheet->getStyle('A1:M1')->getAlignment()->setWrapText(false);
        $sheet->getStyle('A1:M1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("A2", "Member Passbook Report from " . date("d-m-Y", strtotime($startDate)) . " to " . date("d-m-Y", strtotime($endDate)));
        $sheet->mergeCells("A2:M2");
        $sheet->getStyle("A2")->applyFromArray($subtitleStyle);
        $sheet->getStyle('A2:M2')->getAlignment()->setWrapText(false);
        $sheet->getStyle('A2:M2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("A3", "Member: $member->memberPersonalCode - $member->memberPersonalName");
        $sheet->mergeCells("A3:M3");
        $sheet->getStyle("A3")->getFont()->setBold(true);
        $sheet->getStyle('A3:M3')->getAlignment()->setWrapText(false);
        $sheet->getStyle('A3:M3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("A4", "Generated: " . date('d-m-Y H:i'));
        $sheet->mergeCells("A4:M4");
        $sheet->getStyle("A4")->getFont()->setItalic(true);
        $sheet->getStyle('A4:M4')->getAlignment()->setWrapText(false);
        $sheet->getStyle('A4:M4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = ['Date', 'M Qty', 'M Fat', 'M SNF', 'M Rate', 'M Amt', 'E Qty', 'E Fat', 'E SNF', 'E Rate', 'E Amt', 'Total Qty', 'Total Amt'];
        $startRow = 6;

        foreach ($headers as $colIndex => $header) {

            $col = chr(65 + $colIndex);
            $sheet->setCellValue($col . $startRow, $header);
            $sheet->getStyle($col . $startRow)->applyFromArray($headerStyle);
        }

        $rowIndex = $startRow + 1;
        $totalQtyMorning = $totalAmtMorning = $totalQtyEvening = $totalAmtEvening = 0;
        $fatPlusMorning = $snfPlusMorning = $fatPlusEvening = $snfPlusEvening = 0;
        //dd($transactions);
         $fatkg=0;
        $snfkg=0;

        foreach ($transactions as $date => $shifts) {

            $maxRows = max(count($shifts['morning'] ?? []), count($shifts['evening'] ?? []));
            for ($i = 0; $i < $maxRows; $i++) {
                $morning = $shifts['morning'][$i] ?? null;
                $evening = $shifts['evening'][$i] ?? null;

                $mQty = $mFat = $mSnf = $mRate = $mAmt = 0;
                $eQty = $eFat = $eSnf = $eRate = $eAmt = 0;

                if ($morning) {
                    $mQty = $morning->milkQuality;
                    $mFat = $morning->fat;
                    $mSnf = $morning->snf;
                    $mRate = $morning->rate;
                    $mAmt = $morning->amount;
                    $fatkg += $morning->fatkg;
                    $snfkg += $morning->snfkg;

                    $totalQtyMorning += $mQty;
                    $totalAmtMorning += $mAmt;
                    $fatPlusMorning += $mQty * $mFat;
                    $snfPlusMorning += $mQty * $mSnf;
                }

                if ($evening) {
                    $eQty = $evening->milkQuality;
                    $eFat = $evening->fat;
                    $eSnf = $evening->snf;
                    $eRate = $evening->rate;
                    $eAmt = $evening->amount;
                      $fatkg += $evening->fatkg;
                        $snfkg += $evening->snfkg;

                    $totalQtyEvening += $eQty;
                    $totalAmtEvening += $eAmt;
                    $fatPlusEvening += $eQty * $eFat;
                    $snfPlusEvening += $eQty * $eSnf;
                }

                $sheet->fromArray([
                    date("d-m-Y", strtotime($date)),
                    $mQty ?: '',
                    $mQty ? $mFat : '',
                    $mQty ? $mSnf : '0',
                    $mQty ? round($mRate, 2) : '',
                    $mQty ? round($mAmt, 2) : '',
                    $eQty ?: '',
                    $eQty ? $eFat : '',
                    $eQty ? $eSnf : '0',
                    $eQty ? round($eRate, 2) : '',
                    $eQty ? round($eAmt, 2) : '',
                    round($mQty + $eQty, 1),
                    round($mAmt + $eAmt, 2)
                ], null, "A{$rowIndex}");

                $rowIndex++;
            }
        }

        $avgFatM = $totalQtyMorning ? round($fatPlusMorning / $totalQtyMorning, 1) : "0.0";
        $avgSnfM = $totalQtyMorning ? floor($snfPlusMorning / $totalQtyMorning) : "0";
        $avgFatE = $totalQtyEvening ? round($fatPlusEvening / $totalQtyEvening, 1) : "0.0";
        $avgSnfE = $totalQtyEvening ? floor($snfPlusEvening / $totalQtyEvening) : "0";

        $sheet->setCellValue("A{$rowIndex}", "Total");
        $sheet->setCellValue("B{$rowIndex}", round($totalQtyMorning, 1));
        $sheet->setCellValue("C{$rowIndex}", $avgFatM);
        $sheet->setCellValue("D{$rowIndex}", $avgSnfM);
        $sheet->setCellValue("F{$rowIndex}", round($totalAmtMorning, 2));
        $sheet->setCellValue("G{$rowIndex}", round($totalQtyEvening, 1));
        $sheet->setCellValue("H{$rowIndex}", $avgFatE);
        $sheet->setCellValue("I{$rowIndex}", $avgSnfE);
        $sheet->setCellValue("K{$rowIndex}", round($totalAmtEvening, 2));
        $sheet->setCellValue("L{$rowIndex}", round($totalQtyMorning + $totalQtyEvening, 1));
        $sheet->setCellValue("M{$rowIndex}", round($totalAmtMorning + $totalAmtEvening, 2));
        $sheet->getStyle("A{$rowIndex}:M{$rowIndex}")->applyFromArray($totalStyle);
       $rowIndex++;
        $sheet->setCellValue("A{$rowIndex}", "Total");
        $sheet->setCellValue("B{$rowIndex}", 'Fatkg');
        $sheet->setCellValue("C{$rowIndex}", round($fatkg, 2));
        $sheet->setCellValue("D{$rowIndex}", 'Snfkg');
        $sheet->setCellValue("F{$rowIndex}", round($snfkg, 2));
           $sheet->getStyle("A{$rowIndex}:F{$rowIndex}")->applyFromArray($totalStyle);

        $numericCols = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
        foreach ($numericCols as $col) {
            $sheet->getStyle("{$col}7:{$col}{$rowIndex}")->applyFromArray($numberStyle);
        }

        $milkTypeRow = $rowIndex + 2;
        $sheet->setCellValue("A{$milkTypeRow}", "Milk Type Summary");
        $sheet->mergeCells("A{$milkTypeRow}:M{$milkTypeRow}");
        $sheet->getStyle("A{$milkTypeRow}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A{$milkTypeRow}")->getAlignment()->setHorizontal('center');

        $headerRow = $milkTypeRow + 1;
        $milkHeaders = [
            'A' => ['C', "Type"],
            'D' => ['E', "Total Quantity"],
            'F' => ['G', "Total Amount"],
            'H' => ['I', "Avg Fat"],
            'J' => ['K', "Avg SNF"],
            'L' => ['M', "Avg Rate"]
        ];

        foreach ($milkHeaders as $startCol => [$endCol, $label]) {
            $sheet->mergeCells("{$startCol}{$headerRow}:{$endCol}{$headerRow}");
            $sheet->setCellValue("{$startCol}{$headerRow}", $label);
            $sheet->getStyle("{$startCol}{$headerRow}")->applyFromArray($headerStyle);
            $sheet->getStyle("{$startCol}{$headerRow}")->getAlignment()->setHorizontal('center');
        }
        // === Flatten and Calculate Milk Types
        $buffaloQty = $buffaloAmount = $buffaloFat = $buffaloSnf = 0;
        $cowQty = $cowAmount = $cowFat = $cowSnf = 0;
        $allEntries = $transactions->flatMap(
            fn($shiftsByDate) =>
            collect($shiftsByDate)->flatMap(fn($shifts) => collect($shifts))
        );
        foreach ($allEntries as $entry) {
            if ($entry->milkType === 'buffalo') {
                $buffaloQty += $entry->milkQuality;
                $buffaloAmount += $entry->amount;
                $buffaloFat += $entry->milkQuality * $entry->fat;
                $buffaloSnf += $entry->milkQuality * $entry->snf;
            } elseif ($entry->milkType === 'cow') {
                $cowQty += $entry->milkQuality;
                $cowAmount += $entry->amount;
                $cowFat += $entry->milkQuality * $entry->fat;
                $cowSnf += $entry->milkQuality * $entry->snf;
            }
        }

        function fillMilkRow($sheet, $row, $type, $qty, $amt, $fat, $snf)
        {
            $avgFat = $qty ? round($fat / $qty, 1) : "0.0";
            $avgSnf = $qty ? floor($snf / $qty) : "0";
            $avgRate = $qty ? round($amt / $qty, 2) : "0.00";

            $sheet->mergeCells("A{$row}:C{$row}")->setCellValue("A{$row}", ucfirst($type));
            $sheet->mergeCells("D{$row}:E{$row}")->setCellValue("D{$row}", round($qty, 1));
            $sheet->mergeCells("F{$row}:G{$row}")->setCellValue("F{$row}", round($amt, 2));
            $sheet->mergeCells("H{$row}:I{$row}")->setCellValue("H{$row}", $avgFat);
            $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", $avgSnf);
            $sheet->mergeCells("L{$row}:M{$row}")->setCellValue("L{$row}", $avgRate);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
        }

        fillMilkRow($sheet, $milkTypeRow + 2, 'Buffalo', $buffaloQty, $buffaloAmount, $buffaloFat, $buffaloSnf);
        fillMilkRow($sheet, $milkTypeRow + 3, 'Cow', $cowQty, $cowAmount, $cowFat, $cowSnf);

        $milkTypeRow = $milkTypeRow + 4;
        $sheet->mergeCells("A{$milkTypeRow}:M{$milkTypeRow}");
        $milkTypeRow = $milkTypeRow + 1;
        $sheet->mergeCells("A{$milkTypeRow}:M{$milkTypeRow}");

        // === SALES REPORT ===
        // Leave two empty rows after milk type 
        $salesStartRow = $milkTypeRow;
        $sheet->setCellValue("A{$salesStartRow}", "SALES REPORT");
        $sheet->mergeCells("A{$salesStartRow}:M{$salesStartRow}");
        $sheet->getStyle("A{$salesStartRow}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A{$salesStartRow}")->getAlignment()->setHorizontal('center');

        // === Fetch Sales Data ===
        $query = DB::table('sales')
            ->where('dairyId', $dairy->dairyId)
            ->where('partyCode', $partyCode)
            ->where('status', 'true')
            ->whereIn('saleType', ['local_sale', 'product_sale']);

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('saleDate', [$startDate, $endDate]);
        }
        $sales = $query->get();

        // === Headers ===
        $headerRow = $salesStartRow + 1;
        $salesHeaders = [
            'A' => ['B', 'Date'],
            'C' => ['c', 'Sale Type'],
            'D' => ['F', 'Product Name'],
            'G' => ['G', 'Qty'],
            'H' => ['H', 'Rate'],
            'I' => ['I', 'Amount'],
            'J' => ['J', 'Discount'],
            'K' => ['K', 'Paid Amount'],
            'L' => ['M', 'Final Amount']
        ];

        foreach ($salesHeaders as $startCol => [$endCol, $label]) {
            $sheet->mergeCells("{$startCol}{$headerRow}:{$endCol}{$headerRow}");
            $sheet->setCellValue("{$startCol}{$headerRow}", $label);
            $sheet->getStyle("{$startCol}{$headerRow}")->applyFromArray($headerStyle);
            $sheet->getStyle("{$startCol}{$headerRow}")->getAlignment()->setHorizontal('center');
        }

        // === Adjust Column Widths ===
        /* foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }*/
        $row = $headerRow + 1;
        $salesTotal = 0;
        if ($sales->isEmpty()) {
            $sheet->mergeCells("A{$row}:M{$row}");
            $sheet->setCellValue("A{$row}", "DATA NOT FOUND");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setItalic(true)->getColor()->setRGB('FF0000');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
        } else {
            foreach ($sales as $data) {
                if ($data->productType == 'cowMilk') {
                    $product = 'Cow Milk';
                } elseif ($data->productType == 'buffaloMilk') {
                    $product = 'Buffalo Milk';
                } else {
                    $product = DB::table('products')->where('productCode', $data->productType)->value('productName') ?: $data->productType;
                }
                $amount = $data->productQuantity * $data->productPricePerUnit;
                $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", date("d-m-Y", strtotime($data->saleDate)));
                $sheet->mergeCells("C{$row}:D{$row}")->setCellValue("C{$row}", ucfirst(str_replace('_', ' ', $data->saleType)));
                $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", $product);
                $sheet->setCellValue("G{$row}", round($data->productQuantity, 2));
                $sheet->setCellValue("H{$row}", round($data->productPricePerUnit, 2));
                $sheet->setCellValue("I{$row}", round($amount, 2));
                $sheet->setCellValue("J{$row}", round($data->discount, 2));
                $sheet->setCellValue("K{$row}", round($data->paidAmount, 2));
                $sheet->setCellValue("M{$row}", round($data->finalAmount, 2));
                foreach (range('A', 'M') as $col) {
                    $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal('center');
                }
                $salesTotal += $data->finalAmount;
                $row++;
            }
            $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", "TOTAL");
            $sheet->setCellValue("M{$row}", round($salesTotal, 2));
            $sheet->getStyle("A{$row}:M{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:M{$row}")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($totalStyle);
        }
        $row = $row + 1;
        $sheet->mergeCells("A{$row}:M{$row}");
        $row = $row + 1;
        $sheet->mergeCells("A{$row}:M{$row}");



        // === ADVANCE/CREDIT DETAILS ===
        $creditAdvanceStartRow = $row;
        $sheet->setCellValue("A{$creditAdvanceStartRow}", "Advance / Credit Details");
        $sheet->mergeCells("A{$creditAdvanceStartRow}:D{$creditAdvanceStartRow}");
        $sheet->getStyle("A{$creditAdvanceStartRow}")->getFont()->setBold(true)->setSize(12);
        $headers = ['Date', 'Type', 'Amount', 'Remarks'];
        $creditAdvanceHeaderRow = $creditAdvanceStartRow + 1;

        foreach ($headers as $colIndex => $header) {
            $col = chr(65 + $colIndex);
            $sheet->setCellValue($col . $creditAdvanceHeaderRow, $header);
            $sheet->getStyle($col . $creditAdvanceHeaderRow)->applyFromArray($headerStyle);
        }
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);

        $credit = DB::table('credit')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('partyCode', $partyCode)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy("created_at", "desc")
            ->get();

        $advance = DB::table('advance')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('partyCode', $partyCode)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy("created_at", "desc")
            ->get();

        $dairyInfo = DB::table("dairy_info")->where("id", session()->get('loginUserInfo')->dairyId)->first();
        $mem = DB::table("member_personal_info")->where("dairyId", session()->get('loginUserInfo')->dairyId)
            ->where("memberPersonalCode", $partyCode)->first();
        if (!$mem) {
            return ["error" => true, "msg" => "No member found"];
        }
        $ubal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->first();
        $details = [];
        foreach ($advance as $a) {
            //dd($a);
            // $typeLabel = !empty($a->amount) && $a->amount >= 0 ?  ' cr.' : ' dr.';
            $details[] = [
                'date' => date("d-m-Y", strtotime($a->created_at)),
                'type' => 'Advance',
                'amount' => $a->amount,
                'remarks' => $a->remark ?? '',
            ];
        }
        foreach ($credit as $c) {
            //$typeLabel = $c->amount >= 0 ?  ' cr.' : ' dr.';
            $details[] = [
                'date' => date("d-m-Y", strtotime($c->created_at)),
                'type' => 'Credit',
                'amount' => $c->amount,
                'remarks' => $c->remark ?? '',
            ];
        }
        usort($details, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        $currentRow = $creditAdvanceHeaderRow + 1;
        $totalAdvance = 0;
        $totalCredit = 0;

        if (empty($details)) {
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", " DATA NOT FOUND");
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setItalic(true)->getColor()->setRGB('FF0000');
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal('center');
            $currentRow++;
        } else {
            foreach ($details as $entry) {
                $sheet->setCellValue("A{$currentRow}", $entry['date']);
                $sheet->setCellValue("B{$currentRow}", $entry['type']);
                $sheet->setCellValue("C{$currentRow}", $entry['amount']);
                $sheet->setCellValue("D{$currentRow}", $entry['remarks']);

                if ($entry['type'] == 'Advance') {
                    $totalAdvance += $entry['amount'];
                } else {
                    $totalCredit += $entry['amount'];
                }
                $currentRow++;
            }
        }


        $sheet->mergeCells("A{$currentRow}:M{$currentRow}");
        $currentRow = $currentRow + 1;
        $balanceRow = $currentRow;
        //$typeLabel = $previousbal->currentBalance >= 0 ?  ' cr.' : ' dr.';
        $sheet->setCellValue("A{$balanceRow}", "Previous Balance Till " . date("d-m-Y", strtotime($previousDay)));
        if (!empty($latestTransaction1) && isset($latestTransaction1->currentBalance)) {
            $sheet->setCellValue("C{$balanceRow}", $latestTransaction1->currentBalance);
        } else {
            $sheet->setCellValue("C{$balanceRow}", '0.00');
        }
        $sheet->setCellValue("D{$balanceRow}", '');
        $balanceRow1 = $balanceRow + 1;
        // $typeLabel = $lastCurrentBalance >= 0 ?  ' cr.' : ' dr.';
        $sheet->setCellValue("A{$balanceRow1}", "Balance Till " . date("d-m-Y", strtotime($endDate)));
        if (!empty($latestTransaction) && isset($latestTransaction->currentBalance)) {
            $sheet->setCellValue("C{$balanceRow1}", $latestTransaction->currentBalance);
        } else {
            $sheet->setCellValue("C{$balanceRow1}", '0.00');
        }
        $sheet->setCellValue("D{$balanceRow1}", '');
        $currentBalanceRow = $balanceRow1 + 1;
        $typeLabel2 = $ubal->openingBalanceType == "credit" ? "cr." : "dr.";
        $sheet->setCellValue("A{$currentBalanceRow}", "Current Balance");
        if (!empty($ubal) && isset($ubal->openingBalance)) {
            $sheet->setCellValue("C{$currentBalanceRow}", $ubal ? $ubal->openingBalance . $typeLabel2 : '0.00');
        } else {
            $sheet->setCellValue("C{$currentBalanceRow}", '0.00');
        }
        $sheet->setCellValue("D{$currentBalanceRow}", '');
        $sheet->getStyle("A{$balanceRow}:D{$balanceRow}")->applyFromArray($totalStyle);
        $sheet->getStyle("A{$balanceRow1}:D{$balanceRow1}")->applyFromArray($totalStyle);
        $sheet->getStyle("A{$currentBalanceRow}:D{$currentBalanceRow}")->applyFromArray($totalStyle);
        $lastRow = $currentBalanceRow;

        $sheet->getStyle("A6:M{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setPrintArea("A1:M{$lastRow}");
        $sheet->freezePane('A7');
        $fileName = 'Member_Report_' . $member->memberPersonalCode . '_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }


    private function getTransactionsBeforeDate($table, $alias, $dateColumn, $conditions, $transactionType, $pDate, $extraJoin = null, $extraSelect = [])
    {
        $query = DB::table("$table as $alias")
            ->select(array_merge(["$alias.*", "bs.currentBalance", DB::raw("'$alias' as source")], $extraSelect))
            ->leftJoin("balance_sheet as bs", function ($join) use ($alias, $transactionType) {
                $join->on("bs.transactionId", "=", "$alias.id")
                    ->where("bs.transactionType", $transactionType);
            });

        if ($extraJoin) {
            foreach ($extraJoin as $join) {
                $query->leftJoin($join['table'], $join['first'], '=', $join['second']);
            }
        }

        return $query->where($conditions)
            ->where("$alias.$dateColumn", "<", $pDate)
            ->get();
    }

    public function fetchPreviousTransactions($dairy, $partyCode, $startDate)
    {
        // $dairy = $request->user();
        // $partyCode = $request->partyCode;
        // $startDate = $request->startDate;

        $pDate = Carbon::parse($startDate)->startOfDay();

        $milkCollectionp = $this->getTransactionsBeforeDate("daily_transactions", "dt", "date", [
            "dt.dairyId" => $dairy->dairyId,
            "dt.memberCode" => $partyCode,
            "dt.status" => "true"
        ], "daily_transactions", $pDate);

        $localsaleFinalp = $this->getTransactionsBeforeDate("sales", "s", "saleDate", [
            "s.dairyId" => $dairy->dairyId,
            "s.partyCode" => $partyCode,
            "s.status" => "true",
            "s.saleType" => "local_sale"
        ], "sales", $pDate);

        $productsalep = $this->getTransactionsBeforeDate("sales", "s", "saleDate", [
            "s.dairyId" => $dairy->dairyId,
            "s.partyCode" => $partyCode,
            "s.status" => "true",
            "s.saleType" => "product_sale"
        ], "sales", $pDate, [
            ['table' => 'products as p', 'first' => 'p.productCode', 'second' => 's.productType']
        ], ["p.productName"]);

        $advancep = $this->getTransactionsBeforeDate("advance", "a", "date", [
            "a.dairyId" => $dairy->dairyId,
            "a.partyCode" => $partyCode
        ], "advance", $pDate);

        $creditp = $this->getTransactionsBeforeDate("credit", "c", "date", [
            "c.dairyId" => $dairy->dairyId,
            "c.partyCode" => $partyCode
        ], "credit", $pDate);

        $allTransactionsp = collect()
            ->merge($milkCollectionp)
            ->merge($localsaleFinalp)
            ->merge($productsalep)
            ->merge($advancep)
            ->merge($creditp);

        $latestTransactionp = $allTransactionsp->sortByDesc('created_at')->first();
        $lastCurrentBalancep = str_replace(' cr', '', $latestTransactionp->currentBalance ?? '0.00');

        return response()->json([
            'latestTransaction' => $latestTransactionp,
            'lastCurrentBalance' => $lastCurrentBalancep,
        ]);
    }




    public function exportMemberReportPdf(Request $request)
    {

        $partyCode = $request->partyCode;
        $status = $request->status;
        $startDate = $request->memberPassbookStartDate;
        $endDate = $request->memberPassbookEndDate;
        $dairy = Session::get("loginUserInfo");
        $startDate = date("Y-m-d", strtotime($startDate));
        $startDate1 = Carbon::parse($startDate)->subDay(10)->toDateString();
        $endDate1 = Carbon::parse($startDate)->subDay()->toDateString();
        $endDate = date("Y-m-d", strtotime($endDate));

        // Milk Collection
        $milkCollection = DB::table("daily_transactions as dt")
            ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "dt.id")
                    ->where("bs.transactionType", "daily_transactions");
            })
            ->where([
                "dt.dairyId" => $dairy->dairyId,
                "dt.memberCode" => $partyCode,
                "dt.status" => "true"
            ])
            ->whereBetween("dt.date", [$startDate, $endDate])
            ->get();

        // Local Sales
        $localsaleFinal = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "local_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate, $endDate])
            ->get();

        $localsalepaid = $localsaleFinal->sum('paidAmount');

        // Product Sales
        $productsale = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", "p.productName", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->leftJoin("products as p", "p.productCode", "=", "s.productType")
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "product_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate, $endDate])
            ->get();

        // Advance
        $advance = DB::table("advance as a")
            ->select("a.*", "bs.currentBalance", DB::raw("'a' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "a.id")
                    ->where("bs.transactionType", "advance");
            })
            ->where([
                "a.dairyId" => $dairy->dairyId,
                "a.partyCode" => $partyCode
            ])
            ->whereBetween("a.date", [$startDate, $endDate])
            ->get();

        // Credit
        $credit = DB::table("credit as c")
            ->select("c.*", "bs.currentBalance", DB::raw("'c' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "c.id")
                    ->where("bs.transactionType", "credit");
            })
            ->where([
                "c.dairyId" => $dairy->dairyId,
                "c.partyCode" => $partyCode
            ])
            ->whereBetween("c.date", [$startDate, $endDate])
            ->get();

        // Previous Period Data
        $milkCollection1 = DB::table("daily_transactions as dt")
            ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "dt.id")
                    ->where("bs.transactionType", "daily_transactions");
            })
            ->where([
                "dt.dairyId" => $dairy->dairyId,
                "dt.memberCode" => $partyCode,
                "dt.status" => "true"
            ])
            ->whereBetween("dt.date", [$startDate1, $endDate1])
            ->get();

        $allTransactions = collect()
            ->merge($milkCollection)
            ->merge($localsaleFinal)
            ->merge($productsale)
            ->merge($advance)
            ->merge($credit);

        $latestTransaction = $allTransactions->sortByDesc('created_at')->first();



        // Start

        $milkCollection1 = DB::table("daily_transactions as dt")
            ->select("dt.*", "bs.currentBalance", DB::raw("'dt' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "dt.id")
                    ->where("bs.transactionType", "daily_transactions");
            })
            ->where([
                "dt.dairyId" => $dairy->dairyId,
                "dt.memberCode" => $partyCode,
                "dt.status" => "true"
            ])
            ->whereBetween("dt.date", [$startDate1, $endDate1])
            ->get();

        $localsaleFinal1 = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "local_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate1, $endDate1])
            ->get();

        $localsalepaid1 = $localsaleFinal1->sum('paidAmount');

        $productsale1 = DB::table("sales as s")
            ->select("s.*", "bs.currentBalance", "p.productName", DB::raw("'s' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "s.id")
                    ->where("bs.transactionType", "sales");
            })
            ->leftJoin("products as p", "p.productCode", "=", "s.productType")
            ->where([
                "s.dairyId" => $dairy->dairyId,
                "s.partyCode" => $partyCode,
                "s.status" => "true",
                "s.saleType" => "product_sale"
            ])
            ->whereBetween("s.saleDate", [$startDate1, $endDate1])
            ->get();
        $advance1 = DB::table("advance as a")
            ->select("a.*", "bs.currentBalance", DB::raw("'a' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "a.id")
                    ->where("bs.transactionType", "advance");
            })
            ->where([
                "a.dairyId" => $dairy->dairyId,
                "a.partyCode" => $partyCode
            ])
            ->whereBetween("a.date", [$startDate1, $endDate1])
            ->get();
        $credit1 = DB::table("credit as c")
            ->select("c.*", "bs.currentBalance", DB::raw("'c' as source"))
            ->leftJoin("balance_sheet as bs", function ($join) {
                $join->on("bs.transactionId", "=", "c.id")
                    ->where("bs.transactionType", "credit");
            })
            ->where([
                "c.dairyId" => $dairy->dairyId,
                "c.partyCode" => $partyCode
            ])
            ->whereBetween("c.date", [$startDate1, $endDate1])
            ->get();

        //End

        $allTransactions1 = collect()
            ->merge($milkCollection1)
            ->merge($localsaleFinal1)
            ->merge($productsale1)
            ->merge($advance1)
            ->merge($credit1);

        $latestTransaction1 = $allTransactions1->sortByDesc('created_at')->first();

        $previousDay = Carbon::parse($startDate)->subDay()->toDateString();

        $member = DB::table('member_personal_info')
            ->where('dairyId', $dairy->dairyId)
            ->where('memberPersonalCode', $partyCode)
            ->where('status', $status)
            ->first();

        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $query = DB::table('daily_transactions')
            ->select('daily_transactions.*')
            ->addSelect(DB::raw('DATE(date) as only_date'))
            ->where('dairyId', $dairy->dairyId)
            ->where('status', 'true')
            ->where('memberCode', $partyCode)
            ->orderBy('date', 'asc');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        }

        $transactions = $query->get()
            ->groupBy(function ($item) {
                return $item->only_date;
            })
            ->map(function ($dateGroup) {
                return $dateGroup->groupBy('shift');
            });

        // Calculate totals
        $totalQtyMorning = $totalAmtMorning = $totalQtyEvening = $totalAmtEvening = 0;
        $fatPlusMorning = $snfPlusMorning = $fatPlusEvening = $snfPlusEvening = 0;
$fatkg=0;
 $snfkg=0;
        foreach ($transactions as $date => $shifts) {
            $maxRows = max(count($shifts['morning'] ?? []), count($shifts['evening'] ?? []));
            for ($i = 0; $i < $maxRows; $i++) {
                $morning = $shifts['morning'][$i] ?? null;
                $evening = $shifts['evening'][$i] ?? null;

                if ($morning) {
                    $totalQtyMorning += $morning->milkQuality;
                    $totalAmtMorning += $morning->amount;
                    $fatPlusMorning += $morning->milkQuality * $morning->fat;
                    $snfPlusMorning += $morning->milkQuality * $morning->snf;
                   $fatkg += $morning->fatkg;
                    $snfkg += $morning->snfkg;
                }

                if ($evening) {
                    $totalQtyEvening += $evening->milkQuality;
                    $totalAmtEvening += $evening->amount;
                    $fatPlusEvening += $evening->milkQuality * $evening->fat;
                    $snfPlusEvening += $evening->milkQuality * $evening->snf;
                    $fatkg += $evening->fatkg;
                    $snfkg += $evening->snfkg;
                }
            }
        }

        $avgFatM = $totalQtyMorning ? round($fatPlusMorning / $totalQtyMorning, 1) : "0.0";
        $avgSnfM = $totalQtyMorning ? floor($snfPlusMorning / $totalQtyMorning) : "0";
        $avgFatE = $totalQtyEvening ? round($fatPlusEvening / $totalQtyEvening, 1) : "0.0";
        $avgSnfE = $totalQtyEvening ? floor($snfPlusEvening / $totalQtyEvening) : "0";

        // Milk type calculations
        $buffaloQty = $buffaloAmount = $buffaloFat = $buffaloSnf = 0;
        $cowQty = $cowAmount = $cowFat = $cowSnf = 0;

        $allEntries = $transactions->flatMap(
            fn($shiftsByDate) =>
            collect($shiftsByDate)->flatMap(fn($shifts) => collect($shifts))
        );

        foreach ($allEntries as $entry) {
            if ($entry->milkType === 'buffalo') {
                $buffaloQty += $entry->milkQuality;
                $buffaloAmount += $entry->amount;
                $buffaloFat += $entry->milkQuality * $entry->fat;
                $buffaloSnf += $entry->milkQuality * $entry->snf;
            } elseif ($entry->milkType === 'cow') {
                $cowQty += $entry->milkQuality;
                $cowAmount += $entry->amount;
                $cowFat += $entry->milkQuality * $entry->fat;
                $cowSnf += $entry->milkQuality * $entry->snf;
            }
        }

        // Sales data
        $sales = DB::table('sales')
            ->where('dairyId', $dairy->dairyId)
            ->where('partyCode', $partyCode)
            ->where('status', 'true')
            ->whereIn('saleType', ['local_sale', 'product_sale'])
            ->whereBetween('saleDate', [$startDate, $endDate])
            ->get();

        // Credit/Advance data
        $credit = DB::table('credit')
            ->where('dairyId', $dairy->dairyId)
            ->where('partyCode', $partyCode)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy("created_at", "desc")
            ->get();

        $advance = DB::table('advance')
            ->where('dairyId', $dairy->dairyId)
            ->where('partyCode', $partyCode)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy("created_at", "desc")
            ->get();

        $dairyInfo = DB::table("dairy_info")->where("id", $dairy->dairyId)->first();
        $mem = DB::table("member_personal_info")->where("dairyId", $dairy->dairyId)
            ->where("memberPersonalCode", $partyCode)->first();

        if (!$mem) {
            return response()->json(['error' => 'No member found'], 404);
        }

        $ubal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->first();

        $details = [];
        foreach ($advance as $a) {
            $details[] = [
                'date' => date("d-m-Y", strtotime($a->created_at)),
                'type' => 'Advance',
                'amount' => $a->amount,
                'remarks' => $a->remark ?? '',
            ];
        }

        foreach ($credit as $c) {
            $details[] = [
                'date' => date("d-m-Y", strtotime($c->created_at)),
                'type' => 'Credit',
                'amount' => $c->amount,
                'remarks' => $c->remark ?? '',
            ];
        }

        usort($details, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });



        // Generate PDF
        $pdf = PDF::loadView('report.member_report_pdfs', [
            'member' => $member,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactions' => $transactions,
            'totalQtyMorning' => $totalQtyMorning,
            'totalAmtMorning' => $totalAmtMorning,
            'totalQtyEvening' => $totalQtyEvening,
            'totalAmtEvening' => $totalAmtEvening,
            'avgFatM' => $avgFatM,
            'avgSnfM' => $avgSnfM,
            'avgFatE' => $avgFatE,
            'avgSnfE' => $avgSnfE,
            'buffaloQty' => $buffaloQty,
            'buffaloAmount' => $buffaloAmount,
            'buffaloFat' => $buffaloFat,
            'buffaloSnf' => $buffaloSnf,
            'cowQty' => $cowQty,
            'cowAmount' => $cowAmount,
            'cowFat' => $cowFat,
            'cowSnf' => $cowSnf,
            'sales' => $sales,
            'details' => $details,
            'latestTransaction1' => $latestTransaction1,
            'latestTransaction' => $latestTransaction,
            'ubal' => $ubal,
            'previousDay' => $previousDay,
            'dairyInfo' => $dairyInfo,
            'fatkg' => $fatkg,
            'snfkg' => $snfkg,
        ]);

        $fileName = 'Member_Report_' . $member->memberPersonalCode . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
