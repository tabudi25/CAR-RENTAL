<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToBookings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bookings', [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bookings', 'updated_at');
    }
}

