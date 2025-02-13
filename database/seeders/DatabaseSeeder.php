<?php

namespace Database\Seeders;

// Mengimpor model User untuk keperluan seeding data user (meskipun tidak digunakan langsung di sini)
use App\Models\User;
// Mengimpor Seeder untuk menjalankan seeding ke database
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan seeder untuk mengisi data pada database.
     */
    public function run(): void
    {
        // Memanggil seeder untuk mengisi tabel 'roles' dengan data role yang diperlukan
        $this->call(RolesTableSeeder::class);

        // Memanggil seeder untuk mengisi tabel 'permissions' dengan data permission yang diperlukan
        $this->call(PermissionsTableSeeder::class);

        // Memanggil seeder untuk mengisi tabel 'users' dengan data user
        $this->call(UserTableSeeder::class);
    }
}
