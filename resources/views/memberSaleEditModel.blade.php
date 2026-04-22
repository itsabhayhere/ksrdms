<div style="min-height:335px">
    <h3 style="padding:0 15px">Modify Product Sale</h3>

    @if($sale->partyType=="customer")
    
    <div id="ecustomersSale" class="tab-pane fade in">
        <div class="clearfix">
            <form method="post" action="{{ url('/productSaleEditSubmitAj') }}?returnurl=memberSaleForm" onsubmit="submitForm(event, this)">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="saleId" value="{{ $sale->id }}">
                <input type="hidden" id="edairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
                <input type="hidden" name="status" value="true">
                <input type="hidden" name="partyType" value="customer" id="epartyType">
                <input type="hidden" name="sale_type" value="product_sale">
                <input type="hidden" name="returnurl" value="memberSaleForm">
                <input type="hidden" name="activetab" value="customer">

                <div class="clearfix">
                    <div class="col-md-2">
                        <label>Date</label>
                        <input type="text" class="form-control" id="ecdate" placeholder="Enter Name" value="<?php echo date("d-m-Y", strtotime($sale->saleDate)); ?>" name="date" tabindex=51  autocomplete="off">
                    </div>

                    <div class="col-md-3">
                        <label>Customer Code</label>
                        <span id="ecustomerCodeErr" class="customerCodeErr errmsg"></span>
                        <input id="ecustomerCode" class="form-control" required autofocus name="customerCode" required data-name="code" value="{{$sale->partyCode}}" readonly>
                        <img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/> 
                    </div>
                    <div class="col-md-3">
                        <label>Customer Name</label>
                        <span id="ecustomerNameErr" class="customerNameErr errmsg"></span>
                        <input id="ecustomerName" name="partyName" class="form-control" required required data-name="name" value="{{$sale->partyName}}" readonly>
                    </div>

                    
                </div>

                {{-- <div class="col-md-12">
                </div> --}}
                <div class="clearfix">
                    <div class="col-md-6"> 
                        <label>Product</label>
                        <select class="product selectpicker" name="product" title="Select Product" onchange="checkProduct(this.value, 'customer');getSaleAmount('customer')" data-live-search="true" tabindex="52">
                            @foreach($prods as $prod)
                                @php if($prod->productUnit <= 0){ $dis = "disabled class=out-stock"; $t = "(Out of stock)"; } else {$dis = ""; $t = ""; } 
                                    if($prod->productCode == $sale->productType){$sel="selected";}else{$sel="";}
                                @endphp
                                <option value="{{$prod->productCode}}" data-subtext="<span style='right:10px;position:absolute'>{{$prod->productCode}}</span>"
                                    {{ $dis }} {{$sel}}>{{$prod->productName . " " . $t}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2"> 
                        <label>Quantity</label>
                    <input type="number" class="form-control" onkeyup="egetSaleAmount('customer')" id="ecQuantity" placeholder="Enter Quantity" name="quantity" tabindex="53" step="0.1" value="{{$sale->productQuantity}}">
                    </div>
                
                    <div class="col-md-1"> 
                        <label>&nbsp;</label>
                        <input type="text" id="eunit_" name="unit" value="{{$sale->unit}}" class="noinput" style="width: 100%;line-height: 32px; color: #d00606; padding-left:0; font-weight:bold" readonly>
                    </div>

                    <div class="col-md-2">
                        <label>&nbsp; </label>
                        <input type="hidden" class="form-control" id="ecPricePerUnit" name="PricePerUnit" value="{{$sale->productPricePerUnit}}">
                        <input type="text" readonly="readonly" class="noinput" id="ecRate" name="rate" value="@ {{$sale->productPricePerUnit}} per {{$sale->unit}}" style="width: 100%;line-height: 32px;color: #d00606; padding-left:0;font-weight:bold">
                    </div>

                    <div class="col-md-3">
                        <label>Remark</label>
                        <input type="text" class="form-control" id="ecRemark" value="{{$sale->remark}}" name="remark" tabindex="55" row="2" placeholder="(Optional)" maxlength="300">
                    </div>

                    <div class="col-md-2"> 
                        <label> Amount </label>
                        <input type="number" readonly="readonly" class="form-control rupee"  id="ecAmount" name="amount" min="0" value="{{$sale->amount}}">
                    </div>
                        
                    <div class="col-md-2">
                        <label>Discount (&#8377;)</label>
                        <input type="number" class="form-control"  id="ecDiscount" name="discount" value="{{$sale->discount}}" tabindex="56" onchange="egetFinalAmount('customer')" min="0" >
                    </div>

                    <div class="col-md-2">
                        <label>Final Amount</label>
                        <input type="number" class="form-control" id="ecfinalAmount" value="{{$sale->finalAmount}}" name="finalAmount" readonly min="0">
                    </div>

                    <div class="col-md-2">
                        <label> Paid Amount </label>
                        <input type="number" class="form-control rupee" value="{{$sale->paidAmount}}" id="ecpaidAmount" name="paidAmount" tabindex="57" required min="0">
                    </div>
                </div>

                <div class="pt-25 text-center">
                    <button type="submit" class="btn btn-primary customerSubmit ms-20" tabindex="58">Update</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{$sale->id}})"><i class="fa fa-trash"></i> Delete</button>
                </div>			

            </form>
        </div>
    
    </div>

    @elseif($sale->partyType=="member")

    <div id="ememberSale" class="tab-pane fade in">

        <div class="clearfix">
            <form method="post" action="{{ url('/productSaleEditSubmitAj') }}?returnurl=memberSaleForm" onsubmit="submitForm(event, this)">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="saleId" value="{{ $sale->id }}">
                <input type="hidden" id="edairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
                <input type="hidden" name="status" value="true">
                <input type="hidden" name="partyType" value="member" id="epartyType">
                <input type="hidden" name="sale_type" value="product_sale">
                <input type="hidden" name="activetab" value="member">
                <input type="hidden" name="returnurl" value="memberSaleForm">

                <div class="clearfix">
                    <div class="col-md-2"> 
                        <label>Date</label>
                        <input type="text" class="form-control" id="emdate" placeholder="Enter Name" value="<?php echo date("d-m-Y", strtotime($sale->saleDate)); ?>" name="date" autocomplete="off" tabindex="61">
                    </div>

                    <div class="col-md-3">
                        <label>Member Code</label>
                        <span id="ememberCodeErr" class="memberCodeErr errmsg">  </span>
                        <input id="ememberCode" class="form-control" required autofocus name="memberCode" required data-name="code" value="{{$sale->partyCode}}" readonly>
                        <img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/>
                    </div>
                    <div class="col-md-3">
                        <label>Member Name</label>
                        <span id="ememberNameErr" class="memberNameErr errmsg">  </span>
                        <input id="ememberName" name="partyName" class="form-control" required data-name="name" value="{{$sale->partyName}}" readonly>
                    </div>
                        
                </div>
                {{-- <div class="col-md-12">
                </div> --}}
                <div class="clearfix">

                    <div class="col-md-6"> 
                        <label>Product</label>
                        <input type="hidden" name="product" value="{{$sale->productType}}">

                        <select class="product selectpicker" name="product" title="Select Product" onchange="checkProduct(this.value, 'customer');getSaleAmount('customer')" data-live-search="true" tabindex="62" disabled>
                            {{-- <option >--Select--</option> --}}
                            @foreach($prods as $prod)
                                @php if($prod->productUnit <= 0){ $dis = "disabled class=out-stock"; $t = "(Out of stock)"; } else {$dis = ""; $t = ""; } 
                                    if($prod->productCode == $sale->productType){$sel="selected";}else{$sel="";}
                                @endphp
                                    <option value="{{$prod->productCode}}" data-subtext="<span style='right:10px;position:absolute'>{{$prod->productCode}}</span>"
                                        {{ $dis }} {{ $sel }}>{{$prod->productName . " " . $t}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2"> 
                        <label>Quantity</label>
                        <input type="number" class="form-control" onkeyup="egetSaleAmount('member')" id="emQuantity" placeholder="Enter Quantity" name="quantity" tabindex="64" step="0.1" value="{{$sale->productQuantity}}">
                    </div>
                
                    <div class="col-md-1"> 
                        <label>&nbsp;</label>
                        <input type="text" id="eunit_" name="unit" value="{{$sale->unit}}" class="noinput" style="width: 100%;line-height: 32px; color: #d00606; padding-left:0; font-weight:bold" readonly>
                    </div>
    
                    <div class="col-md-2"> 
                        <label>&nbsp; </label>
                        <input type="hidden" class="form-control" id="emPricePerUnit" name="PricePerUnit" value="{{$sale->productPricePerUnit}}">
                        <input type="text" readonly="readonly" class="noinput"  id="emRate" name="rate" value="@ {{$sale->productPricePerUnit}} per {{$sale->unit}}" style="width: 100%;line-height: 32px;color: #d00606; padding-left:0;font-weight:bold">
                    </div>

                    <div class="col-md-3">
                        <label>Remark</label>
                        <input type="text" class="form-control" id="emRemark" value="{{$sale->remark}}" name="remark" tabindex="65" row="2" placeholder="(Optional)" maxlength="300">
                    </div>

                    <div class="col-md-2"> 
                        <label> Amount </label>
                        <input type="number" readonly="readonly" class="form-control rupee" id="emAmount" name="amount" min="0" value="{{$sale->amount}}">
                    </div>

                    <div class="col-md-2">
                        <label>Discount (&#8377;)</label>
                        <input type="number" class="form-control"  id="emDiscount" name="discount" value="{{$sale->discount}}" tabindex="66" onchange="egetFinalAmount('member')" min="0">
                    </div>

                    <div class="col-md-2">
                        <label>Final Amount</label>
                        <input type="number" class="form-control" id="emfinalAmount" value="{{$sale->finalAmount}}" name="finalAmount" readonly min="0">
                    </div>

                    <div class="col-md-2">
                        <label> Paid Amount </label>
                        <input type="number" class="form-control rupee" value="{{$sale->paidAmount}}" id="empaidAmount" name="paidAmount" tabindex="67" required min="0">
                    </div>	
                </div>
                
                <div class="pt-25 text-center">	
                    <button type="submit" class="btn btn-primary customerSubmit ms-20" tabindex=68>Update</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{$sale->id}})"><i class="fa fa-trash"></i> Delete</button>
                </div>	
            </form>
        </div>

    </div>

    @endif

</div>


<script>

    $(function () {
        $('#ecdate, #emdate').datetimepicker({
          format: 'DD-MM-YYYY'
   	 	});
	});

    function echeckProduct(product, type){

        buf = {{($dairyInfo->buffaloMilkPrice)?$dairyInfo->buffaloMilkPrice:0}};
        cow = {{($dairyInfo->cowMilkPrice)?$dairyInfo->buffaloMilkPrice:0}}

        if(type=="customer"){
            rate = "ecRate";
            pp = "ecPricePerUnit";
        }
        if(type=="plant"){
            rate = "epRate";
            pp = "epPricePerUnit";
        }
        if(type=="member"){
            rate = "emRate";
            pp = "emPricePerUnit";
        }

        if($(product).val()=="buffaloMilk"){
            $("#"+pp).val(parseFloat(buf).toFixed(2));
            $("#"+rate).val("@ "+buf+" per Ltr");
        }else{
            $("#"+pp).val(parseFloat(cow).toFixed(2));
            $("#"+rate).val("@ "+cow+" per Ltr");
        }

        $("#quantity").focus();
    }

    function egetSaleAmount(type){
		if(type="customer"){
			a = parseFloat($("#ecQuantity").val()) * parseFloat($("#ecPricePerUnit").val());
			$("#ecAmount, #ecfinalAmount").val( Math.round (a*100) / 100);
		}
		if(type="member"){
			a = $("#emQuantity").val() * $("#emPricePerUnit").val();
			$("#emAmount, #emfinalAmount").val( Math.round (a*100) / 100);
		}

		getFinalAmount(type);
	}

	function egetFinalAmount(type){
		if(type="customer"){
			dis = $("#ecDiscount").val();
			if( dis == "" || dis < 0){
				$("#ecDiscount").val("0");
			}
			a = parseFloat($("#ecAmount").val()) - parseFloat($("#ecDiscount").val());
			$("#ecfinalAmount").val( Math.round (a*100) / 100);
			if(isCash) $("#ecpaidAmount").val( Math.round (a*100) / 100);
			$("#ecpaidAmount").attr("max",  Math.round (a*100) / 100);
		}
		if(type="member"){
			dis = $("#emDiscount").val();
			if( dis == "" || dis < 0){
				$("#emDiscount").val("0");
			}
			a =  parseFloat($("#emAmount").val()) - parseFloat($("#emDiscount").val());
			$("#emfinalAmount").val( Math.round (a*100) / 100);
			$("#empaidAmount").attr("max",  Math.round (a*100) / 100);
		}
	}

    function confirmDelete(id){
        $.confirm({
			title: 'Confirm!',
			content: 'Are you sure? proceed to delete this sale record?.',
			type: 'red',
			typeAnimated: true,
			buttons: {
				delete: {
					text: 'Delete ',
					btnClass: 'btn-red',
					action: function(){
                        deleteSale(id);
					}
				},
                cancel:{
                    action: function(){
                        return true;
                    }
                }
			}
		});
    }

    function deleteSale(id){
        loader("show");
        $.ajax({
            type:"POST",
            url:'deleteSaleAjax',
            data: {
                saleId: id,
                saleType: "product_sale"
            },
            success:function(res){
                console.log(res);
                if(res.error){
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-success").show().addClass("alert-danger");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 7000);                    
                }else{
                    $("#saleEditModel").fadeOut();
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 7000);
                    $('#sale-table').DataTable().ajax.reload();
                }
            },
            error:function(res){
                $.alert('An error has occurred.');
            }
        }).done(function(res){
            loader("hide");
            console.log(res);
        });;
    }

    function submitForm(e, frm){
        e.preventDefault();
        loader('show');

        if($(frm)[0].checkValidity()){;}else{return false;}
        console.log("afdsd");
        
        $.ajax({
            type: $(frm).attr('method'),
            url: $(frm).attr('action'),
            data: $(frm).serialize(),

            success: function (res) {
                if(res.error){
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-success").show().addClass("alert-danger");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 7000);                    
                }else{
                    $("#saleEditModel").fadeOut();
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 7000);
                    $('#sale-table').DataTable().ajax.reload();
                }
            },
            error: function (data) {
                $.alert('An error has occurred.');
                console.log(data);
            }
        }).done(function(res){
            loader("hide");
            console.log(res);
        });
    }

    $(".selectpicker").selectpicker('render');

</script>