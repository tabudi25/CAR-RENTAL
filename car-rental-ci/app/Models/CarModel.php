<?php

namespace App\Models;

use CodeIgniter\Model;

class CarModel extends Model
{
    protected $table = 'cars';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name', 'plate', 'type', 'category', 'seats', 'price', 
        'status', 'image', 'model', 'brand', 'year', 'price_per_day', 
        'image_url'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]',
        'plate' => 'required|is_unique[cars.plate,id,{id}]',
        'type' => 'required',
        'category' => 'required',
        'seats' => 'required|numeric',
        'price' => 'required|numeric',
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get all available cars
     */
    public function getAvailableCars()
    {
        return $this->where('status', 'available')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Find car by status
     */
    public function findByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}