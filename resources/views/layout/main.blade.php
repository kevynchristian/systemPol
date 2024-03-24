<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <style>
        h1{
            color: red;
        }
    </style>
</head>
<body>

    @yield('content')


<script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
