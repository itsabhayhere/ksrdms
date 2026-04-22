<table class="table table-stripped table-hover mt-1 supplierSttTable">
    <thead>
        <tr>
            <th>Supplier Code</th>
            <th>Supplier Name</th>
            <th>Remark</th>
            <th>Date</th>
            <th>Cash</th>
            <th>Credit</th>
            <th>Debit</th>
        </tr>
    </thead>
    <tbody>

        @foreach($supplierdata as $data)
        <tr>

            <td>{{$data->partyCode}}</td>
            <td>{{$data->partyName}}</td>
            <td>{{$data->type}} @if(!empty($data->remark)) [{{$data->remark}}] @endif
            </td>

            <td>{{$data->date}}</td>
            <td>@php if(empty((float) $data->cash)) { echo "--"; } else{ echo $data->cash; } 
@endphp
            </td>
            <td>
                @php if(((float) $data->cash - (float) $data->credit)==0) { echo "--"; } else{ echo (float) $data->cash - (float) $data->credit;
                } 
@endphp
            </td>
            <td>@php if(empty((float) $data->debit)) { echo "--"; } else{ echo $data->debit; } 
@endphp
            </td>
        </tr>
        @endforeach
    </tbody>

</table>