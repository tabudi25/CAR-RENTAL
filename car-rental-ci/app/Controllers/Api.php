<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    public function index()
    {
        return $this->response->setJSON(['status' => 'success', 'message' => 'Car Rental API is working']);
    }

    public function login()
    {
        return (new User())->login();
    }

    public function register()
    {
        return (new User())->register();
    }
}
