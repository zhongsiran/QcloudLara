
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
        <div class="row">
            <div class="container">
                <div class="jumbotron col-md-12 text-center" style="background-color: white;">
                    <div id="badges" class="weui-flex justify">
                        <img src="https://sinacloud.net/aicbucket/AICBADGE.jpg" class="weui-flex-item">
                        <img src="https://sinacloud.net/aicbucket/QUABADGE%20%281%29.jpg" class="weui-flex-item">
                    </div>
                    <h1>狮岭所年报工作系统</h1>
                </div>
            </div>
            <div class="row">
                <div class="container-fluid">
                    <div class="jumbotron col-md-6 text-center">
                        <h1>录入模块</h1>
                        <p>进入模块录入企业联系情况</p>
                        <form method="GET" action="{{route('nb_table')}}">
                            <div class="form-group">
                                <label for="corp_type">选择名单：</label>
                                <select name="corp_type" class="form-control">
                                    <option>2017年新增</option>
                                    <option>2016年正常</option>
                                    <option>2016年未报</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="division">选择片区：</label>
                                <select name="division" class="form-control">
                                    <option>中心一区</option>
                                    <option>中心二区</option>
                                    <option>中心三区</option>
                                    <option>中心四区</option>
                                    <option>中心五区</option>
                                    <option>中心六区</option>
                                    <option>西一片区</option>
                                    <option>西二片区</option>
                                    <option>西三片区</option>
                                </select>
                            </div>
                            <input type="submit" class="btn btn-primary btn-lg" role="button" value="点击进入">
                        </form>
                    </div>
                    <div class="jumbotron col-md-6 text-center" style="height: 472px;">
                        <h1>统计模块</h1>
                        <p>查看年报统计情况</p>
                        <p><a class="btn btn-primary btn-lg" href="#" role="button">点击进入</a></p>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts._footer')
        <script src="/js/app.js"></script>
    </body>
    </html>