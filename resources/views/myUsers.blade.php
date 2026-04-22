@extends('theme.default')



@section('content')

<div class="row">

    <div class="col-lg-12">

        <h1 class="page-header">My Users</h1>

    </div>

    <!-- /.col-lg-12 -->

</div>

<!-- /.row -->



<table class="table table-striped table-bordered table-hover">

    <thead>

        <tr>

            <th>#</th>

            <th>Name</th>

            <th>Email</th>

            <th>Register on </th>

        </tr>

    </thead>

    <tbody>
	
		@foreach($user as $town)
		<tr> 
			<td>
				{{ $town->id }}
			</td> 
			<td>
				{{ $town->name }}
			</td>
			<td>
				{{ $town->email }}
			</td>
			<td>
				{{ $town->created_at }}
			</td>
		</tr>
		@endforeach
		
        

    </tbody>

</table>



@endsection