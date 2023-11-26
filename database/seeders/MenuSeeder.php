<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Dashboard
        Menu::updateOrCreate(
            [
                'id' => 1
            ],
            [
                'parent_id' => 0,
                'name'      => 'Dashboard',
                'url'       => 'admin/dashboard',
                'icon'      => 'fa fa-dashboard',
                'sequence'  => 1,
                'roles'     => json_encode([]),
                'permission' => json_encode([]),
                'has_child' => 0
            ]
        );


        // Book Management
        Menu::updateOrCreate(
            [
                'id' => 10
            ],
            [
                'parent_id' => 0,
                'name'      => 'Book Management',
                'url'       => '#',
                'icon'      => 'fa fa-book',
                'sequence'  => 10,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(['book-authors-list', 'book-publishers-list', 'books-list']),
                'has_child' => 1
            ]
        );

        // Book Authors
        Menu::updateOrCreate(
            [
                'id' => 11
            ],
            [
                'parent_id' => 10,
                'name'      => 'Book Authors',
                'url'       => 'admin/book_authors',
                'icon'      => 'fa fa-user',
                'sequence'  => 11,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(["book-authors-list"]),
                'has_child' => 0
            ]
        );

        // Book Publisher
        Menu::updateOrCreate(
            [
                'id' => 12
            ],
            [
                'parent_id' => 10,
                'name'      => 'Book Publishers',
                'url'       => 'admin/book_publishers',
                'icon'      => 'fa fa-user',
                'sequence'  => 12,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(["book-publishers-list"]),
                'has_child' => 0
            ]
        );

        // Book Publisher
        Menu::updateOrCreate(
            [
                'id' => 13
            ],
            [
                'parent_id' => 10,
                'name'      => 'Books',
                'url'       => 'admin/books',
                'icon'      => 'fa fa-book',
                'sequence'  => 13,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(["books-list"]),
                'has_child' => 0
            ]
        );

        // Setting
        Menu::updateOrCreate(
            [
                'id' => 900
            ],
            [
                'parent_id' => 0,
                'name'      => 'Pengaturan',
                'url'       => '#',
                'icon'      => 'fa fa-gear',
                'sequence'  => 900,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(['users-list', 'access-logs-list', 'konfigurasi-list']),
                'has_child' => 1
            ]
        );
        // Users
        Menu::updateOrCreate(
            [
                'id' => 901
            ],
            [
                'parent_id' => 900,
                'name'      => 'User Manager',
                'url'       => 'admin/users',
                'icon'      => 'fa fa-user',
                'sequence'  => 901,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(["users-list"]),
                'has_child' => 0
            ]
        );

        // Access Logs
        Menu::updateOrCreate(
            [
                'id' => 902
            ],
            [
                'parent_id' => 900,
                'name'      => 'Access Logs',
                'url'       => 'admin/access_logs',
                'icon'      => 'fa fa-gears',
                'sequence'  => 902,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(["access-logs-list"]),
                'has_child' => 0
            ]
        );

        // Konfigurasi
        Menu::updateOrCreate(
            [
                'id' => 920
            ],
            [
                'parent_id' => 900,
                'name'      => 'Konfigurasi',
                'url'       => 'admin/konfigurasi',
                'icon'      => 'fa fa-gears',
                'sequence'  => 920,
                'roles'     => json_encode(["SUPERADMIN"]),
                'permission' => json_encode(["konfigurasi-list"]),
                'has_child' => 0
            ]
        );
    }
}
