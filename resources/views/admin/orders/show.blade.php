@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel" style="padding:24px;">
        <div style="display:flex;justify-content:space-between;gap:16px;align-items:flex-start;">
            <div>
                <h1 style="color:#e74c3c;margin-bottom:6px;">CHI TIẾT ĐƠN HÀNG: {{ $orderCode }}</h1>
                <p style="color:#777;">Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}</p>
            </div>
            <span class="status-pill status-pending">{{ $order->statusLabel() }}</span>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;margin-top:18px;">
            <article class="admin-panel" style="padding:18px;">
                <h2>THÔNG TIN KHÁCH HÀNG</h2>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>Số điện thoại: {{ $order->customer_phone }}</p>
                <p>Địa chỉ: {{ $order->customer_address }}</p>
                <p>Ghi chú: {{ $order->note ?: 'Không có ghi chú' }}</p>
            </article>

            <article class="admin-panel" style="padding:18px;">
                <h2>CẬP NHẬT TRẠNG THÁI</h2>
                <form id="orderStatusForm">
                    @csrf
                    <select id="orderStatus" name="status" style="width:100%;padding:10px;margin:8px 0;">
                        @foreach([0 => 'Chờ xác nhận', 1 => 'Đang chuẩn bị', 2 => 'Đang giao', 3 => 'Đã giao', 4 => 'Đã hủy'] as $statusCode => $statusLabel)
                            <option value="{{ $statusCode }}" @selected($order->status === $statusCode)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="admin-button">Cập nhật trạng thái</button>
                </form>
            </article>
        </div>

        <article class="admin-panel" style="padding:18px;margin-top:18px;">
            <h2>DANH SÁCH MÓN ĂN CẦN LÀM</h2>
            <div style="display:grid;gap:10px;margin-top:12px;">
                @forelse($items as $item)
                    <div style="display:grid;grid-template-columns:80px 1fr auto;align-items:center;gap:12px;border-bottom:1px solid #eee;padding:10px 0;">
                        @if($item['image'])
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
                        @endif
                        <strong>{{ $item['name'] }}</strong>
                        <span>x{{ $item['quantity'] }} - {{ $item['price'] }}</span>
                    </div>
                @empty
                    <p>Đơn hàng chưa có món ăn</p>
                @endforelse
            </div>
            <div style="text-align:right;margin-top:18px;">
                <p>Phí giao hàng: {{ number_format($order->shipping_fee, 0, ',', '.') }} đ</p>
                <p>Giảm giá: {{ number_format($order->discount_amount, 0, ',', '.') }} đ</p>
                <strong style="font-size:20px;color:#e74c3c;">Tổng thanh toán: {{ number_format($order->total_price, 0, ',', '.') }} đ</strong>
            </div>
        </article>
    </section>
</div>

<script>
    document.getElementById('orderStatusForm')?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const response = await fetch(@json(route('admin.orders.update-status', $order)), {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ status: Number(document.getElementById('orderStatus').value) })
        });
        const data = await response.json();
        if (!response.ok) {
            alert(data.message || 'Không thể cập nhật trạng thái');
            return;
        }
        if (typeof showSiteToast === 'function') showSiteToast('Đã cập nhật: ' + data.statusLabel, 'success');
        window.location.reload();
    });
</script>
@endsection
