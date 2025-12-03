<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';
require_once __DIR__ . '/../../models/Reservation.php';

session_start();
Middleware::adminOnly();

// Vérification du token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
    $_SESSION['flash_message'] = "Requête invalide (CSRF).";
    $_SESSION['flash_type'] = "error";
    redirect('index.php?route=admin_bookings');
}

// Vérification des paramètres
if (!isset($_POST['reservation_id']) || !isset($_POST['action'])) {
    $_SESSION['flash_message'] = "Paramètres manquants.";
    $_SESSION['flash_type'] = "error";
    redirect('index.php?route=admin_bookings');
}

$reservation_id = (int)$_POST['reservation_id'];
$action = $_POST['action'];

if (!in_array($action, ['confirm', 'cancel'])) {
    $_SESSION['flash_message'] = "Action invalide.";
    $_SESSION['flash_type'] = "error";
    redirect('index.php?route=admin_bookings');
}

try {
    $reservation = new Reservation($pdo);
    $status = ($action === 'confirm') ? 'confirmed' : 'cancelled';
    
    if ($reservation->updateStatus($reservation_id, $status)) {
        $_SESSION['flash_message'] = "Réservation " . ($action === 'confirm' ? 'confirmée' : 'annulée') . " avec succès.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Erreur lors de la mise à jour de la réservation.";
        $_SESSION['flash_type'] = "error";
    }
} catch (Exception $e) {
    error_log("Error in admin_reservation_action: " . $e->getMessage());
    $_SESSION['flash_message'] = "Une erreur est survenue : " . $e->getMessage();
    $_SESSION['flash_type'] = "error";
}

redirect('index.php?route=admin_bookings');
