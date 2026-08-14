<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_activity(): void
    {
        $this->actingAs($this->admin())
            ->post(route('activities.store'), [
                'name' => 'Daily SMS count in comparison to SMS count from logs',
                'description' => 'Reconcile platform SMS against SMSC logs.',
                'category' => 'SMS',
                'frequency' => 'daily',
                'is_active' => true,
            ])->assertRedirect(route('activities.index'));

        $this->assertDatabaseHas('activities', [
            'name' => 'Daily SMS count in comparison to SMS count from logs',
            'category' => 'SMS',
        ]);
    }

    public function test_activity_name_is_required(): void
    {
        $this->actingAs($this->admin())
            ->post(route('activities.store'), [
                'frequency' => 'daily',
            ])->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_activity(): void
    {
        $activity = Activity::factory()->create();

        $this->actingAs($this->admin())
            ->put(route('activities.update', $activity), [
                'name' => 'Renamed Activity',
                'frequency' => 'weekly',
                'is_active' => false,
            ])->assertRedirect(route('activities.index'));

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'name' => 'Renamed Activity',
            'frequency' => 'weekly',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_activity(): void
    {
        $activity = Activity::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('activities.destroy', $activity))
            ->assertRedirect(route('activities.index'));

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }
}
