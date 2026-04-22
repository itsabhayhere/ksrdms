@extends('plant.layout')


@section('content')

<div class="fcard margin-fcard-1 pt-0 clearfix">

    <div class="upper-controls clearfix">

        {{-- <div class="fl"> --}}

        <h4>Member Report</h4>
        <div class="light-color f-12 total">Total: {{count($members)}}</div>
        <div class="mb-20 clearfix">

            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Select Dairy </label>
                <select class="form-control" id="dairyName" onchange="getSocietyCode(this.value);">
                    <option value="all">All</option>
                    @foreach($dairy as $d)
                    <option value="{{$d->society_code}}" {{($d->society_code == $dairy_code)? 'selected':''}}>{{$d->dairyName}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Society Code </label>
                <input type="text" id="societyCode" value="{{$dairy_code}}" class="form-control" onchange="getSocietyName(this.value)">
            </div>
            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> &nbsp; </label>
                <input type="button" name="getMemberList" value="Get" onclick="getMemberList();" id="getMemberList" class=" form-control getButton btn-primary">
            </div>
        </div>

    </div>



    {{-- <div class="table-back">
    <form action="{{url('/plant/getAllMemberReportPdf')}}" method="get">
        <input type="submit" class="getPdfButton" value="PDF">
    </form>

    <form action="{{url('/plant/getAllMemberReportPdf')}}" method="get">
        <input type="submit" class="getPdfButton" value="Excel">
    </form>

</div> --}}

<div class="table-back">

    <input type="text" id='filename' style="display:none">

    <table id="MyTable" class="table table-bordered" cellspacing="0" width="100%">

        <thead>

            <tr>
                <th>Society Code</th>
                <th>Member Code</th>

                <th>Member Name</th>
                <th>Father Name</th>

                <th>Mobile Number</th>

                <th>Aadhar Number</th>

                <th>Bank Name & Branch</th>

                <th>IFSC Code</th>

                <th>Bank Account Number</th>

                <th>Name of A/C Holder</th>

            </tr>

        </thead>



        <tbody>



            @foreach ($members as $d)




            <tr>
                <td>{{$d->society_code}}</td>
                <td>{{ $d->memberPersonalCode}}</td>

                <td>{{ $d->memberPersonalName}}</td>
                <td>{{ $d->memberPersonalFatherName}}</td>


                <td>{{ $d->memberPersonalMobileNumber}}</td>

                <td>{{ $d->memberPersonalAadarNumber}}</td>

                <td>{{$d->memberPersonalBankName}}</td>
                <td>{{$d->memberPersonalIfsc}}</td>
                <td>{{$d->memberPersonalAccountNumber}}</td>
                <td>{{$d->memberPersonalAccountName}}</td>
                </td>


                @endforeach
            </tr>


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
                    filename: "member report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Member Report",
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr('s', '42');
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    filename: "member report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Member Report ",

                }
            ]
        });



    });

    function getSocietyCode(societyCode) {

        console.log(societyCode);
        if (societyCode == "all") {
            $("#societyCode").val("");
            return;
        }
        $("#societyCode").val(societyCode);

    }


    function getSocietyName(societyCode) {
        // societyCode = $(this).val(); 
        $('#dairyName option').removeAttr("selected");
        $('#dairyName option[value="' + societyCode + '"]').attr("selected", "selected");
    }



    function getMemberList() {

        var society_code = $("#societyCode").val();
        var society_name = $('#dairyName option:selected').text();

        loader("show");
        if (society_code == "" || society_code == null) {

            var temp = "{{'allmember'}}";
            $(location).attr('href', temp);
            return;
        }
        $.ajax({

            type: "POST",
            url: "{{url('plant/memberByDairy')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                societyCode: society_code,
                societyName: society_name
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
                            filename: "member report",
                            messageTop: "Dairy " + g['headings'].dairyName + "(" + g['headings'].society_code + ")" + g['headings'].report,
                            customize: function(xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row:first c', sheet).attr('s', '42');
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            filename: "member report",
                            messageTop: "Dairy " + g['headings'].dairyName + "(" + g['headings'].society_code + ")" + g['headings'].report,
                            orientation: 'landscape', //portrait

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
                $("noti-error").text("some error occcured").addClass('alert alert-danger').show();
                return;
            }
        }).always(function() {

            loader("hide");
        });
    }

    function getPdf() {

        var filename = $("#filename").val();
        var url = '{{url("")}}/' + filename;

        window.open(url, '_blank');
    }
</script>

@endsection