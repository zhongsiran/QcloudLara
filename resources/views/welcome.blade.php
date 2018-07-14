<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>我的学习</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
        <link rel="stylesheet" type="text/css" href="/css/app.css">
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    我的Laravel学习
                </div>

                <div class="links">
                    <a href="http://cpc.people.com.cn/">中国共产党新闻网</a>
                    <a href="http://www.gov.cn/">中国政府网</a>
                    <a href="http://www.gmw.cn/">光明网</a>
                    <a href="http://www.southcn.com/">南方网</a>
                    <a href="https://laravel-china.org/">Laravel 中国</a>
                </div>
<!--                 <div class="footer navbar-fixed-bottom">
                  <div class="links">
                        <a href="http://www.miitbeian.gov.cn/">粤ICP备17077902号-1</a>
                    </div>
                </div> -->
                <footer class="footer">
                    <div class="links">
                        <a href="http://www.miitbeian.gov.cn/">粤ICP备17077902号-1</a>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
