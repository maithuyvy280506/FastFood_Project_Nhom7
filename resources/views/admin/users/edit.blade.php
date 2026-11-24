@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel admin-form-panel">
        <h1>CẬP NHẬT THÔNG TIN NHÂN VIÊN</h1>
        <div class="admin-form-grid">
            <label>Mã người dùng<input class="admin-input is-readonly" value="U{{ str_pad((string) $user->id_user, 3, '0', STR_PAD_LEFT) }}" readonly></label>
            <label>Tên đăng nhập<input class="admin-input is-readonly" value="{{ \Illuminate\Support\Str::before($user->email, '@') }}" readonly></label>
            <label>Họ và tên<input class="admin-input is-readonly" value="{{ $user->name }}" readonly></label>
            <label>SĐT<input class="admin-input is-readonly" value="{{ $user->phone ?: '—' }}" readonly></label>
            <label>Email<input class="admin-input is-readonly" value="{{ $user->email }}" readonly></label>
            <label>Vai trò<select class="admin-input" disabled><option>Staff</option></select></label>
            <label>Trạng thái<select class="admin-input" disabled><option>{{ $user->status ? 'Active - Hoạt động' : 'Inactive - Ngừng hoạt động' }}</option></select></label>
        </div>
        <p class="form-demo-note">Màn hình chỉnh sửa hiện dựng giao diện theo mẫu; cập nhật thông tin chưa được bật.</p>
        <div class="admin-form-actions">
            <button type="button" class="admin-button" disabled>LƯU THAY ĐỔI</button>
            <a href="{{ route('admin.users.index') }}" class="admin-button admin-button-muted">HỦY BỎ</a>
        </div>
    </section>
</div>
@endsection
