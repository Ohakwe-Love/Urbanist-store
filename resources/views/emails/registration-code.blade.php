<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Urbanist verification code</title>
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
                                <div style="font-size:28px; line-height:1.2; font-weight:700; color:#ffffff;">Verify your email</div>
                                <p style="margin:12px 0 0; font-size:15px; line-height:1.7; color:#d8e5eb;">Use the code below to continue creating your Urbanist account.</p>
                            </div>
                            <div style="padding:32px;">
                                <p style="margin:0 0 14px; font-size:15px; line-height:1.7; color:#5f726d;">We received a registration request for <strong style="color:#0d2235;">{{ $email }}</strong>.</p>
                                <div style="margin:22px 0; padding:22px 18px; border-radius:18px; background-color:#f1fbf8; border:1px solid rgba(17,179,135,0.24); text-align:center;">
                                    <div style="font-size:12px; letter-spacing:0.2em; text-transform:uppercase; color:#0f9874; margin-bottom:10px;">Verification code</div>
                                    <div style="font-size:34px; line-height:1; letter-spacing:0.38em; font-weight:800; color:#0d2235;">{{ $code }}</div>
                                </div>
                                <p style="margin:0 0 12px; font-size:14px; line-height:1.7; color:#5f726d;">This code expires in {{ $expiresInMinutes }} minutes. Enter it on the registration screen to unlock the account setup form.</p>
                                <p style="margin:0; font-size:14px; line-height:1.7; color:#5f726d;">If you did not request this, you can safely ignore this email.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 8px 0; text-align:center; font-size:12px; line-height:1.7; color:#8b8989;">
                            Urbanist furniture and home styling.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
