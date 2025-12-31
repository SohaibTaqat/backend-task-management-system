<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Create a pending task
            Task::factory()->pending()->create([
                'user_id' => $user->id,
            ]);

            // Create an in-progress task
            Task::factory()->inProgress()->create([
                'user_id' => $user->id,
            ]);

            // Create a completed task
            Task::factory()->completed()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
