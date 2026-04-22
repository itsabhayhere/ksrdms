<html>
    <body>
        
		
		@foreach($user as $singleUser)
			{{ $singleUser->name }} or what the column name is {{ $singleUser->id }} <br/>
		@endforeach
    </body>
</html>