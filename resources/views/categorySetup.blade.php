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

<a class="nav-back" href="categoryList" title="Back to Category list">
	<i class="fa fa-angle-left"></i>&nbsp; 
	{{-- <span class="sub">Back to Member List</span> --}}
</a>

<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
	<div class="fcard mt-30 clearfix">
		<div class="heading">
			<h3>Category setup</h4>
		</div>
		<form method="post" action="{{ url('/categorySubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
			<input type="hidden" name="status" value="true"> 
			
			<div class="col-md-12">
				<div class="col-sm-6 col-md-offset-3 col-lg-4 col-lg-offset-4"> 
					<label>Category Name</label>
					<input type="text" value="{{ old('categoryName') }}" class="form-control" id="categoryName" placeholder="Enter Category Name" name="categoryName">
				</div>	
			</div>
			<div class="col-md-12">
				<div class="col-sm-6 col-md-offset-3 col-lg-4 col-lg-offset-4"> 
					<label>Price</label>
					<input type="number" value="{{ old('categoryPrice') }}" class="form-control" id="categoryPrice" placeholder="Enter Category Price" name="categoryPrice">
				</div>	
			</div>
				
			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
				<div class="pt-20"></div>
				<button type="submit" class="btn btn-primary btn-block supplierSubmit">Submit</button>
			</div>
			
		</div>

	</form>
</div>

<script type="text/javascript">

	/* Category code validation */
	function CheckCategoryCode(){
	    var category_code  = document.getElementById("categoryCodePrefix").value + document.getElementById("categoryCode").value;
	 
	    if(category_code){
	            $.ajax({
                type: 'post',
                url: 'categoryCodeValidation',
                data: {
                    category_code: category_code,
                },
                success: function(data) {
                
                	if(data == "true"){
					  document.getElementById("CategoryCodeErrorMessage").innerHTML = "This code is already being used.";
	                  document.getElementById("categoryCode").focus();
	                }else if(data == "false"){
	                  document.getElementById("CategoryCodeErrorMessage").innerHTML = "";
	                }
                }
            });
	    }
	}

</script>
@endsection