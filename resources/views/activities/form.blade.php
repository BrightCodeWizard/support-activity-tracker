@extends('layouts.app')

@section('title', $activity->exists ? 'Edit Activity' : 'New Activity')

@section('content')
    <div class="max-w-2xl">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">{{ $activity->exists ? 'Edit Activity' : 'New Activity' }}</h1>
            <p class="text-slate-500 text-sm mt-1">Describe the activity your support team must update daily.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4">
                <ul class="text-sm text-rose-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $activity->exists ? route('activities.update', $activity) : route('activities.store') }}"
              class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            @csrf
            @if ($activity->exists)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Activity name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $activity->name) }}" required
                       class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-slate-400 mt-1.5">e.g. Daily SMS count in comparison to SMS count from logs</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $activity->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                    <input id="category" type="text" name="category" value="{{ old('category', $activity->category) }}"
                           placeholder="e.g. SMS, Batch, Monitoring"
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="frequency" class="block text-sm font-medium text-slate-700 mb-1.5">Frequency</label>
                    <select id="frequency" name="frequency"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="daily" {{ old('frequency', $activity->frequency) === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('frequency', $activity->frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    </select>
                </div>
            </div>

            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $activity->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-slate-700">Active (visible on the daily tracker)</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold transition">
                    {{ $activity->exists ? 'Save changes' : 'Create activity' }}
                </button>
                <a href="{{ route('activities.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
