<table class="table memStatement3-table tright table-bordered table-stripped">
    <thead>
        <tr>
            <th>Member Code</th>
            <th>Date</th>
            <th>Activity</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Cash</th>
            <th>Balance</th>
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
            <td></td>
<!--             for new current balance
 -->       <?php /*     <td>
                  @if($getOpeningCurrentBalance)
                {{$getOpeningCurrentBalance->currentBalance}}
               
                @endif
              

 </td> */ ?>
        </tr>
<?php // print_r($balSheet); exit;?> 



 @if(count($balSheet['milkCollection']) > 0)
    @foreach($balSheet['milkCollection'] as $d)
     @php
            $getMilkCurrentBalance= DB::table("balance_sheet")
            ->where("transactionId",$d->id)
            ->select('currentBalance')
            ->orderBy('id','desc')
            ->get()->first();
   @endphp         
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{ strtotime($d->created_at) }}">{{ date('d-m-Y',strtotime($d->date)) }}</td>
            <td>Milk Collection</td>
            <td>{{ $d->amount}}</td>
            <td>-</td>
            <td>-</td>
            <td> 
                @if($getMilkCurrentBalance)
                   {{$getMilkCurrentBalance->currentBalance}}
                @endif
            </td>
        </tr>
@endforeach
        @endif


    @if($balSheet['localsaleFinal'])
    @foreach($balSheet['localsaleFinal'] as $d)
        @php
             $getLocalSaleBalance= DB::table("balance_sheet")
            ->where("transactionId",$d->id)
            ->select('currentBalance')
            ->get()->first();
        @endphp
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($d->created_at)}}"> {{$d->saleDate}}</td>
            <td>Local Sale</td>
            <td>-</td>
            <td> {{number_format($d->finalAmount-$d->paidAmount, 2, ".", "") }}</td>
            <td>{{number_format($d->paidAmount, 2, ".", "") }}</td>
            <td>
                @if($getLocalSaleBalance)
                {{$getLocalSaleBalance->currentBalance}}
               
                @endif
           </td> 
        </tr>
    @endforeach
    @endif

    @if(count($balSheet['productsale']) > 0) 


        @foreach ($balSheet['productsale'] as $item) @php
        $i=0;

         $pro = DB::table("products")->where(["productCode"
        => $item->productType])->get()->first(); if($pro == (null||false)){ $pro->productName = $item->productCode; } 
        $getProductCurrentBalance = DB::table("balance_sheet")
            ->where(['ledgerId' => $item->ledgerId, "status" => "true"])
            ->where("transactionType", 'sales')
            ->where("transactionId",$item->id)
            ->select('currentBalance')
            ->get()->first();

@endphp
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($item->created_at)}}">{{ date("d-m-Y", strtotime($item->saleDate)) }}</td>
            <td><b>{{ $item->productQuantity}} x </b>{{ $pro->productName . " (Rate: ".$item->productPricePerUnit.", discount:
                ".$item->discount.")"}}</td>
            <td>-</td>
            <td>{{ number_format(($item->finalAmount-$item->paidAmount), 2, ".", "")}}</td>
            <td>{{ number_format($item->paidAmount, 2, ".", "") }}</td>
              <td> @if($getProductCurrentBalance)
                {{$getProductCurrentBalance->currentBalance}}
               
                @endif
            </td> 
        </tr>
        @php $i++; @endphp

        @endforeach

@endif





 @if(count($balSheet['advance']) > 0) 
    @foreach ($balSheet['advance'] as $item)
        @php
            $getAdvanceCurrentBalance = DB::table("balance_sheet")
            ->where(['ledgerId' => $item->ledgerId, "status" => "true"])
             ->where("transactionId",$item->id)
            ->select('currentBalance')
            ->get()->first();
        @endphp
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($item->date)}}">{{ date("d-m-Y", strtotime($item->date)) }} </td>
            <td>{{ "Advance: ".$item->remark }}</td>
            <td>-</td>
            <td>{{ number_format($item->amount, 2, ".", "")}}</td>
            <td></td>
            <td>
                 @if($getAdvanceCurrentBalance)
                {{$getAdvanceCurrentBalance->currentBalance}}
               
                @endif
              

            </td> 
        </tr>
    @endforeach 
@endif 

        @if(count($balSheet['credit']) > 0) 
            @foreach ($balSheet['credit'] as $item)
                @php
                    $getCreditCurrentBalance = DB::table("balance_sheet")
                    ->where(['ledgerId' => $item->ledgerId, "status" => "true"])
                   
                    ->where("transactionId",$item->id)
                    ->select('currentBalance')
                    ->get()->first();
                @endphp
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{strtotime($item->date)}}">{{ date("d-m-Y", strtotime($item->date)) }}</td>
            <td>{{ "Credit: ".$item->remark }}</td>
            <td>{{ number_format($item->amount, 2, ".", "")}}</td>
            <td>-</td>
            <td>-</td>
		   <td> @if($getCreditCurrentBalance)
                {{$getCreditCurrentBalance->currentBalance}}
               
                @endif
               

            </td>
 
        </tr>
        @endforeach @endif  
    </tbody>

    <tfoot>
        @if($i>0)
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td></td>
            <td></td>
            <td> </td>
            <td>Current Balance</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($ubal->openingBalance, 2 ,".", ""). " ". $balType}}</td>
            <td colspan="2"> </td>
        </tr>
        @endif
    </tfoot>
</table>