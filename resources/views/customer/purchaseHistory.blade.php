@extends('customer.layout') 
@section('content')

@php
    if (session()->has('purchaseHistoryFromDate')) { $date = session()->get('purchaseHistoryFromDate');}else{ $date = date("d-m-Y"); }
    if (session()->has('purchaseHistoryToDate')) { $todate = session()->get('purchaseHistoryToDate');}else{ $todate = date("d-m-Y");}
@endphp

<style type="text/css">
    .m-0 {
        margin-top: 0;
    }

    .errorMessage {
        color: red;
    }

</style>

<div class="span-fixed response-alert" id="response-alert"></div>

<div class="pageblur clearfix">

    <div class="fcard margin-fcard-1 pt-0 clearfix">
        <div class="upper-controls pt-0 clearfix">
            <div class="fl">
                <h3>Purchase History</h3>
                <hr class="m-0">        
            </div>

            <div class="fr pt-10">
                <div class="fr" style="font-size: 26px;color: #003b86;">
                    <span style="font-size: 18px;font-weight: 100;">Balance</span>
                    &#8377; {{$curBal->openingBalance}}
                    <small style="font-size:18px;">
                            @if($curBal->openingBalanceType == "credit") Cr. @endif
                            @if($curBal->openingBalanceType == "debit") Dr. @endif        
                    </small>
                </div>
                <a href="#" class="fr pt-10 info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            </div>
        </div>

        <div class="clearfix">

                <div class="col-md-12 clearfix" style="background: #f6f7f9;padding: 10px 15px;border: 1px solid #dedede;">
                    <div class="col-sm-12 col-md-12">
                        <div class="col-sm-6 col-md-3">
                            <label>From</label>
                            <input type="text" class="form-control" id="sdate" placeholder="From" value="{{date("d-m-Y", strtotime($date))}}" name="fromdate"
                                    autofocus autocomplete="off">
                        </div>

                        <div class="col-sm-6 col-md-3">
                            <label>To</label>
                            <input type="text" class="form-control" id="tdate" placeholder="To" value="{{date("d-m-Y", strtotime($todate))}}" name="todate"
                                    autofocus autocomplete="off">
                        </div>
                    </div>

                </div>
        </div>

    </div>

    <div class="clearfix">
        <div class="table-back ">
            <table id="trans-table" class="display table-bordered tright" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price Per Unit</th>
                        <th>Amount Type</th>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>Final Amount</th>
                        <th>Paid Amount</th>
                    </tr>
                </thead>

                <tbody class="table-transactions">

                </tbody>
            </table>
        </div>
    </div>

</div>


<script>

    var dairyId = '{{$cust->dairyId}}';

        $(document).ready(function() {
            table = $('#trans-table').DataTable({
                        // "ajax": {
                        //     url     : '{{url("member/DailyTransactionListAjax")}}',
                        //     type    : "POST",
                        // },
                        // "columnDefs": [{
                        //         "targets": [ 0 ],
                        //         "visible": false,
                        //         "searchable": false,
                        //         // "bSortable": true,
                        //         // "iDataSort":1
                        //     },
                        //     // { "width": "52px", "targets": 4 },
                        // ]
                    });

            // fetch_purchaseHistory_by_date(event);

        });


    $("#sdate, #tdate").on("dp.change", function(){
        // console.log($(this).val());
        fetch_purchaseHistory_by_date(this)
        changeUrl();
    })
    
    function changeUrl(){
        param = "date="+$("#sdate").val()+"&todate="+$("#tdate").val();
        if(document.location.href.indexOf('?') != -1) {
            var url = "{{ url('customer/purchaseHistory') }}"+"?"+param;
        }else{
            var url = "{{ url('customer/purchaseHistory') }}"+"?"+param;
        }
        window.history.pushState("data","Title",url);
    }

    function fetch_purchaseHistory_by_date(e){
        loader('show'); 

        fromdate = $("#sdate").val();
        todate = $("#tdate").val();

        $.ajax({
            type:"POST",
            url:'{{url("customer/purchaseHistoryListAjax")}}',
            data: {
                dairyId: dairyId,
                fromdate: fromdate,
                todate: todate,
            },
            success:function(res){
                loader("hide");
                $(".table-transactions").html(res);
            },
            error:function(res){
                loader("hide");
                console.log(res);
            }
        });
    }

	/* date picker */
    $(function () {
        $('#sdate, #tdate').datetimepicker({
             format: 'DD-MM-YYYY'
        });
    });


</script>

@endsection