@extends('layouts.guest')

@section('title', 'Sign in — '.config('app.name'))

@section('content')
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-lg font-semibold text-white mb-6">Sign in to your account</h2>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="w-full px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-slate-300">
                    <input type="checkbox" name="remember" class="rounded bg-slate-800 border-slate-600 text-indigo-600 focus:ring-indigo-500">
                    Remember me
                </label>
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Create account</a>
            </div>

            <button type="submit"
                    class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition">
                Sign in
            </button>
        </form>
    </div>

    <p class="text-center text-slate-500 text-xs mt-6">
        Demo accounts — admin@support.local / kofi@support.local / abena@support.local (password: password)
    </p>
@endsection
