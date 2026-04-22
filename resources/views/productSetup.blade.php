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
.col-sm-12 {
    margin-top: 12px;
}
.col-sm-6 {
    margin-top: 12px;
}

.errorMessage {
	color: red;
}
.page_head{
	margin: 0 0 0 15px ;	
}

</style>

<a class="nav-back" href="productList" title="Back to Product list">
	<i class="fa fa-angle-left"></i>&nbsp; 
	{{-- <span class="sub">Back to Member List</span> --}}
</a>

<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
	<div class="fcard mt-30 clearfix">
		<div class="heading">
			<h3>Product setup</h4>
		</div>
		<form method="post" action="{{ url('/productSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
			<input type="hidden" name="status" value="true"> 
			
			<div class="col-md-12">
				<div class="col-sm-6"> 
					<label>Product Code</label>
					<span class="ProductCodeErrorMessage errorMessage" id="ProductCodeErrorMessage"> </span>
					<div class="input-group">
						<span class="input-group-addon">{{ Session::get('loginUserInfo')->dairyId }}P</span>
						<input type="hidden" name="productCodePrefix" id="productCodePrefix" value="{{ Session::get('loginUserInfo')->dairyId }}P">
						<input type="text" value="{{ old('productCode') }}" class="form-control" id="productCode" onfocusout="CheckProductCode()" placeholder="Enter Product Code" name="productCode" aria-describedby="basic-addon1">
					</div>
				</div>

				<div class="col-sm-6"> 
					<label>Product Name</label>
					<input type="text" value="{{ old('productName') }}" class="form-control" id="productName" placeholder="Enter Product Name" name="productName">
				</div>	
			</div>
				
			<div class="col-md-12">
				<div class="col-sm-6">
					<label>Sale Price (per unit)</label>
					<input type="text" value="{{ old('productAmount') }}" name="productAmount" class="form-control" id="productAmount" placeholder="Enter Saling Price">
				</div>	
			</div>

			<div class="col-md-12">				
				<hr>
			</div>
			
			<div class="col-md-12">
				<div class="col-sm-6">
					<label>Supplier</label>
					<select name="supplier" class="selectpicker" id="supplier" title="Select Supplier">
						@foreach($suppliers as $supps)
						<option value="{{$supps->id}}">{{$supps->supplierFirmName}}</option>
						@endforeach
					</select>
				</div>

				<div class="col-sm-6">
					<label>Quantity</label>
					<input type="text" value="{{ old('productUnit') }}" class="form-control" id="productUnit" placeholder="Enter Quantity" name="productUnit">
				</div>
			</div>
			
			<div class="col-md-12">
				<div class="col-sm-6">
					<label>Total Purchase Price</label>
					<input type="text" value="{{ old('purchaseAmount') }}" name="purchaseAmount" class="form-control" id="purchaseAmount" placeholder="Enter Purchase Amount">
				</div>
				{{-- <div class="col-sm-6">
					<label>Payment Method</label>
					<select name="paymentMethod" class="selectpicker" id="paymentMethod" title="Select method">
						<option value="cash">Cash</option>
						<option value="credit">Credit</option>
					</select>
				</div> --}}
				<div class="col-sm-6">
					<label>Paid Amount</label>
					<input type="text" value="{{ old('paidAmount') }}" name="paidAmount" class="form-control" id="paidAmount" placeholder="Amount Paid to supplier" value="0">
				</div>
			</div>
			
			{{-- <div class="col-md-12 text-center pt-30">
				<label class="rdolb lh-25"><input type="checkbox" name="isLocalSale" value="1">Add this product to Local Sale</label>
			</div> --}}

			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
				<div class="pt-20"></div>
				<button type="submit" class="btn btn-primary btn-block supplierSubmit">Submit</button>
			</div>
			
		</div>

	</form>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		@if($nosupplier)
			$.confirm({
				title: 'No Supplier found',
				content: 'You need a supplier to add products, Please add atleast 1 supplier.',
				type: 'orange',
				typeAnimated: true,
				buttons: {
					setPrice: {
						text: 'Add Supplier',
						btnClass: 'btn-orange',
						action: function(){
							window.location = "{{url('supplierForm')}}";
						}
					}
				}
			});
		@endif
	})

	/* Product code validation */
	function CheckProductCode(){
	    var product_code  = document.getElementById("productCodePrefix").value + document.getElementById("productCode").value;
	 
	    if(product_code){
	            $.ajax({
                type: 'post',
                url: 'productCodeValidation',
                data: {
                    product_code: product_code,
                },
                success: function(data) {
                
                	if(data == "true"){
					  document.getElementById("ProductCodeErrorMessage").innerHTML = "This code is already being used.";
	                  document.getElementById("productCode").focus();
	                }else if(data == "false"){
	                  document.getElementById("ProductCodeErrorMessage").innerHTML = "";
	                }
                }
            });
	    }
	}

</script>
@endsection