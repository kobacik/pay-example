<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            background-color: #2b2b2b;
            color: #fafafa;
        }
    </style>
    <title>Fee's</title>
</head>
<body>
    @foreach($fees as $fee)
       {{ $loop->iteration }} - {{ $fee }} <br>
    @endforeach
</body>
</html>
