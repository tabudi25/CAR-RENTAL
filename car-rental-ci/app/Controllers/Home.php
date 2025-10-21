<?php

namespace App\Controllers;

use App\Models\CarModel;

class Home extends BaseController
{
    public function index(): string
    {
        $carModel = new CarModel();
        $data = [
            'cars' => $carModel->getAvailableCars()
        ];
        
        return view('home', $data);
    }
}
