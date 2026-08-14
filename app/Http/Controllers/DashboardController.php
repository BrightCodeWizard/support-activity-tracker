<?php

namespace App\Http\Controllers;

use App\Models\ActivityUpdate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today()->toDateString();
        $user = Auth::user();

        $todaysUpdates = ActivityUpdate::query()
            ->with(['activity', 'user'])
            ->whereDate('update_date', $today)
            ->get();

        $myPending = $todaysUpdates
            ->where('user_id', $user->id)
            ->where('status', 'pending');

        $handoverItems = $todaysUpdates
            ->where('status', 'pending')
            ->sortBy('user.name')
            ->values();

        $totalUsers = User::count();
        $reportingUsers = $todaysUpdates->pluck('user_id')->unique()->count();

        return view('dashboard.index', [
            'today' => Carbon::parse($today),
            'stats' => [
                'done' => $todaysUpdates->where('status', 'done')->count(),
                'pending' => $todaysUpdates->where('status', 'pending')->count(),
                'reporting' => $reportingUsers,
                'totalUsers' => $totalUsers,
            ],
            'myPendingCount' => $myPending->count(),
            'myAssignedCount' => $todaysUpdates->where('user_id', $user->id)->count(),
            'handoverItems' => $handoverItems,
            'weeklyTrend' => $this->weeklyTrend(),
        ]);
    }

    /**
     * @return Collection<int, array{date: string, label: string, done: int, pending: int}>
     */
    private function weeklyTrend(): Collection
    {
        $rows = ActivityUpdate::query()
            ->selectRaw('update_date, status, COUNT(*) as total')
            ->whereDate('update_date', '>=', today()->subDays(6)->toDateString())
            ->whereDate('update_date', '<=', today()->toDateString())
            ->groupBy('update_date', 'status')
            ->get()
            ->groupBy('update_date');

        $trend = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $day = $rows->get($date->toDateString());

            $trend->push([
                'date' => $date->toDateString(),
                'label' => $date->shortDayName,
                'done' => (int) $day?->firstWhere('status', 'done')?->total,
                'pending' => (int) $day?->firstWhere('status', 'pending')?->total,
            ]);
        }

        return $trend;
    }
}
