<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card">
    <h2 class="h5 mb-3">Buat QAL (Draft)</h2>
    <form action="<?= site_url('dc/store') ?>" method="post" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Judul</label>
                <input class="form-control" name="title" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Dokumen</label>
                <input class="form-control" name="doc_number" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <input class="form-control" name="category">
            </div>
            <div class="col-md-6">
                <label class="form-label">Reviewer</label>
                <select class="form-select" name="reviewer_id">
                    <option value="">- Pilih Reviewer -</option>
                    <?php foreach ($users as $u): ?>
                        <?php if ($u['role'] === 'reviewer' || $u['role'] === 'admin'): ?>
                            <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['role']) ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Approver</label>
                <select class="form-select" name="approver_id">
                    <option value="">- Pilih Approver -</option>
                    <?php foreach ($users as $u): ?>
                        <?php if ($u['role'] === 'approver' || $u['role'] === 'admin'): ?>
                            <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['role']) ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">File Draft (opsional)</label>
                <input class="form-control" type="file" name="file">
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" rows="4" name="description"></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success" type="submit">Simpan Draft</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('dc') ?>">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
