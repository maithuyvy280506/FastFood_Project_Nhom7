<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'FastFood Delivery' }}</title>

    <!-- Tích hợp Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Thêm jQuery phục vụ các đoạn AJAX bên dưới -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #ffffff;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- HEADER --- */
        .header-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 1000;
            border-bottom: 3px solid #e74c3c;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .logo {
            font-size: 22px;
            font-weight: 900;
            color: #e74c3c;
            text-decoration: none;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logo i {
            color: #ff9f43;
        }
        .delivery-location {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #555;
            background: #fff5f2;
            border: 1px solid #ffcca3;
            padding: 6px 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .delivery-location:hover {
            background: #ffe8d6;
        }
        .delivery-location strong {
            color: #e74c3c;
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }
        .header-center {
            flex: 1;
            max-width: 500px;
            margin: 0 20px;
            position: relative;
        }
        .search-input {
            width: 100%;
            padding: 10px 45px 10px 15px;
            border-radius: 20px;
            border: 1px solid #ffcca3;
            outline: none;
            font-size: 14px;
            background-color: #fffcfb;
            transition: all 0.3s;
        }
        .search-input:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 6px rgba(255, 159, 67, 0.3);
            background-color: #ffffff;
        }
        .search-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #e74c3c;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-auth-btn {
            background: linear-gradient(135deg, #e74c3c, #ff9f43);
            color: white;
            padding: 8px 22px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(231, 76, 60, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .user-auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(231, 76, 60, 0.4);
        }

        /* --- USER DROPDOWN PANEL & AVATAR --- */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            position: relative;
            padding: 5px 10px;
            border-radius: 20px;
            transition: background 0.2s;
        }
        .user-profile:hover {
            background: #fff5f2;
        }
        .header-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: bold;
            font-size: 15px;
            text-transform: uppercase;
        }
        .user-dropdown-panel {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            width: 320px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            z-index: 1050;
            border: 1px solid #ffe8d6;
            overflow: hidden;
            cursor: default;
        }
        .user-dropdown-panel.active {
            display: block;
        }
        .dropdown-header {
            padding: 20px;
            background: #fff5f2;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #ffe8d6;
        }
        .dropdown-avatar {
            width: 50px;
            height: 50px;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .dropdown-user-info h4 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }
        .dropdown-user-info p {
            margin: 4px 0 0;
            font-size: 13px;
            color: #666;
            word-break: break-all;
        }
        .dropdown-section-title {
            padding: 15px 20px 5px;
            font-size: 12px;
            font-weight: bold;
            color: #ff9f43;
            text-transform: uppercase;
        }
        .dropdown-menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .dropdown-menu-list li a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            transition: background 0.2s;
            border-bottom: 1px solid #f9f9f9;
        }
        .dropdown-menu-list li a:hover {
            background: #fffafa;
            color: #e74c3c;
        }
        .dropdown-menu-list li a i.icon-left {
            width: 25px;
            color: #ffb870;
            font-size: 16px;
        }
        .dropdown-menu-list li a i.icon-right {
            color: #ccc;
            font-size: 12px;
        }

        /* --- MODAL NHẬP ĐỊA CHỈ --- */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }
        .address-modal {
            background: #fff;
            width: 450px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }
        .modal-overlay.active .address-modal {
            transform: translateY(0);
        }
        .order-success-overlay {
            position: fixed;
            inset: 0;
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(47, 53, 66, 0.48);
            backdrop-filter: blur(3px);
        }
        .order-success-overlay.active {
            display: flex;
            animation: orderSuccessFadeIn 0.18s ease-out;
        }
        .order-success-modal {
            width: min(440px, 100%);
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 18px 50px rgba(47, 53, 66, 0.25);
            border: 1px solid #ffe8d6;
            text-align: center;
            overflow: hidden;
            animation: orderSuccessPop 0.24s ease-out;
        }
        .order-success-body {
            padding: 28px 30px 24px;
        }
        .order-success-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eafaf1;
            color: #27ae60;
            font-size: 34px;
            border: 7px solid #f4fbf7;
        }
        .order-success-modal h2 {
            margin: 0 0 8px;
            color: #2f3542;
            font-size: 22px;
        }
        .order-success-modal p {
            margin: 0;
            color: #747d8c;
            line-height: 1.55;
        }
        .order-success-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 18px;
            background: #fff5f2;
            border: 1px solid #ffe8d6;
            border-radius: 10px;
        }
        .order-success-summary div {
            padding: 12px 8px;
        }
        .order-success-summary div + div {
            border-left: 1px solid #ffe8d6;
        }
        .order-success-summary small {
            display: block;
            color: #999;
            margin-bottom: 4px;
        }
        .order-success-summary strong {
            color: #e74c3c;
            font-size: 16px;
        }
        .order-success-actions {
            display: grid;
            grid-template-columns: 1fr 1.35fr;
            gap: 10px;
            margin-top: 22px;
        }
        .order-success-actions a,
        .order-success-actions button {
            min-height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }
        .order-success-actions a {
            color: #555;
            border: 1px solid #ddd;
            background: #fff;
        }
        .order-success-actions button {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #e74c3c, #ff9f43);
            box-shadow: 0 5px 12px rgba(231, 76, 60, 0.24);
        }
        @keyframes orderSuccessFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes orderSuccessPop {
            from { opacity: 0; transform: translateY(14px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .site-toast {
            position: fixed;
            top: 88px;
            right: 24px;
            z-index: 4000;
            width: min(390px, calc(100vw - 40px));
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 15px 16px;
            background: #fff;
            border: 1px solid #ffe3d5;
            border-left: 4px solid #27ae60;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(47, 53, 66, 0.16);
            color: #2f3542;
            opacity: 0;
            transform: translateX(24px);
            pointer-events: none;
            transition: opacity 0.22s ease, transform 0.22s ease;
        }
        .site-toast.active {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }
        .site-toast.error {
            border-left-color: #e74c3c;
        }
        .site-toast.info {
            border-left-color: #ff9f43;
        }
        .site-toast-icon {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eafaf1;
            color: #27ae60;
        }
        .site-toast.error .site-toast-icon {
            background: #fdecea;
            color: #e74c3c;
        }
        .site-toast.info .site-toast-icon {
            background: #fff4e7;
            color: #ff9f43;
        }
        .site-toast-content {
            flex: 1;
            padding-top: 1px;
        }
        .site-toast-content strong {
            display: block;
            margin-bottom: 3px;
            font-size: 14px;
        }
        .site-toast-content span {
            display: block;
            color: #747d8c;
            font-size: 13px;
            line-height: 1.45;
        }
        .site-toast-close {
            border: 0;
            background: transparent;
            color: #b2b2b2;
            cursor: pointer;
            padding: 2px;
        }
        .address-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .close-modal {
            cursor: pointer;
            color: #999;
            font-size: 20px;
            border: none;
            background: none;
            transition: color 0.2s;
        }
        .close-modal:hover {
            color: #e74c3c;
        }
        .address-input-wrapper {
            position: relative;
        }
        .address-input-wrapper i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #ff9f43;
        }
        .address-input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s;
        }
        .address-input:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 5px rgba(231,76,60,0.2);
        }
        .btn-save-address {
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-save-address:hover {
            background: #c0392b;
        }

        /* --- NÚT TOGGLE MENU CỐ ĐỊNH --- */
        .fixed-menu-btn {
            position: fixed;
            top: 108px;
            left: 215px;
            width: 35px;
            height: 35px;
            background: #ffffff;
            color: #e74c3c;
            border: 1px solid #ffe8d6;
            border-radius: 6px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            transition: left 0.3s ease, opacity 0.3s, box-shadow 0.3s;
        }
        .fixed-menu-btn:hover {
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.2);
        }
        .fixed-menu-btn.faded {
            left: 15px;
            opacity: 0.4;
        }

        /* --- LAYOUT CHÍNH --- */
        .wrapper {
            display: flex;
            flex: 1;
            margin-top: 70px;
        }
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #ffe8d6;
            position: fixed;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 900;
        }
        .sidebar.collapsed {
            transform: translateX(-260px);
        }
        .sidebar-section-title {
            padding: 15px 20px 8px;
            font-size: 11px;
            text-transform: uppercase;
            color: #ff9f43;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            border-left: 4px solid transparent;
            padding-right: 45px;
        }
        .sidebar-menu li a i {
            width: 25px;
            font-size: 15px;
            color: #ffb870;
            transition: color 0.2s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background-color: #fff5f2;
            color: #e74c3c;
            border-left-color: #e74c3c;
            font-weight: bold;
        }
        .sidebar-menu li a:hover i, .sidebar-menu li a.active i {
            color: #e74c3c;
        }
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 25px;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
            background-color: #fafafa;
        }
        .main-content.expanded {
            margin-left: 0;
        }

        /* --- FOOTER --- */
        .footer {
            background-color: #2b2b2b;
            color: #ffffff;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            border-top: 3px solid #ff9f43;
            width: 100%;
            position: relative;
            z-index: 950;
        }
        .footer p {
            margin-bottom: 6px;
            color: #bbb;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    @php
        $banners = $banners ?? [
            "https://intphcm.com/data/upload/poster-tra-sua-dac-sac.jpg",
            "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR03s-glNZWJrtSzIFiV7F_bkyWtsAqhfhEiHoGzLWF_jIFErBcpJHAiko&s=10",
            "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSZHwHt-FZuhlg8cIdDZwqSYFhBE5i3LJ3qcGYqgH7hYuyJtw0-np-u4PLK&s=10",
            "https://img.magnific.com/psd-mien-phi/mau-banner-anh-bia-facebook-thuc-don-mon-an-va-pizza-ngon_120329-4878.jpg?semt=ais_hybrid&w=740&q=80",
            "https://cdn.s99.vn/ss1/prod/product/5fff27bb45cef24f69225ae6a2425871.jpg"
        ];
        $totalBanners = count($banners);
        $isFlashSaleActive = $showFlashSale ?? true;
        $flashSaleEndTime = $flashSaleEndTime ?? date('Y-m-d\TH:i:s', strtotime('+2 hours 30 minutes'));
        // Xử lý phân quyền từ Session Laravel
        $currentController = Request()->route()->getActionname(); // Hoặc lấy tên controller hiện tại
        $currentRole = session('user_role', ''); // Role từ MySQL: 0 (Customer), 1 (Admin), 2 (Staff)
        $isAdminArea = ($currentRole == 1);
        $hideSidebar = $isAdminArea; // Tùy chỉnh ẩn hiện sidebar theo ý bạn
        $userFullName = session('user_name', '');
        $userEmail = session('user_email', 'user.email@gmail.com');
        $userPhone = session('user_phone', '0901 234 567');
        $userAddress = session('user_address', 'Nhập địa chỉ');
        $avatarLetter = "U";
        $avatarBgColor = "#e74c3c";
        if (!empty($userFullName)) {
            $nameParts = explode(' ', trim($userFullName));
            $displayFirstName = end($nameParts);
            $avatarLetter = strtoupper(mb_substr($displayFirstName, 0, 1));
            $bgColors = ["#e74c3c", "#3498db", "#2ecc71", "#f1c40f", "#9b59b6", "#1abc9c", "#e67e22", "#34495e", "#ff7979", "#badc58", "#005a54"];
            $colorIndex = abs(crc32($userFullName)) % count($bgColors);
            $avatarBgColor = $bgColors[$colorIndex];
        }
    @endphp

    <!-- NÚT MENU CỐ ĐỊNH -->
    @if (!empty($hideSidebar) && !$hideSidebar)
        <button id="menuToggleBtn" class="fixed-menu-btn" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>
    @endif

    <!-- HEADER -->
    <header class="header-container">
        <div class="header-left">
            <a href="{{ url('/foods') }}" class="logo">
                FASTFOOD STORE
            </a>
            @if (!$isAdminArea)
                <div class="delivery-location" onclick="openAddressModal()">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Giao đến:</span>
                    <strong id="displayAddress">{{ $userAddress }}</strong>
                </div>
            @endif
        </div>
        {{-- Thanh tìm kiếm chỉ hiển thị ở trang chủ --}}
        @if (request()->is('foods', '/') && !$isAdminArea)
            <div class="header-center">
                <form action="{{ url('/foods') }}" method="GET">
                    <input type="text"
                        name="search"
                        class="search-input"
                        placeholder="Tìm món ăn, nước uống..."
                        value="{{ request('search') }}">
                    <button type="submit" class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>
        @endif
        <div class="header-right">
            @if (!empty($userFullName))
                <div class="user-profile" onclick="toggleUserDropdown(event)">
                    <div class="header-avatar" style="background-color: {{ $avatarBgColor }};">
                        {{ $avatarLetter }}
                    </div>
                        <span>{{ mb_strtoupper($userFullName, 'UTF-8') }}</span>
                    <div class="user-dropdown-panel" id="userDropdownPanel">
                        <div class="dropdown-header">
                            <div class="dropdown-avatar" style="background-color: {{ $avatarBgColor }};">
                                {{ $avatarLetter }}
                            </div>
                            <div class="dropdown-user-info">
                                <h4>{{ $userFullName }}</h4>
                                <p>{{ $userPhone }}</p>
                                <p>{{ $userEmail }}</p>
                            </div>
                        </div>
                        <div class="dropdown-section-title">Tài khoản khách hàng</div>
                        <ul class="dropdown-menu-list">
                            <li>
                                <a href="javascript:void(0);" onclick="checkCustomerPermission(event, '{{ url('/orders') }}')">
                                    <span><i class="fa-solid fa-receipt icon-left"></i> Đơn hàng của tôi</span>
                                    <i class="fa-solid fa-chevron-right icon-right"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="checkCustomerPermission(event, '{{ url('/cart') }}')">
                                    <span><i class="fa-solid fa-cart-shopping icon-left"></i> Giỏ hàng</span>
                                    <i class="fa-solid fa-chevron-right icon-right"></i>
                                </a>
                            </li>
                        </ul>
                        <div class="dropdown-section-title">Tài khoản</div>
                        <ul class="dropdown-menu-list">
                            <li>
                                <a href="{{ url('/logout') }}">
                                    <span><i class="fa-solid fa-right-from-bracket icon-left" style="color: #e74c3c;"></i> Đăng xuất</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <a href="{{ url('/login') }}" class="user-auth-btn">
                    <i class="fa-regular fa-user" style="margin-right: 5px;"></i> Đăng nhập
                </a>
            @endif
        </div>
    </header>
    <div class="wrapper">
        @if ($isAdminArea)
            @include('layouts.sidebar_admin')
        @else
            @include('layouts.sidebar')
        @endif

        <!-- NỘI DUNG CHÍNH -->
        <main class="main-content {{ $isAdminArea ? 'admin-main-content' : '' }}" id="appContent">
            @yield('content')
        </main>
    </div>

    <!-- MODAL NHẬP ĐỊA CHỈ -->
    @if (!$isAdminArea)
        <div class="modal-overlay" id="addressModal">
            <div class="address-modal">
                <div class="address-modal-header">
                    <span>Chọn địa chỉ giao hàng</span>
                    <button class="close-modal" onclick="closeAddressModal()"><i class="fa-solid fa-times"></i></button>
                </div>
                <div class="address-input-wrapper">
                    <i class="fa-solid fa-location-dot"></i>
                    <input type="text" class="address-input" id="addressInput" placeholder="Nhập địa chỉ giao hàng của bạn..." autocomplete="off">
                </div>
                <button type="button" class="btn-save-address" onclick="saveNewAddress()">LƯU ĐỊA CHỈ</button>
            </div>
        </div>
    @endif
    @if (session()->has('order_success'))
        @php $orderSuccess = session('order_success'); @endphp
        <div class="order-success-overlay" id="orderSuccessModal" role="dialog" aria-modal="true" aria-labelledby="orderSuccessTitle">
            <div class="order-success-modal">
                <div class="order-success-body">
                    <div class="order-success-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h2 id="orderSuccessTitle">Đặt hàng thành công!</h2>
                    <p>Đơn hàng của bạn đã được tiếp nhận.<br>Hãy theo dõi trạng thái giao hàng trong mục đơn hàng.</p>
                    <div class="order-success-summary">
                        <div>
                            <small>Mã đơn hàng</small>
                            <strong>{{ $orderSuccess['code'] }}</strong>
                        </div>
                        <div>
                            <small>Tổng thanh toán</small>
                            <strong>{{ $orderSuccess['total'] }}</strong>
                        </div>
                    </div>
                    <div class="order-success-actions">
                        <button type="button" onclick="window.location.href='{{ url('/foods') }}'">Tiếp tục mua</button>
                        <a href="{{ route('customer.orders.index') }}">
                            Xem đơn hàng <i class="fa-solid fa-arrow-right" style="margin-left:7px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- FOOTER -->
    <footer class="footer">
        <p><strong>FASTFOOD DELIVERY SYSTEM</strong> - Đồ án Quản lý dịch vụ giao thức ăn nhanh</p>
        <p><i class="fa-solid fa-location-dot" style="color: #ff9f43; margin-right: 5px;"></i> Địa chỉ: 140 Lê Trọng Tấn, Phường Tây Thạnh, TP.HCM</p>
        <p><i class="fa-solid fa-phone" style="color: #ff9f43; margin-right: 5px;"></i> Liên hệ: 0901 234 567 | <i class="fa-solid fa-envelope" style="color: #ff9f43; margin-right: 5px; margin-left: 10px;"></i> Email: support@fastfoodstore.vn</p>
    </footer>

    <!-- SCRIPT XỬ LÝ GIAO DIỆN -->
    <script>

        function showSiteToast(message, type = 'success') {
            const existingToast = document.querySelector('.site-toast');
            if (existingToast) existingToast.remove();
            const toast = document.createElement('div');
            toast.className = `site-toast ${type === 'error' ? 'error' : (type === 'info' ? 'info' : '')}`;
            const icon = type === 'error'
                ? 'fa-circle-exclamation'
                : (type === 'info' ? 'fa-circle-info' : 'fa-check');
            const title = type === 'error'
                ? 'Có lỗi xảy ra'
                : (type === 'info' ? 'Thông báo' : 'Thành công');
            toast.innerHTML = `
                <div class="site-toast-icon"><i class="fa-solid ${icon}"></i></div>
                <div class="site-toast-content">
                    <strong>${title}</strong>
                    <span></span>
                </div>
                <button type="button" class="site-toast-close" aria-label="Đóng thông báo">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            toast.querySelector('.site-toast-content span').textContent = message;
            document.body.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('active'));
            const removeToast = () => {
                toast.classList.remove('active');
                setTimeout(() => toast.remove(), 220);
            };
            toast.querySelector('.site-toast-close').addEventListener('click', removeToast);
            toast._hideTimer = setTimeout(removeToast, 3500);
        }

        function checkCustomerPermission(event, targetUrl) {
            event.preventDefault();
            var isAdmin = @json($isAdminArea);
            if (isAdmin) {
                alert("Chỉ khách hàng mới được thao tác");
            } else {
                window.location.href = targetUrl;
            }
        }

        function toggleSidebar() {
            var sidebar = document.getElementById("appSidebar");
            var content = document.getElementById("appContent");
            var button = document.getElementById("menuToggleBtn");
            if (!sidebar || !content) return;
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("expanded", sidebar.classList.contains("collapsed"));
            if (button) button.classList.toggle("faded");
        }

        function toggleUserDropdown(event) {
            event.stopPropagation();
            let panel = document.getElementById("userDropdownPanel");
            if (panel) panel.classList.toggle("active");
        }
        window.onclick = function(event) {
            if (!event.target.closest('.user-profile')) {
                let dropdowns = document.getElementsByClassName("user-dropdown-panel");
                for (let i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('active')) {
                        dropdowns[i].classList.remove('active');
                    }
                }
            }
        }

        function openAddressModal() {
            if (!@json(session()->has('user_email'))) {
                showSiteToast('Vui lòng đăng nhập để lưu địa chỉ.', 'info');
                return;
            }
            let modal = document.getElementById("addressModal");
            if (modal) modal.classList.add("active");
            let input = document.getElementById("addressInput");
            let currentAddr = document.getElementById("displayAddress") ? document.getElementById("displayAddress").innerText.trim() : "";
            if (input) {
                input.value = (currentAddr !== "Nhập địa chỉ") ? currentAddr : "";
                setTimeout(() => input.focus(), 100);
            }
        }

        function closeAddressModal() {
            let modal = document.getElementById("addressModal");
            if (modal) modal.classList.remove("active");
        }

        function closeOrderSuccessModal() {
            let modal = document.getElementById("orderSuccessModal");
            if (modal) modal.classList.remove("active");
        }
        document.addEventListener('DOMContentLoaded', function () {
            let orderSuccessModal = document.getElementById("orderSuccessModal");
            if (orderSuccessModal) {
                orderSuccessModal.classList.add("active");
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeOrderSuccessModal();
            }
        });

        async function saveNewAddress() {
            const input = document.getElementById('addressInput');
            const button = document.querySelector('.btn-save-address');
            const address = input?.value.trim();
            if (!address) {
                showSiteToast('Vui lòng nhập địa chỉ!', 'error');
                return;
            }
            if (button.disabled) return;
            button.disabled = true;
            button.textContent = 'ĐANG LƯU...';
            try {
                const response = await fetch(@json(route('customer.update-address')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({ address })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.errors?.address?.[0] || data.message || 'Không thể lưu địa chỉ.');
                }
                const display = document.getElementById('displayAddress');
                if (display) display.textContent = data.address;
                const checkout = document.querySelector('[name="customer_address"]');
                if (checkout) checkout.value = data.address;
                closeAddressModal();
                showSiteToast('Đã cập nhật địa chỉ giao hàng.', 'success');
            } catch (error) {
                showSiteToast(error.message || 'Không thể lưu địa chỉ.', 'error');
            } finally {
                button.disabled = false;
                button.textContent = 'LƯU ĐỊA CHỈ';
            }
        }
    </script>
</body>
</html>
