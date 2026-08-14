<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@support.local'],
            [
                'name' => 'Ama Owusu',
                'staff_id' => 'STF-0001',
                'department' => 'Applications Support',
                'phone' => '+233-24-000-0001',
                'role' => 'admin',
                'password' => 'password',
            ],
        );

        $members = [
            [
                'name' => 'Kofi Mensah',
                'email' => 'kofi@support.local',
                'staff_id' => 'STF-1002',
                'phone' => '+233-24-111-2222',
            ],
            [
                'name' => 'Abena Boateng',
                'email' => 'abena@support.local',
                'staff_id' => 'STF-1003',
                'phone' => '+233-24-333-4444',
            ],
            [
                'name' => 'Yaw Asante',
                'email' => 'yaw@support.local',
                'staff_id' => 'STF-1004',
                'phone' => '+233-24-555-6666',
            ],
        ];

        foreach ($members as $member) {
            User::updateOrCreate(
                ['email' => $member['email']],
                [
                    ...$member,
                    'department' => 'Applications Support',
                    'role' => 'member',
                    'password' => 'password',
                ],
            );
        }

        $activities = [
            [
                'name' => 'Daily SMS count in comparison to SMS count from logs',
                'description' => 'Reconcile the total SMS dispatched via the platform against the SMS count captured in the SMSC logs and flag any variance.',
                'category' => 'SMS',
                'frequency' => 'daily',
            ],
            [
                'name' => 'EOD batch job status check',
                'description' => 'Confirm that all end-of-day batch jobs (interest accrual, statement generation) completed without errors.',
                'category' => 'Batch Processing',
                'frequency' => 'daily',
            ],
            [
                'name' => 'Interface heartbeat monitoring',
                'description' => 'Verify the heartbeat status of all third-party interfaces (switch, USSD, SMS aggregator).',
                'category' => 'Interface Monitoring',
                'frequency' => 'daily',
            ],
            [
                'name' => 'Pending ticket queue review',
                'description' => 'Review the queue of open support tickets, prioritize P1/P2 items, and action or escalate as required.',
                'category' => 'Core Banking',
                'frequency' => 'daily',
            ],
            [
                'name' => 'Weekly standby roster update',
                'description' => 'Confirm the on-call roster for the coming week is current and communicated to the team.',
                'category' => 'Reports',
                'frequency' => 'weekly',
            ],
        ];

        $users = User::all();

        foreach ($activities as $activity) {
            $model = Activity::updateOrCreate(
                ['name' => $activity['name']],
                [
                    'description' => $activity['description'],
                    'category' => $activity['category'],
                    'frequency' => $activity['frequency'],
                ],
            );
            $weeklies = $activity['frequency'] === 'weekly';

            foreach ($users as $user) {
                for ($daysAgo = 0; $daysAgo <= 13; $daysAgo++) {
                    if ($weeklies && $daysAgo % 7 !== 0) {
                        continue;
                    }

                    $date = today()->subDays($daysAgo);

                    $exists = ActivityUpdate::query()
                        ->where('activity_id', $model->id)
                        ->where('user_id', $user->id)
                        ->whereDate('update_date', $date)
                        ->exists();

                    if (! $exists) {
                        ActivityUpdate::create([
                            'activity_id' => $model->id,
                            'user_id' => $user->id,
                            'update_date' => $date->toDateString(),
                            'status' => collect(['done', 'done', 'done', 'pending'])->random(),
                            'remark' => fake()->optional(0.7)->sentence(),
                        ]);
                    }
                }
            }
        }
    }
}
