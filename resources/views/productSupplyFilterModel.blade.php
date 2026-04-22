<table id="MyTable" class="table table-bordered table-striped tright" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Supplier Code</th>
            <th>Supplier Name</th>
            <th>Product Code</th>
            <th>Quantity</th>
            <th>Purchase Type</th>
            <th>Total Purchase Amount &#8377;</th>
            <th>Paid to supplier</th>
            <th>Final Amount to be Paid</th>
        </tr>
    </thead>

    <tbody>

        @foreach ($purchase as $purch)
            <tr>
                <td>{{ date("d-m-Y", strtotime($purch->date)) }}</td>
                <td>{{ $purch->supplierCode}}</td>
                <td>{{ $purch->supplierName}}</td>
                <td>{{ $purch->productCode. " (". $purch->itemPurchased.")" }}</td>
                <td>{{ $purch->quantity}}</td>
                <td>{{ ucfirst($purch->purchaseType)}}</td>
                <td>&#8377; {{ $purch->amount}}</td>
                <td>&#8377; {{ $purch->paidAmount}}</td>
                <td>&#8377; {{ $purch->amount - $purch->paidAmount}}</td>
            </tr>
        @endforeach

    </tbody>
</table>