<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Cronjob;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@demo.com',
            'password' => '123456',
        ]);

        Cronjob::create([
            'name' => 'Lets Encrypt Renewal',
            'payload' => 'certbot renew --post-hook \'supervisorctl restart nginx\'',
            'run_every' => 'day',
        ]);
    }
}
