<?php

namespace App\Controllers;

use App\Models\CarModel;
use App\Models\BookingModel;
use CodeIgniter\Controller;

class Customer extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function index()
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        
        $data = [
            'cars' => $carModel->getAvailableCars(),
            'bookings' => $bookingModel->getUserBookings($this->session->get('user_id')),
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('customer/dashboard', $data);
    }

    public function bookCar($carId)
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $car = $carModel->find($carId);
        
        if (!$car) {
            return redirect()->to('/customer')->with('error', 'Car not found');
        }
        
        $data = [
            'car' => $car,
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('customer/book_car', $data);
    }

    public function createBooking()
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }
        
        // Debug: Check if we have a user_id in session
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/customer')->with('error', 'User not found in session');
        }

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        
        $carId = $this->request->getPost('car_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        // Debug: Check if POST data is received
        if (!$carId || !$startDate || !$endDate) {
            return redirect()->to('/customer')->with('error', 'Missing required booking data');
        }
        
        $car = $carModel->find($carId);
        
        if (!$car) {
            return redirect()->to('/customer')->with('error', 'Car not found');
        }
        
        // Calculate total price
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $days = $end->diff($start)->days + 1;
        $totalAmount = $car['price_per_day'] * $days;
        
        // Prepare data based on what columns exist in the database
        $data = [
            'user_id' => $this->session->get('user_id'),
            'car_id' => $carId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Use the correct field name based on the actual database schema
        $data['total_price'] = $totalAmount;
        
        try {
            if ($bookingModel->insert($data)) {
                // Update car status
                $carModel->update($carId, ['status' => 'reserved']);
                return redirect()->to('/customer')->with('success', 'Booking created successfully');
            } else {
                // Get validation errors
                $errors = $bookingModel->errors();
                $errorMessage = 'Failed to create booking';
                if (!empty($errors)) {
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                return redirect()->to('/customer')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Create booking error: ' . $e->getMessage());
            return redirect()->to('/customer')->with('error', 'An error occurred while creating the booking. Please try again.');
        }
    }

    public function viewBookings()
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        try {
            $bookingModel = new BookingModel();
            
            $bookings = $bookingModel->getUserBookings($this->session->get('user_id'));
            
            // Ensure all bookings have the required fields
            foreach ($bookings as &$booking) {
                if (!isset($booking['car_name'])) {
                    $booking['car_name'] = 'Unknown Car';
                }
            }
            
            $data = [
                'bookings' => $bookings,
                'user' => [
                    'id' => $this->session->get('user_id'),
                    'name' => $this->session->get('name'),
                    'email' => $this->session->get('email'),
                    'user_type' => $this->session->get('user_type')
                ]
            ];
            
            return view('customer/bookings', $data);
        } catch (\Exception $e) {
            log_message('error', 'View bookings error: ' . $e->getMessage());
            return redirect()->to('/customer')->with('error', 'Unable to load bookings. Please try again.');
        }
    }

    public function cancelBooking($id)
    {
        // Debug: Log the request
        log_message('debug', 'Cancel booking method called with ID: ' . $id);
        
        try {
            // Check if user is logged in and is customer
            if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
                log_message('debug', 'User not logged in or not customer');
                return redirect()->to('/auth/login')->with('error', 'Please login to continue');
            }

            $bookingModel = new BookingModel();
            $carModel = new CarModel();
            
            $booking = $bookingModel->find($id);
            
            // Check if booking exists
            if (!$booking) {
                return redirect()->to('/customer/bookings')->with('error', 'Booking not found');
            }
            
            // Check if user owns this booking
            if ($booking['user_id'] != $this->session->get('user_id')) {
                return redirect()->to('/customer/bookings')->with('error', 'You are not authorized to cancel this booking');
            }
            
            // Check if booking can be cancelled (only pending or confirmed bookings)
            if (!in_array($booking['status'], ['pending', 'confirmed'])) {
                return redirect()->to('/customer/bookings')->with('error', 'This booking cannot be cancelled. Only pending or confirmed bookings can be cancelled.');
            }
            
            // Start database transaction
            $this->db = \Config\Database::connect();
            $this->db->transStart();
            
            // Update booking status to cancelled
            $updateData = ['status' => 'cancelled'];
            
            // Only add updated_at if the column exists
            if ($this->db->fieldExists('updated_at', 'bookings')) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            $updateResult = $bookingModel->update($id, $updateData);
            
            if (!$updateResult) {
                $this->db->transRollback();
                return redirect()->to('/customer/bookings')->with('error', 'Failed to update booking status');
            }
            
            // Update car status back to available
            $carUpdateData = ['status' => 'available'];
            
            // Only add updated_at if the column exists
            if ($this->db->fieldExists('updated_at', 'cars')) {
                $carUpdateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            $carUpdateResult = $carModel->update($booking['car_id'], $carUpdateData);
            
            if (!$carUpdateResult) {
                $this->db->transRollback();
                return redirect()->to('/customer/bookings')->with('error', 'Failed to update car status');
            }
            
            // Commit transaction
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return redirect()->to('/customer/bookings')->with('error', 'Transaction failed. Please try again.');
            }
            
            return redirect()->to('/customer/bookings')->with('success', 'Booking cancelled successfully');
            
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Cancel booking error: ' . $e->getMessage());
            return redirect()->to('/customer/bookings')->with('error', 'An error occurred while cancelling the booking. Please try again.');
        }
    }
    
    public function testCancel($id)
    {
        return "Test cancel booking for ID: " . $id;
    }
}