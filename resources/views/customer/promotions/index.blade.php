
@extends('layouts.app')
@section('content')

@php
    $title = 'Kho Voucher';
    $totalPromotions = $promotions->count();
    $totalSaved = collect($savedVoucherCodes)->count();
@endphp

<style>
    /* ==================== LAYOUT CHUNG ==================== */
    .voucher-page {
        --primary: #e74c3c;
        --primary-dark: #c0392b;
        --text: #292929;
        --muted: #888;
        --border: #eae5e2;
        width: 100%;
        max-width: 1180px;
        min-width: 0;
        margin: 20px auto;
        padding: 0 20px 25px;
        color: var(--text);
        box-sizing: border-box;
        container-type: inline-size;
    }
    .voucher-page * {
        box-sizing: border-box;
    }
    .voucher-page button,
    .voucher-page input {
        font-family: inherit;
    }
    .voucher-page form {
        margin: 0;
    }

    /* ==================== BANNER ƯU ĐÃI ==================== */
    .voucher-page .promotion-banner {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 175px;
        padding: 28px 35px;
        background: linear-gradient(115deg, #e74c3c, #f36d3d);
        color: #fff;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 26px;
    }
    .voucher-page .banner-content {
        position: relative;
        z-index: 2;
        max-width: 75%;
    }
    .voucher-page .banner-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1.5px;
        color: #ffe4d7;
        margin-bottom: 12px;
    }
    .voucher-page .banner-title {
        font-size: 30px;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 10px;
        color: #fff;
    }
    .voucher-page .banner-description {
        color: #fff0ea;
        font-size: 13px;
        line-height: 1.7;
        margin: 0;
    }
    .voucher-page .banner-decoration {
        position: absolute;
        right: 40px;
        top: 50%;
        transform: translateY(-50%) rotate(-15deg);
        font-size: 100px;
        color: rgba(255, 255, 255, .9);
        z-index: 1;
    }
    .voucher-page .promotion-banner::before {
        content: '';
        position: absolute;
        width: 270px;
        height: 270px;
        border: 30px solid rgba(255, 255, 255, .07);
        border-radius: 50%;
        right: -50px;
        top: -90px;
    }
    .voucher-page .promotion-banner::after {
        content: '';
        position: absolute;
        width: 170px;
        height: 170px;
        background: rgba(255, 255, 255, .06);
        border-radius: 50%;
        right: 170px;
        bottom: -120px;
    }

    /* ==================== TIÊU ĐỀ ==================== */
    .voucher-page .voucher-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }
    .voucher-page .page-title {
        font-size: 25px;
        font-weight: 750;
        color: var(--primary);
        margin: 0 0 5px;
    }
    .voucher-page .page-description {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }
    .voucher-page .saved-wallet {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px 14px;
        border: 1px solid #ffd9c8;
        border-radius: 8px;
        background: #fff;
        color: var(--primary);
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }
    .voucher-page .saved-wallet:hover {
        background: #fff4ee;
    }
    .voucher-page .wallet-count {
        min-width: 23px;
        padding: 3px 6px;
        border-radius: 5px;
        background: #fff0eb;
        color: var(--primary);
        font-size: 12px;
        text-align: center;
    }

    /* ==================== BỘ LỌC ==================== */
    .voucher-page .voucher-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .voucher-page .voucher-tabs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }
    .voucher-page .voucher-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 14px;
        background: #fff;
        color: #666;
        border: 1px solid #e5e5e5;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: .2s;
    }
    .voucher-page .voucher-tab:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    .voucher-page .voucher-tab.active {
        color: #fff;
        background: var(--primary);
        border-color: var(--primary);
    }
    .voucher-page .voucher-search {
        position: relative;
        width: 210px;
        max-width: 100%;
    }
    .voucher-page .voucher-search i {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        font-size: 13px;
        color: #999;
    }
    .voucher-page .voucher-search input {
        width: 100%;
        padding: 10px 12px 10px 34px;
        font-size: 12px;
        border: 1px solid #e3e3e3;
        border-radius: 7px;
        background: #fff;
        outline: none;
    }
    .voucher-page .voucher-search input:focus {
        border-color: var(--primary);
    }

    /* ==================== DANH SÁCH VOUCHER ==================== */
    .voucher-page .voucher-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
        align-items: stretch;
    }
    .voucher-page .voucher-card {
        --voucher-color: #e74c3c;
        --voucher-bg: #fff0eb;
        position: relative;
        display: flex;
        align-items: stretch;
        min-width: 0;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .025);
        transition: box-shadow .2s, transform .2s;
    }
    .voucher-page .voucher-card:not(.unavailable):hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, .06);
    }
    .voucher-page .voucher-card.shipping {
        --voucher-color: #d98221;
        --voucher-bg: #fff3e0;
    }
    .voucher-page .voucher-card.special {
        --voucher-color: #8058a5;
        --voucher-bg: #f5effa;
    }
    .voucher-page .voucher-card.unavailable {
        --voucher-color: #999;
        --voucher-bg: #f2f2f2;
    }
    .voucher-page .voucher-card[hidden] {
        display: none !important;
    }

    /* ==================== PHẦN GIÁ TRỊ VOUCHER ==================== */
    .voucher-page .voucher-ticket {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 115px;
        flex-shrink: 0;
        padding: 17px 8px;
        background: var(--voucher-bg);
        color: var(--voucher-color);
        border-right: 1px dashed #dcd3ce;
        text-align: center;
    }
    .voucher-page .voucher-ticket::before,
    .voucher-page .voucher-ticket::after {
        content: '';
        position: absolute;
        right: -8px;
        width: 15px;
        height: 15px;
        background: #f7f7f7;
        border-radius: 50%;
        z-index: 2;
    }
    .voucher-page .voucher-ticket::before {
        top: -8px;
    }
    .voucher-page .voucher-ticket::after {
        bottom: -8px;
    }
    .voucher-page .ticket-icon {
        font-size: 30px;
        margin-bottom: 12px;
    }
    .voucher-page .ticket-value {
        font-size: 27px;
        font-weight: 800;
        line-height: 1.1;
        overflow-wrap: anywhere;
    }
    .voucher-page .ticket-type {
        margin-top: 8px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
    }

    /* ==================== THÔNG TIN VOUCHER ==================== */
    .voucher-page .voucher-content {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
        padding: 15px 16px;
    }
    .voucher-page .voucher-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 9px;
    }
    .voucher-page .voucher-code {
        color: var(--voucher-color);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .5px;
        overflow-wrap: anywhere;
    }
    .voucher-page .voucher-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 600;
        color: #888;
        background: #f4f4f4;
        border-radius: 4px;
        padding: 4px 6px;
    }
    .voucher-page .voucher-status.saved {
        background: #eaf7ef;
        color: #198754;
    }
    .voucher-page .voucher-status.limited {
        background: #fff0eb;
        color: var(--primary);
    }
    .voucher-page .voucher-name {
        font-size: 15px;
        font-weight: 700;
        line-height: 1.4;
        color: #292929;
        margin: 0 0 6px;
        overflow-wrap: anywhere;
    }
    .voucher-page .voucher-description {
        color: #555;
        font-size: 12px;
        line-height: 1.5;
        margin: 0 0 8px;
    }
    .voucher-page .voucher-meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
        font-size: 11px;
        line-height: 1.5;
        color: #888;
        margin-bottom: 12px;
    }
    .voucher-page .voucher-meta i {
        width: 14px;
        color: #aaa;
        margin-right: 3px;
    }

    /* ==================== SỐ LƯỢT SỬ DỤNG ==================== */
    .voucher-page .voucher-usage {
        margin-top: auto;
    }
    .voucher-page .usage-track {
        width: 100%;
        height: 5px;
        background: #f0eeec;
        border-radius: 10px;
        overflow: hidden;
    }
    .voucher-page .usage-fill {
        height: 100%;
        background: var(--voucher-color);
        border-radius: 10px;
    }
    .voucher-page .usage-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 5px;
        margin-top: 6px;
        color: #999;
        font-size: 10px;
    }
    .voucher-page .usage-info .limited {
        color: var(--primary);
    }

    /* ==================== NÚT LƯU VOUCHER ==================== */
    .voucher-page .voucher-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
    }
    .voucher-page .copy-code {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #777;
        background: transparent;
        border: none;
        padding: 4px 0;
        font-size: 11px;
        cursor: pointer;
    }
    .voucher-page .copy-code:hover {
        color: var(--primary);
    }
    .voucher-page .voucher-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-width: 85px;
        padding: 8px 12px;
        border: 1px solid var(--primary);
        border-radius: 6px;
        background: var(--primary);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        cursor: pointer;
        transition: .2s;
        text-decoration: none;
    }
    .voucher-page .voucher-button:hover {
        border-color: var(--primary-dark);
        background: var(--primary-dark);
    }
    .voucher-page .voucher-button:disabled {
        background: #f0f0f0;
        border-color: #f0f0f0;
        color: #999;
        cursor: not-allowed;
    }
    .voucher-page .voucher-button.saved {
        color: #198754;
        background: #eaf7ef;
        border-color: #eaf7ef;
    }

    /* ==================== THÔNG BÁO ==================== */
    .voucher-page .voucher-message {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 16px;
    }
    .voucher-page .voucher-message.success {
        background: #eafaf1;
        color: #196f3d;
        border: 1px solid #d0ead9;
    }
    .voucher-page .voucher-message.error {
        background: #fff0ed;
        color: #c0392b;
        border: 1px solid #f4d0c9;
    }

    /* ==================== KHÔNG CÓ VOUCHER ==================== */
    .voucher-page .voucher-empty {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        text-align: center;
        padding: 50px 20px;
    }
    .voucher-page .voucher-empty[hidden] {
        display: none;
    }
    .voucher-page .empty-icon {
        font-size: 50px;
        color: #e9b7a9;
        margin-bottom: 15px;
    }
    .voucher-page .voucher-empty h3 {
        font-size: 18px;
        margin: 0 0 8px;
    }
    .voucher-page .voucher-empty p {
        font-size: 13px;
        color: #999;
        margin: 0 0 18px;
    }
    .voucher-page .reset-filter {
        padding: 9px 15px;
        background: #fff4ed;
        border: 1px solid #ffd5c4;
        color: var(--primary);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    /* ==================== HƯỚNG DẪN SỬ DỤNG ==================== */
    .voucher-page .voucher-guide {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        margin-top: 22px;
        border: 1px solid #eae5e2;
        border-radius: 8px;
        background: #fff;
    }
    .voucher-page .guide-icon {
        color: var(--primary);
        font-size: 18px;
        margin-top: 2px;
    }
    .voucher-page .guide-content h4 {
        font-size: 13px;
        font-weight: 700;
        margin: 0 0 5px;
    }
    .voucher-page .guide-content p {
        color: #888;
        font-size: 12px;
        line-height: 1.7;
        margin: 0;
    }

    /* ==================== TOAST SAO CHÉP ==================== */
    .voucher-page .voucher-toast {
        position: fixed;
        right: 25px;
        bottom: 25px;
        z-index: 9999;
        padding: 13px 20px;
        border-radius: 8px;
        background: #292929;
        color: #fff;
        font-size: 13px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: .25s;
    }
    .voucher-page .voucher-toast.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* ==================== RESPONSIVE ==================== */
    @container (min-width: 1250px) {
        .voucher-page .voucher-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    @container (max-width: 760px) {
        .voucher-page .voucher-grid {
            grid-template-columns: 1fr;
        }
        .voucher-page .voucher-search {
            width: 100%;
        }
    }
    @container (max-width: 480px) {
        .voucher-page .promotion-banner {
            min-height: 150px;
            padding: 20px;
        }
        .voucher-page .banner-title {
            font-size: 22px;
        }
        .voucher-page .banner-decoration {
            font-size: 65px;
            right: 15px;
            opacity: .5;
        }
        .voucher-page .banner-content {
            max-width: 85%;
        }
        .voucher-page .voucher-ticket {
            width: 95px;
        }
        .voucher-page .ticket-value {
            font-size: 23px;
        }
        .voucher-page .voucher-content {
            padding: 13px 11px;
        }
        .voucher-page .voucher-tab {
            padding: 8px 10px;
            font-size: 11px;
        }
        .voucher-page .page-title {
            font-size: 22px;
        }
    }
    @media (max-width: 600px) {
        .voucher-page {
            margin: 15px auto;
            padding: 0 12px 20px;
        }
    }
</style>

<main class="voucher-page">
    {{-- ==================== BANNER ==================== --}}
    <section class="promotion-banner">
        <div class="banner-content">
            <div class="banner-label">
                FASTFOOD STORE · ƯU ĐÃI DÀNH CHO BẠN
            </div>
            <h1 class="banner-title">
                Ăn ngon, tiết kiệm hơn!
            </h1>
            <p class="banner-description">
                Khám phá những ưu đãi hấp dẫn và lưu voucher cho đơn hàng tiếp theo của bạn.
            </p>
        </div>
        <div class="banner-decoration" aria-hidden="true">
            <i class="fa-solid fa-ticket"></i>
        </div>
    </section>

    {{-- ==================== TIÊU ĐỀ ==================== --}}
    <div class="voucher-heading">
        <div>
            <h2 class="page-title">KHO VOUCHER</h2>
            <p class="page-description">
                Khám phá {{ $totalPromotions }} ưu đãi trong kho voucher
            </p>
        </div>
        <button type="button" class="saved-wallet" id="savedVoucherButton">
            <i class="fa-solid fa-wallet"></i>
            Voucher của tôi
            <span class="wallet-count">{{ $totalSaved }}</span>
        </button>
    </div>

    {{-- ==================== THÔNG BÁO ==================== --}}
    @if (session('success'))
        <div class="voucher-message success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="voucher-message error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="voucher-message error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ==================== BỘ LỌC VOUCHER ==================== --}}
    <div class="voucher-toolbar">
        <div class="voucher-tabs">
            <button type="button"
                    class="voucher-tab active"
                    data-filter="all"
                    aria-pressed="true">
                Tất cả
            </button>
            <button type="button"
                    class="voucher-tab"
                    data-filter="discount"
                    aria-pressed="false">
                <i class="fa-solid fa-tag"></i>
                Giảm giá
            </button>
            <button type="button"
                    class="voucher-tab"
                    data-filter="shipping"
                    aria-pressed="false">
                <i class="fa-solid fa-truck"></i>
                Phí giao hàng
            </button>
            <button type="button"
                    class="voucher-tab"
                    data-filter="saved"
                    aria-pressed="false">
                <i class="fa-solid fa-bookmark"></i>
                Đã lưu
            </button>
            <button type="button"
                    class="voucher-tab"
                    data-filter="available"
                    aria-pressed="false">
                Còn khả dụng
            </button>
        </div>
        <div class="voucher-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="search"
                   id="voucherSearch"
                   aria-label="Tìm kiếm voucher"
                   placeholder="Tìm mã ưu đãi...">
        </div>
    </div>

    {{-- ==================== DANH SÁCH VOUCHER ==================== --}}
    @if ($promotions->isEmpty())
        <div class="voucher-empty">
            <div class="empty-icon">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h3>Chưa có voucher</h3>
            <p>Hãy quay lại sau để khám phá những ưu đãi mới nhất.</p>
        </div>
    @else
        <div class="voucher-grid" id="voucherGrid">
            @foreach ($promotions as $promotion)
                @php
                    $isExpired = $promotion->isExpired();
                    $isSoldOut = $promotion->isSoldOut();
                    $isAvailable = $promotion->isAvailable();
                    $isUsed = in_array((string) $promotion->id_promotion, array_map('strval', $usedPromotionIds), true);
                    $isSaved = in_array($promotion->code, $savedVoucherCodes, true);
                    $isShipping = $promotion->type === \App\Models\Promotion::TYPE_SHIPPING;
                    $isPercent = $promotion->type === \App\Models\Promotion::TYPE_PERCENT;
                    $isFixed = $promotion->type === \App\Models\Promotion::TYPE_FIXED;
                    $canUse = $isAvailable && !$isExpired && !$isSoldOut && !$isUsed;
                    $remaining = max(0, $promotion->quantity - $promotion->used);
                    $usedPercent = $promotion->quantity > 0
                        ? min(100, max(0, $promotion->used / $promotion->quantity * 100))
                        : 0;
                    $isLimited = $remaining > 0 && $usedPercent >= 80;
                    $category = $isShipping
                        ? 'shipping'
                        : (($isPercent || $isFixed) ? 'discount' : 'special');

                    // Định dạng giá trị voucher
                    if ($isPercent) {
                        $ticketValue = rtrim(rtrim(number_format($promotion->value, 2, '.', ''), '0'), '.') . '%';
                        $ticketType = 'GIẢM GIÁ';
                        $ticketIcon = 'fa-percent';
                        $description = 'Giảm ' . $ticketValue . ' giá trị đơn hàng';
                    } elseif ($isFixed || $isShipping) {
                        $ticketValue = $promotion->value >= 1000 && $promotion->value % 1000 == 0
                            ? number_format($promotion->value / 1000, 0, ',', '.') . 'K'
                            : number_format($promotion->value, 0, ',', '.') . 'đ';
                        $ticketType = $isShipping ? 'PHÍ SHIP' : 'GIẢM GIÁ';
                        $ticketIcon = $isShipping ? 'fa-truck-fast' : 'fa-tag';
                        $description = $isShipping
                            ? 'Giảm phí giao hàng ' . number_format($promotion->value, 0, ',', '.') . 'đ'
                            : 'Giảm ' . number_format($promotion->value, 0, ',', '.') . 'đ cho đơn hàng';
                    } else {
                        $ticketValue = 'GIFT';
                        $ticketType = 'ĐẶC BIỆT';
                        $ticketIcon = 'fa-gift';
                        $description = 'Ưu đãi đặc biệt dành cho khách hàng';
                    }

                    // Xác định trạng thái voucher
                    $statusText = '';
                    $statusClass = '';
                    if ($isSoldOut) {
                        $statusText = 'Đã hết lượt';
                    } elseif ($isExpired) {
                        $statusText = 'Hết hạn';
                    } elseif (!$promotion->status) {
                        $statusText = 'Ngừng áp dụng';
                    } elseif ($promotion->start_date->isFuture()) {
                        $statusText = 'Sắp diễn ra';
                    } elseif ($isUsed) {
                        $statusText = 'Đã sử dụng';
                    } elseif (!$isAvailable) {
                        $statusText = 'Chưa khả dụng';
                    } elseif ($isSaved) {
                        $statusText = 'Đã lưu';
                        $statusClass = 'saved';
                    } elseif ($isLimited) {
                        $statusText = 'Sắp hết';
                        $statusClass = 'limited';
                    }
                @endphp

                <article class="voucher-card {{ $category }} {{ !$canUse ? 'unavailable' : '' }}"
                         data-category="{{ $category }}"
                         data-saved="{{ $isSaved ? '1' : '0' }}"
                         data-available="{{ $canUse ? '1' : '0' }}"
                         data-search="{{ $promotion->code }} {{ $promotion->name }} {{ $description }}">

                    {{-- Giá trị voucher --}}
                    <div class="voucher-ticket">
                        <div class="ticket-icon">
                            <i class="fa-solid {{ $ticketIcon }}"></i>
                        </div>
                        <div class="ticket-value">
                            {{ $ticketValue }}
                        </div>
                        <div class="ticket-type">
                            {{ $ticketType }}
                        </div>
                    </div>

                    {{-- Thông tin voucher --}}
                    <div class="voucher-content">
                        <div class="voucher-card-header">
                            <span class="voucher-code">
                                {{ $promotion->code }}
                            </span>
                            @if ($statusText !== '')
                                <span class="voucher-status {{ $statusClass }}">
                                    @if ($statusClass === 'saved')
                                        <i class="fa-solid fa-check"></i>
                                    @elseif ($statusClass === 'limited')
                                        <i class="fa-solid fa-fire"></i>
                                    @endif
                                    {{ $statusText }}
                                </span>
                            @endif
                        </div>
                        <h3 class="voucher-name">
                            {{ $promotion->name }}
                        </h3>
                        <p class="voucher-description">
                            {{ $description }}
                        </p>
                        <div class="voucher-meta">
                            <div>
                                <i class="fa-solid fa-bag-shopping"></i>
                                Đơn từ {{ number_format($promotion->min_order_value, 0, ',', '.') }}đ
                            </div>
                            <div>
                                <i class="fa-regular fa-clock"></i>
                                HSD: {{ $promotion->end_date->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        {{-- Số lượt sử dụng --}}
                        <div class="voucher-usage">
                            <div class="usage-track">
                                <div class="usage-fill" style="width: {{ $usedPercent }}%;"></div>
                            </div>
                            <div class="usage-info">
                                <span>Đã dùng {{ round($usedPercent) }}%</span>
                                <span class="{{ $isLimited ? 'limited' : '' }}">
                                    Còn {{ $remaining }} lượt
                                </span>
                            </div>
                        </div>

                        {{-- Các nút thao tác --}}
                        <div class="voucher-card-footer">
                            <button type="button"
                                    class="copy-code"
                                    data-code="{{ $promotion->code }}"
                                    title="Sao chép mã voucher">
                                <i class="fa-regular fa-copy"></i>
                                Sao chép mã
                            </button>
                            @if ($isSoldOut)
                                <button class="voucher-button" type="button" disabled>
                                    Đã hết
                                </button>
                            @elseif ($isExpired)
                                <button class="voucher-button" type="button" disabled>
                                    Hết hạn
                                </button>
                            @elseif (!$isAvailable)
                                <button class="voucher-button" type="button" disabled>
                                    Chưa khả dụng
                                </button>
                            @elseif ($isUsed)
                                <button class="voucher-button" type="button" disabled>
                                    Đã sử dụng
                                </button>
                            @elseif ($isSaved)
                                <button class="voucher-button saved" type="button" disabled>
                                    <i class="fa-solid fa-check"></i>
                                    Đã lưu
                                </button>
                            @else
                                <form method="POST" action="{{ route('promotions.save', $promotion) }}">
                                    @csrf
                                    <button class="voucher-button" type="submit">
                                        <i class="fa-solid fa-plus"></i>
                                        Lưu ngay
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Không tìm thấy kết quả --}}
        <div class="voucher-empty" id="voucherEmpty" hidden>
            <div class="empty-icon">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h3>Không tìm thấy voucher</h3>
            <p>Không có voucher phù hợp với lựa chọn hiện tại.</p>
            <button type="button" class="reset-filter" id="resetVoucherFilter">
                Xem tất cả voucher
            </button>
        </div>
    @endif

    {{-- ==================== THÔNG BÁO SAO CHÉP ==================== --}}
    <div class="voucher-toast" id="voucherToast" role="status" aria-live="polite"></div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.voucher-page .voucher-tab');
        const cards = document.querySelectorAll('.voucher-page .voucher-card');
        const searchInput = document.getElementById('voucherSearch');
        const savedButton = document.getElementById('savedVoucherButton');
        const emptyState = document.getElementById('voucherEmpty');
        const resetButton = document.getElementById('resetVoucherFilter');
        const toast = document.getElementById('voucherToast');
        let currentFilter = 'all';
        let toastTimer;

        // Chuẩn hóa nội dung tìm kiếm tiếng Việt
        function normalizeText(text) {
            return String(text || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .trim();
        }

        // Lọc voucher theo danh mục và từ khóa
        function filterVouchers() {
            const keyword = normalizeText(searchInput?.value);
            let visibleCount = 0;
            cards.forEach(card => {
                let matchesFilter = false;
                switch (currentFilter) {
                    case 'all':
                        matchesFilter = true;
                        break;
                    case 'saved':
                        matchesFilter = card.dataset.saved === '1';
                        break;
                    case 'available':
                        matchesFilter = card.dataset.available === '1';
                        break;
                    default:
                        matchesFilter = card.dataset.category === currentFilter;
                        break;
                }
                const matchesSearch = normalizeText(card.dataset.search).includes(keyword);
                const isVisible = matchesFilter && matchesSearch;
                card.hidden = !isVisible;
                if (isVisible) {
                    visibleCount++;
                }
            });
            if (emptyState) {
                emptyState.hidden = visibleCount !== 0;
            }
        }

        // Thay đổi bộ lọc
        function setFilter(filter) {
            currentFilter = filter;
            tabs.forEach(tab => {
                const isActive = tab.dataset.filter === filter;
                tab.classList.toggle('active', isActive);
                tab.setAttribute('aria-pressed', String(isActive));
            });
            filterVouchers();
        }

        // Sự kiện bấm bộ lọc
        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                setFilter(this.dataset.filter);
            });
        });

        // Tìm kiếm voucher
        searchInput?.addEventListener('input', filterVouchers);

        // Xem voucher đã lưu
        savedButton?.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
            }
            setFilter('saved');
        });

        // Đặt lại bộ lọc
        resetButton?.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
            }
            setFilter('all');
        });

        // Hiển thị thông báo
        function showVoucherToast(message) {
            if (!toast) return;
            clearTimeout(toastTimer);
            toast.textContent = message;
            toast.classList.add('show');
            toastTimer = setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }

        // Sao chép mã voucher
        document.querySelectorAll('.voucher-page .copy-code').forEach(button => {
            button.addEventListener('click', async function () {
                const code = this.dataset.code;
                try {
                    await navigator.clipboard.writeText(code);
                    showVoucherToast('Đã sao chép mã ' + code);
                } catch (error) {
                    showVoucherToast('Không thể sao chép. Mã voucher: ' + code);
                }
            });
        });

        // Hạn chế gửi yêu cầu lưu voucher nhiều lần
        document.querySelectorAll('.voucher-page .voucher-card form').forEach(form => {
            form.addEventListener('submit', function () {
                const button = this.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu';
                }
            });
        });

        // Khôi phục nút khi quay lại trang bằng bộ nhớ trình duyệt
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                document.querySelectorAll('.voucher-page .voucher-card form button[type="submit"]').forEach(button => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fa-solid fa-plus"></i> Lưu ngay';
                });
            }
        });
    });
</script>

@endsection
