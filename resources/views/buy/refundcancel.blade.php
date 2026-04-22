@extends('layouts.app') 

@section('content')
<style>
    table{
        border: burlywood;
    }
</style>

    <div class="container bg-white mt-5 w-75">
        
         <h2 class="text-center ">   Refund & Cancellation Policy </h2>
       
         <hr>
    <div class="row ">
        <div class="col-md-1">
        </div>
        
        <div class="col-md-9">
         <br>
         <p class="text-justify text-grey-dark  ml-5 mt-5">
                Thanks for registering at www.ksrdms.com
                If you are not entirely satisfied with our service, we're here to help.
                We do not provide refund and return policy, as we are providing trial period for all the subscription plans however if the registration and subscription is cancelled within a day's time subject to payment not confirmed we will refund your payments only after refund request by registered mobile number. Refund will be processed through same mode within 10 working days
                
                KSR Services makes best possible efforts to provide quality products and services. 
                
         </p>
         <br>
         <legend class=" text-dark ml-5">
             Refunds
         </legend>
         <p class="ml-5 ">
                Once we receive your request by registered mobile number for cancellation and refund, 
                we will scrutinize and evaluate the transaction. After approval refund will initiate. 
                Refund amount is calculated by deducting internet payment charges (if any) from your original 
                payment.
         </p>
         <br><br>
         <table class="table ml-5  mt-5">
            

            <tr>
                <th>Payment Method</th>
                <th>Refund Method</th>
                <th>Refund Time-frame</th>
            </tr>
            <tr>
                <td>Credit Card/ Debit Card	Credit Card/ Debit Card	8-10 business days</td>
                <td>Credit Card/ Debit Card</td>
                <td>8-10 business days</td>
            </tr>
            <tr>
                <td>Net Banking</td>
                <td>Net Banking Account (Credited to Bank Account)</td>
                <td>8-10 business days</td>
            </tr>

         </table>
        <br>
        <p class="ml-5">If the standard time-frame as mentioned above has passed and you have still not received the refund, please contact your credit or debit card issuer or your bank for more information.
                If you have any questions about our Returns and Refunds Policy, please contact us:
                <br><p class="text-info ml-5 mb-5">By email: support@ksrdms.com or call us on +91 94991 94291</p>
        </p>  
        </div>
        <div class="col-md-1 mb-5">
            
        </div>              
    </div>
    <br>
    </div>
    <br>
    
@endsection