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

        $data = [
            'cars' => $carModel->findAll(),
            'bookings' => $bookingModel->getBookingsWithDetails(),
            'users' => $userModel->findAll(),
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
            'cars' => $carModel->findAll(),
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
            
            if ($file->isValid() && !$file->hasMoved()) {
                $imageName = $file->getRandomName();
                $file->move('uploads/cars', $imageName);
                $imageName = 'uploads/cars/' . $imageName;
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
            $imageName = $car['image'];
            
            if ($file->isValid() && !$file->hasMoved()) {
                $imageName = $file->getRandomName();
                $file->move('uploads/cars', $imageName);
                $imageName = 'uploads/cars/' . $imageName;
            }
            
            $data = [
                'name' => $this->request->getPost('name'),
                'plate' => $this->request->getPost('plate'),
                'type' => $this->request->getPost('type'),
                'category' => $this->request->getPost('category'),
                'seats' => $this->request->getPost('seats'),
                'price' => $this->request->getPost('price'),
                'image' => $imageName,
                'model' => $this->request->getPost('name'),
                'brand' => explode(' ', $this->request->getPost('name'))[0],
                'price_per_day' => $this->request->getPost('price'),
                'image_url' => $imageName
            ];
            
            if ($carModel->update($id, $data)) {
                return redirect()->to('/admin/cars')->with('success', 'Car updated successfully');
            } else {
                return redirect()->to('/admin/cars')->with('error', 'Failed to update car');
            }
        }
        
        $data = [
            'car' => $car,
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('admin/edit_car', $data);
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
            'bookings' => $bookingModel->getBookingsWithDetails(),
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
        $data = [
            'users' => $userModel->findAll(),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('admin/users', $data);
    }
}