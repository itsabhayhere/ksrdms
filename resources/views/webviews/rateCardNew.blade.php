@extends('theme.webview') 
@section('content')
<style>
    tr th,
    tr td {
        text-align: right;
    }

    th {
        background: #eee;
    }

    .ps-0 {
        padding-left: 0;
        padding-right: 0;
    }

    .table-striped>tbody>tr:nth-child(odd)>td,
    .table-striped>tbody>tr:nth-child(odd)>th {
        background-color: #f6f6f6;
    }
</style>
<div class="fcard margin-fcard-1 clearfix">
    <div class="upper-controls p-0 pb-5 ml-40 clearfix">
        <div class="fl">
            <h3>Generate Rate Card</h3>
        </div>
    </div>
    <div class="col-md-12 clearfix">
        <form class="rate clearfix" id="form">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="cManager" class="col-sm-6 col-form-label">Collection Manager</label>
                        <div class="col-sm-6">
                            <select name="cManager" id="cManager" class="selectpicker" onchange="updateCollectionManager(this)" title="Select Collection Manager"
                                required>
                                    @foreach($colManager as $man)
                                        <option value="{{$man->id}}">{{$man->userName}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="rateType" class="col-sm-6 col-form-label">Rate Card Type</label>
                        <div class="col-sm-6">
                            <select name="rateType" id="rateType" class="selectpicker" onchange="updateRateCardType(this)" title="Select Rate Card Type"
                                required>
                                    <option value="fat">FAT</option>
                                    <option value="fat/snf">FAT/SNF</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="description" class="col-sm-6 col-form-label">Description</label>
                        <div class="col-sm-6">
                            <input name="description" id="description" class="form-control" value="">
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <hr>
                </div>
                <div class="togglefieldsfirst dnone">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="minFat" class="col-sm-6 col-form-label">Min FAT Value</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="minFat" placeholder="Min FAT: 2" required step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="maxFat" class="col-sm-6 col-form-label">Max FAT Value</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="maxFat" placeholder="Max FAT: 20" required step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="snfRange">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="minSnf" class="col-sm-6 col-form-label">Min SNF Value</label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="minSnf" placeholder="Min SNF: 700" required step="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="maxSnf" class="col-sm-6 col-form-label">Max SNF Value</label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="maxSnf" placeholder="Max SNF: 990" required step="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 clearfix">
                        <div class="pt-10 fl mb-20">
                            <h4>Enter Range Details</h4>
                            <hr class="m-0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 togglefieldsfirst dnone range clearfix">
                <div class="rangeText">FAT Range 1</div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="minFatRange" class="col-sm-6 col-form-label">Min FAT </label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="minFatRange" placeholder="Lower FAT Range" data-range="1" readonly required
                                step="0.01">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="maxFatRange" class="col-sm-6 col-form-label">Max FAT </label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="maxFatRange" placeholder="Upper FAT Range" data-range="1" required step="0.01">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="rInFatRange" class="col-sm-6 col-form-label">Increase Rate on FAT Increase </label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="rInFatRange" placeholder="Price Up for FAT" data-range="1" required step="0.01">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 hide">
                    <div class="form-group row">
                        <label for="rDecFatRange" class="col-sm-6 col-form-label">Decrease Rate on FAT Decrease </label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="rDecFatRange" placeholder="Price Drop for FAT" data-range="1" required step="0.01">
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-6 fatSnfIncreaseField">
                    <div class="form-group row">
                        <label for="rInSnfRange_fat" class="col-sm-6 col-form-label">Increase Rate on SNF Increase </label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="rInSnfRange_fat" placeholder="Price Up for SNF" data-range="1" required step="0.01">
                        </div>
                    </div>
                </div> --}}

                {{--
                <div class="col-md-12">SNF Values are same as above</div> --}}
                <div class="clearfix"></div>

                <div class="dnone toggleSnfRange snfRanges">
                    <div class="snfVariations clearfix">

                        <div class="snfrangeText">SNF Range <strong>1</strong> </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="minSnfRange" class="col-sm-6 col-form-label">Min SNF </label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="minSnfRange" placeholder="Lower SNF Range" data-range="1" data-rangesnf="1"
                                        readonly required step="10">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="maxSnfRange" class="col-sm-6 col-form-label">Max SNF </label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="maxSnfRange" placeholder="Upper SNF Range" data-range="1" data-rangesnf="1"
                                        required step="10">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="rInSnfRange" class="col-sm-6 col-form-label">Increase Rate on SNF Increase </label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="rInSnfRange" placeholder="Price Up for SNF" data-range="1" required step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 hide">
                            <div class="form-group row">
                                <label for="rDecSnfRange" class="col-sm-6 col-form-label">Decrease Rate on SNF Decrease </label>
                                <div class="col-sm-6">
                                    <input type="number" class="form-control" id="rDecSnfRange" placeholder="Price Drop for SNF" data-range="1" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix">
                    <br>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="rAvgFatSnfRange" class="col-sm-6 col-form-label Avg-Show-Label">Milk Price </label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="rAvgFatSnfRange" placeholder="Rate in Rs" required step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>

                <div class="col-md-6 col-md-offset-3">
                    <a class="btn btn-primary saveRange-btn" role="button" href="javascript:void(0);" onclick="saveRange(event,this)" data-range="1">Save Range</a>
                    <input class="hide" id="submit-btn" type="submit" />
                </div>
    
            </div>


            {{--
            <div class="col-md-6 col-md-offset-3">
                <button class="btn btn-primary" href="javascript:void(0);" onclick="generateRateCard(event)">View and edit Rate Card</button>
            </div> --}}
        </form>
    </div>
    <div class="col-md-12 rangeList mt-20 hide">
        <table class="table rangeListTable table-bordered table-striped">
            <thead>
                <tr id="theadTrRangeTbl">
                    <th>S.N</th>
                    <th>Min FAT</th>
                    <th>Max FAT</th>
                    <th>Min SNF</th>
                    <th>Max SNF</th>
                    <th>Rate Increase FAT Increase</th>
                    <th>Rate Decrease FAT Decrease</th>
                    <th>Rate Increase SNF Increase</th>
                    <th>Rate Decrease SNF Decrease</th>
                    <th>Rate For Avg FAT & SNF</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="rangeListBody">
            </tbody>
        </table>
        <div class="col-md-6 col-md-offset-3">
            <a class="btn btn-primary rateCardGenerateBtn dnone" href="javascript:void(0);" onclick="generateRateCard(event)">View Rate Chart and Edit</a>
        </div>
    </div>
    <div class="col-md-12 ps-0 rateChart hide">
        <div class="pt-50"></div>
        <div style="width: 100%;overflow: scroll;height: 800px;">
            <table class="table rateChartTable table-bordered table-striped">
            </table>
        </div>
        <div class="pt-10 col-md-3 col-md-offset-3">
            <button class="btn btn-primary btn-block" id="saveRateChartbtn" onclick="saveRateChart()">Save your Rate Card</button>
        </div>
    </div>
</div>
@endsection
 
@section('scripts')
<script>
    // var minFat=0;
    // var minSnf=0;
    // var maxFat=0;
    // var maxSnf=0;
    var minRate=0;
    var rateVaryOnFat=0;
    var rateVaryOnSnf=0;
    var pageUnloadFlag=false;
    var range = [];
    var snfRanges = [];
    var rangeList = {};
    window.rateChart = {};
    var theadTrRangeSNF = '<th>S.N</th>'+
                            '<th>Min FAT</th>'+
                            '<th>Max FAT</th>'+
                            '<th>Rate Increase FAT Increase</th>'+
                            // '<th>Rate Increase SNF Increase</th>'+
                            // '<th>Rate For Avg FAT & SNF</th>'+
                            '<th>Action</th>';
    var theadTrRangeFat = '<th>S.N</th>'+
                            '<th>Min FAT</th>'+
                            '<th>Max FAT</th>'+
                            '<th>Rate Increase FAT Increase</th>'+
                            '<th>Rate For Min. Range of FAT & SNF</th>'+
                            '<th>Action</th>';
    var subtable = '<table class="table table-bordered table-striped"><th>S.N</th>'+
                        '<th>Min SNF</th>'+
                        '<th>Max SNF</th>'+
                        '<th>Rate Increase SNF Increase</th>'+
                        '<th>Rate For Avg FAT & SNF</th>';
    

    $("#form").on("submit", function(event){
        event.preventDefault();
        $(".saveRange-btn").click();
    });

    $("#minFat").on("change", function(){
        $("#minFatRange").val($(this).val());
        $("#minFatRange").attr("min", $(this).val());
        $("#maxFatRange").attr("min", $(this).val());
    });
    $("#maxFat").on("change", function(){
        $("#maxFatRange").attr("max", $(this).val());
        $("#minFatRange").attr("max", $(this).val());
    });
    $("#minSnf").on("change", function(){
        $("#minSnfRange").val($(this).val());
        $("#minSnfRange").attr("min", $(this).val());
        $("#maxSnfRange").attr("min", $(this).val());
    });
    $("#maxSnf").on("change", function(){
        $("#minSnfRange").attr("max", $(this).val());
        $("#maxSnfRange").attr("max", $(this).val());
    });

    $("#rInSnfRange_fat").on("change", function(){
        $("#rInSnfRange").val($(this).val());
    });

    function saveRange(event, e){
        event.preventDefault();
        pageUnloadFlag = true;
        $("#rDecFatRange").val($("#rInFatRange").val());
        $("#rDecSnfRange").val($("#rInSnfRange").val());
        if(!validateRange()){
            return;
        }
        loaderTime(500);
        r = $(e).data("range");
        updateValidation(r);

        _r = createRangeListTable();
        updateRateCardButton();

        resetRangeForm();
        $("#rateType").css("disabled", true);
        setRangeValuesInForm(_r);

        console.log(range, rangeList);
    }
    
    function setRangeValuesInForm(_r){
        if(Object.keys(rangeList).length == 0 || typeof rangeList[_r] === undefined){
            snflength = 0;
            
            $("#minFatRange").val(range.minFat).attr('min', range.minFat).attr('max', range.minFat);
            $("#maxFatRange").val('').attr('min', range.minFat+0.1).focus();

            $("#minSnfRange").val(range.minSnf).attr('min', range.minSnf).attr('max', range.minSnf).trigger('keyup');
            $("#maxSnfRange").val('').attr('min', range.minSnf+10);

            $(".snfrangeText").html("SNF Range 1");
        }else{
            snflength = rangeList[_r].snfRanges.length;
                
            if($("#rateType").val()=="fat" || ($("#rateType").val()=="fat/snf" && rangeList[_r].snfRanges[snflength-1].mxSnf == range.maxSnf)){
                snfRanges = [];

                if(Object.keys(rangeList).length>0){
                    if(rangeList[_r].mxFat < range.maxFat){
                        $("#minFatRange").val(roundNumber(rangeList[_r].mxFat+0.1, 1)).attr('min', roundNumber(rangeList[_r].mxFat+0.1,1)).attr('max', roundNumber(rangeList[_r].mxFat+0.1, 1));
                        $("#maxFatRange").attr('min', roundNumber(rangeList[_r].mxFat+0.1, 1));
                        $("#maxFatRange").val('').focus();

                        $("#minSnfRange").val(range.minSnf).attr('min', range.minSnf).attr('max', range.minSnf).trigger('keyup');
                        $("#maxSnfRange").attr('min', range.minSnf+10);

                        $("#rInFatRange").val('');

                        $(".snfrangeText").html("SNF Range 1");
                    }else{
                        console.log("afdasd");
                        $(".togglefieldsfirst").hide();
                        $(".rateCardGenerateBtn").show();
                    }
                }else{
                    $("#minFatRange").val(range.minFat);
                }

                $(".rangeText").html("FAT Range "+(parseInt(_r)+1));
                $("[data-range]").data("range", parseInt(_r)+1);
                // console.log($("input[data-range]"), rangeNo);
                $(".rangeList").removeClass("hide");

            }else{

                $("#minSnfRange").val(rangeList[_r].snfRanges[snflength-1].mxSnf+10).attr('min', rangeList[_r].snfRanges[snflength-1].mxSnf).attr('max', range.maxSnf).trigger('keyup');
                $("#maxSnfRange").attr('min', rangeList[_r].snfRanges[snflength-1].mxSnf+10);
                $("#maxSnfRange").focus();

                $(".snfrangeText").html("SNF Range "+(snflength+1));
                $(".rangeList").removeClass("hide");

            }
        }

    }

    $("#minFatRange, #maxFatRange, #minSnf, #maxSnf, #minSnfRange").on("keyup",function(){
        avgFat = roundNumber(parseFloat($("#minFatRange").val()), 1);
        avgSnf = roundNumber(parseFloat($("#minSnfRange").val()), 0);
        if(isNaN(avgFat))SavgFat="...";else SavgFat=avgFat.toFixed(2);
        if(isNaN(avgSnf))SavgSnf="...";else SavgSnf=avgSnf.toFixed(2);

        if($("#rateType").val() == "fat"){
            $(".Avg-Show-Label").html("Milk Price of <avg>"+SavgFat+"</avg> Fat");
        }else{
            $(".Avg-Show-Label").html("Milk Price of <avg>"+SavgFat+"</avg> Fat & <avg>"+SavgSnf+"</avg> SNF");
        }
        r = $(this).data("range");
        // updateValidation(r);
    });


    function createRangeListTable(){
        var row = "";
        var rangeNo = 0;

        if($("#rateType").val()=="fat"){
            

            l = Object.keys(rangeList).length;i = 1;str="removeRangeClass";
            $.each(rangeList, function(rNo, range){

                if(i==l) str = "";
                i++;

                row += '<tr id="range'+rNo+'">'+
                        '<td>'+rNo+'. </td>'+
                        '<td>'+range.mnFat+'</td>'+
                        '<td>'+range.mxFat+'</td>'+
                        '<td>'+range.rInFat+'</td>'+
                        '<td>'+range.rFat+'</td>'+
                        '<td><a role="button" href="javascript:void(0);" class="'+str+'" onclick="removeRangeList(this)" data-rrange="'+rNo+'"><i class="fa fa-close red"></i></a></td>'+
                    '</tr>';

                rangeNo = rNo;
            });
            $("#theadTrRangeTbl").html(theadTrRangeFat);
        }else{
            l = Object.keys(rangeList).length;i = 1;str="removeRangeClass";
            $.each(rangeList, function(rNo, range){
                if(i==l) str = "";
                i++;
                row += '<tr id="range'+rNo+'">'+
                        '<td>'+rNo+'. </td>'+
                        '<td>'+range.mnFat+'</td>'+
                        '<td>'+range.mxFat+'</td>'+
                        '<td>'+range.rInFat+'</td>'+
                        // '<td>'+range.rInSnf+'</td>'+
                        // '<td>'+range.rAvgFatSnf+'</td>'+
                        '<td><a role="button" href="javascript:void(0);" class="'+str+'" onclick="removeRangeList('+rNo+')" data-rrange="'+rNo+'"><i class="fa fa-close red"></i></a></td>'+
                    '</tr>';
                    
                _subtable = "";
                $.each(range.snfRanges, function(sno, snr){
                    
                    _subtable += "<tr>"+
                                "<td>"+(sno+1)+"</td>"+
                                "<td>"+snr.mnSnf+"</td>"+
                                "<td>"+snr.mxSnf+"</td>"+
                                "<td>"+snr.rInSnf+"</td>"+
                                "<td>"+snr.rFatSnf+"</td>"+
                                "</tr>";
                });
                _subtable += "</table>";
                row += '<tr id="range123'+rNo+'"><td></td><td colspan="5">'+subtable+_subtable+'</td></tr>';
                
                rangeNo = rNo;
            });

            $("#theadTrRangeTbl").html(theadTrRangeSNF);
        }

        document.getElementById("rangeListBody").innerHTML= row;
        delete row;


        return rangeNo;
    }

    function removeRangeList(rangeId){
        delete rangeList[rangeId];
        snfRanges = [];
        var i = 1;
        var ob = {};
        // console.log(rangeList);
        // console.log(Object.getOwnPropertyNames(rangeList).length);
        $.each(rangeList, function(index, value){
            Object.defineProperty(ob, i, {value : this, writable : true, enumerable : true, configurable : true});
            i++;
        })
        rangeList = ob;
        delete ob;

        _r = createRangeListTable();
        updateRateCardButton();

        setRangeValuesInForm(_r);
        $(".togglefieldsfirst").show();
        $(".rateCardGenerateBtn").hide();
    }

    function updateRateCardButton(){
        // var fatRange = parseFloat(range['maxFat']) - parseFloat(range["minFat"]);
        // var snfRange = parseFloat(range['maxSnf']) - parseFloat(range["minSnf"]);
        // fat = 0;
        // $(rangeList).each(function(){
        //     console.log(this.mnFat);
        //     fat += this.mxFat - this.mnFat;
        // })

        // console.log(fat);
        // $(".rateCardGenerateBtn")
    }
    
    function validateRange(){
        form = document.getElementById("form");
        if (form.checkValidity()) {
                return true;
        }else{
            form.querySelector('input[type="submit"]#submit-btn').click();
            return false;
        }
    }

    function updateValidation(r){
        console.log("drwer");
        if(typeof r === 'undefined' || r == undefined){
            return;
        }
        var mnFat = roundNumber(parseFloat($("#minFatRange").val()), 1);
        var mxFat = roundNumber(parseFloat($("#maxFatRange").val()), 1);
        var rInFat = roundNumber(parseFloat($("#rInFatRange").val()), 2);
        // var rDecFat = roundNumber(parseFloat($("#rDecFatRange").val()), 2);
        if(isNaN(mxFat)){
            return;
        }

        var rAvgFatSnf = roundNumber(parseFloat($("#rAvgFatSnfRange").val()), 2);

        range = {
            minFat: roundNumber(parseFloat($("#minFat").val()), 1),
            maxFat: roundNumber(parseFloat($("#maxFat").val()), 1),
            minSnf: roundNumber(parseFloat($("#minSnf").val()), 0),
            maxSnf: roundNumber(parseFloat($("#maxSnf").val()), 0),
        }

        if($("#rateType").val()=="fat"){
            var rInSnf = roundNumber(parseFloat($("#rInSnfRange").val()), 2);
            var minSnf = range.minSnf;
            var maxSnf = range.maxSnf;
            // var rDecSnf = null;
            
            snfRanges[0] = {
                "mnSnf":range.minSnf,
                "mxSnf":range.maxSnf,
                "rInSnf":rInSnf,
                "rFatSnf": rAvgFatSnf,
            };
        }else{
            var rInSnf = roundNumber(parseFloat($("#rInSnfRange").val()), 2);
            var minSnf = roundNumber(parseFloat($("#minSnfRange").val()), 0);
            var maxSnf = roundNumber(parseFloat($("#maxSnfRange").val()), 0);
            // var rDecSnf = roundNumber(parseFloat($("#rDecSnfRange").val()), 2);
            if(isNaN(maxSnf)){
                return;
            }
            snfRanges[snfRanges.length] = {
                "mnSnf":minSnf,
                "mxSnf":maxSnf,
                "rInSnf":rInSnf,
                "rFatSnf": rAvgFatSnf,
            };
        }


        rangeList[r] = {
            "mnFat":mnFat,
            "mxFat":mxFat,
            "rInFat":rInFat,
            "rFat": rAvgFatSnf,
            "snfRanges": snfRanges,
        };

        // Object.defineProperty(rangeList, r+"", {value : {
        //         "mnFat":mnFat,
        //         "mxFat":mxFat,
        //         "rInFat":rInFat,
        //         "snfRange": snfRanges,
        //         // "rDecFat":rDecFat,
        //         // "rDecSnf":rDecSnf,
        //         // "rAvgFatSnf":rAvgFatSnf,
        //         // "avgFat":mnFat
        //     },
        //     writable : true,
        //     enumerable : true,
        //     configurable : true
        // });

        triggerFlag = false;
        if(range.minFat>range.maxFat){
            range.maxFat = range.minFat + 0.1;
            $("#maxFat").val(range.maxFat);
            triggerFlag = true;
        }

        if(range.maxFat<range.minFat){
            range.minFat = range.maxFat - 0.1;
            $("#minFat").val(range.maxFat);
            triggerFlag = true;
        }

        if($("#rateType").val() == "fat/snf"){
            if(range.minSnf>range.maxSnf){
                range.maxSnf = range.minSnf + 10;
                $("#maxSnf").val(range.maxSnf);
                triggerFlag = true;
            }
            if(range.maxSnf<range.minSnf){
                range.minSnf = range.maxSnf - 10;
                $("#minSnf").val(range.maxSnf);
                triggerFlag = true;
            }
        }
        if(triggerFlag){
            $("#maxFat").trigger("change");
        }
            // $("#minFatRange, #maxFatRange").attr("min", range.minFat).attr("max", range.maxFat);
            // $("#minFatRange").val();
    }

    function generateRateCard(event){
        event.preventDefault();
        loaderTime(500);

        pageUnloadFlag = true;
        // form = getFormValues();

        window.rateChart.range = range;
        window.rateChart.rangeList = rangeList;
        window.rateChart.data = {};
        window.rateChart.collectionManager = $("#cManager").val();
        window.rateChart.rateType = $("#rateType").val();

            if($("#rateType").val()=="fat"){
                thead = "<thead><tr><th>FAT</th><th>Milk Value</th>";
        }else{
                thead = "<thead><tr><th class='dual-column'><span class='bl'>FAT</span><span class='tr_'>SNF</span></th>";
                for(s = range.minSnf; s<=range.maxSnf; s+=10){
                    thead+="<th>"+s+"</th>";
            }
        }
        thead += "</tr></thead>";
        tbody = "<tbody>";
        fcount = 0;
        scount = 0;
        $.each(rangeList, function(index, rng){
            // trate = rng.snfRanges[0].rFatSnf;

            // avgFat = this.mnFat;
            // avgSnf = range.minSnf;
            for(f=this.mnFat*10; f<=this.mxFat*10; f+=1){
                tbody+="<tr><th>"+(f/10).toFixed(1)+"</th>";

                $.each(this.snfRanges, function(snfRangesIndex, snfRange){

                    s = snfRange.mnSnf;
                    do{
                        fatVary = rng.rInFat;
                        snfVary = snfRange.rInSnf;
                        
                        // if((f-(avgFat*10))<0){fatVary = this.rDecFat;}else{fatVary = rng.rInFat;}
                        // if((s-(avgSnf))<0){snfVary = this.rDecSnf;}else{snfVary = snfRange.rInSnf;}
                        
                        if(window.rateChart.rateType == 'fat'){
                            trate = rng.rFat+(fatVary*(f-(rng.mnFat*10)));
                        }else{
                            trate = snfRange.rFatSnf+(fatVary*(f-(rng.mnFat*10))) + (snfVary*(s-(snfRange.mnSnf)))/10;
                        }

                        tbody+="<td><input type='number' value='"+trate.toFixed(2)+"' data-fat='"+(f/10).toFixed(1)+"' data-snf='"+s+"' data-fixrate='"+trate.toFixed(2)+"'"+
                                " data-rangelist='"+index+"' onchange='changeRate(this)'></td>";
                        Object.defineProperty(window.rateChart.data, (f/10).toFixed(1)+' '+s, {value : {f:(f/10).toFixed(1), s:s, rate: trate.toFixed(2), rangeListKey:index+" "+snfRangesIndex, isUpdated:"false"},
                                writable : true,
                                enumerable : true,
                                configurable : true});
                        scount++;

                        s+=10;
                    }while(s<=snfRange.mxSnf);

                    // for(s = snfRange.mnSnf; s<=snfRange.mxSnf; s+=10){
                        
                    //     fatVary = rng.rInFat;
                    //     snfVary = snfRange.rInSnf;
                        
                    //     // if((f-(avgFat*10))<0){fatVary = this.rDecFat;}else{fatVary = rng.rInFat;}
                    //     // if((s-(avgSnf))<0){snfVary = this.rDecSnf;}else{snfVary = snfRange.rInSnf;}
                        
                    //     trate = snfRange.rFatSnf+(fatVary*(f-(rng.mnFat*10))) + (snfVary*(s-(snfRange.mnSnf)))/10;

                    //     tbody+="<td><input type='number' value='"+trate.toFixed(2)+"' data-fat='"+(f/10).toFixed(1)+"' data-snf='"+s+"' data-fixrate='"+trate.toFixed(2)+"'"+
                    //             " data-rangelist='"+index+"' onchange='changeRate(this)'></td>";
                    //     Object.defineProperty(window.rateChart.data, (f/10).toFixed(1)+' '+s, {value : {f:(f/10).toFixed(1), s:s, rate: trate.toFixed(2), rangeListKey:index, isUpdated:"false"},
                    //             writable : true,
                    //             enumerable : true,
                    //             configurable : true});
                    //     scount++;
                    // }
                })

                scount = 0;
                fcount++;
                tbody+="</tr>";
            }
        })
        // console.log(window.rateChart);
        $(".rateChartTable").html(thead+tbody);
        $(".rateChart").removeClass("hide");
    }
    
    function changeRate(e){
        f = $(e).data('fat');
        s = $(e).data('snf');
        val = $(e).val();
        index = $(e).data("rangelist");
        update = "false";

        fixRate = $(e).data('fixrate');
        if(parseFloat(fixRate)!=parseFloat(val)){
            $(e).closest("td").addClass("updated_cell");
            update = "true";
        }else{
                $(e).closest("td").removeClass("updated_cell");
                update = "false";
        }
            // console.log(update, val, fixRate);
            window.rateChart.data[f+" "+s]={f:f, s:s, rate: val, rangeListKey:index, isUpdated:update};
    }

    function saveRateChart(){
        // $("#saveRateChartbtn").attr("disabled", true);
        d = window.rateChart
        d.device_token = "{{$device_token}}";;

        console.log(d);

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        loader('show');
        $.ajax({
                type: 'post',
                url: '{{url("api/saveRateCardNew")}}',
                // dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(d),
                success: function(res) {
                    // console.log(res);
                    if(res.error){
                        $(".flash-alert .flash-msg").html(res.msg);
                        $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                }else{
                        $(".flash-alert .flash-msg").html(res.msg);
                        $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                }
                    pageUnloadFlag = false;
                    loader('hide');
                    window.location = "{{url('api/rateCardShow')}}"+"?device_token={{$device_token}}";
            },
            error: function(res){
                loader('hide');
                console.log(res);
            }
        });
    }
    
    function resetRangeForm(){
        //$("#minFatRange").val("");
        // $("#maxFatRange").val("");
        // $("#rInFatRange").val("");
        // $("#rDecFatRange").val("");
        $("#maxSnfRange").val("");
        $("#rInSnfRange").val("");
        $("#rDecSnfRange").val("");
        $("#rAvgFatSnfRange").val("");
        // $("#minFatRange").trigger("keyup");
        updateRateCardType();
    }

    window.onbeforeunload = function() {
        if(pageUnloadFlag){
            return "Changes you made may not be saved. Please save your work before you navigate or refresh page.";
        }else return null;
    }

    // function getFormValues(){
    //     minFat = parseFloat($("#minFat").val());
    //     minSnf = parseFloat($("#minSnf").val());
    //     maxFat = parseFloat($("#maxFat").val());
    //     maxSnf = parseFloat($("#maxSnf").val());
    //     minRate = parseFloat($("#minRate").val());
    //     rateVaryOnFat = parseFloat($("#rateVaryOnFat").val());
    //     rateVaryOnSnf = parseFloat($("#rateVaryOnSnf").val());
    //     minFat = minFat?minFat:0;
    //     minSnf = minSnf?minSnf:0;
    //     maxFat = maxFat?maxFat:0;
    //     maxSnf = maxSnf?maxSnf:0;
    //     minRate = minRate?minRate:0;
    //     rateVaryOnFat = rateVaryOnFat?rateVaryOnFat:0;
    //     rateVaryOnSnf = rateVaryOnSnf?rateVaryOnSnf:0;

    //     return ({minFat,minRate,minSnf,maxFat,maxSnf,rateVaryOnFat,rateVaryOnSnf});
    // }

    function updateRateCardType(){
        $(".togglefieldsfirst").show();
        $(".rateCardGenerateBtn").hide();

        if($("#rateType").val()=="fat"){
            $(".snfRange, .toggleSnfRange").hide();
            $("#minSnf, #maxSnf, #maxSnfRange, #minSnfRange, #rInSnfRange").attr("required", false);
        }else{
            $(".snfRange, .toggleSnfRange").show();
            $("#minSnf, #maxSnf, #maxSnfRange, #minSnfRange, #rInSnfRange").attr("required", true);
        }
    }

    function updateCollectionManager(){
        window.rateChart.collectionManager = $("#cManager").val();
    }

    function roundNumber(num, scale) {
        if(!("" + num).includes("e")) {
            return +(Math.round(num + "e+" + scale)  + "e-" + scale);
        } else {
            var arr = ("" + num).split("e");
            var sig = ""
            if(+arr[1] + scale > 0) {
                sig = "+";
            }
            return +(Math.round(+arr[0] + "e" + sig + (+arr[1] + scale)) + "e-" + scale);
        }
    }

</script>
@endsection