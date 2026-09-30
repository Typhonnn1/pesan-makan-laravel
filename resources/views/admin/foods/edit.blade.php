{{-- // Menggunakan template layout utama aplikasi (layouts/app.blade.php) bawaan Laravel Breeze --}}
<x-app-layout>
    {{-- // Slot header untuk judul navbar atas halaman edit --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Makanan</h2>
    </x-slot>

    {{-- // Wrapper container dengan lebar maksimal 2xl agar form berada rapi di tengah --}}
    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        {{-- // Form mengirim data update ke rute foods.update dengan parameter ID makanan. enctype multipart diperlukan untuk file upload --}}
        <form action="{{ route('foods.update', $food->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-sm border">
            {{-- // Token proteksi CSRF untuk keamanan form submit --}}
            @csrf
            {{-- // Directive @method('PUT') melakukan method spoofing agar form diproses sebagai request HTTP PUT oleh controller --}}
            @method('PUT')

            {{-- // Input grup: Nama Makanan --}}
            <div class="mb-4">
                {{-- // Label keterangan untuk field input nama makanan --}}
                <label class="block font-medium mb-1">Nama Makanan</label>
                {{-- // Input teks untuk nama makanan, atribut value diisi otomatis dengan data nama makanan saat ini ($food->name) --}}
                <input type="text" name="name" value="{{ $food->name }}" class="w-full border rounded p-2" required>
            </div>

            {{-- // Input grup: Kategori Makanan --}}
            <div class="mb-4">
                {{-- // Label keterangan dropdown kategori --}}
                <label class="block font-medium mb-1">Kategori</label>
                {{-- // Dropdown pilihan kategori makanan --}}
                <select name="category" class="w-full border rounded p-2" required>
                    {{-- // Opsi Makanan: Ternary operator mengecek jika kategori bernilai 'Makanan', tambahkan atribut 'selected' --}}
                    <option value="Makanan" {{ $food->category == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                    {{-- // Opsi Minuman: Ternary operator mengecek jika kategori bernilai 'Minuman', tambahkan atribut 'selected' --}}
                    <option value="Minuman" {{ $food->category == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                    {{-- // Opsi Cemilan: Ternary operator mengecek jika kategori bernilai 'Cemilan', tambahkan atribut 'selected' --}}
                    <option value="Cemilan" {{ $food->category == 'Cemilan' ? 'selected' : '' }}>Cemilan</option>
                </select>
            </div>

            {{-- // Input grup: Harga Makanan --}}
            <div class="mb-4">
                {{-- // Label keterangan input nominal harga --}}
                <label class="block font-medium mb-1">Harga (Rp)</label>
                {{-- // Input number untuk nominal harga, value terisi otomatis dari nilai harga yang tersimpan ($food->price) --}}
                <input type="number" name="price" value="{{ $food->price }}" class="w-full border rounded p-2" required>
            </div>

            {{-- // Input grup: Deskripsi Makanan --}}
            <div class="mb-4">
                {{-- // Label keterangan textarea deskripsi --}}
                <label class="block font-medium mb-1">Deskripsi</label>
                {{-- // Textarea deskripsi makanan: Nilai teks diletakkan di antara tag pembuka dan penutup, bukan di atribut value --}}
                <textarea name="description" class="w-full border rounded p-2" rows="3" required>{{ $food->description }}</textarea>
            </div>

            {{-- // Input grup: Upload File Gambar Baru --}}
            <div class="mb-4">
                {{-- // Label keterangan opsi upload gambar baru --}}
                <label class="block font-medium mb-1">Gambar Baru (Opsional)</label>
                {{-- // Input file: Bersifat opsional, jika dikosongkan controller akan tetap mempertahankan gambar yang sudah ada --}}
                <input type="file" name="image" class="w-full border rounded p-2">
            </div>

            {{-- // Tombol submit untuk mengirimkan pembaruan data ke FoodController@update via HTTP PUT --}}
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded font-semibold">Update Data</button>
        </form>
    </div>
</x-app-layout>
