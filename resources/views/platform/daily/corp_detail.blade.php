@extends('layouts.app')

@section('navbar_items')
    {{--  <li class="nav-item">  --}}
        <a class="nav-link" href="{{ session()->get('daily_corp_list_url') }}">结果列表</a>
    {{--  </li>
    <li class="nav-item">  --}}
        <a class="nav-link" href="{{ route('platform.daily_search_form') }}">搜索页面</a>
    {{--  </li>  --}}
@endsection

@section('head_supplement')
<script src="{{ mix('js/all.js') }}"></script>
{{--  <script>window.showPhoto = false</script>  --}}
@endsection

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">属性</th>
            <th scope="col">内容</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th scope="row">注册号</th>
            <td>{{ $corp->registration_num }}</td>
        </tr>
        <tr>
            <th scope="row">名称</th>
            <td>{{ $corp->corporation_name }}</td>
        </tr>
        <tr>
            <th scope="row">地址</th>
            <td>{{ $corp->address }}</td>
        </tr>
        <tr>
            <th scope="row">负责人及电话</th>
            <td>{{ $corp->represent_person }} - {{ $corp->phone }}</td>
        </tr>
        <tr>
            <th scope="row">联络员及电话</th>
            <td>{{ $corp->contact_person }} - {{ $corp->contact_phone }}</td>
        </tr>
        <tr>
            <th scope="row">电话记录</th>
            <td>{{ $corp->phone_call_record }}</td>
        </tr>
        <tr>
            <th scope="row">核查备注</th>
            <td>{{ $corp->inspection_status }}</td>
        </tr>        
        <tr>
            <th scope="row">现有图片</th>
            <td> 
                @if (count($photo_items))
                    {{ count($photo_items) }}
                @else
                当前企业未上传照片
            @endif</td>
        </tr>
    </tbody>
</table>

<general-form-layout-corp-detail></general-form-layout-corp-detail>
<div id='response' class="flash-message">
</div>

@if (count($photo_items))
    <general-show-photos-toggle v-if="hide_photo"></general-show-photos-toggle>
    <div v-else>
        <general-show-photos-toggle></general-show-photos-toggle>
        <general-show-photos v-for="photo_item in photo_items" 
                            :photo_item="photo_item" 
                            :key="photo_item.id" 
                            user_openid="{{$user_openid}}"
        >
        </general-show-photos>
    </div>
@endif


@endsection

@section('footer_supplement')
<script>
    wx.config( {!! $jssdk_config !!} );
    let user_openid = '{{$user_openid}}';
    wx.ready(function () {
        // WxChooseAndUploadImages()
    });

</script>
@endsection