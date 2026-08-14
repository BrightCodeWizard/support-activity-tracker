<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        return view('activities.index', [
            'activities' => Activity::withCount('updates')->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('activities.form', [
            'activity' => new Activity,
        ]);
    }

    public function store(ActivityRequest $request): RedirectResponse
    {
        Activity::create($request->validated());

        return redirect()->route('activities.index')
            ->with('status', 'Activity created successfully.');
    }

    public function edit(Activity $activity): View
    {
        return view('activities.form', compact('activity'));
    }

    public function update(ActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($request->validated());

        return redirect()->route('activities.index')
            ->with('status', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('status', 'Activity deleted successfully.');
    }
}
