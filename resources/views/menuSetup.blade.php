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
		<div class="col-sm-12 page_head" > <h3> Menu setup </h3>  </div>
		<div class="col-sm-12">
			<form method="post" class="expenseSetup" action="{{ url('/adminMenuFormSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="col-sm-6"> 
				<label>Menu Title</label>
				<input type="text" class="form-control" id="menuTitle" required placeholder="Enter Menu Title" name="menuTitle">
			</div>
			<div class="col-sm-6"> 
				<label>Url</label>
				<input type="text" class="form-control" id="menuUrl" required placeholder="Enter Url" name="menuUrl">
			</div>
			<div class="col-sm-6"> 
				<label>Sub Menu Of</label>
				<select id="subMenu" name="subMenu" class="subMenu form-control"> 
					<option></option>
					@foreach($adminMenus as $adminMenusValue)
						<option value="{{ $adminMenusValue->id }}"> {{ $adminMenusValue->title }} </option> 
					@endforeach
				</select>
			</div>
			<div class="col-sm-12"> <button type="submit" class="btn btn-default">Submit</button> </div>
			
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