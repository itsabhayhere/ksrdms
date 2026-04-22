<!DOCTYPE html>

<html lang="en">

   <head>

      <meta charset="UTF-8">

      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

      <title>Invoice</title>

      <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all">

      <style type="text/css">

         body{
            font-size: 0.88rem;
         }

         .table-striped tbody tr:nth-of-type(odd) {

         background-color: rgb(255, 255, 255);

         }

         .text-right {

         text-align: right;

         }

         .fl{

         float: left;

         }

         .fr{

         float: right;

         }

         .lh-1{    

             /* line-height: 1; */

         }

         .table td, .table th {

         padding: 5px;

         }

         /* .card-header{

         height: 200px;

         }

         .mt-200{    

         margin-top: 200px;

         } */

      </style>

   </head>

   <body>

      <div class="clearfix">

         <div class=" clearfix">

            <div class="clearfix">

               <div class="row clearfix">

                  <div class="col-sm-6 fl clearfix">

                     <img src="data:image/jpeg;base64,{{ base64_encode(@file_get_contents(url('images/pdfLogo.png'))) }}" class="img-fluid">

                     <h4 class="">We manage your business </h4>

                     <div>VPO Banwala, Tehsil Dabwali - 125103</div>

                     <div>Distt. Sirsa, Haryana</div>

                     <div>M. +91 94991 94291</div>

                     <div>GSTIN. 06AMWPV3442M1Z6</div>

                  </div>

                  <div class="col-sm-6 fl clearfix">

                     <h4 class="fr"> <strong>TAX INVOICE</strong><br/>Invoice# KSRDMS{{$paymentId}}</h4>

                  </div>

                  <div class="clearfix"></div>

               </div>

            </div>

            <hr>

            <div class="lh-1 clearfix">

               <div class="row clearfix">

                  <div class="col-sm-6 fl clearfix">

                     <h4 class="mb-3">Bill To</h4>

                     <div>

                        <strong>{{ucwords($payment->billing_name)}}</strong>

                     </div>

                     <div>{{$payment->billing_address}}</div>

                     <div>{{ucwords($payment->billing_city)}}</div>

                     <div>{{$payment->billing_zip." ".ucwords($payment->billing_state)}}</div>

                     <div>{{$payment->billing_country}}</div>

                     <div>Tel: {{$payment->billing_tel}}</div>

                     <div>Email: {{$payment->billing_email}}</div>


                  </div>

                  <div class="col-sm-6 fl clearfix">

                     <h4 class="mb-3">Ship To:</h4>

                     <div>

                        <strong>{{ucwords($dairy->dairyName)}}</strong>

                     </div>

                     <div>{{ucwords($dairy->cityName)}}</div>

                     <div>{{$dairy->pincode." ".ucwords($dairy->stateName)}}</div>

                     <div>India</div>

                     {{-- 

                     <div>GSTIN 06AMWPV3442M1Z6</div>

                     --}}

                     <br/>

                     {{-- 

                     <div>Place Of Supply: Haryana(06)</div>

                     --}}

                     <br/>

                  </div>

               </div>

            </div>

            <hr>

            <div class="card-header">

               <div class="table-responsive-sm">

                  <div>Invoice Date</div>

                  <div>{{$payment->trans_date}}</div>

               </div>

            </div>

            <div class="card-header">

               <div class="table-responsive-sm">

                  <table class="table table-striped">

                     <thead>

                        <tr>

                           <th class="center">#</th>

                           <th>Item & Description</th>

                           <th class="center">Qty</th>

                           <th class="center">Rate</th>

                           <th class="right">Amount</th>

                        </tr>

                     </thead>

                     <tbody>

                        <tr>

                           <td class="center">1</td>

                           <td><b>DMS {{$plan->name}} Subscription</b>

                              <br/> @if($payment->merchant_param1 == "monthly") 1 Month @else 1 Year @endif Validity

                           </td>

                           <td class="center">1</td>

                           <td class="center">{{$rate}}</td>

                           <td class="right">{{$rate}}</td>

                        </tr>

                     </tbody>

                  </table>

               </div>

            </div>

            <div class="row clearfix">


               <div class="col-lg-5 col-sm-5 ml-auto">

                  <table class="table table-clear">

                     <tbody>

                        <tr>

                           <td class="text-right">

                              <strong>Subtotal</strong>

                           </td>

                           <td class="text-right">{{$rate}}</td>

                        </tr>

                        <tr>

                           <td class="text-right">

                              <strong>CGST ({{$cgst_per}}%)</strong>

                           </td>

                           <td class="text-right">{{$cgst}}</td>

                        </tr>

                        <tr>

                           <td class="text-right">

                              <strong>SGST ({{$sgst_per}}%)</strong>

                           </td>

                           <td class="text-right">{{$sgst}}</td>

                        </tr>

                        <tr>

                           <td class="text-right">

                              <strong>IGST ({{$igst_per}}%)</strong>

                           </td>

                           <td class="text-right">{{$igst}}</td>

                        </tr>

                        <tr>

                           <td class="text-right">

                              <strong>Total</strong>

                           </td>

                           <td class="text-right">

                              <strong>Rs.{{$payment->amount}}</strong>

                           </td>

                        </tr>
                        <tr>
                           <td class="text-right">

                              <strong>Total in words:</strong>

                           </td>

                           <td class="text-right">

                              <strong>Rs.{{$payment->amountinwords}}</strong>

                           </td>
      
                        </tr>

                        {{-- <tr>

                           <td class="text-right">

                              <strong>Payment Mode</strong>

                           </td>

                           <td class="text-right">

                              <strong>(-) {{$payment->amount}}</strong>

                           </td>

                        </tr>

                        <tr>

                           <td class="text-right">

                              <strong>Balance Due</strong>

                           </td>

                           <td class="text-right">

                              <strong>Rs.0.00</strong>

                           </td>

                        </tr> --}}

                     </tbody>

                  </table>

               </div>

            </div>

         </div>

      </div>

      <div class="clearfix"></div>

      <div style="bottom:0">

         <hr/>

         <li>All Subject to Dabwali Jurisdiction only.</li>

         <li>E. & O. E.</li>

         <li>This is an electronically generated receipt & does not require signature.</li>

      </div>

   </body>

</html>