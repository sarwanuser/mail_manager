<!DOCTYPE html>
<html lang="en">
<head>
    <title>CLYKK Email Template</title>
</head>
<body style="font-family: sans-serif;line-height: 25px;">
    <table style="margin:50px;text-align: left;border-collapse:collapse;">
        <tr>
            <th colspan="2"><img src="images/logo.png" alt="" style="height: 30px;"></img></th>
            <td colspan="3" style="text-align: right;">{{date('d M Y, h:i A')}}</td>
        </tr>

        <tr>
            <th colspan="2">Your Service are just a CLYKK Away ™</th>
            <td colspan="3" style="text-align: right;">Transaction ID: <span style="color: blue;">{{$data->transaction_id}}</span> </td>
        </tr>
        <tr style="border-bottom: 1px solid #eee;"><td>&nbsp;</td></tr>
        
        <tr>
            <td colspan="5">
                <br><br><b>Hello {{$data->getcartdetails->getUserDetails->first_name}} {{$data->getcartdetails->getUserDetails->last_name}},</b>
                <br><br><b style="color: #bb9820;">You sent a payment of ₹ {{$data->total}} to CLYKK Service India Private Limited</b>
                <br><b style="color: #bb9820;">(<span style="color: blue;">customer.support@clykk.com</span>)</b> 
                <br><br>It may take a few moments for this transaction to appear in your account.
                <br><br><b>MarketPlace</b>
                <br>CLYKK Service India Private Limited
                <br><span style="color: blue;">customer.support@clykk.com</span>
            </td>
        </tr>

        <tr><td>&nbsp;</td></tr>
        <tr>
            <th colspan="2">Order NO: {{$data->cart_id}}</th>
            <th colspan="1">Subscription Id: {{$data->subscription_id}}</th>
            <th colspan="1">Service Date: {{$data->service_date}}</th>
            <th colspan="2">Service Time: {{$data->service_time}}</th>
        </tr>

        <tr>
			@foreach($data->getAddressDetails as $address) 
            <td colspan="3">
                <h4>{{ucfirst(str_replace('_', ' ', $address->addressType))}}:</h4>
                {{$address->line1}}
                <br>{{$address->line2}}
                <br>{{$address->line3}}
                <br>{{$address->landmark}}
                <br>{{$address->city}}
                <br>{{$address->pincode}}
                <br>{{$address->state}}
                <br>{{$address->country}}
            </td>
			@endforeach
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr style="border-top: 1px solid #eee;border-bottom: 1px solid #eee;background-color: #0407060f;">
            <td style="padding: 8px;">Package </td>
            <td></td>
            <td style="text-align: right;width: 200px;padding: 8px;">Qty</td>
            <td style="text-align: right;width: 200px;padding: 8px;">Base Price </td>
            <td style="text-align: right;width: 200px;padding: 8px;">Selling Price</td>
        </tr>

        <tbody>
			@foreach($data->getCartPackageDetails as $package)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px;"><img src="{{$package->package_image}}" alt="logo" style="height:40px; width:40px;"></td>
                <td style="padding: 8px;"> {{$package->package_name}}</td>
                <td style="text-align: right;padding: 8px;">1</td>
                <td style="text-align: right;padding: 8px;color: red;">₹ {{$package->base_price}}</td>
                <td style="text-align: right;padding: 8px;">₹ {{$package->selling_price}} </td>
            </tr>
			@endforeach
            <tr style="border-top: 1px solid #eee;border-bottom: 1px solid #eee;background-color: #0407060f;">
                <td style="padding: 8px;">AddOns </td>
                <td></td>
                <td style="text-align: right;width: 200px;padding: 8px;">Qty</td>
                <td style="text-align: right;width: 200px;padding: 8px;">Base Price </td>
                <td style="text-align: right;width: 200px;padding: 8px;">Selling Price</td>
            </tr>

            @foreach($data->getCartAddonPackageDetails as $addonpackage)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px;"><img src="{{$addonpackage->package_image}}" alt="logo" style="height:40px; width:40px;"></td>
                <td style="padding: 8px;"> {{$addonpackage->package_name}}</td>
                <td style="text-align: right;padding: 8px;">1</td>
                <td style="text-align: right;padding: 8px;color: red;">₹ {{$addonpackage->base_price}}</td>
                <td style="text-align: right;padding: 8px;">₹ {{$addonpackage->selling_price}} </td>
            </tr>
			@endforeach

            <tr>
                <td colspan="4" style="text-align: right;"><br>Sub Total</td>
                <td style="text-align: right;"><br> ₹ {{$data->amount_before_tax}}</td>
            </tr>

            <tr>
                <td colspan="4" style="text-align: right;">Discount</td>
                <td style="text-align: right;">coupon</td>
            </tr>

            <tr>
                <td colspan="4" style="text-align: right;">Discount Value</td>
                <td style="text-align: right;">₹ {{$data->payable_amount-$data->total}}</td>
            </tr>

            <tr>
                <td colspan="4" style="text-align: right;">Taxes (GST-SAC 998533-18%)</td>
                <td style="text-align: right;">₹ {{$data->tax_amount}}</td>
            </tr>

            <tr>
                <td colspan="4" style="text-align: right;">Paid Amount</td>
                <td style="text-align: right;">₹ {{$data->total}}</td>
            </tr>

            <tr>
                <td colspan="4" style="text-align: right;">Payment Type</td>
                <td style="text-align: right;">{{$data->payment_type}}</td>
            </tr>

            <tr>
                <td colspan="4" style="text-align: right;">Payment Status</td>
                <td style="text-align: right;">{{$data->collection_status}}</td>
            </tr>


            <tr><td>&nbsp;</td></tr>
            
            <tr>
                <td colspan="5" style="text-align: right;">Charge will appear on your credit card or bank statement as “CLYKK Services India Private Limited “</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">Payment sent to <span style="color: blue;">customer.support@clykk.com</span></td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>

            <tr>
                <td colspan="5"  style="border-top: 1px solid #eee;">
                    <br><b>Issues with this transaction?</b>
                    <br>You have 30 days from the date of the transaction to open a dispute with customer service.
                    <br>Questions? Go to the Help Center at <span style="color: blue;">www.clykk.com/faq</span>.
                    <br>Please do not reply to this email. This mailbox is not monitored and you will not receive a response. For assistance, log in to your clykk consumer app account and click <b>menu</b> in the top left corner of any app and select My Services and the order to see details.  
                    <br>CLYKK Services Payments in India are provided by RazorPay  (www.razorpay.com. Users are advised to read the Terms and Conditionsand Privacy Policyfully.
                    <br><br>CLYKK Order ID: {{$data->order_id}}                                    
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>