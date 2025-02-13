<?php

namespace Database\Seeders;

// Mengimpor model User untuk membuat user baru
use App\Models\User;
// Mengimpor Seeder untuk melakukan seeding ke database
use Illuminate\Database\Seeder;
// Mengimpor model Role dari Spatie untuk menangani role pengguna
use Spatie\Permission\Models\Role;
// Mengimpor model Permission dari Spatie untuk menangani permission pengguna
use Spatie\Permission\Models\Permission;
// Mengimpor WithoutModelEvents jika ingin menonaktifkan event model selama seeding
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Menjalankan seeder untuk mengisi data pada tabel.
     */
    public function run(): void
    {
        // Membuat user baru dengan nama 'zalfa' dan email 'zalfadwi@gmail.com'
        // Password di-hash dengan bcrypt untuk enkripsi keamanan
        $user = User::create([
            'name'      => 'zalfa',                 // Nama user
            'email'     => 'zalfadwi@gmail.com',    // Email user
            'password'  => bcrypt('password'),      // Password terenkripsi dengan bcrypt
        ]);

        // Mengambil semua permissions yang ada dari tabel permissions
        $permissions = Permission::all();

        // Menemukan role dengan ID 1 (biasanya role admin) dari tabel roles
        $role = Role::find(1);

        // Menyinkronkan semua permission yang ada dengan role admin
        // Artinya role admin akan diberikan akses ke semua permission yang ada
        $role->syncPermissions($permissions);

        // Menugaskan role admin ke user yang baru dibuat
        // User ini sekarang memiliki semua hak akses yang terkait dengan role 'admin'
        $user->assignRole($role);
    }
}
