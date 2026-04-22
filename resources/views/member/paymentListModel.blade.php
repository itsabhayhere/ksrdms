
        @php 
        // $total = 0;
        // $avFat = 0;
        // $avSnf = 0;
        // $avQty = 0;
        $i     = 0;
    @endphp
    
    @foreach ($balSheet as $d)

    @php
        $ledger = DB::table('ledger')->where("id", $d->srcDest)->get()->first();
        if($ledger == (null||false)){
            $name = "";
        }else{
            switch($ledger->userType){
                case "1":{
                    if($d->colMan == (null || "")){
                        $srcDest = DB::table("dairy_info")->where("ledgerId", $d->srcDest)->get()->first();
                        $name    = $srcDest->dairyName;
                    }else
                        $name = $d->colMan;
                    
                    break;
                }
                case "2":{
                    $srcDest = DB::table("customer")->where("ledgerId", $d->srcDest)->get()->first();
                    $name    = $srcDest->customerName;
                    break;
                }   
                case "3":{
                    $srcDest = DB::table("suppliers")->where("ledgerId", $d->srcDest)->get()->first();
                    $name    = $srcDest->supplierFirmName;
                    break;
                }
                case "4":{
                    $srcDest = DB::table("member_personal_info")->where("ledgerId", $d->srcDest)->get()->first();
                    $name    = $srcDest->memberPersonalName;
                    break;
                }
                case "5":{
                    $srcDest = DB::table("suppliers")->where("ledgerId", $d->srcDest)->get()->first();
                    $name    = $srcDest->supplierFirmName;
                    break;
                }
                case "6":{
                    $srcDest = DB::table("milk_plants")->where("ledgerId", $d->srcDest)->get()->first();
                    $name    = $srcDest->plantName;
                    break;
                }
                default:
                    $name = "";            
            }
        }

        
        $i++;
        // $total += $d->amount;
        // $avFat += $d->fat;
        // $avSnf += $d->snf;
        // $avQty += $d->milkQuality;
    @endphp
    <tr>
        {{-- <td>{{$i}}</td> --}}
        <td>{{ date("d-m-Y H:i:s", strtotime($d->created_at)) }}</td>
        <td>{{ $d->remark }}</td>
        <td>{{ $name }}</td>
        {{-- <td>{{ $d->id }}</td> --}}
        <td>@if($d->amountType == "debit") &#8377; {{$d->finalAmount}} @endif</td>
        <td>@if($d->amountType == "credit") &#8377; {{$d->finalAmount}} @endif</td>
        <td>@if($d->amountType == "cash") &#8377; {{$d->finalAmount}} @endif</td>
        <td>Success</td>
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
