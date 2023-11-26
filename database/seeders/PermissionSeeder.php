<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        try {
            DB::beginTransaction();

            // User
            Permission::create(['name' => 'users-list']);
            Permission::create(['name' => 'users-add']);
            Permission::create(['name' => 'users-edit']);
            Permission::create(['name' => 'users-download']);
            Permission::create(['name' => 'users-delete']);

            // Konfigurasi
            Permission::create(['name' => 'konfigurasi-list']);
            Permission::create(['name' => 'konfigurasi-edit']);
            Permission::create(['name' => 'konfigurasi-reset']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
        DB::beginTransaction();
        try {
            Role::updateOrCreate(['id' => 1], ['name' => 'SUPERADMIN']);
            Role::updateOrCreate(['id' => 2], ['name' => 'USER']);
            $users = User::find(1);
            $users->assignRole('SUPERADMIN');
            $users->update(['role_id' => 1]);


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
