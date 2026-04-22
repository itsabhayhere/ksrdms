@extends('theme.default')

@section('content')



<div class="container" >
    
    <div class="col-sm-12">
        <input type="hidden" name="dairyId" id="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">

        <div class="rate-card-table" id="rate-card-table">

        </div>
        <form method="post" action="{{ url('/getFatSnfRangeDataPdf') }}">
                    <input type="hidden" name="rateCardTable" id="rateCardTable" value=""> 
                    <button style="display: none;" id="getPdfButton" class='getPdfButton'  >Get Pdf</button>
        </form>
        <!-- <input type="hidden" name="rateCardTable" id="rateCardTable" value=""> -->
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


$( document ).ready(function() {
    dairyId = document.getElementById("dairyId").value;

    if(dairyId){
         $.ajax({
                type:"POST",
                url:'getFatSnfRangeData' ,
                data: {
                    dairyId: dairyId,
                },
               success:function(res){  
                var tableHead = "<table style='border: solid black;'>";
                var snfRane = "<tr><td style='border: solid black;'>Fat/Snf</td>" ;
                for(i=0;i<res[0].length;i++){
                   snfRane = snfRane + '<td style="border: solid black;">' + res[0][i] +  '</td>';
                }
                snfRane = snfRane + '</tr>';
                var loopCount = res[2] ;
              
                var fatAndAmount = "<tr>" ;
                for(j=0;j<loopCount;j++){
                   fatAndAmount = fatAndAmount + "<td style='border: solid black;'>" + res[3][j] + "</td>" ;
                    for(k=0;k<res[1][j].length;k++){
                        console.log(res[1][j][k]);
                        fatAndAmount = fatAndAmount + '<td style="border: solid black;">' + res[1][j][k] +  '</td>';
                    }
                    fatAndAmount = fatAndAmount + "</tr>" ;
                }
                
                var tableFoot = "</table>" ;
                var table = tableHead + snfRane + fatAndAmount + tableFoot ;
                
                document.getElementById("rate-card-table").innerHTML = table ;

                // var getPdfButton = "<input type='hidden' value='"+ table +"' name='pdfInfo' id='pdfInfo' class='pdfInfo'><button class='getPdfButton'  >Get Pdf</button>  " ;
                // console.log(getPdfButton);
                // document.getElementById("getFatSnfRangeDataPdfDiv").innerHTML = getPdfButton  ;
                document.getElementById("rateCardTable").value = table ;
                $('#getPdfButton').show();
               }
            });
      }
});


</script>
@endsection