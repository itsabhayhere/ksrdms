@extends('theme.default')
@section('content')


<div class="wmodel" id="milkPriceModel">

    <div class="close">X</div>

    <div class="wmodel-body">



        <h3 class="text-center">Milk Price </h3>

        <hr>

        <form action="setMilkPrice" method="POST">

            <div class="col-sm-12">

                <div class="col-sm-6">

                    <label>Cow Milk</label>

                    <input type="number" class="form-control" id="cowMilk" placeholder="Price" value="{{$dairyInfo->cowMilkPrice}}" name="cowMilkPrice" step="0.05">

                </div>

                <div class="col-sm-6">

                    <label>Buffalo Milk</label>

                    <input type="number" class="form-control" id="bufMilk" placeholder="Price" value="{{$dairyInfo->buffaloMilkPrice}}" name="buffaloMilkPrice" step="0.05">

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

            <h3>Product List</h3>

            <div class="light-color f-12">Total: {{count($products)}}</div>

            

        </div>

        <div class="fr">

            <span class="pr-30">Cow Milk: <b>&#8377; {{$dairyInfo->cowMilkPrice}}</b></span>

            <span class="pr-100">Buffalo Milk: <b>&#8377; {{$dairyInfo->buffaloMilkPrice}}</b></span>

            

            <a href="#" role="button" class="btn btn-primary" id="milkModel" onclick="openMilkPriceSetup(this)">Milk Price</a>

            <a class="btn btn-primary" href="ProductForm">Add New Product</a>

        </div>

    </div>

    

    

    

    <div class="table-back">

        <table id="MyTable" class="table table-bordered table-striped tright" cellspacing="0" width="100%">

        <thead>

            <tr>

                <th>Products Code</th>

                <th>Products Name</th>

                <th>Supplier</th>

                <th>Saling Price &#8377;</th>

                <th>Stock</th>
                
                <th>Purchase Amount &#8377;</th>

                <th>Total Purchase Amount &#8377;</th>

                <th>Edit</th>

                <td class="p-0"></td>

            </tr>

        </thead>



        <tbody>



            @foreach ($products as $productsData)

            @php $s = DB::table("suppliers")->where("id", $productsData->supplierId)->get()->first(); @endphp

                <tr>
                    <input type="hidden" name="initial_quantity_table_field" value="{{ isset($productsData->initial_quantity) ? $productsData->initial_quantity : '' }}"/>
                    <input type="hidden" name="single_purchase_amount_table_field" value="{{ isset($productsData->single_purchase_amount) ? $productsData->single_purchase_amount : '' }}"/>
                    <td>{{ $productsData->productCode}}</td>

                    <td>{{ $productsData->productName}}</td>

                    <td> @if($s!=null){{ $s->supplierFirmName }} @endif</td>

                    <td>{{ $productsData->amount}}</td>

                    <td>{{ number_format($productsData->productUnit, 1, ".", "")}}</td>
                    
                    <td>{{ isset($productsData->single_purchase_amount) ? $productsData->single_purchase_amount : 'NA' }}</td>

                    <td>{{ $productsData->purchaseAmount}}</td>

                    <td>

                        <a href="productEdit?productid={{ $productsData->id}}" class="link"> <i class="fa fa-edit"></i> Edit Rate</a>

                        <a href="productStockAdd?productid={{ $productsData->id}}" class="link"> <i class="fa fa-plus"></i> Add Stock</a>

                    </td>

                    <td class="f-16">

                        <a href="#" onclick="deleteProductConfirm({{ $productsData->id}});" class="danger-text"> <i class="fa fa-trash-o"></i> </a>

                    </td>

                </tr>

            @endforeach



        </tbody>

    </table>

    </div>



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

        },

        "scrollX": true

    });

});





function deleteProductConfirm(productid){

//    alert(productid);

    $.confirm({

        title: 'Confirm!',

        content: 'You are about to delete product',

        buttons: {

            confirm: function () {

                deleteProduct(productid);

                return true;

            },

            cancel: function () {

                return true;

            }

        }

    });

  }



  function deleteProduct(productid){

    if(productid){

        $.ajax({

                type:"POST",

                url:'{{url("productDelete")}}',

                data: {

                    productId: productid,

                },

            success:function(res){

                console.log(res);

                $.alert(res);

                location.reload();

            },

            error:function(res){

                console.log(res);

                $.alert(res);

            }

        });

      }

  }



function openMilkPriceSetup(){

    $('#milkPriceModel').fadeIn();

}

$(".wmodel .close").on('click', function(){

    $('.wmodel').fadeOut();    

})



</script>

@endsection