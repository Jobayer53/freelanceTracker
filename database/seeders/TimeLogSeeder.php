<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\TimeLog;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TimeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $projects = Project::all();

        foreach (range(1, 5) as $i) {
            $project = $projects->random();
            $start = Carbon::now()->subDays(rand(1, 10))->setTime(rand(8, 10), 0);
            $end = (clone $start)->addHours(rand(2, 4));

            TimeLog::create([
                'project_id' => $project->id,
                'start_time' => $start,
                'end_time' => $end,
                'description' => 'Worked on module ' . $i,
                'hours' => $start->diffInMinutes($end) / 60,
            ]);
        }
    }
}
