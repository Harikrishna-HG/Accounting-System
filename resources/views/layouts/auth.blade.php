<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Accounting System')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @include('layouts._type-scale')
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { font-size: 78.125%; }

        body {
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size:var(--fs-1-6);
            background: linear-gradient(135deg, #dc3545, #c82333);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container,
        .signup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            animation: slideIn 0.5s ease-out;
            max-height: calc(100vh - 40px);
            min-height: 0;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-left,
        .signup-left {
            background: linear-gradient(135deg, #dc3545, #c82333);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 0;
        }

        .login-left::before,
        .signup-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .logo-container { margin-bottom: 30px; }

        .logo {
            width: 64px;
            height: 64px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .logo-text {
            font-size:var(--fs-2-4);
            font-weight: 700;
            color: #dc3545;
        }

        .brand-title {
            font-size:var(--fs-2-8);
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .brand-tagline {
            font-size:var(--fs-1-4);
            opacity: 0.95;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .brand-features,
        .brand-benefits {
            list-style: none;
            text-align: left;
            margin-top: 20px;
        }

        .brand-features li,
        .brand-benefits li {
            padding: 8px 0;
            font-size:var(--fs-1-3);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-features li::before,
        .brand-benefits li::before {
            content: '✓';
            background: rgba(255,255,255,0.2);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .login-right,
        .signup-right {
            padding: 28px 36px;
            overflow-y: auto;
            height: 100%;
            min-height: 0;
        }

        .login-right::-webkit-scrollbar,
        .signup-right::-webkit-scrollbar {
            width: 6px;
        }

        .login-right::-webkit-scrollbar-track,
        .signup-right::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 10px;
        }

        .login-right::-webkit-scrollbar-thumb,
        .signup-right::-webkit-scrollbar-thumb {
            background: #dc3545;
            border-radius: 10px;
        }

        .login-header,
        .signup-header {
            margin-bottom: 20px;
        }

        .login-header h2,
        .signup-header h2 {
            font-size:var(--fs-2-2);
            color: #333;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .login-header p,
        .signup-header p {
            color: #666;
            font-size:var(--fs-1-4);
        }

        .login-form,
        .signup-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            font-size:var(--fs-1-3);
            margin-left: 4px;
        }

        .form-group label .required {
            color: #dc3545;
            margin-left: 2px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size:var(--fs-1-6);
            pointer-events: none;
        }

        .form-group input {
            padding: 12px 40px 12px 40px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            width: 100%;
            font-size:var(--fs-1-4);
            font-family: 'Noto Sans Devanagari', sans-serif;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            font-size:var(--fs-1-4);
            line-height: 1;
            color: #999;
            z-index: 2;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #333;
        }

        .form-group input:focus {
            outline: none;
            border-color: #dc3545;
            background: white;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .input-error {
            font-size:var(--fs-1-1);
            color: #dc3545;
            margin-top: 2px;
            display: none;
        }

        .input-error[style*="display:block"],
        .input-error.show {
            display: block;
        }

        .form-group input.error {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .login-btn,
        .signup-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size:var(--fs-1-5);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Noto Sans Devanagari', sans-serif;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .login-btn:hover,
        .signup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        }

        .login-btn:active,
        .signup-btn:active {
            transform: translateY(0);
        }

        .login-btn:disabled,
        .signup-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .login-btn.loading,
        .signup-btn.loading {
            position: relative;
            color: transparent;
        }

        .login-btn.loading::after,
        .signup-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 14px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            color: #999;
            font-size:var(--fs-1-3);
        }

        .social-login,
        .social-signup {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            background: white;
            color: #666;
            font-size:var(--fs-1-3);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .social-btn:hover {
            border-color: #dc3545;
            color: #dc3545;
            background: #fff5f5;
        }

        .social-icon {
            width: 20px;
            height: 20px;
        }

        .signup-link,
        .login-link {
            text-align: center;
            margin-top: 16px;
            color: #666;
            font-size:var(--fs-1-3);
        }

        .signup-link a,
        .login-link a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .signup-link a:hover,
        .login-link a:hover {
            color: #c82333;
            text-decoration: underline;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            font-size:var(--fs-1-4);
            border-left: 4px solid #dc3545;
            display: none;
            margin-bottom: 16px;
        }

        .error-message.show {
            display: block;
            animation: shake 0.5s;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 8px;
            font-size:var(--fs-1-4);
            border-left: 4px solid #28a745;
            display: none;
            margin-bottom: 16px;
        }

        .success-message.show {
            display: block;
            animation: slideIn 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert {
            padding: 10px 12px;
            height: auto;
            min-height: 40px;
            width: 100%;
            border: 1px solid #f44336;
            border-radius: 6px;
            color: white;
            margin-bottom: 10px;
            animation: slideIn 0.5s ease-out;
            font-size:var(--fs-1-3);
            display: flex;
            align-items: center;
        }

        .alert-danger {
            background-color: #f44336;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: -4px 0 8px 0;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #dc3545;
        }

        .remember-me label {
            font-size:var(--fs-1-3);
            color: #666;
            cursor: pointer;
            font-weight: 400;
        }

        .forgot-password {
            color: #dc3545;
            text-decoration: none;
            font-size:var(--fs-1-3);
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #c82333;
            text-decoration: underline;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 6px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
            accent-color: #dc3545;
            flex-shrink: 0;
        }

        .checkbox-group label {
            font-size:var(--fs-1-4);
            color: #666;
            cursor: pointer;
            font-weight: 400;
            line-height: 1.5;
        }

        .checkbox-group label a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
        }

        .checkbox-group label a:hover {
            text-decoration: underline;
        }

        .password-strength {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #dc3545;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #ffc107;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: #28a745;
        }

        .password-hint {
            font-size:var(--fs-1-2);
            color: #666;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 768px) {
            .login-container,
            .signup-container {
                grid-template-columns: 1fr;
                max-height: none;
            }

            .login-left,
            .signup-left {
                display: none;
            }

            .login-right,
            .signup-right {
                padding: 28px 24px;
            }

            .login-header h2,
            .signup-header h2 {
                font-size:var(--fs-2);
            }

            .social-login,
            .social-signup {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            body { padding: 10px; }

            .login-right,
            .signup-right {
                padding: 20px 16px;
            }

            .login-header h2,
            .signup-header h2 {
                font-size:var(--fs-1-8);
            }

            .brand-title { font-size:var(--fs-2-2); }

            .form-group input { font-size:var(--fs-1-4); padding: 10px 36px 10px 36px; }

            .form-row { grid-template-columns: 1fr; }
        }
    </style>
    @yield('styles')
</head>
<body>
    @yield('content')
    <script>
    function togglePassword(id, btn) {
        var input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = 'Hide';
        } else {
            input.type = 'password';
            btn.textContent = 'Show';
        }
    }
    </script>
    @yield('scripts')
</body>
</html>

