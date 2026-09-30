{{-- // Menggunakan template layout utama aplikasi (layouts/app.blade.php) bawaan Laravel Breeze --}}
<x-app-layout>
    {{-- // Slot header untuk menampilkan judul halaman di navbar/header atas --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- // Judul navigasi halaman --}}
            Master Data Makanan
        </h2>
    </x-slot>

    {{-- // Container utama pembungkus konten dengan padding vertikal dan lebar maksimal --}}
    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- // Tombol Tambah Makanan: Hanya tampil jika user sudah login sebagai Admin --}}
        @auth
            <a href="{{ route('foods.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4 inline-block font-semibold">
                + Tambah Makanan
            </a>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded text-blue-700 text-sm">
                Selamat Datang! Silakan lihat daftar menu makanan dan minuman kami di bawah ini.
            </div>
        @endauth

        {{-- // Memeriksa jika ada pesan flash session bernama 'success' yang dikirim dari controller --}}
        @if(session('success'))
            {{-- // Menampilkan kotak notifikasi alert berwarna hijau berisi pesan sukses --}}
            <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- // Tabel untuk menyajikan daftar data menu makanan --}}
        <table class="w-full bg-white border mt-4 shadow-sm rounded-lg overflow-hidden">
            <thead>
                {{-- // Baris judul kolom tabel (Header) --}}
                <tr class="bg-gray-100 border-b text-gray-600 text-sm">
                    <th class="p-3">Gambar</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Harga</th>
                    @auth
                        <th class="p-3">Aksi</th>
                    @endauth
                </tr>
            </thead>
            <tbody>
                {{-- // Looping setiap item makanan dari pagination $foods yang dikirim FoodController@index --}}
                @foreach($foods as $food)
                <tr class="border-b text-center text-sm">
                    <td class="p-3">
                        {{-- // Pengecekan kondisi: Jika item makanan memiliki path gambar --}}
                        @if($food->image)
                            {{-- // Menampilkan gambar menggunakan helper asset() yang merujuk ke symlink public storage --}}
                            <img src="{{ asset('storage/' . $food->image) }}" class="w-16 h-16 object-cover mx-auto rounded">
                        @else
                            {{-- // Jika tidak ada foto makanan di database, tampilkan teks placeholder --}}
                            <span class="text-gray-400 text-xs">No Image</span>
                        @endif
                    </td>
                    {{-- // Menampilkan nama makanan --}}
                    <td class="p-3 font-semibold">{{ $food->name }}</td>
                    {{-- // Menampilkan kategori makanan (Makanan / Minuman / Cemilan) --}}
                    <td class="p-3">{{ $food->category }}</td>
                    {{-- // Menampilkan format rupiah menggunakan number_format() bawaan PHP --}}
                    <td class="p-3 font-bold text-green-600">Rp {{ number_format($food->price) }}</td>
                    @auth
                        <td class="p-3">
                            {{-- // Tombol tautan Edit: mengarah ke rute foods.edit dengan parameter ID makanan --}}
                            <a href="{{ route('foods.edit', $food->id) }}" class="text-blue-600 hover:underline mr-3 font-semibold">Edit</a>

                            {{-- // Form untuk menghapus data makanan dengan metode HTTP DELETE --}}
                            <form action="{{ route('foods.destroy', $food->id) }}" method="POST" class="inline">
                                {{-- // Directive @csrf untuk keamanan token pencegah serangan Cross-Site Request Forgery --}}
                                @csrf
                                {{-- // Directive @method('DELETE') untuk mengubah method form POST menjadi DELETE (method spoofing) --}}
                                @method('DELETE')
                                {{-- // Tombol konfirmasi hapus data dengan dialog JavaScript confirm() --}}
                                <button type="submit" onclick="return confirm('Yakin hapus data ini?')" class="text-red-600 hover:underline font-semibold">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    @endauth
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- // Menampilkan tautan pagination navigasi halaman berikutnya / sebelumnya --}}
        <div class="mt-4">
            {{ $foods->links() }}
        </div>
    </div>
</x-app-layout>
