@extends('theme.default') 
@section('content') 
@php 
// if (session()->has(' ')) { $tab = session()->get('creditActiveTab');
// }else{ $tab = "customer"; } 
$tab = "member";

@endphp


<div class="pageblur">

    <div class="fcard margin-fcard-1 pt-0 clearfix">
        <div class="upper-controls pt-0 clearfix">
            <div class="fl">
                <div class="heading">
                    <h3>Credit</h3>
                    <hr class="m-0">
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs sale-tabs">
            <li class="@if($tab=='customer') active @endif"><a data-toggle="tab" href="#customersAdvance" onclick="document.getElementById('customerCode').focus();">Customers</a></li>
            <li class="@if($tab=='member') active @endif"><a data-toggle="tab" href="#memberAdvance" onclick="document.getElementById('memberCode').focus()">Members</a></li>
            <li class="@if($tab=='supplier') active @endif"><a data-toggle="tab" href="#supplierAdvance" onclick="document.getElementById('supplierCode').focus()">Suppliers</a></li>
        </ul>

        <div class="tab-content pt-20">

            <div id="customersAdvance" class="tab-pane fade in @if($tab=='customer') active @endif">
                <div class="col-sm-12">
                    <form method="post" action="{{ url('/creditSubmit') }}" class="clearfix">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
                        <input type="hidden" name="status" value="true">
                        <input type="hidden" name="partyType" value="customer">

                        <div class="col-md-12 clearfix">
                            <div class="col-sm-3">
                                <label>Date: </label>
                                <input type="text" class="form-control" id="sdate" value="<?php echo date(" d-m-Y ") ; ?>" name="date" autocomplete="off">
                            </div>

                            <div class="col-sm-3">
                                <label>Customer Code</label>
                                <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessage"></span>
                                <input id="customerCode" class="form-control" name="partyCode" required data-name="code">
                            </div>

                            <div class="col-sm-3">
                                <label>Customer Name</label>
                                <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage"></span>
                                <input class="form-control" id="customerName" name="partyName" required="true" data-name="name">
                            </div>

                            <div class="col-sm-3">
                                <label>Current Balance</label>
                                <input class="form-control" id="customerBalance" readonly tabindex="-1">
                            </div>

                            <div class="col-sm-3">
                                <label>Amount</label>
                                <input type="text" required="true" class="form-control" placeholder="Credit" id="camount" name="credit">
                            </div>
                            <div class="col-sm-3">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remarks" id="cremark" name="remark">
                            </div>
                            
                            <div class="col-sm-3 pr-30">
                                <div class="pt-20"></div>
                                <button type="submit" name="submit" class="btn btn-primary">Add to Credit</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>


            <div id="memberAdvance" class="tab-pane fade in @if($tab=='member') active @endif">

                <div class="col-sm-12">
                    <form method="post" action="{{ url('/creditSubmit') }}" class="clearfix">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="mdairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
                        <input type="hidden" name="status" value="true">
                        <input type="hidden" name="partyType" value="member">

                        <div class="col-md-12 clearfix">
                            <div class="col-sm-3">
                                <label>Date: </label>
                                <input type="text" class="form-control" id="mdate" value="<?php echo date(" d-m-Y ") ; ?>" name="date" autocomplete="off">
                            </div>

                            <div class="col-sm-3">
                                <label>Member Code</label>
                                <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessage"></span>
                                <input id="memberCode" class="form-control" name="partyCode" required data-name="code">
                            </div>

                            <div class="col-sm-3">
                                <label>Member Name</label>
                                <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage"></span>
                                <input class="form-control" id="memberName" name="partyName" required="true" data-name="name">
                            </div>

                            <div class="col-sm-3">
                                <label>Current Balance</label>
                                <input class="form-control" id="memberBalance" readonly tabindex="-1">
                            </div>

                            <div class="col-sm-3">
                                <label>Amount</label>
                                <input type="text" required="true" class="form-control" placeholder="Credit" id="mamount" name="credit">
                            </div>

                            <div class="col-sm-3">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remarks" id="cremark" name="remark">
                            </div>

                            <div class="col-sm-3 pr-30 ">
                                <div class="pt-20"></div>
                                <button type="submit" name="submit" class="btn btn-primary">Add to Credit</button>
                            </div>
    
                        </div>

                    </form>
                </div>

            </div>


            

            <div id="supplierAdvance" class="tab-pane fade in @if($tab=='supplier') active @endif">

                <div class="col-sm-12">
                    <form method="post" action="{{ url('/creditSubmit') }}" class="clearfix">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="sdairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
                        <input type="hidden" name="status" value="true">
                        <input type="hidden" name="partyType" value="supplier">

                        <div class="col-md-12 clearfix">
                            <div class="col-sm-3">
                                <label>Date: </label>
                                <input type="text" class="form-control" id="sdate" value="<?php echo date(" d-m-Y ") ; ?>" name="date" autocomplete="off">
                            </div>

                            <div class="col-sm-3">
                                <label>Supplier Code</label>
                                <span class="memberCodeErrorMessage errorMessage" id="supplierCodeErrorMessage"></span>
                                <input id="supplierCode" class="form-control" name="partyCode" required data-name="code">
                            </div>

                            <div class="col-sm-3">
                                <label>Supplier Name</label>
                                <span class="supplierNameErrorMessage errorMessage" id="supplierNameErrorMessage"></span>
                                <input class="form-control" id="supplierName" name="partyName" required="true" data-name="name">
                            </div>

                            <div class="col-sm-3">
                                <label>Current Balance</label>
                                <input class="form-control" id="supplierBalance" readonly tabindex="-1">
                            </div>

                            <div class="col-sm-3">
                                <label>Amount</label>
                                <input type="text" required="true" class="form-control" placeholder="Credit" id="samount" name="credit">
                            </div>

                            <div class="col-sm-3">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remarks" id="cremark" name="remark">
                            </div>

                            <div class="col-sm-3 pr-30">
                                <div class="pt-20"></div>
                                <button type="submit" name="submit" class="btn btn-primary">Add to Credit</button>
                            </div>
    
                        </div>

                    </form>
                </div>

            </div>

        </div>


    </div>

    <div class="table-back ">
        <table id="credit-table" class="display tright" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Party Code</th>
                    <th>Party Name</th>
                    <th>Date <small>(DD-MM-YYYY)</small></th>
                    <th>Amount <small>&#8377; (INR)</small></th>
                    <th>Remark</th>
                </tr>
            </thead>

            <tbody class="table-transactions">

            </tbody>
        </table>

    </div>

</div>



<script>

	var dairyId = document.getElementById("dairyId").value;

    $( function() {
                                        var members = [
                                            @foreach ($members as $memberInfoData)
                                                {
                                                    value: "{{ $memberInfoData->memberPersonalCode }}",
                                                    label: "{{ $memberInfoData->memberPersonalCode }}",
                                                    desc: "{{ $memberInfoData->memberPersonalName }}",
                                                },
                                            @endforeach
                                        ];

                                        var membersName = [
                                            @foreach ($members as $memberInfoData)
                                                {
                                                    value: "{{ $memberInfoData->memberPersonalName }}",
                                                    label: "{{ $memberInfoData->memberPersonalName }}",
                                                    desc: "{{ $memberInfoData->memberPersonalCode }}",
                                                },
                                            @endforeach
                                        ];

                                        var customer = [
                                            @foreach ($customer as $cust)
                                                {
                                                    value: "{{ $cust->customerCode }}",
                                                    label: "{{ $cust->customerCode }}",
                                                    desc: "{{ $cust->customerName }}",
                                                },
                                            @endforeach
                                        ];

                                        var customerName = [
                                            @foreach ($customer as $cust)
                                                {
                                                    value: "{{ $cust->customerName }}",
                                                    label: "{{ $cust->customerName }}",
                                                    desc: "{{ $cust->customerCode }}",
                                                },
                                            @endforeach
                                        ];
                                    
                                        var suppliers = [
                                            @foreach ($suppliers as $sup)
                                                {
                                                    value: "{{ $sup->supplierCode }}",
                                                    label: "{{ $sup->supplierCode }}",
                                                    desc: "{{ $sup->supplierFirmName }}",
                                                },
                                            @endforeach
                                        ];

                                        var supplierName = [
                                            @foreach ($suppliers as $sup)
                                                {
                                                    value: "{{ $sup->supplierFirmName }}",
                                                    label: "{{ $sup->supplierFirmName }}",
                                                    desc: "{{ $sup->supplierCode }}",
                                                },
                                            @endforeach
                                        ];
                                    
                                        $( "#memberCode" ).autocomplete({
                                            minLength: 0,
                                            source: members,
                                            focus: function( event, ui ) {
                                                $( "#memberCode" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#memberCode" ).val( ui.item.value );
                                                $( "#memberName" ).val( ui.item.desc );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#memberName" ).autocomplete({
                                            minLength: 0,
                                            source: membersName,
                                            focus: function( event, ui ) {
                                                $( "#memberName" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#memberCode" ).val( ui.item.desc );
                                                $( "#memberName" ).val( ui.item.value );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#customerCode" ).autocomplete({
                                            minLength: 0,
                                            source: customer,
                                            focus: function( event, ui ) {
                                                $( "#customerCode" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#customerCode" ).val( ui.item.value );
                                                $( "#customerName" ).val( ui.item.desc );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#customerName" ).autocomplete({
                                            minLength: 0,
                                            source: customerName,
                                            focus: function( event, ui ) {
                                                $( "#customerName" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#customerCode" ).val( ui.item.desc );
                                                $( "#customerName" ).val( ui.item.value );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };
                                    

                                        $( "#supplierCode" ).autocomplete({
                                            minLength: 0,
                                            source: suppliers,
                                            focus: function( event, ui ) {
                                                $( "#supplierCode" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#supplierCode" ).val( ui.item.value );
                                                $( "#supplierName" ).val( ui.item.desc );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#supplierName" ).autocomplete({
                                            minLength: 0,
                                            source: supplierName,
                                            focus: function( event, ui ) {
                                                $( "#supplierName" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#supplierCode" ).val( ui.item.desc );
                                                $( "#supplierName" ).val( ui.item.value );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };
                                    });



    $("#memberCode, #memberName").on("change, focusout", function(){
        v = $(this).val();
        if(v==(null||'')){
            return false;
        }
        getUserDetail(v, this, $(this).data("name"), "member", "");
    })

    $("#customerCode, #customerName").on("change, focusout", function(){
        v = $(this).val();
        if(v==(null||'')){
            return false;
        }
        getUserDetail(v, this, $(this).data("name"), "customer", "");
    })

    $("#supplierCode, #supplierName").on("change, focusout", function(){
        v = $(this).val();
        if(v==(null||'')){
            return false;
        }
        getUserDetail(v, this, $(this).data("name"), "supplier", "");
    })

	function getUserDetail(q, elm, qtype, user, no){
        if(q){
            loader("show");

            $.ajax({
                type:"POST",
                url:'getUserDetail',
                data: {
                    q: q,
                    qtype: qtype,
                    dairyId: dairyId,
                    user: user
                },
                success:function(res){
                    if(res.error){
                        $("#response-alert").html(res.msg).show();
                        $(elm).addClass("has-error");
                    }else{
                        $("#response-alert").hide();
                        setUserData(res.data, elm, user, no);
                    }
                    loader("hide");
                    console.log(res);
                },
                error:function(res){
                    console.log(res);
                }
            });
        }
    }

    function setUserData(data, elm, user, no){
        if(user=="member"){
            $("#memberCode").val(data.code);
            $("#memberName").val(data.name);
            $("#memberBalance").val(data.bal+ " "+ data.balType);
        }

        if(user=="customer"){
            $("#customerCode").val(data.code);
            $("#customerName").val(data.name);
            if(!data.isCash){
                $("#customerBalance").val(data.bal+ " "+ data.balType);
            }else{
                $("#customerBalance").val("Cash User");
            }
        }

        if(user=="supplier"){
            $("#supplierCode").val(data.code);
            $("#supplierName").val(data.name);
            $("#supplierBalance").val(data.bal+ " "+ data.balType);
        }

        $("#customerCode, #customerName, #memberCode, #memberName, #supplierCode, #supplierName").removeClass("has-error");
    }


    $(document).ready(function() {
		$('#credit-table').DataTable({
            "ajax": 'getCreditData',
            "order": [],
		});
        
        $("#date, #mdate, #sdate").datetimepicker({
            format:"DD-MM-YYYY"
        })
	});

</script>
@endsection