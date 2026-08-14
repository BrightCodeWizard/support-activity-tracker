<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_tracker_renders_active_activities(): void
    {
        $user = User::factory()->create();
        Activity::factory()->create(['name' => 'Daily SMS count in comparison to SMS count from logs', 'is_active' => true]);
        Activity::factory()->create(['name' => 'Legacy retired activity', 'is_active' => false]);

        $this->actingAs($user)
            ->get(route('tracker.daily'))
            ->assertOk()
            ->assertSee('Daily SMS count in comparison to SMS count from logs')
            ->assertDontSee('Legacy retired activity');
    }

    public function test_update_can_be_recorded_with_status_remark_and_user(): void
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create();

        $this->actingAs($user)
            ->post(route('tracker.updates.store'), [
                'activity_id' => $activity->id,
                'update_date' => '2026-08-14',
                'status' => 'done',
                'remark' => 'All counts match the logs.',
            ])->assertSessionHas('status');

        $this->assertDatabaseHas('activity_updates', [
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'update_date' => '2026-08-14 00:00:00',
            'status' => 'done',
            'remark' => 'All counts match the logs.',
        ]);
    }

    public function test_second_update_same_day_overwrites_previous_one(): void
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create();
        $date = '2026-08-14';

        $this->actingAs($user)->post(route('tracker.updates.store'), [
            'activity_id' => $activity->id,
            'update_date' => $date,
            'status' => 'pending',
            'remark' => 'Initial check',
        ]);

        $this->actingAs($user)->post(route('tracker.updates.store'), [
            'activity_id' => $activity->id,
            'update_date' => $date,
            'status' => 'done',
            'remark' => 'Resolved after review',
        ]);

        $this->assertSame(1, ActivityUpdate::query()
            ->where('activity_id', $activity->id)
            ->where('user_id', $user->id)
            ->whereDate('update_date', $date)
            ->count());

        $this->assertDatabaseHas('activity_updates', [
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'status' => 'done',
        ]);
    }

    public function test_update_status_must_be_valid(): void
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create();

        $this->actingAs($user)->post(route('tracker.updates.store'), [
            'activity_id' => $activity->id,
            'update_date' => '2026-08-14',
            'status' => 'in-progress',
        ])->assertSessionHasErrors('status');
    }
}
