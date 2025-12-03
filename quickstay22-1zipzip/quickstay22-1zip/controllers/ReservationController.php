<?php
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/Middleware.php';
require_once __DIR__ . '/../config.php'; // Ensure config is included

class ReservationController {
    private $reservation;
    private $db; // Add a property to store the DB connection

    public function __construct($db) { // Accept DB connection in constructor
        $this->db = $db; // Store the DB connection
        $this->reservation = new Reservation($db);
    }

    public function bookProperty($user_id, $property_id, $start_date, $end_date, $message = '') {
        error_log("[ReservationController] bookProperty called with: user_id=$user_id, property_id=$property_id, start_date=$start_date, end_date=$end_date");
        error_log("[ReservationController] Session user: " . print_r($_SESSION['user'] ?? 'not set', true));
        error_log("[ReservationController] POST data: " . print_r($_POST, true));

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user']['id']) || $_SESSION['user']['id'] != $user_id) {
            $_SESSION['flash_message'] = "Vous devez être connecté pour effectuer une réservation.";
            $_SESSION['flash_type'] = "error";
            error_log("[ReservationController] User not logged in or user ID mismatch. Session user_id: " . ($_SESSION['user']['id'] ?? 'not set') . ", Provided user_id: $user_id");
            return false;
        }

        // Vérifier que tous les champs requis sont présents
        if (!$user_id || !$property_id || !$start_date || !$end_date) {
            $_SESSION['flash_message'] = "Tous les champs sont obligatoires.";
            $_SESSION['flash_type'] = "error";
            error_log("[ReservationController] Missing required fields. user_id: $user_id, property_id: $property_id, start_date: $start_date, end_date: $end_date");
            return false;
        }

        // Valider les dates
        try {
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            
            error_log("[ReservationController] Date validation - Start: " . $start->format('Y-m-d') . ", End: " . $end->format('Y-m-d') . ", Today: " . $today->format('Y-m-d'));
            
            if ($start < $today) {
                $_SESSION['flash_message'] = "La date d'arrivée doit être postérieure à aujourd'hui.";
                $_SESSION['flash_type'] = "error";
                error_log("[ReservationController] Start date is in the past");
                return false;
            }

            if ($end <= $start) {
                $_SESSION['flash_message'] = "La date de départ doit être postérieure à la date d'arrivée.";
                $_SESSION['flash_type'] = "error";
                error_log("[ReservationController] End date is not after start date");
                return false;
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Format de date invalide.";
            $_SESSION['flash_type'] = "error";
            error_log("[ReservationController] Invalid date format: " . $e->getMessage());
            return false;
        }

        // Vérifier la disponibilité
        error_log("[ReservationController] Checking availability for property $property_id from $start_date to $end_date");
        try {
            if (!$this->reservation->checkAvailability($property_id, $start_date, $end_date)) {
                $_SESSION['flash_message'] = "Désolé, la propriété n'est pas disponible pour ces dates.";
                $_SESSION['flash_type'] = "error";
                error_log("[ReservationController] Property is not available for these dates");
                return false;
            }
            error_log("[ReservationController] Property is available");
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Erreur lors de la vérification de la disponibilité : " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
            error_log("[ReservationController] Error checking availability: " . $e->getMessage());
            return false;
        }

        // Créer la réservation
        try {
            error_log("[ReservationController] Attempting to create reservation");
            $result = $this->reservation->create($user_id, $property_id, $start_date, $end_date, $message);
            error_log("[Reservation Model] INSERT query result: " . ($result ? "success" : "failed"));

            // Log the success case
            if ($result) {
                error_log("[ReservationController] bookProperty returning true. Flash message: " . ($_SESSION['flash_message'] ?? 'none'));
            } else {
                 error_log("[ReservationController] bookProperty returning false. Flash message: " . ($_SESSION['flash_message'] ?? 'none'));
            }

            return $result;
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Une erreur est survenue : " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
            error_log("[ReservationController] Exception while creating reservation: " . $e->getMessage());
            return false;
        }
    }

    public function showBookingForm($property_id) {
        if (!isUser()) {
            redirect('index.php?route=login');
            return;
        }

        // Vérifier que la propriété existe
        require_once __DIR__ . '/../models/Property.php';
        $property = Property::findById($this->db, $property_id); // Use the stored DB connection
        
        if (!$property) {
            $_SESSION['flash_message'] = "Propriété introuvable.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=home');
            return;
        }

        include __DIR__ . '/../views/reservation/book.php';
    }

    public function cancelBooking($reservation_id) {
        if (!isUser()) {
            $_SESSION['flash_message'] = "Action non autorisée.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=login');
            return;
        }

        try {
            if ($this->reservation->cancel($reservation_id, $_SESSION['user']['id'])) {
                $_SESSION['flash_message'] = "Réservation annulée avec succès.";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "Impossible d'annuler la réservation.";
                $_SESSION['flash_type'] = "error";
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Une erreur est survenue : " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
    }

    // New method to handle user booking deletion
    public function deleteUserBooking()
    {
        if (!isUser()) {
            $_SESSION['flash_message'] = "Action non autorisée.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=login');
            return;
        }

        $reservation_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user']['id'] ?? null;

        if (!$reservation_id) {
            $_SESSION['flash_message'] = "Réservation non spécifiée.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=my_bookings');
            return;
        }

        // Verify ownership before deletion
        $reservation = Reservation::findById($this->db, $reservation_id);

        if (!$reservation || $reservation['user_id'] !== $user_id) {
            $_SESSION['flash_message'] = "Action non autorisée ou réservation introuvable.";
            $_SESSION['flash_type'] = "error";
            redirect('index.php?route=my_bookings');
            return;
        }

        // Proceed with deletion
        if (Reservation::delete($this->db, $reservation_id)) {
            $_SESSION['flash_message'] = "Réservation supprimée avec succès.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Erreur lors de la suppression de la réservation.";
            $_SESSION['flash_type'] = "error";
        }

        redirect('index.php?route=my_bookings');
    }
}
