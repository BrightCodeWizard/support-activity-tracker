<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackerUpdateRequest;
use App\Models\Activity;
use App\Models\ActivityUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrackerController extends Controller
{
    public function daily(Request $request): View
    {
        $date = $request->date ?? today()->toDateString();

        $activities = Activity::query()
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $updates = ActivityUpdate::query()
            ->where('user_id', Auth::id())
            ->whereDate('update_date', $date)
            ->get()
            ->keyBy('activity_id');

        return view('tracker.daily', [
            'date' => $date,
            'activities' => $activities,
            'updates' => $updates,
        ]);
    }

    public function store(TrackerUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $update = ActivityUpdate::query()
            ->where('activity_id', $data['activity_id'])
            ->where('user_id', Auth::id())
            ->whereDate('update_date', $data['update_date'])
            ->first();

        if ($update) {
            $update->update([
                'status' => $data['status'],
                'remark' => $data['remark'] ?? null,
            ]);
        } else {
            ActivityUpdate::create([
                'activity_id' => $data['activity_id'],
                'user_id' => Auth::id(),
                'update_date' => $data['update_date'],
                'status' => $data['status'],
                'remark' => $data['remark'] ?? null,
            ]);
        }

        return back()->with('status', sprintf(
            'Update for "%s" (%s) recorded.',
            Activity::findOrFail($data['activity_id'])->name,
            strtoupper($data['status']),
        ));
    }
}
