<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3 class="text-center">Verify OTP</h3>
                <form action="<?= base_url('/verify-otp') ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Verify</button>
                    </div>
                </form>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger mt-3"><?= session()->getFlashdata('error'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
