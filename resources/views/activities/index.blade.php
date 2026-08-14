@extends('layouts.app')

@section('title', 'Manage Activities')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manage Activities</h1>
            <p class="text-slate-500 text-sm mt-1">Define the activities your team tracks each day.</p>
        </div>
        <a href="{{ route('activities.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Activity
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($activities as $activity)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="font-semibold text-slate-900 leading-snug">{{ $activity->name }}</h2>
                    <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $activity->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                        {{ $activity->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                </div>

                @if ($activity->description)
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed line-clamp-3">{{ $activity->description }}</p>
                @endif

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @if ($activity->category)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-medium">{{ $activity->category }}</span>
                    @endif
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium capitalize">{{ $activity->frequency }}</span>
                </div>

                <div class="mt-auto pt-4 flex items-center justify-between">
                    <span class="text-xs text-slate-400">{{ $activity->updates_count ?? 0 }} updates recorded</span>
                    <div class="flex gap-2">
                        <a href="{{ route('activities.edit', $activity) }}"
                           class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                        <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                              onsubmit="return confirm('Delete this activity? Its update history will also be removed.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg border border-rose-200 text-sm font-medium text-rose-600 hover:bg-rose-50">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
                <p class="text-slate-500">No activities defined yet.</p>
                <a href="{{ route('activities.create') }}" class="text-indigo-600 font-medium text-sm mt-2 inline-block">Create the first activity</a>
            </div>
        @endforelse
    </div>
@endsection
