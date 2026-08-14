@extends('layouts.app')

@section('title', 'My Daily Tracker')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My Daily Tracker</h1>
            <p class="text-slate-500 text-sm mt-1">Update the status of each activity for the selected day.</p>
        </div>

        <form method="GET" action="{{ route('tracker.daily') }}" class="flex items-center gap-3">
            <label class="text-sm font-medium text-slate-600">Date</label>
            <input type="date" name="date" value="{{ $date }}"
                   onchange="this.form.submit()"
                   class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($activities as $activity)
            @php
                $update = $updates->get($activity->id);
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-semibold text-slate-900 leading-snug">{{ $activity->name }}</h2>
                        @if ($activity->description)
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $activity->description }}</p>
                        @endif
                    </div>
                    @if ($update)
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $update->status === 'done' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ strtoupper($update->status) }}
                        </span>
                    @else
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                            NOT SET
                        </span>
                    @endif
                </div>

                <div class="px-5 py-3 border-b border-slate-100 flex flex-wrap items-center gap-2 text-xs">
                    @if ($activity->category)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-medium">{{ $activity->category }}</span>
                    @endif
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium capitalize">{{ $activity->frequency }}</span>
                </div>

                <form method="POST" action="{{ route('tracker.updates.store') }}" class="px-5 py-4 flex flex-col gap-3 flex-1">
                    @csrf
                    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                    <input type="hidden" name="update_date" value="{{ $date }}">

                    <div class="grid grid-cols-2 gap-2">
                        <label class="relative">
                            <input type="radio" name="status" value="done" class="peer sr-only"
                                   {{ $update?->status === 'done' ? 'checked' : '' }}>
                            <span class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border text-sm font-medium cursor-pointer transition
                                         border-slate-300 text-slate-600
                                         peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Done
                            </span>
                        </label>
                        <label class="relative">
                            <input type="radio" name="status" value="pending" class="peer sr-only"
                                   {{ $update?->status === 'pending' ? 'checked' : '' }}>
                            <span class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border text-sm font-medium cursor-pointer transition
                                         border-slate-300 text-slate-600
                                         peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pending
                            </span>
                        </label>
                    </div>

                    <textarea name="remark" rows="2" placeholder="Remark (e.g. variance of 12 SMS observed, escalated to vendor)..."
                              class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $update?->remark }}</textarea>

                    <div class="mt-auto pt-1 flex items-center justify-between gap-3">
                        <div class="text-xs text-slate-400">
                            @if ($update)
                                Updated {{ $update->updated_at->format('g:i A') }}
                            @else
                                No update recorded yet
                            @endif
                        </div>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold transition">
                            {{ $update ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                <p class="text-slate-500">No active activities yet.</p>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('activities.create') }}" class="text-indigo-600 font-medium text-sm mt-2 inline-block">Create the first activity</a>
                    @endif
                @endauth
            </div>
        @endforelse
    </div>
@endsection
