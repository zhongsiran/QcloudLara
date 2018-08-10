@extends('layouts.app') 
@section('title', $sp_item->sp_name) 
@section('navbar_items')
<li class="nav-item">
    <a class="nav-link" href="{{ $corp_list_url .'#'. $sp_item->sp_corp_id }}">返回名单</a>
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
            <td>{{ $corp->represent_person }} - {{ $corp->phone }}</td>
        </tr>
        <tr>
            <th scope="row">联络员及电话</th>
            <td>{{ $corp->contact_person }} - {{ $corp->contact_phone }}</td>
        </tr>
    </tbody>
</table>

<special-action-form></special-action-form>
<general-form-layout-corp-detail></general-form-layout-corp-detail>

<div id='response' class="flash-message">
</div>

<done-and-undone-button></done-and-undone-button>

@foreach ($photo_items as $photo_item)
<form style="margin:unset;" method="POST" action="{{ route('corp_photos.delete', ['id' => $photo_item->id]) }}">
    <img src="{{ $signed_url_list[$photo_item->id] }}" class="img-fluid border border-secondary rounded " alt="Responsive image"
    />
    <button type="button" class="btn btn-info">上传时间： {{ $photo_item->updated_at->format('Y-m-d h:i') }}</button> @if ($photo_item->uploader
    == $user_openid) {{ csrf_field() }} {{method_field('DELETE')}}
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeletePhoto{{ $photo_item->id }}">删除照片</button>

    <confirm-delete-photo photo-id="{{ $photo_item->id }}"></confirm-delete-photo>

    @endif
</form>
@endforeach

@endsection
 
@section('footer_supplement')
<script>
    wx.config( {!! $jssdk_config !!} );
    let user_openid = '{{$user_openid}}';
    wx.ready(function () {

    });
</script>
@endsection