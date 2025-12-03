<?php
// Remove the unused BookingModel use statement
// use QuickStay\Models\BookingModel;
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/Middleware.php';
require_once __DIR__ . '/../config.php';

class BookingController
{
    private static function getUserId() {
        $user = $_SESSION['user'];
        return is_object($user) ? $user->id : $user['id'];
    }

    

    public static function myBookings()
    {
        if (!isUser()) redirect('index.php?route=login');
        $user_id = self::getUserId();
        
        global $pdo; // Get the PDO connection
        $reservation = new Reservation($pdo);
        $bookings = $reservation->getUserBookings($user_id);
        
        // The view expects a variable named $bookings
        include __DIR__ . '/../views/bookings/index.php';
    }

    public static function allBookings()
    {
        Middleware::adminOnly();
        
        global $pdo; // Get the PDO connection
        $reservation = new Reservation($pdo);
        $bookings = $reservation->getAllBookings();
        $pendingBookings = $reservation->getPendingBookingsCount(); // This might not be used in the view directly, but good to have
        
        // The view expects a variable named $bookings
        include __DIR__ . '/../views/admin/bookings_list.php'; // Assuming bookings_list.php is used for admin list
    }

  
}
?>
