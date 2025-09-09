{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <meta http-equiv="refresh" content="3;url={{ route('login') }}">--}}
{{--    <title>419 | Page Expired</title>--}}
{{--    <style>--}}
{{--        body {--}}
{{--            background-color: #2d2d2d;--}}
{{--            color: #ccc;--}}
{{--            font-family: Arial, sans-serif;--}}
{{--            display: flex;--}}
{{--            justify-content: center;--}}
{{--            align-items: center;--}}
{{--            height: 100vh;--}}
{{--            margin: 0;--}}
{{--        }--}}
{{--        .message {--}}
{{--            text-align: center;--}}
{{--        }--}}
{{--        .message h1 {--}}
{{--            font-size: 48px;--}}
{{--        }--}}
{{--        .message p {--}}
{{--            font-size: 18px;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<div class="message">--}}
{{--    <h1>419 | Page Expired</h1>--}}
{{--    <p>Your session has expired. You will be redirected to the login page shortly.</p>--}}
{{--    <p>If not redirected, <a href="{{ route('login') }}" style="color: #fff; text-decoration: underline;">click here</a>.</p>--}}
{{--</div>--}}
{{--</body>--}}
{{--</html>--}}
@if (!session()->has('username'))
    <script>
        window.location.href = "{{ route('login') }}"
    </script>
@endif