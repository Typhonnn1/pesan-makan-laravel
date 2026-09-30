# 🍽️ Sistem Pemesanan Makanan (Pesan Makan)

Aplikasi web manajemen menu dan pemesanan makanan berbasis **Laravel 12**, **Tailwind CSS**, dan **MySQL**. Sistem ini dilengkapi dengan modul autentikasi (Laravel Breeze), manajemen data menu (CRUD Makanan, Minuman, Cemilan beserta upload gambar), dan struktur database untuk transaksi pemesanan meja restoran.

---

## 🚀 Fitur Utama

- **Autentikasi Pengguna & Admin (Laravel Breeze - Modul 2)**:
  - Registrasi, Login, dan Logout.
  - Manajemen profil pengguna (Update nama, email, dan password).
- **Master Data Menu Makanan (CRUD - Modul 2)**:
  - Tambah menu baru dengan kategori (**Makanan**, **Minuman**, **Cemilan**).
  - Upload dan kelola gambar/foto menu.
  - Edit informasi menu dan otomatis memperbarui file gambar lama.
  - Hapus menu beserta file gambar terkait di storage.
  - Penomoran halaman (*pagination*) data menu.
- **Sisi Customer / Pelanggan (Katalog & Checkout - Modul 3)**:
  - Melihat katalog menu makanan dengan filter kategori real-time JavaScript (Semua, Makanan, Minuman, Cemilan).
  - Input nama lengkap, nomor meja, dan kuantitas menu.
  - Modal konfirmasi pesanan interaktif (rincian item & total harga otomatis).
  - Checkout transaksi multi-tabel atomik dengan `DB::transaction()`.
- **Sisi Admin (Rekap Pesanan & Status - Modul 3)**:
  - Melihat rekap transaksi pesanan masuk beserta rincian pesanan.
  - Mengubah status pesanan secara langsung (**Pending**, **Diproses**, **Selesai**).
- **Database Seeder**:
  - Menyediakan akun admin default dan data menu awal siap pakai.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi |
| :--- | :--- |
| **Framework Backend** | [Laravel 12](https://laravel.com/) (PHP ^8.2) |
| **Frontend & Styling** | [Tailwind CSS](https://tailwindcss.com/), [Alpine.js](https://alpinejs.dev/), [Blade Components](https://laravel.com/docs/blade) |
| **Authentication Kit** | [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze) |
| **Database** | MySQL |
| **Asset Bundler** | [Vite](https://vitejs.dev/) |

---

## 📋 Prasyarat Sistem

Sebelum menjalankan proyek ini, pastikan sistem Anda telah terpasang:
- **PHP** >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`)
- **Composer** (PHP Package Manager)
- **Node.js** (v18+ direkomendasikan) & **NPM**
- **MySQL / MariaDB** (melalui XAMPP, Laragon, Docker, atau instalasi native)

---

## ⚙️ Panduan Instalasi & Menjalankan Proyek

Ikuti langkah-langkah berikut untuk mengatur dan menjalankan proyek di lingkungan lokal:

### 1. Masuk ke Direktori Proyek
```bash
cd c:/project_laravel
```

### 2. Install Dependensi PHP (Composer)
```bash
composer install
```

### 3. Install Dependensi Frontend (NPM)
```bash
npm install
```

### 4. Konfigurasi File Environment (`.env`)
Salin file `.env.example` menjadi `.env` (jika belum ada):
```bash
cp .env.example .env
```
Buka file `.env` dan pastikan konfigurasi database sudah sesuai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pesanmakan
DB_USERNAME=root
DB_PASSWORD=
```
> **Catatan**: Pastikan database bernama `pesanmakan` sudah dibuat di MySQL Anda (misalnya melalui phpMyAdmin atau command line MySQL: `CREATE DATABASE pesanmakan;`).

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Hubungkan Storage Link (Wajib untuk Upload Gambar)
Buat symlink direktori `public/storage` ke `storage/app/public`:
```bash
php artisan storage:link
```

### 7. Jalankan Migrasi & Seeder Database
Jalankan migrasi untuk membuat tabel beserta data dummy awal (admin dan menu makanan):
```bash
php artisan migrate --seed
```

### 8. Kompilasi Aset Frontend
Jalankan Vite di mode pengembangan (development):
```bash
npm run dev
```
*Atau lakukan build untuk produksi:*
```bash
npm run build
```

### 9. Jalankan Web Server Laravel
Di terminal terpisah, jalankan server pengembangan:
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 👤 Akun Default (Seeder)

Setelah menjalankan `php artisan migrate --seed`, Anda dapat langsung login menggunakan akun berikut:

- **Email**: `admin@gmail.com`
- **Password**: `password123`

---

## 🗄️ Struktur Basis Data

Aplikasi ini menggunakan skema relasional berikut:

```
[ users ]
   - id
   - name
   - email
   - password
   - timestamps

[ foods ]
   - id
   - name
   - category (enum: 'Makanan', 'Minuman', 'Cemilan')
   - price
   - description
   - image (nullable)
   - timestamps

[ orders ]
   - id
   - customer_name
   - table_number
   - total_price
   - status (default: 'Pending')
   - timestamps

[ order_details ]
   - id
   - order_id (FK -> orders.id [CASCADE])
   - food_id (FK -> foods.id [CASCADE])
   - quantity
   - subtotal
   - timestamps
```

---

## 📁 Struktur File & Direktori Penting

```
project_laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/                   # Controller autentikasi Breeze
│   │       ├── FoodController.php      # Logika CRUD Menu Makanan & Upload Gambar
│   │       ├── OrderController.php     # Controller Pesanan
│   │       └── ProfileController.php   # Manajemen Profil Pengguna
│   └── Models/
│       ├── Food.php                    # Model Food
│       ├── Order.php                   # Model Order
│       ├── OrderDetail.php             # Model OrderDetail
│       └── User.php                    # Model User
├── database/
│   ├── migrations/                     # File skema tabel database
│   └── seeders/
│       ├── DatabaseSeeder.php          # Seeder utama (User default)
│       └── FoodSeeder.php              # Seeder data menu makanan awal
├── resources/
│   └── views/
│       ├── admin/foods/                # Tampilan index, create, dan edit makanan
│       ├── auth/                       # Tampilan halaman login & register
│       ├── layouts/                    # Layout Blade (app, guest, navigation)
│       └── dashboard.blade.php         # Tampilan dashboard setelah login
├── routes/
│   ├── web.php                         # Route web & resource foods
│   └── auth.php                        # Route autentikasi
└── storage/
    └── app/public/foods/               # Lokasi file foto makanan yang diunggah
```

---

## 📝 Lisensi

Aplikasi ini merupakan perangkat lunak *open-source* di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
