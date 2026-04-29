<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply from Urbanist support</title>
</head>
<body style="margin:0; padding:32px 16px; background-color:#f5f5f5; font-family:'Urbanist','Segoe UI',Arial,sans-serif; color:#212121;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; border-collapse:collapse;">
                    <tr>
                        <td style="padding-bottom:18px; text-align:center;">
                            <img src="{{ asset('assets/images/logo/logo-spin.png') }}" alt="Urbanist" style="width:78px; height:78px; display:block; margin:0 auto 12px;">
                            <div style="font-size:13px; letter-spacing:0.18em; text-transform:uppercase; color:#0f9874;">Urbanist</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #d9e4e1; border-radius:22px; overflow:hidden;">
                            <div style="background-color:#0d2235; padding:28px 32px;">
                                <div style="font-size:28px; line-height:1.2; font-weight:700; color:#ffffff;">We replied to your message</div>
                                <p style="margin:12px 0 0; font-size:15px; line-height:1.7; color:#d8e5eb;">Our Urbanist support team has responded to your recent contact request.</p>
                            </div>
                            <div style="padding:32px;">
                                <p style="margin:0 0 16px; font-size:15px; line-height:1.7; color:#5f726d;">Hi {{ $contactMessage->name }},</p>
                                <div style="padding:20px; border-radius:18px; background-color:#f1fbf8; border:1px solid rgba(17,179,135,0.24);">
                                    <div style="font-size:12px; letter-spacing:0.16em; text-transform:uppercase; color:#0f9874; margin-bottom:10px;">Support reply</div>
                                    <div style="font-size:15px; line-height:1.8; color:#23343d; white-space:pre-line;">{{ $reply->body }}</div>
                                </div>
                                <p style="margin:18px 0 0; font-size:14px; line-height:1.7; color:#5f726d;">Original message:</p>
                                <div style="margin-top:10px; padding:18px; border-radius:16px; background-color:#fafcfc; border:1px solid #d9e4e1; font-size:14px; line-height:1.7; color:#5f726d; white-space:pre-line;">{{ $contactMessage->message }}</div>
                                <p style="margin:18px 0 0; font-size:14px; line-height:1.7; color:#5f726d;">If you need anything else, you can reply to this email or reach us again through the website contact page.</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
