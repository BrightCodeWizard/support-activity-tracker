<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_dashboard_shows_todays_stats_and_handover_items(): void
    {
        $kofi = User::factory()->create(['name' => 'Kofi Mensah']);
        $abena = User::factory()->create(['name' => 'Abena Boateng']);
        $activity = Activity::factory()->create(['name' => 'SMS Reconciliation']);

        ActivityUpdate::factory()->create(['activity_id' => $activity->id, 'user_id' => $kofi->id, 'update_date' => today(), 'status' => 'done']);
        ActivityUpdate::factory()->create(['activity_id' => $activity->id, 'user_id' => $abena->id, 'update_date' => today(), 'status' => 'pending', 'remark' => 'Waiting on vendor']);
        ActivityUpdate::factory()->create(['activity_id' => $activity->id, 'user_id' => $abena->id, 'update_date' => today()->subDay(), 'status' => 'pending']);

        $viewer = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('SMS Reconciliation')
            ->assertSee('Waiting on vendor')
            ->assertSee('Handover queue');
    }

    public function test_dashboard_marks_my_pending_items(): void
    {
        $user = User::factory()->create();
        $pendingActivity = Activity::factory()->create();
        $doneActivity = Activity::factory()->create();

        ActivityUpdate::factory()->create(['activity_id' => $pendingActivity->id, 'user_id' => $user->id, 'update_date' => today(), 'status' => 'pending']);
        ActivityUpdate::factory()->create(['activity_id' => $doneActivity->id, 'user_id' => $user->id, 'update_date' => today(), 'status' => 'done']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My pending');
    }
}
