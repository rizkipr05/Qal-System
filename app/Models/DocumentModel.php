<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'doc_number',
        'category',
        'description',
        'status',
        'owner_id',
        'reviewer_id',
        'approver_id',
        'owner_approval_id',
        'current_version_id',
        'locked_at',
        'approved_by',
        'approved_at',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
}
