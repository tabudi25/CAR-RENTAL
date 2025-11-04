<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'booking_id', 'user_id', 'type', 'subject', 'message', 'sent_at', 'read_at', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'booking_id' => 'required|numeric',
        'user_id' => 'required|numeric',
        'type' => 'required|in_list[ready_for_pickup,due_date_near,overdue]',
        'subject' => 'required|max_length[255]',
        'message' => 'required',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get notifications for a user
     */
    public function getUserNotifications($userId, $limit = null)
    {
        $builder = $this->select('notifications.*, bookings.start_date, bookings.end_date, cars.name as car_name')
                        ->join('bookings', 'bookings.id = notifications.booking_id')
                        ->join('cars', 'cars.id = bookings.car_id')
                        ->where('notifications.user_id', $userId)
                        ->orderBy('notifications.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('read_at IS NULL')
                    ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
                    ->where('user_id', $userId)
                    ->set('read_at', date('Y-m-d H:i:s'))
                    ->update();
    }
}

