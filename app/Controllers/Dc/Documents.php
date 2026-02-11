<?php

namespace App\Controllers\Dc;

use App\Controllers\BaseController;
use App\Models\DocumentModel;
use App\Models\DocumentReviewModel;
use App\Models\DocumentVersionModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Documents extends BaseController
{
    protected $helpers = ['form', 'url'];

    private DocumentModel $documents;
    private DocumentVersionModel $versions;
    private DocumentReviewModel $reviews;
    private UserModel $users;
    private ?bool $hasVersionFileNameColumn = null;

    public function __construct()
    {
        $this->documents = new DocumentModel();
        $this->versions = new DocumentVersionModel();
        $this->reviews = new DocumentReviewModel();
        $this->users = new UserModel();
    }

    public function index()
    {
        $currentUser = $this->currentUser();
        $statusFilter = $this->request->getGet('status');

        $query = $this->documents
            ->select('documents.*, u.name as owner_name, r.name as reviewer_name, a.name as approver_name, oa.name as owner_approval_name')
            ->join('users u', 'u.id = documents.owner_id')
            ->join('users r', 'r.id = documents.reviewer_id', 'left')
            ->join('users a', 'a.id = documents.approver_id', 'left')
            ->join('users oa', 'oa.id = documents.owner_approval_id', 'left')
            ->orderBy('documents.created_at', 'DESC');

        if ($this->hasRole($currentUser, 'construction')) {
            $query->where('documents.owner_id', $currentUser['id']);
        } elseif ($this->hasRole($currentUser, 'qc')) {
            $query->where('documents.reviewer_id', $currentUser['id']);
        } elseif ($this->hasRole($currentUser, 'pc')) {
            $query->where('documents.approver_id', $currentUser['id']);
        } elseif ($this->hasRole($currentUser, 'owner')) {
            $query->where('documents.owner_approval_id', $currentUser['id']);
        }

        if ($statusFilter) {
            $query->where('documents.status', $statusFilter);
        }

        $docs = $query->findAll();

        $statusCountsRaw = $this->documents
            ->select('status, COUNT(*) as total')
            ->groupBy('status');

        if ($this->hasRole($currentUser, 'construction')) {
            $statusCountsRaw->where('owner_id', $currentUser['id']);
        } elseif ($this->hasRole($currentUser, 'qc')) {
            $statusCountsRaw->where('reviewer_id', $currentUser['id']);
        } elseif ($this->hasRole($currentUser, 'pc')) {
            $statusCountsRaw->where('approver_id', $currentUser['id']);
        } elseif ($this->hasRole($currentUser, 'owner')) {
            $statusCountsRaw->where('owner_approval_id', $currentUser['id']);
        }

        $statusCountsRaw = $statusCountsRaw->findAll();

        $this->logActivity($currentUser['id'], 'view_dashboard', [
            'status' => $statusFilter,
        ]);

        $statusCounts = [
            'draft' => 0,
            'submitted' => 0,
            'reviewed' => 0,
            'pc_signed' => 0,
            'revision_requested' => 0,
            'archived' => 0,
        ];

        foreach ($statusCountsRaw as $row) {
            $statusCounts[$row['status']] = (int) $row['total'];
        }

        return view('dc/index', [
            'currentUser' => $currentUser,
            'users'       => $this->users->findAll(),
            'documents'   => $docs,
            'statusFilter' => $statusFilter,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function show(int $id)
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        $owner = $this->users->find($doc['owner_id']);
        $reviewer = $doc['reviewer_id'] ? $this->users->find($doc['reviewer_id']) : null;
        $approver = $doc['approver_id'] ? $this->users->find($doc['approver_id']) : null;
        $ownerApproval = $doc['owner_approval_id'] ? $this->users->find($doc['owner_approval_id']) : null;

        $versions = $this->versions->where('document_id', $id)->orderBy('revision', 'DESC')->findAll();
        $reviews = $this->reviews
            ->select('document_reviews.*, users.name as reviewer_name')
            ->join('users', 'users.id = document_reviews.reviewer_id', 'left')
            ->where('document_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('dc/show', [
            'currentUser' => $currentUser,
            'users'       => $this->users->findAll(),
            'document'    => $doc,
            'owner'       => $owner,
            'reviewer'    => $reviewer,
            'approver'    => $approver,
            'ownerApproval' => $ownerApproval,
            'versions'    => $versions,
            'reviews'     => $reviews,
        ]);
    }

    public function print(int $id)
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        $owner = $this->users->find($doc['owner_id']);
        $reviewer = $doc['reviewer_id'] ? $this->users->find($doc['reviewer_id']) : null;
        $approver = $doc['approver_id'] ? $this->users->find($doc['approver_id']) : null;
        $ownerApproval = $doc['owner_approval_id'] ? $this->users->find($doc['owner_approval_id']) : null;
        $versions = $this->versions->where('document_id', $id)->orderBy('revision', 'DESC')->findAll();

        return view('dc/print', [
            'currentUser' => $currentUser,
            'document'    => $doc,
            'owner'       => $owner,
            'reviewer'    => $reviewer,
            'approver'    => $approver,
            'ownerApproval' => $ownerApproval,
            'versions'    => $versions,
        ]);
    }

    public function create()
    {
        $currentUser = $this->currentUser();
        if (!$this->canCreateQal($currentUser)) {
            return redirect()->to(site_url('dc'))->with('error', 'Hanya Construction atau Admin yang bisa membuat QAL.');
        }

        return view('dc/create', [
            'currentUser' => $currentUser,
            'users'       => $this->users->findAll(),
        ]);
    }

    public function profile()
    {
        $currentUser = $this->currentUser();

        return view('dc/profile', [
            'currentUser' => $currentUser,
        ]);
    }

    public function updateProfile(): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $newPassword = (string) $this->request->getPost('password');

        if ($name === '' || $email === '') {
            return redirect()->back()->withInput()->with('error', 'Nama dan email wajib diisi.');
        }

        $existing = $this->users->where('email', $email)->where('id !=', $currentUser['id'])->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan.');
        }

        $data = [
            'name' => $name,
            'email' => $email,
        ];

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $this->users->update($currentUser['id'], $data);
        $this->logActivity($currentUser['id'], 'update_profile', ['user_id' => $currentUser['id']]);

        return redirect()->to(site_url('dc/profile'))->with('success', 'Profil berhasil diperbarui.');
    }

    public function store(): RedirectResponse
    {
        $currentUser = $this->currentUser();
        if (!$this->canCreateQal($currentUser)) {
            return redirect()->to(site_url('dc'))->with('error', 'Hanya Construction atau Admin yang bisa membuat QAL.');
        }

        $data = [
            'title'       => $this->request->getPost('title'),
            'doc_number'  => $this->request->getPost('doc_number'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'owner_id'    => $currentUser['id'],
            'reviewer_id' => $this->request->getPost('reviewer_id') ?: null,
            'approver_id' => $this->request->getPost('approver_id') ?: null,
            'owner_approval_id' => $this->request->getPost('owner_approval_id') ?: null,
            'status'      => 'draft',
        ];

        $docId = $this->documents->insert($data, true);

        $files = $this->request->getFileMultiple('files');
        if (empty($files)) {
            $single = $this->request->getFile('file');
            if ($single && $single->isValid()) {
                $files = [$single];
            }
        }

        if (!empty($files)) {
            $revision = 1;
            $lastVersionId = null;
            foreach ($files as $file) {
                if (!$file || !$file->isValid() || $file->hasMoved()) {
                    continue;
                }

                $path = $this->storeUpload($file, $docId);
                $versionData = [
                    'document_id' => $docId,
                    'revision'    => $revision,
                    'file_path'   => $path,
                    'notes'       => 'Draft awal',
                    'created_by'  => $currentUser['id'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ];
                if ($this->supportsVersionFileName()) {
                    $versionData['file_name'] = $file->getClientName();
                }

                $lastVersionId = $this->versions->insert($versionData, true);
                $revision++;
            }

            if ($lastVersionId) {
                $this->documents->update($docId, ['current_version_id' => $lastVersionId]);
            }
        }

        $this->logActivity($currentUser['id'], 'create_qal', ['document_id' => $docId]);

        return redirect()->to(site_url('dc/' . $docId))->with('success', 'QAL draft berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canEdit($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Anda tidak punya akses untuk edit dokumen ini.');
        }

        return view('dc/edit', [
            'currentUser' => $currentUser,
            'users'       => $this->users->findAll(),
            'document'    => $doc,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canEdit($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Anda tidak punya akses untuk edit dokumen ini.');
        }

        $data = [
            'title'       => $this->request->getPost('title'),
            'doc_number'  => $this->request->getPost('doc_number'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'reviewer_id' => $this->request->getPost('reviewer_id') ?: null,
            'approver_id' => $this->request->getPost('approver_id') ?: null,
            'owner_approval_id' => $this->request->getPost('owner_approval_id') ?: null,
        ];

        $this->documents->update($id, $data);

        $this->logActivity($currentUser['id'], 'update_qal', ['document_id' => $id]);

        return redirect()->to(site_url('dc/' . $id))->with('success', 'Dokumen berhasil diupdate.');
    }

    public function delete(int $id): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canDelete($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Dokumen tidak bisa dihapus.');
        }

        $db = db_connect();
        $db->transStart();

        $this->versions->where('document_id', $id)->delete();
        $this->reviews->where('document_id', $id)->delete();
        $this->documents->delete($id);

        $db->transComplete();
        if (!$db->transStatus()) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Gagal menghapus dokumen karena relasi data.');
        }

        $this->logActivity($currentUser['id'], 'delete_qal', ['document_id' => $id]);

        return redirect()->to(site_url('dc'))->with('success', 'Dokumen berhasil dihapus.');
    }

    public function submit(int $id): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canSubmit($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Dokumen tidak bisa disubmit.');
        }

        $this->documents->update($id, ['status' => 'submitted']);

        $this->logActivity($currentUser['id'], 'submit_qal', ['document_id' => $id]);

        return redirect()->to(site_url('dc/' . $id))->with('success', 'Dokumen pendukung berhasil diserahkan ke Quality Control.');
    }

    public function uploadRevision(int $id): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canUploadRevision($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Anda tidak bisa upload revisi.');
        }

        $files = $this->request->getFileMultiple('files');
        if (empty($files)) {
            $single = $this->request->getFile('file');
            if ($single && $single->isValid()) {
                $files = [$single];
            }
        }
        if (empty($files)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Minimal satu file revisi wajib diupload.');
        }

        $latest = $this->versions->where('document_id', $id)->orderBy('revision', 'DESC')->first();
        $nextRevision = $latest ? ((int) $latest['revision'] + 1) : 1;

        $versionId = null;
        foreach ($files as $file) {
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                continue;
            }

            $path = $this->storeUpload($file, $id);
            $versionData = [
                'document_id' => $id,
                'revision'    => $nextRevision,
                'file_path'   => $path,
                'notes'       => $this->request->getPost('notes'),
                'created_by'  => $currentUser['id'],
                'created_at'  => date('Y-m-d H:i:s'),
            ];
            if ($this->supportsVersionFileName()) {
                $versionData['file_name'] = $file->getClientName();
            }

            $versionId = $this->versions->insert($versionData, true);
            $nextRevision++;
        }

        if (!$versionId) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Tidak ada file valid yang berhasil diupload.');
        }

        $this->documents->update($id, [
            'current_version_id' => $versionId,
            'status'             => 'submitted',
        ]);

        $this->logActivity($currentUser['id'], 'upload_revision', ['document_id' => $id, 'revision' => $nextRevision]);

        return redirect()->to(site_url('dc/' . $id))->with('success', 'File revisi berhasil diupload dan dikirim ulang.');
    }

    public function review(int $id): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canReview($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Anda tidak punya akses Quality Control.');
        }

        $action = $this->request->getPost('action');
        $comment = $this->request->getPost('comment');
        if (!in_array($action, ['approve', 'revision'], true)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Aksi review tidak valid.');
        }

        $reviewStatus = $action === 'approve' ? 'review_approved' : 'revision_requested';
        $docStatus = $action === 'approve' ? 'reviewed' : 'revision_requested';

        $this->reviews->insert([
            'document_id' => $id,
            'reviewer_id' => $currentUser['id'],
            'status'      => $reviewStatus,
            'comment'     => $comment,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->documents->update($id, ['status' => $docStatus]);

        $message = $action === 'approve'
            ? 'QC selesai review. Dokumen diteruskan ke Project Control untuk penandatanganan.'
            : 'Revisi diminta ke Construction.';
        $this->logActivity($currentUser['id'], 'review_qal', ['document_id' => $id, 'action' => $action]);

        return redirect()->to(site_url('dc/' . $id))->with('success', $message);
    }

    public function approve(int $id): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canApprove($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Anda tidak punya akses Project Control.');
        }

        $this->documents->update($id, [
            'status' => 'pc_signed',
        ]);

        $this->logActivity($currentUser['id'], 'pc_sign_qal', ['document_id' => $id]);

        return redirect()->to(site_url('dc/' . $id))->with('success', 'PC sudah menandatangani dokumen. QAL diteruskan ke Owner untuk approval.');
    }

    public function ownerApprove(int $id): RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doc = $this->documents->find($id);
        if (!$doc) {
            return redirect()->to(site_url('dc'))->with('error', 'Dokumen tidak ditemukan.');
        }

        if (!$this->canOwnerApprove($doc, $currentUser)) {
            return redirect()->to(site_url('dc/' . $id))->with('error', 'Anda tidak punya akses Owner.');
        }

        $this->documents->update($id, [
            'status'      => 'archived',
            'locked_at'   => date('Y-m-d H:i:s'),
            'approved_by' => $currentUser['id'],
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity($currentUser['id'], 'owner_approve_qal', ['document_id' => $id]);

        return redirect()->to(site_url('dc/' . $id))->with('success', 'Owner sudah approve. QAL dikembalikan ke QC dan disimpan sebagai arsip proyek.');
    }

    public function downloadVersion(int $id)
    {
        $version = $this->versions->find($id);
        if (!$version) {
            return redirect()->to(site_url('dc'))->with('error', 'File tidak ditemukan.');
        }

        $path = WRITEPATH . $version['file_path'];
        if (!is_file($path)) {
            return redirect()->back()->with('error', 'File tidak tersedia di server.');
        }

        return $this->response->download($path, null);
    }

    private function currentUser(): array
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            redirect()->to(site_url('login'))->send();
            exit;
        }

        $user = $this->users->find($userId);
        if (!$user) {
            session()->destroy();
            redirect()->to(site_url('login'))->send();
            exit;
        }

        return $user;
    }

    private function canEdit(array $doc, array $user): bool
    {
        if (in_array($doc['status'], ['archived'], true)) {
            return false;
        }

        if ($this->hasRole($user, 'admin')) {
            return true;
        }

        return $doc['owner_id'] === $user['id'] && in_array($doc['status'], ['draft', 'revision_requested'], true);
    }

    private function canDelete(array $doc, array $user): bool
    {
        if ($this->hasRole($user, 'admin')) {
            return true;
        }

        return $doc['owner_id'] === $user['id'] && $doc['status'] === 'draft';
    }

    private function canSubmit(array $doc, array $user): bool
    {
        if ($this->hasRole($user, 'admin')) {
            return true;
        }

        return $doc['owner_id'] === $user['id'] && in_array($doc['status'], ['draft', 'revision_requested'], true);
    }

    private function canUploadRevision(array $doc, array $user): bool
    {
        if ($this->hasRole($user, 'admin')) {
            return true;
        }

        return $doc['owner_id'] === $user['id'] && in_array($doc['status'], ['draft', 'revision_requested'], true);
    }

    private function canReview(array $doc, array $user): bool
    {
        if ($this->hasRole($user, 'admin')) {
            return true;
        }

        return $this->hasRole($user, 'qc') && $doc['reviewer_id'] === $user['id'] && in_array($doc['status'], ['submitted'], true);
    }

    private function canApprove(array $doc, array $user): bool
    {
        if ($this->hasRole($user, 'admin')) {
            return true;
        }

        return $this->hasRole($user, 'pc') && $doc['approver_id'] === $user['id'] && $doc['status'] === 'reviewed';
    }

    private function canOwnerApprove(array $doc, array $user): bool
    {
        if ($this->hasRole($user, 'admin')) {
            return true;
        }

        return $this->hasRole($user, 'owner')
            && $doc['owner_approval_id'] === $user['id']
            && $doc['status'] === 'pc_signed';
    }

    private function storeUpload($file, int $docId): string
    {
        $uploadDir = WRITEPATH . 'uploads/qal/' . $docId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);

        return 'uploads/qal/' . $docId . '/' . $newName;
    }

    private function canCreateQal(array $user): bool
    {
        return $this->hasRole($user, 'admin') || $this->hasRole($user, 'construction');
    }

    private function hasRole(array $user, string $expected): bool
    {
        return $this->normalizeRole((string) ($user['role'] ?? '')) === $expected;
    }

    private function normalizeRole(string $role): string
    {
        return match ($role) {
            'drafter' => 'construction',
            'reviewer' => 'qc',
            'approver' => 'pc',
            default => $role,
        };
    }

    private function supportsVersionFileName(): bool
    {
        if ($this->hasVersionFileNameColumn !== null) {
            return $this->hasVersionFileNameColumn;
        }

        $this->hasVersionFileNameColumn = db_connect()->fieldExists('file_name', 'document_versions');
        return $this->hasVersionFileNameColumn;
    }
}
