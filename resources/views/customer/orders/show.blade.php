
@extends('layouts.app')
@section('content')

@php
    $title = 'Chi tiết đơn hàng';
    $orderCode = 'DH' . str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT);
    $statusColor = $order->statusColor();
    $statusLabel = $order->statusLabel();
    $isCancelled = (string) $order->status === (string) \App\Models\Order::STATUS_CANCELLED;
    $isDelivered = (string) $order->status === (string) \App\Models\Order::STATUS_DELIVERED;
    $isTracking = !$isCancelled && !$isDelivered;
    $totalQuantity = $order->details->sum('quantity');
    $subtotal = $order->details->sum(fn($detail) => $detail->price * $detail->quantity);
    $shippingFee = $order->shipping_fee ?? 0;
    $totalPrice = $order->total_price;
    $discountAmount = max(0, $subtotal + $shippingFee - $totalPrice);
@endphp

<style>
    /* ==================== LAYOUT CHUNG ==================== */
    .order-detail-page {
        --primary: #e74c3c;
        --primary-dark: #c0392b;
        --text: #292929;
        --muted: #888;
        --border: #e8e8e8;
        width: 100%;
        max-width: 1150px;
        min-width: 0;
        margin: 20px auto;
        padding: 0 20px 25px;
        color: var(--text);
        box-sizing: border-box;
        container-type: inline-size;
    }
    .order-detail-page * {
        box-sizing: border-box;
    }
    .order-detail-page button,
    .order-detail-page input {
        font-family: inherit;
    }
    .order-detail-page form {
        margin: 0;
    }

    /* ==================== BREADCRUMB ==================== */
    .order-detail-page .order-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 12px;
        color: #999;
        margin-bottom: 18px;
    }
    .order-detail-page .order-breadcrumb a {
        color: #777;
        text-decoration: none;
    }
    .order-detail-page .order-breadcrumb a:hover {
        color: var(--primary);
    }
    .order-detail-page .order-breadcrumb i {
        font-size: 9px;
    }
    .order-detail-page .order-breadcrumb span {
        color: var(--primary);
        font-weight: 600;
    }

    /* ==================== TIÊU ĐỀ ==================== */
    .order-detail-page .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 22px;
    }
    .order-detail-page .page-title {
        color: var(--primary);
        font-size: 27px;
        font-weight: bold;
        margin: 0 0 6px;
    }
    .order-detail-page .page-description {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
        margin: 0;
    }
    .order-detail-page .heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        border-radius: 11px;
        background: #fff0e8;
        color: var(--primary);
        font-size: 24px;
        flex-shrink: 0;
    }

    /* ==================== THÔNG TIN ĐƠN HÀNG ==================== */
    .order-detail-page .order-overview {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 11px;
        padding: 20px;
        margin-bottom: 18px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .025);
    }
    .order-detail-page .overview-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
    }
    .order-detail-page .order-identification {
        display: flex;
        align-items: center;
        gap: 13px;
    }
    .order-detail-page .order-main-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #fff2eb;
        color: var(--primary);
        font-size: 22px;
        flex-shrink: 0;
    }
    .order-detail-page .order-code-label {
        color: #999;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .6px;
        margin-bottom: 4px;
    }
    .order-detail-page .order-code {
        font-size: 23px;
        font-weight: 800;
        margin: 0 0 5px;
        line-height: 1.3;
    }
    .order-detail-page .order-date {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #888;
    }

    /* ==================== TRẠNG THÁI ĐƠN HÀNG ==================== */
    .order-detail-page .order-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 13px;
        border-radius: 6px;
        background: #f5f5f5;
        background: color-mix(in srgb, var(--status-color) 10%, white);
        color: var(--status-color);
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }
    .order-detail-page .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }
    .order-detail-page .tracking-info {
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid #eee;
        margin-top: 17px;
        padding-top: 14px;
        color: #888;
        font-size: 12px;
        line-height: 1.5;
    }
    .order-detail-page .tracking-info i {
        color: var(--primary);
        font-size: 13px;
    }
    .order-detail-page .tracking-info.delivered i {
        color: #198754;
    }
    .order-detail-page .tracking-info.cancelled i {
        color: #c0392b;
    }

    /* ==================== BỐ CỤC HAI CỘT ==================== */
    .order-detail-page .order-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, 35%);
        align-items: start;
        gap: 18px;
    }
    .order-detail-page .order-left,
    .order-detail-page .order-right {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }
    .order-detail-page .detail-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 11px;
        padding: 18px;
        min-width: 0;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .025);
    }
    .order-detail-page .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 14px;
        border-bottom: 1px solid #eee;
        margin-bottom: 5px;
    }
    .order-detail-page .section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 17px;
        font-weight: 700;
        margin: 0;
    }
    .order-detail-page .section-title i {
        color: var(--primary);
        font-size: 16px;
    }
    .order-detail-page .section-count {
        color: #888;
        font-size: 12px;
        white-space: nowrap;
    }

    /* ==================== DANH SÁCH MÓN ĂN ==================== */
    .order-detail-page .product-item {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 16px 0;
        border-bottom: 1px solid #eee;
    }
    .order-detail-page .product-item:last-child {
        border-bottom: none;
        padding-bottom: 5px;
    }
    .order-detail-page .product-image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 72px;
        height: 72px;
        border-radius: 9px;
        background: #fff2e9;
        color: var(--primary);
        overflow: hidden;
        flex-shrink: 0;
        font-size: 24px;
    }
    .order-detail-page .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .order-detail-page .product-info {
        flex: 1;
        min-width: 0;
    }
    .order-detail-page .product-name {
        font-size: 14px;
        font-weight: 700;
        color: #333;
        margin: 0 0 5px;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }
    .order-detail-page .product-detail {
        color: #888;
        font-size: 12px;
        line-height: 1.6;
        margin: 3px 0;
        overflow-wrap: anywhere;
    }
    .order-detail-page .product-note {
        white-space: pre-wrap;
    }
    .order-detail-page .product-quantity {
        color: #777;
        font-size: 12px;
        margin-top: 6px;
    }
    .order-detail-page .product-price {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
    }
    .order-detail-page .empty-products {
        padding: 25px 10px;
        text-align: center;
        color: #999;
        font-size: 13px;
    }

    /* ==================== THÔNG TIN GIAO HÀNG ==================== */
    .order-detail-page .customer-info {
        padding-top: 8px;
    }
    .order-detail-page .customer-item {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 11px 0;
    }
    .order-detail-page .customer-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 33px;
        height: 33px;
        border-radius: 7px;
        background: #f7f7f7;
        color: #888;
        font-size: 13px;
        flex-shrink: 0;
    }
    .order-detail-page .customer-content {
        flex: 1;
        min-width: 0;
    }
    .order-detail-page .customer-label {
        font-size: 11px;
        color: #999;
        margin-bottom: 5px;
    }
    .order-detail-page .customer-value {
        font-size: 13px;
        font-weight: 500;
        color: #333;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }
    .order-detail-page .customer-note {
        white-space: pre-wrap;
    }

    /* ==================== CHI TIẾT THANH TOÁN ==================== */
    .order-detail-page .payment-summary {
        padding-top: 5px;
    }
    .order-detail-page .payment-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: 13px;
        margin: 14px 0;
    }
    .order-detail-page .payment-row span {
        color: #777;
    }
    .order-detail-page .payment-row strong {
        font-weight: 600;
        white-space: nowrap;
    }
    .order-detail-page .discount-row,
    .order-detail-page .discount-row span {
        color: #198754;
    }
    .order-detail-page .payment-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        border-top: 1px dashed #ddd;
        padding-top: 17px;
        margin-top: 17px;
        font-size: 15px;
        font-weight: 700;
    }
    .order-detail-page .payment-total strong {
        color: var(--primary);
        font-size: 23px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* ==================== PHƯƠNG THỨC THANH TOÁN ==================== */
    .order-detail-page .payment-method {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 17px;
        padding: 12px;
        border-radius: 7px;
        background: #f8f8f8;
    }
    .order-detail-page .payment-method-icon {
        color: var(--primary);
        font-size: 19px;
    }
    .order-detail-page .payment-method-label {
        color: #999;
        font-size: 11px;
        margin-bottom: 4px;
    }
    .order-detail-page .payment-method-value {
        color: #444;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    /* ==================== HỖ TRỢ ĐƠN HÀNG ==================== */
    .order-detail-page .support-card {
        display: flex;
        align-items: flex-start;
        gap: 13px;
    }
    .order-detail-page .support-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        border-radius: 8px;
        background: #fff0e8;
        color: var(--primary);
        font-size: 19px;
    }
    .order-detail-page .support-content {
        flex: 1;
        min-width: 0;
    }
    .order-detail-page .support-title {
        font-size: 14px;
        font-weight: 700;
        margin: 0 0 6px;
    }
    .order-detail-page .support-description {
        color: #888;
        font-size: 12px;
        line-height: 1.7;
        margin: 0 0 9px;
    }
    .order-detail-page .support-phone {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--primary);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }
    .order-detail-page .support-phone:hover {
        text-decoration: underline;
    }

    /* ==================== HỦY ĐƠN HÀNG ==================== */
    .order-detail-page .cancel-section {
        margin-top: 18px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    .order-detail-page .cancel-description {
        color: #999;
        font-size: 11px;
        line-height: 1.6;
        text-align: center;
        margin: 0 0 10px;
    }
    .order-detail-page .cancel-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 11px 14px;
        background: #fff;
        color: var(--primary);
        border: 1px solid #f1aaa0;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
    }
    .order-detail-page .cancel-button:hover {
        color: #fff;
        background: var(--primary);
        border-color: var(--primary);
    }
    .order-detail-page .cancel-button:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    /* ==================== HỘP THOẠI XÁC NHẬN HỦY ==================== */
    .order-detail-page .cancel-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(0, 0, 0, .45);
    }
    .order-detail-page .cancel-modal[hidden] {
        display: none !important;
    }
    .order-detail-page .modal-content {
        width: 100%;
        max-width: 390px;
        background: #fff;
        border-radius: 14px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, .15);
    }
    .order-detail-page .modal-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        height: 58px;
        margin: 0 auto 16px;
        border-radius: 50%;
        background: #fff0e9;
        color: var(--primary);
        font-size: 25px;
    }
    .order-detail-page .modal-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 10px;
    }
    .order-detail-page .modal-description {
        color: #888;
        font-size: 13px;
        line-height: 1.7;
        margin: 0 0 22px;
    }
    .order-detail-page .modal-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .order-detail-page .modal-button {
        padding: 11px 8px;
        border: 1px solid #ddd;
        border-radius: 7px;
        background: #fff;
        color: #555;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .order-detail-page .modal-button:hover {
        background: #f5f5f5;
    }
    .order-detail-page .modal-button.confirm {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
    .order-detail-page .modal-button.confirm:hover {
        background: var(--primary-dark);
    }

    /* ==================== THÔNG BÁO ==================== */
    .order-detail-page .order-message {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 13px 15px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 13px;
        line-height: 1.6;
    }
    .order-detail-page .order-message.success {
        background: #eafaf1;
        color: #196f3d;
        border: 1px solid #cdebd8;
    }
    .order-detail-page .order-message.error {
        background: #fff0ed;
        color: #c0392b;
        border: 1px solid #f4d0c8;
    }

    /* ==================== NÚT QUAY LẠI ==================== */
    .order-detail-page .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-top: 20px;
    }
    .order-detail-page .back-button:hover {
        text-decoration: underline;
    }

    /* ==================== RESPONSIVE ==================== */
    @container (max-width: 780px) {
        .order-detail-page .order-layout {
            grid-template-columns: 1fr;
        }
    }
    @container (max-width: 450px) {
        .order-detail-page .detail-card,
        .order-detail-page .order-overview {
            padding: 14px;
        }
        .order-detail-page .product-item {
            gap: 10px;
        }
        .order-detail-page .product-image {
            width: 55px;
            height: 55px;
        }
        .order-detail-page .product-name {
            font-size: 13px;
        }
        .order-detail-page .product-price {
            font-size: 12px;
        }
        .order-detail-page .product-detail {
            font-size: 11px;
        }
        .order-detail-page .payment-total strong {
            font-size: 20px;
        }
        .order-detail-page .section-title {
            font-size: 15px;
        }
    }
    @media (max-width: 600px) {
        .order-detail-page {
            margin: 15px auto;
            padding: 0 12px 20px;
        }
        .order-detail-page .page-title {
            font-size: 23px;
        }
        .order-detail-page .order-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="order-detail-page">
    {{-- ==================== BREADCRUMB ==================== --}}
    <nav class="order-breadcrumb" aria-label="Đường dẫn trang">
        <a href="{{ url('/foods') }}">Trang chủ</a>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('customer.orders.index') }}">Lịch sử đơn hàng</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>{{ $orderCode }}</span>
    </nav>

    {{-- ==================== TIÊU ĐỀ ==================== --}}
    <div class="page-heading">
        <div>
            <h1 class="page-title">CHI TIẾT ĐƠN HÀNG </h1>
        </div>
    </div>

    {{-- ==================== THÔNG BÁO ==================== --}}
    @if (session('success'))
        <div class="order-message success" role="status">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="order-message error" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="order-message error" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ==================== THÔNG TIN ĐƠN HÀNG ==================== --}}
    <section class="order-overview">
        <div class="overview-header">
            <div class="order-identification">
                <div class="order-main-icon">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <div class="order-code-label">MÃ ĐƠN HÀNG</div>
                    <h2 class="order-code">#{{ $orderCode }}</h2>
                    <div class="order-date">
                        <i class="fa-regular fa-clock"></i>
                        {{ $order->created_at?->format('d/m/Y · H:i') ?? 'Không xác định' }}
                    </div>
                </div>
            </div>
            <span id="orderStatus"
                  class="order-status"
                  data-status="{{ $order->status }}"
                  style="--status-color: {{ $statusColor }};">
                <span class="status-dot"></span>
                <span id="orderStatusLabel">{{ $statusLabel }}</span>
            </span>
        </div>
        @if ($isDelivered)
            <div class="tracking-info delivered">
                <i class="fa-solid fa-circle-check"></i>
                Đơn hàng đã được giao thành công.
            </div>
        @elseif ($isCancelled)
            <div class="tracking-info cancelled">
                <i class="fa-solid fa-circle-xmark"></i>
                Đơn hàng này đã được hủy.
            </div>
        @endif
    </section>

    <div class="order-layout">
        {{-- ==================== CỘT BÊN TRÁI ==================== --}}
        <div class="order-left">

            {{-- Danh sách món ăn --}}
            <section class="detail-card">
                <div class="section-heading">
                    <h3 class="section-title">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Món đã đặt
                    </h3>
                    <span class="section-count">
                        {{ $totalQuantity }} món
                    </span>
                </div>
                @forelse ($order->details as $detail)
                    @php
                        $variants = $detail->variants_selected ?? [];
                        if (is_string($variants)) {
                            $variants = json_decode($variants, true) ?? [$variants];
                        }
                        if ($variants instanceof \Illuminate\Support\Collection) {
                            $variants = $variants->toArray();
                        }
                        $variants = is_array($variants) ? $variants : [];
                        $toppingList = [];
                        $optionList = [];
                        foreach ($variants as $variant) {
                            $variantName = is_array($variant)
                                ? ($variant['name'] ?? '')
                                : $variant;
                            if (!is_string($variantName) || trim($variantName) === '') {
                                continue;
                            }
                            $variantName = trim($variantName);
                            $lowerName = mb_strtolower($variantName, 'UTF-8');
                            if (\Illuminate\Support\Str::contains($lowerName, [
                                'thêm', 'topping', 'trân châu', 'thạch'
                            ])) {
                                $toppingList[] = $variantName;
                            } else {
                                $optionList[] = $variantName;
                            }
                        }
                        $toppingText = implode(', ', array_unique($toppingList));
                        $optionText = implode(', ', array_unique($optionList));
                        $itemNote = $detail->note ?? '';
                        $itemTotal = $detail->price * $detail->quantity;
                    @endphp
                    <div class="product-item">
                        <div class="product-image">
                            @if (!empty($detail->product?->image))
                                <img src="{{ asset($detail->product->image) }}"
                                     alt="{{ $detail->product?->name ?? 'Món ăn' }}"
                                     loading="lazy">
                            @else
                                <i class="fa-solid fa-utensils"></i>
                            @endif
                        </div>
                        <div class="product-info">
                            <h4 class="product-name">
                                {{ $detail->product?->name ?? 'Món ăn không còn trong thực đơn' }}
                            </h4>
                            @if (filled($toppingText))
                                <div class="product-detail">
                                    Topping: {{ $toppingText }}
                                </div>
                            @endif
                            @if (filled($optionText))
                                <div class="product-detail">
                                    Tùy chọn: {{ $optionText }}
                                </div>
                            @endif
                            @if (filled($itemNote))
                                <div class="product-detail product-note">Ghi chú: {{ $itemNote }}</div>
                            @endif
                            <div class="product-quantity">
                                SL: {{ $detail->quantity }}
                                × {{ number_format($detail->price, 0, ',', '.') }}đ
                            </div>
                        </div>
                        <div class="product-price">
                            {{ number_format($itemTotal, 0, ',', '.') }}đ
                        </div>
                    </div>
                @empty
                    <div class="empty-products">
                        Không có thông tin món ăn.
                    </div>
                @endforelse
            </section>
        </div>

        {{-- ==================== CỘT BÊN PHẢI ==================== --}}
        <div class="order-right">

            {{-- Thông tin giao hàng --}}
            <section class="detail-card">
                <div class="section-heading">
                    <h3 class="section-title">
                        <i class="fa-solid fa-location-dot"></i>
                        Thông tin giao hàng
                    </h3>
                </div>
                <div class="customer-info">
                    <div class="customer-item">
                        <div class="customer-icon">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <div class="customer-content">
                            <div class="customer-label">Người nhận</div>
                            <div class="customer-value">
                                {{ $order->customer_name }}
                            </div>
                        </div>
                    </div>
                    <div class="customer-item">
                        <div class="customer-icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="customer-content">
                            <div class="customer-label">Số điện thoại</div>
                            <div class="customer-value">
                                {{ $order->customer_phone }}
                            </div>
                        </div>
                    </div>
                    <div class="customer-item">
                        <div class="customer-icon">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div class="customer-content">
                            <div class="customer-label">Địa chỉ giao hàng</div>
                            <div class="customer-value">
                                {{ $order->customer_address }}
                            </div>
                        </div>
                    </div>
                    @if (filled($order->note ?? null))
                        <div class="customer-item">
                            <div class="customer-icon">
                                <i class="fa-regular fa-note-sticky"></i>
                            </div>
                            <div class="customer-content">
                                <div class="customer-label">Ghi chú đơn hàng</div>
                                <div class="customer-value customer-note">{{ $order->note }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Chi tiết thanh toán --}}
            <section class="detail-card">
                <div class="section-heading">
                    <h3 class="section-title">
                        <i class="fa-solid fa-wallet"></i>
                        Chi tiết thanh toán
                    </h3>
                </div>
                <div class="payment-summary">
                    <div class="payment-row">
                        <span>Tạm tính</span>
                        <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                    </div>
                    <div class="payment-row">
                        <span>Phí giao hàng</span>
                        <strong>{{ number_format($shippingFee, 0, ',', '.') }}đ</strong>
                    </div>
                    <div class="payment-row discount-row">
                        <span>Giảm giá</span>
                        <strong>
                            {{ $discountAmount > 0 ? '-' : '' }}{{ number_format($discountAmount, 0, ',', '.') }}đ
                        </strong>
                    </div>
                    <div class="payment-total">
                        <span>Tổng thanh toán</span>
                        <strong>{{ number_format($totalPrice, 0, ',', '.') }}đ</strong>
                    </div>
                </div>

                {{-- Phương thức thanh toán --}}
                @if (in_array((string) ($order->method ?? ''), ['1', '2'], true))
                    <div class="payment-method">
                        <div class="payment-method-icon">
                            @if ((string) $order->method === '1')
                                <i class="fa-solid fa-money-bill-wave"></i>
                            @else
                                <i class="fa-solid fa-building-columns"></i>
                            @endif
                        </div>
                        <div>
                            <div class="payment-method-label">
                                Phương thức thanh toán
                            </div>
                            <div class="payment-method-value">
                                {{ (string) $order->method === '1' ? 'Tiền mặt khi nhận hàng' : 'Chuyển khoản ngân hàng' }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Hủy đơn hàng --}}
                @if ($order->canBeCancelled())
                    <div class="cancel-section">
                        <p class="cancel-description">
                            Bạn có thể hủy đơn hàng khi đơn vẫn đủ điều kiện hủy.
                        </p>
                        <form id="cancelOrderForm"
                              method="POST"
                              action="{{ route('customer.orders.cancel', $order) }}">
                            @csrf
                            <button type="button"
                                    class="cancel-button"
                                    id="openCancelModal">
                                <i class="fa-solid fa-xmark"></i>
                                Hủy đơn hàng
                            </button>
                        </form>
                    </div>
                @endif
            </section>
        </div>
    </div>

    <a href="{{ route('customer.orders.index') }}" class="back-button">
        <i class="fa-solid fa-arrow-left"></i>
        Quay lại lịch sử đơn hàng
    </a>

    {{-- ==================== HỘP THOẠI HỦY ĐƠN ==================== --}}
    @if ($order->canBeCancelled())
        <div class="cancel-modal"
             id="cancelOrderModal"
             role="dialog"
             aria-modal="true"
             aria-labelledby="cancelModalTitle"
             hidden>
            <div class="modal-content">
                <div class="modal-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="modal-title" id="cancelModalTitle">
                    Hủy đơn hàng?
                </h3>
                <p class="modal-description">
                    Bạn có chắc chắn muốn hủy đơn hàng
                    <strong>#{{ $orderCode }}</strong> không?
                    Đơn hàng đã hủy sẽ không thể tiếp tục giao.
                </p>
                <div class="modal-actions">
                    <button type="button"
                            class="modal-button"
                            id="closeCancelModal">
                        Giữ đơn hàng
                    </button>
                    <button type="button"
                            class="modal-button confirm"
                            id="confirmCancelOrder">
                        Xác nhận hủy
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cancelForm = document.getElementById('cancelOrderForm');
        const cancelModal = document.getElementById('cancelOrderModal');
        const openCancelButton = document.getElementById('openCancelModal');
        const closeCancelButton = document.getElementById('closeCancelModal');
        const confirmCancelButton = document.getElementById('confirmCancelOrder');
        let isCancelling = false;

        // Mở hộp thoại xác nhận
        function openModal() {
            if (!cancelModal || isCancelling) return;
            cancelModal.hidden = false;
            closeCancelButton?.focus();
        }

        // Đóng hộp thoại xác nhận
        function closeModal() {
            if (!cancelModal || isCancelling) return;
            cancelModal.hidden = true;
            openCancelButton?.focus();
        }

        openCancelButton?.addEventListener('click', openModal);
        closeCancelButton?.addEventListener('click', closeModal);
        cancelModal?.addEventListener('click', function (event) {
            if (event.target === cancelModal) {
                closeModal();
            }
        });

        // Đóng bằng phím Escape và giới hạn focus trong hộp thoại
        document.addEventListener('keydown', function (event) {
            if (!cancelModal || cancelModal.hidden) return;
            if (event.key === 'Escape') {
                closeModal();
            }
            if (event.key === 'Tab') {
                const buttons = [closeCancelButton, confirmCancelButton].filter(button => button && !button.disabled);
                if (!buttons.length) return;
                const first = buttons[0];
                const last = buttons[buttons.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });

        // Xác nhận hủy đơn hàng
        confirmCancelButton?.addEventListener('click', function () {
            if (!cancelForm || isCancelling) return;
            cancelForm.requestSubmit();
        });

        cancelForm?.addEventListener('submit', function (event) {
            if (isCancelling) {
                event.preventDefault();
                return;
            }
            isCancelling = true;
            if (confirmCancelButton) {
                confirmCancelButton.disabled = true;
                confirmCancelButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang hủy...';
            }
            if (closeCancelButton) {
                closeCancelButton.disabled = true;
            }
            if (openCancelButton) {
                openCancelButton.disabled = true;
            }
        });

        // Khôi phục giao diện khi quay lại trang
        window.addEventListener('pageshow', function (event) {
            if (!event.persisted) return;
            isCancelling = false;
            if (cancelModal) cancelModal.hidden = true;
            if (confirmCancelButton) {
                confirmCancelButton.disabled = false;
                confirmCancelButton.textContent = 'Xác nhận hủy';
            }
            if (closeCancelButton) closeCancelButton.disabled = false;
            if (openCancelButton) openCancelButton.disabled = false;
        });
    });
</script>

{{-- ==================== TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI ==================== --}}
@if ($isTracking)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusElement = document.getElementById('orderStatus');
        const statusLabel = document.getElementById('orderStatusLabel');
        const trackingMessage = document.getElementById('trackingMessage');
        const statusUrl = @json(route('customer.orders.status', $order));
        let isChecking = false;
        let trackingTimer;

        // Kiểm tra trạng thái đơn hàng
        async function checkOrderStatus() {
            if (!statusElement || isChecking || document.hidden) return;
            isChecking = true;
            try {
                const response = await fetch(statusUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                });
                if (!response.ok) {
                    throw new Error('Không thể cập nhật trạng thái');
                }
                const data = await response.json();
                if (data.status === undefined || data.status === null) return;
                const currentStatus = String(statusElement.dataset.status);
                const newStatus = String(data.status);

                // Tải lại khi trạng thái thay đổi
                if (newStatus !== currentStatus) {
                    clearInterval(trackingTimer);
                    window.location.reload();
                    return;
                }

                // Cập nhật nội dung trạng thái
                if (statusLabel && data.statusLabel) {
                    statusLabel.textContent = data.statusLabel;
                }
                if (trackingMessage) {
                    trackingMessage.textContent = 'Trạng thái đơn hàng được tự động cập nhật mỗi 10 giây.';
                }
            } catch (error) {
                console.error('Order status error:', error);
                if (trackingMessage) {
                    trackingMessage.textContent = 'Tạm thời chưa thể cập nhật trạng thái. Hệ thống sẽ thử lại.';
                }
            } finally {
                isChecking = false;
            }
        }

        // Kiểm tra sau mỗi 10 giây
        trackingTimer = setInterval(checkOrderStatus, 10000);

        // Kiểm tra ngay khi quay lại tab
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                checkOrderStatus();
            }
        });
    });
</script>
@endif

@endsection
