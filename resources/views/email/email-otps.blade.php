<!DOCTYPE html>
<html>
<head>
    <title>{{$subject}} OTP Verification</title>
</head>
<body style="font-family: Arial, sans-serif;">

    <h2>{{$subject}} - OTP Verification</h2>

    <p>
        We received a request to cancel your order.
    </p>

    <p>
        Please use the OTP below to confirm this action:
    </p>

    <h1 style="color: #e74c3c; letter-spacing: 2px;">
        {{ $otp }}
    </h1>

    <p>This OTP is valid for <strong>5 minutes</strong>.</p>

    <p>
        If you did not request this cancellation, you can safely ignore this email.
    </p>

    <br>

    <p style="color: gray; font-size: 12px;">
        — Team Classic Bakery
    </p>

</body>
</html>