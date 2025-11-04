<?php

namespace App\Controllers;

use App\Models\CarModel;
use App\Models\BookingModel;
use App\Libraries\ActivityLogService;
use CodeIgniter\Controller;

class Customer extends Controller
{
    protected $session;
    protected $activityLog;

    public function __construct()
    {
        $this->session = session();
        $this->activityLog = new ActivityLogService();
    }

    public function index()
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        
        // Get all cars (not just available ones)
        $allCars = $carModel->orderBy('created_at', 'DESC')->findAll();
        
        // Get active bookings (pending or confirmed) to determine which cars are unavailable
        $activeBookings = $bookingModel->whereIn('status', ['pending', 'confirmed'])
                                       ->where('end_date >=', date('Y-m-d'))
                                       ->findAll();
        
        // Create a list of car IDs that are currently booked
        $bookedCarIds = [];
        foreach ($activeBookings as $booking) {
            $bookedCarIds[] = $booking['car_id'];
        }
        
        // Mark cars as available/unavailable
        foreach ($allCars as &$car) {
            $car['is_available'] = !in_array($car['id'], $bookedCarIds) && ($car['status'] === 'available');
        }
        
        $data = [
            'cars' => $allCars,
            'bookings' => $bookingModel->getUserBookings($this->session->get('user_id')),
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];

        // Log dashboard view
        $this->activityLog->logCustomerDashboardView($this->session->get('user_id'));

        return view('customer/dashboard', $data);
    }

    public function bookCar($carId)
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $carModel = new CarModel();
        $car = $carModel->find($carId);
        
        if (!$car) {
            return redirect()->to('/customer')->with('error', 'Car not found');
        }
        
        $data = [
            'car' => $car,
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'user_type' => $this->session->get('user_type')
            ]
        ];
        
        // Log car view
        $this->activityLog->logCarView($this->session->get('user_id'), $carId, $car['name'] ?? 'Unknown');
        
        return view('customer/book_car', $data);
    }

    public function createBooking()
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }
        
        // Debug: Check if we have a user_id in session
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/customer')->with('error', 'User not found in session');
        }

        $carModel = new CarModel();
        $bookingModel = new BookingModel();
        
        $carId = $this->request->getPost('car_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $pickUpTime = $this->request->getPost('pick_up_time');
        $paymentMethod = $this->request->getPost('payment_method');
        $downPaymentPaid = $this->request->getPost('down_payment_paid');
        $downPaymentAmountValue = $this->request->getPost('down_payment_amount_value');
        $paymentReferenceNumber = $this->request->getPost('payment_reference_number');
        $customerPaymentAccount = $this->request->getPost('customer_payment_account'); // Customer's GCash or bank account number
        
        // Debug: Check if POST data is received
        if (!$carId || !$startDate || !$endDate || !$pickUpTime || !$paymentMethod) {
            return redirect()->to('/customer')->with('error', 'Missing required booking data. Please fill all required fields.');
        }
        
        // Check if down payment was paid
        if (!$downPaymentPaid || $downPaymentPaid !== '1') {
            return redirect()->to("/customer/book-car/{$carId}")->with('error', 'Please complete the down payment before booking.');
        }
        
        $car = $carModel->find($carId);
        
        if (!$car) {
            return redirect()->to('/customer')->with('error', 'Car not found');
        }
        
        // Calculate total price
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $days = $end->diff($start)->days + 1;
        $totalAmount = $car['price_per_day'] * $days;
        // Use the down payment amount from the form (already paid) or calculate 50%
        $downPaymentAmount = $downPaymentAmountValue ? (float)$downPaymentAmountValue : ($totalAmount * 0.5);
        
        // Prepare data - check which fields exist in database
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('bookings');
        
        $data = [
            'user_id' => $this->session->get('user_id'),
            'car_id' => $carId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
        ];
        
        // Add fields only if they exist in database
        if (in_array('pick_up_time', $fields)) {
            $data['pick_up_time'] = $pickUpTime;
        }
        
        if (in_array('total_price', $fields)) {
            $data['total_price'] = $totalAmount;
        }
        
        if (in_array('total_amount', $fields)) {
            $data['total_amount'] = $totalAmount;
        }
        
        if (in_array('down_payment_amount', $fields)) {
            $data['down_payment_amount'] = $downPaymentAmount;
        } else {
            // Log if field doesn't exist
            log_message('warning', 'down_payment_amount field does not exist in bookings table. Down payment amount: ' . $downPaymentAmount);
        }
        
        if (in_array('payment_method', $fields)) {
            $data['payment_method'] = $paymentMethod;
        }
        
        if (in_array('payment_reference', $fields) && $paymentReferenceNumber) {
            $data['payment_reference'] = $paymentReferenceNumber;
        }
        
        if (in_array('payment_status', $fields)) {
            // Check if 'partial' is a valid enum value, otherwise use 'pending'
            // Try to get enum values from database
            try {
                $enumQuery = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'payment_status'");
                $enumResult = $enumQuery->getRow();
                if ($enumResult && isset($enumResult->Type)) {
                    preg_match("/enum\((.*)\)/", $enumResult->Type, $matches);
                    if (isset($matches[1])) {
                        $enumValues = str_replace("'", "", $matches[1]);
                        $enumArray = explode(',', $enumValues);
                        $enumArray = array_map('trim', $enumArray);
                        if (in_array('partial', $enumArray)) {
                            $data['payment_status'] = 'partial';
                        } else {
                            $data['payment_status'] = 'pending'; // Fallback to pending
                        }
                    } else {
                        $data['payment_status'] = 'pending';
                    }
                } else {
                    $data['payment_status'] = 'pending';
                }
            } catch (\Exception $e) {
                // If we can't check, default to pending
                $data['payment_status'] = 'pending';
                log_message('debug', 'Could not check payment_status enum: ' . $e->getMessage());
            }
        }
        
        if (in_array('created_at', $fields)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        if (in_array('updated_at', $fields)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        // Ensure down_payment_amount column exists before insert
        try {
            if (!in_array('down_payment_amount', $fields)) {
                // Try to add the column if it doesn't exist
                try {
                    $db->query("ALTER TABLE bookings ADD COLUMN down_payment_amount DECIMAL(10,2) NULL AFTER total_price");
                    $fields = $db->getFieldNames('bookings'); // Refresh fields list
                    if (in_array('down_payment_amount', $fields)) {
                        $data['down_payment_amount'] = $downPaymentAmount;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Failed to add down_payment_amount column: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error checking/adding down_payment_amount column: ' . $e->getMessage());
        }
        
        try {
            // Temporarily disable validation to see actual database errors
            $bookingModel->skipValidation(false);
            
            // Log the data being inserted for debugging
            log_message('debug', 'Booking data being inserted: ' . print_r($data, true));
            
            if ($bookingModel->insert($data)) {
                $bookingId = $bookingModel->getInsertID();
                
                // Update car status
                $carModel->update($carId, ['status' => 'reserved']);
                
                // Log booking creation
                $this->activityLog->logBookingCreated(
                    $userId,
                    $bookingId,
                    $carId,
                    $car['name'] ?? 'Unknown',
                    $startDate,
                    $endDate,
                    $totalAmount
                );
                
                return redirect()->to('/customer')->with('success', 'Booking created successfully. Please note: 50% down payment is required.');
            } else {
                // Get validation errors
                $errors = $bookingModel->errors();
                $errorMessage = 'Failed to create booking';
                if (!empty($errors)) {
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                
                // Log detailed error
                log_message('error', 'Booking creation failed. Errors: ' . print_r($errors, true));
                log_message('error', 'Data attempted: ' . print_r($data, true));
                
                return redirect()->to('/customer')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the error with full details
            log_message('error', 'Create booking error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            log_message('error', 'Data attempted: ' . print_r($data, true));
            
            return redirect()->to('/customer')->with('error', 'An error occurred while creating the booking: ' . $e->getMessage() . '. Please check the database migrations have been run.');
        }
    }

    public function viewBookings()
    {
        // Check if user is logged in and is customer
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        try {
            $bookingModel = new BookingModel();
            
            $bookings = $bookingModel->getUserBookings($this->session->get('user_id'));
            
            // Ensure all bookings have the required fields
            foreach ($bookings as &$booking) {
                if (!isset($booking['car_name'])) {
                    $booking['car_name'] = 'Unknown Car';
                }
            }
            
            $data = [
                'bookings' => $bookings,
                'user' => [
                    'id' => $this->session->get('user_id'),
                    'name' => $this->session->get('name'),
                    'email' => $this->session->get('email'),
                    'user_type' => $this->session->get('user_type')
                ]
            ];
            
            // Log bookings view
            $this->activityLog->logBookingsView($this->session->get('user_id'));
            
            return view('customer/bookings', $data);
        } catch (\Exception $e) {
            log_message('error', 'View bookings error: ' . $e->getMessage());
            return redirect()->to('/customer')->with('error', 'Unable to load bookings. Please try again.');
        }
    }

    public function cancelBooking($id)
    {
        // Debug: Log the request
        log_message('debug', 'Cancel booking method called with ID: ' . $id);
        
        try {
            // Check if user is logged in and is customer
            if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
                log_message('debug', 'User not logged in or not customer');
                return redirect()->to('/auth/login')->with('error', 'Please login to continue');
            }

            $bookingModel = new BookingModel();
            $carModel = new CarModel();
            
            $booking = $bookingModel->find($id);
            
            // Check if booking exists
            if (!$booking) {
                return redirect()->to('/customer/bookings')->with('error', 'Booking not found');
            }
            
            // Check if user owns this booking
            if ($booking['user_id'] != $this->session->get('user_id')) {
                return redirect()->to('/customer/bookings')->with('error', 'You are not authorized to cancel this booking');
            }
            
            // Check if booking can be cancelled (only pending or confirmed bookings)
            if (!in_array($booking['status'], ['pending', 'confirmed'])) {
                return redirect()->to('/customer/bookings')->with('error', 'This booking cannot be cancelled. Only pending or confirmed bookings can be cancelled.');
            }
            
            // Start database transaction
            $this->db = \Config\Database::connect();
            $this->db->transStart();
            
            // Update booking status to cancelled
            $updateData = ['status' => 'cancelled'];
            
            // Only add updated_at if the column exists
            if ($this->db->fieldExists('updated_at', 'bookings')) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            $updateResult = $bookingModel->update($id, $updateData);
            
            if (!$updateResult) {
                $this->db->transRollback();
                return redirect()->to('/customer/bookings')->with('error', 'Failed to update booking status');
            }
            
            // Update car status back to available
            $carUpdateData = ['status' => 'available'];
            
            // Only add updated_at if the column exists
            if ($this->db->fieldExists('updated_at', 'cars')) {
                $carUpdateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            $carUpdateResult = $carModel->update($booking['car_id'], $carUpdateData);
            
            if (!$carUpdateResult) {
                $this->db->transRollback();
                return redirect()->to('/customer/bookings')->with('error', 'Failed to update car status');
            }
            
            // Get car details for logging
            $car = $carModel->find($booking['car_id']);
            $carName = $car ? ($car['name'] ?? 'Unknown') : 'Unknown';
            
            // Commit transaction
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return redirect()->to('/customer/bookings')->with('error', 'Transaction failed. Please try again.');
            }
            
            // Log booking cancellation
            $this->activityLog->logBookingCancelled(
                $this->session->get('user_id'),
                $id,
                $carName
            );
            
            return redirect()->to('/customer/bookings')->with('success', 'Booking cancelled successfully');
            
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Cancel booking error: ' . $e->getMessage());
            return redirect()->to('/customer/bookings')->with('error', 'An error occurred while cancelling the booking. Please try again.');
        }
    }
    
    public function testCancel($id)
    {
        return "Test cancel booking for ID: " . $id;
    }

    public function requestReturn($id)
    {
        try {
            // Check if user is logged in and is customer
            if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
                return redirect()->to('/auth/login')->with('error', 'Please login to continue');
            }

            $bookingModel = new BookingModel();
            $booking = $bookingModel->find($id);

            // Check if booking exists
            if (!$booking) {
                return redirect()->to('/customer/bookings')->with('error', 'Booking not found');
            }

            // Check if user owns this booking
            if ($booking['user_id'] != $this->session->get('user_id')) {
                return redirect()->to('/customer/bookings')->with('error', 'You are not authorized to request return for this booking');
            }

            // Check if payment is fully paid
            $paymentStatus = $booking['payment_status'] ?? 'pending';
            if ($paymentStatus !== 'paid') {
                return redirect()->to('/customer/bookings')->with('error', 'You can only return the car after full payment is completed.');
            }

            // Check if booking can have return requested (only confirmed bookings)
            if ($booking['status'] !== 'confirmed') {
                return redirect()->to('/customer/bookings')->with('error', 'You can only request return for confirmed bookings.');
            }

            // Check if return already requested
            if (in_array($booking['status'], ['return_requested', 'returned'])) {
                return redirect()->to('/customer/bookings')->with('error', 'Return has already been requested or processed.');
            }

            // Check if return already requested (via return_requested_at field)
            $db = \Config\Database::connect();
            if ($db->fieldExists('return_requested_at', 'bookings')) {
                if (!empty($booking['return_requested_at'])) {
                    return redirect()->to('/customer/bookings')->with('error', 'Return request has already been submitted. Please wait for staff approval.');
                }
            }

            // Update booking with return request - set status to 'return_requested'
            $db = \Config\Database::connect();
            $fields = $db->getFieldNames('bookings');
            $updateData = [];

            // First, check if 'return_requested' status exists in ENUM
            try {
                $enumResult = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'")->getRow();
                if ($enumResult) {
                    preg_match("/^enum\((.*)\)$/", $enumResult->Type, $matches);
                    if (!empty($matches[1])) {
                        $enumValues = str_replace("'", "", explode(",", $matches[1]));
                        if (!in_array('return_requested', $enumValues)) {
                            // Add 'return_requested' and 'returned' to ENUM
                            $enumValues[] = 'return_requested';
                            $enumValues[] = 'returned';
                            $enumString = "'" . implode("','", $enumValues) . "'";
                            $db->query("ALTER TABLE bookings MODIFY COLUMN status ENUM({$enumString}) DEFAULT 'pending'");
                        }
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to update status ENUM: ' . $e->getMessage());
            }

            // Set status to 'return_requested'
            $updateData['status'] = 'return_requested';

            // Also set return_requested_at timestamp if column exists
            if (in_array('return_requested_at', $fields)) {
                $updateData['return_requested_at'] = date('Y-m-d H:i:s');
            } else {
                // Try to add the column if it doesn't exist
                try {
                    $db->query("ALTER TABLE bookings ADD COLUMN return_requested_at DATETIME NULL AFTER status");
                    $updateData['return_requested_at'] = date('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    log_message('debug', 'Failed to add return_requested_at column: ' . $e->getMessage());
                }
            }

            if (in_array('updated_at', $fields)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }

            if ($bookingModel->update($id, $updateData)) {
                // Log the return request
                $car = (new CarModel())->find($booking['car_id']);
                $carName = $car ? ($car['name'] ?? 'Unknown') : 'Unknown';
                
                $this->activityLog->log($this->session->get('user_id'), 'customer', 'return_requested', 
                    "Customer requested to return car: {$carName}", 
                    $this->request->getIPAddress(), 
                    $this->request->getUserAgent()->getAgentString(),
                    $id, 'booking', ['car_id' => $booking['car_id']]);

                return redirect()->to('/customer/bookings')->with('success', 'Return request submitted successfully. Staff will process your request shortly.');
            } else {
                return redirect()->to('/customer/bookings')->with('error', 'Failed to submit return request. Please try again.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Request return error: ' . $e->getMessage());
            return redirect()->to('/customer/bookings')->with('error', 'An error occurred while submitting the return request. Please try again.');
        }
    }
}