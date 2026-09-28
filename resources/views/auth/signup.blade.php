@extends('layouts.auth')
@section('title', 'Register - Accounting System')
@section('content')
<div class="signup-container">
    <div class="signup-left">
        <div class="brand-content">
            <div class="logo-container"><div class="logo"><span class="logo-text">AS</span></div></div>
            <h1 class="brand-title">Accounting System</h1>
            <p class="brand-tagline">Complete accounting solution for your business</p>
            <ul class="brand-benefits">
                <li>Track all financial transactions</li>
                <li>Generate professional invoices</li>
                <li>Manage clients and suppliers</li>
                <li>Real-time financial reports</li>
            </ul>
        </div>
    </div>
    <div class="signup-right">
        <div class="signup-header">
            <h2>Create Account</h2>
            <p>Register to get started</p>
        </div>
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form class="signup-form" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" id="name" name="name" placeholder="John Doe" value="{{ old('name') }}" required>
                    </div>
                    @error('name')<div class="input-error show">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" placeholder="your@email.com" value="{{ old('email') }}" required>
                    </div>
                    @error('email')<div class="input-error show">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">Show</button>
                    </div>
                    @error('password')<div class="input-error show">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">Show</button>
                    </div>
                </div>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>
            <button type="submit" class="signup-btn">Create Account</button>
            <div class="login-link">Already have an account? <a href="{{ route('login') }}">Sign In</a></div>
        </form>
    </div>
</div>
@endsection
