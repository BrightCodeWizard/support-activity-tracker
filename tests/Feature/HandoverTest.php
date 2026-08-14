<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandoverTest extends TestCase
{
    use RefreshDatabase;

    public function test_handover_shows_all_updates_for_selected_day(): void
    {
        $kofi = User::factory()->create(['name' => 'Kofi Mensah', 'staff_id' => 'STF-1002']);
        $abena = User::factory()->create(['name' => 'Abena Boateng', 'staff_id' => 'STF-1003']);
        $activity = Activity::factory()->create(['name' => 'EOD batch job status check']);

        ActivityUpdate::factory()->create([
            'activity_id' => $activity->id,
            'user_id' => $kofi->id,
            'update_date' => '2026-08-14',
            'status' => 'done',
            'remark' => 'Batch completed.',
        ]);
        ActivityUpdate::factory()->create([
            'activity_id' => $activity->id,
            'user_id' => $abena->id,
            'update_date' => '2026-08-14',
            'status' => 'pending',
            'remark' => 'Handing over for night shift.',
        ]);
        ActivityUpdate::factory()->create([
            'activity_id' => $activity->id,
            'user_id' => $kofi->id,
            'update_date' => '2026-08-13',
            'status' => 'done',
            'remark' => 'Yesterday remark',
        ]);

        $viewer = User::factory()->create();

        $this->actingAs($viewer)
            ->get(route('handover.show', ['date' => '2026-08-14']))
            ->assertOk()
            ->assertSee('Kofi Mensah')
            ->assertSee('Abena Boateng')
            ->assertSee('Batch completed.')
            ->assertSee('Handing over for night shift.')
            ->assertSee('PENDING')
            ->assertDontSee('Yesterday remark');
    }
}
