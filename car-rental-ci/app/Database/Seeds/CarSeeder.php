<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Mitsubishi Outlander',
                'plate' => 'MIT-001',
                'brand' => 'Mitsubishi',
                'model' => 'Outlander',
                'year' => 2023,
                'type' => 'SUV',
                'category' => 'luxury',
                'seats' => 7,
                'price' => 120.00,
                'price_per_day' => 120.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1549317336-206569e8475c?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'BMW i5 Electric',
                'plate' => 'BMW-002',
                'brand' => 'BMW',
                'model' => 'i5',
                'year' => 2024,
                'type' => 'Sedan',
                'category' => 'luxury',
                'seats' => 5,
                'price' => 180.00,
                'price_per_day' => 180.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Nissan Caravan',
                'plate' => 'NIS-003',
                'brand' => 'Nissan',
                'model' => 'Caravan',
                'year' => 2022,
                'type' => 'Van',
                'category' => 'economy',
                'seats' => 8,
                'price' => 80.00,
                'price_per_day' => 80.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Toyota Vios',
                'plate' => 'TOY-004',
                'brand' => 'Toyota',
                'model' => 'Vios',
                'year' => 2023,
                'type' => 'Sedan',
                'category' => 'economy',
                'seats' => 5,
                'price' => 60.00,
                'price_per_day' => 60.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Honda Civic Sport',
                'plate' => 'HON-005',
                'brand' => 'Honda',
                'model' => 'Civic',
                'year' => 2024,
                'type' => 'Sedan',
                'category' => 'sports',
                'seats' => 5,
                'price' => 100.00,
                'price_per_day' => 100.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ford Explorer',
                'plate' => 'FOR-006',
                'brand' => 'Ford',
                'model' => 'Explorer',
                'year' => 2023,
                'type' => 'SUV',
                'category' => 'luxury',
                'seats' => 7,
                'price' => 140.00,
                'price_per_day' => 140.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1549317336-206569e8475c?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Mercedes-Benz C-Class',
                'plate' => 'MER-007',
                'brand' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'year' => 2024,
                'type' => 'Sedan',
                'category' => 'luxury',
                'seats' => 5,
                'price' => 200.00,
                'price_per_day' => 200.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Nissan Altima',
                'plate' => 'NIS-008',
                'brand' => 'Nissan',
                'model' => 'Altima',
                'year' => 2023,
                'type' => 'Sedan',
                'category' => 'economy',
                'seats' => 5,
                'price' => 70.00,
                'price_per_day' => 70.00,
                'status' => 'available',
                'image_url' => 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=600&fit=crop',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert cars into database
        $this->db->table('cars')->insertBatch($data);
        
        echo "Car seeder completed successfully!\n";
    }
}
