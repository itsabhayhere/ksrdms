 @extends('theme.default')

@section('content')

<style type="text/css">
.col-sm-12, .col-sm-6, .col-sm-3 {
    margin-top: 12px;
}

.errmsg{
	color:red;
}
</style>
<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
		<div class="fcard mt-30 clearfix">
			<form method="post" action="{{ url('/expenseFormSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
			<input type="hidden" name="status" value="true">
			<input type="hidden" name="productType" value="" id="productType">
			<div class="col-sm-12">
				<div class="col-sm-6">
					<label>Ledger name</label>
					<span id="ledgerErr" class="ledgerErr errmsg">  </span>
				    <input list="ledgerList"  name="ledgerName" id="ledger" readonly="readonly" value="{{$dairyInfo->ledgerId }}"  class="ledger form-control">
				</div>
				<div class="col-sm-6">
						<label>Party name</label>
						<span id="customerNameErr" class="customerNameErr errmsg">  </span>
					  	<input list="customerNameList"  name="partyName" id="customerName" readonly="readonly" value="{{$dairyInfo->society_name }}" class="customerName form-control">
				</div>
			</div>
			<div class="col-sm-12"> 
				<div class="col-sm-6"> 
					<label>Date</label>
					<input type="text" class="form-control" value="{{ old('date') }}" id="date" placeholder="Enter Name" value="<?php echo date("Y/m/d"); ?>" name="date" >
				</div>
				<div class="col-sm-6"> 
					<label>Time</label>
					<input type="text" class="form-control" value="{{ old('time') }}" id="time" placeholder="Enter time" value="<?php echo date("H:i"); ?>" name="time" >
				</div>
			</div>
			<div class="col-sm-12"> 
				<div class="col-sm-6"> 
					<label>Expense Type</label>
					<select id="expenseType" class="expenseType form-control" name="expenseType" onchange="checkPaymentMode(this.value);">
						@foreach ($expenses as $expensesData)
							<option value="{{ $expensesData->id }}">{{ $expensesData->expenseHeadName }}</option>
                		@endforeach
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
					<label> Amount </label>
					<input type="text" value="{{ old('amount') }}" class="form-control"  id="amount" name="amount">
				</div>
			</div>

			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
				<div class="pt-10"></div>
				<button type="submit" class="btn btn-primary btn-block customerSubmit">Add Expense</button>
			</div>
				
			</div>
		</div>
 
    </form>
</div>
</div>
<script type="text/javascript">

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