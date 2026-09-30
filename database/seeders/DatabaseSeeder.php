<?php

// Menentukan namespace lokasi class Seeder
namespace Database\Seeders;

// Mengimpor Model User untuk membuat data akun user default
use App\Models\User;
// Mengimpor trait WithoutModelEvents agar event model (seperti observer) dinonaktifkan saat proses seeding berlangsung
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// Mengimpor class dasar Seeder bawaan Laravel
use Illuminate\Database\Seeder;
// Mengimpor Facade Hash untuk mengenkripsi password dengan algoritma hashing aman
use Illuminate\Support\Facades\Hash;

// DatabaseSeeder adalah seeder utama yang dieksekusi ketika menjalankan perintah 'php artisan db:seed'
class DatabaseSeeder extends Seeder
{
    // Mengaktifkan pengabaian event model agar proses insert seeder lebih cepat dan bersih
    use WithoutModelEvents;

    // Method run() merupakan titik masuk utama untuk menjalankan semua data seeding
    public function run(): void
    {
        // 1. Membuat akun default User Admin untuk keperluan login pertama kali
        User::create([
            'name'     => 'Admin Toko',                // Nama akun admin
            'email'    => 'admin@gmail.com',           // Alamat email untuk login
            'password' => Hash::make('password123'),   // Mengenkripsi password 'password123' sebelum disimpan ke database
        ]);

        // 2. Memanggil class FoodSeeder untuk mengisi data awal menu makanan dan minuman
        $this->call([
            FoodSeeder::class, // Menjalankan pengisian data awal dari class FoodSeeder
        ]);
    }
}
