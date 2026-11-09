<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Bảng users 
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->tinyInteger('role')->default(0); // 0: Khách hàng, 1: Admin
            $table->tinyInteger('status')->default(1); // 1: Active, 0: Locked/Inactive
            $table->json('saved_vouchers')->nullable(); // Lưu mã voucher khách đã lưu
            $table->timestamps();
        });

        // 2. Bảng categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id('id_category');
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            // $table->timestamps();
        });

        // 3. Bảng products
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->unsignedBigInteger('id_category');
            $table->string('name');
            $table->integer('price');
            $table->text('description')->nullable();
            $table->boolean('isFlashSale')->default(false);
            $table->dateTime('flash_sale_end_time')->nullable();
            $table->tinyInteger('flash_sale_type')->nullable(); // 1: %, 2: VNĐ
            $table->integer('flash_sale_value')->nullable();
            $table->text('image');
            $table->tinyInteger('is_active')->default(1);
            $table->json('variants')->nullable();
            // $table->timestamps();

            $table->foreign('id_category')->references('id_category')->on('categories');
        });

        // 4. Bảng promotions
        Schema::create('promotions', function (Blueprint $table) {
            $table->id('id_promotion');
            $table->string('code')->unique();
            $table->string('name');
            $table->tinyInteger('type'); // 1: %, 2: VNĐ, 3: Freeship
            $table->integer('value');
            $table->integer('min_order_value')->default(0);
            $table->integer('quantity');
            $table->integer('used')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // 5. Bảng carts
        Schema::create('carts', function (Blueprint $table) {
            $table->id('id_cart');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_product');
            $table->integer('quantity');
            $table->text('note')->nullable();
            $table->json('variants_selected')->nullable();
            // $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_product')->references('id_product')->on('products');
        });

        // 6. Bảng orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->unsignedBigInteger('id_user');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->unsignedBigInteger('id_promotion')->nullable();
            $table->integer('discount_amount')->default(0);
            $table->integer('total_price');
            $table->text('note')->nullable();
            $table->integer('shipping_fee')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('method');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_promotion')->references('id_promotion')->on('promotions')->onDelete('set null');
        });

        // 7. Bảng order_details
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('id_order_detail');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_product');
            $table->integer('quantity');
            $table->json('variants_selected')->nullable();
            $table->integer('price');
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('orders');
            $table->foreign('id_product')->references('id_product')->on('products');
        });

        // 8. Bảng banners
        Schema::create('banners', function (Blueprint $table) {
            $table->id('id_banner');
            $table->text('image');
            $table->tinyInteger('status')->default(1);
            // $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
        Schema::dropIfExists('banners');
    }
};