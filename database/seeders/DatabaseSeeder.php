<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */


    public function run(): void
    {
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $managerUser = User::factory()->create([
            'role_id' => $managerRole->id,
        ]);

        User::factory(5)->create([
            'role_id' => $userRole->id,
        ]);

        $tasks = Task::factory(20)->create([
            'created_by' => $managerUser->id,
        ]);

        $tasks->each(function ($task) use ($tasks) {
            $dependencyTasks = $tasks->where('id', '!=', $task->id)->random(2);
            $task->dependencies()->attach($dependencyTasks->pluck('id')->toArray());
        });
    }
}
