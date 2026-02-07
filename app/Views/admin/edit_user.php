<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card">
    <h2 class="h5 mb-3">Edit User</h2>
    <form action="<?= site_url('admin/users/' . $user['id'] . '/update') ?>" method="post">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama</label>
                <input class="form-control" name="name" value="<?= esc($user['name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" value="<?= esc($user['email']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <?php foreach (['drafter','reviewer','approver','admin'] as $role): ?>
                        <option value="<?= $role ?>" <?= $user['role'] === $role ? 'selected' : '' ?>><?= ucfirst($role) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/users') ?>">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
