{{-- Shared shell for the HTTP error pages. The status files pass code/title/message in.

     These pages are the one place in the app that never sees the dashboard
     layout, so they carry the fluid scale themselves: a 404 opened on a phone
     has to size its type from the viewport like every other screen does. --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} - {{ $title }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @include('layouts._type-scale')
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 78.125%; }
        body {
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: var(--fs-1-6);
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--error-pad, 24px);
        }
        .error-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            padding: clamp(24px, 6vw, 56px);
            max-width: 560px;
            width: 100%;
            text-align: center;
        }
        .error-code { font-size: var(--fs-3-6); font-weight: 700; color: #CD2737; line-height: 1.1; }
        h1 { font-size: var(--fs-2-4); font-weight: 700; color: #1a1a2e; margin: 12px 0 8px; }
        .error-message { font-size: var(--fs-1-5); color: #6c757d; margin-bottom: 28px; }
        .error-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: var(--fs-1-4);
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-primary { background: #CD2737; color: white; box-shadow: 0 4px 15px rgba(205, 39, 55, 0.3); }
        .btn-secondary { background: #e9ecef; color: #333; }
        .error-foot { margin-top: 24px; font-size: var(--fs-1-15); color: #999; }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="error-code">{{ $code }}</div>
        <h1>{{ $title }}</h1>
        <p class="error-message">{{ $message }}</p>
        <div class="error-actions">
            <a class="btn btn-primary" href="{{ url('/') }}">Go home</a>
            <a class="btn btn-secondary" href="javascript:history.back()">Go back</a>
        </div>
        <p class="error-foot">{{ config('app.name') }}</p>
    </main>
</body>
</html>
