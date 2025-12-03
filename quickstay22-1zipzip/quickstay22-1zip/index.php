<?php
error_log("[index.php] File reached");
ob_start(); // Start output buffering
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php'; // This file establishes $pdo
error_log("[index.php] config.php included.");

// Inclure tous les contrôleurs nécessaires
require_once 'controllers/AuthController.php';
error_log("[index.php] AuthController.php included.");
require_once 'controllers/PropertyController.php';
error_log("[index.php] PropertyController.php included.");
require_once 'controllers/BookingController.php'; // For user bookings list
error_log("[index.php] BookingController.php included.");
require_once 'controllers/UserController.php';
error_log("[index.php] UserController.php included.");
require_once 'controllers/ReservationController.php'; // For booking and cancellation logic
error_log("[index.php] ReservationController.php included.");
include __DIR__ . '/views/partials/nav.php';
error_log("[index.php] nav.php included.");

// Fonction de redirection (si pas déjà définie)
if (!function_exists('redirect')) {
    function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}
error_log("[index.php] Redirect function checked.");

// Générer un jeton CSRF si non défini
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
error_log("[index.php] CSRF token checked/generated.");

// Récupérer la route demandée
$route = filter_input(INPUT_GET, 'route', FILTER_DEFAULT) ?? 'home';
error_log("[index.php] Route determined: " . $route);

// Routes valides
$validRoutes = [
    'home', 'login', 'register', 'logout',
    'admin_dashboard', 'user_dashboard',
    'add_property', 'delete_property', 'properties_list', 'user_properties',
    'book_property', 'my_bookings', 'admin_bookings', 'admin_users',
    'admin_edit_property', 'show_booking_form', 'property_details',
    'cancel_booking', // New route for user cancellation
    'admin_reservation_action', // New route for admin actions
    'booking_success', // New route for booking success
    'user_delete_booking' // New route for user booking deletion
];

// Sécurité : redirige vers home si route inconnue
if (!in_array($route, $validRoutes)) {
    $_SESSION['flash_message'] = "Page non trouvée.";
    $_SESSION['flash_type'] = "error";
    $route = 'home';
    error_log("[index.php] Invalid route, redirecting to home.");
}
error_log("[index.php] Route is valid or set to home. Processing route: " . $route);

?>

<main class="flex flex-col items-center justify-center flex-grow py-8">

<?php

try {
    // Ensure $pdo is available in the scope for controllers that might need it in their constructor
    global $pdo;

    switch ($route) {
        case 'login':
            error_log("[index.php] Handling login route.");
            AuthController::login();
            break;
        case 'register':
             error_log("[index.php] Handling register route.");
            AuthController::register();
            break;
        case 'logout':
             error_log("[index.php] Handling logout route.");
            AuthController::logout();
            break;
        case 'admin_dashboard':
             error_log("[index.php] Handling admin_dashboard route.");
            if (!isAdmin()) {
                redirect('index.php?route=dashboard');
                break; // Stop execution
            }
            require __DIR__ . '/views/admin/admin_dashboard.php';
            break;
        case 'user_dashboard':
             error_log("[index.php] Handling user_dashboard route.");
            if (!isUser()) {
                redirect('index.php?route=login');
                break; // Stop execution
            }
            PropertyController::listForUser();
            break;
        case 'add_property':
             error_log("[index.php] Handling add_property route.");
            if (!isAdmin()) {
                redirect('index.php?route=dashboard');
                break; // Stop execution
            }
            PropertyController::add();
            break;
        case 'delete_property':
             error_log("[index.php] Handling delete_property route.");
            if (!isAdmin()) {
                redirect('index.php?route=dashboard');
                break; // Stop execution
            }
            PropertyController::delete();
            break;
        case 'properties_list':
             error_log("[index.php] Handling properties_list route.");
            if (!isAdmin()) {
                redirect('index.php?route=dashboard');
                break; // Stop execution
            }
            PropertyController::dashboard();
            break;
        case 'admin_edit_property':
             error_log("[index.php] Handling admin_edit_property route.");
            if (!isAdmin()) {
                redirect('index.php?route=dashboard');
                 break; // Stop execution
            }
            PropertyController::edit();
            break;
        case 'user_properties':
             error_log("[index.php] Handling user_properties route.");
            if (!isUser()) {
                redirect('index.php?route=login');
                break; // Stop execution
            }
            PropertyController::listForUser();
            break;
        
        case 'show_booking_form':
            error_log("[index.php] Handling show_booking_form route.");
            if (!isUser()) {
                redirect('index.php?route=login');
                break; // Stop execution
            }
            $property_id = $_GET['property_id'] ?? null;
            if (!$property_id) {
                 $_SESSION['flash_message'] = "Propriété non spécifiée pour la réservation.";
                 $_SESSION['flash_type'] = "error";
                 redirect('index.php?route=home');
                 break;
            }
            $reservationController = new ReservationController($pdo);
            $reservationController->showBookingForm($property_id);
            break;

        case 'book_property':
            error_log("[index.php] Handling book_property route.");
            if (!isUser()) {
                error_log("[index.php] book_property: User not logged in.");
                redirect('index.php?route=login');
                break; // Stop execution
            }
            // Basic CSRF check for POST requests
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
                $_SESSION['flash_message'] = "Requête invalide (CSRF).";
                $_SESSION['flash_type'] = "error";
                error_log("[index.php] book_property: CSRF check failed.");
                redirect('index.php?route=home');
                break; // Stop execution
            }
            error_log("[index.php] book_property: CSRF check passed.");

            $reservationController = new ReservationController($pdo);
            error_log("[index.php] book_property: ReservationController instantiated.");

            $booking_successful = $reservationController->bookProperty(
                $_POST['user_id'] ?? null,
                $_POST['property_id'] ?? null,
                $_POST['start_date'] ?? null,
                $_POST['end_date'] ?? null,
                $_POST['message'] ?? ''
            );

            if ($booking_successful) {
                // Redirect to success page on successful booking
                redirect('index.php?route=booking_success');
            } else {
                // Redirect to my bookings to show the error flash message
                redirect('index.php?route=my_bookings');
            }
            break;

        case 'my_bookings':
             error_log("[index.php] Handling my_bookings route.");
            if (!isUser()) {
                redirect('index.php?route=login');
                break; // Stop execution
            }
            // This route is handled by BookingController
            BookingController::myBookings();
             error_log("[index.php] my_bookings: BookingController::myBookings called.");
            break;

        case 'cancel_booking':
             error_log("[index.php] Handling cancel_booking route.");
            if (!isUser()) {
                 $_SESSION['flash_message'] = "Action non autorisée.";
                 $_SESSION['flash_type'] = "error";
                 redirect('index.php?route=login');
                 break;
            }
            $reservation_id = $_GET['id'] ?? null; // Assuming ID is passed via GET
            if (!$reservation_id) {
                 $_SESSION['flash_message'] = "Réservation non spécifiée.";
                 $_SESSION['flash_type'] = "error";
                 redirect('index.php?route=my_bookings');
                 break;
            }
            $reservationController = new ReservationController($pdo);
            $reservationController->cancelBooking($reservation_id);
            redirect('index.php?route=my_bookings');
            break;

        case 'admin_bookings':
             error_log("[index.php] Handling admin_bookings route.");
            if (!isAdmin()) {
                redirect('index.php?route=dashboard');
                break; // Stop execution
            }
             // This route is handled by BookingController for admin list
            BookingController::allBookings();
             error_log("[index.php] admin_bookings: BookingController::allBookings called.");
            break;

        case 'admin_reservation_action':
             error_log("[index.php] Handling admin_reservation_action route.");
            // This route is handled by the separate action file as per plan
             if (!isAdmin()) {
                 $_SESSION['flash_message'] = "Action non autorisée.";
                 $_SESSION['flash_type'] = "error";
                 redirect('index.php?route=admin_dashboard');
                 break;
             }
             // Include the action file. It will handle its own logic and redirection.
             require __DIR__ . '/admin/reservations/action.php';
              error_log("[index.php] admin_reservation_action: action.php included.");
            break; // Action file handles exit/redirect

        case 'admin_users':
             error_log("[index.php] Handling admin_users route.");
            if (!isAdmin()) {
                redirect('index.php?route=dashboard');
                break; // Stop execution
            }
            UserController::listUsers();
             require __DIR__ . '/views/admin/user_management.php';
             error_log("[index.php] admin_users: UserController::listUsers called.");
            break;

        case 'property_details':
             error_log("[index.php] Handling property_details route.");
            $property_id = $_GET['id'] ?? null;
            if ($property_id) {
                 global $pdo;
                 require_once __DIR__ . '/models/Property.php';
                $property = Property::findById($pdo, $property_id);

                if ($property) {
                    include __DIR__ . '/views/property/show.php';
                     error_log("[index.php] property_details: show.php included.");
                } else {
                    $_SESSION['flash_message'] = "Propriété introuvable.";
                    $_SESSION['flash_type'] = "error";
                    redirect('index.php?route=home');
                }
            } else {
                $_SESSION['flash_message'] = "Aucune propriété spécifiée.";
                $_SESSION['flash_type'] = "error";
                redirect('index.php?route=home');
            }
            break;
        case 'home':
             error_log("[index.php] Handling home route.");
            PropertyController::listForUser();
             error_log("[index.php] home: PropertyController::listForUser called.");
            break;

        case 'booking_success':
             error_log("[index.php] Handling booking_success route.");
             require __DIR__ . '/views/reservation/success.php';
             error_log("[index.php] booking_success: success.php included.");
            break;

        case 'user_delete_booking':
             error_log("[index.php] Handling user_delete_booking route.");
            // This will be handled by ReservationController::deleteUserBooking
            $reservationController = new ReservationController($pdo);
            $reservationController->deleteUserBooking(); // Assuming this method exists
            break;
    }
} catch (Exception $e) {
    error_log("General application error: " . $e->getMessage());
    $_SESSION['flash_message'] = "Une erreur s'est produite : " . htmlspecialchars($e->getMessage());
    $_SESSION['flash_type'] = "error";
    redirect('index.php?route=home');
}

?>

</main>

<?php
// Include the footer (which closes body and html tags)
include __DIR__ . '/views/partials/footer.php';
error_log("[index.php] footer.php included. Script finished.");
?>
