<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card">
    <?php
    $roleMap = [
        'drafter' => 'construction',
        'reviewer' => 'qc',
        'approver' => 'pc',
    ];
    $roleLabels = [
        'construction' => 'Construction',
        'qc' => 'Quality Control (QC)',
        'pc' => 'Project Control (PC)',
        'owner' => 'Owner',
        'admin' => 'Admin',
    ];
    ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Manajemen User</h2>
        <?php if (!empty($canCreateUser)): ?>
            <a class="btn btn-success" href="<?= site_url('admin/users/create') ?>">+ Tambah User</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="dc-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= esc($u['name']) ?></td>
                        <td><?= esc($u['email']) ?></td>
                        <?php $normalizedRole = $roleMap[$u['role']] ?? $u['role']; ?>
                        <td><?= esc($roleLabels[$normalizedRole] ?? strtoupper($normalizedRole)) ?></td>
                        <td>
                            <?php $canManage = in_array((int) $u['id'], $manageableUserIds ?? [], true); ?>
                            <?php if ($canManage): ?>
                                <div class="dc-actions">
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/users/' . $u['id'] . '/edit') ?>">Edit</a>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#reset<?= $u['id'] ?>">Reset PW</button>
                                </div>
                                <div class="collapse mt-2" id="reset<?= $u['id'] ?>">
                                    <form action="<?= site_url('admin/users/' . $u['id'] . '/reset-password') ?>" method="post" class="d-flex gap-2">
                                        <input class="form-control" type="text" name="password" placeholder="Password baru" required>
                                        <button class="btn btn-sm btn-dark" type="submit">Simpan</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
