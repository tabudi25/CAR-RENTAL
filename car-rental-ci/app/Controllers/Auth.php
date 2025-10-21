<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function index()
    {
        return redirect()->to('/auth/login');
    }

    public function login()
    {
        return view('auth/login');
    }

    public function authenticate()
    {
        $userModel = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->authenticate($email, $password);

        if ($user) {
            $session = session();
            $userData = [
                'user_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'user_type' => $user['user_type'],
                'logged_in' => TRUE
            ];
            $session->set($userData);

            // Redirect based on user type
            switch ($user['user_type']) {
                case 'admin':
                    return redirect()->to('/admin');
                case 'staff':
                    return redirect()->to('/staff');
                case 'customer':
                    return redirect()->to('/customer');
                default:
                    return redirect()->to('/');
            }
        } else {
            return redirect()->to('/auth/login')->with('error', 'Invalid email or password');
        }
    }

    public function register()
    {
        return view('auth/register');
    }

    public function createAccount()
    {
        $userModel = new UserModel();
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'phone' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/auth/register')->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'phone' => $this->request->getPost('phone'),
            'user_type' => 'customer' // Default user type
        ];

        $userModel->insert($data);
        return redirect()->to('/auth/login')->with('success', 'Registration successful. Please login.');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth/login');
    }
}