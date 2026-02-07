<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'action', 'context', 'created_at'];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
