<?php

// Menentukan namespace letak file pengujian fitur (Feature Test)
namespace Tests\Feature;

// Mengimpor Model Food dan User untuk simulasi pengujian
use App\Models\Food;
use App\Models\User;
// Mengimpor trait RefreshDatabase agar skema database di-reset bersih setiap kali pengujian dijalankan
use Illuminate\Foundation\Testing\RefreshDatabase;
// Mengimpor UploadedFile untuk membuat file dummy gambar saat pengujian upload
use Illuminate\Http\UploadedFile;
// Mengimpor Storage untuk memalsukan (fake) disk public storage
use Illuminate\Support\Facades\Storage;
// Mengimpor class dasar TestCase
use Tests\TestCase;

// Class FoodTest menguji seluruh fungsionalitas CRUD pada FoodController
class FoodTest extends TestCase
{
    // Menggunakan trait RefreshDatabase agar setiap pengujian berjalan terisolasi tanpa sisa data pengujian lain
    use RefreshDatabase;

    // Menyiapkan user penguji sebelum setiap metode tes dijalankan
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Membuat akun user penguji dalam database pengujian
        $this->user = User::factory()->create();
    }

    // Uji 1: Pengguna tanpa login (guest) tidak boleh mengakses daftar makanan dan dialihkan ke login
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('foods.index'));
        $response->assertRedirect(route('login'));
    }

    // Uji 2: User terautentikasi dapat melihat halaman daftar makanan (index)
    public function test_authenticated_user_can_view_foods_index(): void
    {
        // Buat sampel data makanan
        Food::create([
            'name' => 'Sate Ayam',
            'category' => 'Makanan',
            'price' => 20000,
            'description' => 'Sate ayam bumbu kacang',
            'image' => null,
        ]);

        $response = $this->actingAs($this->user)->get(route('foods.index'));
        $response->assertStatus(200);
        $response->assertSee('Sate Ayam');
    }

    // Uji 3: User terautentikasi dapat mengakses form penambahan makanan (create)
    public function test_authenticated_user_can_view_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('foods.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Makanan');
    }

    // Uji 4: Validasi wajib mengisi field nama, kategori, harga, dan deskripsi
    public function test_store_validation_errors_on_empty_input(): void
    {
        $response = $this->actingAs($this->user)->post(route('foods.store'), []);
        $response->assertSessionHasErrors(['name', 'category', 'price', 'description']);
    }

    // Uji 5: User dapat menyimpan data makanan baru tanpa gambar
    public function test_authenticated_user_can_store_food_without_image(): void
    {
        $response = $this->actingAs($this->user)->post(route('foods.store'), [
            'name' => 'Nasi Uduk',
            'category' => 'Makanan',
            'price' => 15000,
            'description' => 'Nasi uduk gurih lengkap',
        ]);

        $response->assertRedirect(route('foods.index'));
        $response->assertSessionHas('success', 'Data makanan berhasil ditambahkan!');

        $this->assertDatabaseHas('foods', [
            'name' => 'Nasi Uduk',
            'category' => 'Makanan',
            'price' => 15000,
            'image' => null,
        ]);
    }

    // Helper untuk membuat dummy file gambar JPEG valid tanpa memerlukan ekstensi GD
    private function createDummyImage(string $filename = 'test.jpg'): UploadedFile
    {
        // 1x1 pixel valid JPEG binary (Base64 decoded)
        $binary = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        return UploadedFile::fake()->createWithContent($filename, $binary);
    }

    // Uji 6: User dapat menyimpan data makanan baru beserta file gambar
    public function test_authenticated_user_can_store_food_with_image(): void
    {
        // Membuat disk public virtual (in-memory fake)
        Storage::fake('public');

        $file = $this->createDummyImage('es_jeruk.jpg');

        $response = $this->actingAs($this->user)->post(route('foods.store'), [
            'name' => 'Es Jeruk Segar',
            'category' => 'Minuman',
            'price' => 7000,
            'description' => 'Es jeruk peras asli',
            'image' => $file,
        ]);

        $response->assertRedirect(route('foods.index'));

        // Memastikan record tersimpan di database
        $food = Food::where('name', 'Es Jeruk Segar')->first();
        $this->assertNotNull($food);
        $this->assertNotNull($food->image);

        // Memastikan file gambar benar-benar tersimpan di storage public
        Storage::disk('public')->assertExists($food->image);
    }

    // Uji 7: Akses ke route foods.show berhasil (mencegah BadMethodCallException)
    public function test_authenticated_user_can_view_food_show(): void
    {
        $food = Food::create([
            'name' => 'Tahu Gejrot',
            'category' => 'Cemilan',
            'price' => 10000,
            'description' => 'Tahu goreng dengan kuah asam pedas manis',
            'image' => null,
        ]);

        $response = $this->actingAs($this->user)->get(route('foods.show', $food));
        $response->assertStatus(200);
        $response->assertSee('Tahu Gejrot');
    }

    // Uji 8: User dapat membuka form edit makanan
    public function test_authenticated_user_can_view_edit_page(): void
    {
        $food = Food::create([
            'name' => 'Bakso Sapi',
            'category' => 'Makanan',
            'price' => 18000,
            'description' => 'Bakso urat sapi asli',
            'image' => null,
        ]);

        $response = $this->actingAs($this->user)->get(route('foods.edit', $food));
        $response->assertStatus(200);
        $response->assertSee('Bakso Sapi');
    }

    // Uji 9: User dapat memperbarui data makanan dan mengganti gambar lama dengan gambar baru
    public function test_authenticated_user_can_update_food_and_replace_image(): void
    {
        Storage::fake('public');

        // Buat file gambar awal
        $oldFile = $this->createDummyImage('old_food.jpg');
        $oldPath = $oldFile->store('foods', 'public');

        $food = Food::create([
            'name' => 'Kopi Hitam',
            'category' => 'Minuman',
            'price' => 8000,
            'description' => 'Kopi tubruk robusta',
            'image' => $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        // Siapkan file gambar baru sebagai pengganti
        $newFile = $this->createDummyImage('new_coffee.jpg');

        $response = $this->actingAs($this->user)->put(route('foods.update', $food), [
            'name' => 'Kopi Hitam Spesial',
            'category' => 'Minuman',
            'price' => 10000,
            'description' => 'Kopi arabika gayo',
            'image' => $newFile,
        ]);

        $response->assertRedirect(route('foods.index'));
        $response->assertSessionHas('success', 'Data makanan berhasil diperbarui!');

        // Refresh model dari database
        $food->refresh();

        $this->assertEquals('Kopi Hitam Spesial', $food->name);
        $this->assertEquals(10000, $food->price);

        // Gambar lama harus sudah dihapus dari disk public
        Storage::disk('public')->assertMissing($oldPath);
        // Gambar baru harus tersimpan di disk public
        Storage::disk('public')->assertExists($food->image);
    }

    // Uji 10: User dapat menghapus data makanan beserta file fisik gambarnya
    public function test_authenticated_user_can_delete_food_and_its_image(): void
    {
        Storage::fake('public');

        $file = $this->createDummyImage('pisang_goreng.jpg');
        $path = $file->store('foods', 'public');

        $food = Food::create([
            'name' => 'Pisang Goreng',
            'category' => 'Cemilan',
            'price' => 12000,
            'description' => 'Pisang goreng tepung renyah',
            'image' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->user)->delete(route('foods.destroy', $food));

        $response->assertRedirect(route('foods.index'));
        $response->assertSessionHas('success', 'Data makanan berhasil dihapus!');

        // Memastikan record terhapus dari tabel database
        $this->assertDatabaseMissing('foods', ['id' => $food->id]);
        // Memastikan file gambar ikut terhapus dari disk storage
        Storage::disk('public')->assertMissing($path);
    }
}
