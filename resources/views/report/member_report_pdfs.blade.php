<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Member Passbook Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 14pt; font-weight: bold; }
        .subtitle { font-size: 12pt; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #4472C4; color: white; text-align: center; padding: 5px; border: 1px solid #ddd; }
        td { padding: 5px; border: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #D9E1F2; }
        .section-title { font-weight: bold; margin-top: 20px; margin-bottom: 10px; text-decoration: underline; }
        .page-break { page-break-after: always; }
        .logo { max-width: 150px; max-height: 80px; }
    </style>
    <style>
    @page {
        size: A4 portrait;
        margin: 20mm 15mm 20mm 15mm;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
        margin: 0;
        padding: 0;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .title {
        font-size: 14pt;
        font-weight: bold;
    }

    .subtitle {
        font-size: 12pt;
        font-weight: bold;
    }

    .section-title {
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 10px;
        text-decoration: underline;
    }

    .logo {
        max-width: 150px;
        max-height: 80px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        table-layout: fixed;
        word-wrap: break-word;
    }

    th, td {
        padding: 5px;
        border: 1px solid #ddd;
        height: 24px;
        vertical-align: middle;
        font-size: 9pt;
        word-break: break-word;
    }

    th {
        background-color: #4472C4;
        color: white;
        text-align: center;
        height: 28px;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .total-row {
        font-weight: bold;
        background-color: #D9E1F2;
    }

    .grand-total-row {
        font-weight: bold;
        background-color: #B4C6E7;
    }

    .page-break {
        page-break-after: always;
    }
</style>

</head>
<body>
    <div class="header">
        <div class="title">{{ $dairyInfo->dairyName ?? 'Dairy Management System' }}</div>
        <div class="subtitle">Member Passbook Report</div>
        <div>From {{ isset($startDate) && !empty($startDate) ? date('d-m-Y', strtotime($startDate)) : 'N/A' }} to {{ isset($endDate) && !empty($endDate) ? date('d-m-Y', strtotime($endDate)) : 'N/A' }}</div>
        <div>Member: {{ $member->memberPersonalCode ?? 'N/A' }} - {{ $member->memberPersonalName ?? 'N/A' }}</div>
        <div>Generated: {{ date('d-m-Y H:i') }}</div>
    </div>
    
 

<div class="section-title">Milk Transactions</div>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>M Qty</th>
            <th>M Fat</th>
            <th>M SNF</th>
            <th>M Amt</th>
            <th>M Rate</th>
            <th>E Qty</th>
            <th>E Fat</th>
            <th>E SNF</th>
            <th>E Amt</th>
            <th>E Rate</th>
            <th>Total Qty</th>
            <th>Total Amt</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalMorningQty = 0;
            $totalMorningFat = 0;
            $totalMorningSnf = 0;

            $totalEveningQty = 0;
            $totalEveningFat = 0;
            $totalEveningSnf = 0;

            $totalMorningAmt = 0;
            $totalEveningAmt = 0;

            $totalCombinedQty = 0;
            $totalCombinedAmt = 0;

            $safeDivide = function($numerator, $denominator) {
                return $denominator != 0 ? $numerator / $denominator : 0;
            };
        @endphp

        @foreach($transactions as $date => $shifts)
            @php
                $morning = $shifts['morning'][0] ?? null;
                $evening = $shifts['evening'][0] ?? null;

                $morningQty = floatval($morning->milkQuality ?? 0);
                $eveningQty = floatval($evening->milkQuality ?? 0);
                $morningAmt = floatval($morning->amount ?? 0);
                $eveningAmt = floatval($evening->amount ?? 0);

                $morningFat = floatval($morning->fat ?? 0);
                $morningSnf = floatval($morning->snf ?? 0);
                $eveningFat = floatval($evening->fat ?? 0);
                $eveningSnf = floatval($evening->snf ?? 0);

                $morningRate = $safeDivide($morningAmt, $morningQty);
                $eveningRate = $safeDivide($eveningAmt, $eveningQty);

                $rowTotalQty = $morningQty + $eveningQty;
                $rowTotalAmt = $morningAmt + $eveningAmt;

                $totalMorningQty += $morningQty;
                $totalMorningFat += $morningFat;
                $totalMorningSnf += $morningSnf;
                $totalMorningAmt += $morningAmt;

                $totalEveningQty += $eveningQty;
                $totalEveningFat += $eveningFat;
                $totalEveningSnf += $eveningSnf;
                $totalEveningAmt += $eveningAmt;

                $totalCombinedQty += $rowTotalQty;
                $totalCombinedAmt += $rowTotalAmt;
            @endphp
            <tr>
                <td>{{ date('d-m-Y', strtotime($date)) }}</td>
                <td class="text-right">{{ number_format($morningQty, 2) }}</td>
                <td class="text-right">{{ number_format($morningFat, 1) }}</td>
                <td class="text-right">{{ number_format($morningSnf, 1) }}</td>
                <td class="text-right">{{ number_format($morningAmt, 2) }}</td>
                <td class="text-right">{{ number_format($morningRate, 2) }}</td>

                <td class="text-right">{{ number_format($eveningQty, 2) }}</td>
                <td class="text-right">{{ number_format($eveningFat, 1) }}</td>
                <td class="text-right">{{ number_format($eveningSnf, 1) }}</td>
                <td class="text-right">{{ number_format($eveningAmt, 2) }}</td>
                <td class="text-right">{{ number_format($eveningRate, 2) }}</td>

                <td class="text-right"><strong>{{ number_format($rowTotalQty, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($rowTotalAmt, 2) }}</strong></td>
            </tr>
        @endforeach

        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-right"><strong>{{ number_format($totalMorningQty, 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalMorningFat, 1) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalMorningSnf, 1) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalMorningAmt, 2) }}</strong></td>
            <td></td>

            <td class="text-right"><strong>{{ number_format($totalEveningQty, 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalEveningFat, 1) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalEveningSnf, 1) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalEveningAmt, 2) }}</strong></td>
            <td></td>

            <td class="text-right"><strong>{{ number_format($totalCombinedQty, 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalCombinedAmt, 2) }}</strong></td>
        </tr>
<tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-right"><strong>Fatkg</strong></td>
            <td class="text-right"><strong>{{$fatkg}}</strong></td>
            <td class="text-right"><strong>Snfkg</strong></td>
            <td class="text-right"><strong>{{$snfkg}}</strong></td>
            <td></td>

            <td class="text-right"><strong></strong></td>
            <td class="text-right"><strong></strong></td>
            <td class="text-right"><strong></strong></td>
            <td class="text-right"><strong></strong></td>
            <td></td>

            <td class="text-right"><strong></strong></td>
            <td class="text-right"><strong></strong></td>
        </tr>
        <tr class="grand-total-row">
            <td colspan="11"><strong>Grand Total Amount</strong></td>
            <td colspan="2" class="text-right"><strong>{{ number_format($totalCombinedAmt, 2) }}</strong></td>
        </tr>
    </tbody>
</table>




 <div class="section-title">Milk Type Summary</div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Total Qty</th>
                <th>Total Amount</th>
                <th>Avg Fat</th>
                <th>Avg SNF</th>
                <th>Avg Rate</th>
            </tr>
        </thead>
        <tbody>
            @php
                $safeDivide = function($numerator, $denominator) {
                    return $denominator != 0 ? $numerator / $denominator : 0;
                };
            @endphp
            
            <tr>
                <td>Buffalo</td>
                <td class="text-right">{{ number_format(floatval($buffaloQty ?? 0), 2) }}</td>
                <td class="text-right">{{ number_format(floatval($buffaloAmount ?? 0), 2) }}</td>
                <td class="text-right">
                    {{ number_format($safeDivide(floatval($buffaloFat ?? 0), floatval($buffaloQty ?? 1)), 1) }}
                </td>
                <td class="text-right">
                    {{ number_format($safeDivide(floatval($buffaloSnf ?? 0), floatval($buffaloQty ?? 1)), 1) }}
                </td>
                <td class="text-right">
                    {{ number_format($safeDivide(floatval($buffaloAmount ?? 0), floatval($buffaloQty ?? 1)), 2) }}
                </td>
            </tr>
            <tr>
                <td>Cow</td>
                <td class="text-right">{{ number_format(floatval($cowQty ?? 0), 2) }}</td>
                <td class="text-right">{{ number_format(floatval($cowAmount ?? 0), 2) }}</td>
                <td class="text-right">
                    {{ number_format($safeDivide(floatval($cowFat ?? 0), floatval($cowQty ?? 1)), 1) }}
                </td>
                <td class="text-right">
                    {{ number_format($safeDivide(floatval($cowSnf ?? 0), floatval($cowQty ?? 1)), 1) }}
                </td>
                <td class="text-right">
                    {{ number_format($safeDivide(floatval($cowAmount ?? 0), floatval($cowQty ?? 1)), 2) }}
                </td>
            </tr>
        </tbody>
    </table>


    <!-- Sales Report -->
    <div class="section-title">Sales Report</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Paid</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($sales))
                @foreach($sales as $sale)
                    @php
                        $quantity = floatval($sale->productQuantity ?? 0);
                        $rate = floatval($sale->productPricePerUnit ?? 0);
                    @endphp
                    <tr>
                        <td>{{ !empty($sale->saleDate) ? date('d-m-Y', strtotime($sale->saleDate)) : 'N/A' }}</td>
                        <td>{{ isset($sale->saleType) ? ucfirst(str_replace('_', ' ', $sale->saleType)) : 'N/A' }}</td>
                        <td>{{ $sale->productName ?? ($sale->productType ?? 'N/A') }}</td>
                        <td class="text-right">{{ number_format($quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($rate, 2) }}</td>
                        <td class="text-right">{{ number_format($quantity * $rate, 2) }}</td>
                        <td class="text-right">{{ number_format(floatval($sale->paidAmount ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">No sales data found</td>
                </tr>
            @endif
        </tbody>
    </table>


<div class="section-title" style="margin-top: 30px;">Advance & Credit Details</div>

<table class="table table-bordered" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @if(count($details))
            @foreach($details as $entry)
                <tr>
                    <td>{{ $entry['date'] }}</td>
                    <td>{{ $entry['type'] }}</td>
                    <td>{{ number_format($entry['amount'], 2) }}</td>
                    <td>{{ $entry['remarks'] }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4" class="text-center">No Advance or Credit Records Found</td>
            </tr>
        @endif
    </tbody>
</table>

     
    <!-- Balance Summary -->
    {{-- <div class="section-title">Balance Summary</div>
    <table>
        <tr class="total-row">
            <td>Previous Balance Till {{ isset($previousDay) && !empty($previousDay) ? date('d-m-Y', strtotime($previousDay)) : 'N/A' }}</td>
            <td class="text-right">{{ number_format(floatval($latestTransaction1->currentBalance ?? 0), 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Balance Till {{ isset($endDate) && !empty($endDate) ? date('d-m-Y', strtotime($endDate)) : 'N/A' }}</td>
            <td class="text-right">{{ number_format(floatval($latestTransaction->currentBalance ?? 0), 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Current Balance</td>
            <td class="text-right">{{ number_format(floatval($ubal->openingBalance ?? 0), 2) }}</td>
        </tr>
    </table> --}}
    <div class="section-title">Balance Summary</div>
<table>
    <tr class="total-row">
        <td>Previous Balance Till {{ isset($previousDay) && !empty($previousDay) ? date('d-m-Y', strtotime($previousDay)) : 'N/A' }}</td>
        <td class="text-right">
            {{ number_format(abs(floatval($latestTransaction1->currentBalance ?? 0)), 2) }}
            {{ (isset($latestTransaction1->currentBalance) && $latestTransaction1->currentBalance >= 0) ? 'Cr.' : 'Dr.' }}
        </td>
    </tr>
    <tr class="total-row">
        <td>Balance Till {{ isset($endDate) && !empty($endDate) ? date('d-m-Y', strtotime($endDate)) : 'N/A' }}</td>
        <td class="text-right">
            {{ number_format(abs(floatval($latestTransaction->currentBalance ?? 0)), 2) }}
            {{ (isset($latestTransaction->currentBalance) && $latestTransaction->currentBalance >= 0) ? 'Cr.' : 'Dr.' }}
        </td>
    </tr>
    <tr class="total-row">
        <td>Current Balance</td>
        <td class="text-right">
            {{ number_format(abs(floatval($ubal->openingBalance ?? 0)), 2) }}
            {{ ($ubal->openingBalanceType ?? 'credit') === 'credit' ? 'Cr.' : 'Dr.' }}
        </td>
    </tr>
</table>


    <!-- Footer -->
    <div style="margin-top: 30px; text-align: right;">
        <div style="border-top: 1px solid #000; width: 200px; margin-left: auto; padding-top: 5px;">
            Authorized Signature
        </div>
    </div>
</body>
</html>