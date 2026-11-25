
@extends('layouts.app')
@section('content')

@php
    $title = 'Thanh toán';
    $availablePromotions = $availablePromotions ?? [];
    $selectedPromotion = $selectedPromotion ?? null;
    $promotionError = $promotionError ?? null;
    $discountAmount = $discountAmount ?? 0;
    $total = max(0, $subtotal + $shippingFee - $discountAmount);
@endphp

<style>
    /* ==================== LAYOUT THANH TOÁN ==================== */
    .checkout-page {
        --primary: #e74c3c;
        --primary-dark: #c0392b;
        --text: #292929;
        --muted: #858585;
        --border: #e8e8e8;
        width: 100%;
        max-width: 1150px;
        min-width: 0;
        margin: 20px auto;
        padding: 0 20px 25px;
        color: var(--text);
        container-type: inline-size;
    }
    .checkout-page * {
        box-sizing: border-box;
    }
    .checkout-page input,
    .checkout-page textarea,
    .checkout-page select,
    .checkout-page button {
        font-family: inherit;
    }

    /* ==================== TIẾN TRÌNH ĐẶT HÀNG ==================== */
    .checkout-page .checkout-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin: 0 0 25px;
    }
    .checkout-page .progress-step {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 500;
        color: #aaa;
        white-space: nowrap;
        text-decoration: none;
    }
    .checkout-page .step-number {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 27px;
        height: 27px;
        border: 1px solid #ddd;
        border-radius: 50%;
        font-size: 12px;
        background: #fff;
        flex-shrink: 0;
    }
    .checkout-page .progress-step.completed {
        color: #198754;
    }
    .checkout-page .progress-step.completed .step-number {
        color: #198754;
        background: #eaf7ef;
        border-color: #eaf7ef;
    }
    .checkout-page .progress-step.active {
        color: var(--primary);
        font-weight: 700;
    }
    .checkout-page .progress-step.active .step-number {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
    .checkout-page .progress-line {
        width: 45px;
        height: 1px;
        background: #ddd;
    }
    .checkout-page .progress-line.completed {
        background: var(--primary);
    }

    /* ==================== TIÊU ĐỀ ==================== */
    .checkout-page .checkout-heading {
        margin-bottom: 20px;
        color: var(--primary);
    }
    .checkout-page .checkout-title {
        font-size: 28px;
        font-weight: 750;
        color: var(--primary);
        margin: 0 0 6px;
    }
    .checkout-page .checkout-subtitle {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }
    .checkout-page .checkout-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, 36%);
        align-items: start;
        gap: 20px;
    }
    .checkout-page .checkout-left {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    /* ==================== KHUNG NỘI DUNG ==================== */
    .checkout-page .checkout-card {
        min-width: 0;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .025);
    }
    .checkout-page .section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        margin-bottom: 17px;
        border-bottom: 1px solid #f0f0f0;
    }
    .checkout-page .section-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        background: #fff1eb;
        color: var(--primary);
        font-size: 16px;
        flex-shrink: 0;
    }
    .checkout-page .section-heading h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
    }
    .checkout-page .section-heading p {
        color: var(--muted);
        font-size: 12px;
        margin: 4px 0 0;
    }

    /* ==================== THÔNG TIN GIAO HÀNG ==================== */
    .checkout-page .customer-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }
    .checkout-page .form-group {
        min-width: 0;
    }
    .checkout-page .form-group.full-width {
        grid-column: 1 / -1;
    }
    .checkout-page .field-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #444;
    }
    .checkout-page .required {
        color: var(--primary);
    }
    .checkout-page .input-wrapper {
        position: relative;
    }
    .checkout-page .input-wrapper > i {
        position: absolute;
        top: 14px;
        left: 12px;
        color: #aaa;
        font-size: 13px;
        pointer-events: none;
    }
    .checkout-page .form-control {
        display: block;
        width: 100%;
        min-width: 0;
        min-height: 42px;
        padding: 11px 12px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 7px;
        color: #333;
        font-size: 13px;
        line-height: 1.5;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .checkout-page .input-wrapper .form-control {
        padding-left: 35px;
    }
    .checkout-page .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, .08);
    }
    .checkout-page .form-control::placeholder {
        color: #aaa;
    }
    .checkout-page textarea.form-control {
        resize: vertical;
        min-height: 75px;
    }
    .checkout-page .optional {
        color: #999;
        font-weight: 400;
        font-size: 12px;
    }
    .checkout-page .field-error {
        display: block;
        color: #c0392b;
        font-size: 12px;
        line-height: 1.5;
        margin-top: 6px;
    }

    /* ==================== PHƯƠNG THỨC THANH TOÁN ==================== */
    .checkout-page .payment-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .checkout-page .payment-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        transition: .2s;
    }
    .checkout-page .payment-option:hover {
        border-color: #e74c3c;
    }
    .checkout-page .payment-option:has(input:checked) {
        border-color: var(--primary);
        background: #fff8f5;
    }
    .checkout-page .payment-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border: 1px solid #eee;
        border-radius: 8px;
        background: #fff;
        color: var(--primary);
        font-size: 19px;
    }
    .checkout-page .payment-info {
        flex: 1;
        min-width: 0;
    }
    .checkout-page .payment-name {
        font-size: 14px;
        color: #333;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .checkout-page .payment-desc {
        font-size: 12px;
        color: var(--muted);
        line-height: 1.5;
    }
    .checkout-page .payment-option input {
        width: 17px;
        height: 17px;
        flex-shrink: 0;
        margin: 0;
        accent-color: var(--primary);
        cursor: pointer;
    }

    /* ==================== TÓM TẮT ĐƠN HÀNG ==================== */
    .checkout-page .order-summary {
        align-self: start;
    }
    .checkout-page .summary-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 15px;
    }
    .checkout-page .summary-heading h3 {
        margin: 0 0 5px;
        font-size: 18px;
        font-weight: 700;
    }
    .checkout-page .summary-count {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
    }
    .checkout-page .edit-cart {
        color: var(--primary);
        font-size: 12px;
        text-decoration: none;
        white-space: nowrap;
        padding-top: 4px;
    }
    .checkout-page .edit-cart:hover {
        text-decoration: underline;
    }

    /* ==================== DANH SÁCH MÓN ĂN ==================== */
    .checkout-page .order-items {
        max-height: 310px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .checkout-page .order-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f1f1;
    }
    .checkout-page .order-item:last-child {
        border-bottom: none;
    }
    .checkout-page .item-image {
        position: relative;
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        border-radius: 8px;
        background: #fff1e8;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        color: var(--primary);
    }
    .checkout-page .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .checkout-page .item-info {
        flex: 1;
        min-width: 0;
    }
    .checkout-page .item-name {
        font-size: 13px;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 4px;
        overflow-wrap: anywhere;
    }
    .checkout-page .item-detail {
        color: #888;
        font-size: 11px;
        line-height: 1.5;
        margin: 2px 0;
        overflow-wrap: anywhere;
    }
    .checkout-page .item-quantity {
        color: #888;
        font-size: 12px;
        margin-top: 4px;
    }
    .checkout-page .item-price {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
        padding-top: 2px;
    }

    /* ==================== VOUCHER ==================== */
    .checkout-page .voucher-section {
        padding: 16px 0;
        margin-top: 10px;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }
    .checkout-page .voucher-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .checkout-page .voucher-title {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #444;
        font-size: 13px;
        font-weight: 600;
    }
    .checkout-page .voucher-title i {
        color: var(--primary);
    }
    .checkout-page .voucher-link {
        color: var(--primary);
        font-size: 12px;
        text-decoration: none;
        white-space: nowrap;
    }
    .checkout-page .voucher-link:hover {
        text-decoration: underline;
    }
    .checkout-page .voucher-select {
        width: 100%;
        min-width: 0;
        padding: 10px;
        color: #555;
        font-size: 12px;
        border: 1px dashed #efb4a6;
        border-radius: 7px;
        background: #fff9f6;
        outline: none;
        cursor: pointer;
    }
    .checkout-page .voucher-applied {
        color: #198754;
        font-size: 12px;
        margin: 8px 0 0;
    }

    /* ==================== TÍNH TIỀN ==================== */
    .checkout-page .price-summary {
        padding-top: 5px;
    }
    .checkout-page .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin: 14px 0;
        font-size: 13px;
    }
    .checkout-page .price-row span {
        color: #666;
    }
    .checkout-page .price-row strong {
        font-weight: 600;
        white-space: nowrap;
    }
    .checkout-page .discount-row,
    .checkout-page .discount-row span {
        color: #198754;
    }
    .checkout-page .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding-top: 17px;
        margin-top: 16px;
        border-top: 1px dashed #ddd;
        font-size: 15px;
        font-weight: 700;
    }
    .checkout-page .total-row strong {
        color: var(--primary);
        font-size: 24px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* ==================== NÚT ĐẶT HÀNG ==================== */
    .checkout-page .place-order-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        padding: 14px 10px;
        margin-top: 20px;
        border: none;
        border-radius: 8px;
        background: var(--primary);
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }
    .checkout-page .place-order-button:hover {
        background: var(--primary-dark);
    }
    .checkout-page .place-order-button:disabled {
        cursor: not-allowed;
        opacity: .7;
    }
    .checkout-page .security-text {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        color: #999;
        font-size: 11px;
        text-align: center;
        margin: 12px 0 0;
    }
    .checkout-page .back-to-cart {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 13px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        margin-top: 2px;
    }
    .checkout-page .back-to-cart:hover {
        text-decoration: underline;
    }

    /* ==================== THÔNG BÁO ==================== */
    .checkout-page .checkout-alert {
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 16px;
        font-size: 13px;
        line-height: 1.6;
    }
    .checkout-page .alert-success {
        color: #196f3d;
        background: #eafaf1;
        border: 1px solid #ccebd7;
    }
    .checkout-page .alert-error {
        color: #c0392b;
        background: #fff2ef;
        border: 1px solid #f5cdc5;
    }

    /* ==================== RESPONSIVE ==================== */
    @container (max-width: 800px) {
        .checkout-page .checkout-layout {
            grid-template-columns: 1fr;
        }
    }
    @container (max-width: 460px) {
        .checkout-page .customer-grid {
            grid-template-columns: 1fr;
        }
        .checkout-page .checkout-card {
            padding: 15px;
        }
        .checkout-page .progress-line {
            width: 14px;
        }
        .checkout-page .checkout-progress {
            gap: 5px;
        }
        .checkout-page .progress-step {
            font-size: 11px;
            gap: 4px;
        }
        .checkout-page .step-number {
            width: 23px;
            height: 23px;
        }
    }
    @media (max-width: 600px) {
        .checkout-page {
            margin: 15px auto;
            padding: 0 12px 20px;
        }
        .checkout-page .checkout-title {
            font-size: 24px;
        }
        .checkout-page .checkout-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="checkout-page">
    {{-- ==================== TIẾN TRÌNH ĐẶT HÀNG ==================== --}}
    <div class="checkout-progress">
        <a href="{{ route('cart.index') }}" class="progress-step completed">
            <span class="step-number">
                <i class="fa-solid fa-check"></i>
            </span>
            <span>Giỏ hàng</span>
        </a>
        <div class="progress-line completed"></div>
        <div class="progress-step active">
            <span class="step-number">2</span>
            <span>Thanh toán</span>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <span class="step-number">3</span>
            <span>Hoàn tất</span>
        </div>
    </div>

    {{-- ==================== TIÊU ĐỀ ==================== --}}
    <div class="checkout-heading">
        <h1 class="checkout-title">THANH TOÁN</h1>
    </div>

    {{-- ==================== THÔNG BÁO ==================== --}}
    @if (session('success'))
        <div class="checkout-alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="checkout-alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="checkout-alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ==================== FORM ĐẶT HÀNG ==================== --}}
    <form id="checkoutForm"
          method="POST"
          action="{{ route('cart.place-order') }}"
          class="checkout-layout">
        @csrf

        {{-- ==================== CỘT BÊN TRÁI ==================== --}}
        <div class="checkout-left">
            <section class="checkout-card">
                <div class="section-heading">
                    <div class="section-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h3>Thông tin giao hàng</h3>
                        <p>Nhập thông tin người nhận đơn hàng</p>
                    </div>
                </div>
                <div class="customer-grid">
                    {{-- Họ và tên --}}
                    <div class="form-group">
                        <label for="customerName" class="field-label">
                            Họ và tên <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-user"></i>
                            <input id="customerName"
                                   type="text"
                                   name="customer_name"
                                   class="form-control"
                                   value="{{ old('customer_name', $customer->name) }}"
                                   placeholder="Nhập họ và tên"
                                   autocomplete="name"
                                   required>
                        </div>
                        @error('customer_name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- Số điện thoại --}}
                    <div class="form-group">
                        <label for="customerPhone" class="field-label">
                            Số điện thoại <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-phone"></i>
                            <input id="customerPhone"
                                   type="tel"
                                   name="customer_phone"
                                   class="form-control"
                                   value="{{ old('customer_phone', $customer->phone) }}"
                                   placeholder="Nhập số điện thoại"
                                   autocomplete="tel"
                                   required>
                        </div>
                        @error('customer_phone')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- Địa chỉ giao hàng --}}
                    <div class="form-group full-width">
                        <label for="customerAddress" class="field-label">
                            Địa chỉ giao hàng <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <textarea id="customerAddress"
                                      name="customer_address"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Số nhà, tên đường, phường/xã..."
                                      autocomplete="street-address"
                                      required>{{ old('customer_address', $customer->address) }}</textarea>
                        </div>
                        @error('customer_address')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- Ghi chú đơn hàng --}}
                    <div class="form-group full-width">
                        <label for="orderNote" class="field-label">
                            Ghi chú đơn hàng
                            <span class="optional">(Không bắt buộc)</span>
                        </label>
                        <textarea id="orderNote"
                                  name="note"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Ví dụ: Gọi điện khi đến, giao tại cổng...">{{ old('note') }}</textarea>
                        @error('note')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ==================== PHƯƠNG THỨC THANH TOÁN ==================== --}}
            <section class="checkout-card">
                <div class="section-heading">
                    <div class="section-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <h3>Phương thức thanh toán</h3>
                        <p>Chọn hình thức thanh toán phù hợp với bạn</p>
                    </div>
                </div>
                <div class="payment-list">
                    {{-- Thanh toán tiền mặt --}}
                    <label class="payment-option">
                        <div class="payment-icon">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div class="payment-info">
                            <div class="payment-name">
                                Tiền mặt khi nhận hàng
                            </div>
                            <div class="payment-desc">
                                Thanh toán trực tiếp khi nhận được món ăn.
                            </div>
                        </div>
                        <input type="radio"
                               name="method"
                               value="1"
                               @checked((string) old('method', 1) === '1')
                               required>
                    </label>
                    {{-- Thanh toán chuyển khoản --}}
                    <label class="payment-option">
                        <div class="payment-icon">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div class="payment-info">
                            <div class="payment-name">
                                Chuyển khoản ngân hàng
                            </div>
                            <div class="payment-desc">
                                Thanh toán qua tài khoản ngân hàng.
                            </div>
                        </div>
                        <input type="radio"
                               name="method"
                               value="2"
                               @checked((string) old('method', 1) === '2')
                               required>
                    </label>
                </div>
                @error('method')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </section>

            <a href="{{ route('cart.index') }}" class="back-to-cart">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại giỏ hàng
            </a>
        </div>

        {{-- ==================== CỘT TÓM TẮT ĐƠN HÀNG ==================== --}}
        <aside class="checkout-card order-summary">
            <div class="summary-heading">
                <div>
                    <h3>Đơn hàng của bạn</h3>
                    <p class="summary-count">
                        {{ $cartItems->sum('quantity') }} món ăn
                    </p>
                </div>
                <a href="{{ route('cart.index') }}" class="edit-cart">
                    Chỉnh sửa
                </a>
            </div>

            {{-- ==================== DANH SÁCH MÓN ĂN ==================== --}}
            <div class="order-items">
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
                        $variantNames = [];
                        foreach ($variants as $variant) {
                            $variantName = is_array($variant)
                                ? ($variant['name'] ?? '')
                                : $variant;
                            if (is_string($variantName) && trim($variantName) !== '') {
                                $variantNames[] = trim($variantName);
                            }
                        }
                        $variantText = implode(', ', array_unique($variantNames));
                        $itemNote = $item->note ?? '';
                        $itemTotal = $item->product->price * $item->quantity;
                    @endphp
                    <div class="order-item">
                        <div class="item-image">
                            @if (!empty($item->product->image))
                                <img src="{{ asset($item->product->image) }}"
                                     alt="{{ $item->product->name }}"
                                     loading="lazy">
                            @else
                                <i class="fa-solid fa-utensils"></i>
                            @endif
                        </div>
                        <div class="item-info">
                            <div class="item-name">
                                {{ $item->product->name }}
                            </div>
                            @if (filled($variantText))
                                <div class="item-detail">
                                    {{ $variantText }}
                                </div>
                            @endif
                            @if (filled($itemNote))
                                <div class="item-detail">
                                    Ghi chú: {{ $itemNote }}
                                </div>
                            @endif
                            <div class="item-quantity">
                                Số lượng: {{ $item->quantity }}
                            </div>
                        </div>
                        <div class="item-price">
                            {{ number_format($itemTotal, 0, ',', '.') }}đ
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ==================== VOUCHER ==================== --}}
            <div class="voucher-section">
                <div class="voucher-heading">
                    <label for="promotionSelect" class="voucher-title">
                        <i class="fa-solid fa-ticket"></i>
                        Mã khuyến mãi
                    </label>
                    <a href="{{ route('promotions.index') }}"
                       class="voucher-link">
                        Xem voucher
                    </a>
                </div>
                <select id="promotionSelect"
                        name="promotion_id"
                        class="voucher-select">
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
                @if ($selectedPromotion && !$promotionError)
                    <p class="voucher-applied">
                        <i class="fa-solid fa-circle-check"></i>
                        Đã áp dụng {{ $selectedPromotion->code }}
                    </p>
                @endif
                @if ($promotionError)
                    <span class="field-error">{{ $promotionError }}</span>
                @endif
                @error('promotion_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- ==================== CHI TIẾT THANH TOÁN ==================== --}}
            <div class="price-summary">
                <div class="price-row">
                    <span>Tạm tính</span>
                    <strong>
                        {{ number_format($subtotal, 0, ',', '.') }}đ
                    </strong>
                </div>
                <div class="price-row">
                    <span>Phí giao hàng</span>
                    <strong>
                        {{ number_format($shippingFee, 0, ',', '.') }}đ
                    </strong>
                </div>
                <div class="price-row discount-row">
                    <span>
                        Giảm giá
                        @if ($selectedPromotion && $discountAmount > 0)
                            ({{ $selectedPromotion->code }})
                        @endif
                    </span>
                    <strong>
                        {{ $discountAmount > 0 ? '-' : '' }}{{ number_format($discountAmount, 0, ',', '.') }}đ
                    </strong>
                </div>
                <div class="total-row">
                    <span>Tổng thanh toán</span>
                    <strong>
                        {{ number_format($total, 0, ',', '.') }}đ
                    </strong>
                </div>
            </div>

            {{-- ==================== XÁC NHẬN ĐẶT HÀNG ==================== --}}
            <button id="placeOrderButton"
                    type="submit"
                    class="place-order-button"
                    @disabled($cartItems->isEmpty())>
                Xác nhận đặt hàng
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </aside>
    </form>
</div>

<script>
    const checkoutForm = document.getElementById('checkoutForm');
    const placeOrderButton = document.getElementById('placeOrderButton');
    const promotionSelect = document.getElementById('promotionSelect');

    // Cập nhật tổng tiền theo voucher được chọn
    promotionSelect?.addEventListener('change', function () {
        const checkoutUrl = new URL(@json(route('cart.checkout')), window.location.origin);
        checkoutUrl.searchParams.set('promotion_id', this.value);
        const customerName = document.getElementById('customerName').value;
        const customerPhone = document.getElementById('customerPhone').value;
        const customerAddress = document.getElementById('customerAddress').value;
        const orderNote = document.getElementById('orderNote').value;
        const paymentMethod = document.querySelector('input[name="method"]:checked')?.value || '1';
        try {
            sessionStorage.setItem('fastfood_checkout_draft', JSON.stringify({
                customerName,
                customerPhone,
                customerAddress,
                orderNote,
                paymentMethod
            }));
        } catch (error) {
            console.warn('Không thể lưu tạm thông tin thanh toán.', error);
        }
        window.location.href = checkoutUrl.toString();
    });

    // Khôi phục thông tin khi khách hàng thay đổi voucher
    try {
        const checkoutDraft = sessionStorage.getItem('fastfood_checkout_draft');
        if (checkoutDraft) {
            const draft = JSON.parse(checkoutDraft);
            document.getElementById('customerName').value = draft.customerName ?? '';
            document.getElementById('customerPhone').value = draft.customerPhone ?? '';
            document.getElementById('customerAddress').value = draft.customerAddress ?? '';
            document.getElementById('orderNote').value = draft.orderNote ?? '';
            document.querySelectorAll('input[name="method"]').forEach(input => {
                input.checked = input.value === draft.paymentMethod;
            });
            sessionStorage.removeItem('fastfood_checkout_draft');
        }
    } catch (error) {
        console.warn('Không thể khôi phục thông tin thanh toán.', error);
    }

    // Ngăn khách hàng gửi đơn hàng nhiều lần
    checkoutForm?.addEventListener('submit', function () {
        placeOrderButton.disabled = true;
        placeOrderButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý đơn hàng...';
        try {
            sessionStorage.removeItem('fastfood_checkout_draft');
        } catch (error) {
            console.warn(error);
        }
    });

    // Khôi phục nút khi khách hàng quay lại trang
    window.addEventListener('pageshow', function (event) {
        if (event.persisted && placeOrderButton && checkoutForm) {
            placeOrderButton.disabled = @json($cartItems->isEmpty());
            placeOrderButton.innerHTML = 'Xác nhận đặt hàng <i class="fa-solid fa-arrow-right"></i>';
        }
    });
</script>

@endsection
