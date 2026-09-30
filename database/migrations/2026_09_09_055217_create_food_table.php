<?php

// Mengimpor class Migration dasar dari framework Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor Blueprint untuk mendefinisikan struktur kolom tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk manipulasi skema database (create, drop, modify)
use Illuminate\Support\Facades\Schema;

// Mengembalikan instance class anonim migrasi yang mewarisi Migration
return new class extends Migration
{
    // Method up() dijalankan saat perintah 'php artisan migrate' dieksekusi untuk membuat tabel
    public function up(): void
    {
        // Membuat tabel baru bernama 'foods' di database
        Schema::create('foods', function (Blueprint $table) {
            // Membuat kolom 'id' sebagai Primary Key, auto-increment, bertipe data BigInteger (Unsigned)
            $table->id();

            // Membuat kolom 'name' bertipe VARCHAR(255) untuk menyimpan nama makanan/minuman
            $table->string('name');

            // Membuat kolom 'category' bertipe ENUM yang pilihannya terbatas hanya pada: 'Makanan', 'Minuman', atau 'Cemilan'
            $table->enum('category', ['Makanan', 'Minuman', 'Cemilan']);

            // Membuat kolom 'price' bertipe data INTEGER untuk menyimpan nominal harga makanan dalam rupiah
            $table->integer('price');

            // Membuat kolom 'description' bertipe TEXT untuk menyimpan rincian atau deskripsi panjang makanan
            $table->text('description');

            // Membuat kolom 'image' bertipe VARCHAR(255) untuk path file gambar, nullable artinya boleh bernilai null jika tidak ada gambar
            $table->string('image')->nullable();

            // Membuat otomatis dua kolom timestamp: 'created_at' (waktu data dibuat) dan 'updated_at' (waktu data terakhir diubah)
            $table->timestamps();
        });
    }

    // Method down() dijalankan saat membatalkan migrasi ('php artisan migrate:rollback')
    public function down(): void
    {
        // Menghapus tabel 'foods' dari database jika tabel tersebut ada
        Schema::dropIfExists('foods');
    }
};
