<?php

// Menentukan namespace lokasi class FoodSeeder
namespace Database\Seeders;

// Mengimpor trait WithoutModelEvents bawaan Laravel
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// Mengimpor class dasar Seeder
use Illuminate\Database\Seeder;
// Mengimpor Facade DB untuk melakukan operasi query builder langsung ke database
use Illuminate\Support\Facades\DB;

// Class FoodSeeder bertugas untuk mengisi data awal (dummy/sample data) menu makanan ke tabel 'foods'
class FoodSeeder extends Seeder
{
    // Method run() akan dieksekusi saat seeder ini dipanggil oleh DatabaseSeeder atau perintah artisan
    public function run(): void
    {
        // Memasukkan sekumpulan data array langsung ke tabel 'foods' menggunakan query builder insert()
        // Catatan: DB::table()->insert() membypass event Eloquent sehingga timestamps (created_at & updated_at) wajib diisi manual
        DB::table('foods')->insert([
            // Data menu 1: Nasi Goreng Spesial
            [
                'name'        => 'Nasi Goreng Spesial',                               // Nama makanan
                'category'    => 'Makanan',                                            // Kategori menu
                'price'       => 25000,                                                // Harga dalam rupiah
                'description' => 'Nasi goreng dengan telur, ayam suwir, dan kerupuk.', // Keterangan menu
                'image'       => null,                                                 // Belum ada file gambar (null)
                'created_at'  => now(),                                                // Timestamp waktu saat ini
                'updated_at'  => now(),                                                // Timestamp waktu update saat ini
            ],
            // Data menu 2: Mie Goreng Seafood
            [
                'name'        => 'Mie Goreng Seafood',
                'category'    => 'Makanan',
                'price'       => 28000,
                'description' => 'Mie goreng pedas dengan udang dan cumi.',
                'image'       => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Data menu 3: Es Teh Manis
            [
                'name'        => 'Es Teh Manis',
                'category'    => 'Minuman',
                'price'       => 5000,
                'description' => 'Es teh melati segar.',
                'image'       => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Data menu 4: Jus Alpukat
            [
                'name'        => 'Jus Alpukat',
                'category'    => 'Minuman',
                'price'       => 15000,
                'description' => 'Jus alpukat murni dengan susu cokelat.',
                'image'       => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Data menu 5: Kentang Goreng
            [
                'name'        => 'Kentang Goreng',
                'category'    => 'Cemilan',
                'price'       => 12000,
                'description' => 'Kentang goreng renyah dengan saus keju.',
                'image'       => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
