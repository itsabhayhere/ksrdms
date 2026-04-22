@extends('theme.default') 
@section('content')


<div class="pageblur">

    <div class="fcard margin-fcard-1 pt-0 clearfix">
        <div class="upper-controls pt-0 clearfix">
            <div class="fl">
                <div class="heading">
                    <h3>Credit</h3>
                    <hr class="m-0">
                </div>
            </div>
            <div class="fr pt-10">
                <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
                <a class="btn btn-primary" href="newColMan">Add New Collection Manager</a>
            </div>
        </div>

        <div class="table-back ">
            <table id="colMans-table" class="display tright" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Mobile Number</th>
                        <th>Collection Manager Name</th>
                        <th>RateCard Type For Cow</th>
                        <th>RateCard Type For Buffalo</th>
                    </tr>
                </thead>

                <tbody class="">
                    @foreach($colMans as $c)
                        <tr>
                            <td>{{ $c->mobileNumber }}</td>
                            <td>{{ $c->userName }}</td>
                            <td>{{ $c->rateCardTypeForCow }}</td>
                            <td>{{ $c->rateCardTypeForBuffalo }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>

</div>



<script>


    $(document).ready(function() {
		$('#colMans-table').DataTable({
		});
       
	});

</script>
@endsection