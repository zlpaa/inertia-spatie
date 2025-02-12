<?php

namespace App\Models;

// Menggunakan trait HasFactory untuk mendukung pembuatan factory model untuk testing dan seeding
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Menggunakan kelas Authenticatable dari Laravel untuk mendukung autentikasi pengguna
use Illuminate\Foundation\Auth\User as Authenticatable;
// Menggunakan trait Notifiable agar model User dapat mengirim notifikasi
use Illuminate\Notifications\Notifiable;
// Menggunakan trait HasRoles untuk mengelola peran dan izin menggunakan paket Spatie
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    // Menggunakan trait HasFactory, Notifiable, dan HasRoles pada model User
    // - HasFactory memungkinkan pembuatan data palsu untuk model User
    // - Notifiable memungkinkan model User mengirimkan notifikasi
    // - HasRoles memungkinkan model User menggunakan fitur manajemen peran dan izin dari Spatie
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * Daftar atribut yang dapat diisi secara massal melalui mekanisme mass assignment
     * atau ketika membuat atau memperbarui pengguna
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * Daftar atribut yang harus disembunyikan dari hasil serialisasi model (misalnya ketika mengubah ke format JSON)
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',  // Menyembunyikan atribut password
        'remember_token',  // Menyembunyikan token untuk otentikasi "remember me"
    ];

    /**
     * Get the attributes that should be cast.
     *
     * Menyediakan informasi tentang atribut yang harus dikonversi ke tipe data tertentu
     * secara otomatis saat mengambil data dari database
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Menentukan 'email_verified_at' harus dikonversi ke tipe datetime
            'password' => 'hashed', // Menentukan bahwa password harus diperlakukan sebagai hash
        ];
    }

    /**
     * Mendapatkan semua izin yang dimiliki pengguna dalam bentuk array key-value
     * di mana nama izin digunakan sebagai key dan nilai selalu true
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUserPermissions()
    {
        // Mengambil semua izin yang dimiliki oleh pengguna dan memetakannya
        return $this->getAllPermissions()->mapWithKeys(fn($permission) => [$permission['name'] => true]);
    }
}
