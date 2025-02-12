<?php

// Menentukan namespace untuk controller ini, yang berada di dalam folder App\Http\Controllers
namespace App\Http\Controllers;

// Kelas Controller ini bersifat abstrak, yang berarti kelas ini tidak dapat diinstansiasi secara langsung.
// Kelas ini berfungsi sebagai kelas dasar yang dapat diwarisi oleh controller-controller lain di aplikasi ini.
// Biasanya, controller lainnya akan mewarisi kelas ini untuk mengakses properti atau metode umum yang dapat digunakan di semua controller..
abstract class Controller
{
    // Kelas ini masih kosong, namun biasanya kelas seperti ini digunakan untuk mendefinisikan logika umum
    // yang dapat digunakan oleh controller-controller lain, seperti validasi, pengaturan respon, dll.
}
