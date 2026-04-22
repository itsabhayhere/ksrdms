@extends('plant.layout')

@section('content')

<div class="fcard margin-fcard-1 clearfix">
    <h4>Shift Summary Report</h4>
    <div class="light-color f-12 total">Total: {{count($data1)}}</div>
    <div class="text-center">
        <button type="button" class="btn btn-outline-primary btn-primary active" id="allMember">All Member</button>
        <button type="button" class="btn btn-outline-primary btn-primary" data-toggle="collapse" data-target="#filter1">Individual</button>
    </div>


    <div class="upper-controls clearfix ">




        <div class="mb-20 clearfix collapse" id="filter1">

            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Select Dairy </label>
                <select class="form-control" id="dairyName" onchange="getSocietyCode(this.value);">
                    <option value="all">All</option>
                    @foreach($dairy as $d)
                    <option value="{{$d->society_code}}">{{$d->dairyName}}</option>
                    @endforeach
                </select>

            </div>
            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Society Code </label>
                <input type="text" id="society_code" class="form-control">
            </div>


            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Shift </label>
                <select class="form-control" id="shift_type">
                    <option value="morning">Morning</option>
                    <option value="evening">Evening</option>

                </select>

            </div>

            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Date </label>
                <input type="text" name="date" class="form-control" id="shift_date">
            </div>

            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> &nbsp; </label>
                <input type="button" name="getShiftSummary" value="Get" onclick="getShiftSummary();" id="getShiftSummary" class=" form-control getButton btn-primary">
            </div>

        </div>

    </div>


    <div class="table-back">
        <table id="MyTable" class="table table-bordered" cellspacing="0" width="100%">
            <thead>

                <tr>
                    <th>Name & Code of society</th>
                    <th>Cow Milk Collected</th>
                    <th>Average FAT Cow</th>
                    <th>Average SNF Cow</th>
                    <th>Buff Milk Collected</th>
                    <th>Average FAT Buff</th>
                    <th>Average SNF Buff</th>
                </tr>
            </thead>
            <tbody>

                @foreach($data1 as $d)
                <tr>
                    <td>{{$d->society_code}}</td>
                    <td>{{number_format($d->MilkCollectedCow, 1, ".", "")}}</td>
                    <td>{{number_format($d->averageFatCow, 1, ".", "")}}</td>
                    <td>{{number_format($d->averageSnfCow, 1, ".", "")}}</td>
                    <td>{{number_format($d->MilkCollected, 1, ".", "")}}</td>
                    <td>{{number_format($d->averageFat, 1, ".", "")}}</td>
                    <td>{{number_format($d->averageSnf, 1, ".", "")}}</td>

                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
<script>
    var plant_name = "{{Session::get('plantInfo')->plantName}}";
    var plant_code = "{{Session::get('plantInfo')->plantCode}}";
    $(document).ready(function() {

        var table = $("#MyTable").DataTable({

            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: 'Excel',
                    filename: "Shift Summary Report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Shift Summary Report",
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr('s', '42');
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    filename: "Shift Summary Report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Shift Summary Report ",

                }
            ]
        });
        $("#shift_date").datetimepicker({
            format: 'YYYY-MM-DD'
        });

        // var societyCode="";
        // var shiftType="";
        // var shiftDate="";

    });

    $("#allMember").click(function() {

        $("#filter1").collapse('hide');
        $("#form").show();
        $("#society_code").val('');
        $('#dairyName').val("all");
        s

        // getShiftSummary();

    });

    function getSocietyCode(societyCode) {

        console.log(societyCode);
        if (societyCode == "all") {
            $("#society_code").val("");
            return;

        }
        $("#society_code").val(societyCode);

    }


    function getShiftSummary() {

        var shiftDate = $("#shift_date").val();
        var shiftType = $("#shift_type").val();
        var societyCode = $("#society_code").val();
        var society_name = $('#dairyName option:selected').text()

        loader('show');
        if (societyCode == "" || societyCode == null) {

            var url = "{{url('plant/shift_summary')}}";
            $(location).attr('href', url);
            return;
        }

        $.ajax({

            type: "POST",
            url: "{{url('plant/shiftSummaryByDairy')}}",
            data: {

                shiftDate: shiftDate,
                shiftType: shiftType,
                society_Code: societyCode,
                societyName: society_name


            },
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },
            success: function(res) {


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
                            filename: "shift summary report",
                            messageTop: "Dairy " + g['headings'].dairyName + "(" + g['headings'].society_code + ")" + g['headings'].report,
                            customize: function(xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row:first c', sheet).attr('s', '42');
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            filename: "shift summary report",
                            messageTop: "Dairy " + g['headings'].dairyName + "(" + g['headings'].society_code + ")" + g['headings'].report,

                        }
                    ]
                });
                $(".total").html("Total: " + table.fnGetData().length);
                // $("#MyTable").dataTable();
                // $("#filename").val(res.filename);

                // $("form").hide();
                // $("#pdfbtn").css({'display':'inline','float':'right'});


            },
            error: function(res) {

                loader("hide");
                $("noti-error").text("some error occcured").addClass('alert alert-danger').show();
                return;
            }
        }).done(function() {

            loader('hide');
        });

    }

    function getPdf() {


        var filename = $("#filename").val();
        var url = '{{url("")}}/' + filename;

        window.open(url, '_blank');
    }
</script>
@endsection