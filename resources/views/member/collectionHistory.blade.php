@extends('member.layout') 
@section('content')

@php
    if (session()->has('dailyTransactionFromDate')) { $date = session()->get('dailyTransactionFromDate');}else{ $date = date("d-m-Y"); }
    if (session()->has('dailyTransactionToDate')) { $todate = session()->get('dailyTransactionToDate');}else{ $todate = date("d-m-Y");}
@endphp

<style type="text/css">
    .m-0 {
        margin-top: 0;
    }
    .errorMessage {
        color: red;
    }
</style>
<!-- <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet"> -->

<div class="span-fixed response-alert" id="response-alert"></div>

<div class="pageblur clearfix">

    <div class="fcard margin-fcard-1 pt-0 clearfix">
        <div class="heading">
            <div class="fl">
                <h3>Daily Transactions</h3>
                <hr class="m-0">        
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
                        <th>Shift</th>
                        <th>Milk Type</th>
                        <th>Quantity</th>
                        <th>Fat</th>
                        <th>Snf</th>
                        <th>Rate</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>

                <tbody class="table-transactions">

                </tbody>
            </table>
        </div>
    </div>

</div>


<script>

    var dairyId = '{{$mem->dairyId}}';

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

            // fetch_transactions_by_date(event);

        });


    $("#sdate, #tdate").on("dp.change", function(){
        // console.log($(this).val());
        fetch_transactions_by_date(this)
        changeUrl();
    })
    
    $("#sdailyShift").on("change", function(){
        // console.log($(this).val());
        fetch_transactions_by_date(this)
        changeUrl();
    })
    
    function changeUrl(){
        param = "date="+$("#sdate").val()+"&todate="+$("#tdate").val();
        if(document.location.href.indexOf('?') != -1) {
            var url = "{{ url('member/collectionHistory') }}"+"?"+param;
        }else{
            var url = "{{ url('member/collectionHistory') }}"+"?"+param;
        }
        window.history.pushState("data","Title",url);
    }

    function fetch_transactions_by_date(e){
        loader('show'); 

        fromdate = $("#sdate").val();
        todate = $("#tdate").val();

        $.ajax({
            type:"POST",
            url:'{{url("member/dailyTransactionListAjax")}}',
            data: {
                dairyId: dairyId,
                fromdate: fromdate,
                todate: todate,
            },
            success:function(res){
                loader("hide");
                $(".table-transactions").html(res);
                $("#trans-table").DataTable();
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