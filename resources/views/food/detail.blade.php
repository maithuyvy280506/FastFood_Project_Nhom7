@extends('layouts.app')

@section('content')
@php
    $title = 'Chi Tiết Món Ăn';
    $isActive = (int) ($food->is_active ?? 1);
    $statusBg = $isActive === 1 ? '#e8f8f5' : '#f8f9f9';
    $statusColor = $isActive === 1 ? '#2ecc71' : '#999';
    $statusText = $isActive === 1 ? 'Đang bán' : 'Tạm ngưng';

    $variantsValue = $food->variants ?? null;
    $variants = is_string($variantsValue)
        ? json_decode($variantsValue, true)
        : $variantsValue;
    $variants = is_array($variants) ? $variants : [];
@endphp

<div style="padding: 15px 20px; max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; background: #fff; border: 1px solid #ffe8d6; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <div style="width: 42%; background: #fff5f2; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img src="{{ $food->image }}" alt="{{ $food->name }}" style="width: 100%; height: 100%; min-height: 380px; object-fit: cover;">
        </div>

        <div style="width: 58%; padding: 20px 25px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-size: 11px; padding: 3px 8px; border-radius: 6px; font-weight: bold; background: {{ $statusBg }}; color: {{ $statusColor }};">
                        {{ $statusText }}
                    </span>
                </div>

                <h1 style="font-size: 22px; font-weight: bold; color: #333; margin-bottom: 8px;">{{ $food->name }}</h1>

                <div style="font-size: 20px; color: #e74c3c; font-weight: 900; margin-bottom: 12px;">
                    {{ number_format($food->price, 0, ',', '.') }}đ
                </div>

                <label for="foodQuantity" style="display:block;margin-bottom:12px;font-size:13px;font-weight:bold;color:#444;">
                    Số lượng
                    <input id="foodQuantity" type="number" value="1" min="1" max="99" style="display:block;width:90px;margin-top:6px;padding:7px;border:1px solid #ddd;border-radius:6px;">
                </label>

                @if (count($variants) > 0)
                    <div style="margin-bottom: 12px; border-top: 1px dashed #eee; padding-top: 10px;">
                        <label style="display: block; font-weight: bold; font-size: 13px; color: #444; margin-bottom: 6px;">
                            <i class="fa-solid fa-list-ul" style="color: #ff9f43; margin-right: 5px;"></i>
                            Tùy chọn thêm:
                        </label>
                        <div style="display: flex; flex-direction: column; gap: 6px; max-height: 120px; overflow-y: auto; padding-right: 4px;">
                            @foreach ($variants as $variant)
                                @php
                                    $varName = is_array($variant) ? ($variant['name'] ?? '') : $variant;
                                    $varPrice = is_array($variant) ? ($variant['extraPrice'] ?? 0) : 0;
                                @endphp
                                <label style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: #555; cursor: pointer; background: #fafafa; padding: 6px 10px; border-radius: 6px; border: 1px solid #eee;">
                                    <span style="display: flex; align-items: center; gap: 6px;">
                                        <input type="checkbox" name="selectedVariants" value="{{ $varName }}" style="accent-color: #e74c3c; width: 14px; height: 14px;">
                                        {{ $varName }}
                                    </span>
                                    <span style="color: #e74c3c; font-weight: bold; margin-left: auto;">
                                        +{{ number_format($varPrice, 0, ',', '.') }}đ
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div style="margin-bottom: 12px; border-top: 1px dashed #eee; padding-top: 10px;">
                    <label style="display: block; font-weight: bold; font-size: 13px; color: #444; margin-bottom: 6px;">
                        <i class="fa-solid fa-note-sticky" style="color: #ff9f43; margin-right: 5px;"></i>
                        Ghi chú cho quán:
                    </label>
                    <textarea id="foodNote" rows="2" placeholder="Ví dụ: Không lấy hành, ít cay..." style="width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; outline: none; resize: none; font-family: Arial, sans-serif;"></textarea>
                </div>
            </div>

            <div style="border-top: 1px dashed #eee; padding-top: 12px; display: flex; gap: 12px;">
                <a href="{{ url('/foods') }}" style="flex: 1; text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 6px; color: #555; text-decoration: none; font-weight: bold; font-size: 13px;">
                    <i class="fa-solid fa-arrow-left" style="margin-right: 4px;"></i>
                    Quay lại
                </a>
                <button id="addToCartButton" type="button" onclick="submitAddToCart('{{ $food->id_product }}')" style="flex: 2; background: linear-gradient(135deg, #e74c3c, #ff9f43); color: white; border: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer;">
                    <i class="fa-solid fa-cart-shopping" style="margin-right: 5px;"></i>
                    Thêm vào giỏ hàng
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function submitAddToCart(foodId) {
        const isLoggedIn = @json(session('user_email') || session('user_name') ? 'true' : 'false');

        if (isLoggedIn === 'false') {
            showSiteToast('Vui lòng đăng nhập để thêm món vào giỏ hàng!', 'info');
            setTimeout(() => window.location.href = '{{ url('/login') }}', 1200);
            return;
        }

        const selectedVariants = [];
        document.querySelectorAll('input[name="selectedVariants"]:checked').forEach((checkbox) => {
            selectedVariants.push(checkbox.value);
        });

        const note = document.getElementById('foodNote').value.trim();
        const quantity = Number.parseInt(document.getElementById('foodQuantity').value, 10);
        const button = document.getElementById('addToCartButton');

        if (!Number.isInteger(quantity) || quantity < 1 || quantity > 99) {
            showSiteToast('Số lượng phải từ 1 đến 99', 'error');
            return;
        }

        button.disabled = true;
        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json; charset=utf-8',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                food_id: foodId,
                quantity,
                variants_selected: selectedVariants,
                note
            })
        })
        .then(async (response) => {
            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Không thể thêm món vào giỏ hàng');
            }

            showSiteToast(data.message);
            ['cart-count', 'cartCountBadge'].forEach((id) => {
                const badge = document.getElementById(id);
                if (badge) {
                    badge.innerText = data.totalCount;
                }
            });
        })
        .catch((error) => {
            console.error('Unable to add product to cart:', error);
            showSiteToast(error.message || 'Đã xảy ra lỗi kết nối với server!', 'error');
        })
        .finally(() => {
            button.disabled = false;
        });
    }
</script>
@endsection
