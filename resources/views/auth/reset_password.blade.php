@extends('layouts.auth')
@section('title', 'Reset Password - Accounting System')
@section('content')
<div class="login-container" style="max-width:600px;">
    <div class="login-right" style="grid-column:1/-1;">
        <div class="login-header">
            <h2>Reset Password</h2>
            <p>Choose a new password for your account</p>
        </div>
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form class="login-form" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" id="email" name="email" placeholder="your@email.com" value="{{ old('email', $email) }}" required autofocus>
                </div>
                @error('email')<div class="input-error show">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password">New Password <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">Show</button>
                </div>
                @error('password')<div class="input-error show">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter the password" required>
                </div>
            </div>
            <button type="submit" class="login-btn">Reset Password</button>
            <div class="signup-link"><a href="{{ route('login') }}">Back to Login</a></div>
        </form>
    </div>
</div>
<style>.login-container{display:flex;justify-content:center;}.login-right{max-width:500px;}</style>
@endsection
