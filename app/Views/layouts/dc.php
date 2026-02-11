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
<?php
$roleMap = [
    'drafter' => 'construction',
    'reviewer' => 'qc',
    'approver' => 'pc',
];
$normalizedRole = $roleMap[$currentUser['role']] ?? $currentUser['role'];
$roleLabelMap = [
    'construction' => 'CONSTRUCTION',
    'qc' => 'QC',
    'pc' => 'PC',
    'owner' => 'OWNER',
    'admin' => 'ADMIN',
];
$roleLabel = $roleLabelMap[$normalizedRole] ?? strtoupper($normalizedRole);
?>
<div class="dc-shell">
    <aside class="dc-sidebar">
        <div class="dc-brand">
            <div class="dc-brand-title">QAL DC</div>
            <div class="dc-brand-sub">Document Control</div>
        </div>
        <nav class="dc-nav">
            <a class="dc-nav-link" href="<?= site_url('dc') ?>">Dashboard</a>
            <a class="dc-nav-link" href="<?= site_url('dc/profile') ?>">Profil Saya</a>
            <?php if ($normalizedRole === 'construction'): ?>
                <a class="dc-nav-link" href="<?= site_url('dc/create') ?>">Buat QAL</a>
                <a class="dc-nav-link" href="<?= site_url('dc?status=draft') ?>">Draft Saya</a>
                <a class="dc-nav-link" href="<?= site_url('dc?status=revision_requested') ?>">Perlu Revisi</a>
            <?php endif; ?>
            <?php if ($normalizedRole === 'qc'): ?>
                <a class="dc-nav-link" href="<?= site_url('dc?status=submitted') ?>">Review Masuk</a>
                <a class="dc-nav-link" href="<?= site_url('dc?status=reviewed') ?>">Selesai QC</a>
            <?php endif; ?>
            <?php if ($normalizedRole === 'pc'): ?>
                <a class="dc-nav-link" href="<?= site_url('dc?status=reviewed') ?>">Menunggu TTD</a>
                <a class="dc-nav-link" href="<?= site_url('dc?status=pc_signed') ?>">Sudah TTD</a>
                <a class="dc-nav-link" href="<?= site_url('admin/users') ?>">Kelola User</a>
            <?php endif; ?>
            <?php if ($normalizedRole === 'owner'): ?>
                <a class="dc-nav-link" href="<?= site_url('dc?status=pc_signed') ?>">Menunggu Approval</a>
                <a class="dc-nav-link" href="<?= site_url('dc?status=archived') ?>">Approved</a>
            <?php endif; ?>
            <?php if ($normalizedRole === 'admin'): ?>
                <a class="dc-nav-link" href="<?= site_url('dc/create') ?>">Buat QAL</a>
                <a class="dc-nav-link" href="<?= site_url('admin/users') ?>">Admin Users</a>
                <a class="dc-nav-link" href="<?= site_url('admin/logs') ?>">Activity Logs</a>
            <?php endif; ?>
        </nav>
        <div class="dc-user">
            <div class="dc-user-label">User Aktif</div>
            <div class="dc-user-name"><?= esc($currentUser['name']) ?></div>
            <div class="dc-user-role"><?= esc($roleLabel) ?></div>
            <a class="dc-user-link mt-3 d-inline-block" href="<?= site_url('logout') ?>">Logout</a>
        </div>
    </aside>

    <main class="dc-content">
        <header class="dc-header">
            <div>
                <h1 class="dc-title">QAL Document Control</h1>
                <p class="dc-subtitle">Alur sistem: Construction serahkan dokumen ke QC, QC susun QAL, PC tanda tangan, Owner approval, lalu QC arsipkan.</p>
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
