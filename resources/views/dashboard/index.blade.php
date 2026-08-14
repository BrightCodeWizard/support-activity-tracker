@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}!</h1>
        <p class="text-slate-500 text-sm mt-1">{{ $today->format('l, j F Y') }} — overview of today's support activities.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Done today</p>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold mt-2 text-emerald-600">{{ $stats['done'] }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pending today</p>
                <span class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold mt-2 text-amber-600">{{ $stats['pending'] }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Team reporting</p>
                <span class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold mt-2 text-indigo-600">{{ $stats['reporting'] }}<span class="text-base font-medium text-slate-400">/{{ $stats['totalUsers'] }}</span></p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">My pending</p>
                <span class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold mt-2 text-slate-800">{{ $myPendingCount }}<span class="text-base font-medium text-slate-400">/{{ $myAssignedCount }}</span></p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-5 mb-6">
        <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-semibold text-slate-900">Handover queue — pending items</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pending work that must be picked up before close of day.</p>
                </div>
                <a href="{{ route('handover.show') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View handover &rarr;</a>
            </div>

            @forelse ($handoverItems as $item)
                <div class="flex items-start gap-4 py-3 {{ ! $loop->last ? 'border-b border-slate-100' : '' }}">
                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-semibold text-sm shrink-0">
                        {{ strtoupper(substr($item->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <p class="font-medium text-slate-800 text-sm">{{ $item->activity->name }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">PENDING</span>
                        </div>
                        @if ($item->remark)
                            <p class="text-xs text-slate-500 mt-0.5 leading-snug">{{ $item->remark }}</p>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $item->user->name }} · {{ $item->user->staff_id }} · {{ $item->updated_at->format('g:i A') }}
                            @if ($item->user->phone) · {{ $item->user->phone }} @endif
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-slate-400 text-sm">No pending handover items today.</p>
                </div>
            @endforelse
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-6">Updates — last 7 days</h2>
            <div class="flex items-end justify-between gap-3 h-40">
                @foreach ($weeklyTrend as $day)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                        <div class="w-full flex flex-col justify-end gap-0.5 rounded-lg overflow-hidden h-28">
                            @if ($day['done'] + $day['pending'] > 0)
                                <div class="w-full bg-emerald-400 transition-all" style="height: {{ $day['done'] / max($day['done'] + $day['pending'], 1) * 100 }}%"></div>
                                <div class="w-full bg-amber-400 transition-all" style="height: {{ $day['pending'] / max($day['done'] + $day['pending'], 1) * 100 }}%"></div>
                            @else
                                <div class="w-full bg-slate-100 h-1 mt-auto"></div>
                            @endif
                        </div>
                        <div class="text-center">
                            <p class="text-[11px] font-medium text-slate-600">{{ $day['label'] }}</p>
                            <p class="text-[10px] text-slate-400">{{ $day['done'] + $day['pending'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 flex items-center justify-center gap-6 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-400"></span> Done</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-400"></span> Pending</span>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('tracker.daily') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold transition">
            Update today's activities &rarr;
        </a>
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
            Run a report
        </a>
    </div>
@endsection
