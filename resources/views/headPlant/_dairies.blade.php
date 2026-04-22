@isset($dairies)
@foreach ($dairies as $r)
<tr class="dairyReq{{$r->id}}">
    <td>{{ $r->society_code}}</td>
    <td>{{ $r->dairyName}}</td>
    <td>{{ $r->mobile}}</td>
    <td>{{ $r->dairyAddress}}, {{ $r->state}}, {{ $r->city}}</td>
    <td>{{ $r->dairyPropritorName}}</td>
    <td>{{ $r->PropritorMobile.", ".$r->dairyPropritorEmail}}</td>    
    {{-- <td> --}}
        {{-- <a href="javascript:void(0);" onclick="completeRequest({{ $r->dairyId}}, 'Accept');" class="btn btn-default btn-sm"> Accept </a> --}}
        {{-- <a href="javascript:void(0);" onclick="completeRequest({{ $r->dairyId}}, 'Decline');" class="btn btn-danger btn-sm"> Remove </a> --}}
    {{-- </td> --}}
</tr>
@endforeach
@endisset

<tr>
    <p>No data Found</p>
</tr>

    
