@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel" style="max-width:700px;margin:auto;padding:24px;">
        <h1 style="color:#e74c3c;">CẬP NHẬT THÔNG TIN NHÂN VIÊN</h1>
        <form method="POST" action="{{ url('/admin/users') }}" style="display:grid;gap:14px;margin-top:18px;">
            @csrf
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="padding:10px;">
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="padding:10px;">
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" style="padding:10px;">
            <input type="password" name="password" minlength="6" placeholder="Mật khẩu mới (nếu thay đổi)" style="padding:10px;">
            <button type="submit" class="admin-button">Cập nhật</button>
        </form>
    </section>
</div>
@endsection
