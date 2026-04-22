 @extends('theme.default')

@section('content')


<style type="text/css">
.col-sm-12,
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
	<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
		<div class="fcard mt-30 clearfix">
				<div class="col-sm-12 page_head" > <h3> Expense setup </h3>  </div>
				<div class="col-sm-12">
					<form method="post" class="expenseSetup" action="{{ url('/expenseSubmit') }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
					<input type="hidden" name="status" value="true"> 
					
					<div class="col-sm-6"> 
						<label>Expense Head Code</label>
						<span class="expenseCodeErrorMessage errorMessage" id="expenseCodeErrorMessage"> </span>
						<input type="text" value="{{ old('expenseHeadCode') }}" class="form-control" id="expenseHeadCode" onfocusout="CheckExpenseHeadCode()" placeholder="Enter Product Code" name="expenseCode">
					</div>
					<div class="col-sm-6"> 
						<label>Expense Head Name</label>
						<input type="text" value="{{ old('expenseHeadName') }}" class="form-control" id="expenseHeadName" placeholder="Enter Product Name" name="expenseName">
					</div>
					<div class="col-sm-12">
						<label>Expense Description</label>
						<textarea class="form-control"  id="expenseDescription" name="expenseDesc"> {{ old('expenseDescription') }}  </textarea>
					</div>

					<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
						<div class="pt-10"></div>
						<button type="submit" class="btn btn-primary btn-block supplierSubmit">Add Expense</button>
					</div>
					
				</div>
		
			</form>
		</div>
	</div>
<script type="text/javascript">

	/* Product code validation */
	function CheckExpenseHeadCode(){
	    var expense_code  = document.getElementById("expenseHeadCode").value;
	 	
	    if(expense_code){
	            $.ajax({
                type: 'post',
                url: 'expenseCodeValidation',
                data: {
                    expense_code: expense_code,
                },
                success: function(data) {
                
                	if(data == "true"){
					  document.getElementById("expenseCodeErrorMessage").innerHTML = "This code is already being used.";
	                  document.getElementById("expenseHeadCode").focus();
	                }else if(data == "false"){
	                  document.getElementById("expenseCodeErrorMessage").innerHTML = "";
	                }
                }
            });
	    }
	}

</script>
@endsection