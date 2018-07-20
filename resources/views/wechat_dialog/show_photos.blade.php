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
      <th scope="row">负责人</th>
      <td>{{ $corp->represent_person }}</td>
    </tr>
    <tr>
      <th scope="row">电话</th>
      <td>{{ $corp->phone }}</td>
    </tr>
    <tr>
      <th scope="row">联络员</th>
      <td>{{ $corp->contact_person }}</td>
    </tr>
    <tr>
      <th scope="row">联络员电话</th>
      <td>{{ $corp->contact_phone }}</td>
    </tr>
  </tbody>
</table>

    @if (count($photo_items))
        @foreach ($photo_items as $photo_item)
            <form method="POST" action="{{ route('delete_photo', ['id' => $photo_item->id]) }}">
                <img src="{{$photo_item->link }}" class="img-fluid border border-secondary rounded " alt="Responsive image" />
                <button type="button" class="btn btn-info">上传时间： {{ $photo_item->updated_at->format('Y-m-d h:i') }}</button>
                @if ($photo_item->uploader == $request->user_openid)
                        {{ csrf_field() }}
                        {{method_field('DELETE')}}
                        <button type="submit" class="btn btn-danger">删除上面的照片</button>
                @endif
            </form>
        @endforeach
    @else
        <h5 class="display-5">当前企业未有照片</h3>
    @endif
@endsection