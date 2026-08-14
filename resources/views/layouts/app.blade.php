<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased min-h-screen flex">
    <aside class="hidden lg:flex flex-col w-64 bg-slate-900 text-slate-200 shrink-0 min-h-screen fixed inset-y-0">
        <div class="px-6 py-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white">AS</div>
                <div>
                    <p class="font-semibold text-white leading-tight">Activity Tracker</p>
                    <p class="text-xs text-slate-400">Applications Support</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1">
            @php
                $links = [
                    ['href' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h14V10', 'active' => request()->routeIs('dashboard')],
                    ['href' => route('tracker.daily'), 'label' => 'My Daily Tracker', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'active' => request()->routeIs('tracker.*')],
                    ['href' => route('handover.show'), 'label' => 'Daily Handover', 'icon' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1', 'active' => request()->routeIs('handover.*')],
                    ['href' => route('reports.index'), 'label' => 'Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'active' => request()->routeIs('reports.*')],
                ];
            @endphp

            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ $link['active'] ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach

            @auth
                @if (auth()->user()->isAdmin())
                    <div class="pt-6 pb-2 px-3">
                        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Administration</p>
                    </div>
                    <a href="{{ route('activities.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                              {{ request()->routeIs('activities.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Manage Activities
                    </a>
                @endif
            @endauth
        </nav>

        <div class="px-6 py-4 border-t border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-semibold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->department ?? '—' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white" title="Sign out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 lg:ml-64 min-w-0">
        <header class="lg:hidden bg-slate-900 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-10">
            <p class="font-semibold">Activity Tracker</p>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-300">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-slate-300 hover:text-white text-sm">Sign out</button>
                </form>
            </div>
        </header>

        <nav class="lg:hidden bg-white border-b border-slate-200 px-2 py-1.5 flex gap-1 overflow-x-auto text-sm">
            @foreach ($links ?? [] as $link)
                <a href="{{ $link['href'] }}"
                   class="px-3 py-1.5 rounded-md whitespace-nowrap font-medium {{ $link['active'] ? 'bg-indigo-600 text-white' : 'text-slate-600' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('activities.index') }}"
                       class="px-3 py-1.5 rounded-md whitespace-nowrap font-medium {{ request()->routeIs('activities.*') ? 'bg-indigo-600 text-white' : 'text-slate-600' }}">
                        Activities
                    </a>
                @endif
            @endauth
        </nav>

        <main class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
            @if (session('status'))
                <div class="mb-6 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
