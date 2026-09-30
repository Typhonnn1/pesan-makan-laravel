<?php

// Menentukan namespace lokasi controller ini berada di dalam struktur folder Laravel
namespace App\Http\Controllers;

// Mengimpor Model Food untuk berinteraksi dengan tabel 'foods' di database
use App\Models\Food;
// Mengimpor Request class untuk menangkap data yang dikirim dari form HTTP
use Illuminate\Http\Request;
// Mengimpor Facade Storage untuk manajemen file (simpan dan hapus gambar di server/storage)
use Illuminate\Support\Facades\Storage;

// Mendefinisikan class FoodController yang mewarisi class dasar Controller
class FoodController extends Controller
{
    // Method untuk menampilkan daftar seluruh data makanan (halaman index)
    public function index()
    {
        // Mengambil data makanan dari database: diurutkan dari yang terbaru (latest) dan dibagi 10 data per halaman (paginate)
        $foods = Food::latest()->paginate(10);
        // Mengembalikan tampilan view 'admin/foods/index.blade.php' sambil mengirimkan variabel $foods menggunakan compact()
        return view('admin.foods.index', compact('foods'));
    }

    // Method untuk menampilkan halaman form penambahan makanan baru
    public function create()
    {
        // Merender tampilan form tambah makanan pada view 'admin/foods/create.blade.php'
        return view('admin.foods.create');
    }

    // Method untuk menyimpan data makanan baru yang dikirimkan dari form (HTTP POST)
    public function store(Request $request)
    {
        // Memvalidasi data input dari form sebelum dimasukkan ke database
        $request->validate([
            // 'name' wajib diisi, bertipe string/teks, maksimal 255 karakter
            'name'        => 'required|string|max:255',
            // 'category' wajib diisi dan nilainya harus salah satu dari: Makanan, Minuman, atau Cemilan
            'category'    => 'required|in:Makanan,Minuman,Cemilan',
            // 'price' wajib diisi, berupa angka numerik, dan minimal bernilai 0
            'price'       => 'required|numeric|min:0',
            // 'description' wajib diisi berupa teks penjelasan makanan
            'description' => 'required|string',
            // 'image' bersifat opsional (nullable), harus berupa file gambar berformat jpeg, png, atau jpg, maksimal ukuran 2MB (2048 KB)
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Inisialisasi variabel path gambar dengan null jika user tidak mengunggah gambar
        $imagePath = null;
        // Memeriksa apakah ada file gambar yang diunggah dari input form 'image'
        if ($request->hasFile('image')) {
            // Menyimpan file gambar ke folder 'foods' di dalam disk 'public' (storage/app/public/foods) dan menghasilkan nama file acak yang unik
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        // Menyimpan data makanan baru ke dalam tabel 'foods' di database menggunakan Mass Assignment Eloquent
        Food::create([
            'name'        => $request->name,        // Mengisi kolom 'name' dari input nama makanan
            'category'    => $request->category,    // Mengisi kolom 'category' dari input kategori
            'price'       => $request->price,       // Mengisi kolom 'price' dari input harga
            'description' => $request->description, // Mengisi kolom 'description' dari input deskripsi
            'image'       => $imagePath,            // Mengisi kolom 'image' dengan path gambar yang sudah disimpan
        ]);

        // Mengarahkan kembali user ke halaman rute 'foods.index' dengan pesan notifikasi sukses di session
        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil ditambahkan!');
    }

    // Method untuk menampilkan form edit data makanan berdasarkan ID makanan (Route Model Binding)
    public function edit(Food $food)
    {
        // Merender view 'admin/foods/edit.blade.php' dan membawa objek model $food yang akan diedit
        return view('admin.foods.edit', compact('food'));
    }

    // Method untuk menampilkan detail satu data makanan tertentu (foods.show)
    // Otomatis terdaftar oleh Route::resource, mencegah BadMethodCallException jika URL /foods/{id} diakses
    public function show(Food $food)
    {
        // Mengarahkan ke form edit atau menampilkan view detail makanan yang dipilih
        return view('admin.foods.edit', compact('food'));
    }

    // Method untuk memproses pembaruan data makanan yang telah diedit (HTTP PUT/PATCH)
    public function update(Request $request, Food $food)
    {
        // Validasi input data yang diubah untuk memastikan integritas dan keamanan data
        $request->validate([
            // 'name' wajib diisi, bertipe teks, maksimal 255 karakter
            'name'        => 'required|string|max:255',
            // 'category' wajib berupa salah satu opsi yang valid: Makanan, Minuman, atau Cemilan
            'category'    => 'required|in:Makanan,Minuman,Cemilan',
            // 'price' wajib diisi, bertipe numerik, dan bernilai minimal 0 (tidak boleh negatif)
            'price'       => 'required|numeric|min:0',
            // 'description' wajib diisi berupa penjelasan teks makanan
            'description' => 'required|string',
            // 'image' bersifat opsional, harus file gambar jpg/jpeg/png maksimal 2MB (2048 KB)
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Default path gambar tetap menggunakan path gambar lama dari database
        $imagePath = $food->image;
        // Jika ada file gambar baru yang diunggah oleh user
        if ($request->hasFile('image')) {
            // Periksa apakah gambar lama ada di disk 'public', jika ada maka hapus file gambar lama tersebut agar tidak menumpuk di storage
            if ($food->image && Storage::disk('public')->exists($food->image)) {
                Storage::disk('public')->delete($food->image);
            }
            // Simpan file gambar baru ke folder 'foods' pada disk 'public'
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        // Memperbarui record makanan di database dengan nilai-nilai baru
        $food->update([
            'name'        => $request->name,        // Memperbarui nama makanan
            'category'    => $request->category,    // Memperbarui kategori makanan
            'price'       => $request->price,       // Memperbarui harga makanan
            'description' => $request->description, // Memperbarui deskripsi makanan
            'image'       => $imagePath,            // Memperbarui nama/path gambar baru atau tetap lama
        ]);

        // Mengarahkan kembali ke daftar makanan dengan pesan notifikasi berhasil diperbarui
        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil diperbarui!');
    }

    // Method untuk menghapus data makanan dari database (HTTP DELETE)
    public function destroy(Food $food)
    {
        // Periksa apakah record makanan memiliki file gambar fisik di storage, jika ada hapus filenya
        if ($food->image && Storage::disk('public')->exists($food->image)) {
            Storage::disk('public')->delete($food->image);
        }
        // Menghapus data baris record makanan dari tabel di database
        $food->delete();

        // Mengarahkan kembali ke halaman index makanan dengan pesan notifikasi berhasil dihapus
        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil dihapus!');
    }
}
