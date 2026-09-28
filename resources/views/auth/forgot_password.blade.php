@extends('layouts.auth')
@section('title', 'Forgot Password - Accounting System')
@section('content')
<div class="login-container" style="max-width:600px;">
    <div class="login-right" style="grid-column:1/-1;">
        <div class="login-header">
            <h2>Forgot Password</h2>
            <p>Enter your email to receive a reset link</p>
        </div>
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
        <div class="alert alert-success" style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;">{{ session('success') }}</div>
        @endif
        <form class="login-form" method="POST" action="{{ route('forgot-password') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" id="email" name="email" placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')<div class="input-error show">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="login-btn">Send Reset Link</button>
            <div class="signup-link"><a href="{{ route('login') }}">Back to Login</a></div>
        </form>
    </div>
</div>
<style>.login-container{display:flex;justify-content:center;}.login-right{max-width:500px;}</style>
@endsection
