@extends('layouts.app')

@section('content')
<div class="admin-page">
    <section class="admin-panel" style="padding:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h1 style="color:#e74c3c;">DANH SÁCH NHÂN VIÊN</h1>
            <a class="admin-button" href="{{ route('admin.users.create') }}">Thêm nhân viên</a>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;margin-top:18px;">
                <thead>
                    <tr style="border-bottom:1px solid #eee;text-align:left;">
                        <th style="padding:12px;">Mã</th>
                        <th style="padding:12px;">Họ tên</th>
                        <th style="padding:12px;">Email</th>
                        <th style="padding:12px;">Quyền</th>
                        <th style="padding:12px;">Trạng thái</th>
                        <th style="padding:12px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-bottom:1px solid #f1f1f1;">
                            <td style="padding:12px;">{{ 'U'.str_pad((string) $user->id_user, 3, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding:12px;"><strong>{{ $user->name }}</strong></td>
                            <td style="padding:12px;">{{ $user->email }}</td>
                            <td style="padding:12px;">{{ $user->role == 1 ? 'Quản trị viên' : 'Nhân viên' }}</td>
                            <td style="padding:12px;">{{ $user->status == 1 ? 'Hoạt động' : 'Đã khóa' }}</td>
                            <td style="padding:12px;text-align:right;">
                                @if($user->role == 2)
                                    <a class="admin-button" href="{{ route('admin.users.edit', $user->id_user) }}">Sửa</a>
                                    <form method="POST" action="{{ url('/admin/users/'.$user->id_user.'/toggle-status') }}" style="display:inline;">
                                        @csrf
                                        <button class="admin-button" type="submit">{{ $user->status == 1 ? 'Khóa' : 'Mở khóa' }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="padding:24px;text-align:center;">Chưa có nhân viên</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
