<div class="col-md-12 rateChart" >
    {{--
    <div class="pt-50"></div> --}}

    <h3 class="text-center">Rate Card Details</h3>

    <hr class="m-0">

    <div class="pt-10"></div>

    @if($cardFor=="cow")
        <div class="fl rt-tag defaultRateCardCow"></div>
    @elseif($cardFor=="buff")
        <div class="fl rt-tag defaultRateCardBuff"></div>
    @elseif($cardFor=="both")
        <div class="fl rt-tag defaultCowBuff"></div>
    @endif

    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-10">
        <table class="table table-bordered table-striped table-mini">
            <thead>
                <tr>
                    <th>Rate Card Type</th>
                    <th>Min Fat</th>
                    <th>Max Fat</th>
                    <th>Min SNF</th>
                    <th>Max SNF</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{strtoupper($shortCard->rateCardType)}}</td>
                    <td>{{$shortCard->minFat}}</td>
                    <td>{{$shortCard->maxFat}}</td>
                    <td>
                        @if(strtolower($shortCard->rateCardType) == 'fat' ) - @else {{$shortCard->minSnf}} @endif
                    </td>
                    <td>
                        @if(strtolower($shortCard->rateCardType) == 'fat' ) - @else {{$shortCard->maxSnf}} @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <table class="table table-bordered table-striped table-mini">
        <thead>
            <tr>
                <th>S.N</th>
                <th>Min FAT</th>
                <th>Max FAT</th>
                @if(strtolower($shortCard->rateCardType) == 'fat/snf' )
                    <th>Min SNF</th>
                    <th>Max SNF</th>
                @endif
                <th>Rate Increase FAT Increase</th>
                {{-- <th>Rate Decrease FAT Decrease</th> --}}
                <th>Rate Increase SNF Increase</th>
                {{-- <th>Rate Decrease SNF Decrease</th> --}}
                <th>Rate For Min FAT & SNF</th>
                {{-- <th>Average Fat</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($rangeList as $range)
            <tr>
                <th>{{$range->id}}</th>
                <th>{{$range->mnFat}}</th>
                <th>{{$range->mxFat}}</th>
                @if(strtolower($shortCard->rateCardType) == 'fat/snf' )
                    <th>{{$range->mnSnf}}</th>
                    <th>{{$range->mxSnf}}</th>
                @endif
                <th>{{$range->rIncFat}}</th>
                {{-- <th>{{$range->rDecFat}}</th> --}}
                <th>
                    @if(strtolower($shortCard->rateCardType) == 'fat' ) - @else {{$range->rIncSnf}} @endif
                </th>
                {{-- <th>{{$range->rDecSnf}}</th> --}}
                <th>{{$range->rAvgFatSnf}}</th>
                {{-- <th>{{$range->avgFat}}</th> --}}
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4 class="text-center"></h4>
    <div class="upper-controls pt-0 clearfix">
        <div class="fl">
            <h3>Rate Card</h3>
        </div>
        <div class="fr">
            <form action="applyRatecard" method="post" class="fl ms-20">
                <input type="hidden" name="collectionManager" value="{{$shortCard->collectionManager}}" />
                <input type="hidden" name="shortCardId" value="{{$shortCard->id}}" />
                <input type="hidden" name="rateCardType" value="{{$shortCard->rateCardType}}" />
                <button class="btn btn-success" type="submit" name="type" value="cow"><i class="glyphicon glyphicon-pushpin"></i> Apply RateCard For Cow</button>
                <button class="btn btn-success" type="submit" name="type" value="buffalo"><i class="glyphicon glyphicon-pushpin"></i> Apply RateCard For Buffalo</button>
                <button class="btn btn-success" type="submit" name="type" value="both"><i class="glyphicon glyphicon-pushpin"></i> Apply RateCard For Both</button>
            </form>

            <button class="btn btn-primary updateRateCardBtn hide" onclick="updateRateCard()" disabled>Update Rate Card</button>

            <button class="btn btn-primary" onclick="editRateCard()"><i class="glyphicon glyphicon-edit"></i> Edit</button>
        </div>
    </div>

    <table class="table rateChartTable table-bordered table-striped sticky-header">
        <thead>
            <tr>
                <th class='dual-column'>
                    <span class='bl'>FAT</span>
                    <span class='tr_'>SNF</span>
                </th>

                @php $column = 0; @endphp
                @for($i = (float)$shortCard->minSnf; number_format($i, 1, ".", "")<=(float)$shortCard->maxSnf; $i+=10)
                    <th>{{number_format($i, 0, ".", "")}}</th>
                    @php $column++; @endphp
                @endfor
            </tr>
            <tbody class="table-body">
                {{-- @for($f = (float)$shortCard->minFat; $f
                <=(float)$shortCard->maxFat; $f+=0.1)
                    <th>{{number_format($f, 1)}}</th>
                    @for($s = (float)$rateCard->; ) @endfor --}}
                    @php $ccount=0; @endphp
                    @foreach($rateCard as $rate)
                        @if($ccount==0)
                            <tr class="tr tr-{{number_format($rate->fatRange, 1, ".", "")}}" data-fat="{{number_format($rate->fatRange, 1, ".", "")}}">
                                <th>{{number_format($rate->fatRange, 1, ".", "")}}</th>
                        @endif
                        @php $ccount++; @endphp
                                <td class="tr @if($rate->updated_at!=null) updated_cell @endif">
                                    <input type='number' class="input" value='{{number_format($rate->amount, 2, ".", "")}}' data-fat='{{number_format($rate->fatRange, 1, ".", "")}}'
                                        data-snf='{{number_format($rate->snfRange, 0, ".", "")}}' data-fixrate="{{number_format($rate->amount, 2, ".", "")}}"
                                        onchange='changeRate(this)' data-rcid="{{$rate->id}}" disabled>
                                </td>
                        @if($ccount==$column)
                            </tr>
                            @php $ccount=0; @endphp
                        @endif
                    @endforeach
            </tbody>
    </table>
    <div class="pt-10 col-md-4 col-md-offset-4">
    </div>
</div>

{{-- @php $neededObject = array_filter( $arrayOfObjects, function ($e) { return $e->id == $searchedValue; } ); 
@endphp --}}
{{-- {{$rateCard}} --}}

<script>
    var pageUnloadFlag=false;
    
    window.rateChart = {};
    window.rateChart.data = {};
    window.rateChart.rateCardShortId = {{$shortCard->id}};

    function editRateCard(){
        $(".table-body").find("input.input").each(function(){
            // console.log($(this));
            $(this).attr("disabled", false);
        })
        $(".table-body").find("input.input").first().focus();
        $(".updateRateCardBtn").removeClass("hide");
    }

    
    function changeRate(e){

        f = $(e).data('fat');
        s = $(e).data('snf');
        ratecardId = $(e).data('rcid');
        val = $(e).val();
        update = "false";
        
        fixRate = $(e).data('fixrate');
        if(parseFloat(fixRate)!=parseFloat(val)){
            $(e).closest("td").addClass("updated_cell");
            update = "true";

            Object.defineProperty(window.rateChart.data, ratecardId, {value : {f:f, s:s, rate: val, rcid:ratecardId},
                        writable : true, enumerable : true, configurable : true});
        }else{
            $(e).closest("td").removeClass("updated_cell");
            update = "false";
            
            delete window.rateChart.data[ratecardId];
        }

        if(Object.keys(window.rateChart.data).length>0){
            pageUnloadFlag = true;
            $(".updateRateCardBtn").attr("disabled", false);
        }else{
            pageUnloadFlag = false;
            $(".updateRateCardBtn").attr("disabled", true);
        }

        // console.log(update, val, fixRate);
        console.log(window.rateChart.data);
    }

    function updateRateCard(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        loader('show');
        $.ajax({
                type: 'post',
                url: 'updateRateCardNew',
                // dataType: 'json',
                // contentType: 'application/json',
                data: window.rateChart,
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




