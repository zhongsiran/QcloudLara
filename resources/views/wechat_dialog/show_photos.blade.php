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
  </tbody>
</table>

    @if (count($photo_items))
        @foreach ($photo_items as $photo_item)
            <form method="POST" action="{{ route('delete_photo', ['id' => $photo_item->id]) }}">
                <img src="{{ $signed_url_list[$photo_item->id] }}" class="img-fluid border border-secondary rounded " alt="Responsive image" />
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModalCenter">上传时间： {{ $photo_item->updated_at->format('Y-m-d h:i') }}</button>
                @if ($photo_item->uploader == $user_openid)
                        {{ csrf_field() }}
                        {{method_field('DELETE')}}
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalCenter">删除上面的照片</button>
                @endif
            </form>
        @endforeach
    @else
        <h5 class="display-5">当前企业未有照片</h3>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle">确认</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            请确认是否要删除此照片
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
            <button type="button" class="btn btn-primary">确认删除</button>
          </div>
        </div>
      </div>
    </div>
@endsection