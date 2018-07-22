@extends('layouts.default')
@section('title', $corp->corporation_name)

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

@foreach ($photo_items as $photo_item)
    <form style="margin:unset;" method="POST" action="{{ route('corp_photos.delete', ['id' => $photo_item->id]) }}">
        <img src="{{ $signed_url_list[$photo_item->id] }}" class="img-fluid border border-secondary rounded " alt="Responsive image" />
        <button type="button" class="btn btn-info">上传时间： {{ $photo_item->updated_at->format('Y-m-d h:i') }}</button>
        @if ($photo_item->uploader == $user_openid)
            {{ csrf_field() }}
            {{method_field('DELETE')}}
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeletePhoto{{ $photo_item->id }}">删除上面的照片</button>
            
            <confirm-delete-photo photo-id="{{ $photo_item->id }}"></confirm-delete-photo>    
            
            
        @endif
    </form>
@endforeach

@endsection