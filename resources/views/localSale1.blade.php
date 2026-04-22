@extends('theme.default')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style type="text/css">
.col-sm-6, .col-sm-12, .col-sm-3{
    margin-top: 12px;
}
.errmsg{
	color:red;
}
</style>


<div class="fcard margin-fcard-1 clearfix">

		<div class="upper-controls clearfix">
			<div class="fl">
				<h3>Local Sale</h3>
				<hr>
				{{--
				<div class="light-color f-12">Total: {{count($purchaseList)}}</div> --}}
			</div>
			<div class="fr">
				{{-- <a class="btn btn-primary" href="DailyTransactionForm">Add Purchase</a> --}}
			</div>
		</div>

		<div class="col-sm-12">
			<form method="post" action="{{ url('/localSaleFormSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
			<input type="hidden" name="status" value="true">
			<input type="hidden" name="productType" value="" id="productType">
			<div class="col-sm-12">
				<div class="col-sm-6 hide">
						<label>Ledger name</label>
						<span id="ledgerErr" class="ledgerErr errmsg">  </span>
					    <input list="ledgerList" name="ledgerName" id="ledger" class="ledger form-control" onfocusout="getCustomerName(this.value);">
					    <datalist id="ledgerList">
					    	@foreach ($currnetData[0] as $currnetDataValue)
					    		    <option value="{{ $currnetDataValue->ledgerId }}">
					    		    <label id="ledgerValue{{ $currnetDataValue->ledgerId }}">Customer</label>
							@endforeach
							@foreach ($currnetData[1] as $currnetDataValue)
					    		    <option value="{{ $currnetDataValue->ledgerId }}">
					    		    <label id="ledgerValue{{ $currnetDataValue->ledgerId }}">Milk Plants</label>
							@endforeach
							@foreach ($currnetData[2] as $currnetDataValue)
					    		    <option value="{{ $currnetDataValue->ledgerId }}">
					    		    <label id="ledgerValue{{ $currnetDataValue->ledgerId }}">Member</label>
							@endforeach
					  	</datalist>
				</div>

				
				<div class="col-sm-6"> 
					<label>Date</label>
					<input type="text" class="form-control" id="date" placeholder="Enter Name" value="<?php echo date("d-m-Y", time()); ?>" name="date" >
				</div>

				<div class="col-sm-6">
						<label>Party name</label>
						<span id="customerNameErr" class="customerNameErr errmsg">  </span>
					  	<input name="partyName" id="partyName" type="hidden">
						<select name="custName" id="customerName" class="customerName selectpicker" onchange="getledgerid(this.value)" title="Select party" data-live-search="true">
								@foreach ($currnetData[0] as $currnetDataValue)
									<option value="{{ "CUS".$currnetDataValue->customerCode }}" data-subtext="<span style='right:10px;position:absolute'>Customer</span>">{{ $currnetDataValue->customerName }}</option>
								@endforeach
								@foreach ($currnetData[1] as $currnetDataValue)
									<option value="{{ "MIL".$currnetDataValue->id }}" data-subtext="<span style='right:10px;position:absolute'>Milk Plant</span>">{{ $currnetDataValue->plantName }}</option>
								@endforeach
								@foreach ($currnetData[2] as $currnetDataValue)
									<option value="{{ "MEM".$currnetDataValue->memberPersonalCode }}" data-subtext="<span style='right:10px;position:absolute'>Member</span>">{{ $currnetDataValue->memberPersonalName }}</option>
								@endforeach
						</select>

				</div>
			</div>
			{{-- <div class="col-sm-12"> 
			</div> --}}
			<div class="col-sm-12"> 
				<div class="col-sm-6"> 
					<label>Product</label>
					<select id="product" class="product selectpicker" name="product" title="Select Product" onchange="checkProduct(this.value);" data-live-search="true">
						{{-- <option >--Select--</option> --}}
						@foreach($currnetData[3] as $prod)
							<option value="{{$prod->productCode}}" data-subtext="<span style='right:10px;position:absolute'>{{$prod->productCode}}</span>">{{$prod->productName}}</option>
						@endforeach
						{{-- <option value="Milk">Milk</option>
						<option value="Cattle feed">Cattle feed</option>
						<option value="Ghee">Ghee</option>
						<option value="other">Other</option> --}}
					</select> 
				</div>

				<div class="col-sm-3"> 
					<label>Quantity</label>
					<input type="text" class="form-control" id="quantity" placeholder="Enter Quantity" name="quantity">
				</div>
			
				<div class="col-sm-3"> 
					<label>&nbsp;</label>
					<input type="text" id="unit_" name="unit" value="" class="noinput" style="width: 100%;line-height: 38px;" readonly>
					{{-- <select id="unit" class="unit selectpicker" name="unit" onchange="checkUnit(this.value);" title="Select Unit" required>
						<option value="kg">Kg</option>
						<option value="ltr">Ltr</option>
						<option value="mt">Mt</option>
						<option value="gm">Gm</option>
						<option value="specify">Specify</option>
					</select> --}}
					
				</div>
			</div>

			<div class="col-sm-12"> 
				<div class="col-sm-6"> 
					<input type="text" name="otherProduct" id="otherProduct" placeholder="Other Product" class="otherProduct form-control" style="display: none;">

					<div id="milkPlantFiled" style="display: none;">
						<label>Type of Milk</label>
						<select id="milkType" class="milkType selectpicker" name="milkType">
							<option value="cow">Cow</option>
							<option value="buffalo">Buffalo</option>
						</select>
					</div>

				</div>
				<div class="col-sm-6"> 
					<input type="text" name="otherUnit" id="otherUnit" placeholder="Other Unit" class="otherUnit form-control" style="display: none;">
				</div>
			</div>

			<div class="col-sm-12 hide"> 
				<div class="col-sm-6"> 
					<label> Price per unit  </label>
				    <input type="text" class="form-control" onfocusout="getSaleAmount();" id="PricePerUnit" name="PricePerUnit">
				</div>
			</div>
		
			

			<div class="col-sm-12"> 
				<div class="col-sm-3"> 
					<label> Amount </label>
					<input type="text" readonly="readonly" class="form-control rupee"  id="amount" name="amount">
				</div>
				<div class="col-sm-3"> 
					<label>&nbsp; </label>
					<input type="text" readonly="readonly" class="noinput"  id="rate" name="rate" style="width: 100%;line-height: 38px;">
				</div>
	
				<div class="col-sm-6"> 
					<label>Mode of payment</label>
					<select id="paymentMode" class="paymentMode selectpicker" name="paymentMode" onchange="checkPaymentMode(this.value);">
							<option value="cash">Cash</option>
							<option value="credit">Credit</option>
					</select>
					<br/>
					<input type="text" name="otherPaymentMode" id="otherPaymentMode" placeholder="Other Payment Mode" class="otherPaymentMode form-control" style="display: none;">
				</div>
	
			</div>
			
			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-2 col-lg-offset-5">	
				<div class="pt-10"></div>
				<button type="submit" class="btn btn-primary btn-block customerSubmit">Submit</button>
			</div>

			
		</div>
 
    </form>
</div>
</div>
<script type="text/javascript">

	function getledgerid(customerName){
		var dairyId = document.getElementById("dairyId").value;
		var mainValue = customerName.substring(0, 3);
		console.log(mainValue);

		if(mainValue){
			document.getElementById("productType").value = mainValue ;
		}

		if(customerName){
			$.ajax({
				type:"POST",
				url:'getLedgerIdByName' ,
				data: {
					customerName: customerName,
					dairyId: dairyId,
				},
				success:function(res){  
					if(res == "false"){
						document.getElementById("customerNameErr").innerHTML = "This name is not valid.";
						document.getElementById("customerName").focus();
						document.getElementById("ledger").value = "";
					}else{
						document.getElementById("ledger").value = res.ledgerId;
						document.getElementById("customerNameErr").innerHTML = "";
						$("#partyName").val(res.name);
					}
					console.log(res);
				},
				error:function(res){
					console.log(res);
				}
			});

		}
	} 

	function getCustomerName(ledgerId){
		
		var dairyId = document.getElementById("dairyId").value ;
		var mainValue = document.getElementById("ledgerValue"+ledgerId) ;

		 	if(mainValue){
		        var mainValue= mainValue.innerHTML;
		        document.getElementById("productType").value = mainValue ;
		    }
		
			if(ledgerId){
		         $.ajax({
		                type:"POST",
		                url:'getUserNameByledger' ,
		                data: {
		                    ledgerId: ledgerId,
		                    dairyId: dairyId,
		                    mainValue: mainValue,
		                },
		               success:function(res){  
		               		if(res == "false"){
			               		document.getElementById("ledgerErr").innerHTML = "This ledger if is not valid.";
                  				document.getElementById("ledger").focus();
                  				document.getElementById("customerName").value = "";
				            }else{
				            	document.getElementById("customerName").value = res;
				            	document.getElementById("ledgerErr").innerHTML = "";
				            }	
		               }
		            });
		    }

	}

	function getSaleAmount(){
		var quantity =  $("#quantity").val();
		var PricePerUnit =  $("#PricePerUnit").val();
		$("#amount").val(quantity*PricePerUnit);
	}

    $(function () {
        $('#date').datetimepicker({
          format: 'DD-MM-YYYY'
   	 	});
	});

	function checkPaymentMode(paymentMode){
       
        if(paymentMode == "other"){
        	 $("#otherPaymentMode").show();
          
        }else{
            $("#otherPaymentMode").hide();
        }
    }

    function checkProduct(product){
		var dairyId = document.getElementById("dairyId").value ;

    	var milkPlant = document.getElementById("productType").value ;
		if(milkPlant == "MIL" && product == "Milk"){
			$("#milkPlantFiled").show();
			$("#otherProduct").hide();
		}else if(product == "other"){
        	$("#otherProduct").show();
        	$("#milkPlantFiled").hide();
        }else{
            $("#otherProduct").hide();
            $("#milkPlantFiled").hide();
        }

		console.log(product);
		$.ajax({
				type:"POST",
				url:'getProductUnit' ,
				data: {
					dairyId: dairyId,
					productCode: product,
				},
				success:function(res){  
					console.log(res);
					$("#unit_").val(res.unit);
					$("#rate").val(res.rate+" per "+ res.unit);
					$("#PricePerUnit").val(parseFloat(res.rate).toFixed(2)); 
					$("#quantity").focus();
				},
				error:function(res){
					console.log(res);
				}
			});
    }

    // function checkUnit(checkUnit){
	
 	// 	if(checkUnit == "specify"){
    //     	 $("#otherUnit").show();
    //     }else{
    //         $("#otherUnit").hide();
    //     }
    // }

   
	 /* supplier code validation */
	function CheckCustomerCode(){
	  var customer_code  = document.getElementById("customerCode").value;

	     if(customer_code){
	         $.ajax({
	               type:"GET",
	               url:"{{url('/checkCustomerCode')}}?customer_code="+customer_code ,
	               success:function(res){          
	                if(res == "true"){
					  document.getElementById("customerCodeErrorMessage").innerHTML = "This code is already being used.";
	                  document.getElementById("customerCode").focus();
	                }else if(res == "false"){
	                  document.getElementById("customerCodeErrorMessage").innerHTML = "";
	                }
	               }
	            });
	      }
	}

	$("#quantity").on("keyup", function(){
		var q = parseFloat($("#quantity").val()).toFixed(2);
		var rate = parseFloat($("#PricePerUnit").val()).toFixed(2);
		var am = parseFloat(q*rate).toFixed(2);
		$("#amount").val(am);
		console.log(q,am,rate);
	})

</script>
@endsection