@extends('theme.default') 
@section('content')

<style>
    .member {
        border: 1px solid #eaeaea;
        padding: 5px 10px;
        cursor: pointer;
        margin-bottom: 8px;
    }

    .mem-code {
        font-size: 18px;
        color: #565656;
        font-family: monospace;
        letter-spacing: 1px;
    }
</style>

<div class="fcard margin-fcard-1 pt-0 clearfix">
    <div class="upper-controls clearfix">
        <div class="fl">
            <h3>Product Delivary Request</h3>
        </div>
        <div class="fr">

            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>            {{-- <a class="btn btn-primary" href="memberSetupForm">Add New Member</a> --}}
            <select class="selectpicker" name="deliveryType" id="deliveryType">
                <option value="0" @if($deliveryType == "0") selected @endif>Not delivered</option>
                <option value="2" @if($deliveryType == "2") selected @endif>Declined</option>
                <option value="lastweek" @if($deliveryType == "lastweek") selected @endif>Last week (delivered)</option>
                <option value="lastmonth" @if($deliveryType == "lastmonth") selected @endif>Last month (delivered)</option>
                <option value="6month" @if($deliveryType == "6month") selected @endif>Last 6 month (delivered)</option>
            </select>
    
        </div>
        
    </div>

    <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
        @php $i = 0; @endphp
        @foreach ($prodReq as $m)
        @php
                $i++; 
                $mem = DB::table('member_personal_info')
                    ->where('memberPersonalCode', $m->memberCode)
                    ->where('dairyId', $m->dairyId)
                    ->get()->first();
            $bal = DB::table("user_current_balance")->where("ledgerId", $mem->ledgerId)->get()->first();
            // $milkReq = DB::table("milkrequest")->where("memberCode", $req->memCode)->where("dairyId", $u->dairyId)->get();
        @endphp

        <div class="member @if($m->isDeliverd == 1) req-completed @elseif($m->isDeliverd == 2) req-decliend  @endif" id="milkreq{{$m->id}}" id="milkreq{{$m->id}}">

            <div class="">
                <div class="col-sm-4">
                    
                    <div class="mem-code">{{ $m->memberCode}}</div>
                    <div class="mem-name">{{ $mem->memberPersonalName}}</div>
                    <div class="mem-contact">{{ $mem->memberPersonalEmail.", ".$mem->memberPersonalMobileNumber}}</div>

                    <span class="bold {{$bal->openingBalanceType}}">
                        {{ number_format((float)str_replace("-","",$bal->openingBalance),2) }}
                    </span>

                    <div class="complete-btn-area">
                        @if(!$m->isDeliverd)
                            <button class="btn btn-default btn-xs complete-btn" data-completeid="{{$m->id}}" data-action="complete">Complete</button>
                            &nbsp;
                            <button class="btn btn-danger btn-xs decline-btn" data-completeid="{{$m->id}}" data-action="decline">Decline</button>
                        @elseif($m->isDeliverd == 1)
                            <span class="label label-success">Completed</span>
                        @elseif($m->isDeliverd == 2)
                            <span class="label label-danger">Decliend</span>
                        @endif
                    </div>
                </div>

                <div class="col-sm-8">
                    <div class="clearfix">
                        <div class="s-req-date">{{date("d-m-Y", strtotime($m->date))}} &nbsp; &nbsp; Shift: {{$m->shift}}</div>

                        <div class="s-req-usr" @if($isAdmin) onclick="colManSelect('{{$m->id}}')" data-toggle="tooltip" title="Asssign to collection manager" data-placement="top" @endif id="colManAssigned{{$m->id}}">{{$m->colMan}}</div>
                        <div class="req-type">For: 
                            <b>{{ucfirst($m->type)}}
                                @if($m->type == "product") 
                                    @php $p = DB::table("products")->where("productCode", $m->productCode)->get()->first(); 
                                        if($p!=null) echo $p->productName." (".$m->productCode.")";
                                    @endphp
                                @endif
                            </b>
                        </div>
                        <div class="s=req-qty">Quantity: <b>{{$m->qty}}</b></div>
                        <div class="s-req-content">Comment: {{$m->comment}}</div>
                        <div class="s-req-time">request time: {{date("d-m-Y g:i a", strtotime($m->created_at))}}</div>

                        @if($m->resText != (null||""))
                            <div class="s-req s-req-right">
                                <div class="s-req-usr">{{$m->memberCode}}</div>
                                <div class="s-req-content">{{$m->resText}}</div>
                                <div class="fr s-req-time">at {{date("d-m-Y g:i a", strtotime($m->response_at))}}</div>
                                <div class="clearfix"></div>
                            </div>
                        @endif

                        @if($m->resText == (null||""))
                            <a class="fr btn btn-default reply-btn btn-sm" onclick="showReply(this)">reply</a>
                            <div class="s-req-reply dnone">
                                <form action="{{url("notifSubmit")}}" method="post">
                                    <input type="hidden" name="requestId" value="{{$m->id}}">
                                    <input type="hidden" name="colMan" value="{{$colMan->userName}}">
                                    <textarea name="resText" class="form-control" id="" rows="4"></textarea>
                                    <button type="submit" class="btn btn-default btn-sm">Send</button>
                                    <a href="#" onclick="hideReply(this)" class="btn btn-default btn-sm">Cancel</a>
                                </form>
                            </div>
                        @endif
                        
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>
        @endforeach

        @if($i == 0)
            <div class="no-data">No Request</div>
        @endif

    </div>

    {{-- <div class="col-sm-12 col-md-6">
        <div class="req"></div>
    </div> --}}




    {{--
    <div class="table-back">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Request</th>
                    <th>Member Code</th>
                    <th>Member Name</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Aadhar Number</th>
                    <th>Address</th>
                    <th>Balance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($mem as $d) @php $bal = DB::table("user_current_balance")->where("ledgerId", $d->ledgerId)->get()->first(); 
@endphp

                <tr>
                    <td>
                        <a href="#" class="link" onclick="openrequestModel('{{$d->memberPersonalCode}}')" title="Send a Milk Request"> <i class="fa fa-mail-forward"></i></a>
                    </td>

                    <td>{{ $d->memberPersonalCode}}</td>
                    <td>{{ $d->memberPersonalName}}</td>
                    <td>{{ $d->memberPersonalEmail}}</td>
                    <td>{{ $d->memberPersonalMobileNumber}}</td>
                    <td>{{ $d->memberPersonalAadarNumber}}</td>
                    <td>{{ $d->memberPersonalAddress}}, {{ $d->memberPersonalState}}, {{ $d->memberPersonalCity }}</td>
                    <td>
                        <span class="bold {{$bal->openingBalanceType}}"> 
                            {{ number_format((float)str_replace("-","",$bal->openingBalance),2) }}
                        </span>
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div> --}}



</div>



<div class="wmodel clearfix" id="colManSelectModel" style="width: 85%;max-width:800px">
    <div class="close" onclick="closeColManModel(event)">X</div>
    <div class="wmodel-body">

        <h3 class="text-center">Select Collection Manager </h3>
        <hr>
        <input type="hidden" name="milkReqId" id="milkReqId" />
        <div class="col-sm-6 col-sm-offset-3">
            <label>Select Collection Manager</label>
            <select name="colMans" id="colMans" class="selectpicker">
                @foreach($colMans as $c)
                <option value="{{$c->userName}}">{{$c->userName}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-sm-12 text-center pt-20">
            <a href="#" role="button" class="btn btn-primary" id="assign-btn" onclick="assignReq(event)">Assign</a>
        </div>

    </div>
</div>


<script>
    $(document).ready(function(){

        $('[data-toggle="tooltip"]').tooltip(); 

        $('#MyTable').DataTable({
            initComplete: function (){
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                    //to select and search from grid
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        });
    
                    column.data().unique().sort().each( function (d, j) {
                        select.append( '<option value="'+d+'">'+d+'</option>')
                    });
                });
            }
        });


        // if({{$noMember}}){
        //     $.confirm({
        //         title: 'No Milk Request',
        //         content: 'There are no milk request.',
        //         type: 'orange',
        //         typeAnimated: true,
        //         buttons: {
        //             addMember: {
        //                 text: 'Ok',
        //                 btnClass: 'btn-orange',
        //                 action: function(){
        //                     // window.location = "{{url('memberSetupForm')}}";
        //                 }
        //             }
        //         }
        //     });
        // }
    });


    function showReply(e){
        $(e).siblings(".s-req-reply").show();
        $(e).hide();
    }
    function hideReply(e){
        $(e).closest(".s-req-reply").hide().siblings(".reply-btn").show();
        // $(e).closest(".reply-btn").show();
    }


    function colManSelect(i){
        $("#milkReqId").val(i);
        $("#colManSelectModel").fadeIn();
    }

    function closeColManModel(e){
        e.preventDefault();
        $("#milkReqId").val('');
        $("#colManSelectModel").fadeOut();
    }

    function assignReq(e){
        e.preventDefault();
        loader("show");
        milkReqId = $("#milkReqId").val();
        colMan = $("#colMans").val();


        console.log();

        $.ajax({
            type:"POST",
            url:'{{url('assignReq')}}',
            data: {
                colMan: colMan,
                milkReqId: milkReqId,
            },
            success:function(res){
                if(res.error){
                    $.alert("Some error has occurd at server.");
                }else{
                    $("#colManAssigned"+milkReqId).html(colMan);
                    closeColManModel(event);
                    $.alert(res.msg);
                }
            },
            error:function(res){
                $.alert("Something happened, Please try again.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    }

    function sendReq(e){
        e.preventDefault();
        loader("show");

        memCode = $("#memberCode").val();
        $.ajax({
            type:"POST",
            url:'sendReq',
            data: {
                memCode:memCode,
                date: $("#date").val(),
                shift: $("#dailyShift").val(),
                comment: $("#comment").val(),
            },
            success:function(res){
                if(res.error){
                    $.alert("Some error has occurd at server.");
                }else{
                    refreshData(memCode);
                    closereqModel(event);
                    $.alert(res.msg);                    
                }
            },
            error:function(res){

                $.alert("Something happened, Please try again.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    }

    function refreshData(memCode){
        loader("show");

        $.ajax({
            type:"POST",
            url:'getReqs',
            data: {
                memCode:memCode,
            },
            success:function(res){
                if(res.error){
                    $.alert("Some error has occurd at server.");
                }else{
                    $(".req").html(res.content);
                    $('html, body').animate({
                        scrollTop: $(".req").offset().bottom
                    }, 2000);
                }
            },
            error:function(res){
                $.alert("Something happened, Please try again.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    }

    $(".complete-btn, .decline-btn").on("click", function(){

        console.log("adfasd");
        reqid = $(this).data("completeid");
        action = $(this).data("action");
        loader("show");

        $.ajax({
            type:"POST",
            url:'requestComplete',
            data: {
                id:reqid,
                action: action
            },
            success:function(res){
                if(res.error){
                    $.alert("Some error has occurd at server.");
                }else{
                    if(action=="complete"){
                        $("#milkreq"+reqid).addClass("req-completed");
                        $("#milkreq"+reqid+" .complete-btn-area").html('<span class="label label-success">Completed</span>');
                    }else{
                        $("#milkreq"+reqid).addClass("req-decliend");
                        $("#milkreq"+reqid+" .complete-btn-area").html('<span class="label label-danger">Decliend</span>');
                    }
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 7000);
               }
            },
            error:function(res){
                $.alert("Something happened, Please try again.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });

    })


    $("#deliveryType").on("change", function(){
        console.log("skjdfhkhdsf");
        deliveryType = $(this).val();
        
        window.location.assign("{{url('prodRequest')}}"+"?deliveryType="+deliveryType);
    })
</script>
@endsection