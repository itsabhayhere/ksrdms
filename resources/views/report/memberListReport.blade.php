<table class="memberList-tabel table table-bordered table-striped">
    <thead>
        <tr>
            <th>Code</th>
            <th style="width: 150px;">Name (gender)</th>
            <!--<th>Email</th>-->
            <th>Father Name</th>
            <th>AadharNumber</th>
            <th>MobileNumber</th>
            <th>Address</th>
            <th>Bank</th>
            <th>Account Holder Name</th>
            <th>Account Number</th>
            <th>IFSC Code</th>
            <th style="width: 70px;">Joining Date</th>
        </tr>
    </thead>

    <tbody>
        @php foreach($queryData as $d){
            if($d->ledgerId != "" ){ 
                $bif = DB::table('member_personal_bank_info')
                    ->where('memberPersonalUserId', $d->id)->get()->first();
                $ob = $bif->memberPersonalBankName;
                $obt = $bif->memberPersonalAccountNumber;
                $ifsc = $bif->memberPersonalIfsc;
                $acName = $bif->memberPersonalAccountName;
            }else{
                $ob = "";
                $obt = "";
                $ifsc = "";
            }
        @endphp
        <tr>
            <td>{{ $d->memberPersonalCode }}</td>
            <td style="width: 150px;">{{ $d->memberPersonalName }} <small>@if(strtolower($d->memberPersonalGender)=="male") (m) @elseif(strtolower($d->memberPersonalGender)=="female") (f) @endif </small></td>
            <td>{{ $d->memberPersonalFatherName }}</td>
            <td>{{ $d->memberPersonalAadarNumber }}</td>
            <td>{{ $d->memberPersonalMobileNumber }}</td>
            <td>{{ $d->memberPersonalAddress }}</td>
            <td>{{ $ob }}</td>
            <td>{{ $acName }}</td>
            <td>{{ $obt }}</td>
            <td>{{ $ifsc }}</td>
            <td style="width: 70px;">{{ $d->memberPersonalregisterDate }}</td>
        </tr>
        @php
            }
        @endphp


    </tbody>
</table>