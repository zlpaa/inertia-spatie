<?php

// Menentukan namespace untuk controller ini
namespace App\Http\Controllers;

// Mengimpor Request dari Laravel untuk menangani input dari user
use Illuminate\Http\Request;
// Mengimpor HasMiddleware untuk mendukung middleware pada controller ini
use Illuminate\Routing\Controllers\HasMiddleware;
// Mengimpor Middleware untuk menambahkan middleware pada rute tertentu
use Illuminate\Routing\Controllers\Middleware;
// Mengimpor model Permission dari package Spatie untuk manajemen hak akses
use Spatie\Permission\Models\Permission;

// Controller untuk menangani CRUD (Create, Read, Update, Delete) pada resource Permission
class PermissionController extends Controller implements HasMiddleware
{
    // Menentukan middleware yang digunakan pada controller ini
    // Middleware akan membatasi akses berdasarkan izin (permission) tertentu
    public static function middleware()
    {
        return [
            // Hanya izinkan akses ke 'index' jika pengguna memiliki permission 'permissions index'
            new Middleware('permission:permissions index', only: ['index']),
            // Hanya izinkan akses ke 'create' dan 'store' jika pengguna memiliki permission 'permissions create'
            new Middleware('permission:permissions create', only: ['create', 'store']),
            // Hanya izinkan akses ke 'edit' dan 'update' jika pengguna memiliki permission 'permissions edit'
            new Middleware('permission:permissions edit', only: ['edit', 'update']),
            // Hanya izinkan akses ke 'destroy' jika pengguna memiliki permission 'permissions delete'
            new Middleware('permission:permissions delete', only: ['destroy']),
        ];
    }

    /**
     * Menampilkan daftar permission yang ada, dengan opsi pencarian.
     */
    public function index(Request $request)
    {
        // Mengambil data permissions berdasarkan pencarian, jika ada
        $permissions = Permission::select('id', 'name')
            // Menggunakan kondisi when untuk pencarian berdasarkan nama
            ->when($request->search, fn($search) => $search->where('name', 'like', '%'.$request->search.'%'))
            // Mengurutkan data berdasarkan waktu terbaru
            ->latest()
            // Memaginate data dengan 6 entri per halaman, serta mempertahankan query string pencarian
            ->paginate(6)->withQueryString();

        // Menampilkan view dengan data permissions dan filter pencarian
        return inertia('Permissions/Index', ['permissions' => $permissions,'filters' => $request->only(['search'])]);
    }

    /**
     * Menampilkan form untuk membuat permission baru.
     */
    public function create()
    {
        // Menampilkan view untuk membuat permission baru
        return inertia('Permissions/Create');
    }

    /**
     * Menyimpan permission baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Melakukan validasi input dari pengguna untuk memastikan nama permission valid
        $request->validate(['name' => 'required|min:3|max:255|unique:permissions']);

        // Membuat data permission baru berdasarkan input pengguna
        Permission::create(['name' => $request->name]);

        // Redirect kembali ke halaman daftar permission setelah data berhasil disimpan
        return to_route('permissions.index');
    }

    /**
     * Menampilkan form untuk mengedit permission yang sudah ada.
     */
    public function edit(Permission $permission)
    {
        // Menampilkan view untuk mengedit permission yang dipilih
        return inertia('Permissions/Edit', ['permission' => $permission]);
    }

    /**
     * Memperbarui data permission yang ada di database.
     */
    public function update(Request $request, Permission $permission)
    {
        // Melakukan validasi input dari pengguna untuk memastikan nama permission valid, dan unik kecuali untuk permission yang sedang diedit
        $request->validate(['name' => 'required|min:3|max:255|unique:permissions,name,'.$permission->id]);

        // Memperbarui data permission yang ada berdasarkan input pengguna
        $permission->update(['name' => $request->name]);

        // Redirect kembali ke halaman daftar permission setelah data berhasil diperbarui
        return to_route('permissions.index');
    }

    /**
     * Menghapus permission yang dipilih dari database.
     */
    public function destroy(Permission $permission)
    {
        // Menghapus data permission dari database
        $permission->delete();

        // Mengarahkan kembali ke halaman sebelumnya setelah data dihapus
        return back();
    }
}
