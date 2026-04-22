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

@if(isset($message))
	<div id="successMessage" class="alert alert-success alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
        <strong>{{ $message }}</strong>
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
.expenseSetup{
    width: 84%;
}

.container.formbackground {
    width: 100%;
}

</style>
<div class="container formbackground">
		<div class="col-sm-12 page_head" > <h3> Role setup </h3>  </div>
		<div class="col-sm-12">
			<form method="post" class="expenseSetup" action="{{ url('/roleSetupFormSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
			<input type="hidden" name="status" value="true"> 
			
			<div class="col-sm-6"> 
				<label>Role Name</label>
				<input type="text" class="form-control" id="roleName" required placeholder="Enter Role Name" name="roleName">
			</div>

			<div class="col-sm-12"> <button type="submit" class="btn btn-default supplierSubmit">Submit</button> </div>
			
		</div>
 
    </form>
</div>
</div>
<script type="text/javascript">
	setTimeout(function(){ 
		$("#successMessage").hide(1000);
	}, 3000);
</script>
@endsection