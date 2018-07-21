@extends('layouts.app')

@section('head_supplement')
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css"/>
    {{-- <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css"> --}}
@endsection

@section('content')

<div class="weui-panel weui-panel_access">
    <div class="weui-panel__hd">选择功能模块</div>
    <div class="weui-panel__bd">
        <a href="{{ route('platform.daily') }}" class="weui-media-box weui-media-box_appmsg">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="https://sinacloud.net/aicbucket/AICBADGE.jpg" alt="">
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title">日常监管</h4>
                <p class="weui-media-box__desc">用于无特定名单的日常核查。</p>
            </div>
        </a>
        <a href="{{ route('platform.special_action') }}" class="weui-media-box weui-media-box_appmsg">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="https://sinacloud.net/aicbucket/AICBADGE.jpg" alt="">
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title">专项核查</h4>
                <p class="weui-media-box__desc">针对特定名单内的商事主体进行核查。</p>
            </div>
        </a>
        <!--
        <a href="/public/Wuzhao/H5wuzhao_index.php" class="weui-media-box weui-media-box_appmsg">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="https://sinacloud.net/aicbucket/AICBADGE.jpg" alt="">
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title">无照整治</h4>
                <p class="weui-media-box__desc">对无照责令进行记录、跟踪，形成处理闭环。</p>
            </div>
        </a>
        -->
    </div>
    
{{--     <div class="weui-panel__ft">
        <a href="javascript:void(0);" class="weui-cell weui-cell_access weui-cell_link">
            <div class="weui-cell__bd">更多功能仍在开发中</div>
            <span class="weui-cell__ft"></span>
        </a>    
    </div> --}}
    
</div>
@endsection
