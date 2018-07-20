<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Sample App') - Laravel tutorial)</title>
        <link rel="stylesheet" type="text/css" href="/css/app.css">

    </head>
    <body>
        @include('layouts._header')

        <div class="container">
            <div class="col-md-offset-1 col-md-10">
                @include('shared._messages')
            </div>
        </div>
        @yield('content')
        <div class="container">
            <div class="col-md-offset-1 col-md-10">
                @include('layouts._footer')
            </div>
        </div>

        <script src="/js/app.js"></script>
    </body>
</html>