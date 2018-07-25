@extends('layouts.app')

@section('navbar_items')
<li class="nav-item">
    <a class="nav-link" href="{{ route('platform.daily_search_form') }}">返回搜索</a>
</li>
@endsection

@section('content')
@if (count($result_corps))
@foreach ($result_corps as $corp)
<div class="card">
  <div class="card-body">
    <h5 class="card-title">{{$corp->corporation_name}}</h5>
    <h6 class="card-subtitle mb-2 text-muted">{{$corp->address}}</h6>
    <p class="card-text">注册号：{{$corp->registration_num}}<br/>法人：{{$corp->represent_person}} <br/></p>
    <a href="{{ route('platform.daily_corp_detail', ['corporation_name' => $corp->corporation_name]) }}" class="card-link">详情</a>
    <a href="#" class="card-link">导航</a>
  </div>
</div>
@endforeach
@else
<div class="card">
  <div class="card-body">
    <h5 class="card-title">没有找到相关企业</h5>
    <a href="{{ route('platform.daily_search_form') }}" class="card-link">返回更改条件再次搜索</a>
  </div>
</div>
@endif

{{$result_corps->links()}}

@endsection