<table class="table memStatement-table tright table-bordered table-stripped">
    <thead>
        <tr>
            <th>Member Code</th>
            <th>Date</th>
            <th>Activity</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Cash</th>
        </tr>
    </thead>
    <tbody>

        @php $balance = 0; $i = 0; if($ubal->openingBalanceType == "credit"){ $balType = "Cr."; }else{ $balType = "Dr."; } 

@endphp

        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($opb->created_at)}}">{{ date("d-m-Y", strtotime($opb->created_at)) }}</td>
            <td>Opening Balance</td>
            @if($opb->amountType == "credit")
                <td>{{ $opb->finalAmount }}</td>
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
 

        @foreach ($balSheet as $key => $d) @if($d['milkCollection'] > 0)
      @php
             $splitrange=explode(' to ',$d['range']);
            $milkCollection = DB::table("daily_transactions")
                ->where(['dairyId' => $mem->dairyId, 'memberCode' => $mem->memberPersonalCode, "status" => "true"])
                ->whereBetween("date", [date("Y-m-d", strtotime(trim($splitrange[0]))), date("Y-m-d", strtotime(trim($splitrange[1])))])
                ->select('id')
                ->orderBy('id','desc')
                ->get()->first();
            $trimsplitrang = trim($splitrange[1]).'00:00:00';
   @endphp         
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($trimsplitrang)}}">{{ $d['range'] }}</td>
            <td>Milk Collection</td>
            <td>{{ $d['milkCollection']}}</td>
            <td>-</td>
            <td>-</td>
        </tr>
        @endif @if($d['localsaleFinal'] > 0)
        @php
            $splitrange=explode(' to ',$d['range']);
        @endphp
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime(trim($splitrange[1]))}}">{{ $d['range'] }} </td>
            <td>Local Sale</td>
            <td>-</td>
            <td>{{ number_format($d['localsaleFinal']-$d['localsalePaid'], 2, ".", "") }}</td>
            <td>{{ number_format($d["localsalePaid"], 2, ".", "") }}</td>
        </tr>
        @endif @if(count($d['productsale']) > 0) 
        @foreach ($d['productsale'] as $item) @php
        $i=0;

         $pro = DB::table("products")->where(["productCode"
        => $item->productType])->get()->first(); if($pro == (null||false)){ $pro->productName = $item->productCode; } 

@endphp
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($item->saleDate)}}">{{ date("d-m-Y", strtotime($item->saleDate)) }} </td>
            <td><b>{{ $item->productQuantity}} x </b>{{ $pro->productName . " (Rate: ".$item->productPricePerUnit.", discount:
                ".$item->discount.")"}}</td>
            <td>-</td>
            <td>{{ number_format(($item->finalAmount-$item->paidAmount), 2, ".", "")}}</td>
            <td>{{ number_format($item->paidAmount, 2, ".", "") }}</td>
        </tr>
        @endforeach  @endif @if(count($d['advance']) > 0) @foreach ($d['advance'] as $item)
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($item->date)}}">{{ date("d-m-Y", strtotime($item->date)) }} </td>
            <td>{{ "Advance: ".$item->remark }}</td>
            <td>-</td>
            <td>{{ number_format($item->amount, 2, ".", "")}}</td>
            <td></td>
        </tr>
        @endforeach @endif @if(count($d['credit']) > 0) @foreach ($d['credit'] as $item)
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($item->date)}}">{{ date("d-m-Y", strtotime($item->date)) }}</td>
            <td>{{ "Credit: ".$item->remark }}</td>
            <td>{{ number_format($item->amount, 2, ".", "")}}</td>
            <td>-</td>
            <td>-</td>
        </tr>
        @endforeach @endif @php $i++; 
@endphp @endforeach
    </tbody>

    <tfoot>
        @if($i>0)
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td></td>
            <td></td>
            <td> </td>
            <td>Current Balance</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($ubal->openingBalance, 2 ,".", ""). " ". $balType}}</td>
            <td> </td>
        </tr>
        @endif
    </tfoot>
</table>