<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_user_management(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'role' => 1,
            'status' => 1,
        ]);

        $this->post('/login', [
            'Username' => 'admin@example.com',
            'Password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_create_a_staff_account(): void
    {
        $response = $this->withSession(['user_role' => 1])
            ->post(route('admin.users.store'), [
                'name' => 'Staff Member',
                'email' => 'staff@example.com',
                'phone' => '0900000000',
                'password' => 'secret123',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.com',
            'role' => 2,
            'status' => 1,
        ]);
        $this->assertTrue(Hash::check(
            'secret123',
            User::query()->where('email', 'staff@example.com')->firstOrFail()->password
        ));
    }

    public function test_staff_list_does_not_include_customer_accounts(): void
    {
        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => 'secret123',
            'role' => 2,
            'status' => 1,
        ]);
        User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => 'secret123',
            'role' => 0,
            'status' => 1,
        ]);

        $this->withSession(['user_role' => 1])
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('staff@example.com')
            ->assertDontSee('customer@example.com');
    }

    public function test_admin_can_toggle_staff_status_but_not_customer_status(): void
    {
        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => 'secret123',
            'role' => 2,
            'status' => 1,
        ]);
        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => 'secret123',
            'role' => 0,
            'status' => 1,
        ]);

        $this->withSession(['user_role' => 1])
            ->post(route('admin.users.toggle-status', $staff->id_user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id_user' => $staff->id_user,
            'status' => 0,
        ]);

        $this->withSession(['user_role' => 1])
            ->post(route('admin.users.toggle-status', $customer->id_user))
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id_user' => $customer->id_user,
            'status' => 1,
        ]);
    }
}
