@extends('headPlant.layout')

@section('content')

<div class="fcard margin-fcard-1 pt-0 clearfix">

    <div class="upper-controls clearfix">

        <h4>Member Report</h4>
        <div class="light-color f-12 total">Total: {{count($members)}}</div>

        <div class="mb-20 clearfix">

            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Select Plant </label>
                <select class="form-control" id="plant_id_input" onchange="getDairies(this.value);">
                    <option value="all">All</option>
                    @foreach($plants as $p)
                    <option value="{{$p->id}}">{{$p->plantName." (".$p->plantCode.")"}}</option>
                    @endforeach
                </select>
            </div>

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
                <input type="text" id="societyCode"  onchange="getSocietyName(this.value)"  class="form-control">
            </div>
            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> &nbsp; </label>
                <input type="button" name="getMemberList" value="Get" onclick="getMemberList();" id="getMemberList" class=" form-control getButton btn-primary">
            </div>
        </div>

    </div>

    <div class="table-back">
        {{-- <button type="button" class=" btn btn-primary" id="excel" onclick="exportTableToExcel('MyTable');">Excel</button> --}}
        {{-- <input type="text" id='filename' style="display:none">

<tr >
    <form action="{{url('/headPlant/getAllMemberReportPdf')}}" method="get">
        <input type="submit" value="Getpdf" class="getPdfButton"></button>
        </form>
        <button type="button" id="pdfbtn" class="getPdfButton" style="display:none" onclick="getPdf();">GetPdf</button>

        </tr> --}}
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
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Member Report report",
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr('s', '42');
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    filename: "member report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Member Report report",

                }
            ]
        });




    });

    function getSocietyCode(societyCode) {

        if (societyCode == "all") {
            $("#societyCode").val("");
            return;
        }
        $("#societyCode").val(societyCode);

    }
    
    function getSocietyName(societyCode) {
        // societyCode = $(this).val(); 
        $('#dairyName option').removeAttr("selected");
        $('#dairyName option[value=' + societyCode + ']').attr("selected", "selected");
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
                elm = '<option value="all">All</option>';
                $select = $("#dairyName");
                $select.empty()

                $select.append($("<option></option>")
                    .attr("value", 'all').text("All"));

                $(res.dairies).each(function(i, dairy) {
                    $select.append($("<option></option>")
                        .attr("value", dairy.society_code).text(dairy.dairyName));
                })
                getSocietyCode('all');
            },
            error: function(res) {
                $("noti-error").text("some error occcured").addClass('alert alert-danger').show();
                return;
            }
        }).done(function() {

            loader("hide");
        });
    }


    function getMemberList() {

        var society_code = $("#societyCode").val();
        var society_name = $('#dairyName option:selected').text();
        var dairyName = "first";


        loader("show");

        if (society_code == "" || society_code == null) {

            var url = "{{url()->current()}}";
            $(location).attr('href', url);
            return;

        }
        $.ajax({

            type: "POST",
            url: "{{url('headPlant/memberByDairy')}}",
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
                loader("hide");
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