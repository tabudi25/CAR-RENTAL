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
$routes->get('/admin/delete-car/(:num)', 'Admin::deleteCar/$1');
$routes->get('/admin/bookings', 'Admin::bookings');
$routes->post('/admin/update-booking/(:num)', 'Admin::updateBookingStatus/$1');
$routes->get('/admin/users', 'Admin::users');

// Staff routes
$routes->get('/staff', 'Staff::index');
$routes->get('/staff/bookings', 'Staff::manageBookings');
$routes->post('/staff/update-booking/(:num)', 'Staff::updateBookingStatus/$1');
$routes->get('/staff/cars', 'Staff::manageCars');
$routes->post('/staff/update-car/(:num)', 'Staff::updateCarStatus/$1');

// Customer routes
$routes->get('/customer', 'Customer::index');
$routes->get('/customer/book-car/(:num)', 'Customer::bookCar/$1');
$routes->post('/customer/create-booking', 'Customer::createBooking');
$routes->get('/customer/bookings', 'Customer::viewBookings');
$routes->get('/customer/cancel-booking/(:num)', 'Customer::cancelBooking/$1');
