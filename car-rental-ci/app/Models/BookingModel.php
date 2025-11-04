<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id', 'car_id', 'start_date', 'end_date', 'pick_up_time', 'total_price', 'total_amount', 'down_payment_amount', 
        'status', 'payment_status', 'payment_method', 'payment_reference', 'overdue_charge', 'overdue_days',
        'ready_for_pickup_notification', 'due_date_notification_sent', 'overdue_notification_sent', 'return_requested_at', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = false; // Disable automatic timestamps
    protected $dateFormat = 'datetime';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'car_id' => 'required|numeric',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get bookings with car and user details
     */
    public function getBookingsWithDetails()
    {
        return $this->select('bookings.*, cars.name as car_name, cars.plate as car_plate, cars.price_per_day, users.name as customer_name, users.email as customer_email')
                    ->join('cars', 'cars.id = bookings.car_id')
                    ->join('users', 'users.id = bookings.user_id')
                    ->orderBy('bookings.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get user bookings
     */
    public function getUserBookings($userId)
    {
        return $this->select('bookings.*, cars.name as car_name, cars.plate, cars.image_url, cars.model as car_model, cars.price_per_day')
                    ->join('cars', 'cars.id = bookings.car_id')
                    ->where('bookings.user_id', $userId)
                    ->orderBy('bookings.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get bookings that are due soon (within 1 day)
     */
    public function getBookingsDueSoon()
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $query = $this->select('bookings.*, users.name as customer_name, users.email as customer_email, cars.name as car_name')
                    ->join('users', 'users.id = bookings.user_id')
                    ->join('cars', 'cars.id = bookings.car_id')
                    ->where('bookings.end_date', $tomorrow)
                    ->where('bookings.status', 'confirmed');
        
        // Only filter by due_date_notification_sent if the column exists
        $db = \Config\Database::connect();
        if ($db->fieldExists('due_date_notification_sent', 'bookings')) {
            $query->where('bookings.due_date_notification_sent IS NULL');
        }
        
        return $query->findAll();
    }

    /**
     * Get overdue bookings
     */
    public function getOverdueBookings()
    {
        $today = date('Y-m-d');
        $query = $this->select('bookings.*, users.name as customer_name, users.email as customer_email, cars.name as car_name, cars.price_per_day')
                    ->join('users', 'users.id = bookings.user_id')
                    ->join('cars', 'cars.id = bookings.car_id')
                    ->where('bookings.end_date <', $today)
                    ->where('bookings.status', 'confirmed');
        
        // Only filter by overdue_notification_sent if the column exists
        $db = \Config\Database::connect();
        if ($db->fieldExists('overdue_notification_sent', 'bookings')) {
            $query->where('bookings.overdue_notification_sent IS NULL');
        }
        
        return $query->findAll();
    }
}