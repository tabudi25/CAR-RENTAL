<?php

namespace App\Controllers;

use App\Models\CarModel;
use App\Models\BookingModel;
use App\Models\ActivityLogModel;
use App\Libraries\NotificationService;
use CodeIgniter\Controller;

class Staff extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function index()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        $userModel = new \App\Models\UserModel();
        
        // Get statistics with error handling
        try {
            $pendingBookings = $bookingModel->where('status', 'pending')->countAllResults();
            $availableCars = $carModel->where('status', 'available')->countAllResults();
            $rentedCars = $carModel->where('status', 'rented')->countAllResults();
            $maintenanceCars = $carModel->where('status', 'maintenance')->countAllResults();
            $reservedCars = $carModel->where('status', 'reserved')->countAllResults();
            $activeCustomers = $userModel->where('user_type', 'customer')->countAllResults();
        } catch (\Exception $e) {
            // Set default values if there's an error
            $pendingBookings = 0;
            $availableCars = 0;
            $rentedCars = 0;
            $maintenanceCars = 0;
            $reservedCars = 0;
            $activeCustomers = 0;
        }
        
        // Today's operations with error handling
        try {
            $today = date('Y-m-d');
            $todayCheckins = $bookingModel->where('DATE(created_at)', $today)->countAllResults();
            
            // Check if updated_at column exists before using it
            $db = \Config\Database::connect();
            if ($db->fieldExists('updated_at', 'bookings')) {
                $todayCheckouts = $bookingModel->where('DATE(updated_at)', $today)->where('status', 'completed')->countAllResults();
            } else {
                $todayCheckouts = 0;
            }
        } catch (\Exception $e) {
            $todayCheckins = 0;
            $todayCheckouts = 0;
        }
        
        // Recent bookings with error handling
        try {
            $recentBookings = $bookingModel->select('bookings.*, users.name as customer_name, cars.name as car_name')
                ->join('users', 'users.id = bookings.user_id')
                ->join('cars', 'cars.id = bookings.car_id')
                ->orderBy('bookings.created_at', 'DESC')
                ->limit(5)
                ->findAll();
        } catch (\Exception $e) {
            $recentBookings = [];
        }
        
        // Generate alerts
        $alerts = [];
        if ($maintenanceCars > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'wrench',
                'message' => "{$maintenanceCars} car(s) are in maintenance and need attention."
            ];
        }
        if ($pendingBookings > 5) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'clock',
                'message' => "You have {$pendingBookings} pending bookings to process."
            ];
        }
        if ($availableCars < 3) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'message' => "Low car availability! Only {$availableCars} cars available."
            ];
        }
        
        $data = [
            'title' => 'Staff Dashboard',
            'pageTitle' => 'Staff Dashboard',
            'pageSubtitle' => 'Manage daily operations and customer service',
            'pendingBookings' => $pendingBookings,
            'availableCars' => $availableCars,
            'rentedCars' => $rentedCars,
            'maintenanceCars' => $maintenanceCars,
            'reservedCars' => $reservedCars,
            'activeCustomers' => $activeCustomers,
            'todayCheckins' => $todayCheckins,
            'todayCheckouts' => $todayCheckouts,
            'recentBookings' => $recentBookings,
            'alerts' => $alerts,
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('staff/dashboard', $data);
    }

    public function manageBookings()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
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
        
        return view('staff/bookings', $data);
    }

    public function updateBookingStatus($id)
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $carModel = new CarModel();
        $status = $this->request->getPost('status');
        
        $booking = $bookingModel->find($id);
        
        if (!$booking) {
            return redirect()->to('/staff/bookings')->with('error', 'Booking not found');
        }
        
        // Prepare update data
        $updateData = ['status' => $status];
        
        // If status is being changed to 'completed', also update payment status to 'paid' if it's pending/partial
        if ($status === 'completed') {
            $db = \Config\Database::connect();
            $fields = $db->getFieldNames('bookings');
            
            if (in_array('payment_status', $fields)) {
                $currentPaymentStatus = $booking['payment_status'] ?? 'pending';
                if (in_array($currentPaymentStatus, ['pending', 'partial'])) {
                    try {
                        $enumResult = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'payment_status'")->getRow();
                        if ($enumResult) {
                            preg_match("/^enum\((.*)\)$/", $enumResult->Type, $matches);
                            if (!empty($matches[1])) {
                                $enumValues = str_replace("'", "", explode(",", $matches[1]));
                                if (in_array('paid', $enumValues)) {
                                    $updateData['payment_status'] = 'paid';
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        log_message('debug', 'Payment status update check: ' . $e->getMessage());
                        $updateData['payment_status'] = 'paid';
                    }
                }
            }
        }
        
        if ($bookingModel->update($id, $updateData)) {
            // Update car status based on booking status
            $carStatus = 'available';
            
            if ($status === 'confirmed') {
                $carStatus = 'reserved';
            } else if ($status === 'completed') {
                $carStatus = 'available';
            } else if ($status === 'cancelled') {
                $carStatus = 'available';
            }
            
            $carModel->update($booking['car_id'], ['status' => $carStatus]);
            return redirect()->to('/staff/bookings')->with('success', 'Booking status updated successfully');
        } else {
            return redirect()->to('/staff/bookings')->with('error', 'Failed to update booking status');
        }
    }

    /**
     * Send car ready for pick-up notification
     */
    public function sendReadyForPickupNotification($bookingId)
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $notificationService = new NotificationService();
        
        if ($notificationService->sendReadyForPickupNotification($bookingId, $this->session->get('user_id'))) {
            return redirect()->to('/staff/bookings')->with('success', 'Car ready for pick-up notification sent successfully');
        } else {
            return redirect()->to('/staff/bookings')->with('error', 'Failed to send notification');
        }
    }

    /**
     * Process automatic notifications (due date near and overdue)
     */
    public function processNotifications()
    {
        // Check if user is logged in and is staff or admin
        if (!$this->session->has('logged_in') || !in_array($this->session->get('user_type'), ['staff', 'admin'])) {
            return redirect()->to('/auth/login');
        }

        $notificationService = new NotificationService();
        $notificationService->processAutomaticNotifications();
        
        return redirect()->to('/staff')->with('success', 'Automatic notifications processed successfully');
    }

    public function manageCars()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
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
        
        return view('staff/cars', $data);
    }

    public function updateCarStatus($id)
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $status = $this->request->getPost('status');
        
        if ($carModel->update($id, ['status' => $status])) {
            return redirect()->to('/staff/cars')->with('success', 'Car status updated successfully');
        } else {
            return redirect()->to('/staff/cars')->with('error', 'Failed to update car status');
        }
    }

    public function manageCustomers()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $userModel = new \App\Models\UserModel();
        
        $data = [
            'customers' => $userModel->where('user_type', 'customer')->findAll(),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('staff/customers', $data);
    }

    public function bookingDetails()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $carModel = new CarModel();
        $userModel = new \App\Models\UserModel();
        
        // Get all bookings with detailed information
        $bookings = $bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, users.phone as customer_phone, cars.name as car_name, cars.brand, cars.model, cars.year, cars.plate, cars.price_per_day')
            ->join('users', 'users.id = bookings.user_id')
            ->join('cars', 'cars.id = bookings.car_id')
            ->orderBy('bookings.created_at', 'DESC')
            ->findAll();
        
        // Get statistics
        $totalBookings = count($bookings);
        $pendingBookings = array_filter($bookings, function($booking) {
            return $booking['status'] === 'pending';
        });
        $confirmedBookings = array_filter($bookings, function($booking) {
            return $booking['status'] === 'confirmed';
        });
        $completedBookings = array_filter($bookings, function($booking) {
            return $booking['status'] === 'completed';
        });
        
        $data = [
            'title' => 'Booking Details',
            'pageTitle' => 'Booking Details',
            'pageSubtitle' => 'View detailed information about all bookings',
            'bookings' => $bookings,
            'totalBookings' => $totalBookings,
            'pendingBookings' => count($pendingBookings),
            'confirmedBookings' => count($confirmedBookings),
            'completedBookings' => count($completedBookings),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('staff/booking', $data);
    }

    public function maintenance()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $data = [
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        return view('staff/maintenance', $data);
    }

    public function activityLogs()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $activityLogModel = new ActivityLogModel();
        
        // Get filter parameters
        $action = $this->request->getGet('action');
        $userId = $this->request->getGet('user_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $limit = 100; // Default limit

        // Build query
        $logs = $activityLogModel->select('activity_logs.*, users.name as user_name, users.email as user_email')
                                 ->join('users', 'users.id = activity_logs.user_id', 'left')
                                 ->orderBy('activity_logs.created_at', 'DESC');

        if ($action) {
            $logs->where('activity_logs.action', $action);
        }

        if ($userId) {
            $logs->where('activity_logs.user_id', $userId);
        }

        if ($startDate) {
            $logs->where('activity_logs.created_at >=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $logs->where('activity_logs.created_at <=', $endDate . ' 23:59:59');
        }

        $logs = $logs->limit($limit)->findAll();

        // Get unique actions for filter dropdown
        $uniqueActions = $activityLogModel->select('DISTINCT action')
                                          ->orderBy('action', 'ASC')
                                          ->findAll();

        $data = [
            'title' => 'Activity Logs',
            'logs' => $logs,
            'actions' => array_column($uniqueActions, 'action'),
            'filters' => [
                'action' => $action,
                'user_id' => $userId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('staff/activity_logs', $data);
    }

    public function checkout()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $booking = null;
        $todayCheckouts = 0;

        // Get today's checkouts count
        try {
            $db = \Config\Database::connect();
            $today = date('Y-m-d');
            
            // Check if updated_at column exists, otherwise use created_at or current date logic
            if ($db->fieldExists('updated_at', 'bookings')) {
                $todayCheckouts = $bookingModel->where('DATE(updated_at)', $today)
                                               ->where('status', 'completed')
                                               ->countAllResults();
            } elseif ($db->fieldExists('created_at', 'bookings')) {
                $todayCheckouts = $bookingModel->where('DATE(created_at)', $today)
                                               ->where('status', 'completed')
                                               ->countAllResults();
            } else {
                // Fallback: count completed bookings without date filter
                $todayCheckouts = $bookingModel->where('status', 'completed')
                                               ->countAllResults();
            }
        } catch (\Exception $e) {
            $todayCheckouts = 0;
        }

        // Handle search form submission
        if ($this->request->getMethod() === 'post') {
            $bookingId = $this->request->getPost('booking_id');
            $carPlate = $this->request->getPost('car_plate');

            if ($bookingId) {
                $booking = $bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, users.phone as customer_phone, cars.name as car_name, cars.plate as car_plate, cars.brand, cars.model, cars.year, cars.price_per_day')
                                       ->join('users', 'users.id = bookings.user_id')
                                       ->join('cars', 'cars.id = bookings.car_id')
                                       ->where('bookings.id', $bookingId)
                                       ->whereIn('bookings.status', ['confirmed', 'pending'])
                                       ->first();
            } elseif ($carPlate) {
                $booking = $bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, users.phone as customer_phone, cars.name as car_name, cars.plate as car_plate, cars.brand, cars.model, cars.year, cars.price_per_day')
                                       ->join('users', 'users.id = bookings.user_id')
                                       ->join('cars', 'cars.id = bookings.car_id')
                                       ->where('cars.plate', $carPlate)
                                       ->whereIn('bookings.status', ['confirmed', 'pending'])
                                       ->orderBy('bookings.start_date', 'DESC')
                                       ->first();
            }

            if (!$booking) {
                return redirect()->to('/staff/checkout')->with('error', 'No active rental found with the provided information.');
            }
        }

        $data = [
            'booking' => $booking,
            'todayCheckouts' => $todayCheckouts,
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        return view('staff/checkout', $data);
    }

    public function processCheckout()
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $carModel = new CarModel();

        $bookingId = $this->request->getPost('booking_id');
        $mileageEnd = $this->request->getPost('mileage_end');
        $fuelLevelReturn = $this->request->getPost('fuel_level_return');
        $damageReport = $this->request->getPost('damage_report');
        $checkoutNotes = $this->request->getPost('checkout_notes');
        $additionalFees = $this->request->getPost('additional_fees') ?? 0;

        if (!$bookingId) {
            return redirect()->to('/staff/checkout')->with('error', 'Booking ID is required.');
        }

        $booking = $bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->to('/staff/checkout')->with('error', 'Booking not found.');
        }

        // Prepare update data
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('bookings');

        $updateData = [
            'status' => 'completed'
        ];

        // Update payment status to 'paid' when booking is completed (if payment_status column exists)
        if (in_array('payment_status', $fields)) {
            $currentPaymentStatus = $booking['payment_status'] ?? 'pending';
            // If payment status is pending or partial, mark as paid when checkout is completed
            // This assumes all payments are settled upon return
            if (in_array($currentPaymentStatus, ['pending', 'partial'])) {
                try {
                    // Verify 'paid' is a valid ENUM value before setting
                    $enumResult = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'payment_status'")->getRow();
                    if ($enumResult) {
                        preg_match("/^enum\((.*)\)$/", $enumResult->Type, $matches);
                        if (!empty($matches[1])) {
                            $enumValues = str_replace("'", "", explode(",", $matches[1]));
                            if (in_array('paid', $enumValues)) {
                                $updateData['payment_status'] = 'paid';
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // If we can't verify, try setting it anyway (database will validate)
                    log_message('debug', 'Payment status update check: ' . $e->getMessage());
                    $updateData['payment_status'] = 'paid';
                }
            }
        }

        // Add optional fields if they exist
        if (in_array('mileage_end', $fields) && $mileageEnd) {
            $updateData['mileage_end'] = $mileageEnd;
        }
        if (in_array('fuel_level_return', $fields) && $fuelLevelReturn) {
            $updateData['fuel_level_return'] = $fuelLevelReturn;
        }
        if (in_array('damage_report', $fields) && $damageReport) {
            $updateData['damage_report'] = $damageReport;
        }
        if (in_array('damage_notes', $fields) && $checkoutNotes) {
            $updateData['damage_notes'] = $checkoutNotes;
        }
        if (in_array('additional_charges', $fields) && $additionalFees) {
            $updateData['additional_charges'] = (float)$additionalFees;
        }
        if (in_array('additional_fees', $fields) && $additionalFees) {
            $updateData['additional_fees'] = (float)$additionalFees;
        }
        // Only update updated_at if it exists (it's optional)
        if (in_array('updated_at', $fields)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
        }

        // Update booking status to completed
        if ($bookingModel->update($bookingId, $updateData)) {
            // Update car status back to available
            $carModel->update($booking['car_id'], ['status' => 'available']);
            
            return redirect()->to('/staff/checkout')->with('success', 'Check-out completed successfully. Car is now available.');
        } else {
            return redirect()->to('/staff/checkout')->with('error', 'Failed to complete check-out. Please try again.');
        }
    }

    public function processReturn($bookingId)
    {
        // This method is kept for backward compatibility but redirects to markReturned
        return $this->markReturned($bookingId);
    }

    public function markReturned($bookingId)
    {
        // Check if user is logged in and is staff
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'staff') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        $carModel = new CarModel();

        $booking = $bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->to('/staff/bookings')->with('error', 'Booking not found.');
        }

        // Check if status is return_requested
        if ($booking['status'] !== 'return_requested') {
            return redirect()->to('/staff/bookings')->with('error', 'This booking is not in return_requested status.');
        }

        // Ensure 'returned' status exists in ENUM
        $db = \Config\Database::connect();
        try {
            $enumResult = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'")->getRow();
            if ($enumResult) {
                preg_match("/^enum\((.*)\)$/", $enumResult->Type, $matches);
                if (!empty($matches[1])) {
                    $enumValues = str_replace("'", "", explode(",", $matches[1]));
                    if (!in_array('returned', $enumValues)) {
                        $enumValues[] = 'returned';
                        $enumString = "'" . implode("','", $enumValues) . "'";
                        $db->query("ALTER TABLE bookings MODIFY COLUMN status ENUM({$enumString}) DEFAULT 'pending'");
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to update status ENUM: ' . $e->getMessage());
        }

        // Prepare update data
        $fields = $db->getFieldNames('bookings');
        $updateData = [
            'status' => 'returned'
        ];

        if (in_array('updated_at', $fields)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
        }

        // Update booking status to returned
        if ($bookingModel->update($bookingId, $updateData)) {
            // Update car status back to available
            $carModel->update($booking['car_id'], ['status' => 'available']);
            
            return redirect()->to('/staff/bookings')->with('success', 'Car marked as returned successfully. Car is now available.');
        } else {
            return redirect()->to('/staff/bookings')->with('error', 'Failed to mark as returned. Please try again.');
        }
    }
}