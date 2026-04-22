@extends('theme.default')
@section('content')

<style>
    .m-0 { margin-top: 0; }
    #dateShiftPopup { min-height: 420px; }
</style>

<div class="span-fixed response-alert" id="response-alert" style="display:none;"></div>

{{-- ══════════════════════════════════════════
     POPUP 1 — Date & Shift selector
     ══════════════════════════════════════════ --}}
<div class="wmodel clearfix" id="dateShiftPopup">
    <div class="wmodelheader"></div>
    <div class="close">X</div>
    <div class="wmodel-body">
        <h3 class="text-center">Select Date &amp; Shift</h3>
        <hr>
        <div class="col-sm-12">
            <div class="col-sm-6">
                <label>Date</label>
                <input type="text" class="form-control" id="sdate"
                       value="{{ date('d-m-Y', strtotime($date)) }}"
                       placeholder="Date" autofocus autocomplete="off">
            </div>
            <div class="col-sm-6">
                <label>Shift</label>
                <select id="sdailyShift" class="selectpicker form-control" name="dailyShift">
                    <option value="morning" @if($curShift=='morning') selected @endif>Morning Shift</option>
                    <option value="evening" @if($curShift=='evening') selected @endif>Evening Shift</option>
                </select>
            </div>
        </div>
        <div class="col-sm-12 text-center pt-20">
            <a href="#" class="btn btn-primary" onclick="fetch_sales_by_date(this)">Fetch Sales</a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     POPUP 2 — Duplicate sale warning
     ══════════════════════════════════════════ --}}
<div class="wmodel clearfix" id="sameSaleModel" style="width:70%;">
    <div class="close">X</div>
    <div class="wmodel-body"></div>
</div>

{{-- ══════════════════════════════════════════
     POPUP 3 — Edit sale modal
     ══════════════════════════════════════════ --}}
<div class="wmodel clearfix" id="editSaleModel" style="width:80%;">
    <div class="wmodelheader"></div>
    <div class="close">X</div>
    <div class="wmodel-body">
        <div class="p-5-20">
            <h4>Edit Sale — Plant: <b><span id="editPlantName"></span></b></h4>
            <hr>
            <form id="editSaleForm" method="post" action="{{ url('/updatePlantSale') }}" class="clearfix">
                {{ csrf_field() }}
                <input type="hidden" id="saleId3"       name="saleId">
                <input type="hidden" id="plantCode3"    name="plantCode">
                <input type="hidden" id="dailyShift3"   name="dailyShift">
                <input type="hidden" id="date3"         name="date">
                <input type="hidden" id="rateCardType3" name="rateCardType">
                <input type="hidden" id="milkTypeHid3"  name="milkType">

                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label>Quantity (Ltr)</label>
                        <input type="text" class="form-control" id="quantity3" name="quantity"
                               placeholder="Quantity" tabindex="12" autocomplete="off"
                               onchange="callEditValues()">
                    </div>
                    <div class="col-sm-3" id="rateForRateCardFat3">
                        <label>Fat</label>
                        <input type="number" class="form-control" id="fatValue3" name="fat"
                               placeholder="Fat" step="0.05" tabindex="13" autocomplete="off"
                               onchange="callEditValues()">
                    </div>
                    <div class="col-sm-3 dnone" id="rateForRateCardSnf3">
                        <label>SNF</label>
                        <input type="number" class="form-control" id="snfValue3" name="snf"
                               placeholder="SNF" step="0.1" tabindex="14" autocomplete="off"
                               onchange="callEditValues()">
                    </div>
                    <div class="col-sm-3">
                        <label>Paid Amount</label>
                        <input type="text" class="form-control" id="paidAmount3" name="paidAmount"
                               value="0" tabindex="15">
                    </div>
                </div>

                <div class="col-sm-12 pt-10">
                    <div class="col-sm-3">
                        <label>Milk Type: <b><input type="text" class="noinput" id="milkTypeDisp3" readonly size="10"></b></label>
                    </div>
                    <div class="col-sm-3">
                        <label>Rate (₹): <b><input type="text" class="noinput" id="price3" name="price" readonly size="8"></b></label>
                    </div>
                    <div class="col-sm-3">
                        <label>Amount</label>
                        <input type="text" class="form-control" id="amount3" name="amount" readonly tabindex="16">
                    </div>
                </div>

                <div class="col-sm-12 text-center pt-20">
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn" tabindex="17">Update Sale</button>
                    &nbsp;
                    <a href="#" class="btn btn-default" onclick="closeEditModal(event)" tabindex="18">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MAIN PAGE
     ══════════════════════════════════════════ --}}
<div class="pageblur">
    <div class="clearfix">
        <div class="fcard margin-fcard-1 pt-0 clearfix">

            {{-- Page heading --}}
            <div class="heading clearfix">
                <div class="fl">
                    <h3>Plant Sale</h3>
                    <hr class="m-0">
                </div>
                <div class="fr pt-5">
                    @if($mUtility->isActive || $wUtility->isActive)
                        <small class="text-info">
                            <i class="fa fa-plug"></i> Auto-reading:
                            @if($mUtility->isActive) <b>Fat</b> @endif
                            @if($mUtility->isActive && $wUtility->isActive) &amp; @endif
                            @if($wUtility->isActive) <b>Weight</b> @endif
                        </small>
                    @endif
                </div>
            </div>

            {{-- ── Entry form ── --}}
            <form id="plantSaleForm" method="post" action="{{ url('/plantSaleFormSubmit') }}"
                  class="clearfix" autocomplete="off">
                {{ csrf_field() }}
                <input autocomplete="false" name="hidden" type="text" style="display:none;">
                <input type="hidden" id="rateCardType" name="rateCardType" value="">
                <input type="hidden" id="dairyIdHid"   name="dairyId"
                       value="{{ Session::get('loginUserInfo')->dairyId }}">

                {{-- Date / Shift info bar --}}
                <div class="col-md-12 clearfix"
                     style="background:#f6f7f9;padding:10px 15px;border:1px solid #dedede;margin-bottom:10px;">
                    <div class="col-sm-3">
                        <label>Date: <b>
                            <input type="text" class="noinput" id="date" name="date"
                                   value="{{ date('d-m-Y', strtotime($date)) }}" readonly>
                        </b></label>
                    </div>
                    <div class="col-sm-3">
                        <span>Morning: <b id="msc">{{ $msc }}</b> Ltr</span><br>
                        <span>Evening: <b id="esc">{{ $esc }}</b> Ltr</span>
                    </div>
                    <div class="col-sm-3">
                        <label>Shift: <b>
                            <input type="text" class="noinput" id="dailyShift" name="dailyShift"
                                   value="{{ ucfirst($curShift) }}" readonly size="9">
                        </b></label>
                    </div>
                    <div class="col-sm-3">
                        <a href="#" class="btn btn-primary btn-sm" onclick="show_popUp(event)">
                            <i class="fa fa-calendar"></i> Change Date &amp; Shift
                        </a>
                    </div>
                </div>

                {{-- Row 1: Plant / Quantity / Fat / SNF --}}
                <div class="col-sm-12 reset-fields">
                    <div class="pt-5"></div>

                    <div class="col-sm-3">
                        <label>Milk Plant <span class="errmsg" id="plantCodeErr"></span></label>
                        <select id="plantCode" name="plantCode"
                                class="selectpicker form-control"
                                data-live-search="true" title="Select Plant"
                                tabindex="1" onchange="onPlantChange()">
                            @foreach($milkPlants as $plant)
                                <option value="{{ $plant->id }}">{{ $plant->plantName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label>Quantity (Ltr)</label>
                        <input type="text" class="form-control" id="quantity" name="quantity"
                               placeholder="Enter Quantity" tabindex="2" autocomplete="off"
                               onkeydown="qtyKeyDown(event, this)">
                    </div>

                    <div class="col-sm-3" id="rateForRateCardFat">
                        <label>Fat</label>
                        <input type="number" class="form-control" id="fatValue" name="fat"
                               placeholder="Enter Fat" step="0.05" tabindex="3" autocomplete="off"
                               onkeydown="fatKeyDown(event, this)">
                    </div>

                    <div class="col-sm-3 @if($ratecardtype == 'fat') dnone @endif" id="rateForRateCardSnf">
                        <label>SNF</label>
                        <input type="number" class="form-control" id="snfValue" name="snf"
                               placeholder="Enter SNF" step="0.1" tabindex="4" autocomplete="off">
                    </div>
                </div>

                {{-- Row 2: Milk Type / Rate / Amount / Paid --}}
                <div class="col-sm-12 reset-fields">
                    <div class="col-sm-3 pt-25">
                        <label>Milk Type:
                            <b><input type="text" class="noinput" id="milkType" name="milkType"
                                      value="" readonly size="10" placeholder="—"></b>
                        </label>
                    </div>
                    <div class="col-sm-3 pt-25">
                        <label>Rate (₹):
                            <b><input type="text" class="noinput" id="price" name="price"
                                      readonly size="8"></b>
                        </label>
                    </div>
                    <div class="col-sm-3">
                        <label>Total Amount (₹)</label>
                        <input type="text" class="form-control" id="amount" name="amount"
                               readonly placeholder="Auto calculated" tabindex="5">
                    </div>
                    <div class="col-sm-3">
                        <label>Paid Amount (₹)</label>
                        <input type="text" class="form-control rupee" id="paidAmount"
                               name="paidAmount" value="0" tabindex="6">
                    </div>
                </div>

                {{-- Product radio --}}
                <div class="col-sm-12 reset-fields">
                    <div class="col-sm-6 pt-10">
                        <label class="rdolb">
                            <input type="radio" name="product" value="milk" checked> Milk
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="col-sm-12 text-center pt-20 pb-10">
                    <button type="submit" class="btn btn-primary" id="plantSaleSubmitBtn"
                            tabindex="7" disabled>
                        <i class="fa fa-save"></i> Add Plant Sale
                    </button>
                    <img src="{{ asset('images/loading.gif') }}" class="loading-on-btn dnone" alt="">
                </div>
            </form>
        </div>
    </div>

    {{-- AJAX-rendered transaction table --}}
    <div class="clearfix">
        <div class="table-back" id="table-plant-sales">
            <div class="text-center pt-20 text-muted">Loading sales...</div>
        </div>
    </div>
</div>

<div id="utilityRes" class="alert alert-danger dnone" style="position:fixed;bottom:20px;right:20px;z-index:9999;max-width:400px;"></div>

{{-- ══════════════════════════════════════════
     SCRIPTS
     ══════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script>
<script>

/* ─── globals ─── */
var dairyId = "{{ Session::get('loginUserInfo')->dairyId }}";
var glbl    = {};
glbl.table               = null;
glbl.fetchValuesReq      = null;
glbl.autoFillMilk        =
glbl.autoFillMilk_ini    = parseInt("{{ $mUtility->isActive }}");
glbl.autoFillWeight      =
glbl.autoFillWeight_ini  = parseInt("{{ $wUtility->isActive }}");
glbl.tmp_oldFatVal       = 0;
glbl.tmp_oldWeightVal    = 0;
glbl.manual_entry_fat    = !glbl.autoFillMilk;
glbl.manual_entry_weight = !glbl.autoFillWeight;

function btn_loading(show){
    show ? $('.loading-on-btn').show() : $('.loading-on-btn').hide();
}

/* ════════════════════════
   DOCUMENT READY
   ════════════════════════ */
$(document).ready(function(){

    $('#sdate').datetimepicker({ format: 'DD-MM-YYYY' });

    if({{ $flag }}){
        show_popUp_direct();
    } else {
        fetch_sales_by_date();
    }

    @if($noPlant)
    $.confirm({
        title: 'No Plant Found', content: 'No milk plant is linked to your account. Please add one first.',
        type: 'orange', typeAnimated: true,
        buttons: { go: { text: 'Add Plant', btnClass:'btn-orange',
            action: function(){ window.location = '{{ url("milkPlantForm") }}'; }
        }}
    });
    @endif

    @if($noRateCard)
    $.confirm({
        title: 'Plant Rate Card Missing',
        content: 'No plant rate card is applied. Please create and apply a plant rate card for Cow &amp; Buffalo.',
        type: 'orange', typeAnimated: true,
        buttons: { go: { text: 'Set Plant Rate Card', btnClass:'btn-orange',
            action: function(){ window.location = '{{ url("plantRateCardNew") }}'; }
        }}
    });
    @endif

    /* ── Main form AJAX submit ── */
    $('#plantSaleForm').on('submit', function(e){
        e.preventDefault();
        if(!this.checkValidity()) return;

        var $btn = $('#plantSaleSubmitBtn');
        $btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            type: $(this).attr('method'),
            url : $(this).attr('action'),
            data: $(this).serialize(),
            success: function(res){
                $btn.attr('disabled', false).html('<i class="fa fa-save"></i> Add Plant Sale');
                if(res.error){
                    $.alert('Error: ' + res.msg);
                } else if(res.duplicate){
                    showDuplicateWarning(res.msg);
                } else {
                    fetch_sales_by_date();
                    resetForm();
                    flashMsg(res.msg, 'success');
                }
            },
            error: function(){
                $btn.attr('disabled', false).html('<i class="fa fa-save"></i> Add Plant Sale');
                $.alert('Server error. Please try again.');
            }
        }).always(function(){
            glbl.manual_entry_fat    = !glbl.autoFillMilk_ini;
            glbl.manual_entry_weight = !glbl.autoFillWeight_ini;
        });
    });

    /* ── Edit form AJAX submit ── */
    $('#editSaleForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            type: $(this).attr('method'),
            url : $(this).attr('action'),
            data: $(this).serialize(),
            success: function(res){
                if(res.error){ $.alert('Error: ' + res.msg); }
                else {
                    closeEditModal_direct();
                    fetch_sales_by_date();
                    flashMsg(res.msg, 'success');
                }
            },
            error: function(){ $.alert('Server error.'); }
        });
    });

    $('#sdate').on('dp.change', function(){
        $('#date').val($(this).val());
        updateFormAction();
    });
    $('#sdailyShift').on('change', function(){
        $('#dailyShift').val(ucfirst($(this).val()));
        updateFormAction();
    });

    $('#fatValue, #snfValue, #quantity').on('input change', function(){
        triggerRateFetch('');
    });

});

/* ════════════════════════
   POPUPS
   ════════════════════════ */
function show_popUp(e){ if(e) e.preventDefault(); show_popUp_direct(); }
function show_popUp_direct(){
    $('#dateShiftPopup').fadeIn();
    $('.pageblur').addClass('blur-3');
}
function hidePopup(){
    $('#dateShiftPopup').fadeOut();
    $('.pageblur').removeClass('blur-3');
}
function closeDuplicateModel(){
    $('#sameSaleModel').fadeOut();
    $('.pageblur').removeClass('blur-3');
}
function closeEditModal(e){ if(e) e.preventDefault(); closeEditModal_direct(); }
function closeEditModal_direct(){
    $('#editSaleModel').fadeOut();
    $('.pageblur').removeClass('blur-3');
}

$('#dateShiftPopup .close').on('click', function(e){
    e.preventDefault();
    $.alert('Please select a date before proceeding.');
});
$('#sameSaleModel .close').on('click', function(){ closeDuplicateModel(); });
$('#editSaleModel .close').on('click', function(){ closeEditModal_direct(); });

/* ════════════════════════
   FETCH SALE LIST (AJAX)
   ════════════════════════ */
function fetch_sales_by_date(){
    loader('show');
    var sdate  = $('#sdate').val();
    var sshift = $('#sdailyShift').val();

    $('#date').val(sdate);
    $('#dailyShift').val(ucfirst(sshift));
    pushUrlState(sdate, sshift);

    $.ajax({
        type: 'POST',
        url : '{{ url("/plantSaleListAjax") }}',
        data: { _token: '{{ csrf_token() }}', dairyId: dairyId, date: sdate, shift: sshift },
        success: function(res){
            loader('hide');
            if(glbl.table){ try{ glbl.table.destroy(); }catch(ex){} }
            $('#table-plant-sales').html(res.content);
            glbl.table = $('#plantSaleTable').DataTable({
                order: [[0, 'desc']],
                columnDefs: [{ targets: [0], visible: false, searchable: false }]
            });
            $('#msc').text(res.msc);
            $('#esc').text(res.esc);
            hidePopup();
            $('#plantCode').focus();
        },
        error: function(){ loader('hide'); }
    });
}

function updateFormAction(){
    var sdate  = $('#sdate').val();
    var sshift = $('#sdailyShift').val();
    $('#plantSaleForm').attr('action',
        '{{ url("/plantSaleFormSubmit") }}?date=' + sdate + '&shift=' + sshift);
    pushUrlState(sdate, sshift);
}
function pushUrlState(sdate, sshift){
    var p = 'date=' + sdate + '&shift=' + sshift;
    window.history.pushState('', '', '{{ url("/plantSaleForm") }}?' + p);
}

/* ════════════════════════
   PLANT SELECT CHANGE
   ════════════════════════ */
function onPlantChange(){
    enableSubmit(false);
    if($('#fatValue').val()) triggerRateFetch('');
}

/* ════════════════════════
   FETCH RATE FROM PLANT RATE CARD
   ════════════════════════ */
function triggerRateFetch(no){
    var isEdit    = (no !== '' && no !== null && no !== undefined);
    var fat       = isEdit ? $('#fatValue'+no).val()  : $('#fatValue').val();
    var snf       = isEdit ? $('#snfValue'+no).val()  : $('#snfValue').val();
    var plantCode = isEdit ? $('#plantCode3').val()    : $('#plantCode').val();
    var rct       = isEdit ? $('#rateCardType'+no).val() : $('#rateCardType').val();

    if(!fat) return;
    if(rct === 'fat/snf' && !snf) return;

    enableSubmit(false, no);
    btn_loading(true);
    if(glbl.fetchValuesReq) glbl.fetchValuesReq.abort();

    glbl.fetchValuesReq = $.ajax({
        type: 'POST',
        url : '{{ url("plantSaleRateCardValue") }}',
        data: { _token: '{{ csrf_token() }}', dairyId: dairyId, memberCode: plantCode, fat: fat, snf: snf },
        success: function(res){
            btn_loading(false);
            applyRateCard(res, no);
            if(res.error){
                $('#response-alert').html('<b>Plant Rate Card:</b> ' + res.msg).fadeIn();
                setTimeout(function(){ $('#response-alert').fadeOut(); }, 7000);
                enableSubmit(false, no);
            } else {
                $('#response-alert').fadeOut('fast');
                var qty    = isEdit ? parseFloat($('#quantity'+no).val()||0) : parseFloat($('#quantity').val()||0);
                var rate   = parseFloat(res.amount||0);
                var amount = (qty * rate).toFixed(2);
                var mt     = parseFloat(fat) > 5 ? 'buffalo' : 'cow';
                if(isEdit){
                    $('#price'+no).val(rate);
                    $('#amount'+no).val(amount);
                    $('#milkTypeDisp'+no).val(ucfirst(mt));
                    $('#milkTypeHid'+no).val(mt);
                } else {
                    $('#price').val(rate);
                    $('#amount').val(amount);
                    $('#milkType').val(mt);
                }
                enableSubmit(true, no);
            }
        },
        error: function(){ btn_loading(false); }
    });
}

function applyRateCard(res, no){
    var isEdit = (no !== '' && no !== null && no !== undefined);
    var rtId   = isEdit ? '#rateCardType' + no : '#rateCardType';
    var fId    = isEdit ? '#rateForRateCardFat' + no : '#rateForRateCardFat';
    var sId    = isEdit ? '#rateForRateCardSnf' + no : '#rateForRateCardSnf';

    if(res.rateCardType){
        $(rtId).val(res.rateCardType);
        if(res.rateCardType === 'fat'){ $(sId).hide(); $(fId).show(); }
        else { $(fId).show(); $(sId).show(); }
    }
    if(res.milkType){
        var mtId = isEdit ? '#milkTypeDisp'+no : '#milkType';
        $(mtId).val(res.milkType);
        if(isEdit) $('#milkTypeHid'+no).val(res.milkType);
    }
}

function enableSubmit(ok, no){
    var isEdit = (no !== '' && no !== null && no !== undefined);
    if(!isEdit){
        var rct    = $('#rateCardType').val();
        var snfOk  = (rct === 'fat/snf') ? !!$('#snfValue').val() : true;
        var ready  = ok && !!$('#fatValue').val() && snfOk && !!$('#quantity').val() && !!$('#plantCode').val() && !!$('#amount').val();
        $('#plantSaleSubmitBtn').attr('disabled', !ready);
    }
}

/* ════════════════════════
   EDIT MODAL
   ════════════════════════ */
function editSale(e, saleId, plantName){
    e.preventDefault();
    loader('show');
    $.ajax({
        type: 'POST',
        url : '{{ url("getPlantSaleValues") }}',
        data: { _token: '{{ csrf_token() }}', saleId: saleId },
        success: function(res){
            loader('hide');
            if(res.error){ $.alert('Error: ' + res.msg); }
            else          { populateEditModal(res.sale, plantName); }
        },
        error: function(){ loader('hide'); $.alert('Server error.'); }
    });
}

function populateEditModal(s, plantName){
    $('#saleId3').val(s.id);
    $('#plantCode3').val(s.partyCode);
    $('#dailyShift3').val(s.shift);
    $('#date3').val(s.saleDate);
    $('#quantity3').val(s.productQuantity);
    $('#fatValue3').val(s.fat);
    $('#snfValue3').val(s.snf);
    $('#price3').val(s.productPricePerUnit);
    $('#amount3').val(s.amount);
    $('#paidAmount3').val(s.paidAmount);
    $('#milkTypeDisp3').val(ucfirst(s.milkType||''));
    $('#milkTypeHid3').val(s.milkType||'');
    $('#rateCardType3').val(s.rateCardType||'fat');
    $('#editPlantName').text(plantName);

    if((s.rateCardType||'fat') === 'fat'){
        $('#rateForRateCardSnf3').hide();
        $('#rateForRateCardFat3').show();
    } else {
        $('#rateForRateCardFat3').show();
        $('#rateForRateCardSnf3').show();
    }

    $('#editSaleModel').fadeIn();
    $('.pageblur').addClass('blur-3');
    $('#quantity3').focus();
}

function callEditValues(){
    var fat = $('#fatValue3').val();
    var rct = $('#rateCardType3').val();
    if(!fat) return;
    if(rct === 'fat/snf' && !$('#snfValue3').val()) return;
    triggerRateFetch(3);
}

/* ════════════════════════
   DELETE
   ════════════════════════ */
function deleteSale(e, saleId){
    e.preventDefault();
    $.confirm({
        title: 'Delete Sale', type: 'red',
        content: 'Are you sure you want to delete this entry?',
        buttons: {
            confirm: { text:'Delete', btnClass:'btn-red',
                action: function(){
                    $.ajax({
                        type: 'POST', url: '{{ url("deletePlantSale") }}',
                        data: { _token: '{{ csrf_token() }}', saleId: saleId },
                        success: function(res){
                            if(!res.error){ fetch_sales_by_date(); flashMsg(res.msg,'success'); }
                            else          { $.alert('Error: '+res.msg); }
                        }
                    });
                }
            },
            cancel: { text:'Cancel' }
        }
    });
}

/* ════════════════════════
   DUPLICATE WARNING
   ════════════════════════ */
function showDuplicateWarning(msg){
    $('#sameSaleModel .wmodel-body').html(
        '<div class="p-20">' +
        '<p><i class="fa fa-exclamation-triangle text-warning"></i> ' + msg + '</p>' +
        '<button class="btn btn-danger btn-sm" onclick="forceSubmit()">Add Anyway</button>&nbsp;' +
        '<button class="btn btn-default btn-sm" onclick="closeDuplicateModel()">Cancel</button>' +
        '</div>'
    );
    $('#sameSaleModel').fadeIn();
    $('.pageblur').addClass('blur-3');
}
function forceSubmit(){
    closeDuplicateModel();
    var f   = $('#plantSaleForm');
    var url = f.attr('action');
    f.attr('action', url + (url.indexOf('?') >= 0 ? '&' : '?') + 'forceSubmit=1');
    f.submit();
}

/* ════════════════════════
   KEYBOARD NAV
   ════════════════════════ */
function qtyKeyDown(e, elm){
    if(e.which === 13){
        if(!glbl.manual_entry_weight){
            e.preventDefault();
            getQtyWeightFromPort(elm);
            return;
        }
    }
    if(e.which === 107 || e.which === 187){
        glbl.manual_entry_weight = true;
        e.preventDefault();
        return;
    }
    if(e.which === 9) return;
    if(!glbl.manual_entry_weight){ e.preventDefault(); }
}

function fatKeyDown(e, elm){
    if(e.which === 13){
        if(!glbl.manual_entry_fat){
            e.preventDefault();
            getMilkFatFromPort(elm);
            return;
        }
    }
    if(e.which === 107 || e.which === 187){
        glbl.manual_entry_fat = true;
        e.preventDefault();
        return;
    }
    if(e.which === 9) return;
    if(!glbl.manual_entry_fat){ e.preventDefault(); }
}

$(document).on('keypress', '#plantSaleForm input', function(e){
    if(e.which === 13){
        e.preventDefault();
        var thisTab = parseInt($(this).attr('tabindex')) || 0;
        $(this).closest('form').find(':input:visible').each(function(){
            var t = parseInt($(this).attr('tabindex')) || 0;
            if(t > thisTab){ $(this).focus(); return false; }
        });
    }
});

/* ════════════════════════
   HARDWARE UTILITY READS
   ════════════════════════ */
function getQtyWeightFromPort(elm){
    if(!glbl.autoFillWeight) return;
    var cfg = {
        portName : "{{ $wUtility->communicationPort }}",
        baudRate : "{{ $wUtility->maxSpeed }}",
        parity   : "{{ $wUtility->connectionPerferenceParity }}",
        dataBits : "{{ $wUtility->connectionPerferenceDataBits }}",
        stopBits : "{{ $wUtility->connectionPerferenceStopBits }}",
        startChars: ['$'], endChars: ['\n','\r'],
        decimal_digit: "{{ $wUtility->decimal_digit }}"
    };
    $(elm).LoadingOverlay('show', {zIndex:1999});
    $.ajax({
        method:'POST', cache:false, url:'http://localhost:9000/com/getweight',
        contentType:'application/json; charset=utf-8',
        data:JSON.stringify(cfg), async:true, processData:false,
        success: function(resp){
            var val = truncateToDecimals(parseFloat(resp), cfg.decimal_digit);
            $('#quantity').val(val);
            if(val !== glbl.tmp_oldWeightVal) $('#quantity').trigger('input');
            glbl.tmp_oldWeightVal = val;
        },
        error: function(err){
            showUtilityError('Weight Utility not running. ' + (err.responseText||''));
        }
    }).always(function(){ $(elm).LoadingOverlay('hide'); });
}

function getMilkFatFromPort(elm){
    if(!glbl.autoFillMilk) return;
    var cfg = {
        portName : "{{ $mUtility->communicationPort }}",
        baudRate : "{{ $mUtility->maxSpeed }}",
        parity   : "{{ $mUtility->connectionPerferenceParity }}",
        dataBits : "{{ $mUtility->connectionPerferenceDataBits }}",
        stopBits : "{{ $mUtility->connectionPerferenceStopBits }}",
        startChars: ['$'], endChars: ['\n','\r']
    };
    $(elm).LoadingOverlay('show', {zIndex:1999});
    $.ajax({
        method:'POST', cache:false, url:'http://localhost:9000/com/getweight',
        contentType:'application/json; charset=utf-8',
        data:JSON.stringify(cfg), async:true, processData:false,
        success: function(resp){
            var val = truncateToDecimals(parseFloat(resp), 1);
            $('#fatValue').val(val);
            if(val !== glbl.tmp_oldFatVal) $('#fatValue').trigger('input');
            glbl.tmp_oldFatVal = val;
        },
        error: function(err){
            showUtilityError('Fat Analyzer not running. ' + (err.responseText||''));
        }
    }).always(function(){ $(elm).LoadingOverlay('hide'); });
}

function showUtilityError(msg){
    $('#utilityRes').html('<i class="fa fa-exclamation-triangle"></i> ' + msg)
        .removeClass('dnone').fadeIn();
    setTimeout(function(){ $('#utilityRes').fadeOut(function(){ $(this).addClass('dnone'); }); }, 10000);
}

function truncateToDecimals(num, dec){
    dec = parseInt(dec) || 1;
    var m = Math.pow(10, dec);
    return Math.trunc(num * m) / m;
}

/* ════════════════════════
   HELPERS
   ════════════════════════ */
function resetForm(){
    $('.reset-fields input:not([readonly])').val('');
    $('#milkType').val('');
    $('#price').val('');
    $('#amount').val('');
    $('#paidAmount').val('0');
    try { $('#plantCode').selectpicker('val', ''); $('#plantCode').selectpicker('refresh'); } catch(e){}
    enableSubmit(false);
    glbl.manual_entry_fat    = !glbl.autoFillMilk_ini;
    glbl.manual_entry_weight = !glbl.autoFillWeight_ini;
}

function flashMsg(msg, type){
    var $el = $('<div class="alert alert-'+type+' alert-dismissible" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:280px;">' + msg + '</div>');
    $('body').append($el);
    setTimeout(function(){ $el.fadeOut(function(){ $el.remove(); }); }, 3000);
}

function ucfirst(s){ return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

</script>
@endsection