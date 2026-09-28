@extends('layouts.auth')
@section('title', 'No Access - Accounting System')
@section('content')
<div class="login-container" style="max-width:600px;">
    <div class="login-right" style="grid-column:1/-1;">
        <div class="login-header">
            <h2>पहुँच छैन</h2>
            <p>तपाईंको भूमिकालाई अहिले कुनै पृष्ठ खोल्ने अनुमति दिइएको छैन।</p>
        </div>

        <div class="alert alert-warning" style="background:#fff3cd;color:#856404;border:1px solid #ffeeba;">
            तपाईंको खाता सफल भएर लगइन भएको छ, तर तपाईंको भूमिका अझै कुनै अनुमति पाएको छैन।
            प्रशासकलाई भूमिका सेट गर्न अनुरोध गर्नुहोस्।
        </div>

        <div style="color:#555;line-height:1.7;margin:18px 0;">
            <p style="margin:0 0 8px;">तपाईंको भूमिका: <strong>{{ $roleName ?? 'प्रयोगकर्ता' }}</strong></p>
            @if($roleDescription)
            <p style="margin:0;">विवरण: {{ $roleDescription }}</p>
            @endif
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('logout') }}" class="login-btn" style="text-decoration:none;display:inline-block;">लगआउट</a>
        </div>
    </div>
</div>
<style>.login-container{display:flex;justify-content:center;}.login-right{max-width:500px;}</style>
@endsection
