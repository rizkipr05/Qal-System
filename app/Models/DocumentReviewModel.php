<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentReviewModel extends Model
{
    protected $table = 'document_reviews';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'document_id',
        'reviewer_id',
        'status',
        'comment',
        'created_at',
    ];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
