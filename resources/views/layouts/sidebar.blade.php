<aside class="sidebar" id="appSidebar">
    <div class="sidebar-section-title">DANH MỤC MÓN ĂN</div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ url('/foods') }}?category=&search={{ request('search') }}" class="{{ empty(request('category')) ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i><span>Tất cả món</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=1&search={{ request('search') }}" class="{{ request('category') == '1' ? 'active' : '' }}">
                <i class="fa-solid fa-drumstick-bite"></i> <span>Gà Rán</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=2&search={{ request('search') }}" class="{{ request('category') == '2' ? 'active' : '' }}">
                <i class="fa-solid fa-hamburger"></i> <span>Burger</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=5&search={{ request('search') }}" class="{{ request('category') == '5' ? 'active' : '' }}">
                <i class="fa-solid fa-pizza-slice"></i> <span>Pizza</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=9&search={{ request('search') }}" class="{{ request('category') == '9' ? 'active' : '' }}">
                <i class="fa-sharp fa-solid fa-bowl-rice"></i> <span>Cơm</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=6&search={{ request('search') }}" class="{{ request('category') == '6' ? 'active' : '' }}">
                <i class="fa-solid fa-utensils"></i><span>Mì ý</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=7&search={{ request('search') }}" class="{{ request('category') == '7' ? 'active' : '' }}">
                <i class="fa-solid fa-leaf"></i><span>Salad</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=8&search={{ request('search') }}" class="{{ request('category') == '8' ? 'active' : '' }}">
                <i class="fa-solid fa-ice-cream"></i> <span>Tráng miệng</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=4&search={{ request('search') }}" class="{{ request('category') == '4' ? 'active' : '' }}">
                <i class="fa-solid fa-cookie"></i> <span>Snack</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/foods') }}?category=3&search={{ request('search') }}" class="{{ request('category') == '3' ? 'active' : '' }}">
                <i class="fa-solid fa-mug-hot"></i> <span>Trà Sữa & Đồ Uống</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-section-title">KHUYẾN MÃI</div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ url('/promotions') }}" class="{{ request()->is('promotions*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i>
                <span>Tất cả Voucher</span>
            </a>
        </li>
        @if (!empty($sidebarPromotions))
            @foreach ($sidebarPromotions as $promo)
                <li>
                    <a href="{{ url('/promotions') }}">
                        <i class="fa-solid fa-ticket" style="color: #ff9f43;"></i>
                        <span>{{ is_array($promo) ? ($promo['name'] ?? ($promo['promotionId'] ?? 'Mã ưu đãi')) : ($promo->name ?? ($promo->promotionId ?? 'Mã ưu đãi')) }}</span>
                    </a>
                </li>
            @endforeach
        @endif
    </ul>
</aside>