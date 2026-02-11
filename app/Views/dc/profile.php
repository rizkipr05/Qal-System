<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card" style="max-width: 760px;">
    <h2 class="h5 mb-3">Profil Saya</h2>
    <form action="<?= site_url('dc/profile') ?>" method="post">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama</label>
                <input class="form-control" name="name" value="<?= esc(old('name', $currentUser['name'])) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" value="<?= esc(old('email', $currentUser['email'])) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role</label>
                <input class="form-control" value="<?= esc(strtoupper($currentUser['role'])) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password Baru (opsional)</label>
                <input class="form-control" type="password" name="password" placeholder="Kosongkan jika tidak diubah">
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success" type="submit">Simpan Profil</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('dc') ?>">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
