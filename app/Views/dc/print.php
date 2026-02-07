<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print QAL</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 32px; color: #111; }
        h1 { margin-bottom: 6px; }
        .meta { margin-bottom: 18px; }
        .meta div { margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 12px; background: #eee; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Print</button>
    <h1><?= esc($document['title']) ?></h1>
    <div class="meta">
        <div><strong>Nomor Dokumen:</strong> <?= esc($document['doc_number']) ?></div>
        <div><strong>Kategori:</strong> <?= esc($document['category'] ?? '-') ?></div>
        <div><strong>Status:</strong> <span class="badge"><?= esc(str_replace('_', ' ', strtoupper($document['status']))) ?></span></div>
        <div><strong>Owner:</strong> <?= esc($owner['name'] ?? '-') ?></div>
        <div><strong>Reviewer:</strong> <?= esc($reviewer['name'] ?? '-') ?></div>
        <div><strong>Approver:</strong> <?= esc($approver['name'] ?? '-') ?></div>
    </div>

    <h3>Deskripsi</h3>
    <p><?= nl2br(esc($document['description'] ?? '-')) ?></p>

    <h3>Riwayat Versi</h3>
    <table>
        <thead>
            <tr>
                <th>Revision</th>
                <th>Catatan</th>
                <th>Dibuat</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($versions)): ?>
                <tr><td colspan="3">Belum ada versi.</td></tr>
            <?php else: ?>
                <?php foreach ($versions as $v): ?>
                    <tr>
                        <td>#<?= esc($v['revision']) ?></td>
                        <td><?= esc($v['notes'] ?? '-') ?></td>
                        <td><?= esc($v['created_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
