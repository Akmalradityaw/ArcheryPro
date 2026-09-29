<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Autentikasi user via REST API dan generate Bearer token Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::with(['atlet.sekolah', 'atlet.kategori'])
            ->where('username', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->fail('Username atau password yang dimasukkan salah.', 401);
        }

        // Hapus token lama perangkat jika ada, lalu generate token baru
        $token = $user->createToken('archerypro-mobile-' . now()->timestamp)->plainTextToken;

        return $this->ok([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'atlet' => $user->atlet ? [
                    'id' => $user->atlet->id,
                    'nia' => $user->atlet->nia,
                    'nama_lengkap' => $user->atlet->nama_lengkap,
                    'nomor_target' => $user->atlet->nomor_target,
                    'sekolah' => $user->atlet->sekolah?->nama_sekolah,
                    'kategori' => $user->atlet->kategori?->nama_kategori,
                    'jarak_kategori' => $user->atlet->kategori?->jarak_tempuh,
                    'izinkan_tampil_publik' => (bool) $user->atlet->izinkan_tampil_publik,
                ] : null,
            ],
        ], 'Login berhasil.');
    }

    /**
     * Informasi profil user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['atlet.sekolah', 'atlet.kategori']);

        return $this->ok([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'atlet' => $user->atlet ? [
                'id' => $user->atlet->id,
                'nia' => $user->atlet->nia,
                'nama_lengkap' => $user->atlet->nama_lengkap,
                'nomor_target' => $user->atlet->nomor_target,
                'sekolah' => $user->atlet->sekolah?->nama_sekolah,
                'kategori' => $user->atlet->kategori?->nama_kategori,
                'jarak_kategori' => $user->atlet->kategori?->jarak_tempuh,
                'izinkan_tampil_publik' => (bool) $user->atlet->izinkan_tampil_publik,
            ] : null,
        ]);
    }

    /**
     * Revoke Bearer token saat logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, 'Berhasil logout dan token dicabut.');
    }
}
