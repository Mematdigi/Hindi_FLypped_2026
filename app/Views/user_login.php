<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <!-- ==== User Login/Registration Form ==== -->
    <section class="flyp_login_section">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Form Container -->
                <div class="col-md-6">
                    <div class="card shadow-lg p-4">
                        <div id="registrationForm" class="form-container">
                            <!-- Registration Form -->
                            <h3 class="text-center login_heading">Register</h3>
                            <form action="<?= base_url('/register-user') ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone No.</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <small class="text-muted">Min password length: 6 characters</small>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn log_btn">Sign Up</button>
                                </div>
                            </form>

                            <p class="text-center mt-3">
                                Have an account? 
                                <a href="#" id="showLoginForm" class="text-decoration-none higlight_text">Log in</a>
                            </p>
                        </div>

                        <div id="loginForm" class="form-container d-none">
                            <!-- Login Form -->
                            <h3 class="text-center">Log In</h3>
                            <form action="<?= base_url('/sign-in') ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="mb-3">
                                    <label for="loginEmail" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="loginEmail" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn log_btn ">Log In</button>
                                </div>
                            </form>
                            <p class="text-center mt-3">
                                Don't have an account? 
                                <a href="#" id="showRegistrationForm" class="text-decoration-none higlight_text">Sign up</a>
                            </p>
                        </div>
                    </div>
                </div>

                

            </div>
        </div>
    </section>
    

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>


     
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.min.js"></script>
    
</body>
</html>


<style>
    .flyp_login_section{
        width: 100%;
        height: 100vh;
        padding: 70px 30px;
        background: url(./public/assest/images/Login_background.jpg);
    }
    .form-container {
        animation: fadeIn 0.5s ease;
    }
    .form-container.d-none {
        display: none !important;
    }
    .card {
        border: none;
        border-radius: 10px;
        background-color: #ffdfdf59;
    }
    button {
        border-radius: 30px;
    }
    .text-muted {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .form-label {
        margin-bottom: .5rem;
        text-transform: uppercase;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #ffffff;
    }
    .login_heading{
        text-align: center !important;
        text-transform: uppercase;
        font-size: 42px;
        font-weight: 700;
        color: #ffffff;
    }
    .log_btn{
        background: #7f57ff;
        color: #fff;
        width: 50%;
        text-align: center;
        box-shadow: 3px 3px 7px 4px #3c39473d;
        border: 2px solid #5e34e7;
    }
    .log_btn:hover{
        background: darkblue;
        color: aliceblue;
    }
    .higlight_text{
        font-size: 20px;
        font-weight: 700;
        color: #410fe1;
    }
</style>


<!-- ==== Toggle Forms Script ==== -->
<script>
    document.getElementById('showLoginForm').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('registrationForm').classList.add('d-none');
        document.getElementById('loginForm').classList.remove('d-none');
    });

    document.getElementById('showRegistrationForm').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('loginForm').classList.add('d-none');
        document.getElementById('registrationForm').classList.remove('d-none');
    });
</script>