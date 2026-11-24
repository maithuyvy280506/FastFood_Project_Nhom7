@extends('layouts.app')

@section('content')
@php $title = 'Chi tiết đơn hàng'; @endphp

<div style="max-width:900px; margin:20px auto; padding:0 20px;">
    <a href="{{ route('customer.orders.index') }}" style="color:#555; text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Lịch sử đơn hàng</a>

    @if (session('error'))
        <div style="padding:12px; margin:15px 0; background:#fdecea; border:1px solid #f5b7b1; color:#922b21; border-radius:8px;">{{ session('error') }}</div>
    @endif

    <div style="background:#fff; border:1px solid #ffe8d6; border-radius:12px; margin-top:15px; overflow:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding:18px 20px; background:#fff5f2;">
            <div>
                <h2 style="margin:0;">Đơn #DH{{ str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT) }}</h2>
                <small style="color:#777;">{{ $order->created_at?->format('d/m/Y H:i') }}</small>
            </div>
            <span id="orderStatus" data-status="{{ $order->status }}" style="padding:8px 14px; border-radius:20px; background:{{ $order->statusColor() }}18; color:{{ $order->statusColor() }}; font-weight:bold;">
                {{ $order->statusLabel() }}
            </span>
        </div>

        <div style="padding:20px;">
            <h3 style="margin-bottom:12px;">Món đã mua</h3>
            @foreach ($order->details as $detail)
                <div style="display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #f2f2f2;">
                    <img src="{{ $detail->product->image }}" alt="{{ $detail->product->name }}" style="width:70px; height:70px; object-fit:cover; border-radius:8px;">
                    <div style="flex:1;">
                        <strong>{{ $detail->product->name }}</strong>
                        @if (!empty($detail->variants_selected))
                            <div style="color:#777; font-size:12px; margin-top:4px;">{{ implode(', ', $detail->variants_selected) }}</div>
                        @endif
                        <div style="color:#777; font-size:13px; margin-top:4px;">SL: {{ $detail->quantity }}</div>
                    </div>
                    <strong style="color:#e74c3c;">{{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}đ</strong>
                </div>
            @endforeach

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:18px;">
                <div style="color:#666; line-height:1.8;">
                    <strong>Người nhận:</strong> {{ $order->customer_name }}<br>
                    <strong>Điện thoại:</strong> {{ $order->customer_phone }}<br>
                    <strong>Địa chỉ:</strong> {{ $order->customer_address }}
                </div>
                <div style="text-align:right; line-height:1.8;">
                    <div>Tạm tính: {{ number_format($order->total_price - $order->shipping_fee, 0, ',', '.') }}đ</div>
                    <div>Phí giao hàng: {{ number_format($order->shipping_fee, 0, ',', '.') }}đ</div>
                    <strong style="font-size:18px; color:#e74c3c;">Tổng: {{ number_format($order->total_price, 0, ',', '.') }}đ</strong>
                </div>
            </div>

            @if ($order->canBeCancelled())
                <form id="cancelOrderForm" method="POST" action="{{ route('customer.orders.cancel', $order) }}" style="margin-top:20px; text-align:right;">
                    @csrf
                    <button type="submit" onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')" style="border:0; background:#e74c3c; color:#fff; padding:10px 18px; border-radius:7px; font-weight:bold; cursor:pointer;">
                        Hủy đơn
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@if ($order->status !== \App\Models\Order::STATUS_CANCELLED && $order->status !== \App\Models\Order::STATUS_DELIVERED)
<script>
setInterval(async function () {
    const response = await fetch(@json(route('customer.orders.status', $order)), { headers: { 'Accept': 'application/json' } });
    if (!response.ok) return;
    const data = await response.json();
    document.getElementById('orderStatus').textContent = data.statusLabel;
    if (data.status !== Number(document.getElementById('orderStatus').dataset.status)) {
        window.location.reload();
    }
}, 10000);
</script>
@endif
@endsection
