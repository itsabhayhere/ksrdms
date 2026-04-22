@extends('theme.default')
@section('content')




<div class="fcard margin-fcard-1 clearfix">



    

    <div class="upper-controls pt-0 clearfix">

        <div class="fl">

            <h3>Category List</h3>

            <div class="light-color f-12">Total: {{count($categorys)}}</div>

            

        </div>

        <div class="fr">
            <a class="btn btn-primary" href="CategoryForm">Add New Category</a>

        </div>

    </div>

    

    

    

    <div class="table-back">

        <table id="MyTable" class="table table-bordered table-striped tright" cellspacing="0" width="100%">

        <thead>

            <tr>


                <th>Categorys Name</th>
                <th>Price</th>

                <th>Edit</th>

                <td class="p-0"></td>

            </tr>

        </thead>



        <tbody>



            @foreach ($categorys as $categorysData)


                <tr>
                    <td>{{ $categorysData->name}}</td>
                    <td>{{ $categorysData->price}}</td>
                    <td>

                        <a href="categoryEdit?categoryid={{ $categorysData->id}}" class="link"> <i class="fa fa-edit"></i> Edit</a>
                    </td>

                    <td class="f-16">

                        <a href="#" onclick="deleteCategoryConfirm({{ $categorysData->id}});" class="danger-text"> <i class="fa fa-trash-o"></i> </a>

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





function deleteCategoryConfirm(categoryid){

//    alert(categoryid);

    $.confirm({

        title: 'Confirm!',

        content: 'You are about to delete category',

        buttons: {

            confirm: function () {

                deleteCategory(categoryid);

                return true;

            },

            cancel: function () {

                return true;

            }

        }

    });

  }



  function deleteCategory(categoryid){

    if(categoryid){

        $.ajax({

                type:"POST",

                url:'{{url("categoryDelete")}}',

                data: {

                    categoryId: categoryid,

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






</script>

@endsection