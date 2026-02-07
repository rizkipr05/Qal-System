<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Manajemen User</h2>
        <a class="btn btn-success" href="<?= site_url('admin/users/create') ?>">+ Tambah User</a>
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
                        <td><?= esc($u['role']) ?></td>
                        <td>
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
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
