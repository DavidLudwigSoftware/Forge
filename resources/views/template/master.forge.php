<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    @depends('jquery')
    @stylesheet('styles')
    @javascript('test')
</head>
<body>
    <div class="nav">
        @yield('navigation')
    </div>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
