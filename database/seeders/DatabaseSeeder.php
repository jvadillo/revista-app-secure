<?php

namespace Database\Seeders;
use App\Models\User;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@egibide.org',
            'password' => bcrypt('12345Abcde'),
            'role' => 'admin'
        ]);

        // Create a editor user
        User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@egibide.org',
            'password' => bcrypt('12345Abcde'),
            'role' => 'editor'
        ]);

        // Create 20 users that are normal users
        User::factory(20)->create(['role' => 'user']);
    }
}
