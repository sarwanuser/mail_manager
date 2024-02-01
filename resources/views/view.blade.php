<!DOCTYPE html>
<html lang="en">
<head>
    <title>CLYKK Invoice</title>
    <style>
        .main_div{
            position: relative;
            z-index:1;
            overflow:hidden; /*if you want to crop the image*/
        }
        .main_div:before {
            z-index: -1;
            position: absolute;
            left: -10px;
            top: 565px;
            content: url('https://v2admin.clykk.com/static/media/logo.7744e3f2.png');
            opacity: 0.1;
            transform: rotate(-43deg);
        }
    </style>
</head>
<body style="font-family: sans-serif;line-height: 25px;background: #eee;">
    <div style="margin: 0 auto;background-color: #fff;width: 950px;box-shadow: 0px 1px 6px 1px #555;" class="main_div">
        <table style="margin:50px;text-align: left;border-collapse:collapse;">
            <tr>
                <th colspan="2"><img src="images/logo.png" alt="" style="height: 30px;"></img></th>
                <td colspan="3" style="text-align: right;">{{date('d M Y, h:i A')}}</td>
            </tr>

            <tr>
                <th colspan="2">Your Service are just a <br>CLYKK Away ™</th>
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

            <tr><td colspan="6">&nbsp;</td></tr>
            <tr>
                <th colspan="6">Order NO: {{$data->cart_id}} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; Subscription Id: {{$data->subscription_id}} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; Service Date: {{$data->service_date}} &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; Service Time: {{$data->service_time}}</th>
            </tr>

            <tr>
                @foreach($data->getAddressDetails as $address) 
                <td colspan="3">
                    <h4>{{ucfirst(str_replace('_', ' ', $address->addressType))}}:</h4>
                    {{$address->line1}}
                    &nbsp; {{$address->line2}}
                    &nbsp; {{$address->line3}}
                    &nbsp; {{$address->landmark}}
                    &nbsp; {{$address->city}}
                    &nbsp; {{$address->pincode}}
                    &nbsp; {{$address->state}}
                    &nbsp; {{$address->country}}
                </td>
                @endforeach
            </tr>
            <tr><td colspan="6">&nbsp;</td></tr>
            <tr style="border-top: 1px solid #eee;border-bottom: 1px solid #eee;background-color: #0407060f;">
                <td style="padding: 8px;" colspan="2">Package </td>
                <td style="text-align: right;padding: 8px;">Qty</td>
                <td style="text-align: right;padding: 8px;"><center>Base Price <br> Selling Price</center></td>
                <td style="text-align: right;padding: 8px;">Price</td>
            </tr>

            <tbody>
                @foreach($data->getCartPackageDetails as $package)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px;"><img src="{{$package->package_image}}" alt="logo" style="height:40px; width:40px;"></td>
                    <td style="padding: 8px;" colspan="1"> {{$package->package_name}}</td>
                    <td style="text-align: right;padding: 8px;">1</td>
                    <td style="text-align: right;padding: 8px;color: red;"><center><s>₹ {{$package->base_price}}</s> <br> <span style="color: green;">₹ {{$package->selling_price}}</span></center></td>
                    <td style="text-align: right;padding: 8px;">₹ {{number_format($package->selling_price*1,2)}} </td>
                </tr>
                @endforeach
                <tr style="border-top: 1px solid #eee;border-bottom: 1px solid #eee;background-color: #0407060f;">
                    <td style="padding: 8px;" colspan="2">Package </td>
                    <td style="text-align: right;padding: 8px;">Qty</td>
                    <td style="text-align: right;padding: 8px;"><center>Base Price  <br> Selling Price</center></td>
                    <td style="text-align: right;padding: 8px;">Price</td>
                </tr>

                @foreach($data->getCartAddonPackageDetails as $addonpackage)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px;"><img src="{{$addonpackage->package_image}}" alt="logo" style="height:40px; width:40px;"></td>
                    <td style="padding: 8px;"> {{$addonpackage->package_name}}</td>
                    <td style="text-align: right;padding: 8px;">{{$addonpackage->item_count}}</td>
                    <td style="text-align: center;padding: 8px;color: red;"><s>₹ {{$addonpackage->base_price}}</s><br> <span style="color: green;">₹ {{$addonpackage->selling_price}}</span></td>
                    <td style="text-align: right;padding: 8px;"> ₹ {{number_format($addonpackage->selling_price*$addonpackage->item_count,2)}}</td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="4" style="text-align: right;"><br>Sub Total</td>
                    <td style="text-align: right;"><br> ₹ {{number_format($data->getSubTransactions[0]->amount_before_tax,2)}}</td>
                </tr>
                <?php if($data->getSubTransactions[0]->discount != ''){?>
                <tr>
                    <td colspan="4" style="text-align: right;">Discount</td>
                    <td style="text-align: right;">Coupon</td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align: right;">Discount Value</td>
                    <td style="text-align: right;">₹ {{number_format($data->getSubTransactions[0]->payable_amount-$data->getSubTransactions[0]->total,2)}}</td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="4" style="text-align: right;">Taxes (GST-SAC 998533-18%)</td>
                    <td style="text-align: right;">₹ {{number_format($data->getSubTransactions[0]->tax_amount,2)}}</td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align: right;">Paid Amount</td>
                    <?php if($data->getSubTransactions[0]->discount != ''){?>
                        <td style="text-align: right;">₹ {{number_format($data->getSubTransactions[0]->total,2)}}</td>
                    <?php }else{ ?>
                        <td style="text-align: right;">₹ {{number_format($data->getSubTransactions[0]->payable_amount,2)}}</td>
                    <?php } ?>
                </tr>

                <tr>
                    <td colspan="4" style="text-align: right;">Payment Type</td>
                    <td style="text-align: right;">{{ucfirst($data->getSubTransactions[0]->payment_type)}}</td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align: right;">Payment Status</td>
                    <td style="text-align: right;">{{ucfirst($data->getSubTransactions[0]->collection_status)}}</td>
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
    </div>
</body>
</html>