<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <h1>
        @if(session('msg'))
        {{ session('msg') }}
        @endisset
    </h1>
    <form action="{{ route('create') }}" method="POST">
        @csrf
        <input type="hiden" value="{{ session('tokenMissao') }}">
        <input type="text" name="nickname">
        <input type="email" name="email">
        <input type="password" name="password">
        <input type="submit">
    </form>
</body>
</html>
