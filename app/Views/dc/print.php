<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quality Acceptance Letter</title>
    <style>
        @page { size: A4; margin: 12mm; }
        body { font-family: Arial, sans-serif; margin: 0; color: #111; font-size: 11px; }
        .sheet { border: 1px solid #222; padding: 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-small { font-size: 10px; }
        .title { font-weight: 700; letter-spacing: 0.8px; font-size: 14px; margin: 4px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #222; padding: 4px 6px; vertical-align: top; }
        th { background: #f2f2f2; }
        .no-border { border: 0 !important; }
        .mt-8 { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
        .signature-box { height: 44px; border-bottom: 1px solid #222; margin-bottom: 4px; }
        .action { margin: 8px 0; }
        .print-btn { margin-bottom: 10px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<?php
$inspectionItems = [
    'Civil / Area Readiness',
    'Clearing',
    'Grading and Compaction',
    'Excavation and Backfilling',
    'Pipe / Cable Route',
    'Concrete / Foundation',
    'Structural Installation',
    'Mechanical Installation',
    'Electrical Installation',
    'Instrument Installation',
    'Testing and Commissioning',
    'Piping Leak Test',
    'Electrical Test',
    'Punch List Closure',
    'Final Housekeeping',
];
$isPcSigned = in_array($document['status'], ['pc_signed', 'archived'], true);
$isOwnerSigned = $document['status'] === 'archived';
?>
<button class="no-print print-btn" onclick="window.print()">Print</button>

<div class="sheet">
    <table>
        <tr>
            <td style="width: 20%;" class="text-center"><strong>COMPANY</strong><br><span class="text-small">LOGO</span></td>
            <td style="width: 60%;" class="text-center">
                <div class="title">QUALITY ACCEPTANCE LETTER</div>
            </td>
            <td style="width: 20%;" class="text-center"><strong>PROJECT</strong><br><span class="text-small">LOGO</span></td>
        </tr>
    </table>

    <table class="mt-8">
        <tr>
            <td style="width: 18%;"><strong>Project Name</strong></td>
            <td style="width: 32%;"><?= esc($document['title']) ?></td>
            <td style="width: 18%;"><strong>System Description</strong></td>
            <td style="width: 32%;"><?= esc($document['category'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>Client</strong></td>
            <td>Owner</td>
            <td><strong>Doc Number</strong></td>
            <td><?= esc($document['doc_number']) ?></td>
        </tr>
        <tr>
            <td><strong>Contractor</strong></td>
            <td><?= esc($owner['name'] ?? '-') ?></td>
            <td><strong>Date</strong></td>
            <td><?= esc(date('d M Y')) ?></td>
        </tr>
    </table>

    <table class="mt-8">
        <tr>
            <td class="text-center">
                <strong>THIS ACCEPTANCE LETTER IS TO CERTIFY THAT CONSTRUCTION HAS COMPLETED THE WORKS AS MENTIONED BELOW</strong>
            </td>
        </tr>
    </table>

    <table class="mt-8">
        <tr>
            <td style="width: 22%;"><strong>SYSTEM DESCRIPTION</strong></td>
            <td><?= esc($document['category'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>SCOPE OF WORK</strong></td>
            <td><?= esc($document['description'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>QUALITY DOCS NO.</strong></td>
            <td><?= esc($document['doc_number']) ?></td>
        </tr>
    </table>

    <table class="mt-8">
        <tr>
            <td class="text-center">
                <strong>THE FOLLOWING REQUIRED TEST &amp; INSPECTION HAVE BEEN COMPLETED AS PER APPLICABLE CODES, STANDARD AND PROJECT SPECIFICATION</strong>
            </td>
        </tr>
    </table>

    <table class="mt-8">
        <tr>
            <th style="width: 78%;">Inspection Item</th>
            <th style="width: 11%;" class="text-center">YES</th>
            <th style="width: 11%;" class="text-center">NO</th>
        </tr>
        <?php foreach ($inspectionItems as $idx => $item): ?>
            <tr>
                <td><?= ($idx + 1) . '. ' . esc($item) ?></td>
                <td class="text-center">[x]</td>
                <td class="text-center">[ ]</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <table class="mt-12">
        <tr>
            <td class="text-center"><strong>ALL THE REQUIRED INSPECTION REPORTS &amp; QUALITY DOCUMENT HAVE BEEN ATTACHED IN THIS PACKAGE.</strong></td>
        </tr>
    </table>

    <table class="mt-8">
        <tr>
            <th style="width: 25%;" class="text-center">Prepared by (Construction)</th>
            <th style="width: 25%;" class="text-center">Checked and Verified by (QC)</th>
            <th style="width: 25%;" class="text-center">Signed by (PC)</th>
            <th style="width: 25%;" class="text-center">Approved by (Owner)</th>
        </tr>
        <tr>
            <td>
                <div class="signature-box"></div>
                <div><strong>Name:</strong> <?= esc($owner['name'] ?? '-') ?></div>
                <div><strong>Date:</strong> <?= esc(date('d M Y')) ?></div>
            </td>
            <td>
                <div class="signature-box"></div>
                <div><strong>Name:</strong> <?= esc($reviewer['name'] ?? '-') ?></div>
                <div><strong>Date:</strong> <?= esc(date('d M Y')) ?></div>
            </td>
            <td>
                <div class="signature-box text-center"><?= $isPcSigned ? 'TTD PC' : '' ?></div>
                <div><strong>Name:</strong> <?= esc($approver['name'] ?? '-') ?></div>
                <div><strong>Status:</strong> <?= esc($isPcSigned ? 'Sudah ditandatangani' : 'Belum ditandatangani') ?></div>
            </td>
            <td>
                <div class="signature-box text-center"><?= $isOwnerSigned ? 'TTD OWNER' : '' ?></div>
                <div><strong>Name:</strong> <?= esc($ownerApproval['name'] ?? '-') ?></div>
                <div><strong>Date:</strong> <?= esc($document['approved_at'] ? date('d M Y', strtotime($document['approved_at'])) : '-') ?></div>
            </td>
        </tr>
    </table>

    <table class="mt-8">
        <tr>
            <th style="width: 38%;">File Name</th>
            <th style="width: 12%;">Revision</th>
            <th style="width: 25%;">Notes</th>
            <th style="width: 25%;">Created At</th>
        </tr>
        <?php if (empty($versions)): ?>
            <tr>
                <td colspan="4" class="text-center">No revision data</td>
            </tr>
        <?php else: ?>
            <?php foreach ($versions as $v): ?>
                <tr>
                    <?php $displayName = $v['file_name'] ?? basename((string) ($v['file_path'] ?? '')); ?>
                    <td><?= esc($displayName !== '' ? $displayName : ('File #' . $v['id'])) ?></td>
                    <td>#<?= esc($v['revision']) ?></td>
                    <td><?= esc($v['notes'] ?? '-') ?></td>
                    <td><?= esc($v['created_at'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
