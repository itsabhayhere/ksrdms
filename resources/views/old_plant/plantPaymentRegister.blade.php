@extends('plant.layout')

@section('content')


<div class="fcard margin-fcard-1 pt-0 clearfix">

    <div class="upper-controls clearfix">

        {{-- <div class="fl"> --}}


        <div class="mb-20 clearfix">
            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Society Code </label>
                <input type="text" id="societyCode" class="form-control">
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
                <label> &nbsp; </label>
                <input type="button" name="getMemberList" value="Get" onclick="getPayment();" id="getPayment"
                    class=" form-control getButton btn-primary">
            </div>
        </div>

        {{-- <h3>Member List</h3> --}}


        {{-- <div class="light-color f-12">Total: {{count($members)}}</div> --}}

    {{-- </div> --}}


</div>

<div class="table-back">
    <input type="text" id='filename' style="display:none">

    <tr>
        <form action="{{url('/plant/paymentRegisterPdf')}}" method="get">
            <input type="submit" value="Getpdf" class="getPdfButton"></button>
        </form>
        <button type="button" id="pdfbtn" class="getPdfButton" style="display:none" onclick="getPdf();">GetPdf</button>

    </tr>
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
                <td>{{$d->memberName}}</td>
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
                <td>{{number_format($avFat/$i, 1)}}</td>
                @if($avSnf == 0)
                <td>-</td>
                @else
                <td>{{number_format($avSnf/$i, 0)}}</td>
                @endif
                <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{$total}}</td>

            </tr>
        </tfoot>

    </table>

</div>

</div>

<script>
    $(document).ready(function(){
    
        var table= $("#MyTable").DataTable();
    
    });
   
    
    
    function getSocietyCode(societyCode){

        console.log(societyCode);
        if(societyCode=="all"){
            $("#societyCode").val("");
            return;
            // $("socity")

        }
        $("#societyCode").val(societyCode);

    }


    function getPayment(){

        console.log("function call");
        var society_code= $("#societyCode").val();
        var society_name= $('#dairyName option:selected').text()

        loader("show");
        $.ajax({

            type:"POST",
            url:"{{url('plant/paymentByDairy')}}",
            headers:{

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                },
            data:{
                societyCode:society_code,
                societyName:society_name

            },

            success:function(res){
    //             $('#MyTable').dataTable().fnClearTable();
    //                $('#MyTable').dataTable().fnDestroy(); 
    //             $("#MyTable").html(res);
                
    //    var table= $("#MyTable").dataTable();
                
                console.log(res.content);
                $('#MyTable').dataTable().fnClearTable();
                $('#MyTable').dataTable().fnDestroy(); 
                $("#MyTable").html(res.content);  
                $("#MyTable").dataTable();
                $("#filename").val(res.filename);
                // console.log(res.filename);
                $("form").hide();
                $("#pdfbtn").css({'display':'inline',
                                    'float':'right'});
                
            },
            error:function(res){

                console.log(res);
            }
        }).done(function(){

            loader("hide");

        });
    }

    function getPdf(){

var filename= $("#filename").val();
console.log(filename);
console.log('{{url('')}}/'+filename);
window.location = '{{url('')}}/'+filename;
}


// google.maps.event.addDomListener(window, 'load', initialize);

</script>

@endsection