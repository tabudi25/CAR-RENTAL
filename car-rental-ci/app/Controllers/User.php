<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class User extends BaseController
{
    public function index()
    {
        return $this->response->setJSON(['status' => 'success', 'message' => 'User API is working']);
    }

    public function login()
    {
        $json = $this->request->getJSON();
        $email = $json->email ?? '';
        $password = $json->password ?? '';

        // Simple validation
        if (empty($email) || empty($password)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Email and password are required'
            ]);
        }

        // For demo purposes - in production use proper authentication
        if ($email === 'admin@example.com' && $password === 'admin123') {
            return $this->response->setJSON([
                'status' => 'success',
                'user' => [
                    'id' => 1,
                    'email' => $email,
                    'name' => 'Admin User',
                    'role' => 'admin',
                ],
                'token' => 'admin-token-123'
            ]);
        } else if ($email === 'staff@example.com' && $password === 'staff123') {
            return $this->response->setJSON([
                'status' => 'success',
                'user' => [
                    'id' => 2,
                    'email' => $email,
                    'name' => 'Staff User',
                    'role' => 'staff',
                ],
                'token' => 'staff-token-123'
            ]);
        } else if ($email === 'customer@example.com' && $password === 'customer123') {
            return $this->response->setJSON([
                'status' => 'success',
                'user' => [
                    'id' => 3,
                    'email' => $email,
                    'name' => 'Customer User',
                    'role' => 'customer',
                ],
                'token' => 'customer-token-123'
            ]);
        }

        return $this->response->setStatusCode(401)->setJSON([
            'status' => 'error',
            'message' => 'Invalid email or password'
        ]);
    }

    public function register()
    {
        $json = $this->request->getJSON();
        
        // Simple validation
        if (empty($json->email) || empty($json->password) || empty($json->name)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Name, email and password are required'
            ]);
        }

        // In a real app, save to database
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => [
                'id' => 4, // Demo ID
                'email' => $json->email,
                'name' => $json->name,
                'role' => 'customer', // Default role
            ]
        ]);
    }
}
