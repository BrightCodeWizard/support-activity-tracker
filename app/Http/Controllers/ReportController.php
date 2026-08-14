<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = Carbon::parse($request->input('from', today()->subDays(6)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', today()->toDateString()))->endOfDay();

        $query = ActivityUpdate::query()
            ->with(['activity', 'user'])
            ->whereDate('update_date', '>=', $from->toDateString())
            ->whereDate('update_date', '<=', $to->toDateString());

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->input('activity_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $filters = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'activity_id' => $request->input('activity_id'),
            'user_id' => $request->input('user_id'),
            'status' => $request->input('status'),
        ];

        $summary = [
            'total' => (clone $query)->count(),
            'done' => (clone $query)->where('status', 'done')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'personnel' => (clone $query)->distinct()->count('user_id'),
        ];

        $updates = $query
            ->orderByDesc('update_date')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('reports.index', [
            'updates' => $updates,
            'summary' => $summary,
            'activities' => Activity::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }
}
