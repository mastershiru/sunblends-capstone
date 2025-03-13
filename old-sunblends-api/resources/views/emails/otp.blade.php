<!DOCTYPE html>
<html>
<head>
    <title>Verify Email</title>
</head>
<body>
    <h1>Verify Your Email</h1>

    <form action="{{ url('/verify-otp') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
        </div>
        <button type="submit">Verify</button>
    </form>
</body>
</html>