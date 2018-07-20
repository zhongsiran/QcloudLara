<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('title', '监管测试号')</title>
        <link rel="stylesheet" type="text/css" href="/css/app.css">
    </head>
    <body>
        {{-- @include('layouts._header') --}}

        <div class="container">
            {{-- <div class="col-md-offset-1 col-md-10"> --}}
            <div class="flex-center position-ref full-height">
                @include('shared._messages')
                @yield('content')
                {{-- @include('layouts._footer') --}}
            </div>
        </div>

        <script src="/js/app.js"></script>
    </body>
</html>