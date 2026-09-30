<?php

// Menentukan namespace lokasi class model Order
namespace App\Models;

// Mengimpor trait HasFactory untuk membuat data tiruan/testing (factory)
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor class dasar Model Eloquent
use Illuminate\Database\Eloquent\Model;

// Model Order merepresentasikan tabel 'orders' untuk menyimpan data transaksi pesanan pelanggan
class Order extends Model
{
    // Mengaktifkan penggunaan factory untuk model Order
    use HasFactory;

    // Menjaga agar kolom 'id' tidak dapat diisi manual via mass assignment (kolom lainnya bebas diisi massal)
    protected $guarded = ['id'];

    // Relasi One-to-Many: 1 transaksi pesanan (Order) memiliki banyak rincian item makanan (OrderDetail)
    public function orderDetails()
    {
        // hasMany mendefinisikan relasi satu-ke-banyak dengan referensi foreign key 'order_id' pada tabel 'order_details'
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
