<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_filters_by_custom_date_range(): void
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create();
        $other = Activity::factory()->create();

        ActivityUpdate::factory()->create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'update_date' => '2026-08-10',
            'status' => 'done',
        ]);
        ActivityUpdate::factory()->create([
            'activity_id' => $other->id,
            'user_id' => $user->id,
            'update_date' => '2026-08-12',
            'status' => 'pending',
        ]);
        ActivityUpdate::factory()->create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'update_date' => '2026-08-01',
            'status' => 'done',
        ]);

        $this->actingAs($user)
            ->get(route('reports.index', ['from' => '2026-08-09', 'to' => '2026-08-13']))
            ->assertOk()
            ->assertSee('Mon, 10 Aug 2026')
            ->assertSee('Wed, 12 Aug 2026')
            ->assertDontSee('Sat, 01 Aug 2026');
    }

    public function test_report_filters_by_activity_status_and_personnel(): void
    {
        $activity = Activity::factory()->create(['name' => 'SMS Reconciliation']);
        $other = Activity::factory()->create(['name' => 'Standby Roster']);
        $kofi = User::factory()->create(['name' => 'Kofi Mensah']);
        $abena = User::factory()->create(['name' => 'Abena Boateng']);

        ActivityUpdate::factory()->create(['activity_id' => $activity->id, 'user_id' => $kofi->id, 'update_date' => '2026-08-10', 'status' => 'done', 'remark' => 'Variance nil']);
        ActivityUpdate::factory()->create(['activity_id' => $activity->id, 'user_id' => $abena->id, 'update_date' => '2026-08-10', 'status' => 'pending', 'remark' => 'Waiting on vendor']);
        ActivityUpdate::factory()->create(['activity_id' => $other->id, 'user_id' => $kofi->id, 'update_date' => '2026-08-10', 'status' => 'done', 'remark' => 'Roster published']);

        $response = $this->actingAs($kofi)
            ->get(route('reports.index', [
                'from' => '2026-08-01',
                'to' => '2026-08-31',
                'activity_id' => $activity->id,
                'status' => 'done',
            ]))
            ->assertOk()
            ->assertSee('Variance nil')
            ->assertDontSee('Waiting on vendor')
            ->assertDontSee('Roster published');
    }

    public function test_report_summary_counts_are_computed(): void
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create();

        ActivityUpdate::factory()->create(['activity_id' => $activity->id, 'user_id' => $user->id, 'update_date' => '2026-08-10', 'status' => 'done']);
        ActivityUpdate::factory()->create(['activity_id' => $activity->id, 'user_id' => $user->id, 'update_date' => '2026-08-11', 'status' => 'pending']);

        $this->actingAs($user)
            ->get(route('reports.index', ['from' => '2026-08-01', 'to' => '2026-08-31']))
            ->assertOk()
            ->assertSee('2')
            ->assertSee('1');
    }
}
