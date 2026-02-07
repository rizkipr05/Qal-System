<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QAL Document Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/dc.css') ?>" rel="stylesheet">
</head>
<body>
<div class="dc-shell">
    <aside class="dc-sidebar">
        <div class="dc-brand">
            <div class="dc-brand-title">QAL DC</div>
            <div class="dc-brand-sub">Document Control</div>
        </div>
        <nav class="dc-nav">
            <a class="dc-nav-link" href="<?= site_url('dc') ?>">Dashboard</a>
            <?php if (in_array($currentUser['role'], ['drafter', 'admin'], true)): ?>
                <a class="dc-nav-link" href="<?= site_url('dc/create') ?>">Buat QAL</a>
            <?php endif; ?>
            <?php if ($currentUser['role'] === 'reviewer'): ?>
                <span class="dc-nav-link">Menu Reviewer</span>
            <?php endif; ?>
            <?php if ($currentUser['role'] === 'approver'): ?>
                <span class="dc-nav-link">Menu Approver</span>
            <?php endif; ?>
            <?php if ($currentUser['role'] === 'admin'): ?>
                <a class="dc-nav-link" href="<?= site_url('admin/users') ?>">Admin Users</a>
                <a class="dc-nav-link" href="<?= site_url('admin/logs') ?>">Activity Logs</a>
            <?php endif; ?>
        </nav>
        <div class="dc-user">
            <div class="dc-user-label">User Aktif</div>
            <div class="dc-user-name"><?= esc($currentUser['name']) ?></div>
            <div class="dc-user-role"><?= esc(strtoupper($currentUser['role'])) ?></div>
            <a class="dc-user-link mt-3 d-inline-block" href="<?= site_url('logout') ?>">Logout</a>
        </div>
    </aside>

    <main class="dc-content">
        <header class="dc-header">
            <div>
                <h1 class="dc-title">QAL Document Control</h1>
                <p class="dc-subtitle">Draft, review, revise, approve, dan arsipkan QAL.</p>
            </div>
        </header>

        <section class="dc-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </section>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
