<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBookingPaymentFields extends Migration
{
    public function up()
    {
        // Ensure total_price field exists (some schemas only have total_amount)
        if (!$this->db->fieldExists('total_price', 'bookings')) {
            if ($this->db->fieldExists('total_amount', 'bookings')) {
                // Add total_price as alias to total_amount
                $this->db->query("ALTER TABLE bookings ADD COLUMN total_price DECIMAL(10,2) AFTER total_amount");
            } else {
                // Add total_price if neither exists
                $this->forge->addColumn('bookings', [
                    'total_price' => [
                        'type' => 'DECIMAL',
                        'constraint' => '10,2',
                        'null' => true,
                        'after' => 'end_date',
                    ],
                ]);
            }
        }

        // Ensure payment_status exists with correct ENUM values (including 'partial')
        if (!$this->db->fieldExists('payment_status', 'bookings')) {
            $this->forge->addColumn('bookings', [
                'payment_status' => [
                    'type' => 'ENUM',
                    'constraint' => ['pending', 'partial', 'paid', 'refunded'],
                    'default' => 'pending',
                    'after' => 'status',
                ],
            ]);
        } else {
            // Modify existing payment_status to include 'partial' if it doesn't
            try {
                $this->db->query("ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('pending', 'partial', 'paid', 'refunded') DEFAULT 'pending'");
            } catch (\Exception $e) {
                // If modification fails, it might already have the correct values
                log_message('debug', 'Payment status enum modification: ' . $e->getMessage());
            }
        }

        // Add pick-up time field
        if (!$this->db->fieldExists('pick_up_time', 'bookings')) {
            $this->forge->addColumn('bookings', [
                'pick_up_time' => [
                    'type' => 'TIME',
                    'null' => true,
                    'after' => 'start_date',
                ],
            ]);
        }

        // Add down payment amount
        if (!$this->db->fieldExists('down_payment_amount', 'bookings')) {
            $afterField = $this->db->fieldExists('total_amount', 'bookings') ? 'total_amount' : 'total_price';
            $this->forge->addColumn('bookings', [
                'down_payment_amount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => true,
                    'after' => $afterField,
                ],
            ]);
        }

        // Add payment method
        if (!$this->db->fieldExists('payment_method', 'bookings')) {
            $afterField = $this->db->fieldExists('payment_status', 'bookings') ? 'payment_status' : 'status';
            $this->forge->addColumn('bookings', [
                'payment_method' => [
                    'type' => 'ENUM',
                    'constraint' => ['gcash', 'bank_transfer'],
                    'null' => true,
                    'after' => $afterField,
                ],
            ]);
        }

        // Add overdue fields
        if (!$this->db->fieldExists('overdue_charge', 'bookings')) {
            $this->forge->addColumn('bookings', [
                'overdue_charge' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => '0.00',
                    'after' => 'down_payment_amount',
                ],
            ]);
        }

        if (!$this->db->fieldExists('overdue_days', 'bookings')) {
            $this->forge->addColumn('bookings', [
                'overdue_days' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'after' => 'overdue_charge',
                ],
            ]);
        }

        // Add notification fields
        if (!$this->db->fieldExists('ready_for_pickup_notification', 'bookings')) {
            $this->forge->addColumn('bookings', [
                'ready_for_pickup_notification' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_at',
                ],
            ]);
        }

        if (!$this->db->fieldExists('due_date_notification_sent', 'bookings')) {
            $this->forge->addColumn('bookings', [
                'due_date_notification_sent' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'ready_for_pickup_notification',
                ],
            ]);
        }

        if (!$this->db->fieldExists('overdue_notification_sent', 'bookings')) {
            $this->forge->addColumn('bookings', [
                'overdue_notification_sent' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'due_date_notification_sent',
                ],
            ]);
        }
    }

    public function down()
    {
        $fieldsToRemove = [
            'pick_up_time',
            'down_payment_amount',
            'payment_method',
            'overdue_charge',
            'overdue_days',
            'ready_for_pickup_notification',
            'due_date_notification_sent',
            'overdue_notification_sent',
        ];

        foreach ($fieldsToRemove as $field) {
            if ($this->db->fieldExists($field, 'bookings')) {
                $this->forge->dropColumn('bookings', $field);
            }
        }
    }
}

