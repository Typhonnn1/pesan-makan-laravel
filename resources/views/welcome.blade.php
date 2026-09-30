<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu Restoran</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN untuk interaktivitas filter & kuantitas -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-gray-800 min-h-screen pb-28" x-data="{ 
    activeTab: 'Semua Menu',
    items: {},
    totalQty: 0,
    totalPrice: 0,
    updateQty(id, price, change) {
        if (!this.items[id]) this.items[id] = 0;
        let newQty = this.items[id] + change;
        if (newQty < 0) newQty = 0;
        this.items[id] = newQty;
        this.calculateTotal();
    },
    calculateTotal() {
        let qty = 0;
        let price = 0;
        for (let id in this.items) {
            let count = this.items[id];
            qty += count;
            let el = document.getElementById('food-price-' + id);
            if (el) {
                price += count * parseInt(el.value);
            }
        }
        this.totalQty = qty;
        this.totalPrice = price;
    }
}">

    <!-- Top Admin Link Bar -->
    <header class="max-w-5xl mx-auto px-4 pt-4 flex justify-end">
        @auth
            <a href="{{ route('foods.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-white px-3.5 py-1.5 rounded-full border border-gray-200 shadow-sm transition">
                ⚙️ Kelola Menu (Admin: {{ Auth::user()->name }})
            </a>
        @else
            <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-600 hover:text-blue-600 bg-white px-3.5 py-1.5 rounded-full border border-gray-200 shadow-sm transition">
                🔒 Login Admin
            </a>
        @endauth
    </header>

    <!-- Main Container -->
    <main class="max-w-5xl mx-auto px-4 pt-6">

        <!-- Notifikasi Pesan Sukses / Error -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">✅</span>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">⚠️</span>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
            </div>
        @endif

        <!-- Judul Halaman -->
        <div class="text-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Menu Restoran</h1>
            <p class="text-gray-500 text-xs sm:text-sm mt-1">Pilih menu makanan dan masukkan nomor meja Anda</p>
        </div>

        <!-- Filter Kategori Tabs (Sesuai Screenshot) -->
        <div class="flex flex-wrap justify-center items-center gap-2 mb-8">
            <button 
                type="button"
                @click="activeTab = 'Semua Menu'" 
                :class="activeTab === 'Semua Menu' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-5 py-1.5 rounded-full text-xs sm:text-sm font-semibold transition">
                Semua Menu
            </button>
            <button 
                type="button"
                @click="activeTab = 'Makanan'" 
                :class="activeTab === 'Makanan' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-5 py-1.5 rounded-full text-xs sm:text-sm font-semibold transition">
                Makanan
            </button>
            <button 
                type="button"
                @click="activeTab = 'Minuman'" 
                :class="activeTab === 'Minuman' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-5 py-1.5 rounded-full text-xs sm:text-sm font-semibold transition">
                Minuman
            </button>
            <button 
                type="button"
                @click="activeTab = 'Cemilan'" 
                :class="activeTab === 'Cemilan' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-5 py-1.5 rounded-full text-xs sm:text-sm font-semibold transition">
                Cemilan
            </button>
        </div>

        <!-- FORM PEMESANAN -->
        <form action="{{ route('orders.store') }}" method="POST" id="order-form">
            @csrf

            <!-- 1. Informasi Pemesan (Sesuai Screenshot) -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-8">
                <h2 class="text-base font-bold text-gray-800 pb-3 border-b border-gray-100 mb-4">1. Informasi Pemesan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                        <input 
                            type="text" 
                            name="customer_name" 
                            placeholder="Masukkan nama pemesan" 
                            value="{{ old('customer_name') }}"
                            required 
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nomor Meja</label>
                        <input 
                            type="text" 
                            name="table_number" 
                            placeholder="Contoh: 05" 
                            value="{{ old('table_number') }}"
                            required 
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder:text-gray-400">
                    </div>
                </div>
            </div>

            <!-- 2. Pilih Menu (Sesuai Screenshot) -->
            <div>
                <h2 class="text-base font-bold text-gray-800 mb-4">2. Pilih Menu</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($foods as $food)
                        <!-- Hidden input untuk harga agar bisa dihitung JS -->
                        <input type="hidden" id="food-price-{{ $food->id }}" value="{{ $food->price }}">

                        <!-- Card Menu Makanan (Sesuai Screenshot) -->
                        <div 
                            x-show="activeTab === 'Semua Menu' || activeTab === '{{ $food->category }}'"
                            x-transition
                            class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                            
                            <div>
                                <!-- Kotak Gambar -->
                                <div class="w-full h-44 bg-gray-100 flex items-center justify-center overflow-hidden">
                                    @if($food->image)
                                        <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-gray-400 font-medium text-sm">Tanpa Gambar</span>
                                    @endif
                                </div>

                                <!-- Konten Card -->
                                <div class="p-4 pb-2">
                                    <div class="flex items-center justify-between">
                                        <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-2.5 py-0.5 rounded-md">
                                            {{ $food->category }}
                                        </span>
                                        <span class="text-green-600 font-bold text-sm">
                                            Rp {{ number_format($food->price) }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-base mt-2.5">{{ $food->name }}</h3>
                                    <p class="text-gray-500 text-xs mt-1 leading-relaxed">{{ $food->description }}</p>
                                </div>
                            </div>

                            <!-- Stepper Jumlah Pesan -->
                            <div class="p-4 pt-2 border-t border-gray-100 mt-2 flex items-center justify-between bg-gray-50/50">
                                <span class="text-xs font-semibold text-gray-500">Jumlah:</span>
                                <div class="flex items-center space-x-2">
                                    <button 
                                        type="button" 
                                        @click="updateQty({{ $food->id }}, {{ $food->price }}, -1)"
                                        class="w-7 h-7 rounded-lg bg-white border border-gray-300 text-gray-700 font-bold flex items-center justify-center hover:bg-gray-100 active:scale-95 transition">
                                        -
                                    </button>
                                    <input 
                                        type="number" 
                                        name="items[{{ $food->id }}]" 
                                        :value="items[{{ $food->id }}] || 0" 
                                        readonly 
                                        class="w-10 text-center font-bold text-sm bg-transparent border-none p-0 focus:ring-0">
                                    <button 
                                        type="button" 
                                        @click="updateQty({{ $food->id }}, {{ $food->price }}, 1)"
                                        class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center hover:bg-blue-700 active:scale-95 transition">
                                        +
                                    </button>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400">
                            Belum ada menu makanan yang tersedia. Silakan login admin untuk menambahkan menu!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sticky Bottom Checkout Bar (Muncul Otomatis saat Pesanan > 0) -->
            <div 
                x-show="totalQty > 0"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-y-full opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-full opacity-0"
                class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-200 p-4 shadow-xl z-50">
                <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Total Pesanan (<span x-text="totalQty"></span> porsi):</div>
                        <div class="text-xl sm:text-2xl font-extrabold text-green-600">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalPrice)"></span>
                        </div>
                    </div>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition active:scale-95 flex items-center justify-center space-x-2">
                        <span>Kirim Pesanan Sekarang</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

        </form>

    </main>

</body>
</html>
