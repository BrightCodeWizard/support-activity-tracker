@extends('layouts.app')

@section('title', 'Daily Handover')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Daily Handover</h1>
            <p class="text-slate-500 text-sm mt-1">Every update made by every personnel on the selected day.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('handover.show', ['date' => $date->copy()->subDay()->toDateString()]) }}"
               class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium hover:bg-slate-50">
                &larr; Prev
            </a>
            <form method="GET" action="{{ route('handover.show') }}">
                <input type="date" name="date" value="{{ $date->toDateString() }}"
                       onchange="this.form.submit()"
                       class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </form>
            <a href="{{ route('handover.show', ['date' => $date->copy()->addDay()->toDateString()]) }}"
               class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium hover:bg-slate-50">
                Next &rarr;
            </a>
        </div>
    </div>

    <div class="mb-6 flex flex-wrap gap-3">
        <div class="bg-white rounded-xl border border-slate-200 px-5 py-3 flex items-center gap-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Handover items (pending)</span>
            <span class="text-xl font-bold {{ $handoverCount > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $handoverCount }}</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 px-5 py-3 flex items-center gap-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Updates recorded</span>
            <span class="text-xl font-bold text-slate-800">
                {{ collect($matrix)->flatMap(fn ($row) => $row['cells'])->filter()->count() }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-3.5 font-semibold text-slate-600 w-72">Activity</th>
                    @foreach ($users as $user)
                        <th class="px-3 py-3.5 text-center font-semibold text-slate-600 min-w-[220px]">
                            <span class="block">{{ $user->name }}</span>
                            <span class="block text-xs font-normal text-slate-400">{{ $user->staff_id }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($matrix as $row)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-800 leading-snug">{{ $row['activity']->name }}</p>
                            @if ($row['activity']->category)
                                <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-xs font-medium">{{ $row['activity']->category }}</span>
                            @endif
                        </td>
                        @foreach ($users as $user)
                            @php
                                $update = $row['cells'][$user->id] ?? null;
                            @endphp
                            <td class="px-3 py-4">
                                @if ($update)
                                    <div class="flex flex-col gap-1.5">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold w-fit {{ $update->status === 'done' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ strtoupper($update->status) }}
                                        </span>
                                        @if ($update->remark)
                                            <p class="text-xs text-slate-500 leading-snug">{{ $update->remark }}</p>
                                        @endif
                                        <p class="text-[11px] text-slate-400">
                                            {{ $update->created_at->format('g:i A') }}
                                            @if ($update->created_at->format('g:i A') !== $update->updated_at->format('g:i A'))
                                                · edited {{ $update->updated_at->format('g:i A') }}
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $users->count() + 1 }}" class="px-5 py-10 text-center text-slate-400">
                            No activities defined yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
