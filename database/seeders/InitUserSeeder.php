<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Table Users
        User::firstorCreate([
            'email' => 'superadmin@admin.com',
        ], [
            'name' => 'Superadmin',
            // 'email_verified_at' => now(),
            'role_id' => 1,
            'password' => bcrypt('12345678'),
            'status' => '1',
        ]);
    }
}
