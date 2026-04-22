@extends('theme.default')

@section('content')

<?php 
	// require("fpdf/fpdf.php");
	// $pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont("Arial","B",20);
	$pdf->Cell(0,10,"DMS Daily Transaction Report : #date",0,1);
	$pdf->SetFont("Arial","B",10);
	$pdf->Cell(0,10,"name: {$name}",1,1);
	$pdf->Cell(0,10,"Welcome",1,1);
	$pdf->Cell(0,10,"Welcome",1,1);
	$pdf->Cell(0,10,"Welcome",1,1);
	$pdf->Cell(0,10,"Welcome",1,1);
	$pdf->output();
 ?>

@endsection


