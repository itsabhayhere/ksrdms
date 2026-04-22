@extends('member.layout') 
@section('content')


<style type="text/css">
    .m-0 {
        margin-top: 0;
    }
    .spclreqbtn{
        padding: 30px 10px;
        border-radius: 3px;
        border-color: #dcdcdc;
        box-shadow: 0px 3px 36px rgba(0,0,0,0.2);
        transition: 0.6s;
    }
    .spclreqbtn:hover{
        background-color: #ececec!important;
        color: #ff6600!important;
        font-size: 18px;
    }
</style>

<div class="span-fixed response-alert" id="response-alert"></div>

<div class="pageblur clearfix">

    <div class="fcard margin-fcard-1 pt-0 clearfix">
        <div class="heading">
            <div class="fl">
                <h3>Milk Request</h3>
                <hr class="m-0">
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row text-center">

            <div class="col-md-4 col-md-offset-1 col-sm-6 col-xs-6">
                <a href="#" class="btn btn-default spclreqbtn" onclick="openrequestModel('milk')" title="Send a milk request">
                    <i class="fa fa-mail-forward"></i> Milk Collection Request
                </a>
            </div>
            <div class="col-md-4 col-md-offset-2 col-sm-6 col-xs-6">
                <a href="#" class="btn btn-default spclreqbtn" onclick="openrequestModel('product')" title="Send a product delivery request">
                    <i class="fa fa-mail-forward"></i> Product Delivery Request
                </a>
            </div>

        </div>

        <div class="col-md-8 col-sm-10 pt-20 req">
            @foreach($milkReq as $m)
            <div style="
                border: 1px solid #eee;
                border-radius: 12px;
                margin-bottom: 10px;">
                <div class="s-req s-req-left">
                    <div class="s-req-usr">You</div>
                    <div class="s-req-date">{{date("d-m-Y", strtotime($m->date))}} &nbsp; &nbsp; Shift: {{$m->shift}}</div>

                    <div class="req-type">For:
                        <b>{{ucfirst($m->type)}}
                                @if($m->type == "product") 
                                    @php 
                                        $p = DB::table("products")->where("productCode", $m->productCode)->get()->first(); 
                                        if($p!=null) {
                                            echo $p->productName." (".$m->productCode.") <br/> Qty: ".$m->qty;
                                        }
                                    @endphp
                                @endif
                            </b>
                    </div>

                    <div class="s-req-content">{{$m->comment}}</div>


                    <div class="complete-btn-area">
                        @if($m->isDeliverd == 1)
                        <span class="label label-success">Completed</span> @elseif($m->isDeliverd == 2)
                        <span class="label label-danger">Decliend</span> @endif
                    </div>

                    <div class="fl s-req-time">request time: {{date("d-m-Y g:i a", strtotime($m->created_at))}}</div>
                    @if($m->isSeen == "true")
                    <div class="s-req-seen">seen at {{date("d-m-Y g:i a", strtotime($m->seen_at))}}</div>
                    @endif

                    <div class="clearfix"></div>
                </div>

                @if($m->resText != (null||""))
                <div class="s-req s-req-right">
                    <div class="s-req-usr">{{$m->colMan}}</div>
                    <div class="s-req-content">{{$m->resText}}</div>
                    <div class="fr s-req-time">at {{date("d-m-Y g:i a", strtotime($m->response_at))}}</div>
                    <div class="clearfix"></div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

    </div>

</div>



<div class="wmodel clearfix" id="reqModel" style="width: 85%;max-width:800px">
    <div class="close" onclick="closereqModel(event)">X</div>
    <div class="wmodel-body">

        <h3 class="text-center" id="heading-text">New Milk Request </h3>
        <hr>
        <input type="hidden" name="memberCode" id="memberCode" value="{{$mem->memberPersonalCode}}" />
        <input type="hidden" name="reqtype" id="reqtype" value="" />
        <div class="row">
            <div class="col-sm-6">
                <label>Date</label>
                <input type="text" class="form-control" id="date" placeholder="Date" value="{{date("d-m-Y ", strtotime('tomorrow'))}}" name="date"
                    autofocus autocomplete="off">
            </div>
            <div class="col-sm-6" id="shiftArea">
                <div id="SetShiftField" class="SetShiftField">
                    <label>Shift</label>
                    <select id="dailyShift" class="dailyShift selectpicker" name="dailyShift">
                        <option value="morning" selected> Morning Shift </option>
                        <option value="evening"> Evening Shift </option>
                    </select>
                </div>
            </div>

            <div class="pb-20 clearfix"></div>

            <div class="row pb-20" id="productArea">
                <div class="col-sm-4">
                    <label>Product</label>
                    <select id="product" class="product selectpicker" name="product" title="Choose product...">
                        @foreach($products as $pro)
                            <option value="{{$pro->productCode}}" @if($pro->productUnit <= 0) disabled @endif>{{$pro->productName}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 pt-20">
                    Rate: <b id="prate">..</b>
                    <br> Stock: <b id="pstock">..</b>
                </div>

                <div class="col-sm-4">
                    <label>Quantity</label>
                    <input type="number" class="qty form-control" name="qty" id="qty" placeholder="Quantity">
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <label>Comment</label>
            <textarea name="comment" id="comment" class="form-control" cols="30" rows="6" max="400"></textarea>
        </div>

        <div class="col-sm-12 text-center pt-20">
            <a href="#" role="button" class="btn btn-primary" id="date-shift-btn" onclick="sendReq(event)">Send Request</a>
        </div>

    </div>
</div>

<script>
    // function showReply(e){
    //     $(e).siblings(".s-req-reply").show();
    //     $(e).hide();
    // }
    // function hideReply(e){
    //     $(e).closest(".s-req-reply").hide().siblings(".reply-btn").show();
    //     // $(e).closest(".reply-btn").show();
    // }

    $("#product").on("change", function(){
        checkProduct($(this).val());
    })

    function checkProduct(productCode){

		console.log(product);
		$.ajax({
				type:"POST",
				url:'{{url('member/getProductUnit')}}',
				data: {
					productCode: productCode,
				},
				success:function(res){
					console.log(res);

                    $("#prate").html("&#8377;" + res.rate);
                    $("#pstock").html(res.stock + " Units");
                    
					$("#qty").focus();
				},
				error:function(res){
					console.log(res);
				}
			});
    }


    function openrequestModel(type){
        $("#reqtype").val(type);
        $("#heading-text").html("New "+type+" request ");

        if(type=="milk"){
            $("#productArea").hide();
            $("#shiftArea").show();
        }
        else{
            $("#productArea").show();
            $("#shiftArea").hide();
    }

        $("#reqModel").fadeIn();
    }

    function closereqModel(e){
        e.preventDefault();
        $("#comment").val("");
        $("#reqModel").fadeOut();
    }

    function sendReq(e){
        e.preventDefault();
        loader("show");

        memCode = $("#memberCode").val();
        type = $("#reqtype").val();
        prod = $("#product").val();
        qty = $("#qty").val();
        comment = $("#comment").val();

        if(type=="product"){
            if(qty == "" || prod == ""){
                $.alert("Please fill all fields.");
                loader("hide");
                return;
            }   
        }

        console.log(prod);

        $.ajax({
            type:"POST",
            url:'{{url('member/sendReq')}}',
            data: {
                memCode: memCode,
                type: type,
                productCode: prod,
                qty: qty,
                date: $("#date").val(),
                shift: $("#dailyShift").val(),
                comment: comment,
            },
            success:function(res){
                if(res.error || res.errors){
                    $.alert("Some error has occurd at server.");
                }else{
                    refreshData(memCode);
                    closereqModel(event);
                    $.alert(res.msg);                    
                }
            },
            error:function(res){
                $.alert("Something happened, Please try again.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    }

    function refreshData(memCode){

        location.reload(); return;

        loader("show");

        $.ajax({
            type:"POST",
            url:'{{url('member/getReqs')}}',
            data: {
                memCode:memCode,
            },
            success:function(res){
                if(res.error){
                    $.alert("Some error has occurd at server.");
                }else{
                    $(".req").html(res.content);
                    $('html, body').animate({
                        scrollTop: $(".req").offset().bottom
                    }, 2000);
                }
            },
            error:function(res){
                $.alert("Something happened, Please try again.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    }

</script>
@endsection