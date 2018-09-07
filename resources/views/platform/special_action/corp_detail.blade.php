@extends('layouts.app') 
@section('title', $sp_item->sp_name) 
@section('navbar_items')
<li class="nav-item">
    <a class="nav-link" href="{{ session()->get('corp_list_url') .'#'. $sp_item->sp_corp_id }}">返回名单</a>
</li>
@endsection
 
@section('head_supplement')
<script src="{{ mix('js/all.js') }}"></script>
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
            <td>{{ $corp->represent_person }} - <a href="tel:{{$corp->phone}}" class="tel">{{ $corp->phone }}</a></td>
        </tr>
        <tr>
            <th scope="row">联络员及电话</th>
            <td>{{ $corp->contact_person }} - <a href="tel:{{$corp->contact_phone}}" class="tel">{{ $corp->contact_phone }}</a></td>
        </tr>
    </tbody>
</table>

<special-action-form></special-action-form>
<general-form-layout-corp-detail></general-form-layout-corp-detail>
<special-action-done-and-undone-button></special-action-done-and-undone-button>

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

    });
</script>
@endsection