@extends('theme.default') 
@section('content') 


<div class="fcard margin-fcard-1 pt-0 clearfix">
    <div class="upper-controls pt-0 clearfix">
        <div class="fl">
            <h3>Expense Type</h3>
            <hr class="m-0">
            {{--<div class="light-color f-12">Total: {{count($purchaseList)}}</div> --}}
        </div>
        <div class="fr">
            {{-- <a class="btn btn-primary" href="expenseSetupForm">Add Expense</a> --}}
        </div>
    </div>

    <div class="clearfix">
        <form method="post" action="{{ url('/expenseTypeSubmit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
            <input type="hidden" name="status" value="true">
            <div class="col-sm-12">
                <div class="col-sm-4"> 
                    <label>Expense Name</label>
                    <select id="expenseType" class="expenseType selectpicker" name="expenseType" onchange="checkPaymentMode(this.value);">
                        @foreach ($expenses as $expensesData)
                            <option value="{{ $expensesData->id }}">{{ $expensesData->expenseHeadName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3"> 
                    <label> Amount </label>
                    <input type="text" value="{{ old('amount') }}" class="form-control"  id="amount" name="amount">
                </div>
                <div class="col-sm-2">
                    <div class="pt-25"></div>
                    <button type="submit" class="btn btn-primary btn-block customerSubmit">Add Expense</button>
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
                    <th>ledger</th>
                    <th>Party Name</th>
                    <th>Expense Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($expenseList as $expenseListData)
                <tr>
                    <td>{{ $expenseListData->ledgerName}}</td>
                    <td>{{ $expenseListData->partyName}}</td>
                    <td>{{ $expenseListData->expenseType}}</td>
                    <td>{{ $expenseListData->amount}}</td>
                    <td>{{ $expenseListData->date}}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>


<script>
    $(document).ready(function() {
        function getSaleAmount(){
            var quantity =  $("#quantity").val();
            var PricePerUnit =  $("#PricePerUnit").val();
            $("#amount").val(quantity*PricePerUnit);
        }

        $(function () {
            $('#date').datetimepicker({
            format: 'DD-MM-YYYY'
            });
        });

        $(function () {
            $('#time').datetimepicker({
            format: 'LT'
            });
        });

        $('#MyTable').DataTable({
            initComplete: function () {
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            //to select and search from grid
                            column.search( val ? '^'+val+'$' : '', true, false ).draw();
                        });
    
                    column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' )
                    });
                });
            }
        });
    });

</script>
@endsection