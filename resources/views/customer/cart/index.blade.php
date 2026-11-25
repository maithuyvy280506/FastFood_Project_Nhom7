
@extends('layouts.app')
@section('content')

@php
    $title = 'Giỏ hàng';
    $subtotal = $subtotal ?? $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
    $shippingFee = $shippingFee ?? 0;
    $discountAmount = $discountAmount ?? 0;
    $availablePromotions = $availablePromotions ?? [];
    $selectedPromotion = $selectedPromotion ?? null;
    $promotionError = $promotionError ?? null;
    $total = max(0, $subtotal + $shippingFee - $discountAmount);

    // Chuyển dữ liệu tùy chọn thành chuỗi hiển thị
    $formatOptions = function ($value) use (&$formatOptions) {
        if ($value === null || $value === '') {
            return '';
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $formatOptions($decoded);
            }
            return trim($value);
        }
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }
        if (is_object($value)) {
            $value = method_exists($value, 'toArray')
                ? $value->toArray()
                : (array) $value;
        }
        if (is_array($value)) {
            foreach (['name', 'label', 'option_name', 'topping_name', 'value_name', 'option_value'] as $key) {
                if (isset($value[$key]) && is_string($value[$key])) {
                    return trim($value[$key]);
                }
            }
            $results = [];
            foreach ($value as $key => $option) {
                if (in_array($key, [
                    'id', 'id_option', 'id_topping', 'id_product',
                    'id_cart', 'price', 'extra_price', 'quantity',
                    'created_at', 'updated_at', 'pivot'
                ], true)) {
                    continue;
                }
                $text = $formatOptions($option);
                if ($text !== '') {
                    $results[] = $text;
                }
            }
            return implode(', ', array_unique($results));
        }
        return is_numeric($value) ? (string) $value : '';
    };

    // Lấy nội dung đầu tiên có dữ liệu
    $firstText = function (...$values) use ($formatOptions) {
        foreach ($values as $value) {
            $text = $formatOptions($value);
            if ($text !== '') {
                return $text;
            }
        }
        return '';
    };
@endphp

<style>
    /* ==================== TRANG GIỎ HÀNG ==================== */
    .cart-page {
        --primary: #e74c3c;
        --primary-dark: #c0392b;
        --border: #ffe0cc;
        width: 100%;
        max-width: 1150px;
        min-width: 0;
        margin: 20px auto;
        padding: 0 20px;
        color: #333;
        box-sizing: border-box;
        container-type: inline-size;
    }
    .cart-page * {
        box-sizing: border-box;
    }
    .cart-page form {
        margin: 0;
    }
    .cart-page button,
    .cart-page input,
    .cart-page select {
        font-family: inherit;
    }

    /* ==================== TIÊU ĐỀ ==================== */
    .cart-page .cart-title {
        color: var(--primary);
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 6px;
    }
    .cart-page .cart-subtitle {
        color: #888;
        font-size: 13px;
        margin: 0 0 18px;
    }

    /* ==================== BỐ CỤC ==================== */
    .cart-page .cart-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 32%);
        gap: 18px;
        align-items: start;
    }
    .cart-page .cart-card {
        min-width: 0;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .03);
    }

    /* ==================== DANH SÁCH MÓN ==================== */
    .cart-page .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        gap: 10px;
        border-bottom: 1px solid #eee;
    }
    .cart-page .cart-header h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
    }
    .cart-page .cart-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-bottom: 1px solid #eee;
        transition: background .2s;
    }
    .cart-page .cart-item:hover {
        background: #fffdfb;
    }

    /* ==================== HÌNH ẢNH ==================== */
    .cart-page .cart-image {
        width: 78px;
        height: 78px;
        flex-shrink: 0;
        border-radius: 10px;
        background: #fff1e8;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        color: var(--primary);
        font-size: 28px;
    }
    .cart-page .cart-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* ==================== THÔNG TIN MÓN ==================== */
    .cart-page .cart-info {
        flex: 1;
        min-width: 0;
    }
    .cart-page .cart-name {
        font-size: 16px;
        font-weight: 700;
        line-height: 1.4;
        margin: 0 0 5px;
        color: #222;
        overflow-wrap: anywhere;
    }
    .cart-page .cart-detail {
        color: #808080;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.5;
        margin: 3px 0;
        overflow-wrap: anywhere;
    }
    .cart-page .cart-note {
        white-space: pre-wrap;
    }
    .cart-page .cart-price {
        color: var(--primary);
        font-size: 14px;
        font-weight: 600;
        margin-top: 6px;
    }
    .cart-page .cart-item-total {
        min-width: 75px;
        text-align: right;
        font-size: 14px;
        font-weight: 700;
        color: var(--primary);
        white-space: nowrap;
    }

    /* ==================== SỐ LƯỢNG ==================== */
    .cart-page .quantity-control {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-top: 7px;
    }
    .cart-page .quantity-control button {
        width: 28px;
        height: 28px;
        padding: 0;
        border: none;
        border-radius: 5px;
        background: transparent;
        color: #333;
        cursor: pointer;
        font-size: 17px;
        font-weight: 500;
    }
    .cart-page .quantity-control button:hover {
        background: #fff0e6;
        color: var(--primary);
    }
    .cart-page .quantity-control button:disabled {
        opacity: .3;
        cursor: not-allowed;
    }
    .cart-page .quantity-control input {
        width: 30px;
        height: 28px;
        padding: 0;
        border: none;
        outline: none;
        background: transparent;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        color: #222;
    }

    /* ==================== XÓA MÓN ==================== */
    .cart-page .remove-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border: none;
        border-radius: 6px;
        background: #fff0ed;
        color: var(--primary);
        font-size: 14px;
        cursor: pointer;
        transition: .2s;
    }
    .cart-page .remove-button:hover {
        background: var(--primary);
        color: #fff;
    }
    .cart-page .clear-button {
        border: 1px solid #f5b7b1;
        background: #fff;
        color: var(--primary);
        padding: 7px 10px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        white-space: nowrap;
        transition: .2s;
    }
    .cart-page .clear-button:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    /* ==================== TIẾP TỤC MUA HÀNG ==================== */
    .cart-page .cart-footer {
        padding: 14px;
    }
    .cart-page .continue-shopping {
        color: var(--primary);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .cart-page .continue-shopping:hover {
        text-decoration: underline;
    }

    /* ==================== TÓM TẮT ĐƠN HÀNG ==================== */
    .cart-page .order-summary {
        padding: 17px;
    }
    .cart-page .order-summary h3 {
        margin: 0 0 15px;
        font-size: 18px;
        font-weight: 700;
    }

    /* ==================== VOUCHER ==================== */
    .cart-page .voucher-box {
        background: #fff8f3;
        padding: 12px;
        border: 1px dashed #f4b9a0;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .cart-page .voucher-title {
        display: flex;
        align-items: center;
        gap: 7px;
        color: var(--primary);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .cart-page .voucher-select {
        width: 100%;
        min-width: 0;
        padding: 9px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        margin-bottom: 8px;
        outline: none;
    }
    .cart-page .voucher-select:focus {
        border-color: var(--primary);
    }
    .cart-page .voucher-button {
        width: 100%;
        border: 1px solid var(--primary);
        border-radius: 6px;
        padding: 8px;
        background: #fff;
        color: var(--primary);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
    }
    .cart-page .voucher-button:hover {
        background: var(--primary);
        color: #fff;
    }
    .cart-page .voucher-success {
        font-size: 12px;
        color: #198754;
        margin-top: 8px;
        line-height: 1.5;
    }
    .cart-page .voucher-error {
        color: #c0392b;
        font-size: 12px;
        margin-top: 8px;
    }
    .cart-page .voucher-link {
        display: block;
        margin-top: 10px;
        text-align: center;
        color: var(--primary);
        font-size: 12px;
        text-decoration: none;
    }
    .cart-page .voucher-link:hover {
        text-decoration: underline;
    }

    /* ==================== TÍNH TIỀN ==================== */
    .cart-page .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 10px 0;
        font-size: 14px;
    }
    .cart-page .summary-row span {
        color: #666;
    }
    .cart-page .summary-row strong {
        white-space: nowrap;
    }
    .cart-page .discount-row,
    .cart-page .discount-row span {
        color: #198754;
    }
    .cart-page .summary-total {
        border-top: 1px dashed #ddd;
        margin-top: 14px;
        padding-top: 14px;
        font-size: 17px;
        font-weight: 700;
    }
    .cart-page .summary-total strong {
        color: var(--primary);
        font-size: 21px;
    }

    /* ==================== THANH TOÁN ==================== */
    .cart-page .checkout-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        border: none;
        border-radius: 7px;
        margin-top: 15px;
        padding: 12px;
        background: var(--primary);
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }
    .cart-page .checkout-button:hover {
        background: var(--primary-dark);
    }

    /* ==================== THÔNG BÁO ==================== */
    .cart-page .cart-message {
        padding: 12px 15px;
        background: #eafaf1;
        color: #196f3d;
        border-radius: 7px;
        margin-bottom: 15px;
        font-size: 13px;
    }
    .cart-page .cart-error {
        padding: 12px 15px;
        background: #fff0ed;
        color: #c0392b;
        border-radius: 7px;
        margin-bottom: 15px;
        font-size: 13px;
    }

    /* ==================== GIỎ HÀNG TRỐNG ==================== */
    .cart-page .empty-cart {
        text-align: center;
        padding: 50px 20px;
    }
    .cart-page .empty-icon {
        color: #f2b6a9;
        font-size: 55px;
        margin-bottom: 15px;
    }
    .cart-page .empty-cart h2 {
        font-size: 21px;
        margin: 0 0 10px;
    }
    .cart-page .empty-cart p {
        color: #888;
        font-size: 14px;
        margin-bottom: 20px;
    }
    .cart-page .empty-cart a {
        display: inline-block;
        background: var(--primary);
        color: #fff;
        padding: 10px 22px;
        border-radius: 7px;
        font-size: 14px;
        text-decoration: none;
    }
    .cart-page .empty-cart a:hover {
        background: var(--primary-dark);
    }

    /* ==================== RESPONSIVE ==================== */
    @container (max-width: 760px) {
        .cart-page .cart-layout {
            grid-template-columns: 1fr;
        }
    }
    @container (max-width: 460px) {
        .cart-page .cart-item {
            flex-wrap: wrap;
            gap: 10px;
        }
        .cart-page .cart-image {
            width: 62px;
            height: 62px;
        }
        .cart-page .cart-name {
            font-size: 14px;
        }
        .cart-page .cart-detail {
            font-size: 12px;
        }
        .cart-page .cart-item-total {
            margin-left: auto;
        }
        .cart-page .cart-header h3 {
            font-size: 15px;
        }
    }
    @media (max-width: 600px) {
        .cart-page {
            padding: 0 10px;
            margin: 15px auto;
        }
        .cart-page .cart-title {
            font-size: 23px;
        }
        .cart-page .cart-layout {
            grid-template-columns: 1fr;
        }
        .cart-page .order-summary {
            padding: 14px;
        }
    }
</style>

<div class="cart-page">
    {{-- ==================== TIÊU ĐỀ ==================== --}}
    <h1 class="cart-title">GIỎ HÀNG CỦA BẠN</h1>
    <p class="cart-subtitle">
        Bạn có {{ $cartItems->sum('quantity') }} món ăn trong giỏ hàng
    </p>

    {{-- ==================== THÔNG BÁO ==================== --}}
    @if (session('success'))
        <div class="cart-message">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="cart-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="cart-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ==================== GIỎ HÀNG TRỐNG ==================== --}}
    @if ($cartItems->isEmpty())
        <div class="cart-card empty-cart">
            <div class="empty-icon">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h2>Giỏ hàng đang trống</h2>
            <p>Hãy thêm những món ăn yêu thích vào giỏ hàng nhé!</p>
            <a href="{{ url('/foods') }}">
                <i class="fa-solid fa-utensils"></i>
                Khám phá thực đơn
            </a>
        </div>
    @else
        <div class="cart-layout">
            {{-- ==================== DANH SÁCH MÓN ==================== --}}
            <div class="cart-card">
                <div class="cart-header">
                    <h3>Danh sách món ăn</h3>
                    <form method="POST"
                          action="{{ route('cart.clear') }}"
                          onsubmit="return confirm('Bạn có chắc muốn xóa tất cả món trong giỏ hàng?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="clear-button">
                            <i class="fa-solid fa-trash"></i>
                            Xóa tất cả
                        </button>
                    </form>
                </div>

                @foreach ($cartItems as $item)
@php
    $variants = $item->variants_selected ?? [];
    if (is_string($variants)) {
        $variants = json_decode($variants, true) ?? [$variants];
    }
    if ($variants instanceof \Illuminate\Support\Collection) {
        $variants = $variants->toArray();
    }
    $variants = is_array($variants) ? $variants : [];
    $toppingList = [];
    $optionList = [];

    // Phân loại topping và tùy chọn
    foreach ($variants as $variant) {
        $name = is_array($variant)
            ? ($variant['name'] ?? '')
            : $variant;
        if (!is_string($name) || trim($name) === '') {
            continue;
        }
        $name = trim($name);
        $lowerName = mb_strtolower($name, 'UTF-8');
        if (\Illuminate\Support\Str::contains($lowerName, [
            'thêm', 'topping', 'trân châu', 'thạch'
        ])) {
            $toppingList[] = $name;
        } else {
            $optionList[] = $name;
        }
    }

    $toppings = implode(', ', array_unique($toppingList));
    $optionText = implode(', ', array_unique($optionList));
    $note = $item->note ?? '';
    $unitPrice = $item->product->price;
    $itemTotal = $unitPrice * $item->quantity;
                    @endphp

                    <div class="cart-item">
                        {{-- Hình ảnh món ăn --}}
                        <div class="cart-image">
                            @if (!empty($item->product->image))
                                <img src="{{ asset($item->product->image) }}"
                                     alt="{{ $item->product->name }}"
                                     loading="lazy">
                            @else
                                <i class="fa-solid fa-burger"></i>
                            @endif
                        </div>

                        {{-- Thông tin món ăn --}}
                        <div class="cart-info">
                            <div class="cart-name">
                                {{ $item->product->name }}
                            </div>
                            @if (filled($toppings))
                                <div class="cart-detail">
                                    Topping: {{ $toppings }}
                                </div>
                            @endif
                            @if (filled($optionText))
                                <div class="cart-detail">
                                    Tùy chọn: {{ $optionText }}
                                </div>
                            @endif
                            @if (filled($note))
                                <div class="cart-detail cart-note">Ghi chú: {{ $note }}</div>
                            @endif
                            <div class="cart-price">
                                {{ number_format($unitPrice, 0, ',', '.') }}đ / món
                            </div>

                            {{-- Cập nhật số lượng --}}
                            <form method="POST"
                                  action="{{ route('cart.update', $item->getKey()) }}"
                                  class="quantity-control">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        name="quantity"
                                        value="{{ $item->quantity - 1 }}"
                                        aria-label="Giảm số lượng"
                                        @disabled($item->quantity <= 1)>
                                    −
                                </button>
                                <input type="text"
                                       value="{{ $item->quantity }}"
                                       aria-label="Số lượng"
                                       readonly>
                                <button type="submit"
                                        name="quantity"
                                        value="{{ $item->quantity + 1 }}"
                                        aria-label="Tăng số lượng">
                                    +
                                </button>
                            </form>
                        </div>

                        {{-- Thành tiền --}}
                        <div class="cart-item-total">
                            {{ number_format($itemTotal, 0, ',', '.') }}đ
                        </div>

                        {{-- Xóa từng món --}}
                        <form method="POST"
                              action="{{ route('cart.destroy', $item->getKey()) }}"
                              onsubmit="return confirm('Bạn có muốn xóa món này khỏi giỏ hàng?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="remove-button"
                                    title="Xóa món"
                                    aria-label="Xóa món">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @endforeach

                {{-- Tiếp tục mua hàng --}}
                <div class="cart-footer">
                    <a href="{{ url('/foods') }}" class="continue-shopping">
                        <i class="fa-solid fa-arrow-left"></i>
                        Tiếp tục mua hàng
                    </a>
                </div>
            </div>

            {{-- ==================== TÓM TẮT ĐƠN HÀNG ==================== --}}
            <aside class="cart-card order-summary">
                <h3>Tóm tắt đơn hàng</h3>

                {{-- Voucher --}}
                <div class="voucher-box">
                    <div class="voucher-title">
                        <i class="fa-solid fa-ticket"></i>
                        Voucher giảm giá
                    </div>
                    <form method="POST" action="{{ route('cart.promotion.apply') }}">
                        @csrf
                        <select name="promotion_id"
                                class="voucher-select"
                                aria-label="Chọn voucher">
                            <option value="">Không sử dụng voucher</option>
                            @foreach ($availablePromotions as $promotion)
                                <option value="{{ $promotion->id_promotion }}"
                                        @selected((string) old('promotion_id', $selectedPromotion?->id_promotion) === (string) $promotion->id_promotion)
                                        @disabled($subtotal < $promotion->min_order_value)>
                                    {{ $promotion->code }} - {{ $promotion->name }}
                                    (Tối thiểu {{ number_format($promotion->min_order_value, 0, ',', '.') }}đ)
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="voucher-button">
                            Áp dụng voucher
                        </button>
                    </form>
                    @if ($selectedPromotion && !$promotionError)
                        <div class="voucher-success">
                            <i class="fa-solid fa-circle-check"></i>
                            Đã áp dụng voucher
                            <strong>{{ $selectedPromotion->code }}</strong>
                        </div>
                    @endif
                    @if ($promotionError)
                        <div class="voucher-error">{{ $promotionError }}</div>
                    @endif
                    @error('promotion_id')
                        <div class="voucher-error">{{ $message }}</div>
                    @enderror
                    <a href="{{ route('promotions.index') }}" class="voucher-link">
                        <i class="fa-solid fa-tags"></i>
                        Xem thêm voucher
                    </a>
                </div>

                {{-- Tạm tính --}}
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                </div>

                {{-- Phí giao hàng --}}
                <div class="summary-row">
                    <span>Phí giao hàng dự kiến</span>
                    <strong>{{ number_format($shippingFee, 0, ',', '.') }}đ</strong>
                </div>

                {{-- Giảm giá --}}
                <div class="summary-row discount-row">
                    <span>
                        Giảm giá
                        @if ($selectedPromotion)
                            ({{ $selectedPromotion->code }})
                        @endif
                    </span>
                    <strong>
                        {{ $discountAmount > 0 ? '-' : '' }}{{ number_format($discountAmount, 0, ',', '.') }}đ
                    </strong>
                </div>

                {{-- Tổng tiền --}}
                <div class="summary-row summary-total">
                    Tổng dự kiến
                    <strong>{{ number_format($total, 0, ',', '.') }}đ</strong>
                </div>

                {{-- Thanh toán --}}
                <form method="GET" action="{{ route('cart.checkout') }}">
                    @if ($selectedPromotion && !$promotionError)
                        <input type="hidden"
                               name="promotion_id"
                               value="{{ $selectedPromotion->id_promotion }}">
                    @endif
                    <button type="submit" class="checkout-button">
                        Tiến hành thanh toán
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </aside>
        </div>
    @endif
</div>

@endsection
