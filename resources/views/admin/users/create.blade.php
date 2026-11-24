@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel admin-form-panel">
        <h1>THÊM NHÂN VIÊN MỚI</h1>
        @if($errors->any())
            <div class="admin-alert admin-alert-error">{{ $errors->first() }}</div>
        @endif
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="admin-form-grid">
                <label>Mã người dùng<input class="admin-input is-readonly" value="{{ $nextUserCode }}" readonly></label>
                <label>Tên đăng nhập<input class="admin-input" name="username" value="{{ old('username') }}" placeholder="Nhập tên đăng nhập"></label>
                <label>Mật khẩu<input class="admin-input" type="password" name="password" placeholder="Nhập mật khẩu" required></label>
                <label>Họ và tên<input class="admin-input" name="name" value="{{ old('name') }}" placeholder="Nhập họ và tên" required></label>
                <label>SĐT<input class="admin-input" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại"></label>
                <label>Email<input class="admin-input" type="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required></label>
                <label>Vai trò<select class="admin-input" disabled><option>Staff</option></select></label>
                <label>Trạng thái<select class="admin-input" disabled><option>Active - Hoạt động</option></select></label>
            </div>
            <div class="admin-form-actions">
                <button type="submit" class="admin-button">LƯU TẠO MỚI</button>
                <a href="{{ route('admin.users.index') }}" class="admin-button admin-button-muted">HỦY BỎ</a>
            </div>
        </form>
    </section>
</div>
@endsection
