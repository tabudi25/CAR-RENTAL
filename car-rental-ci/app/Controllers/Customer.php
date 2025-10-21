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

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        
        $carId = $this->request->getPost('car_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        $car = $carModel->find($carId);
        
        if (!$car) {
            return redirect()->to('/customer')->with('error', 'Car not found');
        }
        
        // Calculate total price
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $days = $end->diff($start)->days + 1;
        $totalPrice = $car['price'] * $days;
        
        $data = [
            'user_id' => $this->session->get('user_id'),
            'car_id' => $carId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'total_price' => $totalPrice
        ];
        
        if ($bookingModel->insert($data)) {
            // Update car status
            $carModel->update($carId, ['status' => 'reserved', 'available' => 0]);
            return redirect()->to('/customer')->with('success', 'Booking created successfully');
        } else {
            return redirect()->to('/customer')->with('error', 'Failed to create booking');
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
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $carModel = new CarModel();
        
        $booking = $bookingModel->find($id);
        
        if (!$booking || $booking['user_id'] != $this->session->get('user_id')) {
            return redirect()->to('/customer/bookings')->with('error', 'Booking not found or unauthorized');
        }
        
        if ($bookingModel->update($id, ['status' => 'cancelled'])) {
            // Update car status
            $carModel->update($booking['car_id'], ['status' => 'available', 'available' => 1]);
            return redirect()->to('/customer/bookings')->with('success', 'Booking cancelled successfully');
        } else {
            return redirect()->to('/customer/bookings')->with('error', 'Failed to cancel booking');
        }
    }
}