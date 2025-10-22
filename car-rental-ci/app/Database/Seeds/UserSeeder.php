<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Admin User',
                'email' => 'admin@carrental.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'phone' => '+1234567890',
                'user_type' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Staff Member',
                'email' => 'staff@carrental.com',
                'password' => password_hash('staff123', PASSWORD_DEFAULT),
                'phone' => '+1234567891',
                'user_type' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'John Customer',
                'email' => 'customer@carrental.com',
                'password' => password_hash('customer123', PASSWORD_DEFAULT),
                'phone' => '+1234567892',
                'user_type' => 'customer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert users into database
        $this->db->table('users')->insertBatch($data);
        
        echo "User seeder completed successfully!\n";
    }
}
