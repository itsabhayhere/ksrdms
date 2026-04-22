<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;
use App;

/**
 * PlantRateCardController
 * ─────────────────────────────────────────────────────────────────────────────
 * RateCardController ka EXACT parallel — Plant Sales ke liye.
 *
 * Table mapping:
 *   plant_ratecardshort    ↔  ratecardshort
 *   plant_rangelist        ↔  rangelist
 *   plant_fat_snf_ratecard ↔  fat_snf_ratecard
 *
 * Key difference:
 *   - Applied card: dairy_info.plantRateCardIdForCow / plantRateCardIdForBuffalo
 *   - Per-dairy (not per collection manager)
 *   - memberCode = milk_plants.id
 * ─────────────────────────────────────────────────────────────────────────────
 */
class PlantRateCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('Auth');
    }

    /* ═══════════════════════════════════════════════════════════════════════
       LIST — rateCardShowNew ke exact parallel
       GET /plantRateCardShow
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardShow()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        // rateCardShowNew joins other_users; yahan dairy_info directly use karein
        $rateCards = DB::table('plant_ratecardshort')
            ->where('dairyId', $dairyId)
            ->orderBy('created_at', 'desc')
            ->get();

        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->first();

        // rateCardShowNew jaisi warning
        if (!$dairyInfo->plantRateCardIdForCow || !$dairyInfo->plantRateCardIdForBuffalo) {
            Session::flash('msg', 'Plant rate card cow aur/ya buffalo ke liye apply nahi ki gayi.');
            Session::flash('alert-class', 'alert-info');
        }

        return view('plantRateCardList', [
            'rateCardShort' => $rateCards,
            'dairyInfo'     => $dairyInfo,
            'activepage'    => 'plantRateCard',
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       NEW FORM — rateCardNew ke exact parallel
       GET /plantRateCardNew
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardNew()
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        // rateCardNew mein $colman check hota hai — yahan dairy-wide, so sirf count
        $totalRateCards = DB::table('plant_ratecardshort')
            ->where('dairyId', $dairyId)
            ->count();

        if ($totalRateCards >= 4) {
            Session::flash('msg', 'Sirf 4 Plant Rate Cards add kar sakte hain. Ek delete karein pehle.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('plantRateCardShow');
        }

        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->first();

        return view('plantRateCardNew', [
            'dairyInfo'  => $dairyInfo,
            'activepage' => 'plantRateCard',
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       SAVE NEW — saveRateCardNew ka EXACT parallel
       POST /plantRateCardSave  (JSON body)

       saveRateCardNew mein "all" collection manager loop hota hai.
       Plant mein dairy-wide ek hi card, isliye loop nahi — directly insert.
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardSave(Request $request)
    {
        $res = [];
        $res['st'] = microtime(true);
        $res['error'] = true;
        $res['msg'] = '';

        $dairyId = session()->get('loginUserInfo')->dairyId;

        if (!$dairyId) {
            $res['msg'] = "<a href='dairy-login'>Pehle login karein!</a>";
            return response()->json($res);
        }

        $body = (object) json_decode($request->getContent(), true);

        if (empty($body->data)) {
            $res['msg'] = 'Kuch galat hua, dobara koshish karein.';
            return response()->json($res);
        }

        DB::beginTransaction();

        // ── Header row (ratecardshort ke barabar) ──────────────────────
        $shortId = DB::table('plant_ratecardshort')->insertGetId([
            'dairyId'      => $dairyId,
            'rateCardType' => $body->rateType,
            'minFat'       => $body->range['minFat'],
            'maxFat'       => $body->range['maxFat'],
            'minSnf'       => $body->range['minSnf'] ?? null,
            'maxSnf'       => $body->range['maxSnf'] ?? null,
            'description'  => $body->description ?? null,
            'created_at'   => date('Y-m-d H:i:s', time()),
        ]);

        if (!$shortId) goto ERROR;

        // ── Range bands (rangelist ke barabar) ────────────────────────
        $rangeId = [];
        foreach ($body->rangeList as $key => $rlist) {
            foreach ($rlist['snfRanges'] as $sKey => $slist) {
                $rid = DB::table('plant_rangelist')->insertGetId([
                    'dairyId'    => $dairyId,
                    'rateCardId' => $shortId,
                    'mnFat'      => $rlist['mnFat'],
                    'mxFat'      => $rlist['mxFat'],
                    'mnSnf'      => $slist['mnSnf'] ?? null,
                    'mxSnf'      => $slist['mxSnf'] ?? null,
                    'rDecFat'    => $rlist['rInFat'],
                    'rDecSnf'    => $slist['rInSnf'] ?? null,
                    'rIncFat'    => $rlist['rInFat'],
                    'rIncSnf'    => $slist['rInSnf'] ?? null,
                    'rAvgFatSnf' => $slist['rFatSnf'] ?? null,
                    'avgFat'     => $rlist['rFat'] ?? null,
                    'created_at' => date('Y-m-d H:i:s', time()),
                ]);

                if (!$rid) goto ERROR;
                $rangeId[$key . ' ' . $sKey] = $rid;
            }
        }

        // ── Cell rows (fat_snf_ratecard ke barabar) ───────────────────
        $insert = [];
        foreach ($body->data as $data) {
            $updatedAt = ($data['isUpdated'] === 'true') ? date('Y-m-d H:i:s', time()) : null;

            $insert[] = [
                'fatRange'        => $data['f'],
                'snfRange'        => $data['s'] ?? null,
                'amount'          => $data['rate'],
                'dairyId'         => $dairyId,
                'rateCardShortId' => $shortId,
                'rangeListId'     => $rangeId[$data['rangeListKey']],
                'created_at'      => date('Y-m-d H:i:s', time()),
                'updated_at'      => $updatedAt,
            ];
        }

        if (!empty($insert)) {
            $saved = DB::table('plant_fat_snf_ratecard')->insert($insert);
            if (!$saved) goto ERROR;
        } else {
            goto ERROR;
        }

        DB::commit();
        $res['error'] = false;
        $res['msg']   = 'Plant rate card safaltapoorvak save ho gayi.';
        $res['end']   = microtime(true);
        return response()->json($res);

        ERROR:
        DB::rollBack();
        $res['error'] = true;
        $res['msg']   = 'Ek error aa gayi.';
        $res['end']   = microtime(true);
        return response()->json($res);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       GET LIST MODAL — getRateCardList ke exact parallel
       POST /plantRateCardGetList
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardGetList(Request $request)
    {
        if (!$request->shortid) {
            return response()->json(['error' => true, 'msg' => 'Kuch error aa gayi.']);
        }

        $rangeList = DB::table('plant_rangelist')
            ->where('rateCardId', $request->shortid)
            ->get();

        $shortCard = DB::table('plant_ratecardshort')
            ->where('id', $request->shortid)
            ->first();

        $rateCard = DB::table('plant_fat_snf_ratecard')
            ->where('rateCardShortId', $request->shortid)
            ->orderBy('fatRange')
            ->orderBy('snfRange')
            ->get();

        $cardFor = '';
        if ($shortCard->rateCardFor === 'buffalo') $cardFor = 'buff';
        elseif ($shortCard->rateCardFor === 'cow')  $cardFor = 'cow';
        elseif ($shortCard->rateCardFor === 'both') $cardFor = 'both';

        return view('plantRateCardModal', [
            'rateCard'  => $rateCard,
            'shortCard' => $shortCard,
            'rangeList' => $rangeList,
            'cardFor'   => $cardFor,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       UPDATE CELLS — updateRateCardNew ke exact parallel
       POST /plantRateCardUpdate
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardUpdate(Request $request)
    {
        $res = ['error' => true, 'msg' => ''];
        $dairyId = session()->get('loginUserInfo')->dairyId;

        if (!$dairyId) {
            $res['msg'] = "<a href='dairy-login'>Pehle login karein!</a>";
            return response()->json($res);
        }

        if (empty($request->data)) {
            $res['msg'] = 'Kuch galat hua, dobara koshish karein.';
            return response()->json($res);
        }

        $rateCardShortId = $request->rateCardShortId;
        $updatedAt = date('Y-m-d H:i:s', time());
        $count = 0;
        $r = null;

        foreach ($request->data as $data) {
            $r = DB::table('plant_fat_snf_ratecard')
                ->where('id', $data['rcid'])
                ->update([
                    'amount'     => $data['rate'],
                    'updated_at' => $updatedAt,
                ]);
            if ($r) $count++;
        }

        $r2 = DB::table('plant_ratecardshort')
            ->where('id', $rateCardShortId)
            ->update(['updated_at' => $updatedAt]);

        $res['error'] = false;
        $res['msg']   = 'Plant rate card safaltapoorvak update ho gayi.';
        $res['count'] = $count;
        $res['r']     = $r;
        $res['r2']    = $r2;

        return response()->json($res);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       DELETE — deleteRateCard ke exact parallel
       POST /plantRateCardDelete
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardDelete(Request $request)
    {
        if (!$request->shortid) {
            return response()->json(['error' => true, 'msg' => 'Kuch error aa gayi.']);
        }

        DB::table('plant_fat_snf_ratecard')->where('rateCardShortId', $request->shortid)->delete();
        DB::table('plant_rangelist')->where('rateCardId', $request->shortid)->delete();
        DB::table('plant_ratecardshort')->where('id', $request->shortid)->delete();

        return response()->json(['error' => false, 'msg' => 'Plant rate card delete ho gayi.']);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       APPLY — applyRatecard ke exact parallel
       GET /plantRateCardApply
       
       applyRatecard: other_users table update karta hai (per colman)
       Plant version: dairy_info table update karta hai (dairy-wide)
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardApply(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        if (!$request->shortCardId || !$request->rateCardType || !$request->type) {
            Session::flash('msg', 'Kuch error aa gayi.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('plantRateCardShow');
        }

        if (!$dairyId) {
            Session::flash('msg', "<a href='dairy-login'>Pehle login karein!</a>.");
            Session::flash('alert-class', 'alert-danger');
            return redirect('dairy-login');
        }

        $shortCard = DB::table('plant_ratecardshort')
            ->where('id', $request->shortCardId)
            ->first();

        $rfor = $shortCard->rateCardFor;

        $dt = ['updated_at' => date('Y-m-d H:i:s', time())];

        // applyRatecard ke exact ternary logic (cow/buffalo/both)
        if ($request->type === 'cow') {
            $rfor == 'buffalo' ? $rfor == 'both' : $rfor = 'cow';
            $dt['plantRateCardIdForCow']   = $request->shortCardId;
            $dt['plantRateCardTypeForCow'] = $request->rateCardType;
        } elseif ($request->type === 'buffalo') {
            $rfor == 'cow' ? $rfor == 'both' : $rfor = 'buffalo';
            $dt['plantRateCardIdForBuffalo']   = $request->shortCardId;
            $dt['plantRateCardTypeForBuffalo'] = $request->rateCardType;
        } else {
            // both
            $rfor = 'both';
            $dt['plantRateCardIdForCow']       = $request->shortCardId;
            $dt['plantRateCardIdForBuffalo']   = $request->shortCardId;
            $dt['plantRateCardTypeForBuffalo'] = $request->rateCardType;
            $dt['plantRateCardTypeForCow']     = $request->rateCardType;
        }

        // applyRatecard: other_users update → Plant: dairy_info update
        $resp = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->update($dt);

        DB::table('plant_ratecardshort')
            ->where('id', $request->shortCardId)
            ->update(['rateCardFor' => $rfor]);

        if ($resp) {
            Session::flash('msg', 'Plant rate card safaltapoorvak apply ho gayi.');
            Session::flash('alert-class', 'alert-success');
        } else {
            Session::flash('msg', 'Kuch error aa gayi.');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('plantRateCardShow');
    }

    /* ═══════════════════════════════════════════════════════════════════════
       RATE LOOKUP — fatSnfRateCardvalue ka exact parallel
       POST /plantSaleRateCardValue

       fatSnfRateCardvalue:
         - colMan session se other_users lookup
         - rateCardIdForCow / rateCardIdForBuffalo from other_users
         - fat_snf_ratecard query

       Plant version:
         - dairy_info se directly plantRateCardIdForCow / plantRateCardIdForBuffalo
         - milk_plants confirm
         - plant_fat_snf_ratecard query
         - Same JSON response shape (JS reuse ke liye)
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantSaleRateCardValue(Request $request)
    {
        $dairyId = session()->get('loginUserInfo')->dairyId;

        // fatSnfRateCardvalue: member_personal_info lookup
        // Plant: milk_plants lookup
        $plant = DB::table('milk_plants')
            ->where('id', $request->memberCode)
            ->first();

        if (!$plant) {
            return ['error' => true, 'msg' => 'MEMBER_ID_NULL'];  // same key as original
        }

        // fatSnfRateCardvalue: other_users se rateCardId
        // Plant: dairy_info se plantRateCardId
        $dairyInfo = DB::table('dairy_info')
            ->where('id', $dairyId)
            ->first();

        if (!$dairyInfo) {
            return ['error' => true, 'msg' => 'DAIRY_NOT_FOUND'];
        }

        // fatSnfRateCardvalue: fat<=5 → cow (same logic)
        if ($request->fat <= 5) {
            $milkType     = 'cow';
            $rateCardId   = $dairyInfo->plantRateCardIdForCow;
            $rateCardType = $dairyInfo->plantRateCardTypeForCow;
        } else {
            $milkType     = 'buffalo';
            $rateCardId   = $dairyInfo->plantRateCardIdForBuffalo;
            $rateCardType = $dairyInfo->plantRateCardTypeForBuffalo;
        }

        if (!$rateCardId) {
            return ['error' => true, 'msg' => 'RATECARD_NOT_FOUND'];  // same key
        }

        $ratecardshort = DB::table('plant_ratecardshort')
            ->where('id', $rateCardId)
            ->first();

        if (!$ratecardshort) {
            return ['error' => true, 'msg' => 'RATECARD_NOT_FOUND_404'];  // same key
        }

        // fatSnfRateCardvalue ke exact validation logic
        if ($ratecardshort->rateCardType === 'fat') {
            if ($ratecardshort->minFat > $request->fat || $ratecardshort->maxFat < $request->fat) {
                return [
                    'error'        => true,
                    'rateCardType' => $rateCardType,
                    'milkType'     => $milkType,
                    'msg'          => 'Please enter correct values, <br/> Min Fat: <b>' . $ratecardshort->minFat
                                   . '</b> &nbsp;&nbsp; Max Fat: <b>' . $ratecardshort->maxFat . '</b><br/>',
                ];
            }
        } else {
            if ($ratecardshort->minFat > $request->fat || $ratecardshort->maxFat < $request->fat
                || $ratecardshort->minSnf > $request->snf || $ratecardshort->maxSnf < $request->snf) {
                return [
                    'error'        => true,
                    'rateCardType' => $rateCardType,
                    'milkType'     => $milkType,
                    'msg'          => 'Please enter correct values, <br/> Min Fat: <b>' . $ratecardshort->minFat
                                   . '</b> &nbsp;&nbsp; Max Fat: <b>' . $ratecardshort->maxFat . '</b><br/>'
                                   . ' Min SNF: <b>' . $ratecardshort->minSnf
                                   . '</b> &nbsp;&nbsp; Max SNF: <b>' . $ratecardshort->maxSnf . '</b>',
                ];
            }
        }

        // fatSnfRateCardvalue: fat_snf_ratecard → plant: plant_fat_snf_ratecard
        $fatSnfRatecard = DB::table('plant_fat_snf_ratecard')
            ->where('dairyId',         $dairyId)
            ->where('rateCardShortId', $rateCardId)
            ->where('fatRange',        $request->fat)
            ->where('snfRange',        $request->snf)
            ->first();

        if (!$fatSnfRatecard) {
            return [
                'error'        => true,
                'msg'          => 'RATECARD_HAS_NO_VALUE',  // same key
                'rateCardType' => $rateCardType,
                'milkType'     => $milkType,
            ];
        }

        // Exact same success response shape as fatSnfRateCardvalue
        return [
            'error'        => false,
            'amount'       => $fatSnfRatecard->amount,
            'rateCardType' => $rateCardType,
            'milkType'     => $milkType,
        ];
    }

    /* ═══════════════════════════════════════════════════════════════════════
       PDF — getFatSnfRangeDataPdf ke exact parallel
       POST /plantRateCardPdf
    ═══════════════════════════════════════════════════════════════════════ */
    public function plantRateCardPdf(Request $request)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($request->rateCardTable);
        return $pdf->download('Plant Rate Card.pdf');
    }
}