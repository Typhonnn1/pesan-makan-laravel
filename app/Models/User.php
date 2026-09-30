<?php

// Menentukan namespace lokasi class User
namespace App\Models;

// Mengimpor UserFactory untuk seeding dan testing data user
use Database\Factories\UserFactory;
// Mengimpor trait HasFactory untuk model Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor class Authenticatable agar model User memiliki fitur login dan autentikasi bawaan Laravel
use Illuminate\Foundation\Auth\User as Authenticatable;
// Mengimpor trait Notifiable agar user dapat menerima notifikasi (email, db, sms)
use Illuminate\Notifications\Notifiable;

// Class model User yang mewarisi fitur autentikasi (Authenticatable)
class User extends Authenticatable
{
    // Mengaktifkan fitur Factory untuk dummy data dan Notifiable untuk pengiriman notifikasi
    use HasFactory, Notifiable;

    // Menentukan daftar kolom atribut yang diperbolehkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'name',     // Kolom nama user/admin
        'email',    // Kolom alamat email user untuk login
        'password', // Kolom password user yang telah terenkripsi
    ];

    // Menentukan atribut yang disembunyikan ketika data model diubah menjadi array atau JSON (misalnya pada respon API)
    protected $hidden = [
        'password',       // Menyembunyikan password agar tidak bocor keluar
        'remember_token', // Menyembunyikan token "remember me"
    ];

    // Method untuk menentukan tipe konversi otomatis (type casting) pada atribut model
    protected function casts(): array
    {
        return [
            // Mengonversi string tanggal verifikasi email menjadi objek Carbon/Datetime secara otomatis
            'email_verified_at' => 'datetime',
            // Memastikan password otomatis di-hash menggunakan algoritma aman (Bcrypt) saat disimpan
            'password' => 'hashed',
        ];
    }
}
