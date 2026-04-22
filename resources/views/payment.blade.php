@extends('theme.default') 
@section('content') @if ($errors->any())
<div class="alert alert-danger">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

<style type="text/css">
	.col-sm-12 {
		margin-top: 12px;
	}

	.col-sm-6, .col-sm-3 {
		margin-top: 12px;
	}

	.errmsg {
		color: red;
	}
</style>

<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
	<div class="fcard mt-30 clearfix">

		<div class="heading">
			<h3>Add Payment</h4>
		</div>

		<form method="post" action="{{ url('/paymentFormSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
			<input type="hidden" name="status" value="true">
			<input type="hidden" name="productType" value="" id="productType">
			<div class="col-sm-12">
				<div class="col-sm-6">
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
						@foreach ($currnetData[3] as $currnetDataValue)
				    		    <option value="{{ $currnetDataValue->ledgerId }}">
				    		    <label id="ledgerValue{{ $currnetDataValue->ledgerId }}">Supplier</label>
						@endforeach
				  	</datalist>
				</div>
				<div class="col-sm-6">
					<label>Party name</label>
					<span id="customerNameErr" class="customerNameErr errmsg">  </span>
					<input list="customerNameList" name="partyName" id="customerName" class="customerName form-control" onfocusout="getledgerid(this.value);">
					<datalist id="customerNameList">
				    	@foreach ($currnetData[0] as $currnetDataValue)
				    		     <option value="{{ $currnetDataValue->customerName }}">
				    		     	<label id="mainValue{{ $currnetDataValue->customerName }}">Customer</label>
						@endforeach
						@foreach ($currnetData[1] as $currnetDataValue)
				    		     <option value="{{ $currnetDataValue->plantName }}">
				    		     	<label id="mainValue{{ $currnetDataValue->plantName }}">Milk Plants</label>
						@endforeach
						@foreach ($currnetData[2] as $currnetDataValue)
				    		     <option value="{{ $currnetDataValue->memberPersonalName }}">
				    		     	<label id="mainValue{{ $currnetDataValue->memberPersonalName }}">Member</label>
						@endforeach
						@foreach ($currnetData[3] as $currnetDataValue)
				    		     <option value="{{ $currnetDataValue->supplierFirmName }}">
				    		     	<label id="mainValue{{ $currnetDataValue->supplierFirmName }}">Supplier</label>
						@endforeach
					</datalist>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-6">
					<label>Date</label>
					<input type="text" class="date form-control" id="date" placeholder="Enter date date" name="date">
				</div>
				<div class="col-sm-6">
					<label>Time</label>
					<input type="text" class="time form-control" id="time" placeholder="Enter Payment Amount" name="time">
				</div>
			</div>
			<div class="col-sm-12">
				<div class="col-sm-6">
					<label>Payment Type</label>
					<select id="paymentType" class="paymentType form-control" name="paymentType" onchange="checkPaymentMode(this.value);">
							<option value="acceptAmount">Accept Amount</option>
							<option value="payAmount">Pay Amount</option>
					</select>
				</div>
				<div class="col-sm-3">
					<label>Mode of payment</label>
					<select id="paymentMode" class="paymentMode form-control" name="paymentMode" onchange="checkPaymentMode(this.value);">
							<option value="cash">Cash</option>
							<option value="credit">Credit</option>
					</select>
				</div>
				<div class="col-sm-3">
					<label>Amount</label>
					<input type="text" class="paymentAmount form-control" id="paymentAmount" placeholder="Enter Payment Amount" name="paymentAmount">
				</div>
			</div>

			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
				<div class="pt-10"></div>
				<button type="submit" class="btn btn-primary btn-block customerSubmit">Submit</button>
			</div>
	</div>
	</form>
</div>
</div>
<script type="text/javascript">
	function getledgerid(customerName){
		var dairyId = document.getElementById("dairyId").value ;
		var mainValue = document.getElementById("mainValue"+customerName) ;
		
		    if(mainValue){
		        var mainValue= mainValue.innerHTML;
		        document.getElementById("productType").value = mainValue ;
			}
		    if(customerName){
		        $.ajax({
	                type:"POST",
	                url:'getLedgerIdByName' ,
	                data: {
	                    customerName: customerName,
	                    dairyId: dairyId,
	                    mainValue: mainValue,
	                },
	                success:function(res){  
	 					if(res == "false"){
		               		document.getElementById("customerNameErr").innerHTML = "This name is not valid.";
             	 			document.getElementById("customerName").focus();
             	 			document.getElementById("ledger").value = "";
			            }else{
			            	document.getElementById("ledger").value = res;
			            	document.getElementById("customerNameErr").innerHTML = "";
			            }	
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
			               		document.getElementById("ledgerErr").innerHTML = "This ledger is not valid.";
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
          format: 'YYYY:MM:DD'
   	 	});
	});
 	$(function () {
		$('#time').datetimepicker({
		  format: 'LT'
		}); 
	});

</script>
@endsection