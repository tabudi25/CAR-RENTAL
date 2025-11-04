<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id', 'user_type', 'action', 'description', 'ip_address', 
        'user_agent', 'related_id', 'related_type', 'metadata', 'created_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = null;

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get all activity logs with user details
     */
    public function getActivityLogsWithDetails($limit = 100, $offset = 0)
    {
        return $this->select('activity_logs.*, users.name as user_name, users.email as user_email')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Get activity logs by user
     */
    public function getActivityLogsByUser($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activity logs by action
     */
    public function getActivityLogsByAction($action, $limit = 50)
    {
        return $this->select('activity_logs.*, users.name as user_name, users.email as user_email')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->where('activity_logs.action', $action)
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activity logs by date range
     */
    public function getActivityLogsByDateRange($startDate, $endDate, $limit = 100)
    {
        return $this->select('activity_logs.*, users.name as user_name, users.email as user_email')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->where('activity_logs.created_at >=', $startDate)
                    ->where('activity_logs.created_at <=', $endDate)
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}

