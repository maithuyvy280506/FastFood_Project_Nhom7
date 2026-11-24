@extends('layouts.app')
@section('content')
@php $title = 'Giỏ hàng'; @endphp
<div style="max-width:900px; margin:20px auto; padding:0 20px;">
    <h1 style="color:#e74c3c;">Giỏ hàng</h1>
    @if (session('success')) <div style="padding:12px; margin:12px 0; background:#eafaf1; border-radius:8px;">{{ session('success') }}</div> @endif
    @if (session('error')) <div style="padding:12px; margin:12px 0; background:#fdecea; border-radius:8px;">{{ session('error') }}</div> @endif

    @forelse ($cartItems as $item)
        <div style="display:flex; align-items:center; gap:15px; border:1px solid #ffe8d6; border-radius:10px; padding:12px; margin-top:12px;">
            <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
            <div style="flex:1;">
                <strong>{{ $item->product->name }}</strong>
                @if (!empty($item->variants_selected)) <div style="color:#777;font-size:12px;">{{ implode(', ', $item->variants_selected) }}</div> @endif
                <div style="color:#e74c3c;font-weight:bold;">{{ number_format($item->product->price,0,',','.') }}đ</div>
                <form method="POST" action="{{ route('cart.update', $item) }}" style="margin-top:8px;">
                    @csrf @method('PATCH')
                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" style="width:70px;padding:5px;">
                    <button type="submit" style="padding:6px 10px;border:1px solid #ddd;background:#fff;border-radius:5px;">Cập nhật</button>
                </form>
            </div>
            <form method="POST" action="{{ route('cart.destroy', $item) }}">
                @csrf @method('DELETE')
                <button type="submit" style="border:0;background:none;color:#e74c3c;"><i class="fa-solid fa-trash"></i></button>
            </form>
        </div>
    @empty
        <div style="text-align:center;padding:50px;">Giỏ hàng trống</div>
    @endforelse

    @if ($cartItems->isNotEmpty())
        <div style="text-align:right;margin-top:20px;">
            <div>Tạm tính: <strong>{{ number_format($subtotal,0,',','.') }}đ</strong></div>
            <a href="{{ route('cart.checkout') }}" style="display:inline-block;margin-top:12px;background:#e74c3c;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:bold;">Tiến hành đặt hàng</a>
        </div>
    @endif
</div>
@endsection
