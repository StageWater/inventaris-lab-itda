<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // /register sengaja dihapus: form publik dulu membuat akun Admin Ruangan
    // untuk ruangan mana pun tanpa verifikasi email. Akun admin lab sekarang
    // hanya dibuat Super Admin lewat UserController::store.
    public function test_halaman_pendaftaran_tidak_lagi_ada(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_tidak_bisa_mendaftarkan_akun_lewat_form_publik(): void
    {
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'penyusup@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'ruangan_id' => 1,
        ])->assertNotFound();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'penyusup@example.com']);
    }
}
