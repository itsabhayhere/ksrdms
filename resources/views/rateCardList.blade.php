@extends('theme.default') 
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
        <div class="clearfix">
            <div class="fl">
                <h3>Rate Card</h3>
            </div>
            <div class="fr">
                <a class="btn btn-primary" href="rateCardNew">Add Rate Card</a>
            </div>
        </div>
        <hr class="mt-10"/>
    </div>


    <div class="">
        <div class="pt-20"></div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Rate Card Type</th>
                    <th>Min Fat</th>
                    <th>Max Fat</th>
                    <th>Min SNF</th>
                    <th>Max SNF</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php $count=0; foreach($rateCardShort as $rateCard): $count++;
                    if($rateCard->id == $defaultRateCard->rateCardIdForCow) $defaultCow="defaultRateCardCow";else $defaultCow="";
                    if($rateCard->id == $defaultRateCard->rateCardIdForBuffalo) $defaultBuff="defaultRateCardBuff";else $defaultBuff="";
                    if($defaultCow!="" && $defaultBuff!=""){$defaultCowBuff = "defaultCowBuff"; $defaultBuff=""; $defaultCow="";}else $defaultCowBuff = "";
                @endphp
                    <tr class="tr-clickable" id="ratecard{{$rateCard->id}}">
                        <td class="td-clickable {{$defaultCow." ".$defaultBuff." ".$defaultCowBuff}}" onclick="showRateChart(this)" data-shortid="{{$rateCard->id}}">
                            {{-- {{$count}} --}}
                        </td>
                        <td class="td-clickable" onclick="showRateChart(this)" data-shortid="{{$rateCard->id}}">
                            {{strtoupper($rateCard->rateCardType)}}
                        </td>
                        <td class="td-clickable" onclick="showRateChart(this)" data-shortid="{{$rateCard->id}}">
                            {{$rateCard->minFat}}
                        </td>
                        <td class="td-clickable" onclick="showRateChart(this)" data-shortid="{{$rateCard->id}}">
                            {{$rateCard->maxFat}}
                        </td>
                        <td class="td-clickable" onclick="showRateChart(this)" data-shortid="{{$rateCard->id}}">
                            @if(strtolower($rateCard->rateCardType) == 'fat' ) - @else {{$rateCard->minSnf}} @endif
                        </td>
                        <td class="td-clickable" onclick="showRateChart(this)" data-shortid="{{$rateCard->id}}">
                            @if(strtolower($rateCard->rateCardType) == 'fat' ) - @else {{$rateCard->maxSnf}} @endif
                        </td>
                        <td class="td-clickable" onclick="showRateChart(this)" data-shortid="{{$rateCard->id}}">
                            {{$rateCard->description}}
                        </td>
                        <td>
                            @if($defaultCow!="defaultRateCardCow" && $defaultBuff!="defaultRateCardBuff" && $defaultCowBuff!="defaultCowBuff")
                            <a href="#" class="icon-16" onclick="deleteRateCard(this)" data-shortid="{{$rateCard->id}}" >
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($count==0)
            <div class="no-data">
                <i class="fa fa-warning"></i> No Rate Card found.
            </div>
            @endif
            <div class="pt-10 col-md-4 col-md-offset-4">

                {{-- <button class="btn btn-primary btn-block" onclick="saveRateChart()">Save Rate Card</button> --}}
            </div>
        </div>

    </div>

    <div class="cmodel">
        <div class="close">X</div>
        <div class="cmodel-body"></div>
    </div>
@endsection
 
@section('scripts')
<script>
    function showRateChart(e){
        shortid = $(e).data('shortid');
        $('.cmodel').fadeIn();

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        loader('show');
        $.ajax({
                type: 'post',
                url: 'getRateCardList',
                // dataType: 'json',
                // contentType: 'application/json',
                data: {shortid: shortid},
                success: function(res) {
                    loader('hide');
                    // console.log(res);
                    $(".cmodel-body").html(res);
                },
                error: function(res){
                    loader('hide');
                    console.log(res);   
                }
            });

        }

    function confirmDelete(e){ 
        
        shortid = $(e).data('shortid');

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        loader('show');
        $.ajax({
                type: 'post',
                url: 'deleteRateCard',
                // dataType: 'json',
                // contentType: 'application/json',
                data: {shortid: shortid},
                success: function(res) {
                    loader('hide');
                    if(res.error){
                        $.alert('There were a problem while deleting your Rate Card, Please try again.');
                    }else{
                        $.alert(res.msg);
                        // $(".flash-alert").removeClass("hide").addClass('alert-success');
                        // $(".flash-msg").html();
                        $("#ratecard"+shortid).slideUp();
                    }
                    console.log(res);
                },
                error: function(res){
                    loader('hide');
                    $.alert('An error has occured, Please try again.');
                    console.log(res);   
                }
            });

    }
    
    $(".cmodel .close").on('click', function(){
        if(pageUnloadFlag){
            if(confirm("Are you sure? you are about to cancel changes you made on this page.")){
                $(this).parent().fadeOut();                
            }else{
                return;
            }
        }else{
            $(this).parent().fadeOut();
        }
    })

    function deleteRateCard(e){
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure? You are about to delete a Ratecard.',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm: function () {
                    confirmDelete(e);
                },
                cancel: function () {
                    return true;
                }
                // ,
                // somethingElse: {
                //     text: 'Something else',
                //     btnClass: 'btn-blue',
                //     keys: ['enter', 'shift'],
                //     action: function(){
                //         $.alert('Something else?');
                //     }
                // }
            }
        });
    }

</script>
@endsection