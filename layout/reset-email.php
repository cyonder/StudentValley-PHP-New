<?php
$firstName = $_GET['Firstname'];
$lastName = $_GET['Lastname'];
$owner = $_GET['Owner'];
$code = $_GET['Code'];
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <title>You requested password reset for your Student Valley account</title>
    <style type='text/css'>
        body{
            font-family: helvetica, arial, 'lucida grande', sans-serif;
        }
    </style>
</head>
<body bgcolor='#E0E0E0'>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr>
        <td>
            <table width='640' align='center' border='0' cellspacing='0' cellpadding='0' bgcolor='#F2F2F2' style='border: 1px solid #BDC7D8; padding: 10px; table-layout: fixed;'>
                <tr>
                    <td>
                        <table border='0' cellspacing='0' cellpadding='0' style='background-color: #E0E0E0; border: 1px solid #BDC7D8; padding: 5px;'>
                            <tr style='padding: 10px;'>
                                <td valign='top' rowspan='2' width='86'>
                                    <a href='http://studentvalley.org' target='_blank'><img src='http://www.studentvalley.org/image/sv/Logo-Small.png' alt='Go to Student Valley' width='76' height='60' border='0'></a>
                                </td>
                                <td width='554' height='30' valign='bottom' style='color: #307FB7; font-weight: bold; font-size: 16px;'>Hi <?php echo"{$firstName} {$lastName}" ?>,</td>

                            </tr>
                            <tr>
                                <td width='554' height='30' valign='top' style='font-size: 15px'>You requested password reset for your Student Valley account.</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border='0' cellspacing='0' cellpadding='0' style='margin-bottom: 10px'>
                            <tr>
                                <td style='padding-top: 10px; font-size: 15px;'>
                                    <p>You (or someone else) requested password reset for your Student Valley account.
                                        If you didn't request password reset, you can ignore this message.</p>

                                    <p>If you requested password reset, you can click the button below to reset your password.</p>
                                    <p style='text-align: center'><a href='http://studentvalley.org/reset?Code=<?php echo $code;?>'
                                                                     style='color: #FFFFFF; text-decoration: none; background-color: #307FB7; padding: 5px 10px; border-radius: 5px;'>Click here to reset your password</a></p>
                                    <p>If you are getting a lot of password reset emails you didn't request, Let us know immediately.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border='0' cellspacing='0' cellpadding='0' style='background-color: #E0E0E0; border: 1px solid #BDC7D8; padding: 5px;'>
                            <tr style='padding: 10px;'>
                                <td width='600' style='color: #666666; font-size: 12px; text-align: center'>
                                    <p>This is an automated email. Please DO NOT reply to this email.</p>
                                    <p>Reset password button will be deactivated after 1 hour when you received this email.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>