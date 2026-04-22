@extends('theme.default') 
@section('content') @if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<style type="text/css">
    .errorMessage {
        color: red;
    }
</style>
<!-- <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet"> -->

<a class="nav-back" href="DailyTransactionForm" title="Back to Taransaction list">  
    <i class="fa fa-angle-left"></i>&nbsp; 
    {{-- <span class="sub">Back to Member List</span> --}}
</a>

<div class="fcard mt-30 clearfix">

    <div class="heading">
        <h3>Edit Transaction</h4>
    </div>

    <div class="col-sm-12">
        <form method="post" action="{{ url('/DailyTransactionEditSubmit') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="dairyId" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
            <input type="hidden" name="status" value="true">
            <input type="hidden" name="transactionId" id="transactionId" class="transactionId" value="{{ $returnData[3]->id }}">

            <div class="col-md-12 clearfix">
                <div class="col-sm-6">
                    <label>Date</label>
                    <input type="text" class="form-control" id="date" placeholder="Date" value="{{ $returnData[3]->date }}" name="date">
                </div>
                <div class="col-sm-6">
                    <label>Shift</label>
                    <select id="dailyShift" class="dailyShift form-control selectpicker" name="dailyShift">
                        <option {{ $returnData[3]->date == 'morning' ? 'selected="selected"' : '' }} value="morning"> Morning Shift </option>
                        <option {{ $returnData[3]->date == 'evening' ? 'selected="selected"' : '' }} value="evening"> Evening Shift </option>
                    </select>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="pt-10"></div>
                <div class="col-sm-3">
                    <label>Member Code</label>
                    <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessage"></span> {{-- {{--
                    <input
                        type="text" id="memberCode" class="form-control" onchange="SetMemberCode(this)" name="memberCode"> --}}
                        <input id="memberCode" class="form-control" required value="{{ $returnData[3]->memberCode }}">
                        <input type="hidden" id="memberCode-id" onchange="SetMemberCode(this)" name="memberCode" required value="{{ $returnData[3]->memberCode }}">
                        <script>
                            $( function() {
                            var projects = [
                                @foreach ($returnData[0] as $memberInfoData)
                                    {
                                        value: "{{ $memberInfoData->memberPersonalCode }}",
                                        label: "{{ $memberInfoData->memberPersonalCode }}",
                                        desc: "{{ $memberInfoData->memberPersonalName }}",
                                    },
                                @endforeach
                            ];
                        
                            $( "#memberCode" ).autocomplete({
                                minLength: 0,
                                source: projects,
                                focus: function( event, ui ) {
                                    $( "#memberCode" ).val( ui.item.label );
                                    return false;
                                },
                                select: function( event, ui ) {
                                    $( "#memberCode" ).val( ui.item.label );
                                    $( "#memberCode-id" ).val( ui.item.value );
                                    // $( "#memberCode-description" ).html( ui.item.desc );
                                    $("#memberCode-id").trigger("change");
                                    return false;
                                }
                            })
                            .autocomplete( "instance" )._renderItem = function( ul, item ) {
                                return $( "<li>" )
                                    .append( "<div>" + item.label + "<br>" + item.desc + "</div>" )
                                        .appendTo( ul );
                                    };
                                } );
                        </script>
                </div>

                <div class="col-sm-3">
                    <label>Quantity</label>
                    <input type="text" required="true" class="form-control" placeholder="Enter Quantity" id="quantity" name="quantity" value="{{$returnData[3]->milkQuality}}">
                </div>

                @if(strtolower($returnData[1]) == 'fat')
                <div class="col-sm-6 m-0" id="rateForRateCardFat">
                    <label>Fat</label>
                    <input type="number" class="form-control" placeholder="Enter Fat" id="fatValue" name="fatValue" step="0.05" value="{{ $returnData[3]->fat }}">
                </div>
                @elseif(strtolower($returnData[1]) == 'fat/snf')
                <div class="col-sm-3 m-0" id="rateForRateCardFat">
                    <label>Fat</label>
                    <input type="number" class="form-control" placeholder="Enter Fat" id="fatSnf_fatValue" name="fat" required step="0.05" value="{{ $returnData[3]->fat }}">
                </div>
                <div class="col-sm-3 m-0" id="rateForRateCardSnf">
                    <label>SNF</label>
                    <input type="number" class="form-control" placeholder="Enter Snf" id="fatSnf_snfValue" name="snf" required step="0.1" value="{{ $returnData[3]->snf }}">
                </div>
                @endif

            </div>

            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label>Member Name</label>
                    <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage"></span>
                    <input required="true" type="hidden" id="memberNameInputHidden" name="memberName" value="{{ $returnData[3]->memberName }}">
                    <input required="true" type="hidden" id="memberCodeInputHidden" name="memberCode" value="{{ $returnData[3]->memberCode }}">
                    <div class="member-info-code"><span style="letter-spacing: 1px;font-weight: 100; color:#929292">{{$returnData[3]->memberName}}</span></div>
                    {{-- <datalist id="memberNameList">
                        @foreach ($returnData[0] as $memberInfoData)
                            <option value="{{ $memberInfoData->memberPersonalName }}">
                            <label id="memberName{{ $memberInfoData->memberPersonalName }}">{{ $memberInfoData->memberPersonalCode }}</label>
                        @endforeach
                    </datalist> --}}
                </div>

                <div class="col-sm-3">
                    <label>Milk Type:</label>
                    <select name="milkType" class="selectpicker" id="milkType" readonly>
                        <option {{ $returnData[3]->milkType == 'buffalo' ? 'selected="selected"' : '' }} value="buffalo">Buffalo</option>
                        <option {{ $returnData[3]->milkType == 'cow' ? 'selected="selected"' : '' }} value="cow">Cow</option>
                    </select>
                </div>

                <div class="col-sm-3">
                    <label>Rate: 
                        <b>
                            <input type="text" class="noinput" value="" id="price" readonly value="0.0" size="8">
                        </b>
                    </label>
                </div>

                <div class="col-sm-3">
                    <label>Amount</label>
                    <input required="true" type="text" class="form-control" placeholder="Enter Amount" id="amount" name="amount" value="{{ $returnData[3]->amount }}"
                        readonly>
                </div>

            </div>

            <div class="col-sm-12 text-center">
                <div class="pt-10"></div>
                <button type="submit" name="submit" class="btn btn-primary" >Update Transaction</button>
            </div>
        </form>
    </div>
</div>

<div class="container">
    {{-- <a href="DailyTransactionForm">Add DailyTransaction</a> --}}
    <div class="col-sm-12">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Member Code</th>
                    <th>Member Name</th>
                    <th>Date</th>
                    <th>Milk Type</th>
                    <th>Milk Quality</th>
                    <th>Rate Card Type</th>
                    <th>Fat</th>
                    <th>Snf</th>
                    <th>Amount</th>
                    <th>Edit</th>
                    <th>Notification</th>
                    <th>Pdf</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($returnData[2] as $dailyTransactionsData)
                <tr>
                    <td>{{ $dailyTransactionsData->memberCode}}</td>
                    <td>{{ $dailyTransactionsData->memberName}}</td>
                    <td>{{ $dailyTransactionsData->date}}</td>
                    <td>{{ $dailyTransactionsData->milkType}}</td>
                    <td>{{ $dailyTransactionsData->milkQuality}}</td>
                    <td>{{ $dailyTransactionsData->shift}}</td>
                    <td>{{ $dailyTransactionsData->fat}}</td>
                    <td>{{ $dailyTransactionsData->snf}}</td>
                    <td>{{ $dailyTransactionsData->amount }}</td>
                    <td> <a href="DailyTransactionEdit?transactionId={{ $dailyTransactionsData->id}}"> Edit </a></td>
                    <td> <a href="DailyTransactionResendNoti?transactionId={{ $dailyTransactionsData->id}}&memberCode={{ $dailyTransactionsData->memberCode}}&dairyId={{ $dailyTransactionsData->dairyId}}"> Notification </a></td>
                    <td> <a href="DailyTransactionPsf?listId={{ $dailyTransactionsData->id}}"> Get Pdf </a></td>
                    <td> <a href="DailyTransactionDelete?transactionId={{ $dailyTransactionsData->id}}&memberCode={{ $dailyTransactionsData->memberCode}}&dairyId={{ $dailyTransactionsData->dairyId}}"> Delete </a></td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>


<script type="text/javascript">

    var rateCardReq = false;

    $("#fatSnf_fatValue, #fatSnf_snfValue, #quantity").on("keyup", function(){
            if($("#fatSnf_snfValue").val()=="" || $("#fatSnf_fatValue").val()==""){
                return false;
            }
            if(rateCardReq){
                return false;
            }

            rateCardReq = true;

            snf = $("#fatSnf_snfValue").val();
            fat = $("#fatSnf_fatValue").val();

            memberCode = $("#memberCodeInputHidden").val();

            $.ajax({
                type:"POST",
                url:'fatSnfRateCardvalue' ,
                data: {
                    dairyId: dairyId,
                    memberCode: memberCode,
                    snf: snf,
                    fat:fat
                },
                success:function(res){
                    rateCardReq = false;

                    $("#price").val(res.amount);
                    am = (parseFloat($("#quantity").val()) * parseFloat(res.amount)).toFixed(2);
                    $("#amount").val(am); 
                    $("#milkType").val(res.milkType);
                },
                error:function(res){
                    rateCardReq = false;
                    console.log(res);
                }
            });
        }) 

    function SetMemberCode(e){
        v = $(e).val();
        $("#memberCodeInputHidden").val(v);
    }

	/* date picker */
    $(function () {
        $('#date').datetimepicker({
             format: 'YYYY:MM:DD'
        });
    });

    $( "#date" ).click(function() {
      $('#SetShiftField').show();
    });

    /* get rate by fat and fat/snf  */

    function getRateByFatSnf(PreFat,PreSnf){
      dairyId = document.getElementById("dairyId").value ;

      $.ajax({
          type:"POST",
          url:'fatSnfRateCardvalue' ,
          data: {
              dairyId: dairyId,
              fatRange: PreFat,
              snfRange: PreSnf,
          },
          success:function(res){   
           var Quantity = document.getElementById("quantity").value;
           var amount = parseInt(Quantity)+parseInt(res);
            document.getElementById("amount").value = amount;
          }
      });
    }


 /* get member name by member code */
 function CheckMemberCode(){
    var member_code  = document.getElementById("memberCode").value;
      if(member_code){
         $.ajax({
                type:"POST",
                url:'DailyTransactionMemberCode' ,
                data: {
                    member_code: member_code,
                },
                success:function(res){          
              	  if(res == ""){
  				          document.getElementById("memberCodeErrorMessage").innerHTML = "Member Code is not valid.";
                    document.getElementById("memberCode").focus();
                    document.getElementById("memberName").value = "";
                  }else{
                    document.getElementById("memberName").value = res;
                    document.getElementById("memberCodeErrorMessage").innerHTML = "";
                    document.getElementById("quantity").focus();
                  }
                }
            });
      }
  }


  /* Supplier email validation  */
  function checkRateCardType(){
 
	var rateCardType  = document.getElementById("rateCardType").value;
	
	if(rateCardType == "Rate on Fat/Snf"){
		$("#rateForRateCardFat").show();
		$("#rateForRateCardSnf").show();
		
	}else{
		$("#rateForRateCard").show();
		$("#rateForRateCardFat").show();
		$("#rateForRateCardSnf").hide();
	}
  }


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

</script>
@endsection