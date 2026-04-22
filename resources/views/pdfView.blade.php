

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Milk Collection</title>
    <link rel="stylesheet" href="{{asset("css/pdfStyle.css")}}" media="all" />
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="{{asset("images/logo_220x110.jpg")}}">
      </div>
      <h1>Milk Collection</h1>
      <div id="company" class="clearfix">
        <div>{{$dairyInfo->society_name}}</div>
        <div>{{$dairyInfo->dairyAddress}}<br /> {{$dairyInfo->pincode."-".$dairyInfo->city.", ".$dairyInfo->district}}</div>
        <div>{{$dairyPer->PropritorMobile}}</div>
        <div><a href="mailto:{{$dairyPer->dairyPropritorEmail}}">{{$dairyPer->dairyPropritorEmail}}</a></div>
      </div>
      <div id="project">
        {{-- <div><span>PROJECT</span> Website development</div> --}}
        <div><span>MEMBER NAME</span> {{ $memberName }}</div>
        <div><span>MEMBER CODE</span> {{ $memberCode }}</div>
        <div><span>ADDRESS</span> {{$member->memberPersonalAddress}}</div>
        <div><span>EMAIL</span> <a href="mailto:{{$member->memberPersonalEmail}}">{{$member->memberPersonalEmail}}</a></div>
        <div><span>DATE</span> {{$date}}</div>
        {{-- <div><span>DUE DATE</span> September 17, 2015</div> --}}
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th class="service">Milk Type</th>
            <th class="desc">Quantity (Ltr)</th>
            <th class="text-r">FAT Quantity</th>
            <th class="text-r">SNF Quantity</th>
            <th class="text-r">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="service">{{$milkType}}</td>
            <td class="desc">{{$milkQuality}}</td>
            <td class="unit">{{$fat}}</td>
            <td class="qty">{{$snf}}</td>
            <td class="total">&#8377; {{$amount}}</td>
          </tr>
        </tbody>
      </table>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>

{{-- <!DOCTYPE html>

<html>

<head>

<title>Load PDF</title>

<style type="text/css">

table{

width: 100%;

border:1px solid black;

}

td, th{

border:1px solid black;

}

</style>

</head>

<body>



<h2>DMS Daily Transaction Report : {{ $date }} </h2>

<table>
	<tr>
		<td style="border:solid black;">Member Code</td>
		<td>{{ $memberCode }}</td>
	</tr>

	<tr>
		<td style="border:solid black;">Member Name</td>
		<td>{{ $memberName }}</td>
	</tr>

	<tr>
		<td style="border:solid black;">Milk Type </td>
		<td>{{ $milkType }}</td>
	</tr>

	<tr>
		<td style="border:solid black;">Milk Quality</td>
		<td>{{ $milkQuality }}</td>
	</tr>



	<tr>
		<td style="border:solid black;">Fat</td>
		<td>{{ $fat }}</td>
	</tr>

	<tr>
		<td style="border:solid black;">Snf</td>
		<td>{{ $snf }}</td>
	</tr>

	<tr>
		<td style="border:solid black;">Amount</td>
		<td>{{ $amount }}</td>
	</tr>
</table>



</body>

</html> --}}