 @extends('theme.default')

@section('content')


<style type="text/css">
.col-sm-12, .col-sm-6  {
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
				<h3>Update Product</h4>
			</div>
			<form method="post" action="{{ url('/productEditSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="productId" id="productId" value="{{ $products->id }}">
			<input type="hidden" id="productUnit" value="{{ $products->productUnit }}" name="productUnit">
			<input type="hidden" id="purchaseAmount" value="{{ $products->purchaseAmount }}" name="purchaseAmount">
			
			<div class="col-sm-6"> 
				<label>Product Code</label>
				  <span class="ProductCodeErrorMessage errorMessage" id="ProductCodeErrorMessage"> </span>
				<input type="text" class="form-control" id="productCode" readonly="readonly" value="{{ $products->productCode }}" onfocusout="CheckProductCode()" placeholder="Enter Product Code" name="productCode">
			</div>
			<div class="col-sm-6"> 
				<label>Product Name</label>
				<input type="text" class="form-control" id="productName" value="{{ $products->productName }}" placeholder="Enter Product Name" name="productName">
			</div>
			 
			<div class="col-sm-6">
				<label>Saling Price</label>
				<input type="text" name="productAmount" class="form-control" value="{{ $products->amount }}" id="productAmount" placeholder="Enter Amount">
			</div> 

			{{-- <div class="col-md-12">				
				<hr>
			</div> --}}

			{{-- <div class="col-sm-6"> 
				<label>Quantity</label>
			</div> --}}

			{{-- <div class="col-sm-6"> 
				<label>Supplier</label>
				<select name="supplier" class="selectpicker" id="supplier" title="Select Supplier">
					@foreach($suppliers as $supps)
					<option value="{{$supps->id}}">{{$supps->supplierFirmName}}</option>
					@endforeach
				</select>	
			</div> --}}
		
	
			{{-- <div class="col-sm-6"> 
				<label>Total Purchase Price</label>
			</div> --}}
			{{-- <div class="col-sm-6">
				<label>Payment Method</label>
				<select name="paymentMethod" class="selectpicker" id="paymentMethod" title="Select method">
					<option value="cash">Cash</option>
					<option value="dabit">Dabit</option>
				</select>
			</div> --}}
			
			{{-- <div class="col-md-12 text-center pt-30">
				<label class="rdolb lh-25"><input type="checkbox" name="isLocalSale" value="1" @if($products->isLocalSale==1) checked @endif>Add this product to Local Sale</label>
			</div> --}}

			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
				<div class="pt-20"></div>
				<button type="submit" class="btn btn-primary btn-block supplierSubmit">Submit</button>
			</div>
		</div>
 	</form>
</div>
</div>
<script type="text/javascript">


</script>
@endsection