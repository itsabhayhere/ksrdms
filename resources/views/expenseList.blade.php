@extends('theme.default')

@section('content')

@isset($message)
      <div class="alert alert-success">
        Suppplier Created
      </div>
@endisset
<div class="fcard margin-fcard-1 clearfix">

    <div class="upper-controls p-0 clearfix">
        <div class="fl">
            <h3>Expenses</h3>
            <div class="light-color f-12">Total: {{count($expenses)}}</div>
            
        </div>
        <div class="fr">
            {{-- <a class="btn btn-primary" href="expenseForm?dairyId={{{ Session::get('loginUserInfo')->dairyId }}}">Add Expense</a> --}}
        </div>
    </div>

    <div class="clearfix p-20-0">
        <form method="post" action="{{ url('/expenseSubmit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
            <input type="hidden" name="status" value="true">
            <div class="col-sm-12">
                <div class="col-sm-4"> 
                    <label>Expense Head Code</label>
                    <input type="text" id="expenseCode" class="expenseCode form-control" name="expenseCode">
                </div>
                
                <div class="col-sm-4"> 
                    <label>Expense Head Name</label>
                    <input type="text" id="expenseName" class="expenseName form-control" name="expenseName">
                </div>
                
                <div class="col-sm-4"> 
                    <label>Description</label>
                    <input type="text" id="expenseDesc" class="expenseDesc form-control" name="expenseDesc">
                </div>
                <div class="col-sm-2">
                    <div class="pt-25"></div>
                    <button type="submit" class="btn btn-primary btn-block customerSubmit">Add Expense Head</button>
                </div>
            </div>

            {{-- <div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
                <div class="pt-10"></div>
                <button type="submit" class="btn btn-primary btn-block customerSubmit">Add Expense</button>
            </div>
                    --}}
        </form>
    </div>
</div>

<div class="table-back">
    <table id="MyTable" class="display tright" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Expense Code</th>
                <th>Expense Name</th>
                <th>Desc</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($expenses as $expensesData)
                <tr>
                <td>{{ $expensesData->expenseHeadCode}}</td>
                <td>{{ $expensesData->expenseHeadName}}</td>
                <td>{{ $expensesData->expenseDescription}}</td>
                <td> <a href="expenseEdit?expenseId={{ $expensesData->id}}"> Edit </a></td>
                <td> <a href="#" onclick="deleteSupplier({{ $expensesData->id}});"> Delete </a></td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
<script>
$(document).ready(function() {
    $('#MyTable').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                //to select and search from grid
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
} );


function deleteSupplier(expensesId){
//    alert(expensesId);
    
    if(expensesId){
         $.ajax({
                type:"POST",
                url:'expenseDelete' ,
                data: {
                    expenseId: expensesId,
                },
               success:function(res){  
                alert(res);        
                location.reload();
               }
            });
      }
  }

</script>
@endsection