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

            // Book Authors
            Permission::create(['name' => 'book-authors-list']);
            Permission::create(['name' => 'book-authors-add']);
            Permission::create(['name' => 'book-authors-edit']);
            // Permission::create(['name' => 'book-authors-download']);
            Permission::create(['name' => 'book-authors-delete']);

            // Book Publishers
            Permission::create(['name' => 'book-publishers-list']);
            Permission::create(['name' => 'book-publishers-add']);
            Permission::create(['name' => 'book-publishers-edit']);
            // Permission::create(['name' => 'book-publishers-download']);
            Permission::create(['name' => 'book-publishers-delete']);

            // Books
            Permission::create(['name' => 'books-list']);
            Permission::create(['name' => 'books-add']);
            Permission::create(['name' => 'books-edit']);
            // Permission::create(['name' => 'books-download']);
            Permission::create(['name' => 'books-delete']);

            // User
            Permission::create(['name' => 'users-list']);
            Permission::create(['name' => 'users-add']);
            Permission::create(['name' => 'users-edit']);
            // Permission::create(['name' => 'users-download']);
            Permission::create(['name' => 'users-delete']);

            // Access Logs
            Permission::create(['name' => 'access-logs-list']);

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
            $superadmin = User::find(1);
            $superadmin->assignRole('SUPERADMIN');
            $superadmin->update(['role_id' => 1]);

            Role::updateOrCreate(['id' => 2], ['name' => 'USER']);
            $role_user = Role::find(2);
            $role_user->permissions()->detach();
            $role_user->givePermissionTo(Permission::where('name', "LIKE", "books-%")->where('name', '!=', "books-delete")->get()->pluck('id')->toArray());
            $users = User::where('role_id', $role_user->id)->get();
            foreach ($users as $user) {
                $user->syncRoles((int)$role_user->id);
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
