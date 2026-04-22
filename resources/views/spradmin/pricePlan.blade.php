@extends('spradmin.layout') 
@section("content")

<div class="fcard margin-fcard-1 pt-0 ps-5 clearfix">
    <div class="adupper-controls clearfix">
        <div class="fl">
            <h3>Price Plans</h3>
            {{-- <div class="light-color f-12">Total: </div> --}}
        </div>
        <div class="fr">
            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            <a class="btn btn-primary" href="#{{url('sa/addnewplan')}}" onclick="openPlanFormModel()"> <i class="fa fa-plus-square"></i> Add new plan </a>
        </div>
    </div>
    

<div class="wmodel clearfix" id="planFormModel" style="width: 85%;max-width:800px">
    <div class="close" onclick="closePlanFormModel()">X</div>
    <div class="wmodel-body">
        
        <div class="col-sm-12">
            <h3>New Price Plan</h3>
            <hr>
            <form action="{{url('sa/createPricePlan')}}" method="post">

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="name">Name for plan:</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="noOfMem">No. of Members:</label>
                        <input type="number" class="form-control" name="noOfMem" id="noOfMem">
                    </div>
                    <label><input type="checkbox" class="checkbox" value="1" id="memUnlimit" name="unlimitMem" style="float:left; left:0"> Unlimited Members</label>

                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="noOfSms">No. of free SMS:</label>
                        <input type="number" class="form-control" name="noOfSms" id="noOfSms">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="pricePM">Price per Month:</label>
                        <input type="number" class="form-control" name="pricePM" id="pricePM">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="pricePY">Price per Year:</label>
                        <input type="number" class="form-control" name="pricePY" id="pricePY">
                    </div>
                </div>

                {{-- <div class="col-sm-6">
                    <div class="form-group">
                        <label for="trial">Trial Time</label>
                        <div class="input-group">
                            <input name="trial" id="trial" class="form-control" value="0" />
                            <span class="input-group-addon">&nbsp;Day&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </div>
                </div> --}}

                <div class="col-sm-12">
                    <button type="submit" class="btn btn-default fr">Add Plan</button>
                </div>
            </form>
        </div>

    </div>
</div>



    <div class="plans clearfix">
        @foreach($pp as $d)
            <div class="col-md-4">
                <div class="s-plan">

                    <div class="s-p-edit">
                        <a href="#" class="btn btn-link" role="button"> EDIT</a>
                    </div>

                    <div class="s-p-name">
                        {{strtoupper($d->name)}}
                    </div>
                    
                    <br>
                    <br>
                    <div class="s-p-price">
                        <span class="rupee-symb">&#8377;</span>
                        <span class="" style="margin-left: 15px;">{{$d->monthlyPrice}}</span>
                    </div>
                    per Month

                    <br>
                    <br>
                    <br>
                    <div class="s-p-price">
                        <span class="rupee-symb">&#8377;</span>
                        <span class="" style="margin-left: 15px;">{{$d->yearlyPrice}}</span>
                    </div>
                    per Year
                    
                    <br>
                    <br>
                    <div class="s-p-rate">
                        @if($d->noOfMem < 5000)
                            {{" Member Limit ".$d->noOfMem}}
                        @else
                            Member: Unlimited
                        @endif
                    </div>
                    <div class="s-p-rate">
                        {{" SMS Limit ".$d->noOfSms}}
                    </div>
                    <div class="s-p-trial">
                        {{$d->trial_time}} Days Free Trial
                    </div>    
                </div>
            </div>
        @endforeach
    </div>

    {{-- <div class="adtable-back">
        <table id="dairiesTable" class="display table-bordered table-stripped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Dairy Name</th>
                    <th>Contact</th>
                    <th>Password</th>
                    <th>Propritor Name</th>
                    <th>Propritor Contact</th>
                    <th>Registerd Time</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($pp as $d)
                <tr>
                    <td>{{ $d->name}}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div> --}}

</div>

<script>
    $("#dairiesTable").dataTable({        
        bPaginate : false,
        bFilter : false,
        info: false,
    });

    function openPlanFormModel(){
        $("#planFormModel").fadeIn();
    }

    function closePlanFormModel(){
        $("#planFormModel").fadeOut();
    }


    $("#memUnlimit").on("change", function(){
        if($("#memUnlimit").prop("checked")){
            $("#noOfMem").attr("readonly", true);
        }else{
            $("#noOfMem").attr("readonly", false);
        }
    })
</script>

@endsection