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
        // DB::unprepared("INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_active`, `role_id`, `remember_token`, `last_login`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES (1, 'Head Office', 'superadmin@admin.com', '\$2y\$10\$6bDotkROfClLF6haojMGd.CRzGXyGhPuWlZrgszM9qMQ9f1TS5ACC', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL);");
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
