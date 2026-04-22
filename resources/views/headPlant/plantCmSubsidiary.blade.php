@extends('headPlant.layout')

@section('content')
<style>
    .table {
        vertical-align: middle;
    }

    .spanspaced span {
        padding: 0 20px 0 0;
    }
</style>

<div class="fcard margin-fcard-1 pt-0 clearfix">

    <div class="upper-controls clearfix">
        <h4>Cm Subsidiary Report</h4>
        <div class="light-color f-12 total">Total: {{count($data)}}</div>
        <div id="cmSubsidiary" class="reportElm">

            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Select Plant </label>
                <select class="form-control" id="plant_id_input" onchange="getDairies(this.value);">
                    <option value="">All</option>
                    @foreach($plants as $p)
                    <option value="{{$p->id}}">{{$p->plantName." (".$p->plantCode.")"}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label> Society Name</label>
                <select class="form-control" id="societyName" onchange="getSocietyCode(this.value);">
                    <option value="">All</option>
                    @foreach($dairy as $d)
                    <option value="{{$d->society_code}}">{{$d->dairyName}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label> Society Code </label>
                <input type="text" name="societyCode" id="societyCode" onchange="getSocietyName(this.value)" class="societyCode form-control">
            </div>
            <div class="col-sm-3 pt-5">
                <label> Amount (3.5%-5.0%) </label>
                <input type="number" name="cmSubsidiaryAmountLow" id="cmSubsidiaryAmountLow" class="cmSubsidiaryAmountLow form-control" value="4">
            </div>
            <div class="col-sm-3 pt-5">
                <label> Amount (5.0% and more) </label>
                <input type="number" name="cmSubsidiaryAmountHigh" id="cmSubsidiaryAmountHigh" class="cmSubsidiaryAmountHigh form-control" value="5">
            </div>

            <div class="col-sm-3 pt-5">
                <label> Start Date </label>
                <input type="text" name="cmSubsidiaryStartDate" id="cmSubsidiaryStartDate" class="cmSubsidiaryStartDate form-control" value="{{$from}}">
            </div>
            <div class="col-sm-3 pt-5">
                <label> End Date </label>
                <input type="text" name="cmSubsidiaryEndDate" id="cmSubsidiaryEndDate" class="cmSubsidiaryEndDate form-control" value="{{$to}}">
            </div>
            <div class="col-sm-3 pt-5">
                <label> &nbsp; </label>
                <input type="button" name="getSaleData" value="Get" onclick="getCmSubsidiaryValueForTable(event);" id="getSaleData" class="getSaleData form-control getButton">
            </div>

            <form method="post" action="{{ url('/getCmSubsidiaryReportPdf') }}">
                <div id="cmSubsidiaryTableGetButtone">
                    <p></p>
                </div>
            </form>
            <div id="cmSubsidiaryTable"></div>
        </div>

    </div>

    <div class="table-back">
        {{-- <input type="text" id='filename' style="display:none">

            <tr >
                <form action="{{url('/plant/getCMSubsidiaryReportPdf')}}" method="get">
        <input type="submit" value="Getpdf" class="getPdfButton"></button>
        </form>
        <button type="button" id="pdfbtn" class="getPdfButton" style="display:none" onclick="getPdf();">GetPdf</button>

        </tr> --}}

        @php
        $qty1 = 0;
        $ft1 = 0;
        $am1 = 0;
        $qty2 = 0;
        $ft2 = 0;
        $am2 = 0;
        $tam = 0;

        @endphp

        <table class="table cmSubsidiaryReport-table tright table-bordered table-striped" id="MyTable">
            <thead>
                <tr>
                    <th colspan="13">
                        <span style="float:left">The Sirsa District Co-operative Milk Producers Union Limited.</span>
                        <span style="float:right">Period {{$from}} to {{$to}}</span>
                    </th>
                </tr>

                <tr>
                    <th rowspan="2">member Code.</th>
                    <th rowspan="2">Name Of Supplier</th>
                    <th rowspan="2">Aadhar No.</th>
                    <th rowspan="2">Name of Bank</th>
                    <th rowspan="2">IFSC Code</th>
                    <th rowspan="2">A/C. No.</th>
                    <th colspan="3">Qty Milk Received Containing Fat 3.5% to 5.0% rate of Subsidy 4/-p.kg.('A')</th>
                    <th colspan="3">Qty Milk Received Containing Fat above 5.0% rate of Subsidy 5/-p.kg.('B')</th>
                    <th rowspan="2">G. Total A+B</th>
                </tr>
                <tr>
                    <th>Qty</th>
                    <th>Fat</th>
                    <th>Amount</th>
                    <th>Qty</th>
                    <th>Fat</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1; @endphp
                @foreach($data as $d)
                @php
                $qty1 += (float)$d->qty35_50;
                $ft1 += (float)$d->fat35_50;
                $am1 += (float)$d->amount35_50;


                $qty2 += (float)$d->qty50__;
                $ft2 += (float)$d->fat50__;
                $am2 += (float)$d->amount50__;

                @endphp


                <tr>
                    {{-- <td>{{$dairyId}}</td> --}}
                    <td>{{ $d->memberCode }}</td>
                    <td>
                        {{$d->memberName}}
                    </td>
                    <td>
                        {{$d->adharNo}}
                    </td>
                    <td>
                        {{$d->bankName}}
                    </td>
                    <td>
                        {{$d->ifscCode}}
                    </td>
                    <td>
                        {{$d->accNo}}
                    </td>
                    <td>
                        {{number_format($d->qty35_50, 1, ".", "")}}
                    </td>
                    <td>
                        {{number_format($d->fat35_50, 1, ".", "")}}
                    </td>
                    <td>
                        {{number_format($d->amount35_50, 2, ".", "")}}
                    </td>
                    <td>
                        {{number_format($d->qty50__, 1, ".", "")}}
                    </td>
                    <td>
                        {{number_format($d->fat50__, 1, ".", "")}}
                    </td>
                    <td>
                        {{number_format($d->amount50__, 2, ".", "")}}
                    </td>
                    <td>
                        {{number_format($d->amount35_50 + $d->amount50__, 2, ".", "")}}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{number_format($qty1, 1, ".", "")}}</td>
                    <td>{{number_format($ft1, 1, ".", "")}}</td>
                    <td>{{number_format($am1, 2, ".", "")}}</td>
                    <td>{{number_format($qty2, 1, ".", "")}}</td>
                    <td>{{number_format($ft2, 1, ".", "")}}</td>
                    <td>{{number_format($am2, 2, ".", "")}}</td>
                    <td>{{number_format($am1+$am2, 2, ".", "")}}</td>

                </tr>
            </tfoot>

        </table>

    </div>

</div>
<script>
    var plant_name = "{{Session::get('plantInfo')->plantName}}";
    var plant_code = "{{Session::get('plantInfo')->plantCode}}";
    $(document).ready(function() {

        $("#MyTable").DataTable({

            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: 'Excel',
                    filename: "cm subsidairy report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ")cm subsidiary report",
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr('s', '42');
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    filename: "cm subsidiary Report",
                    orientation: 'landscape', //portrait
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ")cm subsidiary report",

                }
            ]

        });

        $("#cmSubsidiaryStartDate").datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $("#cmSubsidiaryEndDate").datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });

    function getSocietyCode(societyCode) {

        if (societyCode == "") {
            $("#societyCode").val("");
            return;
        }
        $("#societyCode").val(societyCode);

    }


    function getSocietyName(societyCode) {
        // societyCode = $(this).val(); 
        $('#societyName option').removeAttr("selected");
        $('#societyName option[value=' + societyCode + ']').attr("selected", "selected");
    }

    function getDairies(plant_id) {
        loader("show");

        $.ajax({
            type: "POST",
            url: "{{url('headPlant/getDairies_by_plant')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                plant_id: plant_id,
            },

            success: function(res) {
                elm = '<option value="">All</option>';
                $select = $("#societyName");
                $select.empty()

                $select.append($("<option></option>")
                    .attr("value", '').text("All"));

                $(res.dairies).each(function(i, dairy) {
                    $select.append($("<option></option>")
                        .attr("value", dairy.society_code).text(dairy.dairyName));
                })
                getSocietyCode('');
            },
            error: function(res) {
                $("noti-error").text("some error occcured").addClass('alert alert-danger').show();
                return;
            }
        }).always(function() {

            loader("hide");
        });
    }

    function getCmSubsidiaryValueForTable(event) {
        event.preventDefault()

        var amountLow = $("#cmSubsidiaryAmountLow").val();
        var amountHigh = $("#cmSubsidiaryAmountHigh").val();
        var startDate = $("#cmSubsidiaryStartDate").val();
        var endDate = $("#cmSubsidiaryEndDate").val();
        var plant_id = $("#plant_id_input").val();
        var societyCode = $("#societyCode").val();
        var society_name = $('#societyName option:selected').text();

        loader("show");
        $.ajax({
            type: "post",
            url: "{{url('headPlant/cm_subsidiaryByDairy')}}",
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },
            data: {

                amountLow: amountLow,
                amountHigh: amountHigh,
                startDate: startDate,
                endDate: endDate,
                society_Code: societyCode,
                plant_id:plant_id,
                societyName: society_name,

            },
            success: function(res) {
                loader("hide");

                $('#MyTable').dataTable().fnClearTable();
                $('#MyTable').dataTable().fnDestroy();
                $("#MyTable").html(res.content);

                var g = [];
                g['headings'] = res.headings;


                var table = $("#MyTable").dataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'excelHtml5',
                            text: 'Excel',
                            filename: "cm subsidiary report",
                            messageTop: "Dairy " + g['headings'].dairyName + "(" + g['headings'].society_code + ")" + g['headings'].report +
                                "Date [" + g['headings'].from + "-" + g['headings'].to + "]",
                            customize: function(xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row:first c', sheet).attr('s', '42');
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            filename: "cm subsidiary report",
                            orientation: 'landscape', //portrait
                            messageTop: "Dairy " + g['headings'].dairyName + "(" + g['headings'].society_code + ")" + g['headings'].report +
                                "Date [" + g['headings'].from + "-" + g['headings'].to + "]",

                        }
                    ]

                });
                $(".total").html("Total: " + table.fnGetData().length);
                // $("#filename").val(res.filename);
                // $("form").hide();
                // $("#pdfbtn").css({'display':'inline',
                //                     'float':'right'});
            },
            error: function(res) {
                // loader("hide");
                $("noti-error").text("some error occcured").addClass('alert alert-danger').show();
                return;

            }
        }).always(function() {

            loader("hide");
        });
    }

    function getPdf() {

        var filename = $("#filename").val();
        // console.log(filename);
        // console.log('{{url('')}}/'+filename);
        window.location = '{{url("")}}/' + filename;
    }
</script>
@endsection