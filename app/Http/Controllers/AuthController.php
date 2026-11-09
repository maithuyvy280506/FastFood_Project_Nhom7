<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Hiển thị trang đăng nhập
    public function index()
    {
        return view('auth.login');
    }

    // XỬ LÝ ĐĂNG NHẬP
    public function login(Request $request)
    {
        $request->validate([
            'Username' => 'required',
            'Password' => 'required|min:6' // Đảm bảo mật khẩu đăng nhập ít nhất 6 ký tự
        ], [
            'Password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.'
        ]);

        $email = trim($request->Username);
        
        // Tìm user trong DB theo email
        $user = DB::table('users')->where('email', $email)->first();

        if ($user) {
            // Kiểm tra mật khẩu (Sử dụng Hash thay vì so sánh chuỗi thô)
            if (!Hash::check($request->Password, $user->password)) {
                return back()->with('error', 'Mật khẩu không đúng.');
            }

            // Kiểm tra trạng thái khóa (1: Active, 0: Locked)
            if ($user->status == 0) {
                return back()->with('error', 'Tài khoản của bạn đã bị khóa.');
            }

            // Lưu thông tin vào Session
            session([
                'user_id' => $user->id_user,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $user->phone,      
                'user_address' => $user->address,   
                'user_role' => $user->role, // 1: Admin, 0: Customer
            ]);

            // Phân quyền chuyển hướng
            if ($user->role == 1) {
                return redirect('/admin'); 
            }

            return redirect('/foods');
        }

        return back()->with('error', 'Email không tồn tại trong hệ thống.');
    }

    // XỬ LÝ ĐĂNG KÝ
    public function register(Request $request)
    {
        // Kiểm tra độ dài mật khẩu đăng ký tối thiểu 6 ký tự
        if (strlen($request->Password) < 6) {
            return back()->with('error', 'Mật khẩu đăng ký phải có ít nhất 6 ký tự.');
        }

        if ($request->Password !== $request->ConfirmPassword) {
            return back()->with('error', 'Mật khẩu xác nhận không khớp.');
        }

        $email = trim($request->Email);

        // Kiểm tra trùng email
        $exists = DB::table('users')->where('email', $email)->exists();
        if ($exists) {
            return back()->with('error', 'Email đã tồn tại trong hệ thống.');
        }

        // Thêm tài khoản mới (Khách hàng mặc định role = 0, status = 1)
        DB::table('users')->insert([
            'name' => trim($request->FullName),
            'email' => $email,
            'phone' => $request->Phone,
            'password' => Hash::make($request->Password), // Mã hóa mật khẩu
            'role' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }

    // XỬ LÝ QUÊN MẬT KHẨU
    public function forgotPassword(Request $request)
    {
        // Kiểm tra độ dài mật khẩu mới tối thiểu 6 ký tự
        if (strlen($request->NewPassword) < 6) {
            return back()->with('error', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
        }

        if ($request->NewPassword !== $request->ConfirmNewPassword) {
            return back()->with('error', 'Mật khẩu mới không khớp.');
        }

        $email = trim($request->Email);
        $user = DB::table('users')->where('email', $email)->first();

        if ($user) {
            // Cập nhật mật khẩu mới
            DB::table('users')
                ->where('email', $email)
                ->update(['password' => Hash::make($request->NewPassword), 'updated_at' => now()]);

            return back()->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');
        }

        return back()->with('error', 'Email không tồn tại trong hệ thống.');
    }

    // ĐĂNG XUẤT
    public function logout()
    {
        session()->flush();
        return redirect('/login');
    }
}