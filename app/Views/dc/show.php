<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<?php
$roleMap = [
    'drafter' => 'construction',
    'reviewer' => 'qc',
    'approver' => 'pc',
];
$normalizedRole = $roleMap[$currentUser['role']] ?? $currentUser['role'];
$isOwner = $currentUser['id'] === $document['owner_id'];
$isReviewer = $normalizedRole === 'qc' && $document['reviewer_id'] === $currentUser['id'];
$isApprover = $normalizedRole === 'pc' && $document['approver_id'] === $currentUser['id'];
$isOwnerApproval = $normalizedRole === 'owner' && $document['owner_approval_id'] === $currentUser['id'];
$isAdmin = $normalizedRole === 'admin';

$canEdit = $isAdmin || ($isOwner && in_array($document['status'], ['draft', 'revision_requested'], true));
$canDelete = $isAdmin || ($isOwner && $document['status'] === 'draft');
$canSubmit = $isAdmin || ($isOwner && in_array($document['status'], ['draft', 'revision_requested'], true));
$canUploadRevision = $isAdmin || ($isOwner && $document['status'] === 'revision_requested');
$canReview = $isAdmin || ($isReviewer && $document['status'] === 'submitted');
$canApprove = $isAdmin || ($isApprover && $document['status'] === 'reviewed');
$canOwnerApprove = $isAdmin || ($isOwnerApproval && $document['status'] === 'pc_signed');

$jobHint = match ($normalizedRole) {
    'construction' => 'Tugas Construction: lengkapi dokumen, kirim ke QC, dan revisi bila diminta.',
    'qc' => 'Tugas QC: review dokumen dan teruskan QAL ke PC.',
    'pc' => 'Tugas PC: tandatangani QAL lalu kirim ke Owner.',
    'owner' => 'Tugas Owner: approval akhir, kemudian dokumen diarsipkan oleh QC.',
    default => 'Admin memantau dan mengelola seluruh proses.',
};
?>

<div class="dc-card mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h2 class="h5 mb-1"><?= esc($document['title']) ?></h2>
            <div class="text-muted">Nomor Dokumen: <?= esc($document['doc_number']) ?></div>
            <div class="text-muted">Kategori: <?= esc($document['category'] ?? '-') ?></div>
            <div class="mt-2">
                <span class="dc-badge <?= esc($document['status']) ?>">
                    <?= esc(str_replace('_', ' ', strtoupper($document['status']))) ?>
                </span>
            </div>
        </div>
        <div class="dc-actions">
            <?php if ($canEdit): ?>
                <a class="btn btn-outline-secondary" href="<?= site_url('dc/' . $document['id'] . '/edit') ?>">Edit</a>
            <?php endif; ?>
            <a class="btn btn-outline-dark" href="<?= site_url('dc/' . $document['id'] . '/print') ?>" target="_blank">Print</a>
            <?php if ($canDelete): ?>
                <form action="<?= site_url('dc/' . $document['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus dokumen ini?')">
                    <button class="btn btn-outline-danger" type="submit">Hapus</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <div class="text-muted">Construction</div>
            <div><?= esc($owner['name'] ?? '-') ?></div>
        </div>
        <div class="col-md-4">
            <div class="text-muted">Quality Control</div>
            <div><?= esc($reviewer['name'] ?? '-') ?></div>
        </div>
        <div class="col-md-4">
            <div class="text-muted">Project Control</div>
            <div><?= esc($approver['name'] ?? '-') ?></div>
        </div>
        <div class="col-md-4 mt-3">
            <div class="text-muted">Owner Approval</div>
            <div><?= esc($ownerApproval['name'] ?? '-') ?></div>
        </div>
    </div>

    <div class="mt-3">
        <div class="text-muted">Deskripsi</div>
        <div><?= nl2br(esc($document['description'] ?? '-')) ?></div>
    </div>
</div>

<div class="dc-card mb-4">
    <h3 class="h6 mb-3">Aksi</h3>
    <p class="text-muted mb-3"><?= esc($jobHint) ?></p>
    <div class="dc-actions">
        <?php if ($canSubmit): ?>
            <form action="<?= site_url('dc/' . $document['id'] . '/submit') ?>" method="post">
                <button class="btn btn-success" type="submit">Serahkan ke QC</button>
            </form>
        <?php endif; ?>

        <?php if ($canUploadRevision): ?>
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#revisionForm">
                Upload Revisi
            </button>
        <?php endif; ?>

        <?php if ($canReview): ?>
            <button class="btn btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#reviewForm">
                Review QC
            </button>
        <?php endif; ?>

        <?php if ($canApprove): ?>
            <form action="<?= site_url('dc/' . $document['id'] . '/approve') ?>" method="post" onsubmit="return confirm('Tandatangani QAL dan teruskan ke Owner?')">
                <button class="btn btn-dark" type="submit">Tanda Tangan PC</button>
            </form>
        <?php endif; ?>

        <?php if ($canOwnerApprove): ?>
            <form action="<?= site_url('dc/' . $document['id'] . '/owner-approve') ?>" method="post" onsubmit="return confirm('Setujui QAL dan arsipkan ke QC?')">
                <button class="btn btn-primary" type="submit">Approve Owner</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($canUploadRevision): ?>
        <div class="collapse mt-3" id="revisionForm">
            <form action="<?= site_url('dc/' . $document['id'] . '/revision') ?>" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">File Revisi (bisa lebih dari 1)</label>
                        <input class="form-control" type="file" name="files[]" id="revisionFilesInput" multiple required>
                        <small class="text-muted d-block mt-1" id="revisionFilesInfo">Belum ada file dipilih.</small>
                        <ul class="mb-0 mt-2 ps-3" id="revisionFilesList"></ul>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catatan Revisi</label>
                        <input class="form-control" name="notes" placeholder="Perubahan yang dilakukan">
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">Kirim Revisi</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($canReview): ?>
        <div class="collapse mt-3" id="reviewForm">
            <form action="<?= site_url('dc/' . $document['id'] . '/review') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label">Catatan Review</label>
                    <textarea class="form-control" rows="3" name="comment"></textarea>
                </div>
                <div class="dc-actions">
                    <button class="btn btn-success" type="submit" name="action" value="approve">Teruskan ke PC</button>
                    <button class="btn btn-outline-danger" type="submit" name="action" value="revision">Minta Revisi ke Construction</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="dc-card">
            <h3 class="h6 mb-3">Versi Dokumen</h3>
            <?php if (empty($versions)): ?>
                <p class="text-muted">Belum ada file yang diupload.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($versions as $v): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <?php $displayName = $v['file_name'] ?? basename((string) ($v['file_path'] ?? '')); ?>
                                <div><?= esc($displayName !== '' ? $displayName : ('File #' . $v['id'])) ?></div>
                                <small class="text-muted">Revision #<?= esc($v['revision']) ?> | <?= esc($v['notes'] ?? '-') ?></small>
                            </div>
                            <?php if (!empty($v['file_path'])): ?>
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('dc/version/' . $v['id'] . '/download') ?>">Download</a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dc-card">
            <h3 class="h6 mb-3">Riwayat Review</h3>
            <?php if (empty($reviews)): ?>
                <p class="text-muted">Belum ada review.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($reviews as $r): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong><?= esc(str_replace('_', ' ', strtoupper($r['status']))) ?></strong>
                                <small class="text-muted"><?= esc($r['created_at']) ?></small>
                            </div>
                            <div class="text-muted">QC: <?= esc($r['reviewer_name'] ?? $r['reviewer_id']) ?></div>
                            <div><?= esc($r['comment'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(() => {
    const input = document.getElementById('revisionFilesInput');
    const info = document.getElementById('revisionFilesInfo');
    const list = document.getElementById('revisionFilesList');
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
