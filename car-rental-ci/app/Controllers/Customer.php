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
        $totalPrice = $car['price_per_day'] * $days;
        
        $data = [
            'user_id' => $this->session->get('user_id'),
            'car_id' => $carId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
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
    }

    public function viewBookings()
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        
        $data = [
            'bookings' => $bookingModel->getUserBookings($this->session->get('user_id')),
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('customer/bookings', $data);
    }

    public function cancelBooking($id)
    {
        // Debug: Log the request
        log_message('debug', 'Cancel booking request for ID: ' . $id);
        
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            log_message('debug', 'User not logged in or not customer');
            return redirect()->to('/auth/login');
        }
        
        log_message('debug', 'User logged in: ' . $this->session->get('user_id'));

        $bookingModel = new BookingModel();
        $carModel = new CarModel();
        
        $booking = $bookingModel->find($id);
        
        // Debug: Check if booking exists and user authorization
        if (!$booking) {
            return redirect()->to('/customer/bookings')->with('error', 'Booking not found');
        }
        
        if ($booking['user_id'] != $this->session->get('user_id')) {
            return redirect()->to('/customer/bookings')->with('error', 'You are not authorized to cancel this booking');
        }
        
        // Check if booking can be cancelled (only pending or confirmed bookings)
        if (!in_array($booking['status'], ['pending', 'confirmed'])) {
            return redirect()->to('/customer/bookings')->with('error', 'This booking cannot be cancelled');
        }
        
        if ($bookingModel->update($id, ['status' => 'cancelled'])) {
            // Update car status back to available
            $carModel->update($booking['car_id'], ['status' => 'available']);
            return redirect()->to('/customer/bookings')->with('success', 'Booking cancelled successfully');
        } else {
            return redirect()->to('/customer/bookings')->with('error', 'Failed to cancel booking');
        }
    }
    
    public function testCancel($id)
    {
        return "Test cancel booking for ID: " . $id;
    }
}