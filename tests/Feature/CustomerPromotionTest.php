<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CustomerPromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_customer_is_redirected_from_voucher_warehouse(): void
    {
        $this->get(route('promotions.index'))
            ->assertRedirect(route('login'));
    }

    public function test_voucher_warehouse_lists_all_codes_and_marks_expired_and_sold_out_vouchers(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $customerId = $this->createCustomer();
        $this->createPromotion('AVAILABLE');
        $this->createPromotion('EXPIRED', ['end_date' => now()->subMinute()]);
        $this->createPromotion('SOLDOUT', ['quantity' => 1, 'used' => 1]);
        $this->createPromotion('INACTIVE', ['status' => 0]);

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->get(route('promotions.index'));

        $response->assertOk()
            ->assertSee('AVAILABLE')
            ->assertSee('EXPIRED')
            ->assertSee('SOLDOUT')
            ->assertSee('INACTIVE')
            ->assertSee('Đã hết')
            ->assertSee('class="voucher-card expired"', false);
    }

    public function test_customer_can_save_a_voucher_and_continue_to_checkout(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $customerId = $this->createCustomer();
        $promotionId = $this->createPromotion('SAVE10', [
            'min_order_value' => 50000,
        ]);
        $this->createCart($customerId, 100000);

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('promotions.save', $promotionId));

        $response->assertRedirect(route('cart.checkout', ['promotion_id' => $promotionId]));
        $this->assertSame(
            ['SAVE10'],
            json_decode((string) DB::table('users')->where('id_user', $customerId)->value('saved_vouchers'), true),
        );
    }

    public function test_customer_can_claim_each_campaign_once_but_other_campaigns_and_accounts_are_independent(): void
    {
        $customerId = $this->createCustomer();
        $otherCustomerId = $this->createCustomer();
        $firstPromotionId = $this->createPromotion('CAMPAIGN10', ['type' => 1]);
        $secondPromotionId = $this->createPromotion('CAMPAIGN20', ['type' => 1]);
        $session = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ]);

        $session->post(route('promotions.save', $firstPromotionId))
            ->assertRedirect('/foods');
        $session->post(route('promotions.save', $firstPromotionId))
            ->assertRedirect('/foods');
        $session->post(route('promotions.save', $secondPromotionId))
            ->assertRedirect('/foods');

        $this->withSession([
            'user_id' => $otherCustomerId,
            'user_role' => 0,
        ])->post(route('promotions.save', $firstPromotionId))
            ->assertRedirect('/foods');

        $this->assertSame(
            ['CAMPAIGN10', 'CAMPAIGN20'],
            json_decode((string) DB::table('users')->where('id_user', $customerId)->value('saved_vouchers'), true),
        );
        $this->assertSame(
            ['CAMPAIGN10'],
            json_decode((string) DB::table('users')->where('id_user', $otherCustomerId)->value('saved_vouchers'), true),
        );
    }

    public function test_customer_cannot_save_an_expired_voucher(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $customerId = $this->createCustomer();
        $promotionId = $this->createPromotion('EXPIRED', ['end_date' => now()->subMinute()]);

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->from(route('promotions.index'))->post(route('promotions.save', $promotionId));

        $response->assertRedirect(route('promotions.index'))
            ->assertSessionHas('error');
        $this->assertSame(
            [],
            json_decode((string) DB::table('users')->where('id_user', $customerId)->value('saved_vouchers'), true),
        );
    }

    public function test_customer_can_apply_a_saved_voucher_to_an_order(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $customerId = $this->createCustomer(['SAVE10']);
        $promotionId = $this->createPromotion('SAVE10', [
            'type' => 1,
            'value' => 10,
            'min_order_value' => 50000,
        ]);
        $productId = $this->createCart($customerId, 100000);

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('cart.place-order'), [
            'customer_name' => 'Khách hàng',
            'customer_phone' => '0900000000',
            'customer_address' => 'Địa chỉ giao hàng',
            'method' => 1,
            'promotion_id' => $promotionId,
        ]);

        $orderId = (int) DB::table('orders')->where('id_user', $customerId)->value('id_order');
        $response->assertRedirect(route('customer.orders.show', $orderId));
        $this->assertDatabaseHas('orders', [
            'id_order' => $orderId,
            'id_promotion' => $promotionId,
            'discount_amount' => 10000,
            'total_price' => 120000,
        ]);
        $this->assertDatabaseHas('promotions', [
            'id_promotion' => $promotionId,
            'used' => 1,
        ]);
        $this->assertDatabaseMissing('carts', [
            'id_user' => $customerId,
            'id_product' => $productId,
        ]);
    }

    public function test_promotion_can_be_used_once_per_customer_and_is_removed_after_use(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $customerId = $this->createCustomer(['ONCE10']);
        $otherCustomerId = $this->createCustomer();
        $promotionId = $this->createPromotion('ONCE10', [
            'type' => 1,
            'value' => 10,
            'quantity' => 10,
        ]);
        $this->createCart($customerId, 100000);

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('cart.place-order'), [
            'customer_name' => 'Khách hàng',
            'customer_phone' => '0900000000',
            'customer_address' => 'Địa chỉ giao hàng',
            'method' => 1,
            'promotion_id' => $promotionId,
        ]);

        $orderId = (int) DB::table('orders')->where('id_user', $customerId)->value('id_order');
        $response->assertRedirect(route('customer.orders.show', $orderId));
        $this->assertSame(
            [],
            json_decode((string) DB::table('users')->where('id_user', $customerId)->value('saved_vouchers'), true),
        );
        $this->assertDatabaseHas('promotions', [
            'id_promotion' => $promotionId,
            'quantity' => 10,
            'used' => 1,
        ]);

        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->get(route('promotions.index'))
            ->assertOk()
            ->assertSee('Đã sử dụng');

        $this->from(route('promotions.index'))
            ->withSession([
                'user_id' => $customerId,
                'user_role' => 0,
            ])->post(route('promotions.save', $promotionId))
            ->assertRedirect(route('promotions.index'))
            ->assertSessionHas('error', 'Bạn đã sử dụng voucher này trước đó');

        $this->createCart($customerId, 100000);
        $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('cart.place-order'), [
            'customer_name' => 'Khách hàng',
            'customer_phone' => '0900000000',
            'customer_address' => 'Địa chỉ giao hàng',
            'method' => 1,
            'promotion_id' => $promotionId,
        ])->assertSessionHasErrors('promotion_id');

        $this->withSession([
            'user_id' => $otherCustomerId,
            'user_role' => 0,
        ])->post(route('promotions.save', $promotionId))
            ->assertRedirect('/foods');

        $this->assertSame(1, DB::table('orders')->where('id_user', $customerId)->count());
        $this->assertSame(
            ['ONCE10'],
            json_decode((string) DB::table('users')->where('id_user', $otherCustomerId)->value('saved_vouchers'), true),
        );

        $this->createCart($otherCustomerId, 100000);
        $otherOrderResponse = $this->withSession([
            'user_id' => $otherCustomerId,
            'user_role' => 0,
        ])->post(route('cart.place-order'), [
            'customer_name' => 'Khách hàng khác',
            'customer_phone' => '0900000001',
            'customer_address' => 'Địa chỉ khác',
            'method' => 1,
            'promotion_id' => $promotionId,
        ]);
        $otherOrderId = (int) DB::table('orders')->where('id_user', $otherCustomerId)->value('id_order');

        $otherOrderResponse->assertRedirect(route('customer.orders.show', $otherOrderId));
        $this->assertDatabaseHas('promotions', [
            'id_promotion' => $promotionId,
            'quantity' => 10,
            'used' => 2,
        ]);
    }

    public function test_customer_cannot_apply_a_voucher_they_have_not_saved(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $customerId = $this->createCustomer();
        $promotionId = $this->createPromotion('NOTSAVED');
        $this->createCart($customerId, 100000);

        $response = $this->withSession([
            'user_id' => $customerId,
            'user_role' => 0,
        ])->post(route('cart.place-order'), [
            'customer_name' => 'Khách hàng',
            'customer_phone' => '0900000000',
            'customer_address' => 'Địa chỉ giao hàng',
            'method' => 1,
            'promotion_id' => $promotionId,
        ]);

        $response->assertSessionHasErrors('promotion_id');
        $this->assertDatabaseMissing('orders', ['id_user' => $customerId]);
        $this->assertDatabaseHas('promotions', [
            'id_promotion' => $promotionId,
            'used' => 0,
        ]);
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

    private function createCart(int $customerId, int $price): int
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Danh mục',
            'status' => 1,
        ]);
        $productId = (int) DB::table('products')->insertGetId([
            'id_category' => $categoryId,
            'name' => 'Món ăn',
            'price' => $price,
            'description' => 'Món ăn kiểm thử',
            'isFlashSale' => 0,
            'image' => '/food.jpg',
            'is_active' => 1,
        ]);
        DB::table('carts')->insert([
            'id_user' => $customerId,
            'id_product' => $productId,
            'quantity' => 1,
        ]);

        return $productId;
    }
}
