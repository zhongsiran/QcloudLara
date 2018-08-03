@extends('layouts.app')

@section('head_supplement')
{{-- <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css"/> --}}
{{-- <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css"> --}}

<script src="{{ asset('js/all.js') }}" defer></script>
@endsection

@section('navbar_items')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('platform.home') }}">返回首页</a>
    </li>
@endsection

@section('content')

<form action="{{ route('platform.daily_fetch_corp') }}" method="get">
  @csrf
  <div class="form-group">
    <label for="introduction">说明</label>
    <textarea class="form-control" id="introduction" rows="2" readonly>选择监管所，输入注册号\名称\地址等进行搜索。</textarea>
    <label for="corporation_aic_division">监管所</label>
    <select name="corporation_aic_division" class="form-control" id="corporation_aic_division" value="">
        <option value="SL">狮岭</option>
        <option value="FR">芙蓉</option>
        <option value="TB">炭步</option>
        <option value="YH">裕华</option>
        <option value="XH">新华</option>
        <option value="HC">花城</option>
        <option value="XQ">秀全</option>
        <option value="XY">新雅</option>
        <option value="HS">花山</option>
        <option value="HD">花东</option>
        <option value="CN">赤坭</option>
        <option value="TM">梯面</option>
    </select>
</div>
<div class="form-group">
    <label for="registration_num">注册号</label>
    <input name="registration_num" type="text" class="form-control" id="registration_num" placeholder="440121000xxxxxx">
</div>
<div class="form-group">
    <label for="corporation_name">名称（可选）</label>
    <input name="corporation_name" type="text" class="form-control" id="corporation_name" placeholder="XXXX有限公司">
</div>
<div class="form-group">
    <label for="address">地址（可选）</label>
    <input name="address" type="text" class="form-control" id="address" placeholder="XX路XX号">
</div>
<div class="form-group">
    <label for="represent_person">负责人（可选）</label>
    <input name="represent_person" type="text" class="form-control" id="represent_person" placeholder="">
</div>
    <button type="submit" class="btn btn-block btn-primary">开始搜索</button>
</form>

@endsection
