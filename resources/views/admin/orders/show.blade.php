@extends('layouts.app')

@section('content')
<div class="admin-page">
    <div class="admin-detail-heading">
        <h1>CHI TIẾT ĐƠN HÀNG: <span>#{{ $orderCode }}</span></h1>
        <a class="admin-button admin-button-muted" href="{{ route('admin.orders.index') }}">Quay lại danh sách</a>
    </div>

    <div class="order-detail-grid">
        <section class="admin-panel detail-items-panel">
            <div class="admin-panel-heading"><h2>DANH SÁCH MÓN ĂN CẦN LÀM</h2></div>
            <div class="admin-table-scroll">
                <table class="admin-table detail-items-table">
                    <thead><tr><th>Món ăn &amp; Yêu cầu</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td class="food-name-cell"><img src="{{ $item['image'] }}" alt=""><strong>{{ $item['name'] }}</strong></td>
                                <td>×{{ $item['quantity'] }}</td>
                                <td>{{ $item['price'] }}</td>
                                <td class="accent-text"><strong>{{ $item['price'] }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="order-total-box">
                <div><span>Tổng tiền hàng:</span><strong>{{ number_format($order->total_price - $order->shipping_fee, 0, ',', '.') }} đ</strong></div>
                <div><span>Phí vận chuyển:</span><strong>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</strong></div>
                <div class="order-grand-total"><strong>TỔNG THANH TOÁN:</strong><strong>{{ number_format($order->total_price, 0, ',', '.') }} đ</strong></div>
            </div>
        </section>

        <aside class="order-detail-aside">
            <section class="admin-panel detail-info-panel">
                <div class="admin-panel-heading"><h2>TRẠNG THÁI ĐƠN</h2></div>
                <dl>
                    <div><dt>Trạng thái:</dt><dd>
                        <select id="adminOrderStatus" class="admin-input status-select">
                            @foreach(['Chờ xác nhận', 'Đang chuẩn bị', 'Đang giao', 'Đã giao', 'Đã hủy'] as $statusIndex => $status)
                                <option value="{{ $statusIndex }}" @selected($order->status === $statusIndex)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </dd></div>
                    <div><dt>Ngày đặt:</dt><dd>{{ $order->created_at?->format('d/m/Y H:i') }}</dd></div>
                    <div><dt>Thanh toán:</dt><dd>{{ $order->method === 2 ? 'Chuyển khoản' : 'Tiền mặt' }}</dd></div>
                    <div><dt>Ghi chú chung:</dt><dd class="accent-text">{{ $order->note ?: 'Không có ghi chú' }}</dd></div>
                    <div><dt></dt><dd><button type="button" id="saveAdminOrderStatus" class="admin-button">Cập nhật trạng thái</button></dd></div>
                </dl>
            </section>
            <section class="admin-panel detail-info-panel">
                <div class="admin-panel-heading"><h2>KHÁCH HÀNG</h2></div>
                <dl>
                    <div><dt>Họ tên:</dt><dd>{{ $order->customer_name }}</dd></div>
                    <div><dt>Điện thoại:</dt><dd class="accent-text">{{ $order->customer_phone }}</dd></div>
                    <div class="detail-address"><dt>Địa chỉ nhận hàng:</dt><dd>{{ $order->customer_address }}</dd></div>
                </dl>
            </section>
        </aside>
    </div>
</div>
<script>
document.getElementById('saveAdminOrderStatus')?.addEventListener('click', async function () {
    const status = document.getElementById('adminOrderStatus').value;
    const response = await fetch(@json(route('admin.orders.update-status', $order)), {
        method: 'PATCH',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token())
        },
        body: JSON.stringify({ status: Number(status) })
    });
    if (response.ok) {
        alert('Đã cập nhật trạng thái đơn hàng');
    } else {
        alert('Không thể cập nhật trạng thái');
    }
});
</script>
@endsection
