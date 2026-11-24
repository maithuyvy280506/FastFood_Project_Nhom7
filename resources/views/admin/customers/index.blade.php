@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel table-panel">
        <div class="admin-list-toolbar">
            <h1>DANH SÁCH KHÁCH HÀNG</h1>
            <div class="status-tabs">
                <button type="button" class="status-tab is-active">Tất cả</button>
                <button type="button" class="status-tab">Active</button>
                <button type="button" class="status-tab">Inactive</button>
            </div>
        </div>
        <div class="admin-table-scroll">
            <table class="admin-table customer-table">
                <thead><tr><th>STT</th><th>Mã khách hàng</th><th>Họ và tên</th><th>Số điện thoại</th><th>Email</th><th>Địa chỉ</th><th>Trạng thái</th><th>Ngày tạo</th></tr></thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong class="accent-text">C{{ str_pad((string) $loop->iteration, 3, '0', STR_PAD_LEFT) }}</strong></td>
                            <td><strong>{{ $customer->name }}</strong></td>
                            <td>{{ $customer->phone ?: '—' }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->address ?: '—' }}</td>
                            <td><span class="status-pill {{ $customer->status ? 'status-active' : 'status-inactive' }}">{{ $customer->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $customer->created_at?->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">Chưa có dữ liệu khách hàng.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
