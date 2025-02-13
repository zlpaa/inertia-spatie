<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Mengimpor model Permission dari Spatie untuk membuat permission baru
use Spatie\Permission\Models\Permission;
// Mengimpor WithoutModelEvents (meskipun tidak digunakan langsung di sini)
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Menjalankan seeder untuk mengisi data permissions pada database.
     */
    public function run(): void
    {
        // Permission untuk manajemen users
        Permission::create(['name' => 'users index', 'guard_name' => 'web']);  // Akses untuk melihat daftar user
        Permission::create(['name' => 'users create', 'guard_name' => 'web']); // Akses untuk membuat user baru
        Permission::create(['name' => 'users edit', 'guard_name' => 'web']);   // Akses untuk mengedit data user
        Permission::create(['name' => 'users delete', 'guard_name' => 'web']); // Akses untuk menghapus user

        // Permission untuk manajemen roles
        Permission::create(['name' => 'roles index', 'guard_name' => 'web']);  // Akses untuk melihat daftar roles
        Permission::create(['name' => 'roles create', 'guard_name' => 'web']); // Akses untuk membuat role baru
        Permission::create(['name' => 'roles edit', 'guard_name' => 'web']);   // Akses untuk mengedit role yang ada
        Permission::create(['name' => 'roles delete', 'guard_name' => 'web']); // Akses untuk menghapus role

        // Permission untuk manajemen permissions
        Permission::create(['name' => 'permissions index', 'guard_name' => 'web']); // Akses untuk melihat daftar permissions
        Permission::create(['name' => 'permissions create', 'guard_name' => 'web']); // Akses untuk membuat permission baru
        Permission::create(['name' => 'permissions edit', 'guard_name' => 'web']);   // Akses untuk mengedit permission yang ada
        Permission::create(['name' => 'permissions delete', 'guard_name' => 'web']); // Akses untuk menghapus permission
    }
}
