<?php

// Menentukan namespace letak file model ini berada dalam struktur folder aplikasi Laravel
namespace App\Models;

// Trait HasFactory digunakan untuk mendukung pembuatan dummy data menggunakan Factory (seperti untuk seeder/testing)
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Mengimpor class dasar Model Eloquent ORM bawaan Laravel
use Illuminate\Database\Eloquent\Model;

// Model Food merepresentasikan tabel 'foods' dan menangani logika interaksi database terkait makanan
class Food extends Model
{
    // Mengaktifkan fitur Factory pada model Food
    use HasFactory;

    // Secara eksplisit menentukan nama tabel di database yang terhubung dengan model ini ('foods')
    protected $table = 'foods';

    // Kolom 'id' diproteksi agar tidak bisa diisi secara sembarangan lewat mass assignment (semua kolom lain selain 'id' boleh diisi)
    protected $guarded = ['id'];

    // Relasi One-to-Many: Satu jenis makanan bisa muncul di banyak rincian pesanan (OrderDetail)
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'food_id');
    }
}
