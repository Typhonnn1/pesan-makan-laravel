<?php

// Mengimpor class Migration dasar dari framework Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor class Blueprint untuk menentukan skema dan kolom tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk eksekusi pembuatan dan penghapusan tabel
use Illuminate\Support\Facades\Schema;

// Mengembalikan instance anonim migrasi
return new class extends Migration
{
    // Method up() dieksekusi saat proses migrasi dijalankan ('php artisan migrate')
    public function up(): void
    {
        // Membuat tabel baru bernama 'order_details' untuk mencatat setiap makanan dalam pesanan
        Schema::create('order_details', function (Blueprint $table) {
            // Kolom 'id' sebagai Primary Key unik bertipe Unsigned BigInteger Auto Increment
            $table->id();

            // Membuat kolom Foreign Key 'order_id' bertipe UNSIGNED BIGINT yang terhubung ke tabel 'orders'
            $table->foreignId('order_id')
                // Menghubungkan relasi foreign key ke kolom 'id' di tabel 'orders'
                ->constrained('orders')
                // Jika data pesanan utama di tabel 'orders' dihapus, seluruh rincian item pesanan ini otomatis terhapus (cascade)
                ->onDelete('cascade');

            // Membuat kolom Foreign Key 'food_id' bertipe UNSIGNED BIGINT yang terhubung ke tabel 'foods'
            $table->foreignId('food_id')
                // Menghubungkan relasi foreign key ke kolom 'id' di tabel 'foods'
                ->constrained('foods')
                // Jika data makanan di tabel 'foods' dihapus, rincian pesanan terkait makanan ini ikut terhapus
                ->onDelete('cascade');

            // Kolom 'quantity' (INTEGER) untuk mencatat jumlah porsi atau porsi yang dibeli
            $table->integer('quantity');

            // Kolom 'subtotal' (INTEGER) untuk total harga per item makanan (harga satuan dikali jumlah quantity)
            $table->integer('subtotal');

            // Kolom timestamps bawaan Laravel: 'created_at' dan 'updated_at'
            $table->timestamps();
        });
    }

    // Method down() dieksekusi saat proses rollback migrasi dilakukan ('php artisan migrate:rollback')
    public function down(): void
    {
        // Menghapus tabel 'order_details' jika ada di database
        Schema::dropIfExists('order_details');
    }
};
