<table class="table memStatement-table tright table-bordered table-stripped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Activity</th>
                <th>Source/Destination</th>
                {{-- <th>Txn ID</th> --}}
                <th>Debit</th>
                <th>Credit</th>
                <th>Cash</th>
<!--                 <th>Balance</th>
 -->            </tr>
        </thead>
        <tbody>

        @php 
        $balance = 0;
        $i     = 0;
        if($ubal->openingBalanceType == "credit"){
            $balType = "Cr.";
        }else{
            $balType = "Dr.";
        }
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

        if($d->amountType == "debit"){
            $balance -= (float)$d->finalAmount;
        }else if($d->amountType == "credit"){
            $balance += (float)$d->finalAmount;
        }
        if($balance < 0){
            $bType = "Dr.";
        }else{
            $bType = "Cr.";
        }

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
<!--         <td> &#8377; {{$d->currentBalance}} <!-abs($balance) ." ".$bType}} -></td>
 -->    </tr>
    @endforeach
    </tbody>

    <tfoot>
        @if($i>0)
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Current Balance</td>
            <td>&#8377; {{number_format($ubal->openingBalance, 2 ,".", ""). " ". $balType}}</td>
        </tr>
        @endif
    </tfoot>
</table>