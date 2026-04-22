@extends('headPlant.layout')

@section('content')


<div class="fcard margin-fcard-1 pt-0 clearfix">

    <div class="upper-controls clearfix">

        <h4>Payment Register Report</h4>
        <div class="light-color f-12 total">Total: {{count($dailyTrns)}}</div>

        <div class="mb-20 clearfix">

            <div class="row">
                <div class="col-12">
                    <form action="{{url('headPlant/payment_register')}}" method="get" style="width:100%">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <label for="from_date">From</label>
                                <input type="text" name="from_date" id="from_date" class="form-control datepicker" value="{{$from_date}}">
                            </div>
                            <div class="col-md-3">
                                <label for="to_date">To</label>
                                <input type="text" name="to_date" id="to_date" class="form-control datepicker" value="{{$to_date}}">
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">

                            <div class="col-sm-4 col-md-4 col-lg-3">
                                <label> Select Plant </label>
                                <select class="form-control" name="plant_id" id="plant_id_input" onchange="getDairies(this.value);">
                                    <option value="all">All</option>
                                    @foreach($plants as $p)
                                    <option value="{{$p->id}}" {{($p->id == $plant_id)? 'selected':''}}>{{$p->plantName." (".$p->plantCode.")"}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-4 col-md-4 col-lg-3">
                                <label> Select Dairy </label>
                                <select class="form-control" name="dairy_code" id="dairyName" onchange="getSocietyCode(this.value);">
                                    <option value="all">All</option>
                                    @foreach($dairy as $d)
                                    <option value="{{$d->society_code}}" {{($d->society_code == $dairy_code)? 'selected':''}}>{{$d->dairyName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-3">
                                <label> Society Code </label>
                                <input type="text" id="societyCode" value="{{$dairy_code}}" onchange="getSocietyName(this.value)" class="form-control">
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-3">
                                <label> &nbsp; </label>
                                <input type="submit" name="getMemberList" value="Get" id="getPayment" class=" form-control getButton btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- <h3>Member List</h3> --}}


        {{-- <div class="light-color f-12">Total: {{count($members)}}
    </div> --}}

    {{-- </div> --}}


</div>

<div class="table-back">
    <input type="text" id='filename' style="display:none">

    {{-- <tr>
        <form action="{{url('/headPlant/paymentRegisterPdf')}}" method="get">
    <input type="submit" value="Getpdf" class="getPdfButton"></button>
    </form>
    <button type="button" id="pdfbtn" class="getPdfButton" style="display:none" onclick="getPdf();">GetPdf</button>

    </tr> --}}
    <table id="MyTable" class="table table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Society Code</th>
                <th>Member Name</th>
                <th>Total Samples Poured</th>
                <th>Qty</th>
                <th>Avg Fat</th>
                <th>Avg SNF</th>
                <th>Total Amount</th>

            </tr>
        </thead>


        <tbody>

            @php
            $total = 0;
            $qty = 0;
            $avFat = 0;
            $avSnf = 0;
            $shift = 0;
            $i = 0;
            @endphp
            @foreach($dailyTrns as $d)
            @php
            $i++;
            $total += $d->amount;
            $qty += (float)$d->qty;
            $shift += $d->noOfShift;
            $avSnf += $d->snf;
            $avFat += $d->fat;
            @endphp
            <tr>
                <td>{{$d->society_code}}</td>
                <td>{{$d->memberName." (".$d->memberCode.")"}}</td>
                <td>{{$d->noOfShift}}</td>
                <td>{{number_format($d->qty, 1, ".", "")}}</td>
                <td>{{number_format($d->fat, 1, ".", "")}}</td>
                @if($d->snf == 0)
                <td>-</td>
                @else
                <td>{{number_format($d->snf, 0, ".", "")}}</td>
                @endif
                <td>{{number_format($d->amount, 2, ".", "")}}</td>

            </tr>
            @endforeach



        </tbody>

        <tfoot>
            <tr style="font-weight:bolder;background: #e4e4e4;">
                <td>Total <small class="text-grey">(Comlete Report Payment)</small></td>
                <td></td>
                <td>{{$shift}}</td>
                <td>{{number_format($qty, 1, ".", "")}}</td>
                <td>{{($i>0)?number_format($avFat/$i, 1):'0'}}</td>
                @if($avSnf == 0)
                <td>-</td>
                @else
                <td>{{($i>0)?number_format($avSnf/$i, 0):'0'}}</td>
                @endif
                <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{$total}}</td>

            </tr>
        </tfoot>

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
                    filename: "Payment Register Report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Payment Register Report",
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row:first c', sheet).attr('s', '42');
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    filename: "Payment Register Report",
                    messageTop: "My Plant " + plant_name + "(" + plant_code + ") Payment Register Report",

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

    // function getPayment(e){
    //     e.preventDefault()
    //     var society_code= $("#societyCode").val();
    //     var society_name= $('#dairyName option:selected').text()

    //     if(society_code==""||society_code==null){

    //         var url= "{{url()->current()}}";
    //         $(location).attr('href',url);
    //         return;
    //     }
    //     loader("show");
    //     $.ajax({

    //         type:"POST",
    //         url:"{{url('headPlant/paymentByDairy')}}",
    //         headers:{

    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

    //             },
    //         data:{
    //             societyCode:society_code,
    //             societyName:society_name

    //         },

    //         success:function(res){


    //             $('#MyTable').dataTable().fnClearTable();
    //             $('#MyTable').dataTable().fnDestroy(); 
    //             $("#MyTable").html(res.content);  
    //             var g=[];
    //             g['headings']=res.headings;
    //             var table= $("#MyTable").dataTable({

    //                 dom: 'Bfrtip',
    //          buttons: [
    //         {
    //         extend: 'excelHtml5',
    //         text: 'Excel',
    //         filename:"payment register report",
    //         messageTop: "Dairy "+ g['headings'].dairyName +"("+ g['headings'].society_code+")"+g['headings'].report,
    //         customize: function( xlsx ) {
    //             var sheet = xlsx.xl.worksheets['sheet1.xml'];
    //             $('row:first c', sheet).attr( 's', '42' );
    //         }
    //     },
    //         {
    //         extend: 'pdf',
    //         text: 'PDF',
    //         filename:"Payment Register Report",
    //         messageTop: "Dairy "+ g['headings'].dairyName +"("+ g['headings'].society_code+")"+g['headings'].report,

    //         }
    //     ]
    //             });
    //             $(".total").html("Total: "+ table.fnGetData().length);
    //             // $("#filename").val(res.filename);
    //             // $("form").hide();
    //             // $("#pdfbtn").css({'display':'inline',
    //             //                     'float':'right'});

    //         },
    //         error:function(res){

    //             loader("hide");
    //             $("noti-error").text("some error occcured").addClass('alert alert-danger').show();
    //             return;
    //         }
    //     }).done(function(){

    //         loader("hide");

    //     });
    // }

    function getPdf() {

        var filename = $("#filename").val();
        // console.log(filename);
        // console.log('{{url('')}}/'+filename);
        window.location = '{{url("")}}/' + filename;
    }


    // google.maps.event.addDomListener(window, 'load', initialize);
</script>

@endsection