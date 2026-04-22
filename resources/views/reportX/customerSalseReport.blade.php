<table class="table custSalse-table tright table-bordered table-stripped">
    <thead>
        <tr>
            <th>Customer Code</th>
            <th>Date</th>
            <th>Activity</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Cash</th>
        </tr>
    </thead>
    <tbody>

    @php 
    // $total = 0;
    // $avFat = 0;
    // $avSnf = 0;
    // $avQty = 0;
    $i     = 0;
    if($ubal->openingBalanceType == "credit"){
        $balType = "Cr.";
    }else{
        $balType = "Dr.";
    }
@endphp


    <tr>
        <td>{{ $cust->customerCode }}</td>
        <td>{{ date("d-m-Y", strtotime($opb->created_at)) }}</td>
        <td>Opening Balance</td>
        @if($opb->amountType == "credit")
            <td>{{ $opb->finalAmount}}</td>
        @else
            <td>-</td>
        @endif
        @if($opb->amountType == "debit")
            <td>{{ $opb->finalAmount}}</td>
        @else
            <td>-</td>
        @endif
        <td>-</td>
    </tr>


@foreach ($report as $d)

@php
    if($d->productType == "cowMilk"){
        $d->remark = $d->remark." Cow Milk ( &#8377; ". $d->productPricePerUnit. "/ltr)";
    }elseif($d->productType == "buffaloMilk"){
        $d->remark = $d->remark." Buffalo Milk ( &#8377; ". $d->productPricePerUnit. "/ltr)";
    }else{
        if($d->productType != ("" || null)){
            $pro = DB::table("products")->where(["dairyId" => $d->dairyId, "productCode" => $d->productType])->get()->first();
            if($pro == null||false){
                $d->remark = $d->remark. " ". $d->productType . " ( &#8377; ". $d->productPricePerUnit. "/ltr)";
            }else{
                $d->remark = $d->remark." ". $pro->productName . " ( &#8377; ". $d->productPricePerUnit. "/ltr)";
            }
        }
    }
    $i++;
    if($d->partyCode == $d->dairyId."C1"){
        $d->partyCode="CASH Party";
    }

    $dbt = number_format((int)$d->finalAmount-(int)$d->paidAmount, 2, ".", "");
@endphp
    <tr>
        <td>{{$d->partyCode}}</td>
        <td>{{ date("d-m-Y", strtotime($d->date)) }}</td>
        <td style="font-family:'DejaVu Sans', sans-serif;">{{ $d->remark }}</td>
        @if($d->type == "credit")
            <td>&#8377; {{$dbt}}</td>
        @else
            <td>-</td>
        @endif
        @if($d->type == "debit")
            <td>&#8377; {{$dbt}}</td>
        @else
            <td>-</td>
        @endif

        {{-- <td>-</td>
        <td style="font-family:'DejaVu Sans', sans-serif;">@if($dbt > 0) &#8377; {{$dbt}} @else - @endif</td> --}}

        <td style="font-family:'DejaVu Sans', sans-serif;">@if((int)$d->paidAmount > 0) &#8377; {{number_format((int)$d->paidAmount, 2, ".", "")}} @else - @endif</td>       {{-- cash from customer account --}}
    </tr>
@endforeach

</tbody>
@if($i>0)
<tfoot>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <th style="text-align:right">Current Balance:</th>
        <th style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format((int)$ubal->openingBalance, 2 ,".", ""). " ". $balType}}</th>
    </tr>
</tfoot>
@endif

</table>
