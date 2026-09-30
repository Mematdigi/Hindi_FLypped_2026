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
        .alert { padding:10px 15px; margin-bottom:20px; border-radius:5px; border:1px solid transparent; }
        .alert-danger { color:#721c24; background:#f8d7da; border-color:#f5c6cb; }
        .alert-success { color:#155724; background:#d4edda; border-color:#c3e6cb; }
        .form-group { margin-bottom:20px; }
        .form-control { width:100%; padding:12px 15px; border:1px solid #ddd; border-radius:8px; font-size:14px; transition:border-color .3s; }
        .form-control:focus { outline:none; border-color:#667eea; box-shadow:0 0 0 2px rgba(102,126,234,.2); }
        .form-control[readonly] { background:#f3f4fb; color:#6c757d; cursor:not-allowed; }
        .btn-primary { background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); border:none; color:#fff; padding:12px 30px; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; width:100%; transition:transform .2s; }
        .btn-primary:disabled { opacity:.6; cursor:not-allowed; transform:none; }
        .text-muted { color:#6c757d; text-align:center; margin-top:20px; font-size:14px; }
        .otp-note { background:#f3f4fb; border:1px solid #e0e3f5; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px; color:#495057; text-align:center; }
        .otp-note strong { color:#667eea; }
        .otp-row { display:flex; gap:8px; justify-content:center; }
        .otp-box { width:44px; height:48px; border:1px solid #ddd; border-radius:8px; text-align:center; font-size:20px; font-weight:700; outline:none; }
        .otp-box:focus { border-color:#667eea; box-shadow:0 0 0 2px rgba(102,126,234,.2); }
        .input-group { display: flex; align-items: stretch; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="<?php echo base_url('public/assets/images/Logo.png'); ?>" alt="Flypped Logo">
                <p class="text-muted mt-2">Sign in to your account</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="alert alert-danger" id="msgErr" style="display:none;"></div>
            <div class="alert alert-success" id="msgOk" style="display:none;"></div>

            <form method="POST" action="<?= base_url('login') ?>" id="loginForm" autocomplete="off">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group" style="display:flex;">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" style="border-radius:8px 0 0 8px;" required>
                        <button class="btn btn-outline-secondary" style="border:1px solid #ddd; border-left:none; border-radius:0 8px 8px 0; background:#fff;" type="button" id="toggleLoginPassword">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div id="otpSection" style="display:none;">
                    <div class="otp-note" id="otpNote"><i class="fa fa-envelope"></i>&nbsp; OTP sent to the <strong>registered admin email</strong></div>
                    <div class="form-group">
                        <label style="display:block;text-align:center;">Enter 6-digit OTP</label>
                        <div class="otp-row mt-2">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric" autocomplete="one-time-code">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                            <input class="otp-box" type="text" maxlength="1" inputmode="numeric">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="mainBtn">Send OTP</button>
            </form>

            <div class="text-muted">
                <small>Role-based access control enabled</small>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('public/assets'); ?>/vendors/js/vendor.bundle.base.js"></script>

    <script>
        document.getElementById('toggleLoginPassword').addEventListener('click', function() {
            const p = document.getElementById('password'), i = this.querySelector('i');
            const show = p.type === 'password';
            p.type = show ? 'text' : 'password';
            i.classList.toggle('fa-eye', !show);
            i.classList.toggle('fa-eye-slash', show);
        });

        const $ = id => document.getElementById(id);
        const form = $('loginForm'), email = $('email'), pass = $('password'), btn = $('mainBtn');
        const boxes = Array.from(document.querySelectorAll('.otp-box'));
        let otpSent = false;

        const msg = (type, text) => {
            $('msgErr').style.display = $('msgOk').style.display = 'none';
            if (!text) return;
            const el = type === 'err' ? $('msgErr') : $('msgOk');
            el.textContent = text; el.style.display = 'block';
        };

        const getOtp = () => boxes.map(b => b.value).join('');

        // Step 1: Send OTP
        async function sendOtp() {
            if (!email.value || !pass.value) {
                return msg('err', 'Please fill your Email and Password');
            }

            btn.disabled = true; btn.textContent = 'Sending...'; msg();

            try {
                const res = await fetch('<?= base_url("login/send-otp") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ email: email.value.trim() })
                });
                const data = await res.json();

                if (data.success) {
                    otpSent = true;
                    email.readOnly = pass.readOnly = true;
                    $('otpSection').style.display = 'block';

                    // Only when OTP_BYPASS = true on the server: show and auto-fill the OTP
                    if (data.otp) {
                        const code = String(data.otp);
                        code.split('').forEach((c, j) => boxes[j] && (boxes[j].value = c));
                        $('otpNote').innerHTML = '<i class="fa fa-key"></i>&nbsp; Test mode OTP: <strong>' + code + '</strong>';
                        msg('ok', 'OTP filled in. Click "Verify & Sign in".');
                        btn.disabled = false;
                        btn.textContent = 'Verify & Sign in';
                    } else {
                        msg('ok', 'OTP sent to the registered admin email.');
                        btn.textContent = 'Verifying...';
                        boxes[0].focus();
                    }
                } else {
                    msg('err', data.message || 'Failed to send OTP.');
                    btn.disabled = false; btn.textContent = 'Send OTP';
                }
            } catch (e) {
                msg('err', 'Network error. Please try again.');
                btn.disabled = false; btn.textContent = 'Send OTP';
            }
        }

        // Step 2: Verify OTP
        async function verifyOtp() {
            const code = getOtp();
            if (code.length !== 6) return;

            btn.disabled = true; msg();

            try {
                const res = await fetch('<?= base_url("login/verify-otp") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ email: email.value.trim(), otp_code: code })
                });
                const data = await res.json();

                if (data.success) {
                    msg('ok', 'OTP verified. Signing in...');
                    // OTP is valid! Submit the form normally to CodeIgniter's processLogin
                    form.submit(); 
                } else {
                    msg('err', data.message || 'Invalid OTP');
                    boxes.forEach(b => b.value = '');
                    boxes[0].focus();
                    btn.disabled = false;
                }
            } catch (e) {
                msg('err', 'Network error.');
                btn.disabled = false;
            }
        }

        // Intercept Form Submit
        form.addEventListener('submit', e => {
            e.preventDefault();
            if (!otpSent) {
                sendOtp();
            } else {
                verifyOtp(); 
            }
        });

        // OTP Box Logic
        boxes.forEach((b, i) => {
            b.addEventListener('input', () => {
                b.value = b.value.replace(/\D/g, '').slice(-1);
                if (b.value && i < 5) boxes[i + 1].focus();
                if (getOtp().length === 6) verifyOtp();
            });
            b.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !b.value && i > 0) { boxes[i - 1].focus(); boxes[i - 1].value = ''; }
            });
            b.addEventListener('paste', e => {
                e.preventDefault();
                const v = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                v.split('').forEach((c, j) => boxes[j] && (boxes[j].value = c));
                if (v.length === 6) verifyOtp();
            });
        });
    </script>
</body>
</html>