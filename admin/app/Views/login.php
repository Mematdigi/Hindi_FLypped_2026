<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Flypped Admin</title>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/vertical-light-layout/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets'); ?>/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .auth-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); }
        .auth-card { background:#fff; border-radius:15px; padding:40px; box-shadow:0 10px 30px rgba(0,0,0,.1); width:100%; max-width:400px; }
        .auth-logo { text-align:center; margin-bottom:30px; }
        .auth-logo img { max-width:120px; }
        .login-tabs { display:flex; margin-bottom:25px; border-bottom:2px solid #eee; }
        .login-tab { flex:1; text-align:center; padding:10px; cursor:pointer; font-weight:600; color:#6c757d; transition:all .3s; }
        .login-tab.active { color:#667eea; border-bottom:2px solid #667eea; margin-bottom:-2px; }
        .form-section { display:none; }
        .form-section.active { display:block; }
        .alert { padding:10px 15px; margin-bottom:20px; border-radius:5px; display:none; }
        .alert-danger { color:#721c24; background:#f8d7da; border-color:#f5c6cb; display:block; }
        .alert-success { color:#155724; background:#d4edda; border-color:#c3e6cb; }
        .form-group { margin-bottom:20px; }
        .form-control { width:100%; padding:12px 15px; border:1px solid #ddd; border-radius:8px; font-size:14px; }
        .form-control:focus { outline:none; border-color:#667eea; box-shadow:0 0 0 2px rgba(102,126,234,.2); }
        .btn-primary { background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); border:none; color:#fff; padding:12px 30px; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; width:100%; }
        .btn-primary:disabled { opacity:.6; cursor:not-allowed; }
        .otp-row { display:flex; gap:8px; justify-content:center; }
        .otp-box { width:44px; height:48px; border:1px solid #ddd; border-radius:8px; text-align:center; font-size:20px; font-weight:700; outline:none; }
        .otp-box:focus { border-color:#667eea; box-shadow:0 0 0 2px rgba(102,126,234,.2); }
        .text-muted { color:#6c757d; text-align:center; margin-top:20px; font-size:14px; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="<?php echo base_url('public/assets/images/Logo.png'); ?>" alt="Flypped Logo">
                <p class="text-muted mt-2">Sign in to your account</p>
            </div>

            <!-- Tabs -->
            <div class="login-tabs">
                <div class="login-tab active" onclick="switchTab('email')">Email</div>
                <div class="login-tab" onclick="switchTab('mobile')">Mobile OTP</div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" style="display:block;"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="alert alert-danger" id="js-err" style="display:none;"></div>
            <div class="alert alert-success" id="js-ok" style="display:none;"></div>

            <!-- Email Section -->
            <div id="email-section" class="form-section active">
                <form method="POST" action="<?= base_url('login') ?>">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn-primary">Sign In</button>
                </form>
            </div>

            <!-- Mobile OTP Section -->
            <div id="mobile-section" class="form-section">
                <div id="mobileStep">
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text">+91</span>
                            <input type="text" class="form-control" id="mobile" placeholder="Enter 10-digit number" maxlength="10">
                        </div>
                    </div>
                    <button type="button" class="btn-primary" id="btnSendOtp" onclick="sendOtp()">Send OTP</button>
                </div>

                <div id="otpStep" style="display:none;">
                    <div class="form-group" style="text-align:center;">
                        <label>Enter 6-digit OTP sent to <strong id="displayMobile"></strong></label>
                        <div class="otp-row mt-2">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                        </div>
                    </div>
                    <button type="button" class="btn-primary mb-3" id="btnVerifyOtp" onclick="verifyOtp()">Verify & Login</button>
                    <div style="text-align:center;">
                        <a href="javascript:void(0)" onclick="resetMobile()" style="font-size:13px; color:#667eea;">Change Number</a>
                    </div>
                </div>
            </div>

            <div class="text-muted">
                <small>Role-based access control enabled</small>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('public/assets'); ?>/vendors/js/vendor.bundle.base.js"></script>
    <script>
        function switchTab(tab) {
            document.querySelectorAll('.login-tab').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.form-section').forEach(el => el.classList.remove('active'));
            document.getElementById('js-err').style.display = 'none';
            document.getElementById('js-ok').style.display = 'none';
            
            if (tab === 'email') {
                document.querySelector('.login-tab:nth-child(1)').classList.add('active');
                document.getElementById('email-section').classList.add('active');
            } else {
                document.querySelector('.login-tab:nth-child(2)').classList.add('active');
                document.getElementById('mobile-section').classList.add('active');
            }
        }

        const msg = (type, text) => {
            document.getElementById('js-err').style.display = 'none';
            document.getElementById('js-ok').style.display = 'none';
            if (!text) return;
            const el = document.getElementById(type === 'err' ? 'js-err' : 'js-ok');
            el.textContent = text;
            el.style.display = 'block';
        };

        const boxes = Array.from(document.querySelectorAll('.otp-box'));
        const getOtp = () => boxes.map(b => b.value).join('');

        boxes.forEach((b, i) => {
            b.addEventListener('input', () => {
                b.value = b.value.replace(/\D/g, '').slice(-1);
                if (b.value && i < 5) boxes[i + 1].focus();
                if (getOtp().length === 6) verifyOtp();
            });
            b.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !b.value && i > 0) { boxes[i - 1].focus(); boxes[i - 1].value = ''; }
            });
        });

        function resetMobile() {
            document.getElementById('mobileStep').style.display = 'block';
            document.getElementById('otpStep').style.display = 'none';
            boxes.forEach(b => b.value = '');
            msg();
        }

        async function sendOtp() {
            const mobile = document.getElementById('mobile').value;
            if (mobile.length !== 10) return msg('err', 'Enter a valid 10-digit number');

            const btn = document.getElementById('btnSendOtp');
            btn.disabled = true; btn.textContent = 'Sending...'; msg();

            try {
                const res = await fetch('<?= base_url("login/send-otp") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mobile: '+91' + mobile })
                });
                const data = await res.json();
                
                if (data.success) {
                    document.getElementById('mobileStep').style.display = 'none';
                    document.getElementById('otpStep').style.display = 'block';
                    document.getElementById('displayMobile').textContent = '+91 ' + mobile;
                    msg('ok', 'OTP sent successfully');
                    boxes[0].focus();
                } else {
                    msg('err', data.message);
                }
            } catch (e) {
                msg('err', 'Network Error');
            }
            btn.disabled = false; btn.textContent = 'Send OTP';
        }

        async function verifyOtp() {
            const mobile = document.getElementById('mobile').value;
            const otp = getOtp();
            if (otp.length !== 6) return msg('err', 'Enter complete 6-digit OTP');

            const btn = document.getElementById('btnVerifyOtp');
            btn.disabled = true; btn.textContent = 'Verifying...'; msg();

            try {
                const res = await fetch('<?= base_url("login/verify-otp") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mobile: '+91' + mobile, otp_code: otp })
                });
                const data = await res.json();
                
                if (data.success) {
                    msg('ok', 'Verified! Redirecting...');
                    window.location.href = '<?= base_url("dashboard") ?>';
                } else {
                    msg('err', data.message);
                    boxes.forEach(b => b.value = '');
                    boxes[0].focus();
                }
            } catch (e) {
                msg('err', 'Network Error');
            }
            btn.disabled = false; btn.textContent = 'Verify & Login';
        }
    </script>
</body>
</html>