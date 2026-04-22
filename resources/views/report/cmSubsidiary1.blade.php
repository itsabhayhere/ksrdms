<style>
    .table {
        vertical-align: middle;
        width: 100%;
    }

    .spanspaced span {
        padding: 0 20px 0 0;
    }

    .cmSubsidiaryReport-table th, .cmSubsidiaryReport-table td {
        text-align: center;
        vertical-align: middle;
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
$totqty = 0;
$totft = 0;
$totam = 0;
$bothqty = 0;
$bothamount = 0;
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
            <th colspan="15" class="spanspaced">
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
            <th>Member Code</th>
            <th>Name Of Supplier</th>
            <th>Father/Husband Name</th>
            <th>Aadhar No.</th>
            @if($reportType != 'Simple')
                <th>Parivar Pahchan Patra (Family ID)</th>
                <th>Family ID Income Verified</th>
            @endif
            <th>Gen/SC/OBC</th>
            <th>Mobile No.</th>
            <th>Name of Bank</th>
            <th>IFSC Code</th>
            <th>A/C No.</th>
            <th>Qty</th>
            <th>G. Total A+B</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $d)
            @php
            $qty1 += (float)$d->qty35_50;
            $ft1 += (float)$d->fat35_50;
            $am1 += (float)$d->amount35_50;

            $qty2 += (float)$d->qty50__;
            $ft2 += (float)$d->fat50__;
            $am2 += (float)$d->amount50__;

            $bothqty = (float)$d->qty35_50 + (float)$d->qty50__;
            $bothamount = (float)$d->amount35_50 + (float)$d->amount50__;
            $totqty = $qty1 + $qty2;
            $totft = $ft1 + $ft2;
            $totam = $am1 + $am2;
            @endphp

            <tr>
                <td>{{ $d->memberCode }}</td>
                <td>{{ $d->memberName }}</td>
                <td>{{ $d->FName }}</td>
                <td>{{ ' '.$d->adharNo}}</td>
                @if($reportType != 'Simple')
                    <td>{{ $d->pppid }}</td>
                    <td>{{ $d->Verified_income }}</td>
                @endif
                <td>{{ $d->category }}</td>
                <td>{{ $d->mobileNo }}</td>
                <td>{{ $d->bankName }}</td>
                <td>{{ $d->ifscCode }}</td>
                <td>  <?php echo "'" . $d->accNo . "'"; ?> </td>
                <td>{{ number_format($bothqty, 1, ".", "") }}</td>
                <td>{{ number_format($bothamount, 2, ".", "") }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td></td>
            <td></td>
            <td></td>
            @if($reportType != 'Simple')
                <td></td>
                <td></td>
            @endif
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ number_format($totqty, 1, ".", "") }}</td>
            <td>{{ number_format($totam, 2, ".", "") }}</td>
        </tr>
    </tfoot>
</table>
