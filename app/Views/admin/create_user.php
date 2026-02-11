<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card">
    <h2 class="h5 mb-3">Tambah User</h2>
    <form action="<?= site_url('admin/users/store') ?>" method="post">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama</label>
                <input class="form-control" name="name" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <?php foreach (($roleOptions ?? []) as $role => $label): ?>
                        <option value="<?= esc($role) ?>"><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input class="form-control" type="text" name="password" required>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success" type="submit">Simpan</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/users') ?>">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
