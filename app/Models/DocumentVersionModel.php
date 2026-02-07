<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentVersionModel extends Model
{
    protected $table = 'document_versions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'document_id',
        'revision',
        'file_path',
        'notes',
        'created_by',
        'created_at',
    ];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
