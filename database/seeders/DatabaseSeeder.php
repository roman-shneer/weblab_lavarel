<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\Task;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tasks = DB::connection('sqlite_backup')->select("select * from task");
        foreach ($tasks as $task) {
            Task::factory()->create([
                'id' => $task->id,
                'exp_number' => $task->exp_number,
                'title' => $task->title,
                'description' => $task->description,                
                'status' => $task->status,
                'source' => $task->source,
                'created_at' => $task->date,
                'updated_at' => $task->date,
            ]);
        }
    }
}
