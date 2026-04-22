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
.container.formbackground {
    width: 100%;
}

</style>
<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 formbackground">

		<div class="fcard mt-30 clearfix">

			<div class="col-sm-12 page_head"> <h3> Modify Expense </h3>  </div>

			<form method="post" class="expenseSetup" action="{{ url('/expenseEditSubmit') }}" class='clearfix'>
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="expenseId" value="{{ $expenses->id }}">
			
			<div class="col-sm-6"> 
				<label>Expense Head Code</label>
				<input type="text" class="form-control" readonly="readonly" id="expenseHeadCode" placeholder="Enter Product Code" name="expenseHeadCode" value="{{ $expenses->expenseHeadCode }}">
			</div>
			<div class="col-sm-6"> 
				<label>Expense Head Name</label>
				<input type="text" class="form-control" value="{{ $expenses->expenseHeadName }}" id="expenseHeadName" placeholder="Enter Product Name" name="expenseHeadName">
			</div>
			<div class="col-sm-12">
				<label>Expense Description</label>
				<textarea class="form-control" id="expenseDescription" name="expenseDescription"> {{ $expenses->expenseDescription }} </textarea>
			</div>

				<div class="col-md-6">
					<div class="pt-20"></div>
					<a href="{{route("expenseList")}}" class="btn btn-danger btn-block">Cancel</a>
				</div>
				<div class="col-md-6">
					<div class="pt-20"></div>					
					<button type="submit" class="btn btn-primary btn-block supplierSubmit">Modify</button>
				</div>

		</div>
 	</form>
</div>


@endsection