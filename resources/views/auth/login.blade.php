@extends('layouts.auth')
@section('title', 'Login - Accounting System')
@section('content')
<div class="login-container">
    <div class="login-left">
        <div class="brand-content">
            <div class="logo-container"><div class="logo"><span class="logo-text">AS</span></div></div>
            <h1 class="brand-title">Accounting System</h1>
            <p class="brand-tagline">Manage invoices, payments, expenses, and financial reports</p>
            <ul class="brand-features">
                <li>Invoice Management with VAT/Service Charge</li>
                <li>Client & Supplier Management</li>
                <li>Payment & Expense Tracking</li>
                <li>Financial Reports & Ledger</li>
                <li>Product Inventory Management</li>
            </ul>
        </div>
    </div>
    <div class="login-right">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Sign in to your account</p>
        </div>
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
        <div class="alert alert-success" style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;">{{ session('success') }}</div>
        @endif
        <form class="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" id="email" name="email" placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')<div class="input-error show">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">Show</button>
                </div>
                @error('password')<div class="input-error show">{{ $message }}</div>@enderror
            </div>
            <div class="form-options">
                <label class="remember-me"><input type="checkbox" name="remember" value="1"> <label for="remember">Remember me</label></label>
                <a href="{{ route('forgot-password') }}" class="forgot-password">Forgot Password?</a>
            </div>
            <button type="submit" class="login-btn">Sign In</button>
            <div class="signup-link">Need an account? Contact your administrator.</div>
        </form>
    </div>
</div>
@endsection
