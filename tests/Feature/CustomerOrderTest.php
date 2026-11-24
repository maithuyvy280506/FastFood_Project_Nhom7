<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_customer_is_redirected_to_login(): void
    {
        $this->get('/orders')
            ->assertRedirect(route('login'));
    }

    public function test_customer_can_list_only_their_orders_newest_first(): void
    {
        $customerId = $this->createCustomer('A');
        $otherId = $this->createCustomer('B');
        $this->createOrder($customerId, 'Đơn của A cũ', 0, '2026-11-20 10:00:00');
        $this->createOrder($customerId, 'Đơn của A mới', 1, '2026-11-21 10:00:00');
        $this->createOrder($otherId, 'Đơn của B bí mật', 0, '2026-11-22 10:00:00');

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
            'user_name' => 'A',
        ])->get(route('customer.orders.index'));

        $response->assertOk()
            ->assertSee('Đơn của A mới')
            ->assertDontSee('Đơn của B bí mật');

        $response->assertSeeInOrder(['Đơn của A mới', 'Đơn của A cũ']);
    }

    public function test_customer_cannot_open_another_customers_order(): void
    {
        $customerId = $this->createCustomer('A');
        $otherId = $this->createCustomer('B');
        $orderId = $this->createOrder($otherId, 'Đơn bí mật', 0);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->get(route('customer.orders.show', $orderId))
            ->assertNotFound();
    }

    public function test_customer_can_cancel_only_a_pending_order(): void
    {
        $customerId = $this->createCustomer('A');
        $pendingId = $this->createOrder($customerId, 'Đơn chờ', 0);
        $preparingId = $this->createOrder($customerId, 'Đơn chuẩn bị', 1);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('customer.orders.cancel', $pendingId))
            ->assertRedirect();

        $this->assertSame(4, (int) DB::table('orders')->where('id_order', $pendingId)->value('status'));

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('customer.orders.cancel', $preparingId))
            ->assertRedirect();

        $this->assertSame(1, (int) DB::table('orders')->where('id_order', $preparingId)->value('status'));
    }

    public function test_customer_cart_adds_active_product_and_returns_quantity(): void
    {
        $customerId = $this->createCustomer('A');
        $categoryId = DB::table('categories')->insertGetId(['name' => 'Gà', 'status' => 1]);
        $productId = DB::table('products')->insertGetId([
            'id_category' => $categoryId,
            'name' => 'Gà rán',
            'price' => 50000,
            'description' => 'Gà rán',
            'isFlashSale' => 0,
            'image' => '/ga.jpg',
            'is_active' => 1,
        ]);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->postJson(route('cart.add'), [
            'food_id' => $productId,
            'quantity' => 2,
            'variants' => ['Không cay'],
        ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'totalCount' => 2,
            ]);

        $this->assertDatabaseHas('carts', [
            'id_user' => $customerId,
            'id_product' => $productId,
            'quantity' => 2,
        ]);
    }

    private function createCustomer(string $name): int
    {
        return (int) DB::table('users')->insertGetId([
            'name' => $name,
            'email' => strtolower($name).'@example.com',
            'password' => 'secret123',
            'phone' => '0900000000',
            'address' => 'Địa chỉ',
            'role' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createOrder(int $customerId, string $name, int $status, ?string $createdAt = null): int
    {
        $timestamp = $createdAt ?? now()->toDateTimeString();

        return (int) DB::table('orders')->insertGetId([
            'id_user' => $customerId,
            'customer_name' => $name,
            'customer_phone' => '0900000000',
            'customer_address' => 'Địa chỉ',
            'discount_amount' => 0,
            'total_price' => 150000,
            'note' => null,
            'shipping_fee' => 30000,
            'status' => $status,
            'method' => 1,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }
}
