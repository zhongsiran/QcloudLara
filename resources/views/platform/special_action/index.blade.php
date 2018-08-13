@extends('layouts.app')

{{--  @section('navbar_items')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('platform.home') }}">返回首页</a>
    </li>
@endsection  --}}

@section('content')
@if (count($special_action_list))
@foreach ($special_action_list as $special_action)
<div class="card">
  <div class="card-body">
    <h5 class="card-title">{{$special_action->sp_name}}</h5>
    <h6 class="card-subtitle mb-2 text-muted">行动代号：{{$special_action->sp_num}}</h6>
    <p class="card-text">任务数量：{{$special_action->sp_count}}<br/>完成数量：{{$special_action->sp_finish_count}}</p>
    <a href="{{ route('platform.special_action_detail', ['sp_num' => $special_action->sp_num]) }}" class="btn btn-primary">进入行动</a>
    {{-- <a href="#" class="card-link">导航</a> --}}
  </div>
</div>
@endforeach
@else
<div class="card">
  <div class="card-body">
    <h5 class="card-title">目前没有专项行动</h5>
    <a href="{{ route('platform.home') }}" class="btn btn-primary">返回首页</a>
  </div>
</div>
@endif

{{-- <div style="background-color: white">
    {{$special_action_list->links()}}
</div>
 --}}
@endsection