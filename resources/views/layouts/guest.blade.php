<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-600 flex items-center justify-center font-bold text-white text-xl mb-4 shadow-lg shadow-indigo-600/30">AS</div>
            <h1 class="text-2xl font-bold text-white">{{ config('app.name') }}</h1>
            <p class="text-slate-400 text-sm mt-1">Track daily support team activities</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-500/10 border border-rose-500/30 p-4">
                <ul class="text-sm text-rose-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
