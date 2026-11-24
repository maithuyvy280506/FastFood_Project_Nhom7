@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel table-panel">
        <div class="admin-list-toolbar">
            <h1>DANH SÁCH ĐƠN HÀNG</h1>
            <select class="admin-input filter-select" aria-label="Lọc nâng cao">
                <option>-- Lọc nâng cao --</option>
                <option>Đơn mới nhất</option>
                <option>Giá trị cao nhất</option>
            </select>
            <div class="status-tabs" aria-label="Lọc trạng thái">
                <button type="button" class="status-tab is-active">Tất cả</button>
                <button type="button" class="status-tab">Chờ xác nhận</button>
                <button type="button" class="status-tab">Đang chuẩn bị</button>
                <button type="button" class="status-tab">Đang giao</button>
                <button type="button" class="status-tab">Đã giao</button>
                <button type="button" class="status-tab">Đã hủy</button>
            </div>
        </div>

        <div class="admin-table-scroll">
            <table class="admin-table order-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th><th>Ngày đặt</th><th>Khách hàng</th><th>Thanh toán</th>
                        <th>Cập nhật</th><th>Tổng tiền</th><th>Trạng thái</th><th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><strong class="accent-text">{{ $order['code'] }}</strong></td>
                            <td>{{ $order['date'] }}</td>
                            <td>{{ $order['customer'] }}</td>
                            <td>{{ $order['payment'] }}</td>
                            <td>{{ $order['updated'] }}</td>
                            <td><strong>{{ $order['total'] }}</strong></td>
                            <td><span class="status-pill {{ $order['status'] === 'Đã hủy' ? 'status-cancelled' : ($order['status'] === 'Đang chuẩn bị' ? 'status-preparing' : ($order['status'] === 'Đang giao' ? 'status-delivering' : 'status-pending')) }}">{{ $order['status'] }}</span></td>
                            <td class="order-actions">
                                @if($order['status'] !== 'Đã hủy' && $order['status'] !== 'Đã giao')
                                    <select class="admin-input status-select" data-order-id="{{ $order['id'] }}" aria-label="Trạng thái đơn {{ $order['code'] }}">
                                        @foreach(['Chờ xác nhận', 'Đang chuẩn bị', 'Đang giao', 'Đã giao', 'Đã hủy'] as $statusCode => $status)
                                            <option value="{{ $statusCode }}" @selected($order['statusCode'] === $statusCode)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="admin-button save-order-status">Lưu</button>
                                @endif
                                <a class="icon-button" href="{{ route('admin.orders.show', substr($order['code'], 2)) }}" aria-label="Xem chi tiết {{ $order['code'] }}">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">
            <span>Hiển thị {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} trong {{ $orders->total() }} đơn hàng</span>
            <div>{{ $orders->links() }}</div>
        </div>
    </section>
</div>
<script>
document.querySelectorAll('.save-order-status').forEach(function (button) {
    button.addEventListener('click', async function () {
        const select = button.parentElement.querySelector('.status-select');
        const response = await fetch('/admin/orders/' + select.dataset.orderId + '/status', {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token())
            },
            body: JSON.stringify({ status: Number(select.value) })
        });
        if (response.ok) window.location.reload();
        else alert('Không thể cập nhật trạng thái');
    });
});
</script>
@endsection
