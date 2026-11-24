<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CustomerCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_adds_product_with_quantity_variants_and_note(): void
    {
        $customerId = $this->createCustomer();
        $productId = $this->createProduct();

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->postJson(route('cart.add'), [
            'food_id' => $productId,
            'quantity' => 2,
            'variants_selected' => ['Thêm phô mai', 'Ít cay'],
            'note' => 'Không lấy hành',
        ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'totalCount' => 2,
            ]);

        $cart = DB::table('carts')->where('id_user', $customerId)->first();

        $this->assertSame($productId, (int) $cart->id_product);
        $this->assertSame(2, (int) $cart->quantity);
        $this->assertSame(['Thêm phô mai', 'Ít cay'], json_decode($cart->variants_selected, true));
        $this->assertSame('Không lấy hành', $cart->note);
    }

    public function test_customer_can_apply_saved_promotion_and_update_cart_quantity_with_ajax(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $customerId = $this->createCustomer(['CART10']);
        $promotionId = $this->createPromotion('CART10', [
            'type' => 1,
            'value' => 10,
        ]);
        $cartId = $this->createCart($customerId, 50000);

        $session = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ]);

        $session->postJson(route('cart.promotion.apply'), ['promotion_id' => $promotionId])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'discountAmount' => 5000,
                'total' => 75000,
                'selectedPromotionCode' => 'CART10',
            ]);

        $session->patchJson(route('cart.update', $cartId), ['quantity' => 2])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'quantity' => 2,
                'itemSubtotal' => 100000,
                'subtotal' => 100000,
                'discountAmount' => 10000,
                'total' => 120000,
                'cartCount' => 2,
            ]);

        $this->assertDatabaseHas('carts', [
            'id_cart' => $cartId,
            'quantity' => 2,
        ]);
        $this->assertSame($promotionId, (int) session('cart_promotion_id'));
    }

    public function test_customer_can_remove_cart_item_with_ajax(): void
    {
        $customerId = $this->createCustomer();
        $cartId = $this->createCart($customerId, 50000);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->deleteJson(route('cart.destroy', $cartId))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'isEmpty' => true,
                'cartCount' => 0,
                'subtotal' => 0,
            ]);

        $this->assertDatabaseMissing('carts', ['id_cart' => $cartId]);
    }

    public function test_customer_cannot_apply_a_voucher_they_have_not_saved(): void
    {
        $customerId = $this->createCustomer();
        $promotionId = $this->createPromotion('UNSAVED');
        $this->createCart($customerId, 50000);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->postJson(route('cart.promotion.apply'), ['promotion_id' => $promotionId])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Voucher chưa được lưu vào Kho Voucher');

        $this->assertNull(session('cart_promotion_id'));
    }

    public function test_customer_cannot_apply_saved_voucher_below_its_minimum_order(): void
    {
        $customerId = $this->createCustomer(['MIN100']);
        $promotionId = $this->createPromotion('MIN100', ['min_order_value' => 100000]);
        $this->createCart($customerId, 50000);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->postJson(route('cart.promotion.apply'), ['promotion_id' => $promotionId])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Đơn hàng chưa đạt giá trị tối thiểu của voucher');

        $this->assertNull(session('cart_promotion_id'));
    }

    public function test_customer_cart_displays_saved_voucher_picker(): void
    {
        $customerId = $this->createCustomer(['CART10']);
        $this->createPromotion('CART10');
        $this->createCart($customerId, 50000);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Chọn mã khuyến mãi')
            ->assertSee('CART10')
            ->assertSee('promotionModal');
    }

    public function test_checkout_prefills_delivery_details_from_customer_profile(): void
    {
        $customerId = $this->createCustomer();
        DB::table('users')->where('id_user', $customerId)->update([
            'name' => 'Tên trên hồ sơ',
            'phone' => '0912345678',
            'address' => 'Địa chỉ trên hồ sơ',
        ]);
        $this->createCart($customerId, 50000);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
            'user_name' => 'Tên cũ trong session',
            'user_phone' => '0900000000',
            'user_address' => 'Địa chỉ cũ trong session',
        ])->get(route('cart.checkout'))
            ->assertOk()
            ->assertSee('value="Tên trên hồ sơ"', false)
            ->assertSee('value="0912345678"', false)
            ->assertSee('Địa chỉ trên hồ sơ');
    }

    public function test_customer_cannot_update_another_customers_cart_item(): void
    {
        $customerId = $this->createCustomer();
        $otherCustomerId = $this->createCustomer();
        $cartId = $this->createCart($otherCustomerId, 50000);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->patchJson(route('cart.update', $cartId), ['quantity' => 2])
            ->assertNotFound();

        $this->assertDatabaseHas('carts', [
            'id_cart' => $cartId,
            'quantity' => 1,
        ]);
    }

    public function test_customer_order_keeps_cart_variants_and_item_note(): void
    {
        $customerId = $this->createCustomer();
        $cartId = $this->createCart($customerId, 50000);
        DB::table('carts')->where('id_cart', $cartId)->update([
            'variants_selected' => json_encode(['Thêm phô mai']),
            'note' => 'Không lấy hành',
        ]);
        $productId = (int) DB::table('carts')->where('id_cart', $cartId)->value('id_product');

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('cart.place-order'), [
            'customer_name' => 'Khách hàng',
            'customer_phone' => '0900000000',
            'customer_address' => 'Địa chỉ đã xác nhận',
            'method' => 1,
        ]);

        $orderId = (int) DB::table('orders')->where('id_user', $customerId)->value('id_order');
        $response->assertRedirect(route('customer.orders.show', $orderId));

        DB::table('users')->where('id_user', $customerId)->update([
            'address' => 'Địa chỉ hồ sơ mới',
        ]);
        DB::table('products')->where('id_product', $productId)->update(['price' => 90000]);

        $this->assertDatabaseHas('orders', [
            'id_order' => $orderId,
            'customer_address' => 'Địa chỉ đã xác nhận',
            'total_price' => 80000,
        ]);

        $orderDetail = DB::table('order_details')->where('id_order', $orderId)->first();
        $this->assertSame(50000, (int) $orderDetail->price);
        $this->assertSame(
            ['Thêm phô mai', 'Ghi chú: Không lấy hành'],
            json_decode($orderDetail->variants_selected, true),
        );
    }

    /**
     * @param  list<string>  $savedVoucherCodes
     */
    private function createCustomer(array $savedVoucherCodes = []): int
    {
        static $sequence = 0;
        $sequence++;

        return (int) DB::table('users')->insertGetId([
            'name' => 'Customer '.$sequence,
            'email' => 'customer'.$sequence.'@example.com',
            'password' => 'secret123',
            'role' => 0,
            'status' => 1,
            'saved_vouchers' => json_encode($savedVoucherCodes),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createProduct(int $price = 50000): int
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Danh mục',
            'status' => 1,
        ]);

        return (int) DB::table('products')->insertGetId([
            'id_category' => $categoryId,
            'name' => 'Món ăn',
            'price' => $price,
            'description' => 'Món ăn kiểm thử',
            'isFlashSale' => 0,
            'image' => '/food.jpg',
            'is_active' => 1,
        ]);
    }

    private function createCart(int $customerId, int $price): int
    {
        $productId = $this->createProduct($price);

        return (int) DB::table('carts')->insertGetId([
            'id_user' => $customerId,
            'id_product' => $productId,
            'quantity' => 1,
            'variants_selected' => json_encode([]),
        ]);
    }

    private function createPromotion(string $code, array $attributes = []): int
    {
        return (int) DB::table('promotions')->insertGetId(array_merge([
            'code' => $code,
            'name' => 'Voucher '.$code,
            'type' => 2,
            'value' => 10000,
            'min_order_value' => 0,
            'quantity' => 10,
            'used' => 0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }
}
