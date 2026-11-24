@extends('layouts.app')
@section('content')
@php $title = 'Thanh toán'; @endphp
<div style="max-width:900px;margin:20px auto;padding:0 20px;">
    <h1 style="color:#e74c3c;">Thông tin thanh toán</h1>
    <form method="POST" action="{{ route('cart.place-order') }}" style="display:grid;grid-template-columns:1fr 330px;gap:20px;margin-top:15px;">
        @csrf
        <div style="background:#fff;border:1px solid #ffe8d6;border-radius:10px;padding:18px;">
            @foreach (['customer_name'=>'Họ tên','customer_phone'=>'Số điện thoại','customer_address'=>'Địa chỉ giao hàng'] as $field => $label)
                <label style="display:block;margin-bottom:12px;font-weight:bold;">{{ $label }}
                    @if ($field === 'customer_address')
                        <textarea name="{{ $field }}" required style="width:100%;margin-top:6px;padding:9px;border:1px solid #ddd;border-radius:6px;">{{ old($field, session('user_address')) }}</textarea>
                    @else
                        <input name="{{ $field }}" value="{{ old($field, $field === 'customer_name' ? session('user_name') : session('user_phone')) }}" required style="width:100%;margin-top:6px;padding:9px;border:1px solid #ddd;border-radius:6px;">
                    @endif
                </label>
            @endforeach
            <label style="display:block;font-weight:bold;">Phương thức thanh toán
                <select name="method" style="width:100%;margin-top:6px;padding:9px;border:1px solid #ddd;border-radius:6px;">
                    <option value="1">Tiền mặt khi nhận hàng</option>
                    <option value="2">Chuyển khoản</option>
                </select>
            </label>
            <label style="display:block;margin-top:12px;font-weight:bold;">Ghi chú
                <textarea name="note" style="width:100%;margin-top:6px;padding:9px;border:1px solid #ddd;border-radius:6px;">{{ old('note') }}</textarea>
            </label>
        </div>
        <aside style="background:#fff5f2;border:1px solid #ffe8d6;border-radius:10px;padding:18px;height:max-content;">
            <h3>Đơn hàng</h3>
            @foreach ($cartItems as $item)
                <div style="display:flex;justify-content:space-between;margin-top:10px;">
                    <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <strong>{{ number_format($item->product->price * $item->quantity,0,',','.') }}đ</strong>
                </div>
            @endforeach
            <div style="border-top:1px dashed #ddd;margin-top:12px;padding-top:12px;">
                <div style="display:flex;justify-content:space-between;"><span>Tạm tính</span><strong>{{ number_format($subtotal,0,',','.') }}đ</strong></div>
                <div style="display:flex;justify-content:space-between;margin-top:6px;"><span>Phí giao hàng</span><strong>30.000đ</strong></div>
                <div style="display:flex;justify-content:space-between;margin-top:10px;color:#e74c3c;font-size:18px;"><span>Tổng</span><strong>{{ number_format($subtotal + 30000,0,',','.') }}đ</strong></div>
            </div>
            <button id="placeOrderButton" type="submit" style="width:100%;margin-top:15px;border:0;background:#e74c3c;color:#fff;padding:12px;border-radius:8px;font-weight:bold;cursor:pointer;">ĐẶT HÀNG</button>
        </aside>
    </form>
</div>
<script>
document.querySelector('form[action="{{ route('cart.place-order') }}"]')?.addEventListener('submit', function () {
    const button = document.getElementById('placeOrderButton');
    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right:6px;"></i>ĐANG XỬ LÝ...';
});
</script>
@endsection
