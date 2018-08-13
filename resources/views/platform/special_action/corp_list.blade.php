@extends('layouts.app')
@section('title', $special_action_corps_list[0]['sp_name'])

@section('navbar_items')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('platform.special_action') }}">返回专项行动列表</a>
    </li>
@endsection

@section('content')
@if (count($special_action_corps_list))

@foreach ($special_action_corps_list as $corp)
<div 
  @if ($corp->finish_status == '已经完成')
    class="card text-white bg-info"
  @else
    class="card"
  @endif
>
  <div id="{{$corp->sp_corp_id}}" class="card-body">
    <h5 class="card-title">{{$corp->detail->corporation_name}} {{$corp->finish_status}}</h5>
    <h6 class="card-subtitle mb-2">序号：{{$corp->sp_corp_id}}</h6>
    <p class="card-text">注册号：{{$corp->detail->registration_num}} <br/>
                         地址：{{$corp->detail->address}}<br/>
                         电话：{{$corp->detail->phone}}</br>
                         组别：{{$corp->sp_responsible_group}}</p>
    <a href="{{ route('platform.special_action.corp_detail', ['id' => $corp->id]) }}" class="btn btn-info">详情</a>
    {{-- <a href="#" class="card-link">导航</a> --}}
  </div>
</div>
@endforeach
@else
<div class="card">
  <div class="card-body">
    <h5 class="card-title">本行动无对应的企业</h5>
    <a href="{{ route('platform.special_action') }}" class="card-link">返回专项行动列表</a>
  </div>
</div>
@endif

<div style="background-color: white">
  {{$special_action_corps_list->links()}}
</div>

@endsection