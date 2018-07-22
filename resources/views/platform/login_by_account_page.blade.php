@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('花都市场监管') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('platform.login_by_account') }}" aria-label="{{ __('花都市场监管') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-8 offset-md-2">
                                @if (isset($err_msg))
                                    <textarea class="form-control" id="introduction" rows="2" readonly>{{$err_msg}}</textarea>
                                @endif    
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_real_name" class="col-sm-4 col-form-label text-md-right">{{ __('真实姓名') }}</label>

                            <div class="col-md-6">
                                <input id="user_real_name" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="user_real_name" value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('密码') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>


                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('保持登录状态') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('登录') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
