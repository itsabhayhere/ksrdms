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
				<h3>Add Product Stock</h3>
			</div>
			<form method="post" action="{{ url('/productStockSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="productId" id="productId" value="{{ $products->id }}">
			
			<div class="col-sm-6"> 
				<label>Product Code</label>
				  <span class="ProductCodeErrorMessage errorMessage" id="ProductCodeErrorMessage"> </span>
				<input type="text" class="form-control" id="productCode" readonly="readonly" value="{{ $products->productCode }}" onfocusout="CheckProductCode()" placeholder="Enter Product Code" name="productCode">
			</div>
			<div class="col-sm-6">
				<label>Product Name</label>
				<input type="text" class="form-control" id="productName" value="{{ $products->productName }}" placeholder="Enter Product Name" name="productName" readonly>
			</div>

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
				<input type="text" class="form-control" id="productUnit" value="{{ $products->productUnit }}" placeholder="Enter Quantity" name="productUnit">
			</div>

            <div class="col-sm-6">
				<label>Saling Price</label>
				<input type="text" name="productAmount" class="form-control" value="{{ $products->amount }}" id="productAmount" placeholder="Enter Amount">
			</div>
	
			<div class="col-sm-6">
				<label>Total Purchase Price</label>
				<input type="text" class="form-control" id="purchaseAmount" value="" placeholder="Enter Purchase Amount" name="purchaseAmount">
			</div>
			
			<div class="col-sm-6">
				<label>Paid to supplier</label>
				<input type="text" class="form-control" id="" value="" placeholder="Enter Paid Amount to supplier" name="paidAmount">
			</div>

			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
				<div class="pt-20"></div>
				<button type="submit" class="btn btn-primary btn-block supplierSubmit">Add Stock</button>
			</div>
		</div>
 	</form>
</div>
</div>
<script type="text/javascript">


</script>
@endsection
