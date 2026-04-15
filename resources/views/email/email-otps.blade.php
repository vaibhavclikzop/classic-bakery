<!DOCTYPE html>
<html>
<head>
    <title>{{$subject}}   OTP</title>
</head>
<body>
    <h2>{{$subject}}  Verification</h2>

    <p>Your OTP for {{$subject}} : </p>

    <h1 style="color:red;">{{ $otp }}</h1>

    <p>This OTP is valid for 5 minutes.</p>

    <p>If you did not request this, please ignore this email.</p>
</body>
</html>
