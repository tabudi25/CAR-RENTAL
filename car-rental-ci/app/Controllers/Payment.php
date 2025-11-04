<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Libraries\ActivityLogService;
use App\Libraries\NotificationService;
use CodeIgniter\Controller;

class Payment extends Controller
{
    protected $session;
    protected $activityLog;
    protected $notificationService;

    public function __construct()
    {
        $this->session = session();
        $this->activityLog = new ActivityLogService();
        $this->notificationService = new NotificationService();
    }

    /**
     * Show payment page for a booking
     */
    public function index($bookingId)
    {
        // Check if user is logged in
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        
        // Get booking with details
        $booking = $bookingModel->select('bookings.*, cars.name as car_name, cars.plate, cars.price_per_day, users.name as customer_name, users.email as customer_email')
                                ->join('cars', 'cars.id = bookings.car_id')
                                ->join('users', 'users.id = bookings.user_id')
                                ->where('bookings.id', $bookingId)
                                ->where('bookings.user_id', $this->session->get('user_id'))
                                ->first();

        if (!$booking) {
            return redirect()->to('/customer/bookings')->with('error', 'Booking not found');
        }

        // Check if payment is needed
        $paymentStatus = $booking['payment_status'] ?? 'pending';
        if ($paymentStatus === 'paid') {
            return redirect()->to('/customer/bookings')->with('info', 'This booking has already been paid.');
        }

        // Get payment type from URL parameter
        $paymentType = $this->request->getGet('type') ?? 'down_payment';
        
        // Validate payment type
        $downPaymentAmount = $booking['down_payment_amount'] ?? 0;
        $totalAmount = $booking['total_price'] ?? 0;
        
        // If requesting full payment but no down payment was made, redirect
        if ($paymentType === 'full' && $downPaymentAmount == 0) {
            return redirect()->to('/customer/bookings')->with('error', 'Down payment must be made first.');
        }
        
        // If requesting down payment but it's already paid, redirect to full payment
        if ($paymentType === 'down_payment' && $downPaymentAmount > 0 && $paymentStatus !== 'paid') {
            return redirect()->to("/payment/{$bookingId}?type=full")->with('info', 'Down payment already made. Please pay the remaining balance.');
        }

        $data = [
            'booking' => $booking,
            'payment_type' => $paymentType,
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
            ]
        ];

        return view('customer/payment', $data);
    }

    /**
     * Process demo payment
     */
    public function processPayment()
    {
        // Check if user is logged in
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        try {
            // Get POST data
            $bookingId = $this->request->getPost('booking_id');
            $paymentType = $this->request->getPost('payment_type');
            $paymentMethod = $this->request->getPost('payment_method');

            // Validate required fields
            if (empty($bookingId) || empty($paymentType) || empty($paymentMethod)) {
                log_message('error', 'Missing payment data - booking_id: ' . ($bookingId ?? 'null') . ', payment_type: ' . ($paymentType ?? 'null') . ', payment_method: ' . ($paymentMethod ?? 'null'));
                return redirect()->to('/customer/bookings')->with('error', 'Missing required payment information. Please try again.');
            }

            $bookingModel = new BookingModel();
            
            // Get booking - simplified query
            $booking = $bookingModel->find($bookingId);

            if (!$booking) {
                log_message('error', 'Booking not found: ' . $bookingId);
                return redirect()->to('/customer/bookings')->with('error', 'Booking not found');
            }

            // Verify booking belongs to user
            if ($booking['user_id'] != $this->session->get('user_id')) {
                log_message('error', 'Unauthorized payment attempt - booking: ' . $bookingId . ', user: ' . $this->session->get('user_id'));
                return redirect()->to('/customer/bookings')->with('error', 'Unauthorized access.');
            }

            // Get car details for price calculation
            $carModel = new \App\Models\CarModel();
            $car = $carModel->find($booking['car_id']);
            
            if (!$car) {
                log_message('error', 'Car not found for booking: ' . $bookingId);
                return redirect()->to('/customer/bookings')->with('error', 'Car information not found.');
            }

            // Calculate payment amount
            $totalAmount = $booking['total_price'] ?? $booking['total_amount'] ?? 0;
            if ($totalAmount == 0) {
                // Calculate from dates and daily rate
                $start = new \DateTime($booking['start_date']);
                $end = new \DateTime($booking['end_date']);
                $days = $end->diff($start)->days + 1;
                $totalAmount = ($car['price_per_day'] ?? 0) * $days;
            }
            
            $downPaymentAmount = $booking['down_payment_amount'] ?? ($totalAmount * 0.5);
            
            if ($paymentType === 'down_payment') {
                $paymentAmount = $downPaymentAmount;
            } else {
                // Full payment - calculate remaining balance
                $alreadyPaid = $booking['down_payment_amount'] ?? 0; // Check down_payment_amount, not status
                $paymentAmount = $totalAmount - $alreadyPaid;
            }

            // Simulate payment processing delay
            sleep(1);

            // Demo payment - always succeeds
            $paymentReference = $this->generatePaymentReference($paymentMethod);

            // Prepare update data - check which fields exist first
            $db = \Config\Database::connect();
            $fields = $db->getFieldNames('bookings');
            
            $updateData = [];
            
            // Only update fields that exist in database
            if (in_array('payment_method', $fields)) {
                $updateData['payment_method'] = $paymentMethod;
            }
            
            if (in_array('payment_reference', $fields)) {
                $updateData['payment_reference'] = $paymentReference;
            }
            
            if (in_array('payment_status', $fields)) {
                if ($paymentType === 'down_payment') {
                    // Keep status as 'pending' after down payment - only track down_payment_amount
                    // Status will remain 'pending' until full payment is made
                    $updateData['payment_status'] = 'pending';
                } else {
                    // Full payment - change status to 'paid'
                    $updateData['payment_status'] = 'paid';
                }
            }
            
            if ($paymentType === 'down_payment' && in_array('down_payment_amount', $fields)) {
                $updateData['down_payment_amount'] = $paymentAmount;
            } elseif ($paymentType === 'full' && in_array('down_payment_amount', $fields)) {
                // For full payment, update down_payment_amount to total amount
                $updateData['down_payment_amount'] = $totalAmount;
            }

            // Update booking - use skipValidation to avoid validation issues
            $bookingModel->skipValidation(true);
            
            try {
                if ($bookingModel->update($bookingId, $updateData)) {
                    // Log payment activity
                    $car = $carModel->find($booking['car_id']);
                    $carName = $car ? ($car['name'] ?? 'Unknown') : 'Unknown';
                    
                    $this->activityLog->logPayment(
                        $this->session->get('user_id'),
                        $bookingId,
                        $paymentType === 'full' ? 'full payment' : 'down payment',
                        $paymentAmount,
                        $paymentMethod
                    );
                    
                    // Send notification to staff/admin if full payment is received
                    if ($paymentType === 'full') {
                        try {
                            // Get booking with user details for notification
                            $bookingDetails = $bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, cars.name as car_name')
                                                          ->join('users', 'users.id = bookings.user_id')
                                                          ->join('cars', 'cars.id = bookings.car_id')
                                                          ->where('bookings.id', $bookingId)
                                                          ->first();
                            
                            // Send email notification to staff/admin
                            $email = \Config\Services::email();
                            $email->setTo('staff@carrental.com'); // Replace with actual staff email
                            $email->setSubject('Full Payment Received - Booking #' . $bookingId);
                            $email->setMessage(view('emails/full_payment_notification', [
                                'booking' => $bookingDetails,
                                'payment_amount' => $paymentAmount,
                                'payment_reference' => $paymentReference
                            ]));
                            $email->send();
                        } catch (\Exception $notifException) {
                            // Log but don't fail the payment
                            log_message('error', 'Notification failed: ' . $notifException->getMessage());
                        }
                    }
                    
                    // Redirect to success page
                    return redirect()->to("/payment/success/{$bookingId}")
                                    ->with('payment_amount', $paymentAmount)
                                    ->with('payment_type', $paymentType)
                                    ->with('payment_reference', $paymentReference);
                } else {
                    $errors = $bookingModel->errors();
                    log_message('error', 'Payment update failed for booking: ' . $bookingId);
                    log_message('error', 'Update errors: ' . print_r($errors, true));
                    log_message('error', 'Update data: ' . print_r($updateData, true));
                    log_message('error', 'Available fields: ' . implode(', ', $fields));
                    
                    // Even if update fails, redirect to success (for demo purposes)
                    // In production, you'd want to handle this differently
                    return redirect()->to("/payment/success/{$bookingId}")
                                    ->with('payment_amount', $paymentAmount)
                                    ->with('payment_type', $paymentType)
                                    ->with('payment_reference', $paymentReference)
                                    ->with('warning', 'Payment processed but update may have failed. Please contact support.');
                }
            } catch (\Exception $dbException) {
                log_message('error', 'Database update exception: ' . $dbException->getMessage());
                log_message('error', 'Update data attempted: ' . print_r($updateData, true));
                
                // For demo, still redirect to success
                return redirect()->to("/payment/success/{$bookingId}")
                                ->with('payment_amount', $paymentAmount)
                                ->with('payment_type', $paymentType)
                                ->with('payment_reference', $paymentReference)
                                ->with('warning', 'Payment processed but database update encountered an issue.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Payment processing exception: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->to('/customer/bookings')
                            ->with('error', 'An error occurred while processing payment. Please try again or contact support.');
        }
    }

    /**
     * Payment success page
     */
    public function success($bookingId)
    {
        // Check if user is logged in
        if (!$this->session->has('logged_in') || $this->session->get('user_type') !== 'customer') {
            return redirect()->to('/auth/login');
        }

        $bookingModel = new BookingModel();
        
        $booking = $bookingModel->select('bookings.*, cars.name as car_name, cars.plate, cars.price_per_day')
                                ->join('cars', 'cars.id = bookings.car_id')
                                ->where('bookings.id', $bookingId)
                                ->where('bookings.user_id', $this->session->get('user_id'))
                                ->first();

        if (!$booking) {
            return redirect()->to('/customer/bookings')->with('error', 'Booking not found');
        }

        $data = [
            'booking' => $booking,
            'payment_amount' => session()->getFlashdata('payment_amount'),
            'payment_type' => session()->getFlashdata('payment_type'),
            'payment_reference' => session()->getFlashdata('payment_reference'),
            'user' => [
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
            ]
        ];

        return view('customer/payment_success', $data);
    }

    /**
     * Generate a demo payment reference
     */
    private function generatePaymentReference($paymentMethod)
    {
        $prefix = strtoupper($paymentMethod === 'gcash' ? 'GC' : 'BT');
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        return $prefix . $timestamp . $random;
    }
}

