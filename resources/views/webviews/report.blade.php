@extends('theme.webview') 

@section('content') 



<style type="text/css">

    .table-data-area{

        position: relative;

    }

    #printBTN{       

        margin-left: 178px;

        top: 16px;

        position: absolute;

        z-index: 109;

    }

    #pdfBTN{       

        margin-left: 235px;

        top: 16px;

        position: absolute;

        z-index: 109;

    }



    .txtr {

        text-align: right;

    }



    .getButton {

        background-color: #329df9;

        color: #fff;

        border: 0;

        transition: 0.5s;

    }



    .getButton:hover {

        box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.4);

    }



    /* th {

        width: 93px;

        border: solid;

        text-align: center;

    }



    td {

        width: 93px;

        border: solid;

        text-align: center;

    } */



    form {

        margin-top: 20px;

        float: right;

        background: #f9f9f9;

        text-align: right;

    }







    .table {

        margin-top: 80px;

    }



    .getPdfButton {

        background-color: #3199f3;

        color: #fff;

        width: 98px;

        height: 29px;

        border-radius: 15px;

        float: left;

        border: 2px solid #0089ff;

        box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.4);

        margin: 10px

    }



    .printPdfReportP {

        background-color: #3199f3;

        color: #fff;

        width: 98px;

        height: 29px;

        border-radius: 15px;

        float: left;

        border: 2px solid #0089ff;

        box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.4);

        margin: 10px

    }



    input[type=number]::-webkit-inner-spin-button,

    input[type=number]::-webkit-outer-spin-button {

        -webkit-appearance: none;

        -moz-appearance: none;

        appearance: none;

        margin: 0;

    }

    
    #pdfBTNCmSubs{
        margin-left: 235px;
        top: 16px;
        position: absolute;
        z-index: 109;
        margin: 20px 0 0 50px;
    }

@media print {

    html, body {

        height: 99%;    

    }

}

</style>



<div class="">

    <div class="fcard margin-fcard-1 clearfix">



        <div class="">



            <div class="heading clearfix">

                <div class="fl">

                    <h3 class="m-0">Generate Report</h4>

                        <hr class="m-0">

                </div>

            </div>



            <div class="col-md-12">



                <div class="col-md-4 pt-20">

                    <input type="hidden" id="dairyId" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">

                    <input type="hidden" id="status" name="status" value="true">

                    <!-- select list for select report type -->

                    <label> Select Report Type </label>

                    <select id="reportSelect" onchange="getReportForm(this.value);" class="form-control selectpicker" data-live-search="true"

                        title="Select Report">

                        <option value="sales" @if($repType=="sales") selected @endif>Sales Report</option>

                        <option value="memberList" @if($repType=="memberList") selected @endif>Member List</option>

                        <option value="rateChart" @if($repType=="rateChart") selected @endif>Rate Chart</option>

                        <option value="shiftSummary" @if($repType=="shiftSummary") selected @endif>Shift Summary</option>

                        <option value="ledger" @if($repType=="ledger") selected @endif>Ledger</option>

                        <option value="memberPassbook" @if($repType=="memberPassbook") selected @endif>Member Passbook</option>

                        <option value="memStatement" @if($repType=="memStatement") selected @endif>Member Account Statement</option>

                        <option value="custSalseReport" @if($repType=="custSalseReport") selected @endif>Customer Account Statement</option>

                        {{-- <option value="balanceSheet" @if($repType=="balanceSheet") selected @endif>Balance Sheet</option> --}}

                        <option value="payemntRegister" @if($repType=="payemntRegister") selected @endif>Payment Register</option>

                        <option value="cmSubsidiary" @if($repType=="cmSubsidiary") selected @endif>CM Subsidiary</option>

                        <option value="profitLoss" @if($repType=="profitLoss") selected @endif>Profit Loss Report</option>

                    </select>

                </div>





                <div class="col-md-8 bl-1 p-0">



                    <div class="pt-20"></div>



                    <!-- Select list form for report -->

                    <div id="sales" class="sales reportElm clearfix" style="display:none;">

                        <div class="clearfix mb-10">



                            <div class="col-sm-6">

                                <label> Start Date </label>

                                <input type="text" name="saleStartDate" id="saleStartDate" class="saleStartDate form-control" value="{{date(" d-m-Y ")}}">

                            </div>

                            <div class="col-sm-6">

                                <label> End Date </label>

                                <input type="text" name="saleEndDate" id="saleEndDate" class="saleEndDate form-control" value="{{date(" d-m-Y ")}}">

                            </div>



                            <div class="col-sm-12 pt-20">

                                <label for="saleType">Select Sale Type</label>

                            </div>

                            <div class="col-sm-12 pt-10 pb-10">

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="saleType" onclick="" value="" checked>All</label>

                                </div>

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="saleType" onclick="" value="local_sale" >Local Sale</label>

                                </div>

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="saleType" onclick="" value="product_sale">Product Sale</label>

                                </div>

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="saleType" onclick="" value="plant_sale">Plant Sale</label>

                                </div>

                            </div>



                            <div class="col-sm-12 pt-20">

                                <label for="userType"> Select User</label>

                            </div>

                            <div class="col-sm-12 pt-10 pb-10">

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="userType" onclick="" value="" checked>All</label>

                                </div>

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="userType" onclick="" value="customer" >Customer</label>

                                </div>

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="userType" onclick="" value="member">Members</label>

                                </div>

                                <div class="col-sm-3 p-0">

                                    <label class="rdolb lh-25"><input type="radio" name="userType" onclick="" value="plant">Plant</label>

                                </div>

                            </div>



                            <div class="col-sm-6 pt-10">

                                <label> Amount Type </label>

                                <select id="saleAmountType" class="form-control selectpicker"> 

                                <option value="">Both (Cash/Credit)</option>                       

                                <option value="cash">Cash</option>

                                <option value="credit">Credit</option>

                            </select>

                            </div>



                            <div class="col-sm-6 pt-10">

                                <label> &nbsp; </label>

                                <input type="button" name="getSaleData" value="Get" onclick="getSaleCurrentValue();" id="getSaleData" class="getSaleData form-control getButton">

                            </div>



                        </div>



                        <hr class="m-0">



                    </div>







                    <!-- Select Membe form for report -->

                    <div id="memberList" class="col-sm-12 reportElm p-0 clearfix" style="display: none;">

                        <div class="col-md-12 mb-20 p-0 clearfix">



                            <div class="col-sm-3">

                                <label>Member Code</label>

                                <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessageL"></span>

                                <input id="memberCodeL" class="form-control" name="partyCode" required data-name="code">

                            </div>

        

                            <div class="col-sm-3">

                                <label>Member Name</label>

                                <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessageL"></span>

                                <input class="form-control" id="memberNameStL" name="partyName" required="true" data-name="name">

                            </div>

    



                            <div class="col-sm-3">

                                <label> &nbsp; </label>

                                <input type="button" name="getMemberData" value="Get" onclick="getMemberFilterCurrentValue();" id="getMemberData" class="getMemberData form-control getButton">

                            </div>

                        </div>



                    </div>

                    <!-- Select shift form for report -->

                    <div id="shiftSummary" class="reportElm" style="display: none;">

                        <div class="mb-20 clearfix">

                            <div class="col-sm-4 col-md-4 col-lg-3">

                                <label> Date </label>

                                <input type="text" name="shiftDate" id="shiftDate" class="shiftDate form-control" value="{{date(" d-m-Y ")}}">

                            </div>

                            <div class="col-sm-4 col-md-4 col-lg-3">

                                <label> Shift </label>

                                <select name="shiftType" id="shiftType" class="shiftType form-control selectpicker">

                            {{-- <option value="">All</option> --}}

                            <option value="morning"> Morning Shift </option>

                            <option value="evening"> Evening Shift </option>

                        </select>

                            </div>

                            <div class="col-sm-4 col-md-4 col-lg-3">

                                <label> &nbsp; </label>

                                <input type="button" name="getSaleData" value="Get" onclick="getShiftFilterCurrentValue('Asfasf');" id="getSaleData" class="getSaleData form-control getButton">

                            </div>

                        </div>



                    </div>



                    <!-- Select ledger form for report -->

                    <div id="ledger" class="reportElm" style="display: none;">

                        {{--

                        <div class="col-sm-3">

                            <label> Ledger For </label>

                            <select id="leaderFor" name="leaderFor" class="leaderFor selectpicker">

                            <option value="">All</option>

                            <option value="4">Member</option>

                            <option value="2">Customer</option>

                            <option value="6">Milk Plant</option>

                            <option value="3">Supplier</option>

                        </select>

                        </div> --}}



                        <div class="col-sm-12">

                            <label for="">Select Ladger</label>

                        </div>



                        <div class="col-sm-12">

                            <label class="rdolb lh-25"><input type="radio" name="leaderFor" onclick="" value="" checked>All</label>

                        </div>



                        <div class="col-sm-3">

                            <label class="rdolb lh-25"><input type="radio" name="leaderFor" onclick="" value="4" >Members</label>

                        </div>



                        <div class="col-sm-3">

                            <label class="rdolb lh-25"><input type="radio" name="leaderFor" onclick="" value="2" >Customers</label>

                        </div>



                        <div class="col-sm-3">

                            <label class="rdolb lh-25"><input type="radio" name="leaderFor" onclick="" value="6" >Milk Plant</label>

                        </div>



                        <div class="col-sm-3">

                            <label class="rdolb lh-25"><input type="radio" name="leaderFor" onclick="" value="3" >Suppliers</label>

                        </div>



                        <div class="col-sm-3">

                            <label> &nbsp; </label>

                            <input type="button" name="getLaderData" value="Get" onclick="getLedgerFilterCurrentValue();" id="getLaderData" class="getLaderData form-control getButton">

                        </div>



                    </div>



                    <!-- Select member passbook form for report -->

                    <div id="memberPassbook" class="reportElm" style="display: none;">

                        

                        <div class="col-sm-3">

                            <label>Member Code</label>

                            <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessageP"></span>

                            <input id="memberCodeP" class="form-control" name="partyCode" required data-name="code">

                        </div>

    

                        <div class="col-sm-3">

                            <label>Member Name</label>

                            <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessageP"></span>

                            <input class="form-control" id="memberNameStP" name="partyName" required="true" data-name="name">

                        </div>



                        <div class="col-sm-3">

                            <label> Start Date </label>

                            <input type="text" name="memberPassbookStartDate" id="memberPassbookStartDate" value="{{date(" d-m-Y ")}}" class="memberPassbookStartDate form-control">

                        </div>

                        <div class="col-sm-3">

                            <label> End Date </label>

                            <input type="text" name="memberPassbookEndDate" id="memberPassbookEndDate" value="{{date(" d-m-Y ")}}" class="memberPassbookEndDate form-control">

                        </div>

                        <div class="col-sm-3">

                            <label> &nbsp; </label>

                            <input type="button" name="getSaleData" value="Get" onclick="getMemberFilterCurrentPassbookValue('Asfasf');" id="getSaleData"

                                class="getSaleData form-control getButton">

                        </div>



                    </div>





                    <!-- Select Payment Register form for report -->

                    <div id="payemntRegister" class="reportElm" style="display: none;">

                        {{-- <div class="col-sm-3">

                            <label> Amount Type </label>

                            <select id="balanceSheetAmountType" class="selectpicker"> 

                                <option value="">All</option>

                                <option value="cash">Cash</option>

                                <option value="credit">Credit</option>

                            </select>

                        </div> --}}



                        <div class="col-sm-3">

                            <label> Start Date </label>

                            <input type="text" name="balanceSheetStartDate" id="balanceSheetStartDate" value="{{date(" d-m-Y ")}}" class="balanceSheetStartDate form-control">

                        </div>

                        <div class="col-sm-3">

                            <label> End Date </label>

                            <input type="text" name="balanceSheetEndDate" id="balanceSheetEndDate" value="{{date(" d-m-Y ")}}" class="balanceSheetEndDate form-control">

                        </div>

                        <div class="col-sm-3">

                            <label> &nbsp; </label>

                            <input type="button" name="getSaleData" value="Get" onclick="getBalanceSheetValueForTable();" id="getSaleData" class="getSaleData form-control getButton">

                        </div>



                        <div id="balanceSheetTable"> </div>

                    </div>





                    <!-- Select CmSubsidiary form for report -->

                    <div id="cmSubsidiary" class="reportElm" style="display: none;">

                        <div class="col-sm-6">

                            <label> Field Sup Name </label>

                            <input type="text" name="cmSubsidiaryFieldSupName" id="cmSubsidiaryFieldSupName" class="cmSubsidiaryFieldSupName form-control">

                        </div>

                        <div class="col-sm-6">

                            <label> Field Sup Contact No </label>

                            <input type="number" name="cmSubsidiaryFieldSupContactNo" id="cmSubsidiaryFieldSupContactNo" class="cmSubsidiaryFieldSupContactNo form-control">

                        </div>

                        <div class="col-sm-3 pt-5">

                            <label> Amount (3.5%-5.0%) </label>

                            <input type="number" name="cmSubsidiaryAmountLow" id="cmSubsidiaryAmountLow" class="cmSubsidiaryAmountLow form-control">

                        </div>

                        <div class="col-sm-3 pt-5">

                            <label> Amount (5.0% and more) </label>

                            <input type="number" name="cmSubsidiaryAmountHigh" id="cmSubsidiaryAmountHigh" class="cmSubsidiaryAmountHigh form-control">

                        </div>



                        <div class="col-sm-3 pt-5">

                            <label> Start Date </label>

                            <input type="text" name="cmSubsidiaryStartDate" id="cmSubsidiaryStartDate" class="cmSubsidiaryStartDate form-control" value="{{date('d-m-Y')}}">

                        </div>

                        <div class="col-sm-3 pt-5">

                            <label> End Date </label>

                            <input type="text" name="cmSubsidiaryEndDate" id="cmSubsidiaryEndDate" class="cmSubsidiaryEndDate form-control" value="{{date('d-m-Y')}}">

                        </div>

                        <div class="col-sm-3 pt-5">

                            <label> &nbsp; </label>

                            <input type="button" name="getSaleData" value="Get" onclick="getCmSubsidiaryValueForTable();" id="getSaleData" class="getSaleData form-control getButton">

                        </div>



                        <div id="cmSubsidiaryTable"></div>

                    </div>





                    <div id="rateChart" class="reportElm" style="display: none;">

                        <div class="col-sm-3">

                            <label> Collection Manager </label>

                            <select name="collectionManager" id="collectionManager" class="collectionManager form-control selectpicker">

                            <option value="">Dairy Admin</option>

                        </select>

                        </div>

                        <div class="col-sm-3">

                            <label> Select Rate Card For </label>

                            <select name="rateCardFor" id="rateCardFor" class="rateCardFor form-control selectpicker">

                            <option value="cow">Cow</option>

                            <option value="buffalo">Buffalo</option>

                        </select>

                        </div>



                        <div class="col-sm-3">

                            <label> &nbsp; </label>

                            <input type="button" name="getRateCardBtn" value="Get" onclick="getRateCardReport(this);" id="getRateCardBtn" class="getRateCardBtn form-control getButton">

                        </div>

                    </div>



                    <div class="reportElm clearfix dnone" id="memStatement">

                            

                            <div class="col-sm-3">

                                <label>Member Code</label>

                                <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessage"></span>

                                <input id="memberCode" class="form-control" name="partyCode" required data-name="code">

                            </div>

        

                            <div class="col-sm-3">

                                <label>Member Name</label>

                                <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage"></span>

                                <input class="form-control" id="memberNameSt" name="partyName" required="true" data-name="name">

                            </div>

    

                            <div class="col-sm-3 col-md-3">

                                <label>From</label>

                                <input type="text" class="form-control" id="sdate" placeholder="From" value="{{date("d-m-Y")}}" name="fromdate"

                                        autocomplete="off">

                            </div>

    

                            <div class="col-sm-3 col-md-3">

                                <label>To</label>

                                <input type="text" class="form-control" id="tdate" placeholder="To" value="{{date("d-m-Y")}}" name="todate"

                                        autocomplete="off">

                            </div>



                            <div class="col-sm-3 col-md-3 checkbox pt-20 dnone">

                                <label>

                                <input type="checkbox" class="" id="groupByDate" value='1' name="groupByDate">

                                Group By Date</label>

                            </div>



                            <div class="col-sm-3">

                                <label> &nbsp; </label>

                                <input type="button" name="getStatementBtn" value="Get" onclick="getMemStatementReport(this);" id="getStatementBtn" class="getStatementBtn form-control getButton">    

                            </div>



                    </div>



                    <div class="reportElm clearfix dnone" id="custSalseReport">

                        <div class="col-sm-12 col-md-12 p-0">

                            

                            <div class="col-sm-3">

                                <label>Customer Code</label>

                                <input id="customerCode" class="form-control" required data-name="code">

                            </div>

        

                            <div class="col-sm-3">

                                <label>Customer Name</label>

                                <input class="form-control" id="customerName" required="true" data-name="name">

                            </div>

    

                            <div class="col-sm-3 col-md-3">

                                <label>From</label>

                                <input type="text" class="form-control" id="sdateCust" placeholder="From" value="{{date("d-m-Y")}}" name="fromdate"

                                        autocomplete="off">

                            </div>

    

                            <div class="col-sm-3 col-md-3">

                                <label>To</label>

                                <input type="text" class="form-control" id="tdateCust" placeholder="To" value="{{date("d-m-Y")}}" name="todate"

                                        autocomplete="off">

                            </div>



                            <div class="col-sm-3 col-md-3 checkbox pt-20 dnone">

                                <label>

                                <input type="checkbox" class="" id="groupByDateCust" value='1'>

                                Group By Date</label>

                            </div>



                            <div class="col-sm-3">

                                <label> &nbsp; </label>

                                <input type="button" value="Get" onclick="getCustSalseReport(this);" id="getCustSalseBtn" class="getCustSalseBtn form-control getButton">    

                            </div>



                        </div>

                    </div>



                    <div class="reportElm clearfix dnone" id="profitLoss">

                        <div class="col-sm-12 col-md-12 p-0">

                            

                            <div class="col-sm-3 col-md-3">

                                <label>From</label>

                                <input type="text" class="form-control" id="sdateProfit" placeholder="From" value="{{date("d-m-Y")}}" name="fromdate"

                                        autocomplete="off">

                            </div>

    

                            <div class="col-sm-3 col-md-3">

                                <label>To</label>

                                <input type="text" class="form-control" id="tdateProfit" placeholder="To" value="{{date("d-m-Y")}}" name="todate"

                                        autocomplete="off">

                            </div>



                            <div class="col-sm-3">

                                <label> &nbsp; </label>

                                <input type="button" value="Get" onclick="getProfitLossReport(this);" id="getProfitLossBtn" class="getProfitLossBtn form-control getButton">

                            </div>



                        </div>

                    </div>



                </div>



            </div>



        </div>

    </div>



    <div class="table-data-area">

        <button id="pdfBTNCmSubs" class="dt-button dnone">PDF</button>

        <div id="table-data" class="mt-10 clearfix"></div>    

    </div>





    <script>

        $(document).ready(function(){

            g = [];

            getReportForm($("#reportSelect").val());

            

        });







var dairyId = document.getElementById("dairyId").value;



                $( function() {

                    var members = [

                        @foreach ($returnData[0] as $memberInfoData)

                            {

                                value: "{{ $memberInfoData->memberPersonalCode }}",

                                label: "{{ $memberInfoData->memberPersonalCode }}",

                                desc: "{{ $memberInfoData->memberPersonalName }}",

                            },

                        @endforeach

                    ];



                    var membersName = [

                        @foreach ($returnData[0] as $memberInfoData)

                            {

                                value: "{{ $memberInfoData->memberPersonalName }}",

                                label: "{{ $memberInfoData->memberPersonalName }}",

                                desc: "{{ $memberInfoData->memberPersonalCode }}",

                            },

                        @endforeach

                    ];





                    $( "#memberNameSt" ).autocomplete({

                        minLength: 0,

                        source: membersName,

                        focus: function( event, ui ) {

                            $( "#memberNameSt" ).val( ui.item.label );

                            return false;

                        },

                        select: function( event, ui ) {

                            $( "#memberCode" ).val( ui.item.desc );

                            $( "#memberNameSt" ).val( ui.item.value );

                            // SetMemberCode();

                            return false;

                        }

                    }).autocomplete( "instance" )._renderItem = function( ul, item ) {

                        return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

                    };



                    $( "#memberNameStL" ).autocomplete({

                        minLength: 0,

                        source: membersName,

                        focus: function( event, ui ) {

                            $( "#memberNameStL" ).val( ui.item.label );

                            return false;

                        },

                        select: function( event, ui ) {

                            $( "#memberCodeL" ).val( ui.item.desc );

                            $( "#memberNameStL" ).val( ui.item.value );

                            // SetMemberCode();

                            return false;

                        }

                    }).autocomplete( "instance" )._renderItem = function( ul, item ) {

                        return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

                    };



                    

                    $( "#memberNameStP" ).autocomplete({

                        minLength: 0,

                        source: membersName,

                        focus: function( event, ui ) {

                            $( "#memberNameStP" ).val( ui.item.label );

                            return false;

                        },

                        select: function( event, ui ) {

                            $( "#memberCodeP" ).val( ui.item.desc );

                            $( "#memberNameStP" ).val( ui.item.value );

                            // SetMemberCode();

                            return false;

                        }

                    }).autocomplete( "instance" )._renderItem = function( ul, item ) {

                        return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

                    };





                    var cust = [

                        @foreach ($returnData[2] as $c)

                            {

                                value: "{{ $c->customerCode }}",

                                label: "{{ $c->customerCode }}",

                                desc: "{{ $c->customerName }}",

                            },

                        @endforeach

                    ];



                    var custName = [

                        @foreach ($returnData[2] as $c)

                            {

                                value: "{{ $c->customerName }}",

                                label: "{{ $c->customerName }}",

                                desc: "{{ $c->customerCode }}",

                            },

                        @endforeach

                    ];





                    $("#customerName").autocomplete({

                        minLength: 0,

                        source: custName,

                        focus: function( event, ui ) {

                            $( "#customerName" ).val( ui.item.label );

                            return false;

                        },

                        select: function( event, ui ) {

                            $( "#customerCode" ).val( ui.item.desc );

                            $( "#customerName" ).val( ui.item.value );

                            // SetMemberCode();

                            return false;

                        }

                    }).autocomplete( "instance" )._renderItem = function( ul, item ) {

                        return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

                    };





                });



    $("#memberCode, #memberNameSt").on("change, focusout", function(){

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "member", "");

    })

    $("#memberCodeL, #memberNameStL").on("change, focusout", function(){

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "member", "L");

    })

    $("#memberCodeP, #memberNameStP").on("change, focusout", function(){

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "member", "P");

    })



    $("#customerCode, #customerName").on("change, focusout", function(){

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "customer", "");

    })



	function getUserDetail(q, elm, qtype, user, no){

        if(q){

            loader("show");



            $.ajax({

                type:"POST",

                url:'{{url('api/getUserDetail')}}' ,

                data: {

                    device_token: "{{$device_token}}",

                    q: q,

                    qtype: qtype,

                    dairyId: dairyId,

                    user: user

                },

                success:function(res){

                    if(res.error){

                        $("#response-alert").html(res.msg).show();

                        $(elm).addClass("has-error");

                    }else{

                        $("#response-alert").hide();

                        setUserData(res.data, elm, user, no);

                    }

                    loader("hide");

                    console.log(res);

                },

                error:function(res){

                    console.log(res);

                }

            });

        }

    }



    function setUserData(data, elm, user, no){

        console.log();

		if(user=="member"){

			$("#memberCode"+no).val(data.code);

			$("#memberNameSt"+no).val(data.name);

            $("#memberCode"+no+", #memberNameSt"+no).removeClass("has-error");

		}

        

        if(user=="customer"){

			$("#customerCode").val(data.code);

			$("#customerName").val(data.name);

            $("#customerCode, #customerName").removeClass("has-error");

		}

	

	}





    function getReportForm(reportKey){

        $("#table-data").html("").removeClass("table-back");

        $(".reportElm").hide();

        $("#"+reportKey).show();

        window.history.pushState("", reportKey, "{{url('api/reports?type=')}}"+reportKey+"&device_token={{$device_token}}");

        $("#dropdown-report").addClass("in");

        $("#dropdown-report li").removeClass("active");        

        $("#dropdown-report ."+reportKey).addClass('active');

        $("#pdfBTNCmSubs").hide();

    }



    /* get sale report start */

    $(function () {

            $('#saleStartDate, #saleEndDate, #shiftDate, #memberPassbookStartDate, #memberPassbookEndDate, #balanceSheetStartDate, #balanceSheetEndDate, #cmSubsidiaryEndDate, #cmSubsidiaryStartDate, #tdate, #sdate, #sdateCust, #tdateCust, #sdateProfit, #tdateProfit').datetimepicker({

            format: 'DD-MM-YYYY'

        });

    });





    function setUser(reportType, party){



    }



    function pdfFunction(doc) {



        //Remove the title created by datatTables

        doc.content.splice(0,1);

        //Create a date string that we use in the footer. Format is dd-mm-yyyy

        var now = new Date();

        var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();

        // Logo converted to base64

        // var logo = getBase64FromImageUrl('https://datatables.net/media/images/logo.png');

        // The above call should work, but not when called from codepen.io

        // So we use a online converter and paste the string in.

        // Done on http://codebeautify.org/image-to-base64-converter

        // It's a LONG string scroll down to see the rest of the code !!!

        // var logo = 'data:image/jpeg;base64,/9j';

        // A documentation reference can be found at

        // https://github.com/bpampuch/pdfmake#getting-started

        // Set page margins [left,top,right,bottom] or [horizontal,vertical]

        // or one number for equal spread

        // It's important to create enough space at the top for a header !!!

        doc.pageMargins = [20,80,20,40];

        // Set the font size fot the entire document

        doc.defaultStyle.fontSize = 9;

        // Set the fontsize for the table header

        doc.styles.tableHeader.fontSize = 10;

        doc.styles.tableHeader.fillColor = doc.styles.tableFooter.fillColor = '#eaeaea';

        doc.styles.tableHeader.color = doc.styles.tableFooter.color = '#222';

        doc.styles.table = '100%';

        // Create a header object with 3 columns

        // Left side: Logo

        // Middle: brandname

        // Right side: A document title

        doc['header']=(function() {

            return {

                columns: [

                    {

                        alignment: 'center',

                        // italics: true,

                        text: g['headings'].dairyName+"\n"+"Society code: "+g['headings'].society_code +"\n"+g['headings'].report + g['headings'].text,

                        fontSize: 10,

                        margin: [10,0]

                    },

                ],

                margin: 20,

            }

        });

        // Create a footer object with 2 columns

        // Left side: report creation date

        // Right side: current page and total pages

        doc['footer']=(function(page, pages) {

            return {

                columns: [

                    {

                        alignment: 'left',

                        text: ['Created on: ', { text: jsDate.toString() }]

                    },

                    {

                        alignment: 'right',

                        text: ['page ', { text: page.toString() },	' of ',	{ text: pages.toString() }]

                    },

                ],

                fontSize: 7,

                margin: 20

            }

        });

        // Change dataTable layout (Table styling)

        // To use predefined layouts uncomment the line below and comment the custom lines below

        // doc.content[0].layout = 'lightHorizontalLines'; // noBorders , headerLineOnly

        var objLayout = {};

        objLayout['hLineWidth'] = function(i) { return .5; };

        objLayout['vLineWidth'] = function(i) { return .5; };

        objLayout['hLineColor'] = function(i) { return '#aaa'; };

        objLayout['vLineColor'] = function(i) { return '#aaa'; };

        objLayout['paddingLeft'] = function(i) { return 4; };

        objLayout['paddingRight'] = function(i) { return 4; };

        doc.content[0].layout = objLayout;

    }



    function getSaleCurrentValue(){



        loader("show");



        data = {

                device_token: "{{$device_token}}",

                SaleReportType : $("input[name='saleType']:checked").val(),

                saleAmountType : $("#saleAmountType").val(),

                saleStartDate : $("#saleStartDate").val(),

                saleEndDate : $("#saleEndDate").val(),

                userType : $("input[name='userType']:checked").val(),

                partyName : $("#partyName").val(),

                dairyId : $("#dairyId").val(),

                status : $("#status").val(),

            }



        $.ajax({

            type:"POST",

            url:'{{url('api/getSaleReport')}}',

            data: data,

            success:function(res){

                console.log(res.headings.from);

                $("#table-data").html(res.content).addClass("table-back");

                g['headings'] = res.headings;

                table = $('.sales-table').DataTable( {

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{

                            text: 'PDF',
                            action: function(e, dt, node, config){
                                    window.location = '{{url('')}}/'+res.filename;
                                }
                            },
                            {
                                text: 'Excel',
                                action: function(e, dt, node, config){
                                    window.location = '{{url('')}}/'+res.filename_excel;
                                }
                            }]
                        });



                loader("hide");                    

            },

            error:function(res){

                loader("hide");

                $.alert("Something went Wrong, please try again.");

                console.log();

            }

        });

    }



    function printSaleReport() {

        var tableData = document.getElementById("printPdfDara").value ;

        newWin= window.open("");

        newWin.document.write(tableData);

        newWin.print();

        newWin.close();

    }

    /* get sale report end */

    /* get member report start */



    $(function () {

            $('#memberStartDate').datetimepicker({

            format: 'YYYY-MM-DD'

        });

    });



    $(function () {

        $('#memberEndDate').datetimepicker({

            format: 'YYYY-MM-DD'

        });

    });



    function getMemberFilterCurrentValue(){

        loader("show");



        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        

        var memberCode = $("#memberCodeL").val();

        

        $.ajax({

            type:"post",

            url:'{{url('api/getMemberReport')}}',

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                memberCode: memberCode,

            },

            success:function(res){

                g['headings'] = res.headings;

                $("#table-data").html(res.content).addClass("table-back");

                table = $('.memberList-tabel').DataTable( {

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]

                });



                loader("hide");                



            },error: function(res){

                console.log(res);

                loader("hide");

            }

        });

    } 





    function getRateCardReport(){

        loader("show");



        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        

        var colMan = $("#collectionManager").val();

        var rateCardFor = $("#rateCardFor").val();

        $.ajax({

            type:"post",  

            url:'{{url('api/getRateCardReport')}}',

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                colMan: colMan,

                rateCardFor: rateCardFor,

            },

            success:function(res){



                if(res.error){

                    $.alert(res.msg);

                    return false;

                }



                g['headings'] = res.headings;

                $("#table-data").html(res.view)/*.addClass("table-back")*/;

                table1 = $('.table-cardShort').DataTable({                          

                    bPaginate : false,

                    bFilter : false,

                    info: false,

                    "sScrollX": "100%", "sScrollXInner": "100%",

                });

                table2 = $('.table-cardRange').DataTable({                          

                    bPaginate : false,

                    bFilter : false,

                    info: false,

                    "sScrollX": "100%", "sScrollXInner": "100%",

                });

                table3 = $('.rateChartTable').DataTable({

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]
                    });

                

                loader("hide");                



            },error: function(res){

                console.log(res);

                loader("hide");

            }

        });

    } 





function printMemberReport() {

    var tableData = document.getElementById("MemberprintPdfData").value;

    newWin= window.open("");

    newWin.document.write(tableData);

    newWin.print();

    newWin.close();

  }

/* get member report end */





// shiftDate

// shiftType



    function getShiftFilterCurrentValue(){

        loader("show");



        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        var shiftDate = $("#shiftDate").val();

        var shiftType = $("#shiftType").val();



        $.ajax({

            type:"post",  

            url:'{{url('api/getShiftReport')}}',

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                shiftDate: shiftDate,

                shiftType: shiftType,

            },

            success:function(res){  

                $("#table-data").html(res.content).addClass("table-back");

                g['headings'] = res.headings;

                console.log(g);



                table = $('.shift-summary-table').DataTable( {

                    // "scrollX": true,

                    "sScrollX": "100%",

                    "sScrollXInner": "110%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]
                    });





                loader("hide");

                

            },

            error:function(res){

                console.log(res);

                loader("hide");

            }

        });

    }

        

    function printShiftReport() {

        var tableData = document.getElementById("shiftprintPdfData").value ;

        newWin= window.open("");

        newWin.document.write(tableData);

        newWin.print();

        newWin.close();

        

    }

/* get shift report end */



  

    function getMemberFilterCurrentPassbookValue(){



        loader("show");

        

        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        var memberCode = $("#memberCodeP").val();

        var memberPassbookStartDate = $("#memberPassbookStartDate").val();

        var memberPassbookEndDate = $("#memberPassbookEndDate").val();



        $.ajax({

            type:"post",  

            url:'{{url('api/getMemberPassbookReport')}}' ,

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                memberCode: memberCode,

                memberPassbookStartDate: memberPassbookStartDate,

                memberPassbookEndDate: memberPassbookEndDate,

            },

            success:function(res){  

                $("#table-data").html(res.content).addClass('table-back');

                g["headings"] = res.headings;



                table = $('.passbookReport-table').DataTable( {

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]

                    });

                loader("hide");

            },

            error:function(res){

                console.log(res);

                loader("hide");

            }

        });

    

    }



    function printMemberPassbookReport() {

        var tableData = document.getElementById("memberPassbookprintPdfData").value;

        newWin= window.open("");

        newWin.document.write(tableData);

        newWin.print();

        newWin.close();

        

    }

    /* get member passbook report end */

  



    function getBalanceSheetValueForTable(){

        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        var balanceSheetAmountType = $("#balanceSheetAmountType").val();

        var balanceSheetStartDate = $("#balanceSheetStartDate").val();

        var balanceSheetEndDate = $("#balanceSheetEndDate").val();



        $.ajax({

            type: "post",

            url: '{{url('api/getBalanceSheetReport')}}',

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                balanceSheetAmountType: balanceSheetAmountType,

                balanceSheetStartDate: balanceSheetStartDate,

                balanceSheetEndDate: balanceSheetEndDate,

            },

            success:function(res){

                $("#table-data").html(res.content).addClass("table-back");

                g['headings'] = res.headings;

                table = $('.paymentRegister-table').DataTable( {

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]

                });

                loader("hide");

            },

            error:function(res){

                loader("hide");

                console.log(res);

            }

        });

    }



    function printBalanceSheetReport() {

        var tableData = document.getElementById("balanceSheetPdfData").value ;

        newWin= window.open("");

        newWin.document.write(tableData);

        newWin.print();

        newWin.close();   

    }

    /* get balance shwwt report end */



    function getLedgerFilterCurrentValue(){

        

        loader("show");



        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        var leaderFor = $("input[name='leaderFor']:checked").val();

        var ledgerEndDate = $("#ledgerEndDate").val();



        $.ajax({

            type:"post",  

            url:'{{url('api/getLedgerReport')}}' ,

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                leaderFor: leaderFor,

            },

            success:function(res){

                $("#table-data").html(res.content).addClass("table-back");

                g['headings'] = res.headings;



                table = $('.ledgerReport-table').DataTable( {

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]

                    });



                console.log(res);

                loader("hide");                

            },

            error:function(res){

                console.log(res);

                loader("hide");

            }

        });

    }



function printLedgerReport() {

    var tableData = document.getElementById("ledgerReportprintPdfData").value ;

    newWin= window.open("");

    newWin.document.write(tableData);

    newWin.print();

    newWin.close();

    

}



/* get ledger report end*/





  /* Cm subsidiary Report start */





  function getCmSubsidiaryValueForTable() {
        loader("show");

        var dairyId = $("#dairyId").val();
        var status = $("#status").val(); 
        
        var amountLow = document.getElementById("cmSubsidiaryAmountLow").value ;
        var amountHigh = document.getElementById("cmSubsidiaryAmountHigh").value ;
        var startDate = document.getElementById("cmSubsidiaryStartDate").value ;
        var endDate = document.getElementById("cmSubsidiaryEndDate").value ;
        var supervisorName = document.getElementById("cmSubsidiaryFieldSupName").value ;
        var supervisorMobile = document.getElementById("cmSubsidiaryFieldSupContactNo").value ;

        $.ajax({
            type:"post",  
            url:'{{url('api/getCmSubsidiaryReport')}}',
            data: {
                device_token: "{{$device_token}}",

                dairyId: dairyId,
                status: status,
                amountLow: amountLow,
                amountHigh: amountHigh,
                startDate: startDate,
                endDate: endDate,
                supervisorName: supervisorName,
                supervisorMobile: supervisorMobile 
            },
            success:function(res){
                $("#table-data").html(res.content).addClass("table-back");
                // $("#pdfBTNCmSubs").show();
                g['headings'] = res.headings;

                table = $('.cmSubsidiaryReport-table').DataTable( {
                     "sScrollX": "100%", "sScrollXInner": "100%",
                    "dom": '<"dt-buttons"Bf><"clear">lirtp',
                    "paging": true,
                    "autoWidth": true,
                    "buttons": [
                        {
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },  
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]
                    });
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    }



    $("#pdfBTNCmSubs").on("click", function(){
        loader("show");
        $.ajax({
            type:"post",
            url:'{{url('getPDFfromHTML')}}',
            data: {
                content: $("#table-data").html(),
            },
            success:function(res){
                
                var link = document.createElement("a");
                link.download = "CmSubsidaryReport";
                link.href = res.url;
                link.click();
                // window.location = res.url;
                loader("hide");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    });


    function getMemStatementReport() {

        loader("show");



        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        

        var memberCode = $("#memberCode").val();

        var memberName = $("#memberNameSt").val();

        var startDate = $("#sdate").val();

        var endDate = $("#tdate").val();

        var groupByDate = $("#groupByDate").val();



        $.ajax({

            type:"post",

            url:'{{url('api/getMemStatementReport')}}',

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                memberCode: memberCode,

                memberName: memberName,

                startDate: startDate,

                endDate: endDate,

                groupByDate: groupByDate

            },

            success:function(res){

                loader("hide");



                $("#table-data").html(res.content).addClass("table-back");

                g['headings'] = res.headings

                table = $('.memStatement-table').DataTable({

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]

                });

            }

        }).done(function(res){

                console.log(res);

                loader("hide");

            });

    }



    function getCustSalseReport() {

        loader("show");



        var dairyId = $("#dairyId").val();

        var status = $("#status").val();

        

        var custCode = $("#customerCode").val();

        var custName = $("#customerName").val();

        var startDate = $("#sdateCust").val();

        var endDate = $("#tdateCust").val();

        var groupByDate = $("#groupByDateCust").val();



        $.ajax({

            type:"post",

            url:'{{url('api/getCustomerSalseReport')}}',

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                customerCode: custCode,

                customerName: custName,

                startDate: startDate,

                endDate: endDate,

                groupByDate: groupByDate

            },

            success:function(res){

                loader("hide");



                $("#table-data").html(res.content).addClass("table-back");

                g["headings"] = res.headings;



                table = $('.custSalse-table').DataTable( {

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": true,

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]

                });



            }

        }).done(function(res){

                console.log(res);

                loader("hide");

            });

    }



    function getProfitLossReport(e){

        loader("show");



        var dairyId = $("#dairyId").val();

        var status = $("#status").val();



        var startDate = $("#sdateProfit").val();

        var endDate = $("#tdateProfit").val();



        $.ajax({

            type:"post",

            url:'{{url('api/getProfitLossReport')}}',

            data: {

                device_token: "{{$device_token}}",

                dairyId: dairyId,

                status: status,

                startDate: startDate,

                endDate: endDate,

            },

            success:function(res){

                loader("hide");



                $("#table-data").html(res.content).addClass("table-back");

                g['headings'] = res.headings;

                table = $('.profitloss-table').DataTable( {

                     "sScrollX": "100%", "sScrollXInner": "100%",

                    "ordering": false,

                    "dom": '<"dt-buttons"Bf><"clear">lirtp',

                    "paging": false, 

                    "autoWidth": true,

                    "buttons": [{
                            text: 'PDF',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename;
                            }
                        },
                        {
                            text: 'Excel',
                            action: function(e, dt, node, config){
                                window.location = '{{url('')}}/'+res.filename_excel;
                            }
                        }]
                    });

            }

        }).done(function(res){

                console.log(res);

                loader("hide");

            });

    }









     function ExportPdf(){ 

        kendo.drawing

            .drawDOM("#table-data", 

            { 

                paperSize: "A4",

                margin: { top: "1cm", bottom: "1cm" },

                scale: 0.8,

                height: 500

            })

                .then(function(group){

                kendo.drawing.pdf.saveAs(group, "Exported.pdf")

            });

        }



    function printData()

    {

        $("#printableHidden").html("");

        $("#table-data table.table").clone().appendTo("#printableHidden");

        // var divToPrint=document.getElementById("table-data .table");

        newWin= window.open("");

        newWin.document.write($("#printableHidden").html());

        newWin.print();

        newWin.close();

    }



    $('#printBTN').on('click',function(){

        $("#printableHidden").html("");

        $("#table-data table.table").clone().appendTo("#printableHidden");



        printData();

    })



    $('#pdfBTN').on('click',function(){

        ExportPdf();

    })



    </script>





    <div id="printableHidden" class="dnone"></div>



@endsection