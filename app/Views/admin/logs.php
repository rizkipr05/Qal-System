<?= $this->extend('layouts/dc') ?>

<?= $this->section('content') ?>
<div class="dc-card">
    <h2 class="h5 mb-3">Activity Logs</h2>
    <div class="table-responsive">
        <table class="dc-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Context</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= esc($log['created_at']) ?></td>
                        <td><?= esc($log['user_name'] ?? '-') ?></td>
                        <td><?= esc($log['action']) ?></td>
                        <td><small><?= esc($log['context']) ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
