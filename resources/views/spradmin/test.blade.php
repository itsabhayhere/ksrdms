@extends('theme.ytdefault') 
@section('content')


<style>

        tr th,
        tr td {
            text-align: right;
        }
    
        th {
            background: #eee;
        }
    
    </style>
    
    <div class="fcard margin-fcard-1 clearfix">
    
        <div class="upper-controls clearfix">
            <div class="fl">
                <h3>Generate Rate Card</h3>
                <hr>
                {{--
                <div class="light-color f-12">Total: {{count($purchaseList)}}</div> --}}
            </div>
            <div class="fr">
                {{-- <a class="btn btn-primary" href="DailyTransactionForm">Add Purchase</a> --}}
            </div>
        </div>
    
        <div class="col-md-10 clearfix">
    
            <form class="rate clearfix">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-6 col-form-label">Min FAT Value</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="minFat" placeholder="Min FAT: 2">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-6 col-form-label">Max FAT Value</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="maxFat" placeholder="Max FAT: 20">
                        </div>
                    </div>
                </div>
    
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-6 col-form-label">Min SNF Value</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="minSnf" placeholder="Min SNF: 700">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-6 col-form-label">Max SNF Value</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="maxSnf" placeholder="Max SNF: 900">
                        </div>
                    </div>
                </div>
    
                <div class="col-md-12 clearfix">
                    <div class="pt-20 fl mb-20">
                        <h4>Enter Rate for Min FAT/SNF Values</h4>
                        <hr class="m-0">
    
                    </div>
                </div>
    
                <div class="clearfix">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-6 col-form-label">Rate for min FAT and SNF</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" id="minRate" placeholder="Value in Rs">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>
    
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-6 col-form-label">Deduction/addition of Rate on FAT </label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="rateVaryOnFat" placeholder="Value in Rs">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-6 col-form-label">Deduction/addition Rate on SNF </label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="rateVaryOnSnf" placeholder="Value in Rs">
                        </div>
                    </div>
                </div>
    
                <div class="col-md-6 col-md-offset-3">
                    <button class="btn btn-primary" href="#" onclick="generateRateCard(event)">View and edit Rate Card</button>
                </div>
    
            </form>
        </div>
    
        <div class="col-md-12 rateChart hide">
            <div class="pt-50"></div>
            <table class="table rateChartTable table-bordered table-striped">
    
            </table>
            <div class="pt-10 col-md-4 col-md-offset-4">
                <button class="btn btn-primary btn-block" onclick="saveRateChart()">Save your Rate Card</button>
            </div>
        </div>
    
    </div>
    
    
    <div class="flash-alert alert hide">
        <span class="close" data-dismiss="alert" aria-label="close">X</span>
        <div class="flash-msg">
    
        </div>
    </div>
    @endsection
     
    @section('scripts')
    <script>
        var minFat=0;
        var minSnf=0;
        var maxFat=0;
        var maxSnf=0;
        var minRate=0;
        var rateVaryOnFat=0;
        var rateVaryOnSnf=0;
        var pageUnloadFlag=false;
    
        window.rateChart = {};
    
        function getFormValues(){
            minFat = parseFloat($("#minFat").val());
            minSnf = parseFloat($("#minSnf").val());
            maxFat = parseFloat($("#maxFat").val());
            maxSnf = parseFloat($("#maxSnf").val());
            minRate = parseFloat($("#minRate").val());
            rateVaryOnFat = parseFloat($("#rateVaryOnFat").val());
            rateVaryOnSnf = parseFloat($("#rateVaryOnSnf").val());
    
            minFat = minFat?minFat:0;
            minSnf = minSnf?minSnf:0;
            maxFat = maxFat?maxFat:0;
            maxSnf = maxSnf?maxSnf:0;
            minRate = minRate?minRate:0;
            rateVaryOnFat = rateVaryOnFat?rateVaryOnFat:0;
            rateVaryOnSnf = rateVaryOnSnf?rateVaryOnSnf:0;
            
            return ({minFat,minRate,minSnf,maxFat,maxSnf,rateVaryOnFat,rateVaryOnSnf});
        }
    
        function generateRateCard(event){
            event.preventDefault();
            pageUnloadFlag = true;
    
            form = getFormValues();
            
            window.rateChart.header = form;
            window.rateChart.data = {};
            window.rateChart.collectionManager = "";
    
            trate = minRate;
    
            thead = "<thead><tr><th class='dual-column'><span class='bl'>FAT</span><span class='tr_'>SNF</span></th>";
                for(s = minSnf*10; s<=maxSnf*10; s+=1){
                    thead+="<th>"+(s/10).toFixed(1)+"</th>";
                }
            thead += "</tr></thead>";
    
            tbody = "<tbody>";
    
            fcount = 0;
            scount = 0;
            for(f=minFat*10; f<=maxFat*10; f+=1){
                // console.log(f/10);
                trate = minRate+(fcount*rateVaryOnFat);
                tbody+="<tr><th>"+(f/10).toFixed(1)+"</th>";
                for(s = minSnf*10; s<=maxSnf*10; s+=1){
                    trate = trate+(scount?rateVaryOnSnf:0);
    
                    tbody+="<td><input type='number' value='"+trate.toFixed(2)+"' data-fat='"+(f/10).toFixed(1)+"' data-snf='"+(s/10).toFixed(1)+"' data-fixrate='"+trate.toFixed(2)+"' onchange='changeRate(this)'></td>";
    
                    // window.rateChart.data.[(f/10).toFixed(1)+' '+(s/10).toFixed(1)]= {f:(f/10).toFixed(1), s:(s/10).toFixed(1), rate: trate.toFixed(2)};
                    Object.defineProperty(window.rateChart.data, (f/10).toFixed(1)+' '+(s/10).toFixed(1), {value : {f:(f/10).toFixed(1), s:(s/10).toFixed(1), rate: trate.toFixed(2), isUpdated:"false"},
                               writable : true,
                               enumerable : true,
                               configurable : true});
                    scount++;
                }
                scount = 0;
                fcount++;
                tbody+="</tr>";
            }
            console.log(window.rateChart);
    
    
            $(".rateChartTable").html(thead+tbody);
            $(".rateChart").removeClass("hide");
        }
    
    
        function changeRate(e){
            f = $(e).data('fat');
            s = $(e).data('snf');
            val = $(e).val();
            update = "false";
            
            fixRate = $(e).data('fixrate');
            if(parseFloat(fixRate)!=parseFloat(val)){
                $(e).closest("td").addClass("updated_cell");
                update = "true";
            }else{
                $(e).closest("td").removeClass("updated_cell");
                update = "false";
            }
    
            console.log(update, val, fixRate);
    
            window.rateChart.data[f+" "+s]={f:f, s:s, rate: val, isUpdated:update};
    
    
            // console.log(window.rateChart.data[f+" "+s]);
            // console.log(window.rateChart);
        }
    
        function saveRateChart(){
            d = window.rateChart;
            
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            loader('show');
            $.ajax({
                    type: 'post',
                    url: 'saveRateCardNew',
                    // dataType: 'json',
                    // contentType: 'application/json',
                    data: d,
                    success: function(res) {
                        loader('hide');
                        console.log(res);
                        if(res.error){
                            $(".flash-alert").removeClass("hide").addClass("alert-danger");
                            $(".flash-msg").html(res.msg);
                        }else{
                            $(".flash-alert").removeClass("hide").addClass('alert-success');
                            $(".flash-msg").html(res.msg);
                        }
                        pageUnloadFlag = false;
                        window.location = "rateCardShowNew";
                    },
                    error: function(res){
                        loader('hide');
                        console.log(res);
                    }
                });
        }
    
    
        window.onbeforeunload = function() {
            if(pageUnloadFlag){
                return "Changes you made may not be saved. Please save your work before you navigate or refresh page.";
            }else return null;
        }
    
    </script>
@endsection