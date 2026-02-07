<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="dc-card text-center">
            <div class="text-muted">Draft</div>
            <div class="h4 mb-0"><?= esc($statusCounts['draft']) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="dc-card text-center">
            <div class="text-muted">Submitted</div>
            <div class="h4 mb-0"><?= esc($statusCounts['submitted']) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="dc-card text-center">
            <div class="text-muted">Reviewed</div>
            <div class="h4 mb-0"><?= esc($statusCounts['reviewed']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dc-card text-center">
            <div class="text-muted">Revision Requested</div>
            <div class="h4 mb-0"><?= esc($statusCounts['revision_requested']) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dc-card text-center">
            <div class="text-muted">Archived</div>
            <div class="h4 mb-0"><?= esc($statusCounts['archived']) ?></div>
        </div>
    </div>
</div>

<div class="dc-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="h5 mb-0">Daftar QAL</h2>
        <div class="d-flex gap-2">
            <form class="d-flex gap-2" method="get" action="<?= site_url('dc') ?>">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <?php foreach (['draft','submitted','reviewed','revision_requested','archived'] as $st): ?>
                        <option value="<?= $st ?>" <?= $statusFilter === $st ? 'selected' : '' ?>>
                            <?= esc(str_replace('_', ' ', strtoupper($st))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline-secondary" type="submit">Filter</button>
            </form>
            <a class="btn btn-success" href="<?= site_url('dc/create') ?>">+ Buat QAL</a>
        </div>
    </div>

    <?php if (empty($documents)): ?>
        <p class="text-muted">Belum ada QAL. Silakan buat draft baru.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="dc-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Doc No</th>
                        <th>Status</th>
                        <th>Owner</th>
                        <th>Reviewer</th>
                        <th>Approver</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= esc($doc['id']) ?></td>
                            <td><?= esc($doc['title']) ?></td>
                            <td><?= esc($doc['doc_number']) ?></td>
                            <td>
                                <span class="dc-badge <?= esc($doc['status']) ?>">
                                    <?= esc(str_replace('_', ' ', strtoupper($doc['status']))) ?>
                                </span>
                            </td>
                            <td><?= esc($doc['owner_name']) ?></td>
                            <td><?= esc($doc['reviewer_name'] ?? '-') ?></td>
                            <td><?= esc($doc['approver_name'] ?? '-') ?></td>
                            <td>
                                <div class="dc-actions">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('dc/' . $doc['id']) ?>">Detail</a>
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('dc/' . $doc['id'] . '/edit') ?>">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
