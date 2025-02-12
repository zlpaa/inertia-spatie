<?php

// Menentukan namespace untuk controller ini
namespace App\Http\Controllers;

// Mengimpor request yang digunakan untuk memperbarui profil pengguna
use App\Http\Requests\ProfileUpdateRequest;
// Mengimpor MustVerifyEmail untuk memeriksa apakah pengguna harus memverifikasi email
use Illuminate\Contracts\Auth\MustVerifyEmail;
// Mengimpor RedirectResponse untuk menangani response setelah redirect
use Illuminate\Http\RedirectResponse;
// Mengimpor Request untuk menangani input dari pengguna
use Illuminate\Http\Request;
// Mengimpor Auth untuk menangani autentikasi pengguna
use Illuminate\Support\Facades\Auth;
// Mengimpor Redirect untuk menangani proses redirect
use Illuminate\Support\Facades\Redirect;
// Mengimpor Inertia untuk menangani rendering tampilan menggunakan Inertia.js
use Inertia\Inertia;
// Mengimpor Response untuk mendefinisikan tipe response yang dikembalikan oleh metode
use Inertia\Response;

// Controller yang menangani logika profil pengguna
class ProfileController extends Controller
{
    /**
     * Menampilkan formulir profil pengguna untuk diedit.
     * 
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        // Render tampilan 'Profile/Edit' dengan data apakah pengguna perlu memverifikasi email
        // serta status session yang dapat menampilkan pesan setelah pembaruan profil
        return Inertia::render('Profile/Edit', [
            // Memeriksa apakah pengguna harus memverifikasi email
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            // Mengambil status pesan yang disimpan dalam session
            'status' => session('status'),
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     * 
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Mengisi data pengguna dengan data yang telah divalidasi dari form
        $request->user()->fill($request->validated());

        // Jika email pengguna berubah, set email_verified_at menjadi null
        // Ini mengindikasikan bahwa pengguna perlu memverifikasi email baru
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Menyimpan data pengguna yang telah diperbarui
        $request->user()->save();

        // Redirect kembali ke halaman edit profil setelah pembaruan selesai
        return Redirect::route('profile.edit');
    }

    /**
     * Menghapus akun pengguna.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi untuk memastikan pengguna mengisi password saat menghapus akun
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // Mendapatkan data pengguna yang sedang login
        $user = $request->user();

        // Logout pengguna setelah akun dihapus
        Auth::logout();

        // Menghapus data pengguna dari database
        $user->delete();

        // Menghancurkan session pengguna dan menghasilkan token baru untuk mencegah serangan CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect pengguna ke halaman beranda setelah akun dihapus
        return Redirect::to('/');
    }
}
