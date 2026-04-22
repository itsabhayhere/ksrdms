@extends('theme.default')
@section('content') @php if (session()->has('dailyTransactionDate')) { $date = session()->get('dailyTransactionDate');
$flag = 0;}else{ $date = date("d-m-Y"); $flag = 1;} if (session()->has('dailyTransactionShift')) { $curShift =
session()->get('dailyTransactionShift');
if($flag)$flag=0;} else{ if (date("H", time())
< 12) $curShift="morning" ; else $curShift="evening" ; $flag=1;} @endphp <style type="text/css">
    .m-0 { margin-top: 0; } .errorMessage { color: red; } #dateShiftPopup{ min-height: 420px; }
    </style>

    <div class="span-fixed response-alert" id="response-alert"></div>

    <div class="wmodel clearfix" id="dateShiftPopup">
        <div class="wmodelheader"></div>
        <div class="close">X</div>
        <div class="wmodel-body">

            <h3 class="text-center">Select Date </h3>
            <hr>
            <div class="col-sm-12">
                <div class="col-sm-6">
                    <label>Date</label>
                    <input type="text" class="form-control" id="sdate" placeholder="Date"
                        value="{{date(" d-m-Y ", strtotime($date))}}" name="date" autofocus autocomplete="off">
                </div>
                <div class="col-sm-6">
                    <div id="SetShiftField" class="SetShiftField">
                        <label>Shift</label>
                        <select id="sdailyShift" class="dailyShift selectpicker" name="dailyShift">
                            <option value="morning" @if($curShift=="morning" )selected @endif> Morning Shift </option>
                            <option value="evening" @if($curShift=="evening" )selected @endif> Evening Shift </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 text-center pt-20">
                <a href="#" role="button" class="btn btn-primary" id="date-shift-btn"
                    onclick="fetch_transactions_by_date(this); $('#memberCode').trigger('change')">Fetch
                    Transactions</a>
            </div>

        </div>
    </div>


    <div class="wmodel clearfix" id="sameTransModel" style="width: 75%;">
        <div class="close">X</div>
        <div class="wmodel-body">

        </div>
    </div>


    <div class="pageblur">

        <div class="clearfix">

            <div class="fcard margin-fcard-1 pt-0 clearfix">
                <div class="heading clearfix">
                    <div class="fl">
                        <h3>New Daily Transaction</h3>
                        <hr class="m-0">
                    </div>
                    <div class="fr">
                        {{-- @if($mUtility->isActive != 0 || $wUtility->isActive != 0)
                        <div class="checkbox">
                            <label><input type="checkbox" class="checkbox" value="" id="autometicReadingCheck">Temporary
                                disable autometic reading from scale</label>
                        </div>
                        @endif --}}
                    </div>
                </div>

                <div class="clearfix">
                    <form method="post" action="{{ url('/DailyTransactionSubmit') }}" class="clearfix" id="dailyForm">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="dairyId" name="dairyId"
                            value="{{{ Session::get('loginUserInfo')->dairyId }}}">
                        <input type="hidden" name="status" value="true">
                        <input type="hidden" id="rateCardType" value="">

                        <div class="col-md-12 clearfix"
                            style="background: #f6f7f9;padding: 10px 15px;border: 1px solid #dedede;">
                            <div class="col-sm-3">
                                <label>Date: <b><input type="text" class="noinput" id="date"
                                            value="<?php echo date(" d-m-Y ") ; ?>" name="date" readonly></b></label>
                            </div>
                            <div class="col-sm-3">
                                Morning Shift Collection: <b id="msc">{{$msc}}</b>
                                <br> Evening Shift Collection: <b id="esc">{{$esc}}</b>
                            </div>
                            <div class="col-sm-3">
                                <div id="SetShiftField" class="SetShiftField">
                                    <label>Shift: <b><input type="text" class="noinput" id="dailyShift"
                                                value="{{ucfirst($curShift)}}" name="dailyShift" readonly
                                                size="9"></b></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a href="#" role="button" class="btn btn-primary btn-sm" id=""
                                    onclick="show_popUp(this)">Change date & shift</a>
                            </div>
                        </div>

                        <div class="col-sm-12 reset-fields">
                            <div class="pt-10"></div>
                            <div class="col-sm-3">
                                <label>Member Code</label>
                                <span id="memberCodeErr" class="memberCodeErr errmsg"> </span>
                                <input id="memberCode" class="form-control" required name="memberCode" tabindex="1"
                                    data-name="code" autocomplete="off" placeholder="Member Code">
                                <img style="float:right;" id='loading-input-img' width="80px"
                                    src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif" />
                            </div>

                            <div class="col-sm-3">
                                <label>Quantity</label>
                                <input type="text" required="true" class="form-control" placeholder="Enter Quantity"
                                    id="quantity" name="quantity" tabindex="2" autocomplete="off">
                            </div>


                            <div class="col-sm-3 m-0" id="rateForRateCardFat">
                                <label>Fat</label>
                                <input type="number" class="form-control" placeholder="Enter Fat" id="fatSnf_fatValue"
                                    name="fat" required step="0.05" tabindex="3" autocomplete="off" >
                            </div>
                            <div class="col-sm-3 m-0 dnone" id="rateForRateCardSnf">
                                <label>SNF</label>
                                <input type="number" class="form-control" placeholder="Enter Snf" id="fatSnf_snfValue"
                                    name="snf" step="0.1" tabindex="4" autocomplete="off">
                            </div>

                        </div>

                        <div class="col-sm-12 reset-fields">

                            <div class="col-sm-3">
                                <label>Member Name</label>
                                <span id="memberNameErr" class="memberNameErr errmsg"> </span>
                                <input id="memberName" type="text" name="memberName" class="form-control" required
                                    data-name="name" autocomplete="off" placeholder="Member Name">
                            </div>

                            <div class="col-sm-3 pt-30">
                                <label>Milk Type:
                                    <b><input type="text" class="noinput" name="milkType" id="milkType" readonly
                                            value="Select Member" size="12"></b>
                                </label>
                            </div>

                            <div class="col-sm-3 pt-30">
                                <label>Rate:
                                    <b><input type="text" class="noinput" value="" name="price" id="price" readonly
                                            value="0.0" size="8"></b>
                                </label>
                            </div>

                            {{-- </div>

                        <div class="col-md-12"> --}}
                            <div class="col-sm-3">
                                <label>Total Amount</label>
                                <input required="true" type="text" class="form-control" placeholder="Enter Amount"
                                    id="amount" name="amount" readonly>
                            </div>

                            {{--
                            <div class="col-sm-3">
                                <label>Paid Amount</label>
                                <input required="true" type="text" class="form-control" placeholder="Enter paid amount" id="pamount" name="paidamount" tabindex="7"
                                    value="0">
                            </div> --}}
                        </div>

                        <div class="col-sm-12 text-center">
                            <div class="pt-20"></div>
                            <div class="col-sm-3 text-left">
                                Current Balance:
                                <div class="memAccInfo monospace bold"></div>
                            </div>

                            <div class="col-sm-9">
                                <button type="submit" name="submit" class="btn btn-primary" id="dailySubmitBtn"
                                    tabindex="5" disabled>Add Transaction</button>
                                <img src="{{asset('images/loading.gif')}}" alt="" class="loading-on-btn dnone">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="clearfix">

            <div class="table-back " id="table-transactions">

            </div>

        </div>
    </div>


    <div class="wmodel clearfix" id="editTransModel" style="width: 75%;">
        <div class="wmodelheader"></div>
        <div class="close">X</div>
        <div class="wmodel-body">

            <div class="p-5-20">
                <h4>Member Code: <b><span class="memberCode3"></span></b></h4>

                <form method="post" action="{{ url('/updateTransaction') }}" class="clearfix" id="dailyForm2">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="dairyId3" name="dairyId"
                        value="{{ Session::get('loginUserInfo')->dairyId }}">
                    <input type="hidden" name="status" value="true">
                    <input type="hidden" id="date3" value="{{date(" d-m-Y ", strtotime($date))}}" name="date" readonly>
                    <input type="hidden" id="dailyShift3" value="" name="dailyShift">
                    <input type="hidden" id="action3" value="replace" name="action"
                        comment="decide what to do, new, replace">
                    <input type="hidden" id="dailyTransactionId3" value="" name="dailyTransactionId"
                        comment="In replace condition">
                    <input type="hidden" id="rateCardType3" name="rateCardType" value="">

                    <div class="col-sm-12">
                        <div class="pt-10"></div>
                        <div class="col-sm-3">
                            <label>Member Code</label>
                            <span class="memberCodeErrorMessage errorMessage"></span>
                            <input type="text" class="form-control" id="memberCodeInputHidden3" name="memberCode"
                                value="" readonly tabindex="11">
                        </div>

                        <div class="col-sm-3">
                            <label>Quantity</label>
                            <input type="text" required="true" class="form-control" id="quantity3"
                                onchange="callValues2()" placeholder="Enter Quantity" name="quantity" tabindex="12"
                                autocomplete="off">
                        </div>

                        <div class="col-sm-3 m-0" id="rateForRateCardFat3">
                            <label>Fat</label>
                            <input type="number" class="form-control" placeholder="Enter Fat" id="fatSnf_fatValue3"
                                name="fat" required step="0.05" tabindex="13" autocomplete="off">
                        </div>
                        <div class="col-sm-3 m-0 dnone" id="rateForRateCardSnf3">
                            <label>SNF</label>
                            <input type="number" class="form-control" placeholder="Enter Snf" id="fatSnf_snfValue3"
                                name="snf" step="0.1" tabindex="14" autocomplete="off">
                        </div>

                        {{-- @if(strtolower($returnData[1]) == 'fat')
                        <div class="col-sm-6 m-0">
                            <label>Fat</label>
                            <input type="number" class="form-control" id="fatValue3" onchange="callValues2()" placeholder="Enter Fat" name="fatValue"
                                step="0.05" tabindex="13">
                        </div>
                        @elseif(strtolower($returnData[1]) == 'fat/snf')
                        <div class="col-sm-3 m-0">
                            <label>Fat</label>
                            <input type="number" class="form-control" id="fatSnf_fatValue3" onchange="callValues2()" placeholder="Enter Fat" name="fat"
                                required step="0.05" tabindex="13">
                        </div>
                        <div class="col-sm-3 m-0">
                            <label>SNF</label>
                            <input type="number" class="form-control" id="fatSnf_snfValue3" onchange="callValues2()" placeholder="Enter Snf" name="snf"
                                required step="0.1" tabindex="14">
                        </div>
                        @endif --}}

                    </div>

                    <div class="col-sm-12">
                        <div class="col-sm-3">
                            <label>Member Name</label>
                            <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage3"></span>
                            <div class="member-info-code">$memberInfo->memberPersonalName</div>
                        </div>

                        <div class="col-sm-6 pt-30 text-center">
                            <div class="col-sm-6">
                                <label>Milk Type:
                                    <b>
                                        <input type="text" class="noinput" name="milkType" id="milkType3" readonly
                                            value="$info->milkType" size="12">
                                    </b>
                                </label>
                            </div>

                            <div class="col-sm-6">
                                <label>Rate:
                                    <b>
                                        <input type="text" class="noinput" value="" id="price3" name="price" readonly
                                            value="0.0" size="8">
                                    </b>
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <label>Amount</label>
                            <input required="true" type="text" class="form-control" id="amount3"
                                placeholder="Enter Amount" name="amount" readonly tabindex="15">
                        </div>

                    </div>

                    <div class="col-sm-12 text-center">
                        <div class="pt-30"></div>
                        <button type="submit" name="submit" id="transBtn3" class="btn btn-primary"
                            tabindex="17">Update</button>
                        <div class="fr"><a href="#" role="button" onclick="closeTransEdit(event)" class="btn btn-info"
                                tabindex="18">Cancel</a></div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="utilityRes"></div>

    <script type="text/javascript">
        function btn_loading(action){
        if(action == "show"){
            $(".loading-on-btn").show();
        }else{
            $(".loading-on-btn").hide();
        }
    }

    var glbl = []; 
    glbl['table'] = null;
    glbl['fetchVAluesReq'] = null;
    glbl['autoFillMilk'] = glbl['autoFillMilk_ini'] = {{$mUtility->isActive}};
    glbl['autoFillWeight'] = glbl['autoFillWeight_ini'] = {{$wUtility->isActive}};
    glbl['tmp_oldFatVal'] = 0;
    glbl['tmp_oldWeightVal'] = 0;

    $(document).ready(function() {

        glbl['table'] = $('#MyTable').DataTable({"orderData":[ 0 ] }, {"targets": [ 0 ],"visible": false,"searchable": false});


        $(".unclickable").on("click", function(){
            // $("#mem")
        })

        var frm = $('#dailyForm');

        frm.submit(function (e) {
            e.preventDefault();
            
            if($('#dailyForm')[0].checkValidity()){;}else{return false;}
            
            $("#dailySubmitBtn").attr("disabled", true).html("Processing...");

            console.log("afdsd");
            $.ajax({
                type: frm.attr('method'),
                url: frm.attr('action'),
                data: frm.serialize(),
                success: function (res) {
                    if(res.error){
                        $.alert("Error: "+res.msg);
                        window.location.reload(true);
                    }else{
                        fetch_transactions_by_date();
                        // $("#dailyForm").trigger("reset");
                        $('.reset-fields input').val('');
                        $(".flash-alert .flash-msg").html(res.msg);
                        $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");

                        //readonly field
                        // $("#quantity").attr('readonly',true);
                        // $("#fatSnf_fatValue").attr('readonly',true);

                        setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 3000);
                        if(res.isSlip){
                            if(res.slip_data.error){
                                $.alert(res.slip_data.msg);
                            }else{
                                var left  = ($(window).width()/2)-(900/2),
                                top   = ($(window).height()/2)-(600/2),
                                popup = window.open ("_blank", "popup", "width=217, height=600, top="+top+", left="+left);
                                popup.document.write(res.slip_data.data);
                            }
                        }
                    }
                    $("#dailySubmitBtn").attr("disabled", false).html("Add Transaction");
                },
                error: function (data) {
                    $.alert('An error has occurred.');
                    console.log(data);
                    $("#dailySubmitBtn").attr("disabled", false).html("Add Transaction");
                    window.location.reload(true);
                },
            });
        });

    });


    var dairyId = document.getElementById("dairyId").value;

                                        $( function() {

                                            var members = [
                                                @foreach ($memberInfo as $mem)
                                                    {
                                                        value: "{{ $mem->memberPersonalCode }}",
                                                        label: "{{ $mem->memberPersonalCode }}",
                                                        desc: "{{ $mem->memberPersonalName }}",
                                                    },
                                                @endforeach
                                            ];
                                            
                                            var memberNames = [
                                                @foreach ($memberInfo as $mem)
                                                    {
                                                        value: "{{ $mem->memberPersonalName }}",
                                                        label: "{{ $mem->memberPersonalName }}",
                                                        desc: "{{ $mem->memberPersonalCode }}",
                                                    },
                                                @endforeach
                                            ];

                                            $("#memberName").autocomplete({
                                                minLength: 0,
                                                source: memberNames,
                                                focus: function(event, ui) {
                                                    $("#memberName").val(ui.item.label);
                                                    return false;
                                                },
                                                select: function( event, ui ) {
                                                    $("#memberName").val(ui.item.value).trigger("change");
                                                    return false;
                                                }
                                            }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                                return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                            };
                                        });

    $("#memberCode, #memberName").on("change", function(){
        v = $(this).val();
        if(v==(null||'')){
            // console.log(this);
            return false;
        }
        CheckMemberName(v, this, $(this).data("name"), "");
    });

    $(document).on('keypress', 'input', function(event) {
        if (event.which == 13) {
            event.preventDefault();
            old = $(this).attr('tabindex');
            $(this).closest('form').find(":input:visible").each(function(){
                if($(this).attr('tabindex') > old){
                    $(this).focus();
                    return false;
                }
            })
            
            return false;
        }
    });


    $(document).ready(function(){
        if({{$flag}}){
            show_popUp();
        }else{
            fetch_transactions_by_date();
            $("#memberCode").focus();
        }

        if({{$noMember}}){
            $.confirm({
                title: 'Add New Member',
                content: 'There are No members for Milk Collection, Please add atleast 1 member to start milk collection.',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                    addMember: {
                        text: 'Add Member',
                        btnClass: 'btn-orange',
                        action: function(){
                            window.location = "{{url('memberSetupForm')}}";
                        }
                    }
                }
            });
        }

        if({{$noRateCard}}){
            $.confirm({
                title: 'Rate Card not Prepared',
                content: 'There are No Rate card for Milk Collection, Please add Rate card and apply for cow & buffalo to start milk collection.',
                type: 'orange',
                typeAnimated: true,
                buttons: {
                    addMember: {
                        text: 'Set Rate Card',
                        btnClass: 'btn-orange',
                        action: function(){
                            window.location = "{{url('rateCardNew')}}";
                        }
                    }
                }
            });
        }

    });


    $("#dateShiftPopup .close").on('click', function(e){
        e.preventDefault();
        $.alert("Please select <b>Date</b> before proceed.");
    });

    $("#sameTransModel .close").on('click', function(){
        closeSameTransModel();
    });

    function closeSameTransModel(){
        // $.alert("Please select <b>Date</b> before proceed.");
        $("#sameTransModel").fadeOut();
        $("#memberCode, #memberName").val("");$("#memberCode").focus();
        $(".memAccInfo").html("");
        dailySubmitButtonUpdate('');
    }

    $("#sdate").on("dp.change", function(){
        console.log($(this).val());
        $("#date").val($(this).val());
        url = "{{ url('/DailyTransactionSubmit') }}?date="+$("#sdate").val()+"&shift="+$("#sdailyShift").val();
        $("#dailyForm").attr("action", url);
        changeUrl();
    })
    
    $("#sdailyShift").on("change", function(){
        console.log($(this).val());
        $("#dailyShift").val($(this).val());
        url = "{{ url('/DailyTransactionSubmit') }}?date="+$("#sdate").val()+"&shift="+$("#sdailyShift").val();
        $("#dailyForm").attr("action", url);
        changeUrl();
    })

    function changeUrl(){
        param = "date="+$("#sdate").val()+"&shift="+$("#sdailyShift").val();
        if(document.location.href.indexOf('?') != -1) {
            var url = "{{ url('/DailyTransactionForm') }}"+"?"+param;
        }else{
            var url = "{{ url('/DailyTransactionForm') }}"+"?"+param;
        }
        window.history.pushState("data","Title",url);
      }

    function show_popUp(e){
        $('#dateShiftPopup').fadeIn();
        $('.pageblur').addClass("blur-3");
    }
    function hidePopup(){
        $('#dateShiftPopup').fadeOut();
        $('.pageblur').removeClass("blur-3");
    }

    function fetch_transactions_by_date(e){
        loader('show');

        sdate = $("#sdate").val();
        sshift = $("#sdailyShift").val();
        $("#date").val(sdate);
        $("#dailyShift").val(sshift);

        $.ajax({
            type:"POST",
            url:'dailyTransactionListAjax',
            data: {
                dairyId: dairyId,
                date: sdate,
                shift: sshift,
            },
            success:function(res){
                loader("hide");
                glbl['table'].destroy();
                $("#table-transactions").html(res.content);
                glbl['table'] = $('.MyTable-dailyTransactionClass').DataTable({'columnDefs': [{"orderData":[0] }, {"targets": [ 0 ],"visible": false,"searchable": false}]});

                $("#msc").html(res.msc);
                $("#esc").html(res.esc);

                hidePopup();
                $("#memberCode").focus();
            },
            error:function(res){
                loader("hide");
                console.log(res);
            }
        });
    }


    $("#fatSnf_fatValue, #fatSnf_snfValue, #quantity").on("input", function(event){
        console.log("jdkf");
        if(rateCardType == "fat" && $("#fatSnf_fatValue").val()==(""||null)){
            return false;
        }
        if(rateCardType == "fat/snf" && ($("#fatSnf_snfValue").val()==(""||null) || $("#fatSnf_fatValue").val()==(""||null))){
            return false;
        }

        if($("#fatSnf_fatValue").val() <= 5){
            milkType = "cow"; $("#milkType").val("cow");
        }else{
            milkType = "buffalo"; $("#milkType").val("buffalo");
        }
        
        fetchValues(milkType, "", $(this));
    })
    
    function fetchValues(milkType, no, field){

        snf = $("#fatSnf_snfValue"+no).val();
        fat = $("#fatSnf_fatValue"+no).val();

        memberCode = $("#memberCode"+no).val();
        dailySubmitButtonUpdate('', no);

        btn_loading('show');
        
        if(glbl['fetchVAluesReq'] != null) {
            glbl['fetchVAluesReq'].abort();
        }

        glbl['fetchVAluesReq'] = $.ajax({
            type:"POST",
            url:'fatSnfRateCardvalue',
            data: {
                dairyId     : dairyId,
                memberCode  : memberCode,
                snf         : snf,
                fat         : fat,
            },
            success:function(res){
                // console.log(res);
                setMilkVals(res, no);
                if(res.error){
                    $("#response-alert").html(res.msg).show();
                    // setTimeout(function (e){ $("#response-alert").fadeOut('slow');}, 4000);
                    dailySubmitButtonUpdate('', no);
                    $(field).focus();
                }else{
                    $("#response-alert").fadeOut('fast');

                    $("#price"+no).val(res.amount);
                    am = (parseFloat($("#quantity"+no).val()) * parseFloat(res.amount)).toFixed(2);
                    $("#amount"+no).val(am);

                    if(fat>5){
                        $("#milkType"+no).val("buffalo");
                    }else{
                        $("#milkType"+no).val("cow");
                    }

                    dailySubmitButtonUpdate( "ok", no );
                    // $("#dailySubmitBtn"+no).focus();
                }
            },
            error:function(res){
                console.log(res);
            }
        }).done(function(){
            btn_loading('');
        });
    }

    function dailySubmitButtonUpdate(flg, no){
        if(flg==(null||'')){
            $("#dailySubmitBtn"+no).attr("disabled", true);
            return true;
        }

        rateCardType = $("#rateCardType"+no).val();
        if(rateCardType=="fat"){
            snf = "0";
            $("#fatSnf_snfValue"+no).attr("required", false);
        }else{
            snf = $("#fatSnf_snfValue"+no).val();
            $("#fatSnf_snfValue"+no).attr("required", true);
        }

        fat = $("#fatSnf_fatValue"+no).val();
        qty = $("#quantity"+no).val();
        memberCode = $("#memberCode"+no).val();

        console.log(snf, fat, qty, memberCode, flg, no);
        
        if(snf == (null||"") || fat == (null||"") || qty == (null||"") || memberCode == (null||"")){
            $("#dailySubmitBtn"+no).attr("disabled", true);
        }else{
            $("#dailySubmitBtn"+no).attr("disabled", false);
        }
    }

	/* date picker */
    $(function () {
        $('#sdate').datetimepicker({
             format: 'DD-MM-YYYY'
        });
    });

    /* get rate by fat and fat/snf  */

    function getRateByFatSnf(PreFat,PreSnf){
        dairyId = document.getElementById("dairyId").value ;

        $.ajax({
            type:"POST",
            url:'fatSnfRateCardvalue' ,
            data: {
                dairyId: dairyId,
                fatRange: PreFat,
                snfRange: PreSnf,
            },
            success:function(res){
                var Quantity = document.getElementById("quantity").value;
                var amount = parseInt(Quantity)+parseInt(res);
                document.getElementById("amount").value = amount;
            }
        });
    }



  /* get member code by member name */
    function CheckMemberName(memberCode, elm, field, no){
            if(memberCode!= ""){
                loader("show");

                $.ajax({
                    type:"POST",
                    url:'DailyTransactionMemberName',
                    data: {
                        member_code: memberCode,
                        shift: $("#dailyShift"+no).val(),
                        date: $("#date"+no).val(),
                        field: field
                    },
                    success:function(res){
                        // console.log(res);
                        if(res.error){
                            $("#response-alert").html(res.msg).show();
                            $(elm).addClass("has-error").focus();
                            $("#rateCardType"+no).val("");
                            $("#quanity"+no+", #fatSnf_fatValue"+no+", #fatSnf_snfValue"+no).addClass("unclickable");
                        }else{
                            $("#response-alert").hide();
                            $("#memberName"+no).val(res.name);
                            $("#memberCode"+no).val(res.code);
                            $(".memAccInfo").html("&#8377;"+res.balance+" "+res.balanceType);
                            $("#memberCode"+no+", #memberName"+no).removeClass("has-error");
                            $("#quanity"+no+", #fatSnf_fatValue"+no+", #fatSnf_snfValue"+no).removeClass("unclickable");
                            $("#quantity"+no).focus();
                            if(res.trans!=""){
                                $("#sameTransModel .wmodel-body").html(res.trans);
                                $("#sameTransModel").fadeIn();
                                $("#addTransBtn").focus();
                            }
                        }
                        loader("hide");
                    },
                    error:function(data){
                        loader("hide");
                        $("#response-alert").html("An error has occured").show();
                        console.log(data);
                    }
                });
            }
    }

    function setMilkVals(res, no){
        if(typeof res.milkType != undefined){
            $("#milkType"+no).val(res.milkType);
        }
        if(typeof res.rateCardType != undefined){
            $("#rateCardType"+no).val(res.rateCardType);
        }
        if(res.rateCardType == "fat"){
            // console.log("fat");
            $("#rateForRateCardSnf"+no).hide();
            $("#rateForRateCardFat"+no).show();
        }else{
            // console.log(res.rateCardType);
            $("#rateForRateCardFat"+no).show();
            $("#rateForRateCardSnf"+no).show();
        }
    }

    function editTransaction(e, tId, mCode){
        e.preventDefault();

        $("#memberCode").val(mCode).trigger("change");
        
        // loader("show");
        // $.ajax({
        //     type:"POST",
        //     url:'getTransValues',
        //     data: {
        //         transId:tId,
        //     },
        //     success:function(res){
        //         if(res.error==false){
        //             showEditTransModel(res.trans);
        //         }else{
        //             $.alert("Some error has occurd at server.");     
        //         }
        //         // console.log(res);
        //         loader("hide");
        //     },
        //     error:function(res){
        //         console.log(res);
        //         loader("hide");
        //         $.alert("Something happened, Please try again.");
        //     }
        // });
    }

    function showEditTransModel(trans){
        $("#quantity3").val(trans.milkQuality);
        $("#fatSnf_fatValue3").val(trans.fat);
        $("#fatSnf_snfValue3").val(trans.snf);
        $("#amount3").val(trans.amount);
        $("#price3").val(trans.rate);
        $("#dailyTransactionId3").val(trans.id);
        $("#memberCodeInputHidden3").val(trans.memberCode);
        $(".member-info-code").html(trans.memberName);
        $("#milkType3").val(trans.milkType);
        $(".memberCode3").html(trans.memberCode);

        setMilkVals(trans, 3);
        
        $("#editTransModel").fadeIn();
    }

    function closeTransEdit(e){
        e.preventDefault();
        $("#editTransModel").fadeOut();
    }


    function callValues2(){
        snf = $("#fatSnf_snfValue3").val();
        fat = $("#fatSnf_fatValue3").val();
        // console.log(snf,fat);

        if($("#fatSnf_snfValue3").val()=="" || $("#fatSnf_fatValue3").val()==""){
            return false;
        }
        // console.log(this);
        fetchValues3();
    }
    
    function fetchValues3(){
        snf = $("#fatSnf_snfValue3").val();
        fat = $("#fatSnf_fatValue3").val();

        memberCode = $("#memberCodeInputHidden3").val();

        loader("show");
        $.ajax({
            type:"POST",
            url:'fatSnfRateCardvalue',
            data: {
                dairyId: dairyId,
                memberCode: memberCode,
                snf: snf,
                fat: fat
            },
            success:function(res){
                $("#price3").val(res.amount);
                am = (parseFloat($("#quantity3").val()) * parseFloat(res.amount)).toFixed(2);
                $("#amount3").val(am);
                loader("hide");
            },
            error:function(res){
                console.log(res);
                loader("hide");
            }
        });
    }


    $("#milkPortBtn").on("click", function(){

        $(".console-area").html("");

        var data = {
            portName: '{{$mUtility->communicationPort}}',
            baudRate: '{{$mUtility->maxSpeed}}',
            parity:   '{{$mUtility->connectionPerferenceParity}}',
            dataBits: '{{$mUtility->connectionPerferenceDataBits}}',
            stopBits: '{{$mUtility->connectionPerferenceStopBits}}',
            startChars: ['$'],
            endChars: ['\n', '\r'],
        };

        $.ajax({
            method: "POST",
            cache: false,
            url: "http://localhost:9000/com/getweight",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(data),
            async: true,
            processData: false,
            success: function (resp) {
                console.log(resp);
                $(".console-area").html(resp);
            },
            error: function (err) {
                console.log(err);
                $(".console-area").html("ERROR: getWeight Utility may not be started on your computer.");
                alert(err.responseText);
            }
        })
    });


    // function getQtyWeightFromPort(){
        $("#quantity").on('keypress',function(e){

            if(e.keyCode==107){
                alert("+ key press");
                $("#quantity").attr(readonly,false);
            return;
        }
            var quantity= $(this).val();
            // if(!empty(quantity)){
                if(quantity != "" && quantity != null){

                $(this).attr('readonly',false);

            }
            // if(e.keyCode!=13){
            //     return;
            // }

            // alert("enter press")

          
     
        if(!glbl['autoFillWeight']){
            return true;
        }

        var data = {
            portName: '{{$wUtility->communicationPort}}',
            baudRate: '{{$wUtility->maxSpeed}}',
            parity:   '{{$wUtility->connectionPerferenceParity}}',
            dataBits: '{{$wUtility->connectionPerferenceDataBits}}',
            stopBits: '{{$wUtility->connectionPerferenceStopBits}}',
            saparator: '$',
            startChars: ['$'],
            endChars: ['\n', '\r'],
            decimal:'{{$wUtility->decimal_digit}}',
        };
        // alert( data.decimal);
        // $("#quantity, #quantity2, #quantity3").val("45");

        $.ajax({
            method: "POST",
            cache: false,
            url: "http://localhost:9000/com/getweight",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(data),
            async: true,
            processData: false,
            success: function (resp) {
                console.log(resp);
//decimal settings
                // resp = truncateToDecimals(parseFloat(resp), 1);
                resp = truncateToDecimals(parseFloat(resp),data.decimal);
                
                $("#quantity, #quantity2, #quantity3").val(resp);
                
                if(resp!=glbl['tmp_oldWeightVal']){
                    $("#quantity, #quantity2, #quantity3").trigger('input');
                }
                glbl['tmp_oldWeightVal'] = resp;
            },
            error: function (err) {
                console.log(err);
                $("#utilityRes").html("ERROR: getWeight Utility may not be started on your computer. <span>Message: "+err.responseText+"</span>").slideDown("fast", function(){
                    setTimeout(function() {
                        $("#utilityRes").slideUp("fast");
                    }, 10000);
                });
                // alert(err.responseText);
            }
        })

        // setTimeout(getQtyWeightFromPort, 5600);
    });
//fat field
    // function getMilkFatFromPort(){
        $("#fatSnf_fatValue").on('keypress',function(e){
           
          
            var fat_snfValue= $(this).val();
            if(!empty(fat_snfValue)){

                $(this).attr('readonly',false);

            }
            

        if(e.keyCode!=13){
               
                return;
        }
        alert("enter press");
        if(!glbl['autoFillWeight']){
            return true;
        }


        if(!glbl['autoFillMilk']){
            return true;
        }

        var data = {
            portName: '{{$mUtility->communicationPort}}',
            baudRate: '{{$mUtility->maxSpeed}}',
            parity:   '{{$mUtility->connectionPerferenceParity}}',
            dataBits: '{{$mUtility->connectionPerferenceDataBits}}',
            stopBits: '{{$mUtility->connectionPerferenceStopBits}}',
            saparator: '\r',
            startChars: ['$'],
            endChars: ['\n', '\r'],
            decimal:'{{$mUtility->decimal_digit}}',
        };

        // $("#fatSnf_fatValue, #fatSnf_fatValue2, #fatSnf_fatValue3").val("34");
    
        $.ajax({
            method: "POST",
            cache: false,
            url: "http://localhost:9000/com/getweight",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(data),
            async: true,
            processData: false,
            success: function (resp) {
                console.log(resp);
                // $("#fatSnf_fatValue, #fatSnf_fatValue2, #fatSnf_fatValue3").val(resp);
                // resp = parseFloat(resp).toFixed(1);
//decimal change
                resp = truncateToDecimals(parseFloat(resp), data.decimal);

                $("#fatSnf_fatValue, #fatSnf_fatValue2, #atSnf_fatValue3").val(resp);
                
                if(resp!=glbl['tmp_oldFatVal']){
                    $("#fatSnf_fatValue, #fatSnf_fatValue2, #atSnf_fatValue3").trigger('input');
                }
                glbl['tmp_oldFatVal'] = resp;

            },
            error: function (err) {
                console.log(err);
                $("#utilityRes").html("ERROR: getWeight Utility may not be started on your computer. <span>Message: "+err.responseText+"</span>").slideDown("fast", function(){
                    setTimeout(function() {
                        $("#utilityRes").slideUp("fast");
                    }, 10000);
                });
                // alert(err.responseText);
            }
        })

        // setTimeout(getMilkFatFromPort, 6000);
    });

    // $("#autometicReadingCheck").on("change", function(){

    $(document).keydown(function(event){
        // if(event.keyCode!=107){
        //     return;
        // }
        // alert("+ key press");
        $("#autometicReadingCheck").prop("checked")
      
        if($("#autometicReadingCheck").prop("checked")){
            glbl['autoFillMilk'] = false;
            glbl['autoFillWeight'] = false;
        }else{
            glbl['autoFillWeight'] = glbl['autoFillWeight_ini'];
            glbl['autoFillMilk'] = glbl['autoFillMilk_ini'];
            
            // getMilkFatFromPort();
            // getQtyWeightFromPort();
        }
        console.log(glbl['autoFillWeight'], glbl['autoFillMilk']);
    })

    // getMilkFatFromPort();
    // getQtyWeightFromPort();
    
    function truncateToDecimals(num, dec = 2) {
      const calcDec = Math.pow(10, dec);
      return Math.trunc(num * calcDec) / calcDec;
    }

    </script>
    @endsection