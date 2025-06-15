<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Two-Factor Authentication</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .auth-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .auth-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
        }
        .auth-header img {
            height: 48px;
            margin-bottom: 1rem;
        }
        .auth-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
        }
        .auth-body {
            padding: 1.5rem;
        }
        .description {
            margin-bottom: 1.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .alert-success {
            padding: 0.75rem;
            border-radius: 0.375rem;
            background-color: #ecfdf5;
            color: #047857;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .alert-danger {
            padding: 0.75rem;
            border-radius: 0.375rem;
            background-color: #fef2f2;
            color: #b91c1c;
            margin-bottom: 1rem;
        }
        .alert-danger ul {
            list-style-type: disc;
            list-style-position: inside;
            margin-top: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .form-input {
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out;
        }
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        .form-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.25rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            float: right;
        }
        .form-submit:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <img src="{{ asset('favicon.ico') }}" alt="Logo">
            <h1>Two-Factor Authentication</h1>
        </div>

        <div class="auth-body">
            <div class="description">
                We have sent a verification code to your email address. Please enter the code to log in.
            </div>

            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-danger">
                    <strong>Whoops! Something went wrong.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.challenge') }}">
                @csrf
                <div class="form-group">
                    <label for="code" class="form-label">Code</label>
                    <input id="code" class="form-input" type="text" name="code" required autofocus autocomplete="one-time-code">
                </div>

                <button type="submit" class="form-submit">
                    Log in
                </button>
            </form>
        </div>
    </div>
</body>
</html>
