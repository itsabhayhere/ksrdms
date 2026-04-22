@extends('theme.webview')

@section('content')
@php
    if (session()->has('paymentFromDate')) { $date = session()->get('paymentFromDate');}else{ $date = date("d-m-Y"); }
    if (session()->has('paymentToDate')) { $todate = session()->get('paymentToDate');}else{ $todate = date("d-m-Y");}
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

    <div class="fcard margin-fcard-1 p-0 clearfix">
        <div class="upper-controls pt-0 clearfix">
            <div class="fl">
                <h3>Statement Report</h3>
                <hr class="m-0">        
            </div>

            <div class="fr pt-10">
                <div class="fr" style="font-size: 26px;">
                </div>
                <a href="#" class="fr pt-10 info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            </div>
        </div>

        <div class="clearfix">
            <div class="col-sm-5 col-md-3">
                <label>From</label>
                <input type="text" class="form-control" id="sdate" placeholder="From" value="{{date("d-m-Y")}}" name="fromdate"
                        autocomplete="off">
            </div>

            <div class="col-sm-5 col-md-3">
                <label>To</label>
                <input type="text" class="form-control" id="tdate" placeholder="To" value="{{date("d-m-Y")}}" name="todate"
                        autocomplete="off"> 
            </div>

            <div class="col-sm-2">
                <label> &nbsp; </label>
                <br>
                <input type="button" name="getStatementBtn" value="Get" onclick="getMemStatementReport(this);" id="getStatementBtn" class="btn btn-primary">    
            </div>
        </div>
    
        <div class="table-data-area">
            <div id="table-data" class="mt-10 clearfix"></div>    
        </div>
            
    </div>

        
<script>

    $(function () {
            $('#sdate, #tdate').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    });


    function getMemStatementReport() {
        loader("show");

        var dairyId = $("#dairyId").val();
        var status = $("#status").val();
        
        var memberCode = $("#memberCode").val();
        var memberName = $("#memberNameSt").val();
        var startDate = $("#sdate").val();
        var endDate = $("#tdate").val();
        var groupByDate = $("#groupByDate").val();

        $.ajax({
            type:"post",
            url:'{{url('api/memStatementListAjax')}}',
            data: {
                device_token: "{{$device_token}}",
                dairyId: dairyId,
                status: status,
                memberCode: memberCode,
                memberName: memberName,
                startDate: startDate,
                endDate: endDate,
                groupByDate: groupByDate
            },
            success:function(res){
                loader("hide");

                $("#table-data").html(res.content).addClass("table-back");
                g['headings'] = res.headings
                table = $('.memStatement-table').DataTable({
                    "dom": '<"dt-buttons"Bf><"clear">lirtp',
                    "paging": true,
                    "autoWidth": true,
                    "buttons": [{
                            text: 'PDF',
                            footer: true,
                            extend: 'pdfHtml5',
                            filename: res.headings.report ,
                            orientation: 'portrait', //landscape
                            pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                            exportOptions: {
                                columns: ':visible',
                                search: 'applied',
                                order: 'applied'
                            },
                            customize: function (doc) {
                                g["headings"].text = "\n from "+res.headings.from+" to "+res.headings.to;                                
                                return pdfFunction(doc);
                            }
                    }, 
                    {
                        extend: 'excel',
                        footer: true,
                        messageTop: g['headings'].dairyName + "("+g['headings'].society_code+") \n "+ g['headings'].report+" \n ",
                    }],
                });
            }
        }).done(function(res){
                console.log(res);
                loader("hide");
            });
    }
    </script>

    @endsection