<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;

class ActivityLogService
{
    protected $activityLogModel;
    protected $request;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->request = \Config\Services::request();
    }

    /**
     * Log an activity
     */
    public function log($action, $description = null, $userId = null, $userType = null, $relatedId = null, $relatedType = null, $metadata = null)
    {
        try {
            // Check if table exists first
            $db = \Config\Database::connect();
            if (!$db->tableExists('activity_logs')) {
                // Table doesn't exist yet, skip logging
                return;
            }

            // Get user info from session if not provided
            $session = session();
            if (!$userId) {
                $userId = $session->get('user_id');
            }
            if (!$userType) {
                $userType = $session->get('user_type');
            }

            // Prepare metadata as JSON
            $metadataJson = null;
            if ($metadata) {
                $metadataJson = json_encode($metadata);
            }

            // Get user agent safely
            $userAgent = '';
            try {
                $userAgentObj = $this->request->getUserAgent();
                $userAgent = $userAgentObj ? $userAgentObj->getAgentString() : '';
            } catch (\Exception $e) {
                $userAgent = '';
            }

            $data = [
                'user_id' => $userId,
                'user_type' => $userType,
                'action' => $action,
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $userAgent,
                'related_id' => $relatedId,
                'related_type' => $relatedType,
                'metadata' => $metadataJson,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->activityLogModel->insert($data);
        } catch (\Exception $e) {
            // Silently fail - don't break the application if logging fails
            log_message('error', 'Activity log failed: ' . $e->getMessage());
        }
    }

    /**
     * Log customer viewing dashboard
     */
    public function logCustomerDashboardView($userId)
    {
        $this->log(
            'view_dashboard',
            'Customer viewed dashboard',
            $userId,
            'customer'
        );
    }

    /**
     * Log customer viewing car details
     */
    public function logCarView($userId, $carId, $carName)
    {
        $this->log(
            'view_car',
            "Customer viewed car: {$carName}",
            $userId,
            'customer',
            $carId,
            'car',
            ['car_name' => $carName]
        );
    }

    /**
     * Log booking creation
     */
    public function logBookingCreated($userId, $bookingId, $carId, $carName, $startDate, $endDate, $totalAmount)
    {
        $this->log(
            'create_booking',
            "Customer created booking for {$carName} from {$startDate} to {$endDate}",
            $userId,
            'customer',
            $bookingId,
            'booking',
            [
                'car_id' => $carId,
                'car_name' => $carName,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_amount' => $totalAmount
            ]
        );
    }

    /**
     * Log booking cancellation
     */
    public function logBookingCancelled($userId, $bookingId, $carName)
    {
        $this->log(
            'cancel_booking',
            "Customer cancelled booking for {$carName}",
            $userId,
            'customer',
            $bookingId,
            'booking',
            ['car_name' => $carName]
        );
    }

    /**
     * Log viewing bookings
     */
    public function logBookingsView($userId)
    {
        $this->log(
            'view_bookings',
            'Customer viewed bookings list',
            $userId,
            'customer'
        );
    }

    /**
     * Log payment action
     */
    public function logPayment($userId, $bookingId, $paymentType, $amount, $paymentMethod)
    {
        $this->log(
            'payment',
            "Customer made {$paymentType} payment of ₱{$amount} via {$paymentMethod}",
            $userId,
            'customer',
            $bookingId,
            'booking',
            [
                'payment_type' => $paymentType,
                'amount' => $amount,
                'payment_method' => $paymentMethod
            ]
        );
    }
}

