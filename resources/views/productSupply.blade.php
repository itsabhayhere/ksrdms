@extends('theme.default') 
@section('content')


<div class="wmodel">
    <div class="close">X</div>
    <div class="wmodel-body">

        <h3 class="text-center">Milk Price </h3>
        <hr>
        <form action="setMilkPrice" method="POST">
            <div class="col-sm-12">
                <div class="col-sm-6">
                    <label>Cow Milk</label>
                    <input type="number" class="form-control" id="cowMilk" placeholder="Price" value="{{$dairyInfo->cowMilkPrice}}" name="cowMilkPrice"
                        step="0.05">
                </div>
                <div class="col-sm-6">
                    <label>Buffalo Milk</label>
                    <input type="number" class="form-control" id="bufMilk" placeholder="Price" value="{{$dairyInfo->buffaloMilkPrice}}" name="buffaloMilkPrice"
                        step="0.05">
                </div>
            </div>

            <div class="col-md-6 col-md-offset-3 pt-20">
                <button class="btn btn-primary btn-block" type="submit">Update</a>
            </div>
        </form>
    </div>
</div>


<div class="fcard margin-fcard-1 clearfix">

    <div class="upper-controls pt-0 clearfix">
        <div class="fl">
            <h3>Purchase History</h3>
            <div class="light-color f-12">Total: {{count($products)}}</div>
        </div>
        <div class="fr">
            {{-- <span class="pr-30">Cow Milk: <b>&#8377; {{$dairyInfo->cowMilkPrice}}</b></span>
            <span class="pr-100">Buffalo Milk: <b>&#8377; {{$dairyInfo->buffaloMilkPrice}}</b></span> --}}

            {{-- <a href="#" role="button" class="btn btn-primary" id="milkModel" onclick="openMilkPriceSetup(this)">Milk Price</a> --}}
            <a class="btn btn-primary" href="ProductForm">Add New Products </a>
        </div>
    </div>

    
    <div class="table-back">
        <div class="col-md-12">
                <div class="col-md-3">
                    from <input type="text" class="form-control" name="from" id="from" value="{{$from}}" placeholder="Filter by date">
                </div>
                <div class="col-md-3">
                    to <input type="text" class="form-control" name="to" id="to" value="{{$to}}" placeholder="Filter by date">
                </div>
                <div class="col-md-3">
                    Supplier
                    <select name="supplier" id="supplier" class="selectpicker">
                        <option value="all">All</option>
                        @foreach ($suppliers as $item)
                            <option value="{{$item->id}}">{{$item->supplierFirmName}}</option>
                        @endforeach
                    </select>
                </div>
            <div class="clearfix"></div>
            <br>
        </div>
        
        <div id="table-data">
            <table id="MyTable" class="table table-bordered table-striped tright" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Supplier Code</th>
                        <th>Supplier Name</th>
                        <th>Product Code</th>
                        <th>Quantity</th>
                        <th>Purchase Type</th>
                        <th>Total Purchase Amount &#8377;</th>
                        <th>Paid to supplier</th>
                        <th>Final Amount to be Paid </th>
                    </tr>
                </thead>
        
                <tbody>
        
                    @foreach ($purchase as $purch)
                        <tr>
                            <td>{{ date("d-m-Y", strtotime($purch->date)) }}</td>
                            <td>{{ $purch->supplierCode}}</td>
                            <td>{{ $purch->supplierName}}</td>
                            <td>{{ $purch->productCode. " (". $purch->itemPurchased.")" }}</td>
                            <td>{{ $purch->quantity}}</td>
                            <td>{{ ucfirst($purch->purchaseType)}}</td>
                            <td>&#8377; {{ $purch->amount}}</td>
                            <td>&#8377; {{ $purch->paidAmount}}</td>
                            <td>&#8377; {{ $purch->amount - $purch->paidAmount}}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>


</div>


<script>
$(document).ready(function() {
    table = $('#MyTable').DataTable( {
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
        },
        "scrollX": true
    } );

});


    function openMilkPriceSetup(){
        $('.wmodel').fadeIn();
    }
    $(".wmodel .close").on('click', function(){
        $('.wmodel').fadeOut();    
    })

    $("#from, #to").datetimepicker({ format: 'DD-MM-YYYY'});

    $("#from, #to").on("dp.change", function(){
        console.log("skdjfgk");
        fetch_purchaseHistory_by_date(this)
    });

    $("#supplier").on("change", function(){
        fetch_purchaseHistory_by_date(this)
    });

    
    function fetch_purchaseHistory_by_date(e){
        loader('show'); 

        fromdate = $("#from").val();
        todate = $("#to").val();
        supp = $("supplier").val();

        $.ajax({
            type:"POST",
            url:'{{url("getPurchaseHistoryByDate")}}',
            data: {
                from: fromdate,
                to: todate,
                supplierId: supp
            },
            success:function(res){
                loader("hide");
                $("#table-data").html(res.cont);
                // $("#from").val(res.from);
                // $("#to").val(res.to);
                table.destroy();
                $(".table").DataTable();
            },
            error:function(res){
                loader("hide");
                console.log(res);
            }
        });
    }


</script>
@endsection