@extends('layouts.app')

@section('content')
@php $title = 'Lịch sử đơn hàng'; @endphp

<div style="max-width: 1000px; margin: 20px auto; padding: 0 20px;">
    <h1 style="margin-bottom: 20px; color:#e74c3c;">Lịch sử đơn hàng</h1>

    @if (session('success'))
        <div style="padding:12px; margin-bottom:15px; background:#eafaf1; border:1px solid #abebc6; color:#196f3d; border-radius:8px;">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($orders as $order)
        <article style="background:#fff; border:1px solid #ffe8d6; border-radius:12px; padding:18px; margin-bottom:14px; box-shadow:0 3px 10px rgba(0,0,0,.04);">
            <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <div>
                    <strong style="font-size:16px;">Đơn #DH{{ str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT) }}</strong>
                    <div style="color:#777; margin-top:6px;">{{ $order->created_at?->format('d/m/Y H:i') }}</div>
                    <div style="color:#555; margin-top:4px;">{{ $order->customer_name }}</div>
                </div>
                <span style="align-self:flex-start; padding:7px 12px; border-radius:20px; background:{{ $order->statusColor() }}18; color:{{ $order->statusColor() }}; font-weight:bold;">
                    {{ $order->statusLabel() }}
                </span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px; padding-top:14px; border-top:1px dashed #eee;">
                <div style="color:#666;">{{ $order->details_count }} món <span style="margin:0 8px;">•</span> {{ number_format($order->total_price, 0, ',', '.') }}đ</div>
                <a href="{{ route('customer.orders.show', $order) }}" style="text-decoration:none; color:#e74c3c; font-weight:bold;">
                    Xem chi tiết <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
        </article>
    @empty
        <div style="text-align:center; padding:50px 20px; background:#fff5f2; border-radius:12px;">
            <i class="fa-solid fa-receipt" style="font-size:38px; color:#ff9f43;"></i>
            <h3 style="margin:12px 0 6px;">Bạn chưa có đơn hàng</h3>
            <a href="{{ url('/foods') }}" style="color:#e74c3c; font-weight:bold;">Quay lại chọn món</a>
        </div>
    @endforelse

    <div>{{ $orders->links() }}</div>
</div>
@endsection
