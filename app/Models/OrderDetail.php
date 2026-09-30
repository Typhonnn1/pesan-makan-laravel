<?php

// Menentukan namespace lokasi class model OrderDetail
namespace App\Models;

// Mengimpor trait HasFactory untuk pembuatan dummy data/factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor class Model dasar dari Eloquent ORM
use Illuminate\Database\Eloquent\Model;

// Model OrderDetail merepresentasikan tabel perantara/detail transaksi item yang dipesan ('order_details')
class OrderDetail extends Model
{
    // Mengaktifkan fitur factory untuk pembuatan dummy data testing
    use HasFactory;

    // Menentukan nama tabel di database secara spesifik yaitu 'order_details'
    protected $table = 'order_details';

    // Memproteksi kolom 'id' dari pengisian mass-assignment
    protected $guarded = ['id'];

    // Relasi Many-to-One (Belongs To): Setiap satu item rincian pesanan merujuk ke satu data makanan spesifik
    public function food()
    {
        // belongsTo menghubungkan kembali foreign key 'food_id' ke primary key di model Food
        return $this->belongsTo(Food::class, 'food_id');
    }

    // Relasi Many-to-One (Belongs To): Setiap satu item rincian pesanan dimiliki oleh satu nota pesanan induk
    public function order()
    {
        // belongsTo menghubungkan foreign key 'order_id' ke primary key di model Order
        return $this->belongsTo(Order::class, 'order_id');
    }
}
