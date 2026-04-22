<div class="row p-5-20">
    <h4>Member Entry on {{date("d-m-Y", strtotime($date))." (".$shift.")"}}</h4>

    <div class="upper-controls pt-0 clearfix">
        <div class="fr">
            <a href="#" role="button" class="btn btn-primary btn-sm" id="addTransBtn" onclick="addTrans(this)"> <i class="fa fa-plus"></i> Add new entry </a>
        </div>
    </div>

    <div class="formNewEntry dnone" style="padding-bottom:30px">
            <form method="post" id="multiform1" action="{{ url('/updateTransaction')."?date=".date("d-m-Y", strtotime($date))."&shift=".$shift }}" class="clearfix" id="dailyForm" onsubmit="saveTrans(event)">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="dairyId1" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
                <input type="hidden" name="status" value="true">
                <input type="hidden" id="date2" value="{{date("d-m-Y", strtotime($date))}}" name="date" readonly>
                <input type="hidden" id="dailyShift2" value="{{$shift}}" name="dailyShift">
                <input type="hidden" id="action" value="" name="action" comment="decide what to do, new, replace">
                <input type="hidden" id="dailyTransactionId" value="" name="dailyTransactionId" comment="In replace condition">
                <input type="hidden" id="ajax" value="true" name="ajax" comment="">
                <input type="hidden" id="rateCardType2" value="" name="rateCardType">

                <div class="col-sm-12">
                    <div class="pt-10"></div>
                    <div class="col-sm-3">
                        <label>Member Code</label>
                        <span id="memberCodeErr" class="memberCodeErr errmsg">  </span>
                        <input id="memberCode2" class="form-control" value="{{$memberInfo->memberPersonalCode}}" required name="memberCode" tabindex="21" data-name="code" readonly onkeydown="memCodeInputEvent(event, this, $('#quantity2'))">
                        <img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/>
                    </div>
                    
                    <div class="col-sm-3">
                        <label>Quantity</label>
                        <input type="text" required="true" class="form-control" id="quantity2" oninput="callValues()" placeholder="Enter Quantity" name="quantity" tabindex="22" autocomplete="off" onkeydown="qtyInputEvent(event, this, $('#fatSnf_fatValue2'))">
                    </div>
                
                    <div class="col-sm-3 m-0" id="rateForRateCardFat2">
                        <label>Fat</label>
                        <input type="number" class="form-control" placeholder="Enter Fat" id="fatSnf_fatValue2" oninput="callValues()" name="fat" required step="0.05" tabindex="23" autocomplete="off" onkeydown="fatInputEvent(event, this)">
                    </div>
                    <div class="col-sm-3 m-0 @if($valueType != "fat/snf") dnone @endif" id="rateForRateCardSnf2">
                        <label>SNF</label>
                        <input type="number" class="form-control" placeholder="Enter Snf" id="fatSnf_snfValue2" oninput="callValues()" name="snf" step="0.1" tabindex="24" autocomplete="off">
                    </div>

                    {{-- @if(strtolower($dairyInfo->rateCard) == 'fat')
                        <div class="col-sm-6 m-0">
                            <label>Fat</label>
                            <input type="number" class="form-control" id="fatValue2" onchange="callValues()" placeholder="Enter Fat" name="fatValue" step="0.05" tabindex="23">
                        </div>
                    @elseif(strtolower($dairyInfo->rateCard) == 'fat/snf')
                        <div class="col-sm-3 m-0">
                            <label>Fat</label>
                            <input type="number" class="form-control" id="fatSnf_fatValue2" onchange="callValues()" placeholder="Enter Fat" name="fat" required step="0.05" tabindex="23">
                        </div>
                        <div class="col-sm-3 m-0">
                            <label>SNF</label>
                            <input type="number" class="form-control" id="fatSnf_snfValue2" onchange="callValues()" placeholder="Enter Snf" name="snf" required step="0.1" tabindex="24">
                        </div>
                    @endif --}}
            
                </div>
            
                <div class="col-sm-12">

                    <div class="col-sm-3">
                        <label>Member Name</label>
                        <span id="memberNameErr" class="memberNameErr errmsg"></span>
                        <input id="memberName2" name="memberName" class="form-control" value="{{$memberInfo->memberPersonalName}}" required data-name="name" readonly>
                    </div>
        
                    <div class="col-sm-6 pt-30 text-center">
                        <div class="col-sm-6">
                            <label>Milk Type:
                                <b>
                                    <input type="text" class="noinput" name="milkType" id="milkType2" readonly value="{{$info->milkeType}}" size="12">
                                </b>
                            </label>
                        </div>
            
                        <div class="col-sm-6">
                            <label>Rate: 
                                <b>
                                    <input type="text" class="noinput" value="" id="price2" name="price" readonly value="0.0" size="8">
                                </b>
                            </label>
                        </div>
                    </div>
                {{-- </div>
            
                <div class="col-md-12"> --}}
                    <div class="col-sm-3">
                        <label>Amount</label>
                        <input required="true" type="text" class="form-control" id="amount2" placeholder="Enter Amount" name="amount" readonly >
                    </div>
                    {{-- <div class="col-sm-3">
                        <label>Paid Amount</label>
                        <input required="true" type="text" class="form-control" id="paidamount2" value="0" placeholder="Enter Amount" name="paidamount" >
                    </div> --}}
                </div>

                <div class="col-sm-12 text-center">
                    <div class="pt-20"></div>
                    <button type="button" name="submit" id="dailySubmitBtn2" class="btn btn-primary" disabled tabindex="27">Add Transaction</button>
                    <div class="fr"><button type="button" onclick="closeTrans(event)" class="btn btn-info" tabindex="0">Cancel</button></div>
                </div>
            </form>
    </div>

    <div >
        <table id="MyTable" class="display table table-bordered tright" cellspacing="0">
            <thead>
                <tr>
                    <th>Member Code</th>
                    <th>Milk Type</th>
                    <th>Quantity</th>
                    <th>Fat</th>
                    <th>Snf</th>
                    <th>Rate</th>
                    <th>Total Amount</th>
                    <th></th>
                </tr>
            </thead>

            <tbody class="">
                @foreach ($trans as $trns)
                <tr id="trTrans{{$trns->id}}">
                    <td>{{ $trns->memberCode}}</td>
                    <td>{{ $trns->milkType}}</td>
                    <td>{{ $trns->milkQuality}}</td>
                    <td>{{ $trns->fat}}</td>
                    <td>{{ $trns->snf}}</td>
                    <td>{{ $trns->rate }}</td>
                    <td>{{ $trns->amount }}</td>                                
                    <td>
                        <a href="#" role="button" onclick="updateTrans(this)" data-transid="{{$trns->id}}"> <i class="fa fa-edit"></i> Replace</a>
                        &nbsp;
                        <a href="#" role="button" onclick="deleteTransConfirm(this)" data-transid="{{$trns->id}}"> <i class="fa fa-trash"></i> Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


<script>


    function addTrans(e){
        $("#addTransBtn").hide();
        $(".formNewEntry").slideDown("fast");
        $("#quantity2").focus();
        $("#action").val("new");

        glbl['manual_entry_weight'] = true;
        getQtyWeightFromPort($("#quantity2"));
    }

    function closeTrans(event){
        event.preventDefault();

        $("#quantity2").val("");
        $("#fatSnf_fatValue2").val("");
        $("#fatSnf_snfValue2").val("");
        $("#amount2").val("");
        $("#price2").val("");

        $(".formNewEntry").slideUp("fast");
        $("#addTransBtn").show();
        $("#action").val("");
        $("#dailySubmitBtn2").html("Add Transaction");
    }

    // $("#quantity2, #fatSnf_fatValue2, #fatSnf_snfValue2, #fatValue2").on("keyup", function(e){
    //     if(e.keyCode==13){
    //         if($("#dailySubmitBtn2").attr("disabled")){
    //             return;
    //         }else{
    //             callValues();
    //         }
    //     }
    // })
    $("#dailySubmitBtn2").on("click", function(){
        if($("#dailySubmitBtn2").attr("disabled")){
            return;
        }else{
            $("#multiform1").submit();
        }
    })


    function callValues(){
        snf = $("#fatSnf_snfValue2").val();
        fat = $("#fatSnf_fatValue2").val();

        rateCardType = $("#rateCardType2").val();
        // console.log(rateCardType);

        if(rateCardType == (""||null)){
            return false;
        }
        if(rateCardType == "fat" && $("#fatSnf_fatValue2").val()==(""||null)){
            // console.log($("#fatSnf_fatValue2").val(), rateCardType);
            return false;
        }
        if(rateCardType == "fat/snf" && ($("#fatSnf_snfValue2").val()==(""||null) && $("#fatSnf_fatValue2").val()==(""||null))){
            return false;
        }

        fetchValues(rateCardType, "2");
    }
    
    function fetchValues2(){
        snf = $("#fatSnf_snfValue2").val();
        fat = $("#fatSnf_fatValue2").val();

        memberCode = $("#memberCode2").val();

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
                loader("hide");
                if(res.error){
                    $("#response-alert").html(res.msg).show();
                    setTimeout(function (e){ $("#response-alert").fadeOut('slow');}, 8000);
                    dailySubmitButtonUpdate2('');
                }else{
                    $("#price2").val(res.amount);
                    am = (parseFloat($("#quantity2").val()) * parseFloat(res.amount)).toFixed(2);
                    $("#amount2").val(am);

                    dailySubmitButtonUpdate2( "ok" );
                }
                // console.log(res);
            },
            error:function(res){
                console.log(res);
                loader("hide");
            }
        });
    }

    function updateTrans(e){
        transId = $(e).data("transid");

        loader("show");
        $.ajax({
            type:"POST",
            url:'getTransValues',
            data: {
                transId:transId,
            },
            success:function(res){
                if(res.error==false){
                    setTransValues(res.trans);
                    dailySubmitButtonUpdate2("ok");
                }else{
                    $.alert("Some error has occurd at server.");
                    dailySubmitButtonUpdate2('');
                    window.location.reload(true);
                }
                // console.log(res);
                loader("hide");
            },
            error:function(res){
                console.log(res);
                loader("hide");
                $.alert("Something happened, Please try again.");
                window.location.reload(true);
            }
        });
    }

    function setTransValues(trans){
        $("#quantity2").val(trans.milkQuality);
        $("#fatSnf_fatValue2").val(trans.fat);
        $("#fatSnf_snfValue2").val(trans.snf);
        if(trans.snf==(""||null)){
            $("#rateCardType2").val("fat");
            $("#rateForRateCardSnf2").addClass("dnone");
        }else{
            $("#rateCardType2").val("fat/snf");
            $("#rateForRateCardSnf2").removeClass("dnone");
        }
        $("#rateForRateCardFat2").removeClass("dnone");

        $("#milkType2").val(trans.milkType);
        $("#amount2").val(trans.amount);
        $("#price2").val(trans.rate);
        $("#dailyTransactionId").val(trans.id);
        replaceTransFormOpen();
    }

    function replaceTransFormOpen(){
        $("#action").val("replace");
        $("#addTransBtn").hide();
        $(".formNewEntry").slideDown("fast");
        $("#quantity2").focus();
        $("#dailySubmitBtn2").html("Replace Transaction");

        glbl['manual_entry_weight'] = true;
        getQtyWeightFromPort($("#quantity2"));
    }

    function dailySubmitButtonUpdate2(flg){
        if(flg==(null||'')){
            // console.log("sa");
            $("#dailySubmitBtn2").attr("disabled", true);
            return true;
        }

        snf = $("#fatSnf_snfValue2").val();
        fat = $("#fatSnf_fatValue2").val();
        qty = $("#quantity2").val();
        memberCode = $("#memberCode2").val();

        if(snf == (null||"") || fat == (null||"") || qty == (null||"") || memberCode == (null||"")){
            // console.log(snf, fat, qty, memberCode);
            $("#dailySubmitBtn2").attr("disabled", true);
        }else{
            $("#dailySubmitBtn2").attr("disabled", false);
            // console.log(memberCode);
        }
    }

    function deleteTransConfirm(e){
        transId = $(e).data("transid");
        $.confirm({
            title: 'Do you want to delete this transaction?',
            content: 'The transaction will be rollback and balance will effected',
            theme: 'light',
            buttons: {
                confirm: function () {
                    deleteTrans(transId);
                },
                cancel: function () {
                    return true;
                }
            }
        });
    }

    function deleteTrans(transId){
        dairyId = $("#dairyId1").val();
        memberCode = $("#memberCode2").val();

        loader("show");
        $.ajax({
            type:"POST",
            url:'DailyTransactionDelete',
            data: {
                transId:transId,
                memberCode:memberCode,
                dairyId:dairyId,
            },
            success:function(res){
                loader("hide");
                if(res.error==false){
                    $("#trTrans"+transId).slideUp("slow");
                    
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                    setTimeout( function(){$(".flash-alert").fadeOut("slow");}, 3000);

                    fetch_transactions_by_date();
                }else{
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-success").show().addClass("alert-danger");
                    setTimeout( function(){$(".flash-alert").fadeOut("slow");}, 3000);
                }
                // console.log(res);
            },
            error:function(res){
                console.log(res);
                loader("hide");                    
                $(".flash-alert .flash-msg").html("Some error has occured.");
                $(".flash-alert").removeClass("hide").removeClass("alert-success").show().addClass("alert-danger");
                setTimeout( function(){$(".flash-alert").fadeOut("slow");}, 3000);
            }
        });
    }

    function saveTrans(e){
        e.preventDefault();

        btntxt = $("#dailySubmitBtn2").html();
        $("#dailySubmitBtn2").attr("disabled", true).html("Processing...");

        form = $("#multiform1").serializeArray();
        var form1 = { };
        $.each(form, function() {
            form1[this.name] = this.value;
        });
        action = $("#multiform1").attr("action");
        console.log(form);

        loader("show");
        $.ajax({
            type:"POST",
            url:action,
            data: form1,
            success:function(res){
                loader("hide");
                if(res.error){
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-success").show().addClass("alert-danger");
                    setTimeout( function(){$(".flash-alert").fadeOut("slow");}, 3000);
                    window.location.reload(true);
                }else{
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                    setTimeout( function(){$(".flash-alert").fadeOut("slow");}, 3000);

                    closeSameTransModel();
                    fetch_transactions_by_date();
                    // $("#memberCode").val($("#memberCode2").val()).trigger("change");
                    if(res.isSlip){
                        if(res.slip_data.error){
                            $(".flash-alert .flash-msg").html(res.msg+"<br/>"+res.slip_data.msg);
                            $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                            setTimeout( function(){$(".flash-alert").fadeOut("slow");}, 3000);
                        }else{
                            var left  = ($(window).width()/2)-(900/2),
                            top = ($(window).height()/2)-(600/2),
                            popup = window.open("_blank", "popup", "width=217, height=600, top="+top+", left="+left);
                            popup.document.write(res.slip_data.data);
                            // popup.print();
                        }
                    }
                }
                // console.log(res);
                $("#dailySubmitBtn2").attr("disabled", false).html(btntxt); 
            },
            error:function(res){
                console.log(res);
                loader("hide");
                $.alert("There is an error.!");
                $("#dailySubmitBtn2").attr("disabled", false).html(btntxt);
            }
        }).always(function(){
            glbl['manual_entry_fat'] = !glbl['autoFillMilk_ini'];
            glbl['manual_entry_weight'] = !glbl['autoFillWeight_ini'];
        });
    }

</script>