<?php

namespace App\Libraries;

use App\Models\NotificationModel;
use App\Models\BookingModel;
use App\Models\UserModel;

class NotificationService
{
    protected $notificationModel;
    protected $bookingModel;
    protected $userModel;
    protected $email;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->bookingModel = new BookingModel();
        $this->userModel = new UserModel();
        $this->email = \Config\Services::email();
    }

    /**
     * Send car ready for pick-up notification
     */
    public function sendReadyForPickupNotification($bookingId, $staffUserId = null)
    {
        $booking = $this->bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, cars.name as car_name')
                                      ->join('users', 'users.id = bookings.user_id')
                                      ->join('cars', 'cars.id = bookings.car_id')
                                      ->find($bookingId);

        if (!$booking) {
            return false;
        }

        $subject = 'Your Car is Ready for Pick-up';
        $message = "Dear {$booking['customer_name']},\n\n";
        $message .= "Great news! Your booked car ({$booking['car_name']}) is now ready for pick-up.\n\n";
        $message .= "Booking Details:\n";
        $message .= "- Booking ID: {$booking['id']}\n";
        $message .= "- Car: {$booking['car_name']}\n";
        $message .= "- Pick-up Date: " . date('M d, Y', strtotime($booking['start_date'])) . "\n";
        if (!empty($booking['pick_up_time'])) {
            $message .= "- Pick-up Time: " . date('g:i A', strtotime($booking['pick_up_time'])) . "\n";
        }
        $message .= "- Pick-up Location: Our main office\n\n";
        $message .= "Please bring a valid ID and the remaining balance payment.\n\n";
        $message .= "Thank you for choosing our car rental service!\n\n";
        $message .= "Best regards,\nCar Rental Team";

        // Save notification to database (if table exists)
        $db = \Config\Database::connect();
        if ($db->tableExists('notifications')) {
            $notificationData = [
                'booking_id' => $bookingId,
                'user_id' => $booking['user_id'],
                'type' => 'ready_for_pickup',
                'subject' => $subject,
                'message' => $message,
                'sent_at' => date('Y-m-d H:i:s'),
            ];

            $this->notificationModel->insert($notificationData);
        }

        // Send email notification
        $this->sendEmail($booking['customer_email'], $subject, $message);

        // Update booking - check if column exists
        if (!isset($db)) {
            $db = \Config\Database::connect();
        }
        $updateData = [];
        if ($db->fieldExists('ready_for_pickup_notification', 'bookings')) {
            $updateData['ready_for_pickup_notification'] = date('Y-m-d H:i:s');
        }
        if (!empty($updateData)) {
            $this->bookingModel->update($bookingId, $updateData);
        }

        return true;
    }

    /**
     * Send due date near notification (1 day before)
     */
    public function sendDueDateNearNotification($bookingId)
    {
        $booking = $this->bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, cars.name as car_name')
                                      ->join('users', 'users.id = bookings.user_id')
                                      ->join('cars', 'cars.id = bookings.car_id')
                                      ->find($bookingId);

        if (!$booking) {
            return false;
        }

        $subject = 'Reminder: Car Rental Due Tomorrow';
        $message = "Dear {$booking['customer_name']},\n\n";
        $message .= "This is a friendly reminder that your car rental is due tomorrow.\n\n";
        $message .= "Booking Details:\n";
        $message .= "- Booking ID: {$booking['id']}\n";
        $message .= "- Car: {$booking['car_name']}\n";
        $message .= "- Return Date: " . date('M d, Y', strtotime($booking['end_date'])) . "\n";
        $message .= "- Total Amount: ₱" . number_format($booking['total_price'] ?? 0, 2) . "\n";
        if (!empty($booking['down_payment_amount'])) {
            $remaining = ($booking['total_price'] ?? 0) - $booking['down_payment_amount'];
            $message .= "- Remaining Balance: ₱" . number_format($remaining, 2) . "\n";
        }
        $message .= "\nPlease ensure you return the car on time to avoid any late fees.\n\n";
        $message .= "Thank you!\n\n";
        $message .= "Best regards,\nCar Rental Team";

        // Save notification to database (if table exists)
        $db = \Config\Database::connect();
        if ($db->tableExists('notifications')) {
            $notificationData = [
                'booking_id' => $bookingId,
                'user_id' => $booking['user_id'],
                'type' => 'due_date_near',
                'subject' => $subject,
                'message' => $message,
                'sent_at' => date('Y-m-d H:i:s'),
            ];

            $this->notificationModel->insert($notificationData);
        }

        // Send email notification
        $this->sendEmail($booking['customer_email'], $subject, $message);

        // Update booking - check if column exists
        if (!isset($db)) {
            $db = \Config\Database::connect();
        }
        $updateData = [];
        if ($db->fieldExists('due_date_notification_sent', 'bookings')) {
            $updateData['due_date_notification_sent'] = date('Y-m-d H:i:s');
        }
        if (!empty($updateData)) {
            $this->bookingModel->update($bookingId, $updateData);
        }

        return true;
    }

    /**
     * Send overdue notification and calculate charges
     */
    public function sendOverdueNotification($bookingId)
    {
        $booking = $this->bookingModel->select('bookings.*, users.name as customer_name, users.email as customer_email, cars.name as car_name, cars.price_per_day')
                                      ->join('users', 'users.id = bookings.user_id')
                                      ->join('cars', 'cars.id = bookings.car_id')
                                      ->find($bookingId);

        if (!$booking) {
            return false;
        }

        // Calculate overdue days and charges
        $endDate = new \DateTime($booking['end_date']);
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $endDate->setTime(0, 0, 0);
        
        if ($endDate < $today) {
            // Calculate overdue days
            $overdueDays = $today->diff($endDate)->days;
            // Calculate overdue charge (daily rate per day overdue)
            $overdueCharge = ($booking['price_per_day'] ?? 0) * $overdueDays;
        } else {
            $overdueDays = 0;
            $overdueCharge = 0;
        }

        $subject = 'Urgent: Car Rental Overdue';
        $message = "Dear {$booking['customer_name']},\n\n";
        $message .= "URGENT: Your car rental is overdue. Please return the car immediately.\n\n";
        $message .= "Booking Details:\n";
        $message .= "- Booking ID: {$booking['id']}\n";
        $message .= "- Car: {$booking['car_name']}\n";
        $message .= "- Original Return Date: " . date('M d, Y', strtotime($booking['end_date'])) . "\n";
        $message .= "- Days Overdue: {$overdueDays}\n";
        $message .= "- Overdue Charge: ₱" . number_format($overdueCharge, 2) . "\n";
        $message .= "- Total Amount Due: ₱" . number_format(($booking['total_price'] ?? 0) + $overdueCharge, 2) . "\n\n";
        $message .= "Please contact us immediately to arrange for return and payment.\n\n";
        $message .= "Thank you for your immediate attention.\n\n";
        $message .= "Best regards,\nCar Rental Team";

        // Save notification to database (if table exists)
        $db = \Config\Database::connect();
        if ($db->tableExists('notifications')) {
            $notificationData = [
                'booking_id' => $bookingId,
                'user_id' => $booking['user_id'],
                'type' => 'overdue',
                'subject' => $subject,
                'message' => $message,
                'sent_at' => date('Y-m-d H:i:s'),
            ];

            $this->notificationModel->insert($notificationData);
        }

        // Send email notification
        $this->sendEmail($booking['customer_email'], $subject, $message);

        // Update booking with overdue charges - check if columns exist
        if (!isset($db)) {
            $db = \Config\Database::connect();
        }
        $updateData = [];
        
        if ($db->fieldExists('overdue_days', 'bookings')) {
            $updateData['overdue_days'] = $overdueDays;
        }
        if ($db->fieldExists('overdue_charge', 'bookings')) {
            $updateData['overdue_charge'] = $overdueCharge;
        }
        if ($db->fieldExists('overdue_notification_sent', 'bookings')) {
            $updateData['overdue_notification_sent'] = date('Y-m-d H:i:s');
        }
        
        if (!empty($updateData)) {
            $this->bookingModel->update($bookingId, $updateData);
        }

        return true;
    }

    /**
     * Process automatic notifications (due date near and overdue)
     */
    public function processAutomaticNotifications()
    {
        // Send due date near notifications
        $dueSoonBookings = $this->bookingModel->getBookingsDueSoon();
        foreach ($dueSoonBookings as $booking) {
            $this->sendDueDateNearNotification($booking['id']);
        }

        // Send overdue notifications
        $overdueBookings = $this->bookingModel->getOverdueBookings();
        foreach ($overdueBookings as $booking) {
            // Only send if not already sent today (check if column exists)
            $db = \Config\Database::connect();
            $shouldSend = true;
            if ($db->fieldExists('overdue_notification_sent', 'bookings')) {
                $shouldSend = empty($booking['overdue_notification_sent']) || 
                             (isset($booking['overdue_notification_sent']) && 
                              date('Y-m-d', strtotime($booking['overdue_notification_sent'])) < date('Y-m-d'));
            }
            
            if ($shouldSend) {
                $this->sendOverdueNotification($booking['id']);
            }
        }

        return true;
    }

    /**
     * Send email notification
     */
    protected function sendEmail($to, $subject, $message)
    {
        try {
            $this->email->setFrom('noreply@carrental.com', 'Car Rental System');
            $this->email->setTo($to);
            $this->email->setSubject($subject);
            $this->email->setMessage($message);
            
            return $this->email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send email: ' . $e->getMessage());
            return false;
        }
    }
}

