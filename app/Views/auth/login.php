<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - QAL DC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/dc.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="dc-card" style="max-width:420px; width:100%;">
        <h1 class="h4 mb-3">Login QAL DC</h1>
        <p class="text-muted">Masuk menggunakan email dan password.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('login') ?>" method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
            </div>
            <button class="btn btn-success w-100" type="submit">Login</button>
        </form>
    </div>
</div>
</body>
</html>
