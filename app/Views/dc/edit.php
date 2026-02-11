<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card">
    <h2 class="h5 mb-3">Edit QAL</h2>
    <form action="<?= site_url('dc/' . $document['id'] . '/update') ?>" method="post">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Judul</label>
                <input class="form-control" name="title" value="<?= esc($document['title']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor Dokumen</label>
                <input class="form-control" name="doc_number" value="<?= esc($document['doc_number']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <input class="form-control" name="category" value="<?= esc($document['category']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Quality Control (QC)</label>
                <select class="form-select" name="reviewer_id">
                    <option value="">- Pilih QC -</option>
                    <?php foreach ($users as $u): ?>
                        <?php if (in_array($u['role'], ['qc', 'reviewer', 'admin'], true)): ?>
                            <option value="<?= $u['id'] ?>" <?= $document['reviewer_id'] == $u['id'] ? 'selected' : '' ?>>
                                <?= esc($u['name']) ?> (<?= esc($u['role']) ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Project Control (PC)</label>
                <select class="form-select" name="approver_id">
                    <option value="">- Pilih PC -</option>
                    <?php foreach ($users as $u): ?>
                        <?php if (in_array($u['role'], ['pc', 'approver', 'admin'], true)): ?>
                            <option value="<?= $u['id'] ?>" <?= $document['approver_id'] == $u['id'] ? 'selected' : '' ?>>
                                <?= esc($u['name']) ?> (<?= esc($u['role']) ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Owner Approval</label>
                <select class="form-select" name="owner_approval_id">
                    <option value="">- Pilih Owner -</option>
                    <?php foreach ($users as $u): ?>
                        <?php if (in_array($u['role'], ['owner', 'admin'], true)): ?>
                            <option value="<?= $u['id'] ?>" <?= $document['owner_approval_id'] == $u['id'] ? 'selected' : '' ?>>
                                <?= esc($u['name']) ?> (<?= esc($u['role']) ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" rows="4" name="description"><?= esc($document['description']) ?></textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-success" type="submit">Update</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('dc/' . $document['id']) ?>">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
