<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class FastFoodDataSeeder extends Seeder
{
    public function run()
    {
        // ---------------------------------------------------------
        // 1. ĐỔ DỮ LIỆU TÀI KHOẢN (Bảng users có timestamps)
        // ---------------------------------------------------------
        $staffData = json_decode(file_get_contents(storage_path('app/json_data/users.json')), true);
        foreach ($staffData as $user) {
            DB::table('users')->insert([
                'name' => $user['fullName'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
                'phone' => $user['sdt'] ?? null,
                'address' => null,
                'role' => 1,
                'status' => ($user['status'] == 'Active') ? 1 : 0,
                'created_at' => Carbon::parse($user['createdAt']),
                'updated_at' => Carbon::parse($user['createdAt']),
            ]);
        }

        $customerData = json_decode(file_get_contents(storage_path('app/json_data/customers.json')), true);
        foreach ($customerData as $customer) {
            DB::table('users')->insert([
                'name' => $customer['fullName'],
                'email' => $customer['email'],
                'password' => Hash::make($customer['password']),
                'phone' => $customer['phone'] ?? null,
                'address' => $customer['address'] ?? null,
                'role' => 0,
                'status' => ($customer['status'] == 'Active') ? 1 : 0,
                'saved_vouchers' => isset($customer['SavedVouchers']) ? json_encode($customer['SavedVouchers']) : null,
                'created_at' => Carbon::parse($customer['createdAt']),
                'updated_at' => Carbon::parse($customer['createdAt']),
            ]);
        }

        // ---------------------------------------------------------
        // 2. ĐỔ DỮ LIỆU DANH MỤC & SẢN PHẨM (Không có timestamps)
        // ---------------------------------------------------------
        $foodsData = json_decode(file_get_contents(storage_path('app/json_data/foods.json')), true);

        $categories = collect($foodsData)->pluck('category')->unique()->values();
        $categoryMap = [];
        foreach ($categories as $catName) {
            $id = DB::table('categories')->insertGetId([
                'name' => $catName,
                'status' => 1,
            ]);
            $categoryMap[$catName] = $id;
        }

        foreach ($foodsData as $food) {
            DB::table('products')->insert([
                'id_category' => $categoryMap[$food['category']],
                'name' => $food['name'],
                'price' => $food['basePrice'],
                'description' => $food['description'] ?? null,
                'isFlashSale' => $food['isFlashSale'] ?? false,
                'flash_sale_end_time' => null,
                'flash_sale_type' => null,
                'flash_sale_value' => null,
                'image' => $food['image'],
                'is_active' => ($food['status'] === 'Available') ? 1 : 0,
                'variants' => isset($food['variants']) ? json_encode($food['variants'], JSON_UNESCAPED_UNICODE) : null,
            ]);
        }

        // ---------------------------------------------------------
        // 3. ĐỔ DỮ LIỆU KHUYẾN MÃI (Bảng promotions có timestamps)
        // ---------------------------------------------------------
        $promosData = json_decode(file_get_contents(storage_path('app/json_data/promotions.json')), true);
        foreach ($promosData as $promo) {
            $type = 1;
            $val = 0;
            if ($promo['discount']['type'] == 'Percent') {
                $type = 1;
                $val = is_array($promo['discount']['value']) ? $promo['discount']['value']['$numberDecimal'] : $promo['discount']['value'];
            } elseif ($promo['discount']['type'] == 'Fixed') {
                $type = 2;
                $val = is_array($promo['discount']['value']) ? $promo['discount']['value']['$numberDecimal'] : $promo['discount']['value'];
            } elseif ($promo['discount']['type'] == 'Shipping') {
                $type = 3;
                $val = is_array($promo['discount']['value']) ? $promo['discount']['value']['$numberDecimal'] : $promo['discount']['value'];
            }

            $minOrder = 0;
            if (isset($promo['conditions']['minOrderValue'])) {
                $minOrder = is_array($promo['conditions']['minOrderValue']) ? $promo['conditions']['minOrderValue']['$numberDecimal'] : $promo['conditions']['minOrderValue'];
            }

            DB::table('promotions')->insert([
                'code' => $promo['promotionId'],
                'name' => $promo['name'],
                'type' => $type,
                'value' => (int)$val,
                'min_order_value' => (int)$minOrder,
                'quantity' => $promo['quantity'],
                'used' => 0,
                'start_date' => Carbon::parse($promo['validityPeriod']['start']['$date']),
                'end_date' => Carbon::parse($promo['validityPeriod']['end']['$date']),
                'status' => ($promo['status'] == 'Active') ? 1 : 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // ---------------------------------------------------------
        // 4. ĐỔ DỮ LIỆU BANNERS
        // ---------------------------------------------------------
        $bannersData = json_decode(file_get_contents(storage_path('app/json_data/banners.json')), true);
        foreach ($bannersData as $banner) {
            DB::table('banners')->insert([
                'image' => $banner['bannerUrl'],
                'status' => 1,
            ]);
        }

        // ---------------------------------------------------------
        // 5. ĐỔ DỮ LIỆU ORDERS VÀ ORDER_DETAILS (Từ orders.json)
        // ---------------------------------------------------------
        $ordersData = json_decode(file_get_contents(storage_path('app/json_data/orders.json')), true);
        foreach ($ordersData as $order) {
            $userId = DB::table('users')->where('role', 0)->value('id_user') ?? 1;

            // Xử lý giá trị totalAmount (có thể là số hoặc object $numberDecimal)
            $totalAmount = is_array($order['totalAmount']) ? ($order['totalAmount']['$numberDecimal'] ?? 0) : $order['totalAmount'];
            $discountAmount = is_array($order['discountAmount']) ? ($order['discountAmount']['$numberDecimal'] ?? 0) : $order['discountAmount'];
            $shippingFee = is_array($order['deliveryFee']) ? ($order['deliveryFee']['$numberDecimal'] ?? 0) : $order['deliveryFee'];

            // Trạng thái đơn hàng (ví dụ: 'Đã giao' -> 1, 'Đã hủy' -> 2, mặc định chờ xác nhận -> 0)
            $status = 0;
            if ($order['status'] === 'Đã giao' || $order['status'] === 'Hoàn thành') $status = 1;
            elseif ($order['status'] === 'Đã hủy') $status = 2;

            // Phương thức thanh toán (ví dụ: COD -> 1, Chuyển khoản -> 2)
            $method = 1;
            if (isset($order['paymentMethod']) && $order['paymentMethod'] === 'Chuyển khoản') $method = 2;

            // Insert vào bảng orders
            $orderId = DB::table('orders')->insertGetId([
                'id_user' => $userId,
                'customer_name' => $order['receiverName'] ?? 'Khách hàng',
                'customer_phone' => $order['receiverPhone'] ?? '0123456789',
                'customer_address' => is_array($order['deliveryAddress']) ? ($order['deliveryAddress']['address'] ?? 'TP.HCM') : 'TP.HCM',
                'id_promotion' => null,
                'discount_amount' => (int)$discountAmount,
                'total_price' => (int)$totalAmount,
                'note' => $order['note'] ?? null,
                'shipping_fee' => (int)$shippingFee,
                'status' => $status,
                'method' => $method,
            ]);

            // Duyệt mảng items trong đơn hàng để đưa vào order_details
            if (isset($order['items']) && is_array($order['items'])) {
                foreach ($order['items'] as $item) {
                    // Lấy id_product dựa theo tên sản phẩm hoặc mặc định 1
                    $productId = DB::table('products')->where('name', 'like', '%' . ($item['foodName'] ?? '') . '%')->value('id_product');
                    if (!$productId) {
                        $productId = 1; // Fallback nếu không khớp tên
                    }

                    $unitPrice = is_array($item['unitPrice']) ? ($item['unitPrice']['$numberDecimal'] ?? 0) : $item['unitPrice'];

                    DB::table('order_details')->insert([
                        'id_order' => $orderId,
                        'id_product' => $productId,
                        'quantity' => $item['quantity'],
                        'variants_selected' => isset($item['selectedVariants']) ? json_encode($item['selectedVariants'], JSON_UNESCAPED_UNICODE) : null,
                        'price' => (int)$unitPrice,
                    ]);
                }
            }
        }

        $this->command->info('Đã seed thành công toàn bộ dữ liệu');
    }
}