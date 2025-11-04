<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth routes
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/authenticate', 'Auth::authenticate');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/create-account', 'Auth::createAccount');
$routes->get('/auth/logout', 'Auth::logout');

// Admin routes
$routes->get('/admin', 'Admin::index');
$routes->get('/admin/cars', 'Admin::cars');
$routes->get('/admin/add-car', 'Admin::addCar');
$routes->post('/admin/add-car', 'Admin::addCar');
$routes->get('/admin/edit-car/(:num)', 'Admin::editCar/$1');
$routes->post('/admin/edit-car/(:num)', 'Admin::editCar/$1');
$routes->get('/admin/change-status/(:num)/(:any)', 'Admin::changeStatus/$1/$2');
$routes->get('/admin/delete-car/(:num)', 'Admin::deleteCar/$1');
$routes->get('/admin/bookings', 'Admin::bookings');
$routes->post('/admin/update-booking/(:num)', 'Admin::updateBookingStatus/$1');
$routes->get('/admin/delete-booking/(:num)', 'Admin::deleteBooking/$1');
$routes->get('/admin/users', 'Admin::users');
$routes->get('/admin/activity-logs', 'Admin::activityLogs');
$routes->get('/admin/reports', 'Admin::reports');
$routes->get('/admin/process-return/(:num)', 'Admin::processReturn/$1');
$routes->get('/admin/mark-returned/(:num)', 'Admin::markReturned/$1');

// Staff routes
$routes->get('/staff', 'Staff::index');
$routes->get('/staff/bookings', 'Staff::manageBookings');
$routes->post('/staff/update-booking/(:num)', 'Staff::updateBookingStatus/$1');
$routes->get('/staff/send-ready-notification/(:num)', 'Staff::sendReadyForPickupNotification/$1');
$routes->get('/staff/process-notifications', 'Staff::processNotifications');
$routes->get('/staff/cars', 'Staff::manageCars');
$routes->post('/staff/update-car/(:num)', 'Staff::updateCarStatus/$1');
$routes->get('/staff/customers', 'Staff::manageCustomers');
$routes->get('/staff/booking', 'Staff::bookingDetails');
$routes->get('/staff/maintenance', 'Staff::maintenance');
$routes->get('/staff/activity-logs', 'Staff::activityLogs');
$routes->get('/staff/checkout', 'Staff::checkout');
$routes->post('/staff/checkout', 'Staff::checkout');
$routes->post('/staff/process-checkout', 'Staff::processCheckout');
$routes->get('/staff/process-return/(:num)', 'Staff::processReturn/$1');
$routes->get('/staff/mark-returned/(:num)', 'Staff::markReturned/$1');

// Customer routes
$routes->get('/customer', 'Customer::index');
$routes->get('/customer/book-car/(:num)', 'Customer::bookCar/$1');
$routes->post('/customer/create-booking', 'Customer::createBooking');
$routes->get('/customer/bookings', 'Customer::viewBookings');
$routes->get('/customer/cancel-booking/(:num)', 'Customer::cancelBooking/$1');
$routes->post('/customer/cancel-booking/(:num)', 'Customer::cancelBooking/$1');
$routes->get('/customer/request-return/(:num)', 'Customer::requestReturn/$1');

// Payment routes
$routes->get('/payment/(:num)', 'Payment::index/$1');
$routes->post('/payment/process', 'Payment::processPayment');
$routes->get('/payment/success/(:num)', 'Payment::success/$1');
$routes->get('/customer/test-cancel/(:num)', 'Customer::testCancel/$1');
$routes->get('/cancel/(:num)', 'Customer::cancelBooking/$1');
$routes->get('/test-route', function() { return 'Route test successful!'; });
$routes->get('/test-cancel/(:num)', 'Customer::testCancel/$1');
$routes->get('/test-delete-booking/(:num)', function($id) { return "Test delete booking for ID: " . $id; });
$routes->get('/test-booking-creation', function() { 
    $db = \Config\Database::connect();
    $fields = $db->getFieldNames('bookings');
    return "Available booking fields: " . implode(', ', $fields);
});
