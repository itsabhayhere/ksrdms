
        @php 
        // $total = 0;
        // $avFat = 0;
        // $avSnf = 0;
        // $avQty = 0;
        $i     = 0;
    @endphp
    
    @foreach ($purchaseHistory as $d)

    @php
        $p = ucfirst($d->productType);
        if($d->productType != "cowMilk" && $d->productType != "buffaloMilk"){
            $prod = DB::table("products")->where("productCode", $d->productType)->get()->first();
            if($prod!=(null||false||"")){
                $p = $prod->productName;
            }
        }

        $i++; 
        // $total += $d->amount;
        // $avFat += $d->fat;
        // $avSnf += $d->snf;
        // $avQty += $d->milkQuality;
    @endphp
    <tr>

        <td>{{ date("d-m-Y", strtotime($d->saleDate)) }}</td>
        <td>{{ $p }}</td>
        <td>{{ $d->productQuantity }}</td>
        <td> &#8377; {{ $d->productPricePerUnit }}</td>
        <td>{{ ucfirst($d->amountType) }}</td>
        <td> &#8377; {{ $d->amount }}</td>
        <td> &#8377; {{ $d->discount }}</td>
        <td> &#8377; {{ $d->finalAmount }}</td>
        <td> &#8377; {{ $d->paidAmount }}</td>
    </tr>
    @endforeach
    @if($i>0)
    <tr style="font-weight:bolder;background: #e4e4e4;">
        {{-- <td>Total</td>
        <td></td>
        <td></td>
        <td>{{ $avQty }}</td>
        <td>{{ number_format($avFat/$i, 2) }} </td>
        <td>{{ number_format($avSnf/$i, 2) }} </td>
        <td></td>
        <td>{{ $total }}</td> --}}
    </tr>
    @endif
