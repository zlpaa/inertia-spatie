<?php

// Menentukan namespace untuk controller ini
namespace App\Http\Controllers;

// Mengimpor Request untuk menangani input dari pengguna
use Illuminate\Http\Request;
// Mengimpor HasMiddleware untuk mengimplementasikan middleware pada controller ini
use Illuminate\Routing\Controllers\HasMiddleware;
// Mengimpor Middleware untuk menambahkan middleware yang memeriksa permission
use Illuminate\Routing\Controllers\Middleware;
// Mengimpor model Role dari Spatie untuk menangani role dalam sistem
use Spatie\Permission\Models\Role;
// Mengimpor model Permission dari Spatie untuk menangani permission dalam sistem
use Spatie\Permission\Models\Permission;

// Controller untuk menangani CRUD (Create, Read, Update, Delete) pada resource Role
class RoleController extends Controller implements HasMiddleware
{
    // Menentukan middleware yang digunakan pada controller ini
    // Middleware membatasi akses berdasarkan permission yang diperlukan untuk setiap aksi
    public static function middleware()
    {
        return [
            // Membatasi akses ke 'index' hanya untuk pengguna yang memiliki permission 'roles index'
            new Middleware('permission:roles index', only: ['index']),
            // Membatasi akses ke 'create' dan 'store' hanya untuk pengguna yang memiliki permission 'roles create'
            new Middleware('permission:roles create', only: ['create', 'store']),
            // Membatasi akses ke 'edit' dan 'update' hanya untuk pengguna yang memiliki permission 'roles edit'
            new Middleware('permission:roles edit', only: ['edit', 'update']),
            // Membatasi akses ke 'destroy' hanya untuk pengguna yang memiliki permission 'roles delete'
            new Middleware('permission:roles delete', only: ['destroy']),
        ];
    }

    /**
     * Menampilkan daftar role yang ada, dengan opsi pencarian.
     */
    public function index(Request $request)
    {
        // Mengambil data role dengan relasi ke permission terkait
        $roles = Role::select('id', 'name')
            // Memuat data permission yang terkait dengan role
            ->with('permissions:id,name')
            // Menambahkan kondisi pencarian jika ada input pencarian dari pengguna
            ->when($request->search, fn($search) => $search->where('name', 'like', '%'.$request->search.'%'))
            // Mengurutkan role berdasarkan waktu terbaru
            ->latest()
            // Memaginate hasil pencarian dengan 6 entri per halaman
            ->paginate(6);

        // Menampilkan tampilan 'Roles/Index' dengan data roles dan filter pencarian
        return inertia('Roles/Index', ['roles' => $roles,'filters' => $request->only(['search'])]);
    }

    /**
     * Menampilkan form untuk membuat role baru.
     */
    public function create()
    {
        // Mengambil data permission yang ada dan mengelompokkan berdasarkan kata pertama dalam nama permission
        $data = Permission::orderBy('name')->pluck('name', 'id');
        $collection = collect($data);
        $permissions = $collection->groupBy(function ($item, $key) {
            // Memecah nama permission menjadi kata-kata dan mengambil kata pertama
            $words = explode(' ', $item);
            return $words[0];
        });

        // Menampilkan tampilan 'Roles/Create' dengan data permissions yang sudah dikelompokkan
        return inertia('Roles/Create', ['permissions' => $permissions]);
    }

    /**
     * Menyimpan role baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Melakukan validasi untuk nama role dan permission yang dipilih
        $request->validate([
            'name' => 'required|min:3|max:255|unique:roles',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        // Membuat data role baru berdasarkan nama yang diberikan
        $role = Role::create(['name' => $request->name]);

        // Memberikan permission yang dipilih ke role yang baru dibuat
        $role->givePermissionTo($request->selectedPermissions);

        // Redirect ke halaman daftar role setelah role berhasil disimpan
        return to_route('roles.index');
    }

    /**
     * Menampilkan form untuk mengedit role yang ada.
     */
    public function edit(Role $role)
    {
        // Mengambil data permission yang ada dan mengelompokkan berdasarkan kata pertama dalam nama permission
        $data = Permission::orderBy('name')->pluck('name', 'id');
        $collection = collect($data);
        $permissions = $collection->groupBy(function ($item, $key) {
            // Memecah nama permission menjadi kata-kata dan mengambil kata pertama
            $words = explode(' ', $item);
            return $words[0];
        });

        // Memuat data permission yang terkait dengan role yang akan diedit
        $role->load('permissions');

        // Menampilkan tampilan 'Roles/Edit' dengan data role dan permissions yang terkait
        return inertia('Roles/Edit', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Memperbarui role yang ada di database.
     */
    public function update(Request $request, Role $role)
    {
        // Melakukan validasi untuk nama role dan permission yang dipilih
        $request->validate([
            'name' => 'required|min:3|max:255|unique:roles,name,'.$role->id,
            'selectedPermissions' => 'required|array|min:1',
        ]);

        // Memperbarui nama role dengan data yang baru
        $role->update(['name' => $request->name]);

        // Menyinkronkan permission yang dipilih dengan role yang ada
        $role->syncPermissions($request->selectedPermissions);

        // Redirect ke halaman daftar role setelah role berhasil diperbarui
        return to_route('roles.index');
    }

    /**
     * Menghapus role dari database.
     */
    public function destroy(Role $role)
    {
        // Menghapus data role dari database
        $role->delete();

        // Mengarahkan kembali ke halaman sebelumnya setelah role dihapus
        return back();
    }
}
