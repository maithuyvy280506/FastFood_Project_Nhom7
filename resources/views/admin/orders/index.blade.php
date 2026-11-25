@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel" style="padding:24px;">
        <h1 style="color:#e74c3c;">DANH SÁCH ĐƠN HÀNG</h1>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;margin-top:18px;">
                <thead>
                    <tr style="border-bottom:1px solid #eee;text-align:left;">
                        <th style="padding:12px;">Mã đơn</th>
                        <th style="padding:12px;">Ngày đặt</th>
                        <th style="padding:12px;">Khách hàng</th>
                        <th style="padding:12px;">Thanh toán</th>
                        <th style="padding:12px;">Tổng tiền</th>
                        <th style="padding:12px;">Trạng thái</th>
                        <th style="padding:12px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr style="border-bottom:1px solid #f1f1f1;">
                            <td style="padding:12px;"><strong>{{ $order['code'] }}</strong></td>
                            <td style="padding:12px;">{{ $order['date'] }}</td>
                            <td style="padding:12px;">{{ $order['customer'] }}</td>
                            <td style="padding:12px;">{{ $order['payment'] }}</td>
                            <td style="padding:12px;"><strong>{{ $order['total'] }}</strong></td>
                            <td style="padding:12px;">
                                <span class="status-pill">{{ $order['status'] }}</span>
                            </td>
                            <td style="padding:12px;text-align:right;">
                                <a class="admin-button" href="{{ route('admin.orders.show', $order['id']) }}">Chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:24px;text-align:center;">Chưa có đơn hàng</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px;">{{ $orders->links() }}</div>
    </section>
</div>
@endsection
