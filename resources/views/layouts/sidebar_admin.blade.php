@php
    $currentRole = session('user_role'); // Phân quyền từ Session Laravel (Ví dụ: 'Admin' hoặc số 1 tùy cách lưu)
    $isAdmin = (string)$currentRole === 'Admin' || $currentRole == 1;
@endphp

<aside class="sidebar admin-sidebar" id="appSidebar">
    <!-- TỔNG QUAN -->
    <div class="sidebar-section-title">TỔNG QUAN</div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ url('/admin/dashboard') }}" class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Quản lý cửa hàng</span>
            </a>
        </li>
    </ul>

    <!-- QUẢN LÝ HỆ THỐNG -->
    <div class="sidebar-section-title">QUẢN LÝ HỆ THỐNG</div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ url('/admin/customers') }}" class="{{ request()->is('admin/customers*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Quản lý khách hàng</span>
            </a>
        </li>

        @if ($isAdmin)
            <li>
                <a href="{{ url('/admin/users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Quản lý nhân viên</span>
                </a>
            </li>
        @endif

        <li>
            <a href="{{ url('/admin/foods/flash-sale') }}" class="{{ request()->is('admin/foods*') ? 'active' : '' }}">
                <i class="fa-solid fa-utensils"></i>
                <span>Quản lý món ăn</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Quản lý đơn hàng</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/drivers') }}" class="{{ request()->is('admin/drivers*') ? 'active' : '' }}">
                <i class="fa-solid fa-motorcycle"></i>
                <span>Quản lý tài xế</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/admin/promotions') }}" class="{{ request()->is('admin/promotions*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i>
                <span>Quản lý khuyến mãi</span>
            </a>
        </li>
    </ul>

    <!-- ĐIỀU HƯỚNG -->
    <div class="sidebar-section-title">ĐIỀU HƯỚNG</div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ url('/foods') }}">
                <i class="fa-solid fa-store"></i>
                <span>Xem cửa hàng</span>
            </a>
        </li>
    </ul>
</aside>