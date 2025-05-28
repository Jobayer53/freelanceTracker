<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $clients = Client::all();
          $user = User::first();

        Project::create([
            'client_id' => $clients[0]->id,
            'user_id' => $user->id,
            'title' => 'Website Redesign',
            'description' => 'Redesign the client website.',
            'status' => 'active',
            'deadline' => now()->addDays(30),
        ]);

        Project::create([
            'client_id' => $clients[1]->id,
            'user_id' => $user->id,
            'title' => 'Mobile App UI',
            'description' => 'Design UI for the mobile app.',
            'status' => 'completed',
            'deadline' => now()->subDays(10),
        ]);

    }
}
