<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminStaticPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_the_reference_summary_layout(): void
    {
        $this->withSession(['user_role' => 1])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('TỔNG QUAN QUẢN LÝ')
            ->assertSee('ĐƠN HÀNG TUẦN NÀY')
            ->assertSee('CHỜ XÁC NHẬN');
    }

    public function test_admin_dashboard_stats_filter_returns_real_totals(): void
    {
        DB::table('users')->insert([
            'id_user' => 100,
            'name' => 'Dashboard Customer',
            'email' => 'dashboard@example.com',
            'password' => 'secret123',
            'role' => 0,
            'status' => 1,
            'created_at' => '2026-11-20 10:00:00',
            'updated_at' => '2026-11-20 10:00:00',
        ]);

        foreach ([0, 3, 4] as $status) {
            DB::table('orders')->insert([
                'id_user' => 100,
                'customer_name' => 'Dashboard Customer',
                'customer_phone' => '0900000000',
                'customer_address' => 'Dashboard Address',
                'discount_amount' => 0,
                'total_price' => 100000,
                'shipping_fee' => 30000,
                'status' => $status,
                'method' => 1,
                'created_at' => '2026-11-20 10:00:00',
                'updated_at' => '2026-11-20 10:00:00',
            ]);
        }

        $this->withSession(['user_role' => 1])
            ->getJson(route('admin.dashboard.stats', [
                'from' => '2026-11-20',
                'to' => '2026-11-20',
            ]))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'stats' => [
                    'revenue' => 100000,
                    'pending' => 1,
                    'total' => 3,
                    'delivered' => 1,
                    'cancelled' => 1,
                ],
            ]);
    }

    public function test_admin_can_open_the_order_list_and_order_detail_mockups(): void
    {
        DB::table('users')->insert([
            'id_user' => 66,
            'name' => 'Mai Thúy Vy',
            'email' => 'mai66@example.com',
            'password' => 'secret123',
            'role' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('orders')->insert([
            'id_order' => 66,
            'id_user' => 66,
            'customer_name' => 'Mai Thúy Vy',
            'customer_phone' => '0395943126',
            'customer_address' => '140 Lê Trọng Tấn',
            'discount_amount' => 0,
            'total_price' => 219000,
            'shipping_fee' => 15000,
            'status' => 1,
            'method' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['user_role' => 1])
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('DANH SÁCH ĐƠN HÀNG')
            ->assertSee('DH066')
            ->assertSee('Đang chuẩn bị');

        $this->withSession(['user_role' => 1])
            ->get(route('admin.orders.show', 66))
            ->assertOk()
            ->assertSee('DH066')
            ->assertSee('CHI TIẾT ĐƠN HÀNG:')
            ->assertSee('DANH SÁCH MÓN ĂN CẦN LÀM');
    }

    public function test_customer_and_employee_pages_render_inside_the_admin_layout(): void
    {
        User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => 'secret123',
            'role' => 0,
            'status' => 1,
        ]);
        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => 'secret123',
            'role' => 2,
            'status' => 1,
        ]);

        $this->withSession(['user_role' => 1])
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('DANH SÁCH KHÁCH HÀNG')
            ->assertSee('customer@example.com');

        $this->withSession(['user_role' => 1])
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('DANH SÁCH NHÂN VIÊN')
            ->assertSee('staff@example.com')
            ->assertSee('Quản lý đơn hàng');

        $this->withSession(['user_role' => 1])
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('THÊM NHÂN VIÊN MỚI');

        $this->withSession(['user_role' => 1])
            ->get(route('admin.users.edit', 2))
            ->assertOk()
            ->assertSee('CẬP NHẬT THÔNG TIN NHÂN VIÊN');
    }
}
