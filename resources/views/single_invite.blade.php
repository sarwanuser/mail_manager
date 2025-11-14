<!DOCTYPE html>
<html lang="en">
<head>
    <title>CLYKK Class Invite</title>
    <style>
        .main_div{
            position: relative;
            z-index:1;
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
<body style="font-family: sans-serif;line-height: 25px;" class="main_div">
<table style="margin:50px;text-align: left;border-collapse:collapse;">
            <tr>
                <td colspan="5"><img src="images/logo.png" alt="" style="height: 30px;"></img></th>
            </tr>
            <tr>
                <td colspan="5">{{date('d M Y, h:i A')}}</td>
            </tr>
            <tr>
                <td colspan="5">Your Service are just a <br>CLYKK Away ™</th>
            </tr>
            <tr>
                <td colspan="5">Class : <span style="color: blue;">{{$data['class_name']}}</span> </td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;"><td>&nbsp;</td></tr>
            
            <tr>
                <td colspan="5">
                    <br><br><b>Hello {{$data['name']}},</b>
                    
                    <br><b style="color: #bb9820;">(<span style="color: blue;">customer.support@clykk.com</span>)</b> 
                    <br><br>It may take a few moments for this transaction to appear in your account.
                    <br><br><b>MarketPlace</b>
                    <br>CLYKK Service India Private Limited
                    <br><span style="color: blue;">customer.support@clykk.com</span>
                </td>
            </tr>
            <tr><td colspan="6">&nbsp;</td></tr>

            <tbody>
                
                <tr>
                <td>
                	<p>You are invited to attend a class meeting scheduled as follows:</p><br>
					<b>Date: {{$data['class_date']}}</b><br>
					<b>Time: {{$data['class_time']}}</b><br>
					<b>Meeting Link: {{$data['class_link']}}</b><br>
                </td>
                </tr>
                
                <tr><td>&nbsp;</td></tr>

                <tr>
                    <td colspan="5"  style="border-top: 1px solid #eee;">
                        <br><b>Issues with this class?</b>
                        <br>Questions? Go to the Help Center at <span style="color: blue;">www.clykk.com/faq</span>                                
                    </td>
                </tr>
            </tbody>
        </table>
</body>
</html>