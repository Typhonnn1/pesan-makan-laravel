<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar katalog menu makanan untuk sisi customer (pelanggan).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data makanan dari database
        $foods = Food::all();

        // Mengirim data makanan ke view customer/index.blade.php
        return view('customer.index', compact('foods'));
    }

    /**
     * Menyimpan dan memproses transaksi checkout pesanan baru dari customer (multi-tabel DB::transaction).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input data dari form pesanan
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number'  => 'required|integer|min:1',
            'items'         => 'required|array',
            'items.*'       => 'nullable|integer|min:0',
        ]);

        // Menyaring hanya item menu yang kuantitas pesanannya lebih dari 0
        $orderedItems = array_filter($request->items, fn ($qty) => $qty > 0);

        // Jika tidak ada item yang dipilih, kembalikan dengan pesan error
        if (empty($orderedItems)) {
            return back()->with('error', 'Pilih minimal satu menu makanan!');
        }

        // Memulai transaksi multi-tabel untuk menjaga konsistensi database
        DB::beginTransaction();
        try {
            // Membuat data transaksi utama di tabel orders
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'table_number'  => $request->table_number,
                'total_price'   => 0,
                'status'        => 'pending',
            ]);

            $totalPrice = 0;

            // Melakukan looping untuk setiap item yang dipesan dan menyimpannya ke tabel order_details
            foreach ($orderedItems as $foodId => $quantity) {
                $food = Food::findOrFail($foodId);
                $subtotal = $food->price * $quantity;
                $totalPrice += $subtotal;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'food_id'  => $food->id,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal, // Disimpan ke kolom subtotal
                ]);
            }

            // Memperbarui total harga keseluruhan pesanan di tabel orders
            $order->update(['total_price' => $totalPrice]);

            // Melakukan commit transaksi jika semua proses berhasil
            DB::commit();

            return redirect()->route('customer.index')->with('success', 'Pesanan berhasil dibuat! Nomor Meja: ' . $order->table_number);
        } catch (\Exception $e) {
            // Rollback semua perubahan database jika terjadi kesalahan
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan rekap seluruh pesanan yang masuk pada dashboard sisi Admin.
     *
     * @return \Illuminate\View\View
     */
    public function adminDashboard()
    {
        // Mengambil seluruh pesanan beserta detail item dan makanannya
        $orders = Order::with('orderDetails.food')->latest()->get();

        // Mengirim data pesanan ke halaman dashboard admin
        return view('dashboard', compact('orders'));
    }

    /**
     * Memperbarui status pesanan (Pending, Diproses, Selesai) oleh Admin.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID dari pesanan (Order)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi status yang dikirimkan
        $request->validate(['status' => 'required|string']);

        // Mencari pesanan berdasarkan ID dan memperbarui statusnya
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui!');
    }
}
