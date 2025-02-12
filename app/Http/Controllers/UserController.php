<?php

namespace App\Http\Controllers;

// Mengimpor model User untuk menangani data pengguna
use App\Models\User;
// Mengimpor Request untuk menangani input dari pengguna
use Illuminate\Http\Request;
// Mengimpor model Role dari Spatie untuk menangani role yang terkait dengan pengguna
use Spatie\Permission\Models\Role;
// Mengimpor Middleware untuk menambahkan middleware yang memeriksa permission
use Illuminate\Routing\Controllers\Middleware;
// Mengimpor HasMiddleware untuk mengimplementasikan middleware pada controller ini
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends Controller implements HasMiddleware
{
    // Menentukan middleware yang digunakan pada controller ini
    // Middleware membatasi akses berdasarkan permission yang diperlukan untuk setiap aksi
    public static function middleware()
    {
        return [
            // Membatasi akses ke 'index' hanya untuk pengguna yang memiliki permission 'users index'
            new Middleware('permission:users index', only: ['index']),
            // Membatasi akses ke 'create' dan 'store' hanya untuk pengguna yang memiliki permission 'users create'
            new Middleware('permission:users create', only: ['create', 'store']),
            // Membatasi akses ke 'edit' dan 'update' hanya untuk pengguna yang memiliki permission 'users edit'
            new Middleware('permission:users edit', only: ['edit', 'update']),
            // Membatasi akses ke 'destroy' hanya untuk pengguna yang memiliki permission 'users delete'
            new Middleware('permission:users delete', only: ['destroy']),
        ];
    }

    /**
     * Menampilkan daftar semua pengguna, dengan pencarian nama pengguna.
     */
    public function index(Request $request)
    {
        // Mengambil data pengguna dengan relasi ke roles
        $users = User::with('roles')
            // Menambahkan kondisi pencarian berdasarkan nama pengguna jika ada input pencarian
            ->when(request('search'), fn($query) => $query->where('name', 'like', '%'.request('search').'%'))
            // Mengurutkan data pengguna berdasarkan yang terbaru
            ->latest()
            // Memaginate hasil pencarian dengan 6 entri per halaman
            ->paginate(6);

        // Menampilkan tampilan 'Users/Index' dengan data pengguna dan filter pencarian
        return inertia('Users/Index', ['users' => $users,'filters' => $request->only(['search'])]);
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        // Mengambil semua data role yang ada
        $roles = Role::latest()->get();

        // Menampilkan tampilan 'Users/Create' dengan data roles
        return inertia('Users/Create', ['roles' => $roles]);
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Melakukan validasi terhadap input pengguna
        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:4',
            'selectedRoles' => 'required|array|min:1',
        ]);

        // Membuat pengguna baru dan mengenkripsi password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Menugaskan role yang dipilih kepada pengguna yang baru dibuat
        $user->assignRole($request->selectedRoles);

        // Redirect ke halaman daftar pengguna setelah pengguna berhasil disimpan
        return to_route('users.index');
    }

    /**
     * Menampilkan detail pengguna berdasarkan ID (tidak digunakan di sini).
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Menampilkan form untuk mengedit pengguna yang sudah ada.
     */
    public function edit(User $user)
    {
        // Mengambil semua data role, kecuali role 'super-admin'
        $roles = Role::where('name', '!=', 'super-admin')->get();

        // Memuat data roles yang terkait dengan pengguna yang akan diedit
        $user->load('roles');

        // Menampilkan tampilan 'Users/Edit' dengan data pengguna dan roles yang tersedia
        return inertia('Users/Edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Memperbarui data pengguna yang sudah ada di database.
     */
    public function update(Request $request, User $user)
    {
        // Melakukan validasi terhadap input pengguna
        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'selectedRoles' => 'required|array|min:1',
        ]);

        // Memperbarui data nama dan email pengguna
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Menyinkronkan roles yang dipilih dengan pengguna yang ada
        $user->syncRoles($request->selectedRoles);

        // Redirect ke halaman daftar pengguna setelah pengguna berhasil diperbarui
        return to_route('users.index');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $user)
    {
        // Menghapus data pengguna dari database
        $user->delete();

        // Mengarahkan kembali ke halaman sebelumnya setelah pengguna dihapus
        return back();
    }
}
