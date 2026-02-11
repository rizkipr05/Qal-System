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
                <label class="form-label">Quality Control (QC)</label>
                <select class="form-select" name="reviewer_id">
                    <option value="">- Pilih QC -</option>
                    <?php foreach ($users as $u): ?>
                        <?php if (in_array($u['role'], ['qc', 'reviewer', 'admin'], true)): ?>
                            <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['role']) ?>)</option>
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
                            <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['role']) ?>)</option>
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
                            <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['role']) ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">File Draft (opsional, bisa lebih dari 1)</label>
                <input class="form-control" type="file" name="files[]" id="draftFilesInput" multiple>
                <small class="text-muted d-block mt-1" id="draftFilesInfo">Belum ada file dipilih.</small>
                <ul class="mb-0 mt-2 ps-3" id="draftFilesList"></ul>
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

<script>
(() => {
    const input = document.getElementById('draftFilesInput');
    const info = document.getElementById('draftFilesInfo');
    const list = document.getElementById('draftFilesList');
    if (!input || !info || !list) return;

    const dt = new DataTransfer();

    const refresh = () => {
        list.innerHTML = '';
        const files = Array.from(dt.files || []);

        if (files.length === 0) {
            info.textContent = 'Belum ada file dipilih.';
            return;
        }

        info.textContent = `${files.length} file dipilih.`;
        files.forEach((file, idx) => {
            const li = document.createElement('li');
            li.textContent = `${file.name} `;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-link p-0';
            btn.textContent = 'hapus';
            btn.addEventListener('click', () => {
                const next = new DataTransfer();
                Array.from(dt.files).forEach((f, fIdx) => {
                    if (fIdx !== idx) next.items.add(f);
                });
                dt.items.clear();
                Array.from(next.files).forEach((f) => dt.items.add(f));
                input.files = dt.files;
                refresh();
            });

            li.appendChild(btn);
            list.appendChild(li);
        });
    };

    input.addEventListener('change', () => {
        const existingKeys = new Set(
            Array.from(dt.files).map((f) => `${f.name}|${f.size}|${f.lastModified}`)
        );

        Array.from(input.files || []).forEach((file) => {
            const key = `${file.name}|${file.size}|${file.lastModified}`;
            if (!existingKeys.has(key)) {
                dt.items.add(file);
                existingKeys.add(key);
            }
        });

        input.files = dt.files;
        refresh();
    });
})();
</script>
<?= $this->endSection() ?>
