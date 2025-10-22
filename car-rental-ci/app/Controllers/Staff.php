<?php

namespace App\Controllers;

use App\Models\CarModel;
use App\Models\BookingModel;
use CodeIgniter\Controller;

class Staff extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function index()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        $userModel = new \App\Models\UserModel();
        
        // Get statistics
        $pendingBookings = $bookingModel->where('status', 'pending')->countAllResults();
        $availableCars = $carModel->where('status', 'available')->countAllResults();
        $rentedCars = $carModel->where('status', 'rented')->countAllResults();
        $maintenanceCars = $carModel->where('status', 'maintenance')->countAllResults();
        $reservedCars = $carModel->where('status', 'reserved')->countAllResults();
        $activeCustomers = $userModel->where('user_type', 'customer')->countAllResults();
        
        // Today's operations
        $today = date('Y-m-d');
        $todayCheckins = $bookingModel->where('DATE(created_at)', $today)->countAllResults();
        $todayCheckouts = $bookingModel->where('DATE(updated_at)', $today)->where('status', 'completed')->countAllResults();
        
        // Recent bookings
        $recentBookings = $bookingModel->select('bookings.*, users.name as customer_name, cars.name as car_name')
            ->join('users', 'users.id = bookings.user_id')
            ->join('cars', 'cars.id = bookings.car_id')
            ->orderBy('bookings.created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        // Generate alerts
        $alerts = [];
        if ($maintenanceCars > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'wrench',
                'message' => "{$maintenanceCars} car(s) are in maintenance and need attention."
            ];
        }
        if ($pendingBookings > 5) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'clock',
                'message' => "You have {$pendingBookings} pending bookings to process."
            ];
        }
        if ($availableCars < 3) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'message' => "Low car availability! Only {$availableCars} cars available."
            ];
        }
        
        $data = [
            'title' => 'Staff Dashboard',
            'pageTitle' => 'Staff Dashboard',
            'pageSubtitle' => 'Manage daily operations and customer service',
            'pendingBookings' => $pendingBookings,
            'availableCars' => $availableCars,
            'rentedCars' => $rentedCars,
            'maintenanceCars' => $maintenanceCars,
            'reservedCars' => $reservedCars,
            'activeCustomers' => $activeCustomers,
            'todayCheckins' => $todayCheckins,
            'todayCheckouts' => $todayCheckouts,
            'recentBookings' => $recentBookings,
            'alerts' => $alerts,
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('staff/dashboard', $data);
    }

    public function manageBookings()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        
        $data = [
            'bookings' => $bookingModel->getBookingsWithDetails(),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('staff/bookings', $data);
    }

    public function updateBookingStatus($id)
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $carModel = new CarModel();
        $status = $this->request->getPost('status');
        
        $booking = $bookingModel->find($id);
        
        if (!$booking) {
            return redirect()->to('/staff/bookings')->with('error', 'Booking not found');
        }
        
        if ($bookingModel->update($id, ['status' => $status])) {
            // Update car status based on booking status
            $carStatus = 'available';
            
            if ($status === 'confirmed') {
                $carStatus = 'reserved';
            } else if ($status === 'completed') {
                $carStatus = 'available';
            } else if ($status === 'cancelled') {
                $carStatus = 'available';
            }
            
            $carModel->update($booking['car_id'], ['status' => $carStatus]);
            return redirect()->to('/staff/bookings')->with('success', 'Booking status updated successfully');
        } else {
            return redirect()->to('/staff/bookings')->with('error', 'Failed to update booking status');
        }
    }

    public function manageCars()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        
        $data = [
            'cars' => $carModel->findAll(),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('staff/cars', $data);
    }

    public function updateCarStatus($id)
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $status = $this->request->getPost('status');
        
        if ($carModel->update($id, ['status' => $status])) {
            return redirect()->to('/staff/cars')->with('success', 'Car status updated successfully');
        } else {
            return redirect()->to('/staff/cars')->with('error', 'Failed to update car status');
        }
    }
}