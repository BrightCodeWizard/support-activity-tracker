@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Activity History Report</h1>
            <p class="text-slate-500 text-sm mt-1">Query activity histories over any custom date range.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">From</label>
                <input type="date" name="from" value="{{ $filters['from'] }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">To</label>
                <input type="date" name="to" value="{{ $filters['to'] }}"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Activity</label>
                <select name="activity_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All activities</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" {{ $filters['activity_id'] == $activity->id ? 'selected' : '' }}>{{ $activity->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Personnel</label>
                <select name="user_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All personnel</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $filters['user_id'] == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All statuses</option>
                    <option value="done" {{ $filters['status'] === 'done' ? 'selected' : '' }}>Done</option>
                    <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-700 text-white text-sm font-semibold transition">Apply</button>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'Total updates', 'value' => $summary['total'], 'tone' => 'text-slate-900'],
            ['label' => 'Done', 'value' => $summary['done'], 'tone' => 'text-emerald-600'],
            ['label' => 'Pending', 'value' => $summary['pending'], 'tone' => 'text-amber-600'],
            ['label' => 'Personnel involved', 'value' => $summary['personnel'], 'tone' => 'text-indigo-600'],
        ] as $stat)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold mt-1 {{ $stat['tone'] }}">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
        @if ($updates->isEmpty())
            <p class="px-5 py-10 text-center text-slate-400 text-sm">No updates match the selected filters.</p>
        @else
            <table class="w-full text-sm min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3 font-semibold text-slate-600">Date</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-600">Activity</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-600">Personnel</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-600">Remark</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-600">Updated at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($updates as $update)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-5 py-3 font-medium text-slate-700 whitespace-nowrap">{{ $update->update_date->format('D, d M Y') }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $update->activity->name }}</td>
                            <td class="px-3 py-3">
                                <span class="font-medium text-slate-800">{{ $update->user->name }}</span>
                                <span class="block text-xs text-slate-400">{{ $update->user->staff_id }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $update->status === 'done' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ strtoupper($update->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-slate-500 max-w-xs">{{ $update->remark ?? '—' }}</td>
                            <td class="px-3 py-3 text-slate-500 whitespace-nowrap">{{ $update->updated_at->format('d M Y, g:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $updates->links() }}
            </div>
        @endif
    </div>
@endsection
