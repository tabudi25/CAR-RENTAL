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
        'user_id', 'car_id', 'start_date', 'end_date', 'total_price', 'status', 'payment_status', 'payment_method', 'payment_reference', 'created_at'
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
        return $this->select('bookings.*, cars.name as car_name, cars.plate, cars.price_per_day, users.name as customer_name, users.email as customer_email')
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
}