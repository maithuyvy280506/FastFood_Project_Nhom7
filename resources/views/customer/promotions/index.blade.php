@extends('layouts.app')

@section('content')
@php $title = 'Kho Voucher'; @endphp

<style>
    .voucher-page {
        max-width: 1100px;
        margin: 24px auto;
        padding: 0 20px;
        width: 100%;
    }

    .voucher-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
        margin-top: 20px;
    }

    .voucher-card {
        background: #fff;
        border: 1px solid #ffe0cc;
        border-left: 6px solid #e74c3c;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 4px 14px rgba(231, 76, 60, .08);
    }

    .voucher-card.expired {
        opacity: .48;
        filter: grayscale(1);
    }

    .voucher-card-header,
    .voucher-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .voucher-code {
        color: #e74c3c;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: .5px;
    }

    .voucher-badge {
        border-radius: 20px;
        padding: 5px 10px;
        background: #fff0e8;
        color: #b94a20;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .voucher-value {
        margin: 14px 0 8px;
        font-size: 20px;
        font-weight: 800;
        color: #303030;
    }

    .voucher-meta {
        color: #666;
        font-size: 13px;
        line-height: 1.7;
    }

    .voucher-button {
        border: 0;
        border-radius: 7px;
        background: #e74c3c;
        color: #fff;
        padding: 10px 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .voucher-button:disabled {
        background: #999;
        cursor: not-allowed;
    }

    .voucher-message {
        margin-top: 14px;
        padding: 12px;
        border-radius: 8px;
    }

    @media (max-width: 480px) {
        .voucher-page {
            padding: 0 12px;
        }
    }
</style>

<main class="voucher-page">
    <h1 style="color:#e74c3c;">Kho Voucher</h1>
    <p style="color:#666; margin-top:6px;">Lưu ưu đãi để sử dụng khi thanh toán đơn hàng.</p>

    @if (session('success'))
        <div class="voucher-message" style="background:#eafaf1; color:#196f3d;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="voucher-message" style="background:#fdecea; color:#922b21;">{{ session('error') }}</div>
    @endif

    @if ($promotions->isEmpty())
        <p style="margin-top:20px; color:#666;">Hiện chưa có voucher nào.</p>
    @else
        <div class="voucher-grid">
            @foreach ($promotions as $promotion)
                @php
                    $isExpired = $promotion->isExpired();
                    $isSoldOut = $promotion->isSoldOut();
                    $isAvailable = $promotion->isAvailable();
                    $isUsed = in_array($promotion->id_promotion, $usedPromotionIds, true);
                    $isSaved = in_array($promotion->code, $savedVoucherCodes, true);
                @endphp
                <article class="voucher-card {{ $isExpired ? 'expired' : '' }}">
                    <div class="voucher-card-header">
                        <span class="voucher-code">{{ $promotion->code }}</span>
                        @if ($isSoldOut)
                            <span class="voucher-badge">Đã hết</span>
                        @elseif ($isExpired)
                            <span class="voucher-badge">Đã hết hạn</span>
                        @elseif (! $promotion->status)
                            <span class="voucher-badge">Ngừng áp dụng</span>
                        @elseif ($promotion->start_date->isFuture())
                            <span class="voucher-badge">Chưa đến hạn</span>
                        @elseif ($isUsed)
                            <span class="voucher-badge">Đã sử dụng</span>
                        @endif
                    </div>

                    <h2 style="margin-top:12px; font-size:17px;">{{ $promotion->name }}</h2>
                    <div class="voucher-value">
                        @if ($promotion->type === \App\Models\Promotion::TYPE_PERCENT)
                            Giảm {{ $promotion->value }}%
                        @elseif ($promotion->type === \App\Models\Promotion::TYPE_FIXED)
                            Giảm {{ number_format($promotion->value, 0, ',', '.') }}đ
                        @elseif ($promotion->type === \App\Models\Promotion::TYPE_SHIPPING)
                            Giảm phí giao hàng {{ number_format($promotion->value, 0, ',', '.') }}đ
                        @else
                            Ưu đãi đặc biệt
                        @endif
                    </div>
                    <div class="voucher-meta">
                        <div>Đơn tối thiểu: {{ number_format($promotion->min_order_value, 0, ',', '.') }}đ</div>
                        <div>Hạn sử dụng: {{ $promotion->end_date->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="voucher-card-footer" style="margin-top:16px;">
                        @if ($isSoldOut)
                            <button class="voucher-button" type="button" disabled>Đã hết</button>
                        @elseif ($isExpired)
                            <button class="voucher-button" type="button" disabled>Đã hết hạn</button>
                        @elseif (! $isAvailable)
                            <button class="voucher-button" type="button" disabled>Chưa khả dụng</button>
                        @elseif ($isUsed)
                            <button class="voucher-button" type="button" disabled>Đã sử dụng</button>
                        @elseif ($isSaved)
                            <button class="voucher-button" type="button" disabled>Đã lưu</button>
                        @else
                            <form method="POST" action="{{ route('promotions.save', $promotion) }}">
                                @csrf
                                <button class="voucher-button" type="submit">Lưu &amp; Sử dụng</button>
                            </form>
                        @endif
                        <span class="voucher-meta">Còn {{ max(0, $promotion->quantity - $promotion->used) }} lượt</span>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</main>
@endsection
