@extends('theme.default')

@section('content')

<style>
        a.btn.active{
            background-color: #329df9!important;
            color: white;
            font-weight: 1000;
        }
</style>
        
<div class="fcard margin-fcard-1 clearfix">
   
    <div class="col-md-12">
        <h3>Add plant</h3>
        <form action="{{url('milkPlantAddRequest')}}" method="POST">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <label>Select Main Plant</label>
                <select name="mainPlant" id="mainPlant" class="selectpicker" data-live-search="true" title="Select Main plant to add" onchange="getChildPlantList()">
                    @foreach ($mainPlants as $p)
                        <option value="{{$p->id}}" @if(old('plant') == $p->id) selected @endif>{{$p->plantName}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <label>Select Milk Plant from the list</label>
                <select name="plant" id="milkPlant" class="selectpicker" data-live-search="true" title="Select plant to add">
                    @foreach ($allPlants as $p)
                        <option value="{{$p->id}}" @if(old('plant') == $p->id) selected @endif>{{$p->plantName.", ".$p->city. ", ". $p->state}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div style="padding-top:22px">
                    <button type="submit" name="submit" class=" btn btn-primary" > Add Plant</button>
                </div>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
    <br>

    <div class="upper-controls clearfix">
        <div class="fl">
            <h3>Milk Plants</h3>
            <div class="light-color f-12">Total: {{count($milkPlant)}}</div>
            
        </div>
        {{-- <div class="fr">
            <a class="btn btn-default @if($filter=="my") active @endif" href="{{url('milkPlantList')}}?filter=my">Milk Plants</a>
            <a class="btn btn-default @if($filter=="requested") active @endif" href="{{url('milkPlantList')}}?filter=requested">Requested</a>
        </div> --}}
    </div>

    
    <div class="table-back">
        <table id="MyTable" class="display table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Plant Name </th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Pincode</th>
                <th>Plant Head Name</th>
                <th>Plant Head Email</th>
                <th>Plant Head Mobile</th>
                {{-- <th></th> --}}
            </tr>
        </thead>

        <tbody>

            @foreach ($milkPlant as $pl)

            @php 
                $bal = DB::table("user_current_balance")->where("ledgerId", $pl->ledgerId)->get()->first();
            @endphp

                <tr class="tr{{$pl->plantId}}">
                    <td>{{ $pl->plantName}}</td>
                    <td>{{ $pl->contactNumber}}</td>
                    <td>{{ $pl->address}}, {{ $pl->state}}, {{ $pl->city}}</td>
                    <td>{{ $pl->pincode}}</td>
                    <td>{{ $pl->headName}}</td>
                    <td>{{ $pl->email }}</td>
                    <td>{{ $pl->mobile }}</td>

                    {{-- <td>
                        <a href="#" onclick="removePlant({{$pl->plantId}});" class="btn btn-default btn-sm"> Remove </a>
                    </td> --}}
                    
                </tr>
            @endforeach

        </tbody>
    </table>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#MyTable').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                //to select and search from grid
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        },
        "sScrollX": "100%", "sScrollXInner": "100%",
    });
});


function removePlant(milkPlantId){
    
    if(milkPlantId){
         $.ajax({
                type:"POST",
                url:'{{url('milkPlantRemove')}}' ,
                data: {
                    milkPlantId: milkPlantId,
                },
               success:function(res){
                   if(res.error){
                        alert(res.msg);
                   }else{
                        alert(res.msg);
                        $('tr.tr'+milkPlantId).remove();
                   }
               }
            });
      }
  }

    function getChildPlantList(){
        mainPlant = $("#mainPlant").val();
        $.ajax({
                type:"POST",
                url:'{{url('getChildMilkPlants')}}' ,
                data: {
                    milkPlantId: mainPlant,
                },
               success:function(res){
                   if(res.error){
                        alert(res.msg);
                   }else{
                        elm = "";
                        $(res.plants).each(function(index, p){
                                elm += "<option value='"+p.id+"'>"+p.plantName+", "+p.city+", "+p.state+"</option>"
                        });

                        $("#milkPlant").html(elm).selectpicker("refresh");
                   }
               }
            }).done(function(res){
                console.log(res);
            }); 
    }

</script>
@endsection