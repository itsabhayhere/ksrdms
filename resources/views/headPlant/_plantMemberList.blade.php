
 <thead>

        <tr>
            <th>Society Code</th>
            <th>Member Code</th>

            <th>Member Name</th>
            <th>Father Name</th>


            <th>Mobile Number</th>

            <th>Aadhar Number</th>

            <th>Bank Name & Branch</th>

            <th>IFSC Code</th>

            <th>Bank Account Number</th>

            <th>Name of A/C Holder</th>

        </tr>

    </thead>
    <tbody>

        
            @foreach ($members as $d)
            <tr>
            <td>{{$d->society_code}}</td>
            <td>{{ $d->memberPersonalCode}}</td>

            <td>{{ $d->memberPersonalName}}</td>

            <td>{{ $d->memberPersonalFatherName}}</td>
            <td>{{ $d->memberPersonalMobileNumber}}</td>

            <td>{{ $d->memberPersonalAadarNumber}}</td>

            <td>{{$d->memberPersonalBankName}}</td>
            <td>{{$d->memberPersonalIfsc}}</td>
            <td>{{$d->memberPersonalAccountNumber}}</td>
            <td>{{$d->memberPersonalAccountName}}</td>
        </tr>
            @endforeach
       
    </tbody>