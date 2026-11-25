@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel" style="max-width:700px;margin:auto;padding:24px;">
        <h1 style="color:#e74c3c;">THÊM NHÂN VIÊN MỚI</h1>
        <form method="POST" action="{{ route('admin.users.store') }}" style="display:grid;gap:14px;margin-top:18px;">
            @csrf
            <input type="text" value="{{ $nextUserCode }}" readonly aria-label="Mã nhân viên" style="padding:10px;">
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Họ và tên" required style="padding:10px;">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required style="padding:10px;">
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại" style="padding:10px;">
            <input type="password" name="password" placeholder="Mật khẩu" minlength="6" required style="padding:10px;">
            <button type="submit" class="admin-button">Thêm nhân viên</button>
        </form>
    </section>
</div>
@endsection
