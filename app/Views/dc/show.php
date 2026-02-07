<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<?php
$isOwner = $currentUser['id'] === $document['owner_id'];
$isReviewer = $currentUser['role'] === 'reviewer' && $document['reviewer_id'] === $currentUser['id'];
$isApprover = $currentUser['role'] === 'approver' && $document['approver_id'] === $currentUser['id'];
$isAdmin = $currentUser['role'] === 'admin';

$canEdit = $isAdmin || ($isOwner && in_array($document['status'], ['draft', 'revision_requested'], true));
$canDelete = $isAdmin || ($isOwner && $document['status'] === 'draft');
$canSubmit = $isAdmin || ($isOwner && in_array($document['status'], ['draft', 'revision_requested'], true));
$canUploadRevision = $isAdmin || ($isOwner && $document['status'] === 'revision_requested');
$canReview = $isAdmin || ($isReviewer && $document['status'] === 'submitted');
$canApprove = $isAdmin || ($isApprover && $document['status'] === 'reviewed');
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
            <div class="text-muted">Owner</div>
            <div><?= esc($owner['name'] ?? '-') ?></div>
        </div>
        <div class="col-md-4">
            <div class="text-muted">Reviewer</div>
            <div><?= esc($reviewer['name'] ?? '-') ?></div>
        </div>
        <div class="col-md-4">
            <div class="text-muted">Approver</div>
            <div><?= esc($approver['name'] ?? '-') ?></div>
        </div>
    </div>

    <div class="mt-3">
        <div class="text-muted">Deskripsi</div>
        <div><?= nl2br(esc($document['description'] ?? '-')) ?></div>
    </div>
</div>

<div class="dc-card mb-4">
    <h3 class="h6 mb-3">Aksi</h3>
    <div class="dc-actions">
        <?php if ($canSubmit): ?>
            <form action="<?= site_url('dc/' . $document['id'] . '/submit') ?>" method="post">
                <button class="btn btn-success" type="submit">Submit QAL</button>
            </form>
        <?php endif; ?>

        <?php if ($canUploadRevision): ?>
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#revisionForm">
                Upload Revisi
            </button>
        <?php endif; ?>

        <?php if ($canReview): ?>
            <button class="btn btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#reviewForm">
                Review Dokumen
            </button>
        <?php endif; ?>

        <?php if ($canApprove): ?>
            <form action="<?= site_url('dc/' . $document['id'] . '/approve') ?>" method="post" onsubmit="return confirm('Approve QAL final dan kunci dokumen?')">
                <button class="btn btn-dark" type="submit">Approve Final</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($canUploadRevision): ?>
        <div class="collapse mt-3" id="revisionForm">
            <form action="<?= site_url('dc/' . $document['id'] . '/revision') ?>" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">File Revisi</label>
                        <input class="form-control" type="file" name="file" required>
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
                    <button class="btn btn-success" type="submit" name="action" value="approve">Setujui Review</button>
                    <button class="btn btn-outline-danger" type="submit" name="action" value="revision">Minta Revisi</button>
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
                                <div>Revision #<?= esc($v['revision']) ?></div>
                                <small class="text-muted"><?= esc($v['notes'] ?? '-') ?></small>
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
                            <div class="text-muted">Reviewer: <?= esc($r['reviewer_name'] ?? $r['reviewer_id']) ?></div>
                            <div><?= esc($r['comment'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
