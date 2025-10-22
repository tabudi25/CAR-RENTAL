<?php

namespace App\Controllers;

use App\Models\CarModel;
use App\Models\BookingModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Admin extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function index()
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        $userModel = new UserModel();

        // Get statistics
        $totalCars = $carModel->countAllResults();
        $totalBookings = $bookingModel->countAllResults();
        $totalCustomers = $userModel->where('user_type', 'customer')->countAllResults();
        
        // Calculate total revenue
        $bookings = $bookingModel->select('total_price')->findAll();
        $totalRevenue = 0;
        foreach($bookings as $booking) {
            $totalRevenue += $booking['total_price'];
        }
        
        // Car status counts
        $availableCars = $carModel->where('status', 'available')->countAllResults();
        $rentedCars = $carModel->where('status', 'rented')->countAllResults();
        $maintenanceCars = $carModel->where('status', 'maintenance')->countAllResults();
        
        // Recent bookings with customer details
        $recentBookings = $bookingModel->select('bookings.*, users.name as customer_name, cars.name as car_name')
            ->join('users', 'users.id = bookings.user_id')
            ->join('cars', 'cars.id = bookings.car_id')
            ->orderBy('bookings.created_at', 'DESC')
            ->limit(5)
            ->findAll();
            
        // Recent cars
        $recentCars = $carModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        
        // Get booking trends for the last 6 months
        $bookingTrends = [];
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $monthName = date('M', strtotime("-$i months"));
            $months[] = $monthName;
            
            $monthBookings = $bookingModel->where('DATE_FORMAT(created_at, "%Y-%m")', $date)->countAllResults();
            $bookingTrends[] = $monthBookings;
        }
        
        // Get weekly trends for the last 7 days
        $weeklyTrends = [];
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('D', strtotime("-$i days"));
            $days[] = $dayName;
            
            $dayBookings = $bookingModel->where('DATE(created_at)', $date)->countAllResults();
            $weeklyTrends[] = $dayBookings;
        }

        $data = [
            'title' => 'Dashboard',
            'pageTitle' => 'Admin Dashboard',
            'pageSubtitle' => 'Overview of your car rental business',
            'totalCars' => $totalCars,
            'totalBookings' => $totalBookings,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue,
            'availableCars' => $availableCars,
            'rentedCars' => $rentedCars,
            'maintenanceCars' => $maintenanceCars,
            'recentBookings' => $recentBookings,
            'recentCars' => $recentCars,
            'bookingTrends' => $bookingTrends,
            'months' => $months,
            'weeklyTrends' => $weeklyTrends,
            'days' => $days,
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('admin/dashboard', $data);
    }

    public function cars()
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $data = [
            'title' => 'Manage Cars',
            'pageTitle' => 'Manage Cars',
            'pageSubtitle' => 'View and manage your car fleet',
            'cars' => $carModel->findAll(),
            'totalCars' => $carModel->countAllResults(),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('admin/cars', $data);
    }

    public function addCar()
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if ($this->request->getMethod() === 'post') {
            $carModel = new CarModel();
            
            // Handle file upload
            $file = $this->request->getFile('image');
            $imageName = '';
            
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Create uploads/cars directory if it doesn't exist
                $uploadPath = FCPATH . 'uploads/cars';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $imageName = $file->getRandomName();
                if ($file->move($uploadPath, $imageName)) {
                    $imageName = 'uploads/cars/' . $imageName;
                }
            }
            
            $data = [
                'name' => $this->request->getPost('name'),
                'plate' => $this->request->getPost('plate'),
                'type' => $this->request->getPost('type'),
                'category' => $this->request->getPost('category'),
                'seats' => $this->request->getPost('seats'),
                'price' => $this->request->getPost('price'),
                'status' => 'available',
                'image' => $imageName,
                'model' => $this->request->getPost('name'),
                'brand' => explode(' ', $this->request->getPost('name'))[0],
                'year' => date('Y'),
                'price_per_day' => $this->request->getPost('price'),
                'image_url' => $imageName
            ];
            
            if ($carModel->insert($data)) {
                return redirect()->to('/admin/cars')->with('success', 'Car added successfully');
            } else {
                return redirect()->to('/admin/cars')->with('error', 'Failed to add car');
            }
        }
        
        return view('admin/add_car');
    }

    public function editCar($id)
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $car = $carModel->find($id);
        
        if (!$car) {
            return redirect()->to('/admin/cars')->with('error', 'Car not found');
        }
        
        if ($this->request->getMethod() === 'post') {
            // Handle file upload
            $file = $this->request->getFile('image');
            $imageName = $car['image_url']; // Use current image_url as default
            
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Create uploads/cars directory if it doesn't exist
                $uploadPath = FCPATH . 'uploads/cars';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $imageName = $file->getRandomName();
                if ($file->move($uploadPath, $imageName)) {
                    $imageName = 'uploads/cars/' . $imageName;
                } else {
                    $imageName = $car['image_url']; // Keep current image if upload fails
                }
            }
            
            $data = [
                'name' => $this->request->getPost('name'),
                'plate' => $this->request->getPost('plate'),
                'type' => $this->request->getPost('type'),
                'category' => $this->request->getPost('category'),
                'seats' => $this->request->getPost('seats'),
                'price' => $this->request->getPost('price'),
                'status' => $this->request->getPost('status'),
                'year' => $this->request->getPost('year'),
                'image' => $imageName,
                'model' => $this->request->getPost('name'),
                'brand' => explode(' ', $this->request->getPost('name'))[0],
                'price_per_day' => $this->request->getPost('price'),
                'image_url' => $imageName
            ];
            
            if ($carModel->update($id, $data)) {
                return redirect()->to('/admin/cars')->with('success', 'Car updated successfully');
            } else {
                // Get validation errors for debugging
                $errors = $carModel->errors();
                $errorMessage = 'Failed to update car';
                if (!empty($errors)) {
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                return redirect()->to('/admin/cars')->with('error', $errorMessage);
            }
        }
        
        $data = [
            'title' => 'Edit Car',
            'pageTitle' => 'Edit Car',
            'pageSubtitle' => 'Update car information',
            'car' => $car,
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('admin/edit_car', $data);
    }

    public function changeStatus($id, $status)
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $car = $carModel->find($id);
        
        if (!$car) {
            return redirect()->to('/admin/cars')->with('error', 'Car not found');
        }
        
        // Validate status
        $validStatuses = ['available', 'rented', 'maintenance', 'reserved'];
        if (!in_array($status, $validStatuses)) {
            return redirect()->to('/admin/cars')->with('error', 'Invalid status');
        }
        
        if ($carModel->update($id, ['status' => $status])) {
            return redirect()->to('/admin/cars')->with('success', 'Car status updated to ' . ucfirst($status));
        } else {
            return redirect()->to('/admin/cars')->with('error', 'Failed to update car status');
        }
    }

    public function deleteCar($id)
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        
        if ($carModel->delete($id)) {
            return redirect()->to('/admin/cars')->with('success', 'Car deleted successfully');
        } else {
            return redirect()->to('/admin/cars')->with('error', 'Failed to delete car');
        }
    }

    public function bookings()
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $data = [
            'title' => 'Manage Bookings',
            'pageTitle' => 'Manage Bookings',
            'pageSubtitle' => 'View and manage all reservations',
            'bookings' => $bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, cars.name as car_name, cars.plate as car_plate')
                ->join('users', 'users.id = bookings.user_id')
                ->join('cars', 'cars.id = bookings.car_id')
                ->orderBy('bookings.created_at', 'DESC')
                ->findAll(),
            'totalBookings' => $bookingModel->countAllResults(),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('admin/bookings', $data);
    }

    public function updateBookingStatus($id)
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $status = $this->request->getPost('status');
        
        if ($bookingModel->update($id, ['status' => $status])) {
            return redirect()->to('/admin/bookings')->with('success', 'Booking status updated successfully');
        } else {
            return redirect()->to('/admin/bookings')->with('error', 'Failed to update booking status');
        }
    }

    public function users()
    {
        // Check if user is logged in and is admin
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
        
        // Get user statistics
        $totalCustomers = $userModel->where('user_type', 'customer')->countAllResults();
        $totalStaff = $userModel->where('user_type', 'staff')->countAllResults();
        $totalAdmins = $userModel->where('user_type', 'admin')->countAllResults();
        $activeUsers = $userModel->countAllResults();
        
        $data = [
            'title' => 'Manage Users',
            'pageTitle' => 'Manage Users',
            'pageSubtitle' => 'View and manage all system users',
            'users' => $userModel->findAll(),
            'totalCustomers' => $totalCustomers,
            'totalStaff' => $totalStaff,
            'totalAdmins' => $totalAdmins,
            'activeUsers' => $activeUsers,
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('admin/users', $data);
    }
}