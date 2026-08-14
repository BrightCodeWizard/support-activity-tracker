<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HandoverController extends Controller
{
    public function show(Request $request): View
    {
        $date = Carbon::parse($request->date ?? today()->toDateString());

        $activities = Activity::query()
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->orderBy('name')
            ->get();

        $updates = ActivityUpdate::query()
            ->with(['user', 'activity'])
            ->whereDate('update_date', $date->toDateString())
            ->get()
            ->groupBy(fn (ActivityUpdate $u) => $u->activity_id);

        $matrix = $this->buildMatrix($activities, $users, $updates);

        return view('handover.show', [
            'date' => $date,
            'users' => $users,
            'activities' => $activities,
            'matrix' => $matrix,
            'handoverCount' => $this->handoverCount($matrix),
        ]);
    }

    /**
     * Build a user x activity grid for the selected day.
     *
     * @param  Collection<int, Activity>  $activities
     * @param  Collection<int, User>  $users
     * @return array<string, mixed>
     */
    private function buildMatrix(Collection $activities, Collection $users, Collection $updates): array
    {
        $matrix = [];

        foreach ($activities as $activity) {
            $row = [];

            foreach ($users as $user) {
                $update = $updates->get($activity->id)?->firstWhere('user_id', $user->id);
                $row[$user->id] = $update;
            }

            $matrix[$activity->id] = [
                'activity' => $activity,
                'cells' => $row,
            ];
        }

        return $matrix;
    }

    /**
     * @param  array<string, mixed>  $matrix
     */
    private function handoverCount(array $matrix): int
    {
        return collect($matrix)
            ->flatMap(fn (array $row) => $row['cells'])
            ->where('status', 'pending')
            ->count();
    }
}
