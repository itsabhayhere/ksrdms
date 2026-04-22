@extends('theme.default')
@section('content')
<!-- custome css   -->
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

<link href="{{ asset('css/addon/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/addon/tabs.css') }}" rel="stylesheet" />
<script type="text/javascript" href="{{ asset('js/addon/bootstrap.js') }}">

</script>
<style type="text/css">
    * {
        box-sizing: border-box;
    }


    #regForm {
        background-color: #ffffff;
        margin: auto;
        font-family: Raleway;
        padding: 40px;
        width: 100%;
        min-width: 300px;
    }

    h1 {
        color: #0337ac;
    }

    input {
        padding: 10px;
        width: 100%;
        font-size: 17px;
        font-family: Raleway;
        border: 1px solid #d6d6d6;
        margin: 0;
    }
    .form-control{
        margin-bottom: 10px;
    }

    .table>tbody>tr>td,
    .table>tbody>tr>th,
    .table>tfoot>tr>td,
    .table>tfoot>tr>th,
    .table>thead>tr>td,
    .table>thead>tr>th {
        padding: 0;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: none;
    }

    #fatrateCardFatSnf123 .table>tbody>tr>td,
    #fatrateCardFatSnf123 .table>tbody>tr>th,
    #fatrateCardFatSnf123 .table>tfoot>tr>td,
    #fatrateCardFatSnf123 .table>tfoot>tr>th,
    #fatrateCardFatSnf123 .table>thead>tr>td,
    #fatrateCardFatSnf123 .table>thead>tr>th {
        padding: 5px 8px;
    }

    /* Mark input boxes that gets an error on validation: */

    input.invalid {
        background-color: #ffdddd;
    }

    /* Hide all steps by default: */

    .tab {
        display: none;
    }

    /* button {
        background-color: #0337ac;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 17px;
        font-family: Raleway;
        cursor: pointer;
        border-radius: 4px;
    }

    button:hover {
        opacity: 0.8;
    } */

    #prevBtn {
        background-color: #bbbbbb;
    }

    /* Make circles that indicate the steps of the form: */

    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }

    .step.active {
        opacity: 1;
    }

    /* Mark the steps that are finished and valid: */

    .step.finish {
        background-color: #0337ac;
    }

    textarea {
        margin-top: 10px;
    }


    select[multiple],
    select[size] {
        height: 250px;
        width: 300px;
    }

    .ErrorMsg {
        color: red;
    }

    .midRangeFatSnf {
        margin: 15px 0 0 0;
    }

    /* input#midRangeOfFatSnfPrice {
        margin-left: -50px;
        width: 91%;
    } */

    .aa {
        padding-bottom: 10px;
    }

    .rate label {
        margin-top: 12px;
        float: right;
        text-align: right;
    }
</style>

<!-- One "tab" for each step in the form: -->


<!-- <div class="w3-bar w3-black">

    <div class="col-sm-2">

        <label style="margin-left:20px;">  <input type="radio"  class="w3-bar-item w3-button tablink w3-red" name="rateType" onclick="openCity(event,'mm')" checked >Rate on FAT</label>

    </div>

    <div class="col-sm-2">

        <label style="margin-left:20px;">  <input type="radio" name="rateType" class="w3-bar-item w3-button tablink" onclick="openCity(event,'kk')">Rate on FAT / SNF </label>
    </div>
    <div class="col-sm-8"></div>
</div> -->
<div class="rate fcard margin-fcard-1 clearfix">
    <input type="hidden" name="DairyIdFat" id="DairyIdFat" class="DairyIdFat" value="{{{ Session::get('loginUserInfo')->dairyId }}}">@if(Session::get('colMan')->rateCardTypeForCow
    == "fat")
    <div id="mm" class="w3-container w3-border city">

        <!-- <form method="post" id="regForm" action="{{url('/rateCardFetSubmit')}}"> -->

        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <input type="hidden" name="currentRateCardIdFat" id="currentRateCardIdFat" class="currentRateCardIdFat">
        <input type="hidden" name="rateType" id="rateType" class="rateType" value="fat">
        <h2>Rate on FAT</h2>

        <div class="col-sm-12 aa">
            <div class="col-md-2">
                <label> Collection Manager </label>
            </div>
            <div class="col-md-4">
                <select required id="CollectionManager" class="CollectionManager form-control selectpicker" name="CollectionManager" data-live-search="true">
                    <option value="DAIRYADMIN">Dairy Admin</option>
                    @foreach ($collactionManager as $collactionManagerData)
                            <option value="{{ $collactionManagerData->id }}">{{ $collactionManagerData->userName }}</option>
                    @endforeach
                </select>
            </div>

        </div>



        <div class="col-sm-12">

            <div class="col-sm-2">
                <label>Minimum FAT Value</label>
            </div>
            <div class="col-sm-4">
                <span id="minFatValueError" class="ErrorMsg">  </span>
                <input type="number" id="minFatValue" name="minFatValue" class="form-control" onfocusout="checkMinFatValue()" required placeholder="Minimum FAT Value">
            </div>


            <div class="col-sm-2">
                <label>Max FAT Value</label>
            </div>
            <div class="col-sm-4">
                <span id="maxFatValueError" class="ErrorMsg">  </span>
                <input type="number" id="maxFatValue" name="maxFatValue" class="form-control" onfocusout="checkMaxFatValue()" required placeholder="Max FAT Value">
            </div>

        </div>


        <div class="col-sm-12">

            <div class="col-sm-2">
                <label>Rate for FAT (minimum)</label>
            </div>
            <div class="col-sm-4">
                <span id="rateForMinFatValueError" class="ErrorMsg">  </span>
                <input type="number" class="form-control" id="rateForMinFatValue" name="rateForMinFatValue" onfocusout="checkRateForMinFatValue()"
                    required placeholder="Rate for FAT (minimum)">
            </div>

            <div class="col-sm-2">
                <label>Rate increase FAT </label>
            </div>
            <div class="col-sm-4">
                <span id="rateIncreseForFatError" class="ErrorMsg">  </span>
                <input type="number" class="form-control" id="rateIncreseForFat" name="rateIncreseForFat" onfocusout="checkRateIncreseForFat()"
                    required placeholder="Rate increase FAT">
            </div>

        </div>

        <div class="col-sm-12">

            <div style="margin-top:10px;">

                <div class="col-sm-3">

                </div>

                <div class="col-sm-3">
                    <button id="generareRateCard" type="submit" onclick="getRangeid()" style="margin:10px; display: none;">Generare Rate Card   </button>
                </div>

                <div class="col-sm-3">

                </div>
            </div>
        </div>

        <div class="col-sm-12">

            <table class="table table-bordered table-striped ">

                <div id="fatrateCard" class="fatrateCard">
                    <p></p>
                </div>
                <div id="fatrateCardValue" class="fatrateCardValue">
                    <p></p>
                </div>
            </table>

        </div>
    </div>
    @else
    <!-- </form> -->
    <div id="kk" class="w3-container w3-border city">
        <h2>Rate on SNF</h2>

        <!-- <form method="post" id="regForm" action="{{url('/rateCardFetSnfSubmit')}}"> -->


        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="fatSnfdairyId" id="fatSnfDairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">

        <div class="col-sm-12 aa">
            <div class="col-md-2">
                <label> Collection Manager </label>
            </div>
            <div class="col-md-4">
                <select name="fatSnfCollectionManager" id="fatSnfCollectionManager" class="fatSnfCollectionManager form-control selectpicker" data-live-search="true">
                    @foreach ($collactionManager as $collactionManagerData)
                            <option value="{{ $collactionManagerData->id }}">{{ $collactionManagerData->userName }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="col-sm-12">

            <div class="col-sm-2">
                <label>Minimum FAT Value</label>
            </div>
            <div class="col-sm-4">
                <span id="fatSnfMinFatValueError" class="ErrorMsg">  </span>
                <input type="number" name="fatSnfMinFatValue" onfocusout="CheckfatSnfFatValue()" id="fatSnfMinFatValue" class="form-control fatSnfMinFatValue"
                    required placeholder="Enter Min Fat Value">
            </div>
            <div class="col-sm-2">
                <label>Max FAT Value</label>
            </div>
            <div class="col-sm-4">
                <span id="fatSnfMaxFatValueError" class="ErrorMsg">  </span>
                <input type="number" class="form-control fatSnfMaxFatValue" onfocusout="checkfatSnfMinFatValue()" required name="fatSnfMaxFatValue"
                    id="fatSnfMaxFatValue" placeholder="Enter Max Fat Value Fat/SNF">
            </div>

        </div>

        <div class="col-sm-12">

            <div class="col-sm-2">
                <label>Minimum SNF Value</label>
            </div>
            <div class="col-sm-4">
                <span id="fatSnfMinSnfValueError" class="ErrorMsg">  </span>
                <input type="number" class="form-control fatSnfMinSnfValue" onfocusout="checkfatSnfMinSnfValue()" name="fatSnfMinSnfValue"
                    id="fatSnfMinSnfValue" required placeholder="Enter Min SNF Value">
            </div>

            <div class="col-sm-2">
                <label>Max SNF Value</label>
            </div>
            <div class="col-sm-4">
                <span id="fatSnfMaxSnfValueError" class="ErrorMsg">  </span>
                <input type="number" class="form-control fatSnfMaxSnfValue" onfocusout="checkfatSnfMaxSnfValue()" name="fatSnfMaxSnfValue"
                    id="fatSnfMaxSnfValue" required placeholder="Enter Max SNF Value">
            </div>

        </div>

        <div class="col-sm-12">
            <h3>Select range for FAT / SNF</h3>
        </div>

        <div class="col-sm-12">


            <div class="col-sm-2">
                <label>Minimum FAT Value of range</label>
            </div>
            <div class="col-sm-4">
                <span id="fatSnfMinFatValueRangeError" class="ErrorMsg">  </span>
                <input type="number" class="form-control fatSnfMinFatValueRange" id="fatSnfMinFatValueRange" name="fatSnfMinFatValueRange"
                    required placeholder="Enter Min Fat Value for range">
            </div>

            <div class="col-sm-2">
                <label>Max FAT Value of range</label>
            </div>
            <div class="col-sm-4">
                <span id="fatSnfMaxFatValueRangeError" class="ErrorMsg">  </span>
                <input type="number" class="form-control fatSnfMaxFatValueRange" onfocusout="checkfatSnfMaxFatValueRange()" name="fatSnfMaxFatValueRange"
                    id="fatSnfMaxFatValueRange" required placeholder="Enter Max Fat Value for range">
            </div>

        </div>
        <div class="col-sm-12">
            <div class="col-sm-2">
                <label class="midRangeFatSnf">Rate for Fat <b><span class="midRangeOfFat" id="midRangeOfFat"></span></b> and Snf <b><span class="midRangeOfSnf" id="midRangeOfSnf" ></span></b> </label>
            </div>
            <div class="col-sm-4">
                <input type="number" class="form-control midRangeOfFatSnfPrice" id="midRangeOfFatSnfPrice" name="midRangeOfFatSnfPrice" required
                    placeholder="Enter Value">
            </div>

        </div>

        <div class="col-sm-12">

            <div class="col-sm-12">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    <span class="fatSnfMidValue" id="fatSnfMidValue">  </span>
                </div>
            </div>

        </div>

        <div class="col-sm-12">

            <div class="col-sm-2">
                <label>Rate Increase FAT</label>
            </div>
            <div class="col-sm-4">
                <input type="number" class="form-control fatSnfRateIncreaseByFat" id="fatSnfRateIncreaseByFat" name="fatSnfRateIncreaseByFat"
                    required placeholder="Enter Increase Rate for Fat">
            </div>


            <div class="col-sm-2">
                <label>Rate Increase SNF</label>
            </div>
            <div class="col-sm-4">
                <input type="number" class="form-control fatSnfRateIncreaseBySnf" id="fatSnfRateIncreaseBySnf" name="fatSnfRateIncreaseBySnf"
                    required placeholder="Enter Increase Rate for SNF">
            </div>

        </div>

        <div class="col-sm-12">

            <div class="col-sm-2">
                <label>Rate Decrease FAT</label>
            </div>
            <div class="col-sm-4">
                <input type="number" class="form-control fatSnfRateDecreaseByFat" name="fatSnfRateDecreaseByFat" id="fatSnfRateDecreaseByFat"
                    required placeholder="Enter Fat Value">
            </div>


            <div class="col-sm-2">
                <label>Rate Decrease SNF</label>
            </div>
            <div class="col-sm-4">
                <input type="number" class="form-control fatSnfRateDecreaseBySnf" id="fatSnfRateDecreaseBySnf" name="fatSnfRateDecreaseBySnf"
                    onfocusout="getAllFatSnfRangeValue()" required placeholder="Enter Max Fat Value">

                <input type="number" class="form-control fatSnfRateDecreaseBySnfEdit" style="display: none;" id="fatSnfRateDecreaseBySnfEdit"
                    name="fatSnfRateDecreaseBySnfEdit" required placeholder="Enter Max Fat Value">
            </div>

        </div>

        <div class="col-sm-12">
            <div style="margin-top:10px;">
                <div class="col-sm-3">
                </div>

                <div class="col-sm-3">
                    <button id="generateRateCardByFatSnfTable" onclick="generateRateCardByFatSnfTable()" class="btn btn-primary" style="margin:10px;display: none;">Save Range </button>
                    <input type="hidden" id="currnetRangeId" name="currnetRangeId" value="">
                    <button id="editSubmitRateCardByFatSnfTable" onclick="editSubmitRateCardByFatSnfTable()" style="display: none;">Submit Edited Range </button>
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-primary" style="margin:10px;">Cancel</button>
                </div>
                <div class="col-sm-3"> </div>
            </div>

        </div>

        <div class="col-sm-12">
            <div id="fatrateCardFatSnf123">

                <div id="fatrateCardFatSnf" class="fatrateCardFatSnf">
                    <p></p>
                </div>
                <div id="fatrateCardFatSnfValue" class="fatrateCardFatSnfValue">
                    <p></p>
                </div>
                <div id="rangeRateCardValue" class="rangeRateCardValueClass">
                    <p></p>
                </div>
            </div>
            <button id="generateAllRateCardByFatSnf" class="generateAllRateCardByFatSnf btn btn-primary" style="display: none;" onclick="generateAllRateCardByFatSnf();"> Generare Rate Card </button>
        </div>
    </div>
    @endif
    <!-- </form> -->


    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Rate</h4>
                </div>
                <div class="modal-body">
                    Range: <input type="number" readonly="readonly" name="EditRangeByFat" id="EditRangeByFat" class="EditRangeByFat form-control">                    Rate: <input type="number" name="EditRateByFat" id="EditRateByFat" class="EditRateByFat form-control">
                    <input type="hidden" name="EditRateByFatPosition" id="EditRateByFatPosition" checked="EditRateByFatPosition">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="fatRateEditSubmit()" data-dismiss="modal">Submit</button>
                </div>
            </div>

        </div>
    </div>


    <div class="col-sm-12">
        <table class="table table-bordered table-striped">

            <div id="finalRateCard" class="finalRateCard">
                <p></p>
            </div>
            <div id="finalRateCardValueTemp" class="finalRateCardValue">

            </div>
            <div id="finalRateCardValueSubmitButton" class="finalRateCardValueSubmitButton">

            </div>
            <div id="finalRateCardValue" class="finalRateCardValue">

            </div>


        </table>
        <button id="saveRateCard" class="saveRateCard" onclick="saveFinalrateCard();" style="display: none;"> Save Rate Card </button>
    </div>

</div>
@endsection







<script type="text/javascript">
    var listRange = [] ;
 var tableCount = 1 ;
 var totalFatCount = 0 ;
 var totalSnfCount = 0 ;
 var getValueLoopCunt = [] ;

    function generateAllRateCardByFatSnf(){
        var headingRow = "";


        DairyIdFat = document.getElementById("DairyIdFat").value;

        /* get min fat value */
        fatSnfMinSnfValue = document.getElementById("fatSnfMinSnfValue").value;
        fatSnfMinSnfValue = Math.round(parseFloat(fatSnfMinSnfValue) * 100) / 100;       

        /* convert all range int and string to float */
        fatSnfMaxSnfValue = document.getElementById("fatSnfMaxSnfValue").value;
        fatSnfMaxSnfValue = Math.round(parseFloat(fatSnfMaxSnfValue) * 100) / 100;

        var fatStartPosttion = 0;
        var snflist = CalculateNumberBtwRange(fatSnfMinSnfValue, fatSnfMaxSnfValue,0) ;  
         
        $.ajax({
            type: 'post',
            url: 'getFatSnfRangeTable',
            data: {
                dairyId: DairyIdFat
            },
            success: function(data) {
                var i;
                var dataLangth = data.length-1
                var dataLoopCount = 0 ;
                for (i = 0; i < data.length; i++) {
                    
                    headingRow = "Rate card for Range" + i + "</br>";
                    $('#finalRateCardValueTemp').append(headingRow);
                       if( i > 0 ){
                        var totalCount = CalculateNumberBtwRange(data[i].minFatRange, data[i].maxFatRange, 1);
                        fatStartPosttion = totalCount.length ;
                    }
                    else
                    {
                         fatStartPosttion = 0  ;
                    }
                    callRateCardGeneration(data[i].minFatRange, data[i].maxFatRange, fatSnfMinSnfValue, fatSnfMaxSnfValue, data[i].RateByMidPoint, data[i].rateIncreseByFatIncrese, data[i].rateDecreaseByFatDecrease, data[i].rateIncreseBySnfIncrese, data[i].rateDecreaseBySnfDecrease,fatStartPosttion);
                   dataLoopCount++
                    tableCount =  tableCount + 1 ;
                    totalFatCount++ ;
                }
                dataLoopCountLength = dataLoopCount-1 
                if(dataLoopCountLength == dataLangth){
                    var testData = 'hello test rate card' ;
                    var rateCardSaveButton = "<button id='rateCardSaveButton' onclick='rateCardSaveButton()' style='margin: 10px;'> Save Your Rate Card </button>"
                    document.getElementById('finalRateCardValueSubmitButton').innerHTML = rateCardSaveButton ;
                }
                //finalRateCardValueSubmitButton
            }
        });
    }


    function rateCardSaveButton() {
       var loopCount = [] ;
       var fatSnfCollectionManager = document.getElementById("fatSnfCollectionManager").value ;
       DairyIdFat = document.getElementById("DairyIdFat").value;
       // var testValue = document.getElementById("input111").value ;
       for(i=1;i<=totalFatCount;i++){
           var totalRowCount = 0;
           var rowCount = 0;
           var table = document.getElementById("tblRateCard"+i);
           var rows = table.getElementsByTagName("tr");
            for (var j = 0; j < rows.length; j++) {
                totalRowCount++;
                if (rows[j].getElementsByTagName("td").length > 0) {
                    rowCount++;
                }
            }
        var message = "Total Row Count: " + totalRowCount;
        message += "\nRow Count: " + rowCount;

        loopCount.push(rowCount);
       }
       var tdId = 0 ;

       /* remove all old fat/snf with rate */
       var oldFatSnfRateCardDeleteSuccess = 0 ;
        $.ajax({
            type: 'post',
            url: 'oldFatSnfRateCardDelete',
            data: {
                dairyId: DairyIdFat,
            },
            success: function(res) {
                // alert(res);
                if(res == "Success"){
                    oldFatSnfRateCardDeleteSuccess = 1 ;
                }
            }
        });

        
        setTimeout(function() {
          // window.location = "fatSnfRateCardShow";
          // alert(oldFatSnfRateCardDeleteSuccess);
        var currentSnfValue = [] ;
        for(i=1;i<=totalFatCount;i++){
            var currentPosition = i - 1;
            var inputFieldCount = 0 ;
            for(j=1;j<=loopCount[currentPosition];j++){
                var tdCount = document.getElementById("tblRateCard"+i).rows[0].cells.length ;
                var currentFatRange = "" ;
                
                var currentValue = "" ;
                for(k=0;k<tdCount;k++){
                    if(k == 0 && inputFieldCount == 0){
                       console.log($('#input'+i+inputFieldCount+k).text());
                    }else{
                        if(inputFieldCount == 0){
                                currentSnfValue.push($('#input'+i+inputFieldCount+k).val()) ;
                        }else{
                            if(k == 0){
                                currentFatRange = $('#input'+i+inputFieldCount+k).val() ;    
                            }else{
                                currentValue = $('#input'+i+inputFieldCount+k).val();     
                            }
                        }
                        if(currentValue != "" && currentFatRange != "" && currentSnfValue != "" ){
                            //  console.log(currentSnfValue[k-1]);
                            $.ajax({
                                type: 'post',
                                url: 'fatSnfRateCardSubmit',
                                data: {
                                    dairyId: DairyIdFat,
                                    collectionManager: fatSnfCollectionManager,
                                    fatRange: currentFatRange,
                                    snfRange: currentSnfValue[k-1],
                                    amount: currentValue,
                                },
                                success: function(res) {
                                    // console.log(res);
                                }
                            });
                        }
                    }
                }
            inputFieldCount++;
            }
        }
        }, 8000); 
        
     
   
        // alert(tdId);
        
        setTimeout(function() {
          // window.location = "fatSnfRateCardShow";
        }, 3000); 
    }

    function saveFinalrateCard(){
        DairyIdFat = document.getElementById("DairyIdFat").value;

            /* get min fat value */
        fatSnfMinSnfValue = document.getElementById("fatSnfMinSnfValue").value;
        fatSnfMinSnfValue = Math.round(parseFloat(fatSnfMinSnfValue) * 100) / 100;

            /* convert all range int and string to float */
        fatSnfMaxSnfValue = document.getElementById("fatSnfMaxSnfValue").value;
        fatSnfMaxSnfValue = Math.round(parseFloat(fatSnfMaxSnfValue) * 100) / 100;

         $.ajax({
            type: 'post',
            url: 'getFatSnfRangeTable',
            data: {
                dairyId: DairyIdFat
            },
            success: function(data) {
                arrayPosition = data.length - 1;
                currentRangeValue = parseFloat(data[arrayPosition].maxFatRange);
                
                if (currentRangeValue == fatSnfMaxFatValue) {
                    document.getElementById("fatSnfMinFatValueRange").value = "";
                } else {
                    document.getElementById("fatSnfMinFatValueRange").value = currentRangeValue + .1;
                }

                document.getElementById("fatSnfMaxFatValueRange").value = "";
                document.getElementById("midRangeOfFatSnfPrice").value = "";
                document.getElementById("fatSnfRateIncreaseByFat").value = "";
                document.getElementById("fatSnfRateIncreaseBySnf").value = "";
                document.getElementById("fatSnfRateDecreaseByFat").value = "";
                document.getElementById("fatSnfRateDecreaseBySnf").value = "";
                document.getElementById("midRangeOfFat").innerHTML = "";
                document.getElementById("midRangeOfSnf").innerHTML = "";
                document.getElementById("fatSnfMinFatValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMinFatValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMinSnfValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMaxSnfValue").setAttribute("readonly", "readonly");
                
                var filed = "<table class='table table-bordered table-striped'><thead><tr><th> Range </th> <th> Min Fat </th> <th> Max Fat </th><th> mid Range Of Fat Snf Price </th> <th> Rate increase for fat increase  </th><th> Rate increase for snf increase </th> <th> Rate decrease for fat decrease </th><th> Rate decrease for snf decrease </th> <th> mid point fat </th><th> mid point snf </th><th > Action </th></tr></thead>";

                // $("#fatrateCardFatSnf").html(filed);
                var i;
                var dataValue = "";
                for (i = 0; i < data.length; i++) {
                    dataValue = dataValue + "<tr>";
                    dataValue = dataValue + "<td > Range" + i + "</td>";
                    dataValue = dataValue + "<td >" + data[i].minFatRange + "</td>";
                    dataValue = dataValue + "<td >" + data[i].maxFatRange + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateIncreseByFatIncrese + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateIncreseBySnfIncrese + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateDecreaseByFatDecrease + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateDecreaseBySnfDecrease + "</td>";
                    dataValue = dataValue + "<td >" + data[i].MidPointFat + "</td>";
                    dataValue = dataValue + "<td >" + data[i].MidPointSnf + "</td>";
                    dataValue = dataValue + "<td >" + data[i].RateByMidPoint + "</td>";
                    if (i == arrayPosition) {
                        dataValue = dataValue + "<td ><a onclick='editCorrentRange("+data[i].id+");'  class='btn btn-default' >Edit</a> <a class='btn btn-default' onclick='fatSnfDeleteRange(" + DairyIdFat + ")>Delete</a></td>";
                    } else {
                        dataValue = dataValue + "<td ><a class='btn btn-default' onclick='fatSnfDeleteRange(" + DairyIdFat + ")' >Delete</a></td>";
                    }

                    dataValue = dataValue + "</tr>";
                    mainArray = dataValue;
                    callRateCardGeneration(data[i].minFatRange, data[i].maxFatRange, fatSnfMinSnfValue, fatSnfMaxSnfValue, data[i].RateByMidPoint, data[i].rateIncreseByFatIncrese, data[i].rateDecreaseByFatDecrease, data[i].rateIncreseBySnfIncrese, data[i].rateDecreaseBySnfDecrease);
                }

                alert("Successfully Created");
                $("#fatrateCardFatSnf123").html(filed + dataValue + "</table>");
            }

        });

        callRateCardGeneration();
    }

    function editSubmitRateCardByFatSnfTable(){
        DairyIdFat = document.getElementById("DairyIdFat").value;

        /* currnet Range Id */
        currnetRangeId = document.getElementById("currnetRangeId").value;
        currnetRangeId = Math.round(parseFloat(currnetRangeId) * 100) / 100;

        /* min fat value */
        fatSnfMinFatValueRange = document.getElementById("fatSnfMinFatValueRange").value;
        fatSnfMinFatValueRange = Math.round(parseFloat(fatSnfMinFatValueRange) * 100) / 100;

        /* max fat value */
        fatSnfMaxFatValueRange = document.getElementById("fatSnfMaxFatValueRange").value;
        fatSnfMaxFatValueRange = Math.round(parseFloat(fatSnfMaxFatValueRange) * 100) / 100;

        /* mid Range Of fat value */
        midRangeOfFat = document.getElementById("midRangeOfFat").innerHTML;
        midRangeOfFat = Math.round(parseFloat(midRangeOfFat) * 100) / 100;

        /* mid Range Of nf */
        midRangeOfSnf = document.getElementById("midRangeOfSnf").innerHTML;
        midRangeOfSnf = Math.round(parseFloat(midRangeOfSnf) * 100) / 100;

        /* mid Range Of Fat Snf Price */
        midRangeOfFatSnfPrice = document.getElementById("midRangeOfFatSnfPrice").value;
        midRangeOfFatSnfPrice = Math.round(parseFloat(midRangeOfFatSnfPrice) * 100) / 100;

        /* fat Snf Rate Increase By Fat*/
        fatSnfRateIncreaseByFat = document.getElementById("fatSnfRateIncreaseByFat").value;
        fatSnfRateIncreaseByFat = Math.round(parseFloat(fatSnfRateIncreaseByFat) * 100) / 100;

        /* fat Snf Rate Increase By Snf */
        fatSnfRateIncreaseBySnf = document.getElementById("fatSnfRateIncreaseBySnf").value;
        fatSnfRateIncreaseBySnf = Math.round(parseFloat(fatSnfRateIncreaseBySnf) * 100) / 100;

        /* fat Snf Rate Decrease By Fat */
        fatSnfRateDecreaseByFat = document.getElementById("fatSnfRateDecreaseByFat").value;
        fatSnfRateDecreaseByFat = Math.round(parseFloat(fatSnfRateDecreaseByFat) * 100) / 100;

          /* fat Snf Rate Decrease By Snf */
        fatSnfRateDecreaseBySnf = document.getElementById("fatSnfRateDecreaseBySnfEdit").value;
        fatSnfRateDecreaseBySnf = Math.round(parseFloat(fatSnfRateDecreaseBySnf) * 100) / 100;


        $.ajax({
            type: 'post',
            url: 'fatSnfSingleRangeEditSubmit',
            data: {
                DairyIdFat:DairyIdFat,
                currnetRangeId : currnetRangeId,
                fatSnfMinFatValueRange : fatSnfMinFatValueRange,
                fatSnfMaxFatValueRange : fatSnfMaxFatValueRange,
                midRangeOfFat : midRangeOfFat,
                midRangeOfSnf : midRangeOfSnf,
                midRangeOfFatSnfPrice : midRangeOfFatSnfPrice,
                fatSnfRateIncreaseByFat : fatSnfRateIncreaseByFat,
                fatSnfRateIncreaseBySnf : fatSnfRateIncreaseBySnf,
                fatSnfRateDecreaseByFat : fatSnfRateDecreaseByFat,
                fatSnfRateDecreaseBySnf : fatSnfRateDecreaseBySnf,
            },
            success: function(data) {
                arrayPosition = data.length - 1;
                currentRangeValue = parseFloat(data[arrayPosition].maxFatRange);
                
                if (currentRangeValue == fatSnfMaxFatValue) {
                    document.getElementById("fatSnfMinFatValueRange").value = "";
                } else {
                    document.getElementById("fatSnfMinFatValueRange").value = currentRangeValue + .1;
                }

                document.getElementById("fatSnfMaxFatValueRange").value = "";
                document.getElementById("midRangeOfFatSnfPrice").value = "";
                document.getElementById("fatSnfRateIncreaseByFat").value = "";
                document.getElementById("fatSnfRateIncreaseBySnf").value = "";
                document.getElementById("fatSnfRateDecreaseByFat").value = "";
                document.getElementById("fatSnfRateDecreaseBySnf").value = "";
                document.getElementById("midRangeOfFat").innerHTML = "";
                document.getElementById("midRangeOfSnf").innerHTML = "";
                document.getElementById("fatSnfRateDecreaseBySnfEdit").innerHTML = "";
                document.getElementById("fatSnfMinFatValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMinFatValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMinSnfValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMaxSnfValue").setAttribute("readonly", "readonly");
               
                var filed = "<table class='table table-bordered table-striped'> <thead><tr><th> Range </th> <th> Min Fat </th> <th> Max Fat </th><th> mid Range Of Fat Snf Price </th> <th> Rate increase for fat increase  </th><th> Rate increase for snf increase </th> <th> Rate decrease for fat decrease </th><th> Rate decrease for snf decrease </th> <th> mid point fat </th><th> mid point snf </th><th > Action </th></tr></thead>";

                // $("#fatrateCardFatSnf").html(filed);
                var i;
                var dataValue = "";
                for (i = 0; i < data.length; i++) {
                    dataValue = dataValue + "<tr>";
                    dataValue = dataValue + "<td > Range" + i + "</td>";
                    dataValue = dataValue + "<td >" + data[i].minFatRange + "</td>";
                    dataValue = dataValue + "<td >" + data[i].maxFatRange + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateIncreseByFatIncrese + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateIncreseBySnfIncrese + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateDecreaseByFatDecrease + "</td>";
                    dataValue = dataValue + "<td >" + data[i].rateDecreaseBySnfDecrease + "</td>";
                    dataValue = dataValue + "<td >" + data[i].MidPointFat + "</td>";
                    dataValue = dataValue + "<td >" + data[i].MidPointSnf + "</td>";
                    dataValue = dataValue + "<td >" + data[i].RateByMidPoint + "</td>";
                    if (i == arrayPosition) {
                       dataValue = dataValue + "<td ><a onclick='editCorrentRange("+data[i].id+");'  class='btn btn-default' >Edit</a> <a class='btn btn-default' onclick='fatSnfDeleteRange(" + DairyIdFat + ")>Delete</a></td>";
                    } else {
                        dataValue = dataValue + "<td ><a class='btn btn-default' onclick='fatSnfDeleteRange(" + DairyIdFat + ")' >Delete</a></td>";
                    }

                    dataValue = dataValue + "</tr></table>";
                    mainArray = dataValue;
                    
                }

                fatSnfMaxFatValueMax = document.getElementById("fatSnfMaxFatValue").value;
                fatSnfMaxFatValueMax = Math.round(parseFloat(fatSnfMaxFatValueMax) * 100) / 100;

                alert("Successfully Created");
               
                if(fatSnfMaxFatValueRange != fatSnfMaxFatValueMax){
                    $("#generateAllRateCardByFatSnf").hide();
                    $("#fatSnfRateDecreaseBySnf").show();
                    $("#fatSnfRateDecreaseBySnfEdit").hide();
                }else{
                    $("#generateAllRateCardByFatSnf").show();
                }                
                $("#editSubmitRateCardByFatSnfTable").hide();
                $("#generateRateCardByFatSnfTable").show();
                    

                $("#fatrateCardFatSnf123").html(filed + dataValue + "</table>");
            }

        });

    }

    function editCorrentRange(data){
        
            if(data){
                $.ajax({
                type: 'post',
                url: 'fatSnfSingleRange',
                data: {
                    dataId: data
                },
                success: function(returnData) {
                    //currnetRangeId
                    document.getElementById("currnetRangeId").value = returnData[0].id;
                    document.getElementById("fatSnfMinFatValueRange").value = returnData[0].minFatRange;
                    document.getElementById("fatSnfMaxFatValueRange").value = returnData[0].maxFatRange;
                    document.getElementById("midRangeOfFat").innerHTML = returnData[0].MidPointFat;
                    document.getElementById("midRangeOfSnf").innerHTML = returnData[0].MidPointSnf;
                    document.getElementById("midRangeOfFatSnfPrice").value = returnData[0].RateByMidPoint;
                    document.getElementById("fatSnfRateIncreaseByFat").value = returnData[0].rateIncreseByFatIncrese;
                    document.getElementById("fatSnfRateIncreaseBySnf").value = returnData[0].rateIncreseBySnfIncrese;
                    document.getElementById("fatSnfRateDecreaseByFat").value = returnData[0].rateDecreaseByFatDecrease;
                    document.getElementById("fatSnfRateDecreaseBySnfEdit").value = returnData[0].rateDecreaseBySnfDecrease;

                    $("#fatSnfRateDecreaseBySnf").hide();
                    $("#fatSnfRateDecreaseBySnfEdit").show();

                    $("#generateRateCardByFatSnfTable").hide();
                    $("#editSubmitRateCardByFatSnfTable").show();
                }
            });
        }

    }

    function getRoundFingerValue(str){
        
        if(str != ""){
            var stringCount = str.length - 5 ;
            var str = str.slice(0, -stringCount);
              return str ;
        }else{
                return str ;
        }
    }

    function fatSnfDeleteRange(delId) {
     
        var confirmBox = confirm("Are You Sour");
        if (confirmBox == true) {
            location.reload();
        } 

    }

     function CalculateNumberBtwRange(a, b, c)
    {
        var list = [];
        var mainValue = 0;
        var minFat = 0;
        var minSnf = 0;
        var maxSnf = 0;
        var maxFat = 0;

        if (c == 1)
        {
            minFat = parseFloat(a);
            maxFat = parseFloat(b);
            var list = [];
            mainValue = parseFloat(.1);
            for (var i = minFat; minFat <= maxFat; i = i + mainValue) {

                if(i > minFat){
                    minFat = minFat + mainValue ;
                    minFat = parseFloat(minFat);
                    list.push(minFat)
                }else{
                    list.push(minFat);
                }
            }           
        }else{            
            minSnf = parseFloat(a) ;
            maxSnf = parseFloat(b) ;
            mainValue = 10 ;
            for (var i = minSnf; minSnf <= maxSnf; i = i + mainValue) {
                if(i > minSnf){
                    minSnf = minSnf + mainValue ;
                    minSnf = parseFloat(minSnf);
                    list.push(minSnf)
                }else{
                    list.push(minSnf);
                }
            }
        }
        return list ;
    }


    function GeneratePriceForRange(midvaluePrice,SNFList,FATList, RateIncreseFAtIncrease, RateDecreaseFATDEcrease, RateIncreaseSNFIncrease, RateDecreaseSNFDecrease)
    {
        var inputValue
        var FATValue = 0;
        var PreviousColValue = 0;
        var currentColValue = 0;
        var previousColumn = 0;
        var previousRow = 0
        var midSNFPosition = Math.round(SNFList.length / 2);
        var midFATPosition = Math.round(FATList.length / 2);
        var RateDecrease = RateDecreaseSNFDecrease;
        var RateIncrease = RateIncreseFAtIncrease;
        //Loop 1  to get 1st value of the array  i.e. arr[0,0]
        for( i = midFATPosition; i>0 ; i--)
        {
            for(j = midSNFPosition;j>0;j--)
            {
                if(j==midSNFPosition && i==midFATPosition)
                {
                    currentColValue = parseFloat(midvaluePrice) * 100 / 100;
                    document.getElementById("input" + tableCount + i + j).value = parseFloat(midvaluePrice) * 100 / 100;
                    inputValue = document.getElementById("input" + tableCount + i + j).value;
                }
                else
                {
                    if (j == midSNFPosition) {
                        previousColumn = midSNFPosition;
                        previousRow = i+1;
                        PreviousColValue = (document.getElementById("input" + tableCount + previousRow + previousColumn)).value;
                        currentColValue = parseFloat(PreviousColValue) * 100 / 100 - parseFloat(RateDecrease) * 100 / 100;
                        document.getElementById("input" + tableCount + i + j).value = parseFloat(currentColValue) * 100 / 100;
                    }
                    else {
                        RateDecrease = RateDecreaseSNFDecrease;
                        previousColumn = j + 1;
                        PreviousColValue = (document.getElementById("input" + tableCount + i + previousColumn)).value;
                        currentColValue = parseFloat(PreviousColValue) * 100 / 100 - parseFloat(RateDecrease) * 100 / 100;
                        document.getElementById("input" + tableCount + i + j).value = parseFloat(currentColValue) * 100 / 100;
                    }
                }
             }
            RateDecrease = RateDecreaseFATDEcrease;
        }
        // alert("Hurry !!! WE got the First Value" + currentColValue);

        //Loop 2 to populate the full 2 dimentional array
        PreviousColValue = currentColValue;
        previousRow = 0;
        for(i=1; i<=FATList.length;i++)
        {
            for(j=1;j<SNFList.length;j++)
            {
                if (j == 1 && i == 1) {
                    document.getElementById("input" + tableCount + i + j).value = parseFloat(PreviousColValue) * 100 / 100;
                    var value = document.getElementById("input" + tableCount + i + j).value;
                }
                else
                {
                    if (j == 1) {
                        RateIncrease = RateIncreseFAtIncrease;
                        previousRow = i - 1;
                        PreviousColValue = (document.getElementById("input" + tableCount + previousRow + j)).value;
                        currentColValue = parseFloat(PreviousColValue) * 100 / 100 + parseFloat(RateIncrease) * 100 / 100;
                        document.getElementById("input"+ tableCount + i + j).value = parseFloat(currentColValue) * 100 / 100;
                        var value = document.getElementById("input" + tableCount + i + j).value;
                       
                    }
                    else {
                        RateIncrease = RateIncreaseSNFIncrease;
                        previousColumn = j - 1;
                        PreviousColValue = (document.getElementById("input" + tableCount + i + previousColumn)).value;
                        currentColValue = parseFloat(PreviousColValue) * 100 / 100 + parseFloat(RateIncrease) * 100 / 100;
                        document.getElementById("input"+ tableCount + i + j).value = parseFloat(currentColValue) * 100 / 100;
                        var value = document.getElementById("input" + tableCount + i + j).value;
                    }
                }
            }
            RateIncrease = RateIncreseFAtIncrease;
        }
      //  currentTable = document.getElementById("finalRateCardValueTemp").innerHTML;
    }

    
    function callRateCardGeneration(minFAT, maxFAT, minSNF, maxSNF, midPointRate, RateIncreseFAtIncrease, RateDecreaseFATDEcrease, RateIncreaseSNFIncrease, RateDecreaseSNFDecrease) {
        
        var currentTable = document.getElementById("finalRateCardValueTemp");
        var fatCount = parseFloat(0) *100/100 ;
        var minFat = parseFloat(minFAT)*100/100;
        var maxFat = parseFloat(maxFAT)*100/100;
        var minSnf = minSNF;
        var maxSnf = maxSNF;
        var dataValue = "<table id='tblRateCard" + tableCount + "' class='table table-bordered table-striped'>" ;
        var fatlist = CalculateNumberBtwRange(minFAT, maxFAT, 1);
        var snflist = CalculateNumberBtwRange(minSNF, maxSNF,0) ;    
        var fatValue = 0;
        var snfValue = minSnf;
        var snfCount = 0;        
        var RowloopCount = Math.round(parseFloat(fatlist.length) * 100) / 100;
        var ColloopCount = Math.round(parseFloat(snflist.length) * 100) / 100;
        // totalFatCount
        // totalSnfCount
      
        for(i=0;i<=fatlist.length;i++){

            if(parseFloat(fatValue).toFixed(1) >= parseFloat(maxFAT).toFixed(1)){
                break;
            }
            dataValue = dataValue + "<tr>";

            for (j = 0; j <snflist.length; j++) {
                if (i > 0)
                {
                    snfCount = 0;
                    minSnf = 0;
                    if (j == 0){
                        fatValue = parseFloat(minFat)*100/100  + parseFloat(fatCount)*100/100;
                        dataValue = dataValue + "<th > <input readonly type='text' id='input"+ tableCount + i + j + "' value='" + parseFloat(fatValue).toFixed(1) + "' />  </th>";
                        mainArray = dataValue;
                        fatCount = parseFloat(fatCount)*100/100 + parseFloat(.1)*100/100;
                    }else{
                        dataValue = dataValue + "<td ><input type='text' id='input"+ tableCount + i + j + "'  /> </td>";
                    }
                }
                else if (j > 0 && i==0)  {
                    snfValue = snfValue + snfCount;
                    dataValue = dataValue + "<th> <input readonly type='text' id='input"+ tableCount + i + j + "'  value='" + snflist[j-1] + "' />  </th>";
                    mainArray = dataValue;
                    snfCount = 10;
                }
                else { 
                    dataValue = dataValue + "<th id='input"+ tableCount + i + j + "' > <span>Fat\\Snf</span></th>";
                }
                
            }
        dataValue = dataValue + "</tr>";
        totalSnfCount++ ;
        getValueLoopCunt.push(i);
        }
        dataValue = dataValue + "</table>"
        $('#finalRateCardValueTemp').append(dataValue);

        //Populating price

        var FATValue = 0;
        var PreviousColValue = 0;
        var currentColValue = 0;
        var previousColumn = 0;
        var previousRow = 0
        var midSNFPosition = Math.round(snflist.length / 2);
        var midFATPosition = Math.round(fatlist.length / 2);
        var RateDecrease = RateDecreaseSNFDecrease;
        var RateIncrease = RateIncreseFAtIncrease;
        //Loop 1  to get 1st value of the array  i.e. arr[0,0]
        for( i = midFATPosition; i>0 ; i--){
          
            for(j = midSNFPosition;j>0;j--){
                
                if(j==midSNFPosition && i==midFATPosition){
                   currentColValue = parseFloat(midPointRate) * 100 / 100;
                    document.getElementById("input" + tableCount + i + j).value = parseFloat(midPointRate) * 100 / 100;
                }else{
                    if (j == midSNFPosition) {
                        previousColumn = midSNFPosition;
                        previousRow = i+1;
                        PreviousColValue = (document.getElementById("input" + tableCount + previousRow + previousColumn)).value;
                        currentColValue = parseFloat(PreviousColValue) * 100 / 100 - parseFloat(RateDecrease) * 100 / 100;
                        document.getElementById("input" + tableCount + i + j).value = parseFloat(currentColValue) * 100 / 100;
                    }
                    else {
                        RateDecrease = RateDecreaseSNFDecrease;
                        previousColumn = j + 1;
                        PreviousColValue = (document.getElementById("input" + tableCount + i + previousColumn)).value;
                        currentColValue = parseFloat(PreviousColValue) * 100 / 100 - parseFloat(RateDecrease) * 100 / 100;
                        document.getElementById("input" + tableCount + i + j).value = parseFloat(currentColValue) * 100 / 100;
                    }
                }
             }
            RateDecrease = RateDecreaseFATDEcrease;
        }
    
        //Loop 2 to populate the full 2 dimentional array
        PreviousColValue = currentColValue;
        previousRow = 0;
        for(i=1; i<=fatlist.length;i++){
                
                if(tableCount > 1)
               {
                 if(i==fatlist.length)
                 {
                    break;
                 }
               }
           
                for(j=1;j<snflist.length;j++)
                {
                  
                    if (j == 1 && i == 1) {
                        document.getElementById("input" + tableCount + i + j).value = parseFloat(PreviousColValue).toFixed(2);;
                    }
                    else{
                          var loopTotle = fatlist.length - 1 ;
                        if (j == 1) {
                            RateIncrease = RateIncreseFAtIncrease;
                            previousRow = i - 1;
                            PreviousColValue = (document.getElementById("input" + tableCount + previousRow + j)).value;
                            currentColValue = parseFloat(PreviousColValue) * 100 / 100 + parseFloat(RateIncrease) * 100 / 100;
                            var loopTotle = fatlist.length - 1 ;
                            if(i <= 11){
                                document.getElementById("input"+ tableCount + i + j).value = parseFloat(currentColValue).toFixed(2);    
                            }else{
                                if(i <=  loopTotle ){
                                    document.getElementById("input"+ tableCount + i + j).value = parseFloat(currentColValue).toFixed(2);    
                                }    
                            }   
                            
                        }
                        else {
                            RateIncrease = RateIncreaseSNFIncrease;
                            previousColumn = j - 1;
                            // var mainValueJLoop = document.getElementById("input" + tableCount + i + previousColumn) ;
                             if(i <= 11){
                                document.getElementById("input"+ tableCount + i + j).value = parseFloat(currentColValue).toFixed(2);    
                            }else{
                                if(i <=  loopTotle ){
                                    PreviousColValue = (document.getElementById("input" + tableCount + i + previousColumn)).value;

                                    currentColValue = parseFloat(PreviousColValue) * 100 / 100 + parseFloat(RateIncrease) * 100 / 100;
                                    document.getElementById("input"+ tableCount + i + j).value = parseFloat(currentColValue).toFixed(2);
                                }
                            }
                        }
                    }
                }
            RateIncrease = RateIncreseFAtIncrease;
        }
        
    }

    function generateRateCardByFatSnfTable() {

        DairyIdFat = document.getElementById("DairyIdFat").value;

        /* max fat value */
        fatSnfMaxFatValueRange = document.getElementById("fatSnfMaxFatValueRange").value;
        fatSnfMaxFatValueRange = Math.round(parseFloat(fatSnfMaxFatValueRange) * 100) / 100;

        /* last added range max value */
        fatSnfMaxFatValueRange = document.getElementById("fatSnfMaxFatValueRange").value;
        fatSnfMaxFatValueRange = Math.round(parseFloat(fatSnfMaxFatValueRange) * 100) / 100;

        /* get min fat value */
        fatSnfMinSnfValue = document.getElementById("fatSnfMinSnfValue").value;
        fatSnfMinSnfValue = Math.round(parseFloat(fatSnfMinSnfValue) * 100) / 100;

        fatSnfMinFatValue = document.getElementById("fatSnfMinFatValue").value;
        fatSnfMinFatValue = Math.round(parseFloat(fatSnfMinFatValue) * 100) / 100;


        /* get max fat value */
        fatSnfMaxFatValue = document.getElementById("fatSnfMaxFatValue").value;
        fatSnfMaxFatValue = Math.round(parseFloat(fatSnfMaxFatValue) * 100) / 100;

        /* convert all range int and string to float */
        fatSnfMaxSnfValue = document.getElementById("fatSnfMaxSnfValue").value;
        fatSnfMaxSnfValue = Math.round(parseFloat(fatSnfMaxSnfValue) * 100) / 100;
        
        $.ajax({
            type: 'post',
            url: 'getFatSnfRangeTable',
            data: {
                dairyId: DairyIdFat
            },
            success: function(data) {
                arrayPosition = data.length - 1;
                currentRangeValue = parseFloat(data[arrayPosition].maxFatRange);
                
                if (currentRangeValue == fatSnfMaxFatValue) {
                    document.getElementById("fatSnfMinFatValueRange").value = "";
                } else {
                    document.getElementById("fatSnfMinFatValueRange").value = currentRangeValue + .1;
                }

                document.getElementById("fatSnfMaxFatValueRange").value = "";
                document.getElementById("midRangeOfFatSnfPrice").value = "";
                document.getElementById("fatSnfRateIncreaseByFat").value = "";
                document.getElementById("fatSnfRateIncreaseBySnf").value = "";
                document.getElementById("fatSnfRateDecreaseByFat").value = "";
                document.getElementById("fatSnfRateDecreaseBySnf").value = "";
                document.getElementById("midRangeOfFat").innerHTML = "";
                document.getElementById("midRangeOfSnf").innerHTML = "";

                document.getElementById("fatSnfMinFatValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMinFatValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMinSnfValue").setAttribute("readonly", "readonly");
                document.getElementById("fatSnfMaxSnfValue").setAttribute("readonly", "readonly");
             

                var filed = "<table class='table table-bordered table-striped'><thead><tr> <th> Range </th> <th> Min Fat </th> <th> Max Fat </th><th> mid Range Of Fat Snf Price </th> <th> Rate increase for fat increase  </th><th> Rate increase for snf increase </th> <th> Rate decrease for fat decrease </th><th> Rate decrease for snf decrease </th> <th> mid point fat </th><th> mid point snf </th><th > Action </th></tr></thead>";

                // $("#fatrateCardFatSnf").html(filed);
                var i;
                var dataValue = "";
                for (i = 0; i < data.length; i++) {
                    dataValue = dataValue + "<tr>";
                    dataValue = dataValue + "<td> Range" + i + "</td>";
                    dataValue = dataValue + "<td>" + data[i].minFatRange + "</td>";
                    dataValue = dataValue + "<td>" + data[i].maxFatRange + "</td>";
                    dataValue = dataValue + "<td>" + data[i].rateIncreseByFatIncrese + "</td>";
                    dataValue = dataValue + "<td>" + data[i].rateIncreseBySnfIncrese + "</td>";
                    dataValue = dataValue + "<td>" + data[i].rateDecreaseByFatDecrease + "</td>";
                    dataValue = dataValue + "<td>" + data[i].rateDecreaseBySnfDecrease + "</td>";
                    dataValue = dataValue + "<td>" + data[i].MidPointFat + "</td>";
                    dataValue = dataValue + "<td>" + data[i].MidPointSnf + "</td>";
                    dataValue = dataValue + "<td>" + data[i].RateByMidPoint + "</td>";
                    if (i == arrayPosition) {
                        dataValue = dataValue + "<td ><a onclick='editCorrentRange("+data[i].id+");'  class='btn btn-default' >Edit</a> <a class='btn btn-default' onclick='fatSnfDeleteRange(" + DairyIdFat + ")>Delete</a></td>";
                    } else {
                        dataValue = dataValue + "<td ><a class='btn btn-default' onclick='fatSnfDeleteRange(" + DairyIdFat + ")' >Delete</a></td>";
                    }

                    dataValue = dataValue + "</tr></table>";
                    mainArray = dataValue;
                  
               

                        var range = {
                             minFatRange: data[i].minFatRange ,
                             maxFatRange: data[i].maxFatRange ,
                             fatSnfMinSnfValue: fatSnfMinSnfValue  ,
                             fatSnfMaxSnfValue: fatSnfMaxSnfValue  ,
                             RateByMidPoint: data[i].RateByMidPoint ,
                             rateIncreseByFatIncrese: data[i].rateIncreseByFatIncrese ,
                             rateDecreaseByFatDecrease: data[i].rateDecreaseByFatDecrease  ,
                             rateIncreseBySnfIncrese: data[i].rateIncreseBySnfIncrese ,
                             rateDecreaseBySnfDecrease: data[i].rateDecreaseBySnfDecrease 
                             
                         };


                        listRange.push(range) ;

                }
         

                $("#fatrateCardFatSnf123").html(filed + dataValue + "</table>");
            }

        });

        
      if(fatSnfMaxFatValueRange == fatSnfMaxFatValue){
            $('#generateAllRateCardByFatSnf').show();
      }else{
            $('#generateAllRateCardByFatSnf').hide();
      }
        
    }

    function getAllFatSnfRangeValue() {

        /* get all fat and snf range value */
        fatSnfMinFatValueRange = document.getElementById("fatSnfMinFatValueRange").value;
        fatSnfMaxFatValueRange = document.getElementById("fatSnfMaxFatValueRange").value;
        midRangeOfFatSnfPrice = document.getElementById("midRangeOfFatSnfPrice").value;
        fatSnfRateIncreaseByFat = document.getElementById("fatSnfRateIncreaseByFat").value;
        fatSnfRateIncreaseBySnf = document.getElementById("fatSnfRateIncreaseBySnf").value;
        fatSnfRateDecreaseByFat = document.getElementById("fatSnfRateDecreaseByFat").value;
        fatSnfRateDecreaseBySnf = document.getElementById("fatSnfRateDecreaseBySnf").value;
        DairyIdFat = document.getElementById("DairyIdFat").value;
        midRangeOfFat = document.getElementById("midRangeOfFat").innerHTML;
        midRangeOfSnf = document.getElementById("midRangeOfSnf").innerHTML;

        /* create all value in float type  */
        fatSnfMinFatValueRange = Math.round(parseFloat(fatSnfMinFatValueRange) * 100) / 100;
        fatSnfMaxFatValueRange = Math.round(parseFloat(fatSnfMaxFatValueRange) * 100) / 100;
        midRangeOfFatSnfPrice = Math.round(parseFloat(midRangeOfFatSnfPrice) * 100) / 100;
        fatSnfRateIncreaseByFat = Math.round(parseFloat(fatSnfRateIncreaseByFat) * 100) / 100;
        fatSnfRateIncreaseBySnf = Math.round(parseFloat(fatSnfRateIncreaseBySnf) * 100) / 100;
        fatSnfRateDecreaseByFat = Math.round(parseFloat(fatSnfRateDecreaseByFat) * 100) / 100;
        fatSnfRateDecreaseBySnf = Math.round(parseFloat(fatSnfRateDecreaseBySnf) * 100) / 100;
        midRangeOfFat = Math.round(parseFloat(midRangeOfFat) * 100) / 100;
        midRangeOfSnf = Math.round(parseFloat(midRangeOfSnf) * 100) / 100;

        $.ajax({
            type: 'post',
            url: 'addFatSnfRange',
            data: {
                fatSnfMinFatValueRange: fatSnfMinFatValueRange,
                fatSnfMaxFatValueRange: fatSnfMaxFatValueRange,
                midRangeOfFatSnfPrice: midRangeOfFatSnfPrice,
                fatSnfRateIncreaseByFat: fatSnfRateIncreaseByFat,
                fatSnfRateIncreaseBySnf: fatSnfRateIncreaseBySnf,
                fatSnfRateDecreaseByFat: fatSnfRateDecreaseByFat,
                fatSnfRateDecreaseBySnf: fatSnfRateDecreaseBySnf,
                dairyId: DairyIdFat,
                midRangeOfFat: midRangeOfFat,
                midRangeOfSnf: midRangeOfSnf,
            },
            success: function(data) {
                $("#generateRateCardByFatSnfTable").show();
            }
        });

    }

    /* set rate by fat and snf  */
    /* validation */

    function CheckfatSnfFatValue() {
        var x, text;

        fatSnfMinFatValue = document.getElementById("fatSnfMinFatValue").value;

        if (isNaN(fatSnfMinFatValue)) {
            text = "Numbers Only";
            document.getElementById("fatSnfMinFatValue").focus();
        } else {
            if (fatSnfMinFatValue < 2 || fatSnfMinFatValue > 21) {
                text = "Value Must be in between 2 - 20";
                document.getElementById("fatSnfMinFatValue").focus();
            } else {
                text = "";
            }
        }
        document.getElementById("fatSnfMinFatValueError").innerHTML = text;
        document.getElementById("fatSnfMinFatValueRange").value = fatSnfMinFatValue;
        document.getElementById("fatSnfMinFatValueRange").setAttribute("readonly", "readonly");
    }

    /* check max fat value */
    function checkfatSnfMinFatValue() {
        var x, text;
        var fatSnfMinFatValue = document.getElementById("fatSnfMinFatValue").value;
        var fatSnfMaxFatValue = document.getElementById("fatSnfMaxFatValue").value;

        fatSnfMinFatValue = Math.round(parseFloat(fatSnfMinFatValue) * 100) / 100;
        fatSnfMaxFatValue = Math.round(parseFloat(fatSnfMaxFatValue) * 100) / 100;


        if (fatSnfMaxFatValue != '' && fatSnfMaxFatValue <= 20 && fatSnfMaxFatValue >= 2) {
            if (fatSnfMaxFatValue > fatSnfMinFatValue) {
                if (fatSnfMinSnfValue < 20) {
                    text = "Value must be 20 or less ";
                    document.getElementById("fatSnfMinSnfValue").focus();
                } else {
                    text = "";
                }
            } else {
                text = "Max FAT Value must be greater than Min FAT Value";
                document.getElementById("fatSnfMaxFatValue").focus();
            }
        } else {
            text = "Value Must be in between 2 - 20";
            document.getElementById("fatSnfMaxFatValue").focus();
        }
        document.getElementById("fatSnfMaxFatValueError").innerHTML = text;

    }


    function checkfatSnfMinSnfValue() {
        var x, text;

        fatSnfMinSnfValue = document.getElementById("fatSnfMinSnfValue").value;

        if (isNaN(fatSnfMinSnfValue)) {
            text = "Numbers Only";
            document.getElementById("fatSnfMinSnfValue").focus();
        } else {
            if (fatSnfMinSnfValue < 700) {
                text = "Value must be 700 or greater ";
                document.getElementById("fatSnfMinSnfValue").focus();
            } else {
                text = "";
            }
        }
        document.getElementById("fatSnfMinSnfValueError").innerHTML = text;
    }

    /* check max fat value */
    function checkfatSnfMaxSnfValue() {
        var x, text;

        fatSnfMinSnfValue = document.getElementById("fatSnfMinSnfValue").value;
        fatSnfMaxSnfValue = document.getElementById("fatSnfMaxSnfValue").value;

        fatSnfMinSnfValue = Math.round(parseFloat(fatSnfMinSnfValue) * 100) / 100;
        fatSnfMaxSnfValue = Math.round(parseFloat(fatSnfMaxSnfValue) * 100) / 100;

        check = fatSnfMaxSnfValue < fatSnfMinSnfValue;
        if (isNaN(fatSnfMaxSnfValue) || check == true) {
            text = "Numbers only and must be grater then min. snf value.";
            document.getElementById("fatSnfMaxSnfValue").focus();
        } else {
            checkOher = fatSnfMaxSnfValue == fatSnfMinSnfValue;;
            if (checkOher == true) {
                text = "Numbers must be grater then min. fat value.";
                document.getElementById("fatSnfMaxSnfValue").focus();
            } else {

                if (fatSnfMaxSnfValue > 960) {
                    text = "Value must be 960 or less.";
                    document.getElementById("fatSnfMaxSnfValue").focus();
                } else {
                    text = "";
                }
            }
        }
        document.getElementById("fatSnfMaxSnfValueError").innerHTML = text;
    }


    /* check max fat value range */
    function checkfatSnfMaxFatValueRange() {
        var x, text;

        /* get current range fat and snf mid range value */
        fatSnfMaxFatValueRange = document.getElementById("fatSnfMaxFatValueRange").value;
        fatSnfMinFatValueRange = document.getElementById("fatSnfMinFatValueRange").value;
        fatSnfMaxFatValue = document.getElementById("fatSnfMaxFatValue").value;
        /* convert all range int and string to float */
        fatSnfMaxFatValueRange = Math.round(parseFloat(fatSnfMaxFatValueRange) * 100) / 100;
        fatSnfMinFatValueRange = Math.round(parseFloat(fatSnfMinFatValueRange) * 100) / 100;
        fatSnfMaxFatValue = Math.round(parseFloat(fatSnfMaxFatValue) * 100) / 100;

        /* get current range fat and snf mid range value */
        fatSnfMinSnfValue = document.getElementById("fatSnfMinSnfValue").value;
        fatSnfMaxSnfValue = document.getElementById("fatSnfMaxSnfValue").value;
        /* convert all range int and string to float */
        fatSnfMinSnfValue = Math.round(parseFloat(fatSnfMinSnfValue) * 100) / 100;
        fatSnfMaxSnfValue = Math.round(parseFloat(fatSnfMaxSnfValue) * 100) / 100;

        var checkFromMinFatValue = fatSnfMaxFatValueRange <= fatSnfMinFatValueRange;
        var checkFromMinFatValueMax = fatSnfMaxFatValueRange >= fatSnfMaxFatValue;

        if (fatSnfMaxFatValueRange > fatSnfMinFatValueRange && fatSnfMaxFatValueRange <= fatSnfMaxFatValue) {
            text = "";
            midFatValueRange =Math.round(parseFloat((fatSnfMaxFatValueRange + fatSnfMinFatValueRange) / 2) * 10) / 10;
            document.getElementById("midRangeOfFat").innerHTML = midFatValueRange;
            midSnfValueRange = (fatSnfMinSnfValue + fatSnfMaxSnfValue) / 2;
            midSnfValueRange = midSnfValueRange.toString();
            var lastChar = midSnfValueRange[midSnfValueRange.length -1];
              if(lastChar == 5){
                 var midSnfValueRange = parseInt(midSnfValueRange)+parseInt(5) ;
              }
            document.getElementById("midRangeOfSnf").innerHTML = midSnfValueRange;

        } else {
            if (fatSnfMaxFatValueRange <= fatSnfMinFatValueRange) {
                text = "Value must be greater than Min Fat Value Range";
                document.getElementById("fatSnfMaxFatValueRange").focus();
            } else {
                text = "Value must be less than Max Fat Value";
                document.getElementById("fatSnfMaxFatValueRange").focus();
            }
        }
        document.getElementById("fatSnfMaxFatValueRangeError").innerHTML = text;
    }


    /* get currnet submited value id */
    function getRangeid() {

        var minFatValue = document.getElementById("minFatValue").value;
        var maxFatValue = document.getElementById("maxFatValue").value;
        var rateForMinFatValue = document.getElementById("rateForMinFatValue").value;
        var rateIncreseForFat = document.getElementById("rateIncreseForFat").value;
        var DairyIdFat = document.getElementById("DairyIdFat").value;
        var rateType = document.getElementById("rateType").value;
        var selected = $("#CollectionManager option:selected").val();
        // CollectionManager

        if (minFatValue != "" || maxFatValue != "" || rateForMinFatValue != "" || rateIncreseForFat != "") {

            $.ajax({
                type: 'post',
                url: 'rateCardFetSubmit',
                data: {
                    CollectionManager: selected,
                    minFatValue: minFatValue,
                    maxFatValue: maxFatValue,
                    rateForMinFatValue: rateForMinFatValue,
                    rateIncreseForFat: rateIncreseForFat,
                    rateType: rateType,
                    dairyId: DairyIdFat
                },
                success: function(data) {

                    var filed = " <td> Fat </td> <td> Rate </td><td > Action </td>";

                    $("#fatrateCard").html(filed);
                    var i;
                    var dataValue = "";
                    for (i = 0; i < data.rangeArray.length; i++) {
                        dataValue = dataValue + "<tr>";
                        dataValue = dataValue + "<td >" + data.rangeArray[i] + "</td>";
                        dataValue = dataValue + "<td class='current_rate_" + i + "' id='current_rate_" + i + "' >" + data.rateArray[i] + "</td>";
                        dataValue = dataValue + "<td ><a data-toggle='modal' data-target='#myModal' onclick='updateRate(" + i + "," + data.rangeArray[i] + "," + data.rateArray[i] + ")' id='editRate_" + i + "'> Edit</a></td>";
                        dataValue = dataValue + "</tr>";
                        mainArray = dataValue;
                    }
                    alert("Successfully Created");

                    $("#minFatValue").val("");
                    $("#maxFatValue").val("");
                    $("#rateForMinFatValue").val("");
                    $("#rateIncreseForFat").val("");
                    $("#selected").val("");
                    $("#fatrateCardValue").html(dataValue);

                }
            });

        } else {
            // alert("Please insert data in all fields");
        }

    }

    /* edit fat rate card table value  */
    function updateRate(position, rang, rate) {

        var currentRate = $(".current_rate_" + position).text();

        $("#EditRateByFatPosition").val(position);
        $("#EditRangeByFat").val(rang);
        $("#EditRateByFat").val(currentRate);
    }

    /* fat rate edit submit */
    function fatRateEditSubmit() {

        var EditRateByFatPosition = $("#EditRateByFatPosition").val();
        var EditRangeByFat = $("#EditRangeByFat").val();
        var EditRateByFat = $("#EditRateByFat").val();
        var DairyIdFat = $("#DairyIdFat").val();
        var currnetRang = "current_rate_" + EditRateByFatPosition;

        $.ajax({
            type: 'post',
            url: 'submitFatPriceEdit',
            data: {
                EditRateByFatPosition: EditRateByFatPosition,
                EditRangeByFat: EditRangeByFat,
                EditRateByFat: EditRateByFat,
                DairyIdFat: DairyIdFat
            },
            success: function(data) {
                if (data == "true") {
                    $(".current_rate_" + EditRateByFatPosition).text(EditRateByFat);
                }

            }
        });

    }
    /* validation */
    /* check min fat value */
    function checkMinFatValue() {
        var x, text;

        x = document.getElementById("minFatValue").value;

        if (isNaN(x)) {
            text = "Numbers Only";
            document.getElementById("minFatValue").focus();
        } else {
            text = "";
        }
        document.getElementById("minFatValueError").innerHTML = text;
    }

    /* check max fat value */
    function checkMaxFatValue() {
        var x, text;

        x = document.getElementById("maxFatValue").value;
        y = document.getElementById("minFatValue").value;
        if (isNaN(x) || x < y) {
            text = "Numbers only and must be grater then min. fat value.";
            document.getElementById("maxFatValue").focus();
        } else {
            text = "";
        }
        document.getElementById("maxFatValueError").innerHTML = text;
    }

    /* check Rate for min fat value */
    function checkRateForMinFatValue() {

        var x, text;

        x = document.getElementById("rateForMinFatValue").value;

        if (isNaN(x)) {
            text = "Numbers Only";
            document.getElementById("rateForMinFatValue").focus();
        } else {
            text = "";
        }
        document.getElementById("rateForMinFatValueError").innerHTML = text;

    }

    /* check rate oncreses for fat */
    function checkRateIncreseForFat() {
        var x, text;

        x = document.getElementById("rateIncreseForFat").value;

        if (isNaN(x)) {
            text = "Numbers Only";
            document.getElementById("rateIncreseForFat").focus();
            $("#generareRateCard").hide();
        } else {
            text = "";
            $("#generareRateCard").show();

        }
        document.getElementById("rateIncreseForFatError").innerHTML = text;

        // alert(x);
        //rateIncreseForFatError
    }

    function openCity(evt, cityName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("city");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " w3-red";
    }

    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 1000);

</script>