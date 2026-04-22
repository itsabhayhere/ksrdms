@extends('plant.layout')

@section('content')

<div class="fcard margin-fcard-1 clearfix">
    <div class="container ">
        <center>
            <button type="button" class="btn btn-outline-primary btn-primary active" id="allMember">All Member</button>
            <button type="button" class="btn btn-outline-primary btn-primary" data-toggle="collapse"
                data-target="#filter1">Individual</button>
        </center>
    </div>


    <div class="upper-controls clearfix ">
        <div class="mb-20 clearfix collapse" id="filter1">
            <div class="col-sm-4 col-md-4 col-lg-3">
                <label> Society Code </label>
                <input type="text" id="society_code" class="form-control">
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
                <input type="button" name="getShiftSummary" value="Get" onclick="getShiftSummary();"
                    id="getShiftSummary" class=" form-control getButton btn-primary">
            </div>
        </div>

    </div>


    <div class="table-back">
            <input type="text" id='filename' style="display:none">

        <tr>
            <form action="{{url('/plant/shiftSummaryPdf')}}" method="get">
                <input type="submit" value="Getpdf" class="getPdfButton"></button>
            </form>
            <button type="button" id="pdfbtn" class="getPdfButton" style="display:none"
                onclick="getPdf();">GetPdf</button>

        </tr>
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
                    <td>{{$d->averageFat}}</td>
                    <td>{{$d->averageSnf}}</td>
                    <td>{{$d->MilkCollected}}</td>
                    <td>{{$d->averageFatCow}}</td>
                    <td>{{$d->averageSnfCow}}</td>
                    <td>{{$d->MilkCollectedCow}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
<script>
    $(document).ready(function(){
    
    var table= $("#MyTable").DataTable();
    $("#shift_date").datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
    // var societyCode="";
    // var shiftType="";
    // var shiftDate="";

});

$("#allMember").click(function(){

    $("#filter1").collapse('hide');
    $("#form").show();
    $("#society_code").val('');
    $('#dairyName').val("all");
   
    getShiftSummary();
    
});

function getSocietyCode(societyCode){

console.log(societyCode);
if(societyCode=="all"){
    $("#society_code").val("");
    return;
 
}
$("#society_code").val(societyCode);

}

    
        function getShiftSummary(){

            console.log('function calll');
              var shiftDate= $("#shift_date").val();
              var shiftType= $("#shift_type").val();
              var societyCode= $("#society_code").val();
              var society_name= $('#dairyName option:selected').text()
            
            console.log(societyCode);
            // return

            loader('show');

            $.ajax({

                type:"POST",
                url:"{{url('plant/shiftSummaryByDairy')}}",
                data:{

                    shiftDate:shiftDate,
                    shiftType:shiftType,
                    society_Code:societyCode,
                    societyName:society_name


                },
                headers:{

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                },
                success:function(res){
                    
                    console.log(res);
                    $('#MyTable').dataTable().fnClearTable();
                   $('#MyTable').dataTable().fnDestroy(); 
                   $("#MyTable").html(res.content);  

                    $("#MyTable").dataTable();
                    $("#filename").val(res.filename);

                    $("form").hide();
                    $("#pdfbtn").css({'display':'inline','float':'right'});

                   
                },error:function(res){

                    console.log(res);

                }
            }).done(function(){

                loader('hide');
            });

        }

        function getPdf(){

var filename= $("#filename").val();
console.log(filename);
console.log('{{url('')}}/'+filename);
window.location = '{{url('')}}/'+filename;
}
</script>
@endsection