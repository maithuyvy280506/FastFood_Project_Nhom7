@extends('layouts.app')
@section('content')
@php $title = 'Thanh toán'; @endphp
<div style="max-width:900px;margin:20px auto;padding:0 20px;">
    <h1 style="color:#e74c3c;">Thông tin thanh toán</h1>
    @if (session('success'))
        <div style="padding:12px;margin:12px 0;background:#eafaf1;color:#196f3d;border-radius:8px;">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('cart.place-order') }}" style="display:grid;grid-template-columns:1fr 330px;gap:20px;margin-top:15px;">
        @csrf
        <div style="background:#fff;border:1px solid #ffe8d6;border-radius:10px;padding:18px;">
            @foreach (['customer_name'=>'Họ tên','customer_phone'=>'Số điện thoại','customer_address'=>'Địa chỉ giao hàng'] as $field => $label)
                <label style="display:block;margin-bottom:12px;font-weight:bold;">{{ $label }}
                    @if ($field === 'customer_address')
                        <textarea name="{{ $field }}" required style="width:100%;margin-top:6px;padding:9px;border:1px solid #ddd;border-radius:6px;">{{ old($field, $customer->address) }}</textarea>
                    @else
                        <input name="{{ $field }}" value="{{ old($field, $field === 'customer_name' ? $customer->name : $customer->phone) }}" required style="width:100%;margin-top:6px;padding:9px;border:1px solid #ddd;border-radius:6px;">
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
            <label style="display:block;margin-top:12px;font-weight:bold;">Voucher đã lưu
                <select name="promotion_id" style="width:100%;margin-top:6px;padding:9px;border:1px solid #ddd;border-radius:6px;">
                    <option value="">Không sử dụng voucher</option>
                    @foreach ($availablePromotions as $promotion)
                        <option
                            value="{{ $promotion->id_promotion }}"
                            @selected((string) old('promotion_id', $selectedPromotion?->id_promotion) === (string) $promotion->id_promotion)
                            @disabled($subtotal < $promotion->min_order_value)
                        >
                            {{ $promotion->code }} - {{ $promotion->name }}
                            (đơn tối thiểu {{ number_format($promotion->min_order_value, 0, ',', '.') }}đ)
                        </option>
                    @endforeach
                </select>
                @if ($promotionError)
                    <span style="display:block;margin-top:6px;color:#c0392b;font-size:13px;">{{ $promotionError }}</span>
                @endif
                @error('promotion_id')
                    <span style="display:block;margin-top:6px;color:#c0392b;font-size:13px;">{{ $message }}</span>
                @enderror
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
                <div style="display:flex;justify-content:space-between;margin-top:6px;"><span>Phí giao hàng</span><strong>{{ number_format($shippingFee,0,',','.') }}đ</strong></div>
                @if ($discountAmount > 0)
                    <div style="display:flex;justify-content:space-between;margin-top:6px;color:#196f3d;">
                        <span>Voucher {{ $selectedPromotion->code }}</span>
                        <strong>-{{ number_format($discountAmount,0,',','.') }}đ</strong>
                    </div>
                @endif
                <div style="display:flex;justify-content:space-between;margin-top:10px;color:#e74c3c;font-size:18px;">
                    <span>Tổng</span>
                    <strong>{{ number_format(max(0, $subtotal + $shippingFee - $discountAmount),0,',','.') }}đ</strong>
                </div>
            </div>
            <button id="placeOrderButton" type="submit" style="width:100%;margin-top:15px;border:0;background:#e74c3c;color:#fff;padding:12px;border-radius:8px;font-weight:bold;cursor:pointer;">XÁC NHẬN ĐẶT HÀNG</button>
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
