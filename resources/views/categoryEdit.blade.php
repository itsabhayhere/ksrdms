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

<a class="nav-back" href="categoryList" title="Back to Category list">
	<i class="fa fa-angle-left"></i>&nbsp; 
	{{-- <span class="sub">Back to Member List</span> --}}
</a>
	
<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
		<div class="fcard mt-30 clearfix">
			<div class="heading">
				<h3>Update Category</h4>
			</div>
			<form method="post" action="{{ url('/categoryEditSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="categoryId" id="categoryId" value="{{ $categorys->id }}">
			
			<div class="col-sm-6 col-md-offset-3 col-lg-4 col-lg-offset-4"> 
				<label>Category Name</label>
				<input type="text" class="form-control" id="categoryName" value="{{ $categorys->name }}" placeholder="Enter Category Name" name="categoryName">
			</div>
			<div class="col-sm-6 col-md-offset-3 col-lg-4 col-lg-offset-4"> 
				<label>Price</label>
				<input type="number" class="form-control" id="categoryPrice" value="{{ $categorys->price }}" placeholder="Enter Category Price" name="categoryPrice">
			</div>
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