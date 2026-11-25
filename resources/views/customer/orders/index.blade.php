
@extends('layouts.app')
@section('content')

@php
    $title = 'Lịch sử đơn hàng';
    $currentOrders = $orders->getCollection();
    $totalOrders = $orders->total();
    $statusCounts = $currentOrders->countBy(fn($order) => $order->statusLabel());
@endphp

<style>
    /* ==================== LAYOUT CHUNG ==================== */
    .history-page {
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
    .history-page * {
        box-sizing: border-box;
    }
    .history-page button,
    .history-page input,
    .history-page select {
        font-family: inherit;
    }
    .history-page [hidden] {
        display: none !important;
    }

    /* ==================== BREADCRUMB ==================== */
    .history-page .history-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
        color: #999;
        font-size: 12px;
        margin-bottom: 18px;
    }
    .history-page .history-breadcrumb a {
        color: #777;
        text-decoration: none;
    }
    .history-page .history-breadcrumb a:hover {
        color: var(--primary);
    }
    .history-page .history-breadcrumb i {
        font-size: 9px;
    }
    .history-page .history-breadcrumb span {
        color: var(--primary);
        font-weight: 600;
    }

    /* ==================== TIÊU ĐỀ ==================== */
    .history-page .history-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 23px;
    }
    .history-page .history-title {
        color: var(--primary);
        font-size: 27px;
        font-weight: bold;
        margin: 0 0 7px;
    }
    .history-page .history-description {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
        margin: 0;
    }
    .history-page .heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 55px;
        height: 55px;
        background: #fff0e8;
        border-radius: 12px;
        color: var(--primary);
        font-size: 25px;
        flex-shrink: 0;
    }

    /* ==================== THỐNG KÊ ==================== */
    .history-page .statistics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .history-page .statistic-card {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
        padding: 17px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .025);
    }
    .history-page .statistic-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        flex-shrink: 0;
        border-radius: 9px;
        font-size: 19px;
    }
    .history-page .statistic-icon.total {
        color: #e74c3c;
        background: #fff0e8;
    }
    .history-page .statistic-icon.status {
        color: #d98221;
        background: #fff4e3;
    }
    .history-page .statistic-content {
        flex: 1;
        min-width: 0;
    }
    .history-page .statistic-value {
        font-size: 25px;
        font-weight: 800;
        line-height: 1.2;
        color: #292929;
    }
    .history-page .statistic-label {
        margin-top: 5px;
        font-size: 12px;
        color: #888;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    /* ==================== KHUNG ĐƠN HÀNG ==================== */
    .history-page .history-panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, .025);
    }
    .history-page .panel-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }
    .history-page .panel-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .history-page .panel-count {
        font-size: 12px;
        color: #999;
    }

    /* ==================== BỘ LỌC ==================== */
    .history-page .history-tabs {
        display: flex;
        align-items: center;
        gap: 5px;
        overflow-x: auto;
        scrollbar-width: thin;
        border-bottom: 1px solid #eee;
        margin-bottom: 17px;
    }
    .history-page .history-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        position: relative;
        padding: 12px 15px;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        color: #777;
        font-size: 13px;
        font-weight: 500;
        white-space: nowrap;
        cursor: pointer;
        transition: .2s;
    }
    .history-page .history-tab:hover {
        color: var(--primary);
        background: #fffaf7;
    }
    .history-page .history-tab.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        font-weight: 700;
    }
    .history-page .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 19px;
        padding: 2px 5px;
        border-radius: 5px;
        background: #f2f2f2;
        color: #888;
        font-size: 10px;
    }
    .history-page .history-tab.active .tab-count {
        color: var(--primary);
        background: #fff0e8;
    }

    /* ==================== TÌM KIẾM VÀ SẮP XẾP ==================== */
    .history-page .history-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }
    .history-page .history-search {
        position: relative;
        width: 100%;
        max-width: 340px;
    }
    .history-page .history-search i {
        position: absolute;
        top: 50%;
        left: 13px;
        transform: translateY(-50%);
        font-size: 13px;
        color: #aaa;
        pointer-events: none;
    }
    .history-page .history-search input {
        width: 100%;
        min-width: 0;
        padding: 11px 12px 11px 36px;
        border: 1px solid #e3e3e3;
        border-radius: 7px;
        background: #fafafa;
        color: #444;
        font-size: 13px;
        outline: none;
        transition: .2s;
    }
    .history-page .history-search input:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, .06);
    }
    .history-page .history-search input::placeholder {
        color: #aaa;
    }
    .history-page .history-sort {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-shrink: 0;
    }
    .history-page .history-sort i {
        color: #888;
        font-size: 13px;
    }
    .history-page .history-sort select {
        padding: 10px 12px;
        background: #fff;
        border: 1px solid #e3e3e3;
        border-radius: 7px;
        color: #555;
        font-size: 12px;
        outline: none;
        cursor: pointer;
    }
    .history-page .history-sort select:focus {
        border-color: var(--primary);
    }

    /* ==================== DANH SÁCH ĐƠN HÀNG ==================== */
    .history-page .order-list {
        display: flex;
        flex-direction: column;
        gap: 13px;
    }
    .history-page .order-card {
        min-width: 0;
        padding: 17px;
        border: 1px solid #e8e8e8;
        border-radius: 9px;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
    }
    .history-page .order-card:hover {
        border-color: #f1b8a6;
        box-shadow: 0 3px 12px rgba(231, 76, 60, .045);
    }
    .history-page .order-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .history-page .order-identification {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }
    .history-page .order-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 43px;
        height: 43px;
        border-radius: 9px;
        background: #fff2eb;
        color: var(--primary);
        font-size: 19px;
        flex-shrink: 0;
    }
    .history-page .order-heading {
        flex: 1;
        min-width: 0;
    }
    .history-page .order-code {
        font-size: 15px;
        font-weight: 700;
        color: #292929;
        line-height: 1.4;
        margin: 0 0 5px;
    }
    .history-page .order-date {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #999;
        font-size: 12px;
    }
    .history-page .order-date i {
        font-size: 11px;
    }

    /* ==================== TRẠNG THÁI ĐƠN HÀNG ==================== */
    .history-page .order-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        background: color-mix(in srgb, var(--status-color) 10%, white);
        color: var(--status-color);
        font-size: 11px;
        font-weight: 700;
        border-radius: 5px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .history-page .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }

    /* ==================== THÔNG TIN KHÁCH HÀNG ==================== */
    .history-page .order-information {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px 24px;
        padding: 15px 0;
    }
    .history-page .information-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #777;
        font-size: 12px;
        line-height: 1.5;
    }
    .history-page .information-item i {
        width: 14px;
        color: #aaa;
        text-align: center;
    }
    .history-page .information-item strong {
        font-weight: 600;
        color: #444;
    }

    /* ==================== TỔNG TIỀN VÀ THAO TÁC ==================== */
    .history-page .order-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px dashed #e5e5e5;
    }
    .history-page .order-total-label {
        color: #888;
        font-size: 12px;
        margin-bottom: 5px;
    }
    .history-page .order-total-value {
        color: var(--primary);
        font-size: 21px;
        font-weight: 800;
        line-height: 1.2;
        white-space: nowrap;
    }
    .history-page .order-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .history-page .detail-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid var(--primary);
        border-radius: 7px;
        background: #fff;
        color: var(--primary);
        padding: 10px 15px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: .2s;
    }
    .history-page .detail-button:hover {
        color: #fff;
        background: var(--primary);
    }
    .history-page .detail-button i {
        font-size: 10px;
    }

    /* ==================== THÔNG BÁO ==================== */
    .history-page .history-message {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 13px 16px;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.5;
        margin-bottom: 18px;
    }
    .history-page .history-message.success {
        background: #eafaf1;
        color: #196f3d;
        border: 1px solid #cdebd8;
    }
    .history-page .history-message.error {
        background: #fff0ed;
        color: #c0392b;
        border: 1px solid #f5d0c8;
    }

    /* ==================== TRẠNG THÁI TRỐNG ==================== */
    .history-page .empty-state {
        text-align: center;
        padding: 55px 20px;
    }
    .history-page .empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 85px;
        height: 85px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: #fff2e9;
        color: #eaa38b;
        font-size: 35px;
    }
    .history-page .empty-title {
        font-size: 19px;
        font-weight: 700;
        color: #333;
        margin: 0 0 9px;
    }
    .history-page .empty-description {
        font-size: 13px;
        color: #999;
        line-height: 1.7;
        margin: 0 0 20px;
    }
    .history-page .shopping-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 22px;
        border: none;
        border-radius: 7px;
        background: var(--primary);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .history-page .shopping-button:hover {
        background: var(--primary-dark);
    }

    /* ==================== PHÂN TRANG ==================== */
    .history-page .history-pagination {
        padding-top: 20px;
        margin-top: 20px;
        border-top: 1px solid #eee;
    }
    .history-page .pagination-info {
        text-align: center;
        color: #999;
        font-size: 12px;
        margin: 0 0 12px;
    }
    .history-page .pagination-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }
    .history-page .pagination-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        background: #fff;
        color: #666;
        font-size: 12px;
        text-decoration: none;
        transition: .2s;
    }
    .history-page .pagination-button:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    .history-page .pagination-button.active {
        color: #fff;
        border-color: var(--primary);
        background: var(--primary);
        font-weight: 700;
    }
    .history-page .pagination-button.disabled {
        color: #bbb;
        background: #f7f7f7;
        cursor: not-allowed;
    }

    /* ==================== RESPONSIVE ==================== */
    @container (max-width: 650px) {
        .history-page .statistics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .history-page .history-panel {
            padding: 15px;
        }
        .history-page .history-toolbar {
            flex-wrap: wrap;
        }
        .history-page .history-search {
            max-width: none;
        }
        .history-page .history-tab {
            padding: 11px 12px;
            font-size: 12px;
        }
    }
    @container (max-width: 400px) {
        .history-page .statistic-card {
            padding: 12px;
            gap: 9px;
        }
        .history-page .statistic-icon {
            width: 32px;
            height: 32px;
            font-size: 15px;
        }
        .history-page .statistic-value {
            font-size: 21px;
        }
        .history-page .order-card {
            padding: 13px;
        }
        .history-page .order-card-header {
            flex-wrap: wrap;
        }
        .history-page .order-total-value {
            font-size: 18px;
        }
        .history-page .detail-button {
            padding: 9px 11px;
        }
        .history-page .order-icon {
            width: 36px;
            height: 36px;
        }
    }
    @media (max-width: 600px) {
        .history-page {
            margin: 15px auto;
            padding: 0 12px 20px;
        }
        .history-page .history-title {
            font-size: 23px;
        }
    }
</style>

<div class="history-page">
    {{-- ==================== BREADCRUMB ==================== --}}
    <nav class="history-breadcrumb" aria-label="Đường dẫn trang">
        <a href="{{ url('/foods') }}">
            <i class="fa-solid fa-house"></i>
            Trang chủ
        </a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Lịch sử đơn hàng</span>
    </nav>

    {{-- ==================== TIÊU ĐỀ ==================== --}}
    <div class="history-heading">
        <div>
            <h1 class="history-title">LỊCH SỬ ĐƠN HÀNG</h1>
        </div>
    </div>

    {{-- ==================== THÔNG BÁO ==================== --}}
    @if (session('success'))
        <div class="history-message success" role="status">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="history-message error" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="history-message error" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ==================== DANH SÁCH ĐƠN HÀNG ==================== --}}
    <section class="history-panel">
        <div class="panel-heading">
            <h2 class="panel-title">Đơn hàng của tôi</h2>
            <span class="panel-count" id="visibleOrderCount">
                {{ $currentOrders->count() }} đơn trên trang này
            </span>
        </div>

        @if ($orders->isEmpty())
            {{-- ==================== CHƯA CÓ ĐƠN HÀNG ==================== --}}
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="empty-title">Bạn chưa có đơn hàng</h3>
                <p class="empty-description">
                    Hãy khám phá thực đơn và thưởng thức những món ăn yêu thích của bạn.
                </p>
                <a href="{{ url('/foods') }}" class="shopping-button">
                    <i class="fa-solid fa-utensils"></i>
                    Khám phá thực đơn
                </a>
            </div>
        @else
            {{-- ==================== BỘ LỌC TRẠNG THÁI ==================== --}}
            <div class="history-tabs">
                <button type="button"
                        class="history-tab active"
                        data-filter-all="true"
                        aria-pressed="true">
                    Tất cả
                    <span class="tab-count">{{ $currentOrders->count() }}</span>
                </button>
                @foreach ($statusCounts as $statusLabel => $count)
                    <button type="button"
                            class="history-tab"
                            data-status="{{ $statusLabel }}"
                            aria-pressed="false">
                        {{ $statusLabel }}
                        <span class="tab-count">{{ $count }}</span>
                    </button>
                @endforeach
            </div>

            {{-- ==================== TÌM KIẾM VÀ SẮP XẾP ==================== --}}
            <div class="history-toolbar">
                <div class="history-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search"
                           id="orderSearch"
                           placeholder="Tìm mã đơn hàng, tên người nhận..."
                           aria-label="Tìm kiếm đơn hàng">
                </div>
                <div class="history-sort">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                    <select id="orderSort" aria-label="Sắp xếp đơn hàng">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                        <option value="price_high">Giá cao đến thấp</option>
                        <option value="price_low">Giá thấp đến cao</option>
                    </select>
                </div>
            </div>

            {{-- ==================== CÁC ĐƠN HÀNG ==================== --}}
            <div class="order-list" id="orderList">
                @foreach ($orders as $order)
                    @php
                        $orderCode = 'DH' . str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT);
                        $statusLabel = $order->statusLabel();
                        $statusColor = $order->statusColor();
                        $orderTimestamp = $order->created_at?->timestamp ?? 0;
                    @endphp
                    <article class="order-card"
                             data-status="{{ $statusLabel }}"
                             data-price="{{ $order->total_price }}"
                             data-date="{{ $orderTimestamp }}"
                             data-search="{{ $orderCode }} {{ $order->customer_name }} {{ $order->created_at?->format('d/m/Y H:i') }}">

                        {{-- Thông tin đơn hàng --}}
                        <div class="order-card-header">
                            <div class="order-identification">
                                <div class="order-icon">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </div>
                                <div class="order-heading">
                                    <h3 class="order-code">
                                        Đơn hàng #{{ $orderCode }}
                                    </h3>
                                    <div class="order-date">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $order->created_at?->format('d/m/Y · H:i') ?? 'Không xác định' }}
                                    </div>
                                </div>
                            </div>
                            <span class="order-status" style="--status-color: {{ $statusColor }};">
                                <span class="status-dot"></span>
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Người nhận và số lượng món --}}
                        <div class="order-information">
                            <div class="information-item">
                                <i class="fa-regular fa-user"></i>
                                Người nhận:
                                <strong>{{ $order->customer_name }}</strong>
                            </div>
                            <div class="information-item">
                                <i class="fa-solid fa-utensils"></i>
                                <strong>{{ $order->details_count }}</strong>
                                loại món
                            </div>
                        </div>

                        {{-- Tổng tiền và nút xem chi tiết --}}
                        <div class="order-card-footer">
                            <div class="order-total">
                                <div class="order-total-label">
                                    Tổng thanh toán
                                </div>
                                <div class="order-total-value">
                                    {{ number_format($order->total_price, 0, ',', '.') }}đ
                                </div>
                            </div>
                            <div class="order-actions">
                                <a href="{{ route('customer.orders.show', $order) }}"
                                   class="detail-button">
                                    Xem chi tiết
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- ==================== KHÔNG TÌM THẤY ==================== --}}
            <div class="empty-state" id="noOrderResult" hidden>
                <div class="empty-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="empty-title">Không tìm thấy đơn hàng</h3>
                <p class="empty-description">
                    Không có đơn hàng phù hợp với bộ lọc hoặc từ khóa bạn nhập trên trang này.
                </p>
                <button type="button"
                        class="shopping-button"
                        id="resetOrderFilter">
                    <i class="fa-solid fa-rotate-left"></i>
                    Đặt lại bộ lọc
                </button>
            </div>

            {{-- ==================== PHÂN TRANG ==================== --}}
            @if ($orders->hasPages())
                <div class="history-pagination">
                    <p class="pagination-info">
                        Hiển thị {{ $orders->firstItem() }} - {{ $orders->lastItem() }}
                        trong tổng số {{ $orders->total() }} đơn hàng
                    </p>
                    <nav class="pagination-buttons" aria-label="Phân trang đơn hàng">
                        @if ($orders->onFirstPage())
                            <span class="pagination-button disabled">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}"
                               class="pagination-button"
                               aria-label="Trang trước">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        @endif

                        @php
                            $currentPage = $orders->currentPage();
                            $lastPage = $orders->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp
                        @if ($startPage > 1)
                            <a href="{{ $orders->url(1) }}" class="pagination-button">1</a>
                            @if ($startPage > 2)
                                <span class="pagination-button disabled">...</span>
                            @endif
                        @endif
                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page === $currentPage)
                                <span class="pagination-button active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $orders->url($page) }}" class="pagination-button">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor
                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="pagination-button disabled">...</span>
                            @endif
                            <a href="{{ $orders->url($lastPage) }}" class="pagination-button">
                                {{ $lastPage }}
                            </a>
                        @endif

                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}"
                               class="pagination-button"
                               aria-label="Trang sau">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="pagination-button disabled">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        @endif
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const historyPage = document.querySelector('.history-page');
        if (!historyPage) return;
        const tabs = historyPage.querySelectorAll('.history-tab');
        const orderList = document.getElementById('orderList');
        const searchInput = document.getElementById('orderSearch');
        const sortSelect = document.getElementById('orderSort');
        const emptyState = document.getElementById('noOrderResult');
        const resetButton = document.getElementById('resetOrderFilter');
        const visibleCount = document.getElementById('visibleOrderCount');
        if (!orderList) return;
        const orderCards = Array.from(orderList.querySelectorAll('.order-card'));
        let currentStatus = null;

        // Chuẩn hóa nội dung tìm kiếm tiếng Việt
        function normalizeText(text) {
            return String(text || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .trim();
        }

        // Lọc đơn hàng theo trạng thái và từ khóa
        function filterOrders() {
            const keyword = normalizeText(searchInput?.value);
            let count = 0;
            orderCards.forEach(card => {
                const matchesStatus = currentStatus === null
                    || card.dataset.status === currentStatus;
                const matchesSearch = normalizeText(card.dataset.search).includes(keyword);
                const isVisible = matchesStatus && matchesSearch;
                card.hidden = !isVisible;
                if (isVisible) count++;
            });
            if (emptyState) {
                emptyState.hidden = count !== 0;
            }
            if (visibleCount) {
                visibleCount.textContent = count + ' đơn trên trang này';
            }
        }

        // Sắp xếp đơn hàng
        function sortOrders() {
            const sortType = sortSelect?.value || 'newest';
            const sortedOrders = [...orderCards].sort((a, b) => {
                const dateA = Number(a.dataset.date);
                const dateB = Number(b.dataset.date);
                const priceA = Number(a.dataset.price);
                const priceB = Number(b.dataset.price);
                switch (sortType) {
                    case 'oldest':
                        return dateA - dateB;
                    case 'price_high':
                        return priceB - priceA;
                    case 'price_low':
                        return priceA - priceB;
                    default:
                        return dateB - dateA;
                }
            });
            sortedOrders.forEach(card => orderList.appendChild(card));
        }

        // Chọn trạng thái đơn hàng
        function setStatus(status) {
            currentStatus = status;
            tabs.forEach(tab => {
                const isActive = status === null
                    ? tab.dataset.filterAll === 'true'
                    : tab.dataset.status === status;
                tab.classList.toggle('active', isActive);
                tab.setAttribute('aria-pressed', String(isActive));
            });
            filterOrders();
        }

        // Sự kiện chọn bộ lọc
        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const status = this.dataset.filterAll === 'true'
                    ? null
                    : this.dataset.status;
                setStatus(status);
            });
        });

        // Sự kiện tìm kiếm
        searchInput?.addEventListener('input', filterOrders);

        // Sự kiện sắp xếp
        sortSelect?.addEventListener('change', function () {
            sortOrders();
            filterOrders();
        });

        // Đặt lại bộ lọc
        resetButton?.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
            }
            if (sortSelect) {
                sortSelect.value = 'newest';
            }
            setStatus(null);
            sortOrders();
        });

        // Khởi tạo danh sách
        sortOrders();
        filterOrders();
    });
</script>

@endsection
