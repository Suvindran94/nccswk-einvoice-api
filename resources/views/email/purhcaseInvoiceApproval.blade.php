<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body
    style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787e; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0"
        style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
        <tr>
            <td align="center" style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;">
                <table class="content" width="100%" cellpadding="0" cellspacing="0"
                    style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                    <tr>
                        <td class="header"
                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                            <a href="https://nccierp2u.com/"
                                style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; color: black; font-size: 25px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 #ffffff;">
                                NCC - SUPPLY CHAIN MANAGEMENT
                            </a>
                        </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0"
                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; background-color: #ffffff; border-bottom: 1px solid #edeff2; border-top: 1px solid #edeff2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                                style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; background-color: #ffffff; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell"
                                        style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">

                                        <p
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;  font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                            Hi Sir/Madam,</p>



                                        <p
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;  font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                            A <b>Purchase Invoice</b> has been
                                            <b>{{ $purchaseInvoiceHeader->PI_APV_STATUS == 'A' ? 'Approved' : 'Rejected' }}</b>.
                                        </p>
                                        <p
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;  font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                            Detail as below:</p>
                                        <table class="panel" width="100%" cellpadding="0" cellspacing="0"
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;">
                                            <tr>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:30%;">
                                                    Invoice ID
                                                </td>
                                                <td style="width:2%;">
                                                    :
                                                </td>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:68%;">
                                                    <b>{{ $purchaseInvoiceHeader->PI_ID }}</b>
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="panel" width="100%" cellpadding="0" cellspacing="0"
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;">
                                            <tr>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:30%;">
                                                    Reference No.
                                                </td>
                                                <td style="width:2%;">
                                                    :
                                                </td>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:68%;">


                                                    <b>{{ $purchaseInvoiceHeader->PI_REF }}</b>

                                                </td>
                                            </tr>
                                        </table>
                                        <table class="panel" width="100%" cellpadding="0" cellspacing="0"
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;">
                                            <tr>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:30%;">
                                                    Approval Remark
                                                </td>
                                                <td>
                                                    :
                                                </td>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:68%;">


                                                    <b>{{ $purchaseInvoiceHeader->PI_APV_REMARK }}</b>

                                                </td>
                                            </tr>
                                        </table>
                                        <table class="panel" width="100%" cellpadding="0" cellspacing="0"
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;">
                                            <tr>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:30%;">
                                                    {{ $purchaseInvoiceHeader->PI_APV_STATUS == 'A' ? 'Approved' : 'Rejected' }}
                                                    by
                                                </td>
                                                <td>
                                                    :
                                                </td>
                                                <td style=" font-family: Arial, Helvetica, sans-serif; width:68%;">
                                                    <b>{{ $purchaseInvoiceHeader->approver->fname }}
                                                        ({{ $purchaseInvoiceHeader->PI_APV_DATE ? Carbon\Carbon::parse($purchaseInvoiceHeader->PI_APV_DATE)->format('d M Y') : 'N/A' }})</b>
                                                </td>
                                            </tr>
                                        </table>




                                        <p
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;  font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                            For further information, please contact the Invoice approver.<br>

                                            Please do not reply to this email as it was automatically generated</p>


                                        <p>Thank You.</p>

                                        <table class="subcopy" width="100%" cellpadding="0" cellspacing="0"
                                            style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; border-top: 1px solid #edeff2; margin-top: 25px; padding-top: 25px;">
                                            <tr>
                                                <td
                                                    style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;">
                                                    <p
                                                        style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; color: #74787e; line-height: 1.5em; margin-top: 0; text-align: justify; font-size: 12px;">
                                                        DISCLAIMER: This e-mail and its attachments contains
                                                        confidential information. It is intended solely for the use of
                                                        the intended recipient to which it has been addressed. If you
                                                        are not the intended recipient, you are hereby notified that
                                                        disclosing, copying, distributing or taking any action in
                                                        reliance on the contents of this information is strictly
                                                        prohibited. Please delete the email from your system. The
                                                        recipient should check this email and any attachments for the
                                                        presence of viruses.Please note that neither NCC Solutions Sdn
                                                        Bhd
                                                        nor the sender accepts any responsibility for any damage caused
                                                        by any virus that may be contained in this e-mail or its
                                                        attachments.</p>
                                                </td>
                                            </tr>
                                        </table>



                                    </td>
                                </tr>
                            </table>

                        </td>

                    </tr>
                    <tr>
                        <td style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box;">
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0"
                                style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                <tr>
                                    <td class="content-cell" align="center"
                                        style=" font-family: Arial, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                        <p
                                            style=" font-family: Arial, Helvetica, sans-serif;box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #aeaeae; font-size: 12px; text-align: center;">
                                            © 2022 POLYWARE. All rights reserved.</p>
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
