


<style>
    .table {
        vertical-align: middle;
    }

    .spanspaced span {
        padding: 0 20px 0 0;
    }
</style>

@php
$qty1 = 0;
$ft1 = 0;
$am1 = 0;
$qty2 = 0;
$ft2 = 0;
$am2 = 0;
$tam = 0;
@endphp



<table class="table cmSubsidiaryReport-table tright table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="13">
                <span style="float:left">The Sirsa District Co-operative Milk Producers Union Limited.</span>
                <span style="float:right">Period {{$from}} to {{$to}}</span>
            </th>
        </tr>
        <tr>
            <th colspan="19" class="spanspaced">
                <span>
                    Name of MPCS: {{$dairyName}}
                </span>
                <span>
                    Code: {{$dairyCode}}
                </span>
                <span>
                    Field Supervisor Name: {{ request('supervisorName') }}
                </span>
                <span>
                    Field Supervisor Mobile no.: {{ request('supervisorMobile') }}
                </span>
            </th>
        </tr>
        <tr>


            <th rowspan="2">member Code.</th>
            <th rowspan="2">Name Of Supplier</th>
            <th rowspan="2">Father/Husband Name</th>
            <th rowspan="2">Aadhar No.</th>
            @if($reportType!='Simple')
                <th rowspan="2">Parivar Pahchan Patra (Family ID).</th>
                <th rowspan="2">Family ID Income Verified.</th>
                @endif
          
            <th rowspan="2">Gen/SC/OBC.</th>
            <th rowspan="2">Mobile No.</th>

            <th rowspan="2">Name of Bank</th>
            <th rowspan="2">IFSC Code</th>
            <th rowspan="2">A/C. No.</th>
            <th colspan="3">Qty Milk Received Containing Fat 3.5% to 5.0% rate of Subsidy 4/-p.kg.('A')</th>
            <th colspan="3">Qty Milk Received Containing Fat above 5.0% rate of Subsidy 5/-p.kg.('B')</th>
            <th rowspan="2">G. Total A+B</th>
        </tr>
        <tr>
            <th>Qty</th>
            <th>Fat</th>
            <th>Amount</th>
            <th>Qty</th>
            <th>Fat</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @php $i=1; @endphp
        @foreach($data as $d)
        @php
        $qty1 += (float)$d->qty35_50;
        $ft1 += (float)$d->fat35_50;
        $am1 += (float)$d->amount35_50;


        $qty2 += (float)$d->qty50__;
        $ft2 += (float)$d->fat50__;
        $am2 += (float)$d->amount50__;

        @endphp


        <tr>
            <!-- <td>{{$dairyId}}</td> -->
            <td>{{ $d->memberCode }}</td>
            <td>
                {{$d->memberName}}
            </td>
            <td>
                {{$d->FName}}
            </td>
            <td>
                {{' '.$d->adharNo}}
            </td>
            @if($reportType!='Simple')
                    <td >{{$d->pppid}}</td>
                    <td >{{$d->Verified_income}}</td>
                   @endif
           

            <td>
                {{$d->category}}
            </td>
            <td>
                {{$d->mobileNo}}
            </td>
            <td>
                {{$d->bankName}}
            </td>
            <td>
                {{$d->ifscCode}}
            </td>
            <td>  <?php echo "'" . $d->accNo . "'"; ?> </td>
            <td>
                {{number_format($d->qty35_50, 1, ".", "")}}
            </td>
            <td>
                {{number_format($d->fat35_50, 1, ".", "")}}
            </td>
            <td>
                {{number_format($d->amount35_50, 2, ".", "")}}
            </td>
            <td>
                {{number_format($d->qty50__, 1, ".", "")}}
            </td>
            <td>
                {{number_format($d->fat50__, 1, ".", "")}}
            </td>
            <td>
                {{number_format($d->amount50__, 2, ".", "")}}
            </td>
            <td>
                {{number_format($d->amount35_50 + $d->amount50__, 2, ".", "")}}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @if($reportType!='Simple')
                    <td ></td>
                    <td ></td>
                   @endif
            <td>{{number_format($qty1, 1, ".", "")}}</td>
            <td>{{number_format($ft1, 1, ".", "")}}</td>
            <td>{{number_format($am1, 2, ".", "")}}</td>
            <td>{{number_format($qty2, 1, ".", "")}}</td>
            <td>{{number_format($ft2, 1, ".", "")}}</td>
            <td>{{number_format($am2, 2, ".", "")}}</td>
            <td>{{number_format($am1+$am2, 2, ".", "")}}</td>

        </tr>
    </tfoot>
</table>