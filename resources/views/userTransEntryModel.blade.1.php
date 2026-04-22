<div class="row p-5-20">
    <h4>Member Entry on {{date("d-m-Y", strtotime($date))." (".$shift.")"}}</h4>

    <div class="upper-controls pt-0 clearfix">
        <div class="fr">
            <a href="#" role="button" class="btn btn-primary btn-sm" onclick="addTrans(this)"> <i class="fa fa-plus"></i> Add new entry </a>
        </div>
    </div>

    <div class="formNewEntry hide" style="padding-bottom:30px">
            <form method="post" action="{{ url('/DailyTransactionSubmit')."?date=".date("d-m-Y", strtotime($date))." (".$shift.")"}}" class="clearfix" id="dailyForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="dairyId" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
                <input type="hidden" name="status" value="true">
                <input type="hidden" id="date" value="{{date("d-m-Y", strtotime($date))}}" name="date" readonly>
                <input type="hidden" id="dailyShift" value="{{ucfirst($shift)}}" name="dailyShift">

                <div class="col-sm-12">
                    <div class="pt-10"></div>
                    <div class="col-sm-3">
                        <label>Member Code</label>
                        <span class="memberCodeErrorMessage errorMessage"></span>
                        <input type="text" class="form-control" id="memberCodeInputHidden2" name="memberCode" value="{{$memberInfo->memberPersonalCode}}" readonly tabindex="11">
                        <input type="hidden" name="memberName" value="{{$memberInfo->memberPersonalName}}">

                    </div>
            
                    <div class="col-sm-3">
                        <label>Quantity</label>
                        <input type="text" required="true" class="form-control" id="quantity2" onchange="callValues()" placeholder="Enter Quantity" name="quantity" tabindex="12">
                    </div>
            
                    @if(strtolower($dairyInfo->rateCard) == 'fat')
                        <div class="col-sm-6 m-0">
                            <label>Fat</label>
                            <input type="number" class="form-control" id="fatValue2" onchange="callValues()" placeholder="Enter Fat" name="fatValue" step="0.05" tabindex="13">
                        </div>
                    @elseif(strtolower($dairyInfo->rateCard) == 'fat/snf')
                        <div class="col-sm-3 m-0">
                            <label>Fat</label>
                            <input type="number" class="form-control" id="fatSnf_fatValue2" onchange="callValues()" placeholder="Enter Fat" name="fat" required step="0.05" tabindex="13">
                        </div>
                        <div class="col-sm-3 m-0">
                            <label>SNF</label>
                            <input type="number" class="form-control" id="fatSnf_snfValue2" onchange="callValues()" placeholder="Enter Snf" name="snf" required step="0.1" tabindex="14">
                        </div>
                    @endif
            
                </div>
            
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label>Member Name</label>
                        <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage"></span>
                        <div class="member-info-code">{{$memberInfo->memberPersonalName}}</div>
                    </div>
            
                    <div class="col-sm-6 pt-25 text-center">
                        <div class="col-sm-6">
                            <label>Milk Type:
                                <b>
                                    <input type="text" class="noinput" name="milkType" readonly value="{{$info->milkeType}}" size="12">
                                </b>
                            </label>
                        </div>
            
                        <div class="col-sm-6">
                            <label>Rate: 
                                <b>
                                    <input type="text" class="noinput" value="" id="price2" name="price" readonly value="0.0" size="8">
                                </b>
                            </label>
                        </div>
                    </div>
            
                    <div class="col-sm-3">
                        <label>Amount</label>
                        <input required="true" type="text" class="form-control" id="amount2" placeholder="Enter Amount" name="amount" readonly tabindex="5">
                    </div>    
            
                </div>
            
                <div class="col-sm-12 text-center">
                    <div class="pt-10"></div>
                    <button type="submit" name="submit" class="btn btn-primary" tabindex="7">Add Transaction</button>
                </div>
            </form>
    </div>

    <div class="table-back ">
        <table id="MyTable" class="display table table-bordered tright" cellspacing="0">
            <thead>
                <tr>
                    <th>Member Code</th>
                    <th>Milk Type</th>
                    <th>Quantity</th>
                    <th>Fat</th>
                    <th>Snf</th>
                    <th>Rate</th>
                    <th>Total Amount</th>
                    <th></th>
                </tr>
            </thead>

            <tbody class="">
                @foreach ($trans as $dailyTransactionsData)
                <tr>
                    <td>{{ $dailyTransactionsData->memberCode}}</td>
                    <td>{{ $dailyTransactionsData->milkType}}</td>
                    <td>{{ $dailyTransactionsData->milkQuality}}</td>
                    <td>{{ $dailyTransactionsData->fat}}</td>
                    <td>{{ $dailyTransactionsData->snf}}</td>
                    <td>{{ $dailyTransactionsData->rate }}</td>
                    <td>{{ $dailyTransactionsData->amount }}</td>
                    <td>
                        <a href="#" role="button" onclick="updateTrans(this)"> <i class="fa fa-edit"></i> </a>
                        &nbsp;
                        <a href="#" role="button" onclick="deleteTrans(this)"> <i class="fa fa-trash"></i> </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


<script>
    function addTrans(e){
        $(e).hide();
        $(".formNewEntry").removeClass("hide");
    }

        function callValues(){
            snf = $("#fatSnf_snfValue2").val();
            fat = $("#fatSnf_fatValue2").val();
            console.log(snf,fat);

            if($("#fatSnf_snfValue2").val()=="" || $("#fatSnf_fatValue2").val()==""){
                return false;
            }
            console.log(this);
            fetchValues2();
        }
        
        function fetchValues2(){
            snf = $("#fatSnf_snfValue2").val();
            fat = $("#fatSnf_fatValue2").val();

            memberCode = $("#memberCodeInputHidden2").val();

            $.ajax({
                type:"POST",
                url:'fatSnfRateCardvalue',
                data: {
                    dairyId: dairyId,
                    memberCode: memberCode,
                    snf: snf,
                    fat: fat
                },
                success:function(res){
                    $("#price2").val(res.amount);
                    am = (parseFloat($("#quantity2").val()) * parseFloat(res.amount)).toFixed(2);
                    $("#amount2").val(am); 
                },
                error:function(res){
                    console.log(res);
                }
            });
        }
</script>