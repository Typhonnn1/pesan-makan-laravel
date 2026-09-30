<?php

// Mengimpor class Migration dari framework Laravel
use Illuminate\Database\Migrations\Migration;
// Mengimpor class Blueprint untuk merancang struktur kolom tabel
use Illuminate\Database\Schema\Blueprint;
// Mengimpor Facade Schema untuk mengeksekusi operasi skema database
use Illuminate\Support\Facades\Schema;

// Mengembalikan instance anonim class migrasi
return new class extends Migration
{
    // Method up() dieksekusi saat proses migrasi database dijalankan ('php artisan migrate')
    public function up(): void
    {
        // Membuat tabel baru bernama 'orders' untuk menampung data pesanan pelanggan
        Schema::create('orders', function (Blueprint $table) {
            // Kolom 'id' sebagai Primary Key unik bertipe Unsigned BigInteger Auto Increment
            $table->id();

            // Kolom 'customer_name' (VARCHAR 255) untuk menyimpan nama pelanggan yang memesan
            $table->string('customer_name');

            // Kolom 'table_number' (VARCHAR 255) untuk nomor meja (tipe string fleksibel untuk meja "1A", "05", dll)
            $table->string('table_number');

            // Kolom 'total_price' (INTEGER) untuk total biaya keseluruhan pesanan dalam rupiah
            $table->integer('total_price');

            // Kolom 'status' (VARCHAR 255) status pesanan dengan nilai bawaan awal 'Pending' (misal: Pending, Processing, Completed, Cancelled)
            $table->string('status')->default('Pending');

            // Kolom timestamp bawaan Laravel: 'created_at' dan 'updated_at'
            $table->timestamps();
        });
    }

    // Method down() dieksekusi saat membatalkan/rollback migrasi ('php artisan migrate:rollback')
    public function down(): void
    {
        // Menghapus tabel 'orders' jika sudah ada di database
        Schema::dropIfExists('orders');
    }
};
