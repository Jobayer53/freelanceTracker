<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $userId = User::first()->id;

        Client::create([
            'user_id' => $userId,
            'name' => 'Tech Corp',
            'email' => 'contact@techcorp.com',
            'contact_person' => 'John Smith',
        ]);

        Client::create([
            'user_id' => $userId,
            'name' => 'Design Studio',
            'email' => 'hello@designstudio.com',
            'contact_person' => 'Jane Doe',
        ]);

    }
}
