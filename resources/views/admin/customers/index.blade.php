@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel" style="padding:24px;">
        <h1 style="color:#e74c3c;">DANH SÁCH KHÁCH HÀNG</h1>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;margin-top:18px;">
                <thead>
                    <tr style="border-bottom:1px solid #eee;text-align:left;">
                        <th style="padding:12px;">Mã</th>
                        <th style="padding:12px;">Tên khách hàng</th>
                        <th style="padding:12px;">Email</th>
                        <th style="padding:12px;">Số điện thoại</th>
                        <th style="padding:12px;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr style="border-bottom:1px solid #f1f1f1;">
                            <td style="padding:12px;">{{ 'KH'.str_pad((string) $customer->id_user, 3, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding:12px;"><strong>{{ $customer->name }}</strong></td>
                            <td style="padding:12px;">{{ $customer->email }}</td>
                            <td style="padding:12px;">{{ $customer->phone ?: '-' }}</td>
                            <td style="padding:12px;">{{ $customer->status == 1 ? 'Hoạt động' : 'Đã khóa' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="padding:24px;text-align:center;">Chưa có khách hàng</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
