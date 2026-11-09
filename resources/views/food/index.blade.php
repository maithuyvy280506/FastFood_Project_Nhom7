@extends('layouts.app')

@section('content')
@php
    $title = "Danh sách món ăn";
    
    // Kiểm tra quyền Admin từ Session Laravel
    $currentRole = session('user_role', '');
    $isAdmin = ($currentRole == 1 || strtolower($currentRole) === 'admin');

    // Lấy banner từ Controller truyền qua biến $dbBanners
    $dbBanners = $dbBanners ?? null;
    $banners = ($dbBanners && count($dbBanners) > 0) ? $dbBanners : [
        "https://intphcm.com/data/upload/poster-tra-sua-dac-sac.jpg",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR03s-glNZWJrtSzIFiV7F_bkyWtsAqhfhEiHoGzLWF_jIFErBcpJHAiko&s=10",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSZHwHt-FZuhlg8cIdDZwqSYFhBE5i3LJ3qcGYqgH7hYuyJtw0-np-u4PLK&s=10",
        "https://img.magnific.com/psd-mien-phi/mau-banner-anh-bia-facebook-thuc-don-mon-an-va-pizza-ngon_120329-4878.jpg?semt=ais_hybrid&w=740&q=80",
        "https://cdn.s99.vn/ss1/prod/product/5fff27bb45cef24f69225ae6a2425871.jpg"
    ];

    $totalBanners = count($banners);
    $trackWidth = ($totalBanners * 100) . "%";
    $slideWidth = (100.0 / $totalBanners) . "%";

    // Lọc danh sách món ăn đang Flash Sale (is_active / isFlashSale)
    $flashSaleFoods = collect($foods ?? [])->where('isFlashSale', true);
@endphp

<style>
    /* --- BỔ SUNG CSS CHO BANNER ĐỂ KHÔNG BỊ VỠ GIAO DIỆN --- */
    .banner-wrapper {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    .banner-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        width: 100%;
    }

    .banner-slider-container {
        position: relative;
        width: 100%;
        height: 220px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        background: #fff;
        border: 1px solid #ffe8d6;
    }

    .slider-track {
        display: flex;
        height: 100%;
        transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .slide-item {
        height: 100%;
        object-fit: cover;
    }

    .global-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #e74c3c;
        color: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        z-index: 10;
        box-shadow: 0 3px 8px rgba(231, 76, 60, 0.4);
    }

    .global-slider-btn:hover {
        background: #c0392b;
    }

    .global-btn-prev {
        left: -22px;
    }

    .global-btn-next {
        right: -22px;
    }

    /* --- CSS CỦA MÓN ĂN --- */
    .food-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
        padding-top: 15px;
        margin-bottom: 30px;
    }

    .food-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #eee;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .badge-flashsale {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #ffa502;
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: bold;
        z-index: 2;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }

    .food-img {
        width: 100%;
        height: 170px;
        object-fit: cover;
    }

    .food-info {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .food-name {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 5px;
        color: #2f3542;
    }

    /* CSS cho phần mô tả món ăn */
    .food-desc {
        font-size: 13px;
        color: #747d8c;
        margin-bottom: 12px;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Giới hạn tối đa 2 dòng, tránh vỡ giao diện */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.4;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto; /* Đẩy giá và nút bấm xuống đáy card */
    }

    .food-price {
        color: #d65c43;
        font-weight: bold;
        font-size: 16px;
    }

    .old-price {
        text-decoration: line-through;
        color: #a4b0be;
        font-size: 12px;
        display: block;
        line-height: 1;
    }

    .btn-add {
        background: linear-gradient(90deg, #d65c43, #f1a04b);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: bold;
    }

    .btn-add:hover {
        opacity: 0.9;
    }

    .section-title-bar {
        background: linear-gradient(90deg, #d65c43, #f1a04b);
        color: white;
        padding: 14px 20px;
        border-radius: 9px;
        margin: 30px 0 15px 0;
        font-size: 17px;
        font-weight: bold;
        text-transform: uppercase;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(214, 92, 67, 0.2);
    }

    .countdown-timer {
        display: flex;
        gap: 6px;
        align-items: center;
        font-size: 13px;
        font-weight: normal;
    }

    .countdown-box {
        background: #ffffff;
        color: #d65c43;
        padding: 3px 7px;
        border-radius: 5px;
        font-family: monospace;
        font-size: 14px;
        font-weight: bold;
    }
</style>

@if ($totalBanners > 0)
    <div class="banner-wrapper">
        <button class="global-slider-btn global-btn-prev" onclick="moveGlobalSlide(-1)">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <div class="banner-grid-2">
            <div class="banner-slider-container">
                <div class="slider-track" id="sliderTrack1" style="width: {{ $trackWidth }};">
                    @foreach ($banners as $banner)
                        <img src="{{ $banner }}" class="slide-item" style="width: {{ $slideWidth }};" alt="Promo Banner" />
                    @endforeach
                </div>
            </div>
            <div class="banner-slider-container">
                <div class="slider-track" id="sliderTrack2" style="width: {{ $trackWidth }};">
                    @foreach ($banners as $index => $banner)
                        @php $nextBanner = $banners[($index + 1) % count($banners)]; @endphp
                        <img src="{{ $nextBanner }}" class="slide-item" style="width: {{ $slideWidth }};" alt="Promo Banner" />
                    @endforeach
                </div>
            </div>
        </div>
        <button class="global-slider-btn global-btn-next" onclick="moveGlobalSlide(1)">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
@endif

<!-- PHẦN FLASH SALE -->
@if (count($flashSaleFoods) > 0)
    <div class="section-title-bar">
        <div><i class="fa-solid fa-bolt"></i> Flash Sale Trong</div>
        <div class="countdown-timer">
            <span>Kết thúc sau:</span>
            <span class="countdown-box" id="hours">00</span> :
            <span class="countdown-box" id="minutes">00</span> :
            <span class="countdown-box" id="seconds">00</span>
        </div>
    </div>

    <div class="food-grid">
        @foreach ($flashSaleFoods as $item)
            <div class="food-card">
                <span class="badge-flashsale">
                    Flash Sale
                </span>

                <a href="{{ url('/foods/' . $item->id_product) }}">
                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="food-img" />
                </a>

                <div class="food-info">
                    <div class="food-name">
                        <a href="{{ url('/foods/' . $item->id_product) }}" style="text-decoration: none; color: #2f3542;">
                            {{ $item->name }}
                        </a>
                    </div>

                    <!-- THÊM MÔ TẢ DƯỚI TÊN MÓN VÀ TRÊN GIÁ (FLASH SALE) -->
                    <div class="food-desc">
                        {{ $item->description ?? 'Đang cập nhật mô tả món ăn...' }}
                    </div>

                    <div class="price-row">
                        <div>
                            @php
                                $basePrice = $item->price;
                                $finalPrice = $basePrice; 
                            @endphp
                            <span class="old-price">{{ number_format($basePrice, 0, ',', '.') }}đ</span>
                            <span class="food-price">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>
                        </div>

                        @if ($item->is_active == 1)
                            <button type="button" class="btn-add btn-add-quick" data-foodid="{{ $item->id_product }}"><strong>+</strong></button>
                        @else
                            <button class="btn-add" disabled style="background:#ccc; cursor:not-allowed;">Hết hàng</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- PHẦN TẤT CẢ MÓN ĂN -->
<div class="section-title-bar">
    <div>Tất Cả Món Ăn</div>
</div>

<div class="food-grid">
    @foreach ($foods ?? [] as $item)
        <div class="food-card">
            <a href="{{ url('/foods/' . $item->id_product) }}">
                <img src="{{ $item->image }}" alt="{{ $item->name }}" class="food-img" />
            </a>

            <div class="food-info">
                <div class="food-name">
                    <a href="{{ url('/foods/' . $item->id_product) }}" style="text-decoration: none; color: #2f3542;">
                        {{ $item->name }}
                    </a>
                </div>

                <!-- THÊM MÔ TẢ DƯỚI TÊN MÓN VÀ TRÊN GIÁ (TẤT CẢ MÓN ĂN) -->
                <div class="food-desc">
                    {{ $item->description ?? 'Đang cập nhật mô tả món ăn...' }}
                </div>

                <div class="price-row">
                    <div>
                        <span class="food-price">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                    </div>

                    @if ($item->is_active == 1)
                        <button type="button" class="btn-add btn-add-quick" data-foodid="{{ $item->id_product }}"><strong>+</strong></button>
                    @else
                        <button class="btn-add" disabled style="background:#ccc; cursor:not-allowed;">Hết hàng</button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- NÚT ICON GIỎ HÀNG CỐ ĐỊNH GÓC DƯỚI BÊN PHẢI -->
<div style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;">
    <a href="javascript:void(0);" onclick="checkAdminAndGoToCart('{{ url('/cart') }}')" style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: linear-gradient(135deg, #e74c3c, #ff9f43); color: white; border-radius: 50%; box-shadow: 0 4px 15px rgba(231,76,60,0.4); text-decoration: none; font-size: 24px;" title="Xem giỏ hàng">
        <i class="fa-solid fa-cart-shopping"></i>
        <span id="cartCountBadge" style="position: absolute; top: 5px; right: 5px; background: #2f3542; color: white; font-size: 11px; font-weight: bold; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white;">0</span>
    </a>
</div>

<script>
    var isAdminUser = @json($isAdmin);

    function checkAdminAndGoToCart(cartUrl) {
        if (isAdminUser) {
            alert("Vui lòng đăng nhập tài khoản khách hàng để thực hiện.");
            return;
        }
        window.location.href = cartUrl;
    }

    $(document).ready(function () {
        // Lấy số lượng giỏ hàng khi load trang
        $.ajax({
            url: '/cart/count',
            type: 'GET',
            success: function (res) {
                if (res.success) {
                    $('#cartCountBadge').text(res.totalCount);
                }
            }
        });
    });

    // Thêm món ăn nhanh vào giỏ hàng qua AJAX
    $(document).on('click', '.btn-add-quick', function () {
        if (isAdminUser) {
            alert("Vui lòng đăng nhập tài khoản khách hàng để thực hiện.");
            return;
        }

        var foodId = $(this).data('foodid');

        $.ajax({
            url: '/cart/add',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                food_id: foodId,
                quantity: 1
            },
            success: function (res) {
                if (res.success) {
                    $('#cartCountBadge').text(res.totalCount);
                    alert(res.message);
                } else {
                    alert(res.message);
                }
            },
            error: function () {
                alert("Có lỗi xảy ra khi thêm vào giỏ hàng!");
            }
        });
    });
</script>
@endsection