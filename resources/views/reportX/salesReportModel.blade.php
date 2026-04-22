<table class="table sales-table tright table-bordered" >
    <thead>
        <tr>
            <th>Party Code</th>
            <th style="width: 130px;">Party Name</th>
            <th>Sale Type</th>
            <th style="width: 70px;">Sale Date</th>
            <th>Product</th>
            <th>Product Quantity</th>
            <th>Product Price Per Unit</th>
            <th>Payment Type</th>
            <th>Discount</th>
            <th class="txtr">Amount</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totala = 0;
            $qty  = 0; 
            $i = 0;
            $cash = 0;
            $credit = 0;
            $dis = 0;
        @endphp
        @foreach($data as $d)
            @php 
                if(strtolower($d->amountType) == "cash")
                    $cash += $d->finalAmount;

                if(strtolower($d->amountType) == "credit")
                    $credit += $d->finalAmount;

                $i++;
                $prod = ($d->productType=="cowMilk")?"Cow Milk":(($d->productType=="buffaloMilk")?"Buffalo Milk":"-");
            
                if($prod == "-"){
                    $p = DB::table('products')->where('productCode', $d->productType)->get()->first();
                    if($p==(null||false||'')){
                        $prod = $d->productType;
                    }else{
                        $prod = $p->productName;
                    }
                }
                $totala += (float)$d->finalAmount;
                $qty += (float)$d->productQuantity;
                $dis += (float)$d->discount;
            @endphp
            <tr>
                <td>{{$d->partyCode}}</td>
                <td style="width: 130px;">{{$d->partyName}}</td>
                <td>{{$d->saleType}}</td>
                <td style="width: 70px;">{{date("d-m-Y", strtotime($d->saleDate))}}</td>
                <td>{{$prod}}</td>
                <td>{{$d->productQuantity}}</td>
                <td>@if($d->productPricePerUnit == (0||"")) - @else {{$d->productPricePerUnit}} @endif</td>
                <td>{{$d->amountType}}</td>
                <td>{{$d->discount}}</td>
                <td class="txtr" style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{$d->finalAmount}}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if($i>0)
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td>Total: </td>
            <td>
                {{-- Credit total: &#8377; {{$credit}} --}}
            </td>
            <td></td>
            <td>
                {{-- Cash total: &#8377; {{$cash}} --}}
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td  style="font-family:'DejaVu Sans', sans-serif;"> &#8377; {{ $dis }}</td>
            <td  style="font-family:'DejaVu Sans', sans-serif;"> &#8377; {{$sum}}</td>
        </tr>
        @endif
    </tfoot>
</table>
