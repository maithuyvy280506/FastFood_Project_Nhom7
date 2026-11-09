<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Đăng nhập - FastFood Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-color: #EFEAE4; --card-bg: #FAF7F2; --primary-brown: #e74c3c; --primary-hover: #c0392b; --text-dark: #e74c3c; --text-muted: #e74c3c; --border-color: #e74c3c; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-color); display: flex; justify-content: center; align-items: center; min-height: 100vh; position: relative; overflow: hidden; }
        body::after { content: ''; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.35); z-index: 0; pointer-events: none; }
        .page-bg-img { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; object-fit: cover; filter: blur(8px) brightness(0.9); transform: scale(1.05); z-index: -1; }
        .container { position: relative; width: 850px; height: 540px; background-color: var(--card-bg); border-radius: 24px; box-shadow: 0 0 20px rgba(255, 255, 255, 0.4); overflow: hidden; display: flex; z-index: 1; }
        .forms-wrapper { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; }
        .form-box { width: 50%; height: 100%; padding: 35px 45px; display: flex; flex-direction: column; justify-content: center; transition: all 0.6s ease-in-out; opacity: 0; z-index: 1; pointer-events: none; }
        .form-box.login-box { position: absolute; right: 0; opacity: 1; z-index: 2; pointer-events: all; }
        .form-box.register-box, .form-box.forgot-box { position: absolute; left: 0; }
        .container.mode-register .login-box, .container.mode-register .forgot-box { opacity: 0; z-index: 1; pointer-events: none; }
        .container.mode-register .register-box { opacity: 1; z-index: 2; pointer-events: all; }
        .container.mode-forgot .login-box, .container.mode-forgot .register-box { opacity: 0; z-index: 1; pointer-events: none; }
        .container.mode-forgot .forgot-box { opacity: 1; z-index: 2; pointer-events: all; }
        h2 { font-family: 'Playfair Display', serif; font-size: 30px; color: var(--text-dark); margin-bottom: 4px; }
        .subtitle { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; }
        .alert { padding: 10px 12px; border-radius: 8px; margin-bottom: 18px; font-size: 12px; display: flex; align-items: center; gap: 8px; font-weight: 500; }
        .alert-error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .alert-success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; }
        .input-group { position: relative; margin-bottom: 18px; }
        .input-group i { position: absolute; left: 0; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; }
        .input-group input { width: 100%; padding: 8px 10px 8px 28px; border: none; border-bottom: 1px solid var(--border-color); background: transparent; outline: none; font-size: 13px; color: var(--text-dark); transition: border-color 0.3s; }
        .input-group input:focus { border-bottom-color: var(--primary-brown); }
        .input-group input::placeholder { color: #e74c3c; opacity: 0.8; }
        .options { display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: var(--text-muted); margin-bottom: 22px; }
        .forgot-pass { color: var(--text-muted); text-decoration: none; font-weight: 500; text-align: right; }
        .forgot-pass:hover { text-decoration: underline; }
        .btn-submit { width: 100%; padding: 13px; background-color: var(--primary-brown); color: #ffffff; border: none; border-radius: 25px; font-size: 13px; font-weight: 600; letter-spacing: 1px; cursor: pointer; transition: background 0.3s; margin-top: 5px; }
        .btn-submit:hover { background-color: var(--primary-hover); }
        .toggle-text { text-align: center; margin-top: 20px; font-size: 12px; color: var(--text-muted); }
        .toggle-text a { color: var(--text-dark); font-weight: 600; text-decoration: none; margin-left: 4px; }
        .toggle-text a:hover { text-decoration: underline; }
        .overlay-container { position: absolute; top: 0; left: 0; width: 50%; height: 100%; overflow: hidden; transition: transform 0.6s ease-in-out; z-index: 5; }
        .container.mode-register .overlay-container, .container.mode-forgot .overlay-container { transform: translateX(100%); }
        .overlay-bg { position: relative; width: 100%; height: 100%; padding: 40px; display: flex; flex-direction: column; justify-content: space-between; color: #ffffff; }
        .overlay-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; opacity: 0; transition: opacity 0.6s ease-in-out; }
        .img-login { opacity: 1; }
        .container.mode-register .img-login, .container.mode-forgot .img-login { opacity: 0; }
        .container.mode-register .img-register { opacity: 1; }
        .container.mode-forgot .img-forgot { opacity: 1; }
        .overlay-bg::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6)); z-index: 1; }
        .banner-text-box { position: absolute; bottom: 40px; left: 40px; right: 40px; opacity: 0; z-index: 2; transition: opacity 0.6s ease-in-out; }
        .text-login { opacity: 1; }
        .container.mode-register .text-login, .container.mode-forgot .text-login { opacity: 0; }
        .container.mode-register .text-register { opacity: 1; }
        .container.mode-forgot .text-forgot { opacity: 1; }
        .banner-text-box h1 { font-family: 'Playfair Display', serif; font-size: 30px; line-height: 1.2; margin-bottom: 8px; }
        .banner-text-box p { font-size: 12px; opacity: 0.9; line-height: 1.5; font-weight: 300; }
    </style>
</head>
<body>
    <img src="{{ asset('https://channel.vcmedia.vn/prupload/439/2017/01/img20170115215316777.jpg') }}" alt="Background" class="page-bg-img">

    <div class="container" id="container">
        <div class="overlay-container">
            <div class="overlay-bg">
                <img src="{{ asset('https://channel.vcmedia.vn/prupload/439/2017/01/img20170115215316777.jpg') }}" class="overlay-img img-login" alt="Matcha Milk Tea">
                <img src="{{ asset('https://lypham.vn/wp-content/uploads/2024/10/cach-nau-tra-sua-tran-chau-duong-den.jpg') }}" class="overlay-img img-register" alt="Brown Sugar Boba">
                <img src="{{ asset('https://bizweb.dktcdn.net/100/004/714/articles/tra-sua-kem-trung-dua-nuong-2.png?v=1645148134910') }}" class="overlay-img img-forgot" alt="Egg Cream Milk Tea">

                <div class="banner-text-box text-login">
                    <h1>Matcha Latte</h1>
                    <p>Trà sữa Matcha thơm lừng đậm vị trà Uji Nhật Bản, hòa quyện cùng lớp sữa tươi béo ngậy thanh mát.</p>
                </div>
                <div class="banner-text-box text-register">
                    <h1>Brown Sugar</h1>
                    <p>Trân châu đường đen dẻo mềm, đượm vị ngọt ngào caramel quyện cùng sữa tươi trọn vị thơm ngon.</p>
                </div>
                <div class="banner-text-box text-forgot">
                    <h1>Egg Cream Tea</h1>
                    <p>Lớp kem trứng nướng sánh mịn, béo ngậy cháy xém nhẹ kết hợp cùng nền trà đậm vị tạo nét cuốn hút.</p>
                </div>
            </div>
        </div>

        <div class="forms-wrapper">
            <!-- LOGIN -->
            <div class="form-box login-box">
                <h2>Chào mừng</h2>
                <p class="subtitle">Đăng nhập vào tài khoản của bạn</p>
                
                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Thêm id="loginForm" để bắt sự kiện kiểm tra mật khẩu -->
                <form id="loginForm" action="{{ url('/login') }}" method="post">
                    @csrf
                    <div class="input-group">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="text" name="Username" placeholder="Email" autocomplete="email" required />
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="loginPassword" name="Password" placeholder="Mật khẩu" required />
                    </div>
                    <!-- Khu vực hiển thị thông báo lỗi client-side cho Login -->
                    <div id="loginError" class="alert alert-error" style="display: none;">
                        <i class="fa-solid fa-circle-exclamation"></i> <span id="loginErrorText"></span>
                    </div>
                    <div class="options">
                        <a href="#" id="toForgot" class="forgot-pass">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
                </form>
                <p class="toggle-text">
                    Chưa có tài khoản? <a href="#" id="toRegister">Tạo tài khoản</a>
                </p>
            </div>

            <!-- REGISTER -->
            <div class="form-box register-box">
                <h2>Tạo tài khoản</h2>
                <p class="subtitle">Đăng ký thành viên mới</p>
                
                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Thêm id="registerForm" để bắt sự kiện kiểm tra mật khẩu -->
                <form id="registerForm" action="{{ url('/register') }}" method="post">
                    @csrf
                    <div class="input-group">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="FullName" placeholder="Họ và tên" required />
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" name="Phone" placeholder="Số điện thoại" required />
                    </div>
                    <div class="input-group">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="Email" placeholder="Email" required />
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="registerPassword" name="Password" placeholder="Mật khẩu" autocomplete="new-password" required />
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-shield-halved"></i>
                        <input type="password" name="ConfirmPassword" placeholder="Xác nhận mật khẩu" autocomplete="new-password" required />
                    </div>
                    <!-- Khu vực hiển thị thông báo lỗi client-side cho Register -->
                    <div id="registerError" class="alert alert-error" style="display: none;">
                        <i class="fa-solid fa-circle-exclamation"></i> <span id="registerErrorText"></span>
                    </div>
                    <button type="submit" class="btn-submit">ĐĂNG KÝ</button>
                </form>
                <p class="toggle-text">
                    Đã có tài khoản? <a href="#" class="toLogin">Đăng nhập</a>
                </p>
            </div>

            <!-- FORGOT PASSWORD -->
            <div class="form-box forgot-box">
                <h2>Đặt lại mật khẩu</h2>
                <p class="subtitle">Nhập thông tin để khôi phục tài khoản</p>
                
                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Thêm id="forgotForm" để bắt sự kiện kiểm tra mật khẩu -->
                <form id="forgotForm" action="{{ url('/forgot-password') }}" method="post">
                    @csrf
                    <div class="input-group">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="Email" placeholder="Email đăng ký" required />
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-key"></i>
                        <input type="password" id="forgotPassword" name="NewPassword" placeholder="Mật khẩu mới" autocomplete="new-password" required />
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-shield-halved"></i>
                        <input type="password" name="ConfirmNewPassword" placeholder="Xác nhận mật khẩu mới" autocomplete="new-password" required />
                    </div>
                    <!-- Khu vực hiển thị thông báo lỗi client-side cho Forgot Password -->
                    <div id="forgotError" class="alert alert-error" style="display: none;">
                        <i class="fa-solid fa-circle-exclamation"></i> <span id="forgotErrorText"></span>
                    </div>
                    <button type="submit" class="btn-submit">XÁC NHẬN</button>
                </form>
                <p class="toggle-text">
                    Quay lại? <a href="#" class="toLogin">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const toRegisterBtn = document.getElementById('toRegister');
        const toForgotBtn = document.getElementById('toForgot');
        const toLoginBtns = document.querySelectorAll('.toLogin');

        toRegisterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove('mode-forgot');
            container.classList.add('mode-register');
        });

        toForgotBtn.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove('mode-register');
            container.classList.add('mode-forgot');
        });

        toLoginBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                container.classList.remove('mode-register', 'mode-forgot');
            });
        });

        // Xử lý giữ nguyên trạng thái panel dựa trên nội dung lỗi từ Server
        @if (session('error'))
            const errorMsg = "{{ session('error') }}";
            if (errorMsg.includes("đã tồn tại") || errorMsg.includes("không khớp") || errorMsg.includes("ký tự")) {
                if (errorMsg.includes("mới")) {
                    container.classList.add('mode-forgot');
                } else if (!errorMsg.includes("đăng nhập") && !errorMsg.includes("tồn tại trong hệ thống")) {
                    container.classList.add('mode-register');
                }
            }
        @endif

        // ========================================================
        // KIỂM TRA ĐỘ DÀI MẬT KHẨU TRÊN CLIENT (TRƯỚC KHI SUBMIT)
        // ========================================================
        
        // 1. Form Đăng nhập
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const password = document.getElementById('loginPassword').value;
            const errorDiv = document.getElementById('loginError');
            const errorText = document.getElementById('loginErrorText');

            if (password.length < 6) {
                e.preventDefault(); // Chặn không cho gửi form lên server
                errorText.innerText = "Mật khẩu phải trên 6 kí tự";
                errorDiv.style.display = 'flex';
            } else {
                errorDiv.style.display = 'none';
            }
        });

        // 2. Form Đăng ký
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('registerPassword').value;
            const errorDiv = document.getElementById('registerError');
            const errorText = document.getElementById('registerErrorText');

            if (password.length < 6) {
                e.preventDefault(); // Chặn không cho gửi form lên server
                errorText.innerText = "Mật khẩu phải trên 6 kí tự";
                errorDiv.style.display = 'flex';
            } else {
                errorDiv.style.display = 'none';
            }
        });

        // 3. Form Quên mật khẩu
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            const password = document.getElementById('forgotPassword').value;
            const errorDiv = document.getElementById('forgotError');
            const errorText = document.getElementById('forgotErrorText');

            if (password.length < 6) {
                e.preventDefault(); // Chặn không cho gửi form lên server
                errorText.innerText = "Mật khẩu phải trên 6 kí tự";
                errorDiv.style.display = 'flex';
            } else {
                errorDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>