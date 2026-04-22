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
                <input type="button" name="getMemberList" value="Get" onclick="getMemberList();" id="getMemberList"
                    class=" form-control getButton btn-primary">
            </div>
        </div>

    </div>
        {{-- <h3>Member List</h3> --}}


        {{-- <div class="light-color f-12">Total: {{count($members)}}</div> --}}

    {{-- </div> --}}



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

<tr >
    <form action="{{url('/plant/getAllMemberReportPdf')}}" method="get">
    <input type="submit" value="Getpdf" class="getPdfButton" ></button>
    </form>
    <button type="button" id="pdfbtn" class="getPdfButton" style="display:none" onclick="getPdf();" >GetPdf</button>
    
</tr>
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



    <tbody >



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
    $(document).ready(function(){
    
    console.log('fjgjf');
       var table= $("#MyTable").DataTable();
     
    //  $('#MyTable').on( 'page.dt', function () {
    //     var info = table.page.info();
  
    //      console.log('Showing page: '+info.page+' of '+info.pages);
    // } );


    });
    
    function getSocietyCode(societyCode){

        console.log(societyCode);
        if(societyCode=="all"){
            $("#societyCode").val("");
            return;
        }
        $("#societyCode").val(societyCode);

    }


    function getMemberList(){

        console.log("function call");
        var society_code= $("#societyCode").val();
        var society_name= $('#dairyName option:selected').text();

        loader("show");
     
        $.ajax({

            type:"POST",
            url:"{{url('plant/memberByDairy')}}",
            headers:{

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                },
            data:{
                societyCode:society_code,
                societyName:society_name
            },

            success:function(res){

               
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
    
 
 

</script>

@endsection