@extends('theme.default') 
@section('content') 

<div class="pageblur">

    <div class="fcard margin-fcard-1 pt-0 clearfix">
        
        <div class="upper-controls pt-0 clearfix">
            <div class="fl">
                <div class="heading">
                    <h3>Dairy Balance</h3>
                    <hr class="m-0">
                </div>
            </div>
        </div>

        
        <div class="col-md-6 ">
            <div class="dairy_bal clearfix">
                <div class="cur_bal_text">
                    Today's sales in cash
                </div>
                <div class="todaySaleInCash cur_bal">
                    ...
                </div>
            </div>    
        </div>

        <div class="col-md-6">
            <div class="dairy_bal clearfix">
                <div class="cur_bal_text">
                    This Month sales in cash
                </div>
                <div class="monthSaleInCash cur_bal">
                    ...
                </div>
            </div>    
        </div>

        <div class="col-md-4 col-md-offset-4 pt-50">
            <div class="dairy_bal greencard clearfix">
                <div class="cur_bal_text">
                    Current Balance
                </div>
                <div class="cur_bal">
                    {{-- &#8377; {{$dairyInfo->cash_in_hand}} --}}
                    {{$FinalTotal}}
                </div>

                <div class="pt-20 clearfix">
                    <button class="btn btn-default" onclick="show_popUp('add')"> Add Cash</button>
                    {{-- <button class="btn btn-danger" onclick="show_popUp('remove')"> Remove Cash</button> --}}
                    <button class="btn btn-danger" onclick="editLastCashModel()"> Edit Cash</button>
                    
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>

        

    </div>
    <div class="table-back ">
        <table id="dailry-table" class="display tright" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Date <small>(DD-MM-YYYY)</small></th>
                    <th>Date <small>(DD-MM-YYYY)</small></th>
                    <th>Party Code</th>
                    <th>Activity</th>
                    <th>Credit</th>
                    <th>Debit</th>
                </tr>
            </thead>

            <tbody class="table-transactions">

            </tbody>
        </table>

    </div>

</div>


<div class="wmodel clearfix" id="addremoveCashModel">
    <div class="close" onclick="hidePopup()">X</div>
    <div class="wmodel-body">
        <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 pt-30">
            <form action="{{url('submitCashUpdate')}}" method="post">
                <input type="hidden" name="type" value="" id="cashType">
                <div class="col-sm-12">
                    <h3 id="text-to-show">Cash</h3>
                    <input type="number" name="cash" id="fcash" class="form-control" onkeyup="updateBal()" placeholder="Amount here">
                </div>

                <div class="col-sm-12 pt-20">
                    <label>Remark</label>
                    <input type="text" name="remark" id="remark" class="form-control" placeholder="Remark here">
                </div>
    
                <div class="col-sm-12 clearfix pt-30">
                    <div class="disp_updated_bal ">
                        Updated balance will be
                        <span class="wrap">
                            <i class="fa fa-inr"></i> <div class="updated_bal"> {{$dairyInfo->cash_in_hand}}</div>
                        </span>
                    </div>
                </div>

                <div class="col-sm-12 pt-30 text-center">
                    <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Update">
                </div>
    
            </form>
        </div>

    </div>
</div>


<div class="wmodel clearfix" id="editLastCashModel">
    <div class="close" onclick="hidePopup()">X</div>
    <div class="wmodel-body">
        <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 pt-30">
            <form action="{{url('submitCashEditUpdate')}}" method="post">
                <input type="hidden" name="balSheetId" value="" id="balSheetId">
                <div class="col-sm-12">
                    <h3 id="text-to-show">Edit Cash</h3>
                    <span id="last-details"></span>
                    <input type="number" name="cash" id="fcashEdit" class="form-control" placeholder="Amount here">
                </div>

                <div class="col-sm-12 pt-20">
                    <label>Remark</label>
                    <input type="text" name="remark" id="remarkEdit" class="form-control" placeholder="Remark here">
                </div>
    
                <div class="col-sm-12 pt-30 text-center">
                    <input type="submit" name="submit" id="submitEdit" class="btn btn-primary" value="Update">
                </div>
    
            </form>
        </div>

    </div>
</div>

<script>

    function show_popUp(e){

        if(e=="add"){
            $("#text-to-show").html("Add Cash");
            $("#cashType").val("add");
        }else{
            $("#text-to-show").html("Edit Cash");
            $("#cashType").val("remove");
        }

        $(".updated_bal").html({{$dairyInfo->cash_in_hand}});
        $('#addremoveCashModel').fadeIn();
        $('.pageblur').addClass("blur-3");
    }

    function hidePopup(){
        $('#addremoveCashModel, #editLastCashModel').fadeOut();
        $('.pageblur').removeClass("blur-3");
        $("#cashType").val("");
    }

    function updateBal(){

        ubal = 0;
        cbal = {{$dairyInfo->cash_in_hand}};
        ctype = $("#cashType").val();

        console.log(ctype);

        t = parseFloat($("#fcash").val());
        if(isNaN(t)) t = 0;

        if(ctype == "add"){
            ubal = cbal + t;
        }else{
            ubal = t;
        }

        console.log(typeof t, t);
        $(".updated_bal").html(ubal);
    }

    function editLastCashModel(){
        // console.log("dsfhldkf");
        $.ajax({
            type:"POST",
            url:'{{url('getLastCashEdit')}}',
            data: {data: ""},
            success:function(res){
                if(res.error){
                    $.alert(res.msg);
                }else{
                    if(res.editable){
                        $('#editLastCashModel').fadeIn();
                        $('.pageblur').addClass("blur-3");
                        $("#last-details").html("Last update: "+ res.details.created_at);
                        $("#fcashEdit").val(res.details.finalAmount).focus();
                        $("#remarkEdit").val(res.details.remark);
                        $("#balSheetId").val(res.details.id);
                    }else{
                        $.alert("You can only edit cash with in 24 hour.");
                    }
                }
            },
            error:function(res){
            }
        }).done(function(res){
            console.log(res);
        });
    }

    function getOtherDetails(){

        $.ajax({
            type:"POST",
            url:'{{url('dairyBalOtherDetails')}}',
            data: {data: ""},
            success:function(res){
                $(".todaySaleInCash").html("&#8377; "+res.todaySaleInCash);
                $(".monthSaleInCash").html("&#8377; "+ res.monthSaleInCash);
            },
            error:function(res){
            }
        }).done(function(res){
            console.log(res);
        });
    }

    getOtherDetails();
    $(document).ready(function() {
        $('#dailry-table').DataTable({
            "ajax": 'getDailryBalData',
             "columnDefs": [{

                       "targets": [ 0 ],

                        "visible": false,

                        "searchable": false,

                        // "bSortable": true,

                        // "iDataSort":1

                    },

                 

                    ],

                "order": [0]
        });

        $("#date, #mdate, #sdate").datetimepicker({
            format:"DD-MM-YYYY"
        })
    });

</script>

@endsection