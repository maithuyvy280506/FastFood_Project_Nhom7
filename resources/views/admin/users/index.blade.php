@extends('layouts.app')

@section('content')
<div class="admin-page">
    @if(session('success'))
        <div class="admin-alert">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="admin-alert admin-alert-error">{{ $errors->first() }}</div>
    @endif

    <section class="admin-panel table-panel">
        <div class="admin-list-toolbar">
            <h1>DANH SÁCH NHÂN VIÊN</h1>
            <a href="{{ route('admin.users.create') }}" class="admin-button"><i class="fa-solid fa-plus"></i> Thêm nhân viên</a>
            <div class="status-tabs">
                <button type="button" class="status-tab is-active">Tất cả</button>
                <button type="button" class="status-tab">Active</button>
                <button type="button" class="status-tab">Inactive</button>
            </div>
        </div>
        <div class="admin-table-scroll">
            <table class="admin-table staff-table">
                <thead><tr><th>Mã người dùng</th><th>Tên đăng nhập</th><th>Họ và tên</th><th>SĐT</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><strong class="accent-text">U{{ str_pad((string) $user->id_user, 3, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ \Illuminate\Support\Str::before($user->email, '@') }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->phone ?: '—' }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="role-pill">{{ $user->role === 1 ? 'Admin' : 'Staff' }}</span></td>
                            <td><span class="status-pill {{ $user->status ? 'status-active' : 'status-inactive' }}">{{ $user->status ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $user->created_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="staff-actions">
                                @if($user->role === 2)
                                    <a href="{{ route('admin.users.edit', $user->id_user) }}" class="action-button action-edit">Sửa</a>
                                    <form action="{{ route('admin.users.toggle-status', $user->id_user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="action-button {{ $user->status ? 'action-lock' : 'action-unlock' }}">{{ $user->status ? 'Khóa' : 'Mở' }}</button>
                                    </form>
                                @else
                                    <span>—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="empty-state">Chưa có nhân viên. Chọn “Thêm nhân viên” để tạo tài khoản đầu tiên.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination"><span>{{ $users->count() }} nhân viên</span></div>
    </section>
</div>
@endsection
