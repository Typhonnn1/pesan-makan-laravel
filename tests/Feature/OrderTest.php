<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_menu(): void
    {
        Food::create([
            'name' => 'Nasi Goreng Spesial',
            'category' => 'Makanan',
            'price' => 25000,
            'description' => 'Enak',
        ]);

        $response = $this->get(route('customer.index'));
        $response->assertStatus(200);
        $response->assertSee('Menu Restoran');
        $response->assertSee('Nasi Goreng Spesial');
    }

    public function test_customer_can_checkout_order_successfully(): void
    {
        $food1 = Food::create([
            'name' => 'Mie Goreng Seafood',
            'category' => 'Makanan',
            'price' => 28000,
            'description' => 'Pedas',
        ]);

        $food2 = Food::create([
            'name' => 'Es Teh Manis',
            'category' => 'Minuman',
            'price' => 5000,
            'description' => 'Manis',
        ]);

        $response = $this->post(route('customer.checkout'), [
            'customer_name' => 'Budi Santoso',
            'table_number'  => 5,
            'items'         => [
                $food1->id => 2, // 2 * 28000 = 56000
                $food2->id => 1, // 1 * 5000 = 5000
            ],
        ]);

        $response->assertRedirect(route('customer.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi Santoso',
            'table_number'  => '5',
            'total_price'   => 61000,
            'status'        => 'pending',
        ]);

        $order = Order::first();

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'food_id'  => $food1->id,
            'quantity' => 2,
            'subtotal' => 56000,
        ]);

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'food_id'  => $food2->id,
            'quantity' => 1,
            'subtotal' => 5000,
        ]);
    }

    public function test_customer_cannot_checkout_with_zero_items(): void
    {
        $food = Food::create([
            'name' => 'Jus Alpukat',
            'category' => 'Minuman',
            'price' => 15000,
            'description' => 'Segar',
        ]);

        $response = $this->post(route('customer.checkout'), [
            'customer_name' => 'Budi Santoso',
            'table_number'  => 3,
            'items'         => [
                $food->id => 0,
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_can_view_dashboard_orders(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'customer_name' => 'Siti',
            'table_number'  => '2',
            'total_price'   => 25000,
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Pesanan Masuk');
    }

    public function test_admin_can_update_order_status(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'customer_name' => 'Rian',
            'table_number'  => '4',
            'total_price'   => 50000,
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($user)->patch(route('admin.orders.updateStatus', $order->id), [
            'status' => 'Diproses',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => 'Diproses',
        ]);
    }
}
