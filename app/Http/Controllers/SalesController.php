<?php

namespace App\Http\Controllers;

use App\sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PushNotification;
use View;
use function GuzzleHttp\json_encode;

class SalesController extends Controller
{

    public function __construct()
    {
        $this->middleware('Auth');
    }

    /* ═══════════════════════════════════════════════════════
       SALE LIST
       ═══════════════════════════════════════════════════════ */
    public function saleList(Request $request)
    {
        $sales = DB::table('sales')
            ->where(['dairyId' => session()->get('loginUserInfo')->dairyId, "status" => "true"])
            ->get();

        return view('saleList', ['sales' => $sales, "activepage" => "localSale"]);
    }

    /* ═══════════════════════════════════════════════════════
       LOCAL SALE FORM
       ═══════════════════════════════════════════════════════ */
    public function SaleForm(Request $request)
    {
        if ($request->date != (null || "")) {
            $date = date("Y-m-d", strtotime($request->date));
        } else {
            $date = date("Y-m-d");
        }
        if ($request->party != (null || "")) {
            $party = $request->party;
        } else {
            $party = "customer";
        }

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $customers = DB::table('customer')
            ->where('dairyId', $dairyId)
            ->where('status', "true")
            ->get();

        $categories = DB::table('categories')
            ->where('dairyId', $dairyId)
            ->get();

        $memberPersonalInfo = DB::table('member_personal_info')
            ->where('dairyId', $dairyId)
            ->where('status', "true")
            ->get();

        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $msc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d"))->where("shift", "morning")
            ->sum("milkQuality");

        $esc = DB::table("daily_transactions")->where("dairyId", $dairyId)
            ->where("date", date("Y-m-d"))->where("shift", "evening")
            ->sum("milkQuality");

        $tsale = DB::table("sales")->where("dairyId", $dairyId)->where("saleType", "local_sale")
            ->where("saleDate", date("Y-m-d"))->sum('productQuantity');

        $currnetData = array($customers, $memberPersonalInfo);
        $noCustomer  = (count($customers) <= 1) ? true : false;

        return view('localSale', [
            'currnetData' => $currnetData,
            "dairyInfo"   => $dairyInfo,
            "categories"  => $categories,
            "msc"         => number_format($msc, 2, ".", ""),
            "esc"         => number_format($esc, 2, ".", ""),
            "tsale"       => $tsale,
            "date"        => $date,
            "party"       => $party,
            "activepage"  => "localSale",
            "noCustomer"  => $noCustomer
        ]);
    }

    public function SaleForm_delete(Request $request)
    {
        if ($request->date != (null || "")) {
            $date = date("Y-m-d", strtotime($request->date));
        } else {
            $date = date("Y-m-d");
        }
        if ($request->party != (null || "")) {
            $party = $request->party;
        } else {
            $party = "customer";
        }

        $dairyId = session()->get('loginUserInfo')->dairyId;
        $customers = DB::table('customer')->where('dairyId', $dairyId)->where('status', "false")->get();
        $categories = DB::table('categories')->where('dairyId', $dairyId)->get();
        $memberPersonalInfo = DB::table('member_personal_info')->where('dairyId', $dairyId)->where('status', "false")->get();
        $dairyInfo = DB::table("dairy_info")->where("id", $dairyId)->get()->first();

        $msc  = DB::table("daily_transactions")->where("dairyId", $dairyId)->where("date", date("Y-m-d"))->where("shift", "morning")->sum("milkQuality");
        $esc  = DB::table("daily_transactions")->where("dairyId", $dairyId)->where("date", date("Y-m-d"))->where("shift", "evening")->sum("milkQuality");
        $tsale = DB::table("sales")->where("dairyId", $dairyId)->where("saleType", "local_sale")->where("saleDate", date("Y-m-d"))->sum('productQuantity');

        $currnetData = array($customers, $memberPersonalInfo);
        $noCustomer  = (count($customers) <= 1) ? true : false;

        return view('localSale_delete', [
            'currnetData' => $currnetData,
            "dairyInfo"   => $dairyInfo,
            "categories"  => $categories,
            "msc"         => number_format($msc, 2, ".", ""),
            "esc"         => number_format($esc, 2, ".", ""),
            "tsale"       => $tsale,
            "date"        => $date,
            "party"       => $party,
            "activepage"  => "localSale",
            "noCustomer"  => $noCustomer
        ]);
    }

    /* ═══════════════════════════════════════════════════════
       LOCAL SALE AJAX (DataTables)
       ═══════════════════════════════════════════════════════ */
    function getLocalSaleAjaxDelete()
    {
        $sales = DB::table('sales')
            ->where(['dairyId' => session()->get('loginUserInfo')->dairyId, 'saleType' => "local_sale", 'status' => "false"])
            ->orderby("created_at", "DESC")->get();
        $data = [];
        $i    = 0;
        foreach ($sales as $s) {
            $data[] = [$i, $s->partyCode, $s->partyName, $s->milkType, date("d-m-Y", strtotime($s->saleDate)), $s->productPricePerUnit, $s->productQuantity, "&#8377; " . $s->amount, $s->discount, $s->finalAmount, $s->paidAmount, ucfirst($s->amountType), "<a href='javascript:void(0);' onclick='editSale(" . $s->id . ")' ><i class='fa fa-edit'></i></a>"];
            $i++;
        }
        return ["data" => $data];
    }

    public function getLocalSaleAjax(Request $req)
    {
        $sales = DB::table('sales')
            ->where(['dairyId' => session()->get('loginUserInfo')->dairyId, 'saleType' => "local_sale", 'status' => "true"])
            ->orderby("created_at", "DESC")->get();
        $data = [];
        $i    = 0;
        foreach ($sales as $s) {
            $data[] = [$i, $s->partyCode, $s->partyName, $s->milkType, date("d-m-Y", strtotime($s->saleDate)), $s->productPricePerUnit, $s->productQuantity, "&#8377; " . $s->amount, $s->discount, $s->finalAmount, $s->paidAmount, ucfirst($s->amountType), "<a href='javascript:void(0);' onclick='editSale(" . $s->id . ")' ><i class='fa fa-edit'></i></a>"];
            $i++;
        }
        return ["data" => $data];
    }

    public function getProductSaleAjax(Request $req)
    {
        $val   = $req->filter_value ?: "500";
        $sales = DB::table('sales')
            ->select("sales.id", "sales.partyCode", "sales.partyName", "sales.saleDate", "sales.productType", "sales.productPricePerUnit", "sales.purchaseAmount as purchaseAmountS", "sales.productQuantity", "sales.amount", "products.purchaseamount", "sales.discount", "sales.paidAmount", "sales.finalAmount", "sales.amountType", "products.productName")
            ->where(['sales.dairyId' => session()->get('loginUserInfo')->dairyId, 'sales.saleType' => "product_sale", "sales.status" => "true"])
            ->leftjoin("products", "products.productCode", "=", "sales.productType")
            ->orderby("sales.created_at", "DESC")
            ->paginate($val);
        $data = [];
        $i    = 0;
        foreach ($sales as $s) {
            $data[] = [$i, $s->partyCode, $s->partyName, date("d-m-Y", strtotime($s->saleDate)), ($s->productName) ? $s->productName : $s->productType, $s->productPricePerUnit, $s->productQuantity, "&#8377; " . $s->amount, "&#8377; " . $s->purchaseAmountS, $s->discount, "&#8377; " . $s->paidAmount, "&#8377; " . number_format($s->finalAmount, 2), ucfirst($s->amountType), "<a href='javascript:void(0);' onclick='editSale(" . $s->id . ")' ><i class='fa fa-edit'></i></a>"];
            $i++;
        }
        return ["data" => $data];
    }

    public function getProductSaleAjax_delete(Request $req)
    {
        $sales = DB::table('sales')
            ->select("sales.id", "sales.partyCode", "sales.partyName", "sales.saleDate", "sales.productType", "sales.productPricePerUnit", "sales.purchaseAmount as purchaseAmountS", "sales.productQuantity", "sales.amount", "products.purchaseamount", "sales.discount", "sales.paidAmount", "sales.finalAmount", "sales.amountType", "products.productName")
            ->where(['sales.dairyId' => session()->get('loginUserInfo')->dairyId, 'sales.saleType' => "product_sale", "sales.status" => "false"])
            ->leftjoin("products", "products.productCode", "=", "sales.productType")
            ->orderby("sales.created_at", "DESC")
            ->get();
        $data = [];
        $i    = 0;
        foreach ($sales as $s) {
            $data[] = [$i, $s->partyCode, $s->partyName, date("d-m-Y", strtotime($s->saleDate)), ($s->productName) ? $s->productName : $s->productType, $s->productPricePerUnit, $s->productQuantity, "&#8377; " . $s->amount, "&#8377; " . $s->purchaseAmountS, $s->discount, "&#8377; " . $s->paidAmount, "&#8377; " . number_format($s->finalAmount, 2), ucfirst($s->amountType), "<a href='javascript:void(0);' onclick='editSale(" . $s->id . ")' ><i class='fa fa-edit'></i></a>"];
            $i++;
        }
        return ["data" => $data];
    }

    /* ═══════════════════════════════════════════════════════
       PLANT SALE — DataTables source
       ═══════════════════════════════════════════════════════ */
    public function getPlantSaleAjax(Request $req)
    {
        $query = DB::table('sales')
            ->where('dairyId', session()->get('loginUserInfo')->dairyId)
            ->where('saleType', "plant_sale")
            ->where('status', 'true');

        if ($req->plant != null) {
            $query->where('partyCode', $req->plant);
        }

        $sales = $query->orderBy('saleDate', "desc")->get();

        $data = [];
        foreach ($sales as $s) {
            $data[] = [
                $s->partyName,
                ucfirst($s->shift ?? '—'),
                ucfirst($s->milkType ?? '—'),
                $s->productQuantity,
                $s->fat ?? '—',
                $s->snf ?? '—',
                "&#8377; " . $s->productPricePerUnit,
                "&#8377; " . $s->amount,
                "&#8377; " . $s->paidAmount,
                date("d-m-Y", strtotime($s->saleDate)),
                "<a href='javascript:void(0);' onclick='editSale(event, " . $s->id . ", \"" . addslashes($s->partyName) . "\")'><i class='fa fa-edit'></i></a>
                 <a href='javascript:void(0);' onclick='deleteSale(event, " . $s->id . ")'><i class='fa fa-trash text-danger'></i></a>",
            ];
        }
        return ["data" => $data];
    }

    /* ═══════════════════════════════════════════════════════
       PLANT SALE FORM
       ═══════════════════════════════════════════════════════ */
    public function plantSaleForm(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        /* ── Date & Shift ── */
        if (session()->has('plantSaleDate')) {
            $date = session()->get('plantSaleDate');
            $flag = 0;
        } else {
            $date = date('d-m-Y');
            $flag = 1;
        }

        if (session()->has('plantSaleShift')) {
            $curShift = session()->get('plantSaleShift');
            if ($flag) $flag = 0;
        } else {
            $curShift = (date('H', time()) < 12) ? 'morning' : 'evening';
            $flag = 1;
        }

        if ($request->filled('date'))  { $date     = $request->date;  $flag = 0; }
        if ($request->filled('shift')) { $curShift = $request->shift; $flag = 0; }

        /* ── Milk plants linked to this dairy ── */
        $milk_plants = DB::table('plantdairymap')
            ->select("milk_plants.*", "plantdairymap.plantId as id")
            ->where(["plantdairymap.dairyId" => $dairyId])
            ->join("milk_plants", "plantdairymap.plantId", "=", "milk_plants.id")
            ->get();

        $noPlant = (count($milk_plants) == 0) ? 1 : 0;

        /* ── Morning / Evening plant sale totals ── */
        $msc = DB::table('sales')
            ->where('dairyId',  $dairyId)
            ->where('saleType', 'plant_sale')
            ->where('saleDate', date('Y-m-d', strtotime($date)))
            ->where('shift',    'morning')
            ->where('status',   'true')
            ->sum('productQuantity');

        $esc = DB::table('sales')
            ->where('dairyId',  $dairyId)
            ->where('saleType', 'plant_sale')
            ->where('saleDate', date('Y-m-d', strtotime($date)))
            ->where('shift',    'evening')
            ->where('status',   'true')
            ->sum('productQuantity');

        /* ── Plant rate card from dairy_info ── */
        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->first();

        $ratecardtype = $dairyInfo->plantRateCardTypeForCow ?? 'fat';

        $noRateCard = 0;
        if (!$dairyInfo->plantRateCardIdForCow || $dairyInfo->plantRateCardIdForCow == '')         $noRateCard = 1;
        if (!$dairyInfo->plantRateCardIdForBuffalo || $dairyInfo->plantRateCardIdForBuffalo == '')  $noRateCard = 1;

        /* ── Utility setup ── */
        $defaultUtility = (object)[
            'communicationPort'            => '',
            'maxSpeed'                     => '',
            'connectionPerferenceParity'   => '',
            'connectionPerferenceDataBits' => '',
            'connectionPerferenceStopBits' => '',
            'isActive'                     => '0',
            'decimal_digit'                => '1',
        ];

        $mUtility = DB::table('utility_setup')
            ->where('dairyId',    $dairyId)
            ->where('status',     'true')
            ->where('machinType', 'milk')
            ->first() ?? $defaultUtility;

        $wUtility = DB::table('utility_setup')
            ->where('dairyId',    $dairyId)
            ->where('status',     'true')
            ->where('machinType', 'weight')
            ->first() ?? $defaultUtility;

        return view('plantSale', [
            'milkPlants'   => $milk_plants,
            'noPlant'      => $noPlant,
            'date'         => $date,
            'curShift'     => $curShift,
            'flag'         => $flag,
            'msc'          => number_format($msc, 1, '.', ''),
            'esc'          => number_format($esc, 1, '.', ''),
            'ratecardtype' => $ratecardtype,
            'noRateCard'   => $noRateCard,
            'mUtility'     => $mUtility,
            'wUtility'     => $wUtility,
            'activepage'   => 'plantSale',
        ]);
    }

    /* ═══════════════════════════════════════════════════════
       PLANT SALE FORM SUBMIT
       ═══════════════════════════════════════════════════════ */
    public function plantSaleFormSubmit(Request $req)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $validator = Validator::make($req->all(), [
            'plantCode'  => 'required',
            'quantity'   => 'required|numeric|min:0.001',
            'fat'        => 'required|numeric',
            'amount'     => 'required|numeric',
            'date'       => 'required',
            'dailyShift' => 'required',
            'price'      => 'required|numeric',
            'milkType'   => 'required',
            'product'    => 'required',
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'msg' => $validator->errors()->first()];
        }

        if ($req->rateCardType === 'fat/snf' && empty($req->snf)) {
            return ['error' => true, 'msg' => 'SNF is required for fat/snf rate card.'];
        }

        $plant = DB::table('milk_plants')->where('id', $req->plantCode)->first();
        if (!$plant) {
            return ['error' => true, 'msg' => 'Plant not found.'];
        }

        $saleDate = date('Y-m-d', strtotime($req->date));
        $shift    = strtolower($req->dailyShift);

        /* ── Duplicate check ── */
        $existing = DB::table('sales')
            ->where('dairyId',   $dairyId)
            ->where('partyCode', $req->plantCode)
            ->where('saleDate',  $saleDate)
            ->where('shift',     $shift)
            ->where('saleType',  'plant_sale')
            ->where('status',    'true')
            ->first();

        if ($existing && empty($req->forceSubmit)) {
            return [
                'error'     => false,
                'duplicate' => true,
                'msg'       => 'A plant sale already exists for this shift. Add anyway?',
            ];
        }

        try {
            $id = DB::table('sales')->insertGetId([
                'dairyId'             => $dairyId,
                'partyCode'           => $req->plantCode,
                'partyName'           => $plant->plantName,
                'partyType'           => 'plant',
                'shift'               => $shift,
                'saleDate'            => $saleDate,
                'saleFromDate'        => $saleDate,
                'milkType'            => $req->milkType,
                'productQuantity'     => $req->quantity,
                'fat'                 => $req->fat,
                'snf'                 => $req->snf ?? null,
                'rateCardType'        => $req->rateCardType ?? 'fat',
                'productPricePerUnit' => $req->price,
                'amount'              => $req->amount,
                'finalAmount'         => $req->amount,
                'paidAmount'          => $req->paidAmount ?? 0,
                'productType'         => $req->product,
                'unit'                => 'Ltr',
                'discount'            => 0,
                'saleType'            => 'plant_sale',
                'amountType'          => 'credit',
                'status'              => 'true',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        } catch (\Exception $e) {
            return ['error' => true, 'msg' => 'DB Error: ' . $e->getMessage()];
        }

        session()->put('plantSaleDate',  $req->date);
        session()->put('plantSaleShift', $shift);

        return ['error' => false, 'msg' => 'Plant sale saved successfully.', 'transId' => $id];
    }

    /* ═══════════════════════════════════════════════════════
       PLANT SALE LIST AJAX
       ═══════════════════════════════════════════════════════ */
    public function plantSaleListAjax(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        session()->put('plantSaleDate',  $request->date);
        session()->put('plantSaleShift', $request->shift);

        $msc = DB::table('sales')
            ->where('dairyId',  $dairyId)
            ->where('saleType', 'plant_sale')
            ->where('saleDate', date('Y-m-d', strtotime($request->date)))
            ->where('shift',    'morning')
            ->where('status',   'true')
            ->sum('productQuantity');

        $esc = DB::table('sales')
            ->where('dairyId',  $dairyId)
            ->where('saleType', 'plant_sale')
            ->where('saleDate', date('Y-m-d', strtotime($request->date)))
            ->where('shift',    'evening')
            ->where('status',   'true')
            ->sum('productQuantity');

        $sales = DB::table('sales')
            ->where('dairyId',  $dairyId)
            ->where('saleType', 'plant_sale')
            ->where('saleDate', date('Y-m-d', strtotime($request->date)))
            ->where('shift',    strtolower($request->shift))
            ->where('status',   'true')
            ->orderBy('created_at', 'desc')
            ->get();

        $view = View::make('plantSaleListModel', ['sales' => $sales]);

        return [
            'error'   => false,
            'content' => (string) $view,
            'msc'     => number_format($msc, 1, '.', ''),
            'esc'     => number_format($esc, 1, '.', ''),
        ];
    }

    /* ═══════════════════════════════════════════════════════
       GET SINGLE PLANT SALE VALUES  (feeds edit modal)
       ═══════════════════════════════════════════════════════ */
    public function getPlantSaleValues(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $sale = DB::table('sales')
            ->where('id',       $request->saleId)
            ->where('dairyId',  $dairyId)
            ->where('saleType', 'plant_sale')
            ->where('status',   'true')
            ->first();

        if (!$sale) return ['error' => true, 'msg' => 'Sale record not found.'];

        return ['error' => false, 'sale' => $sale];
    }

    /* ═══════════════════════════════════════════════════════
       UPDATE PLANT SALE
       ═══════════════════════════════════════════════════════ */
    public function updatePlantSale(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $validator = Validator::make($request->all(), [
            'saleId'    => 'required',
            'plantCode' => 'required',
            'quantity'  => 'required|numeric',
            'fat'       => 'required|numeric',
            'amount'    => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'msg' => 'Missing fields: ' . $validator->errors()->first()];
        }

        if ($request->rateCardType === 'fat/snf' && empty($request->snf)) {
            return ['error' => true, 'msg' => 'SNF is required for fat/snf rate card.'];
        }

        $plant = DB::table('milk_plants')->where('id', $request->plantCode)->first();

        try {
            $updated = DB::table('sales')
                ->where('id',       $request->saleId)
                ->where('dairyId',  $dairyId)
                ->where('saleType', 'plant_sale')
                ->update([
                    'partyCode'           => $request->plantCode,
                    'partyName'           => $plant ? $plant->plantName : '',
                    'milkType'            => $request->milkType,
                    'productQuantity'     => $request->quantity,
                    'fat'                 => $request->fat,
                    'snf'                 => $request->snf ?? null,
                    'rateCardType'        => $request->rateCardType ?? 'fat',
                    'productPricePerUnit' => $request->price,
                    'amount'              => $request->amount,
                    'finalAmount'         => $request->amount,
                    'paidAmount'          => $request->paidAmount ?? 0,
                    'updated_at'          => now(),
                ]);
        } catch (\Exception $e) {
            return ['error' => true, 'msg' => 'DB Error: ' . $e->getMessage()];
        }

        if ($updated) return ['error' => false, 'msg' => 'Plant sale updated successfully.'];
        return ['error' => true, 'msg' => 'Nothing updated. Check the record.'];
    }

    /* ═══════════════════════════════════════════════════════
       DELETE PLANT SALE  (soft delete)
       ═══════════════════════════════════════════════════════ */
    public function deletePlantSale(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $deleted = DB::table('sales')
            ->where('id',       $request->saleId)
            ->where('dairyId',  $dairyId)
            ->where('saleType', 'plant_sale')
            ->update(['status' => 'false', 'updated_at' => now()]);

        if ($deleted) return ['error' => false, 'msg' => 'Sale deleted successfully.'];
        return ['error' => true, 'msg' => 'Could not delete sale.'];
    }

    /* ═══════════════════════════════════════════════════════
       PLANT SALE — Rate Card value lookup
       ═══════════════════════════════════════════════════════ */
    public function plantSaleRateCardValue(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        $plant = DB::table('milk_plants')->where('id', $request->memberCode)->first();
        if (!$plant) {
            return ['error' => true, 'msg' => 'PLANT_NOT_FOUND'];
        }

        $dairyInfo = DB::table('dairy_info')->where('id', $dairyId)->first();
        if (!$dairyInfo) {
            return ['error' => true, 'msg' => 'DAIRY_NOT_FOUND'];
        }

        // fat <= 5 → cow, else buffalo
        if ($request->fat <= 5) {
            $milkType     = 'cow';
            $rateCardId   = $dairyInfo->plantRateCardIdForCow;
            $rateCardType = $dairyInfo->plantRateCardTypeForCow;
        } else {
            $milkType     = 'buffalo';
            $rateCardId   = $dairyInfo->plantRateCardIdForBuffalo;
            $rateCardType = $dairyInfo->plantRateCardTypeForBuffalo;
        }

        if (empty($rateCardId)) {
            return [
                'error'        => true,
                'msg'          => 'PLANT_RATECARD_NOT_APPLIED',
                'rateCardType' => $rateCardType ?? null,
                'milkType'     => $milkType,
            ];
        }

        $ratecardshort = DB::table('plant_ratecardshort')->where('id', $rateCardId)->first();
        if (!$ratecardshort) {
            return [
                'error'        => true,
                'msg'          => 'PLANT_RATECARD_NOT_FOUND_404',
                'rateCardType' => $rateCardType,
                'milkType'     => $milkType,
            ];
        }

        if ($ratecardshort->rateCardType === 'fat') {
            if ($ratecardshort->minFat > $request->fat || $ratecardshort->maxFat < $request->fat) {
                return [
                    'error'        => true,
                    'rateCardType' => $rateCardType,
                    'milkType'     => $milkType,
                    'msg'          => 'Please enter correct values, <br/> '
                                   . 'Min Fat: <b>' . $ratecardshort->minFat . '</b> &nbsp;&nbsp; '
                                   . 'Max Fat: <b>' . $ratecardshort->maxFat . '</b><br/>',
                ];
            }
        } else {
            if ($ratecardshort->minFat > $request->fat || $ratecardshort->maxFat < $request->fat
                || $ratecardshort->minSnf > $request->snf || $ratecardshort->maxSnf < $request->snf) {
                return [
                    'error'        => true,
                    'rateCardType' => $rateCardType,
                    'milkType'     => $milkType,
                    'msg'          => 'Please enter correct values, <br/> '
                                   . 'Min Fat: <b>' . $ratecardshort->minFat . '</b> &nbsp;&nbsp; '
                                   . 'Max Fat: <b>' . $ratecardshort->maxFat . '</b><br/> '
                                   . 'Min SNF: <b>' . $ratecardshort->minSnf . '</b> &nbsp;&nbsp; '
                                   . 'Max SNF: <b>' . $ratecardshort->maxSnf . '</b>',
                ];
            }
        }

        $cell = DB::table('plant_fat_snf_ratecard')
            ->where('dairyId',         $dairyId)
            ->where('rateCardShortId', $rateCardId)
            ->where('fatRange',        $request->fat)
            ->where('snfRange',        $request->snf)
            ->first();

        if (!$cell) {
            return [
                'error'        => true,
                'msg'          => 'PLANT_RATECARD_HAS_NO_VALUE',
                'rateCardType' => $rateCardType,
                'milkType'     => $milkType,
            ];
        }

        return [
            'error'        => false,
            'amount'       => $cell->amount,
            'rateCardType' => $rateCardType,
            'milkType'     => $milkType,
        ];
    }

    /* ═══════════════════════════════════════════════════════
       ALL ORIGINAL METHODS BELOW — UNCHANGED
       ═══════════════════════════════════════════════════════ */

    public function getProductUnit(Request $req)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;
        $product = DB::table('products')->where('dairyId', $req->dairyId)->where('productCode', $req->productCode)->where('status', "true")->get()->first();
        return ["unit" => $product->productUnit, "rate" => $product->amount, "stock" => $product->productUnit];
    }

    public function productSaleFormSubmit(Request $req)
    {
        $dairyId     = session()->get('loginUserInfo')->dairyId;
        $dairy       = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        $appSettings = DB::table('androidappsetting')->get()->first();

        $validatedData = $req->validate([
            'partyName' => 'required', 'partyType' => 'required', 'sale_type' => 'required',
            'product' => 'required', 'unit' => 'required', 'quantity' => 'required',
            'PricePerUnit' => 'required', 'amount' => 'required', 'remark' => "max:300"
        ]);

        $cData = $req->all();
        for ($i = 0; $i < count($cData['product']); $i++) {
            $data = $req->all();
            unset($data['product']); unset($data['quantity']); unset($data['discount']);
            unset($data['amount']); unset($data['unit']); unset($data['PricePerUnit']);
            $data['product']      = $req->product[$i];
            $data['quantity']     = $req->quantity[$i];
            $data['discount']     = $req->discount[$i];
            $data['amount']       = $req->amount[$i];
            $data['unit']         = $req->unit[$i];
            $data['PricePerUnit'] = $req->PricePerUnit[$i];
            $data['totalProduct'] = count($cData['product']);
            $submitClass          = new sales();
            $submitReturn         = $submitClass->localSaleFormSubmit((object) $data);

            if ($submitReturn && request('sale_type') == "product_sale") {
                $product = DB::table('products')->where('dairyId', $dairyId)->where('productCode', $data['product'])->get()->first();
                $mobile  = null; $name = null;
                if (request("partyType") == "member") {
                    $m = DB::table("member_personal_info")->where(['memberPersonalCode' => request('memberCode'), "status" => "true", "dairyId" => $dairyId])->get()->first();
                    if ($m != null) {
                        $app_data    = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $m->id])->get();
                        $memberToken = ($app_data == null) ? null : $app_data->pluck("token_key");
                        $alerts      = DB::table("member_other_info")->where(["memberId" => $m->id])->get()->first();
                        $mobile      = $m->memberPersonalMobileNumber;
                        $name        = $m->memberPersonalName;
                        $ub          = DB::table("user_current_balance")->where('ledgerId', $m->ledgerId)->get()->first();
                    } else goto SKIPSMS;
                } else goto SKIPSMS;

                if (isset($ub) && $ub) {
                    $bal = ($ub->openingBalanceType == "credit") ? number_format($ub->openingBalance, 2, ".", "") . " CR" : number_format($ub->openingBalance, 2, ".", "") . " DR";
                } else goto SKIPSMS;

                $a = number_format(($data['quantity'] * $product->amount) - $data['discount'], 2, ".", "");
                $f = number_format($a, 2, ".", "");
                $pamount  = request("paidAmount") / $data['totalProduct'];
                $pa       = number_format("0" . $pamount, 2, ".", "");
                $tempName = explode(" ", $name);
                $name     = isset($tempName[1]) ? $tempName[0] . $tempName[1] : $tempName[0];
                if ($mobile == null) goto SKIPSMS;
                if ($alerts->alert_sms != "true") goto SKIPSMS;
                $newLine = "%0A";
                $data    = ["message" => "Dear $name," . $newLine . "Date: " . request('date') . $newLine . "Product: " . $product->productName . $newLine . "Qty: " . $data['quantity'] . $newLine . "Rate: " . $product->amount . $newLine . "Discount: " . $data['discount'] . $newLine . "Paid Amt: " . $pa . $newLine . "Final Amt: " . $f . $newLine . "Current Balance: $bal" . $newLine, "numbers" => $mobile, "messageType" => "productSale"];
                $sms = new \App\Sms();
                $sms->send($data, $dairyId);
            }
            SKIPSMS:
        }

        if (Session::has('noti_message') && Session::has('token_key')) {
            $noti = PushNotification::setService('fcm')->setMessage(['data' => ["message" => Session::get('noti_message')]])->setApiKey($appSettings->server_api_key)->setDevicesToken(Session::get('token_key'))->send()->getFeedback();
            session()->forget('token_key');
            session()->forget('noti_message');
        }

        if ($req->activetab) session()->put('saleActiveTab', $req->activetab);
        if ($req->returnurl) return redirect($req->returnurl);
        return redirect('localSaleForm');
    }

    public function localSaleFormSubmit(Request $req)
    {
        $dairyId     = session()->get('loginUserInfo')->dairyId;
        $dairy       = DB::table("dairy_info")->where("id", $dairyId)->get()->first();
        $appSettings = DB::table('androidappsetting')->get()->first();

        $validatedData = $req->validate([
            'partyName' => 'required', 'partyType' => 'required', 'sale_type' => 'required',
            'product' => 'required', 'unit' => 'required', 'quantity' => 'required',
            'PricePerUnit' => 'required', 'amount' => 'required', 'remark' => "max:300"
        ]);

        $data             = $req->all();
        $data['totalProduct'] = 1;
        $submitClass      = new sales();
        $submitReturn     = $submitClass->localSaleFormSubmit((object) $data);

        if ($submitReturn && request('sale_type') == "product_sale") {
            $product = DB::table('products')->where('dairyId', $dairyId)->where('productCode', $data['product'])->get()->first();
            $mobile  = null; $name = null;
            if (request("partyType") == "member") {
                $m = DB::table("member_personal_info")->where(['memberPersonalCode' => request('memberCode'), "status" => "true", "dairyId" => $dairyId])->get()->first();
                if ($m != null) {
                    $app_data    = DB::table('app_logins')->where(["dairyId" => $dairyId, "userType" => "4", "userId" => $m->id])->get();
                    $memberToken = ($app_data == null) ? null : $app_data->pluck("token_key");
                    $alerts      = DB::table("member_other_info")->where(["memberId" => $m->id])->get()->first();
                    $mobile      = $m->memberPersonalMobileNumber;
                    $name        = $m->memberPersonalName;
                    $ub          = DB::table("user_current_balance")->where('ledgerId', $m->ledgerId)->get()->first();
                } else goto SKIPSMS;
            } else goto SKIPSMS;

            if (isset($ub) && $ub) {
                $bal = ($ub->openingBalanceType == "credit") ? number_format($ub->openingBalance, 2, ".", "") . " CR" : number_format($ub->openingBalance, 2, ".", "") . " DR";
            } else goto SKIPSMS;

            $a = number_format(($data['quantity'] * $product->amount) - $data['discount'], 2, ".", "");
            $f = number_format($a, 2, ".", "");
            $pamount  = request("paidAmount") / $data['totalProduct'];
            $pa       = number_format("0" . $pamount, 2, ".", "");
            $tempName = explode(" ", $name);
            $name     = isset($tempName[1]) ? $tempName[0] . $tempName[1] : $tempName[0];
            if ($mobile == null) goto SKIPSMS;
            if ($alerts->alert_sms != "true") goto SKIPSMS;
            $newLine = "%0A";
            $data    = ["message" => "Dear $name," . $newLine . "Date: " . request('date') . $newLine . "Product: " . $product->productName . $newLine . "Qty: " . $data['quantity'] . $newLine . "Rate: " . $product->amount . $newLine . "Discount: " . $data['discount'] . $newLine . "Paid Amt: " . $pa . $newLine . "Final Amt: " . $f . $newLine . "Current Balance: $bal" . $newLine, "numbers" => $mobile, "messageType" => "productSale"];
            $sms = new \App\Sms();
            $sms->send($data, $dairyId);
        }
        SKIPSMS:

        if (Session::has('noti_message') && Session::has('token_key')) {
            $noti = PushNotification::setService('fcm')->setMessage(['data' => ["message" => Session::get('noti_message')]])->setApiKey($appSettings->server_api_key)->setDevicesToken(Session::get('token_key'))->send()->getFeedback();
            session()->forget('token_key');
            session()->forget('noti_message');
        }

        if ($req->activetab) session()->put('saleActiveTab', $req->activetab);
        if ($req->returnurl) return redirect($req->returnurl);
        return redirect('localSaleForm');
    }

    /* member Sale Form */
    public function memberSaleForm(Request $request)
    {
        $dairyId            = session()->get('loginUserInfo')->dairyId;
        $customers          = DB::table('customer')->where('dairyId', $dairyId)->where('status', "true")->get();
        $memberPersonalInfo = DB::table('member_personal_info')->where('dairyId', $dairyId)->where('status', "true")->get();
        $product            = DB::table('products')->where('dairyId', $dairyId)->where('status', "true")->orderby("productName")->get();
        $noproduct          = (count($product) == 0);
        $currnetData        = array($customers, "", $memberPersonalInfo, $product);
        return view('memberSale', ["currnetData" => $currnetData, "noproduct" => $noproduct, "activepage" => "memberSale"]);
    }

    public function memberSaleFormSubmit(Request $request)
    {
        $validatedData = $request->validate([
            'memberCode' => 'required', 'memberName' => 'required', 'date' => 'required',
            'product' => 'required', 'unit' => 'required', 'quantity' => 'required',
            'PricePerUnit' => 'required', 'amount' => 'required',
        ]);

        $submitClass  = new sales();
        $submitReturn = $submitClass->memberSaleFormSubmit($request);
        $submitClass  = new sales();
        $submitReturn = $submitClass->localSaleFormSubmit($request);

        $customers          = DB::table('customer')->where('dairyId', $request->dairyId)->where('status', "true")->get();
        $milkPlants         = DB::table('milk_plants')->where('dairyId', $request->dairyId)->where('status', "true")->get();
        $memberPersonalInfo = DB::table('member_personal_info')->where('dairyId', $request->dairyId)->where('status', "true")->get();
        $currnetData        = array($customers, $milkPlants, $memberPersonalInfo);
        return view('localSale', ['currnetData' => $currnetData]);
    }

    public function getUserNameByledger(Request $request)
    {
        if ($request->mainValue == "Customer") {
            $customer = DB::table('customer')->where('ledgerId', $request->ledgerId)->get();
            return (empty($customer[0])) ? "false" : $customer[0]->customerName;
        } elseif ($request->mainValue == "Milk Plants") {
            $customer = DB::table('milk_plants')->where('ledgerId', $request->ledgerId)->get();
            return (empty($customer[0])) ? "false" : $customer[0]->plantName;
        } elseif ($request->mainValue == "Member") {
            $customer = DB::table('member_personal_info')->where('ledgerId', $request->ledgerId)->get();
            return (empty($customer[0])) ? "false" : $customer[0]->memberPersonalName;
        } elseif ($request->mainValue == "Supplier") {
            $customer = DB::table('suppliers')->where('ledgerId', $request->ledgerId)->get();
            return (empty($customer[0])) ? "false" : $customer[0]->supplierFirmName;
        } else {
            return "false";
        }
    }

    public function getLedgerIdByName(Request $req)
    {
        if ($req->type == "customer") {
            $customer = DB::table('customer')->where('customerCode', $req->userCode)->where('status', "true")->get()->first();
            return (!empty($customer)) ? ["error" => false, "ledgerId" => $customer->ledgerId, "name" => $customer->customerName] : ["error" => true, "msg" => "Customer Not Found"];
        } elseif ($req->type == "milk_plant") {
            $customer = DB::table('milk_plants')->where('id', $req->userCode)->get()->first();
            return (empty($customer)) ? ["error" => true, "msg" => "Milk Plant Not Found"] : ["error" => false, "ledgerId" => $customer->ledgerId, "name" => $customer->plantName];
        } elseif ($req->type == "member") {
            $customer = DB::table('member_personal_info')->where('memberPersonalCode', $req->userCode)->where('status', "true")->get()->first();
            return (empty($customer)) ? ["error" => true, "msg" => "Member Not Found"] : ["error" => false, "ledgerId" => $customer->ledgerId, "name" => $customer->memberPersonalName];
        } elseif ($req->type == "supplier") {
            $customer = DB::table('suppliers')->where('supplierCode', $req->userCode)->where('status', "true")->get()->first();
            return (empty($customer)) ? ["error" => true, "msg" => "Supplier Not Found"] : ["error" => false, "ledgerId" => $customer->ledgerId, "name" => $customer->supplierPersonName];
        } else {
            return ["error" => true, "msg" => "Bad Request."];
        }
    }

    public function getSaleDetails()
    {
        $dairyId  = session()->get('loginUserInfo')->dairyId;
        $dairyInfo = DB::table('dairy_info')->where(["id" => $dairyId])->get()->first();
        $sale     = DB::table("sales")->where(["id" => request('id'), "dairyId" => $dairyId, "status" => "true"])->get()->first();
        if ($sale == null) return ["error" => true, "msg" => "Record not found."];
        if ($sale->saleType == "local_sale") {
            $cont = view('localSaleEditModel', ["sale" => $sale, "dairyInfo" => $dairyInfo]);
        } elseif ($sale->saleType == "product_sale") {
            $prods = DB::table("products")->where(["dairyId" => $dairyId, "status" => "true"])->get();
            $cont  = view('memberSaleEditModel', ["sale" => $sale, "prods" => $prods, "dairyInfo" => $dairyInfo]);
        } else {
            return ["error" => true, "msg" => "Record not found"];
        }
        return ["error" => false, "data" => (string) $cont];
    }

    public function localSaleEditSubmitAj()
    {
        $validatedData = request()->validate([
            'saleId' => "required", 'partyName' => 'required', 'partyType' => 'required',
            'sale_type' => 'required', 'product' => 'required', 'unit' => 'required',
            'quantity' => 'required', 'PricePerUnit' => 'required', 'amount' => 'required', 'remark' => 'max:300',
        ]);
        $submitClass  = new sales();
        $submitReturn = $submitClass->localSaleEditClearOldEntry(request());
        if (!$submitReturn["error"]) {
            $insertEdited = $submitClass->localSaleFormSubmit(request());
            return ["error" => !$insertEdited, "msg" => Session::get("msg")];
        } else {
            return $submitReturn;
        }
    }

    public function deleteSaleAjax()
    {
        $validatedData = request()->validate(['saleId' => "required"]);
        if (request("saleType") == "local_sale") {
            $sales        = new sales();
            $submitReturn = $sales->localSaleEditClearOldEntry(request());
            return $submitReturn;
        } elseif (request("saleType") == "product_sale") {
            $sales        = new sales();
            $submitReturn = $sales->productSaleEditClearOldEntry(request());
            return $submitReturn;
        } else {
            return ["error" => true, "msg" => "Error in Sale type."];
        }
    }

    public function productSaleEditSubmitAj()
    {
        $validatedData = request()->validate([
            'saleId' => "required", 'partyName' => 'required', 'partyType' => 'required',
            'sale_type' => 'required', 'product' => 'required', 'unit' => 'required',
            'quantity' => 'required', 'PricePerUnit' => 'required', 'amount' => 'required', 'remark' => 'max:300',
        ]);
        $submitClass  = new sales();
        $submitReturn = $submitClass->productSaleEditClearOldEntry(request());
        if (!$submitReturn["error"]) {
            $insertEdited = $submitClass->localSaleFormSubmit(request());
            return ["error" => !$insertEdited, "msg" => Session::get("msg")];
        } else {
            return $submitReturn;
        }
    }
}