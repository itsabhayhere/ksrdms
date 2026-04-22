@extends('headPlant.layout')


@section('content')
<style type="text/css">
        .input-error{
    
            border: 2px solid red;
    
            /* display: none; */
        }
    </style>
    <div class="fcard margin-fcard-1 clearfix">
        
            <div class="upper-controls clearfix">

                 
            
                    <h4>Dairy</h4>
                    <div class="light-color f-12">Total: {{count($dairies)}}</div>
                    <div class="mb-20 clearfix">
                        
                        <div class="col-sm-4 col-md-4 col-lg-3">
                            <label> Select Plant </label>
                            <select class="form-control" id="plantName" onchange="getPlantCode(this.value);">
                                <option value="all">All</option>
                                @foreach($plants as $p)
                                <option value="{{$p->plantCode}}">{{$p->plantName}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-3">
                            <label> Plant Code </label>
                            <input type="text" id="plantCode" class="form-control">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-3">
                            <label> &nbsp; </label>
                            <input type="button" name="getDairyList" value="Get" onclick="getDairyList();" id="getDairyList"
                                class=" form-control getButton btn-primary">
                        </div>
                    </div>
                    </div>
            
       
    
        
        <div class="table-back">
            <table id="MyTable" class="display table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Society Code</th>
                        <th>Society Name </th>
                        <th>Society Contact</th>
                        <th>Address</th>
                        <th>Dairy Propritor </th>
                        <th>Propritor Contact</th>
                    
                    </tr>
                </thead>
        
                <tbody id="searchdairy">
        
                    @foreach ($dairies as $r)
                        <tr class="dairyReq{{$r->id}}">
                            <td>{{ $r->society_code}}</td>
                            <td>{{ $r->dairyName}}</td>
                            <td>{{ $r->mobile}}</td>
                            <td>{{ $r->dairyAddress}}, {{ $r->state}}, {{ $r->city}}</td>
                            <td>{{ $r->dairyPropritorName}}</td>
                            <td>{{ $r->PropritorMobile.", ".$r->dairyPropritorEmail}}</td>    
                                {{-- <a href="javascript:void(0);" onclick="completeRequest({{ $r->dairyId}}, 'Accept');" class="btn btn-default btn-sm"> Accept </a> --}}
                                {{-- <a href="javascript:void(0);" onclick="completeRequest({{ $r->dairyId}}, 'Decline');" class="btn btn-danger btn-sm"> Remove </a> --}}
                        </tr>
                    @endforeach
        
                </tbody>
            </table>
        </div>
    </div>

    <script>
    
$(document).ready(function() {
    $('#MyTable').DataTable();
});
function getPlantCode(plantCode){
if(plantCode=="all"){
    $("#plantCode").val("");
    return;
}
$("#plantCode").val(plantCode);

}

    
function getDairyList(){

var plant_code= $("#plantCode").val();
var plant_name= $('#plantName option:selected').text();

loader("show");
if(plant_code==""||plant_code==null){

    var temp="{{url()->current()}}";
    $(location).attr('href',temp);
    return;
}     

        $.ajax({

            type:"GET",
            url:'{{url("headPlant/dairyByPlantId")}}',
            headers:{

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },
            data:{

                plant_code:plant_code
            },
            success:function(res){
                $(this).removeClass('input-error').addClass('form-control');
               

                $("#searchdairy").html(res);
                
               
            },
            error:function(res){
                $.alert("Something is going wrong. check your internet.");
            }
        }).done(function(res){
            loader("hide");
         
        });
    }
</script>
@endsection