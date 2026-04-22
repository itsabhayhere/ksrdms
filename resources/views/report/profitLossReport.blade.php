<style>
.f-1-2em{
    font-size: 1.2em;
}
.profitClass{
    color: green;
}
.lossClass{
    color: #a50202;
}
</style>

@php
$total_saleM = number_format(($local_sale+$plant_sale), 2, ".", "");

$profitLossM = number_format((($local_sale+$plant_sale) - $milk_collection), 2, ".", "");
if($profitLossM < 0){
    $mClass = "lossClass";
}else{
    $mClass = "profitClass";
}

$profitLossP = number_format((($pro_sale) - $purchase), 2, ".", "");
if($profitLossP < 0){
    $pClass = "lossClass";
}else{
    $pClass = "profitClass";
}
@endphp

<table class="table profitloss-table tright table-bordered table-stripped">
    <thead>
        <tr>
            <th>Report</th>
            <th>from</th>
            <th>to</th>
            <th>total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Milk Collection</td>
            <td>{{request('startDate')}}</td>
            <td>{{request('endDate')}}</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($milk_collection, 2, ".", "")}} </td>
        </tr>
        <tr>
            <td>Local Sale</td>
            <td>{{request('startDate')}}</td>
            <td>{{request('endDate')}}</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($local_sale, 2, ".", "")}} </td>
        </tr>
        <tr>
            <td>Plant Sale</td>
            <td>{{request('startDate')}}</td>
            <td>{{request('endDate')}}</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{$plant_sale}} </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>Total Sale</td>
            <td class="bold" style="font-family:'DejaVu Sans', sans-serif;"><b>&#8377; {{$total_saleM}} </b></td>
        </tr>    
        <tr>
            <td></td>
            <td></td>
            <td class="bold">Profit/Loss (Milk)</td>
            <td class="f-1-2em bold {{$mClass}}" style="font-family:'DejaVu Sans', sans-serif;"><b>&#8377; {{$profitLossM}} </b></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td>Product Purchase</td>
            <td>{{request('startDate')}}</td>
            <td>{{request('endDate')}}</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($purchase, 2, ".", "")}} </td>
        </tr>
        <tr>
            <td>Product Sale</td>
            <td>{{request('startDate')}}</td>
            <td>{{request('endDate')}}</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($pro_sale, 2, ".", "")}} </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td class="bold">Profit/Loss (Products)</td>
            <td class="f-1-2em bold {{$pClass}}" style="font-family:'DejaVu Sans', sans-serif;"><b>&#8377; {{number_format($profitLossP, 2, ".", "")}} </b></td>
        </tr>

        <tr>
            <td>Expenses</td>
            <td>{{request('startDate')}}</td>
            <td>{{request('endDate')}}</td>
            <td style="font-family:'DejaVu Sans', sans-serif;"> &#8377; {{number_format($expense, 2, ".", "")}} </td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    
@php 
$netPro = number_format($profitLossM+$profitLossP-$expense, 2, ".", "");
    if($netPro < 0){
        $pClass = "lossClass";
    }else{
        $pClass = "profitClass";
    }
@endphp
    </tbody>
    <tfoot>
        <tr class="tr-bold">
            <td></td>
            <td></td>
            <td>Net Profit </td>
            <td class="f-1-2em bold {{$pClass}}" style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{$netPro}}</td>
        </tr>
    </tfoot>
</table>