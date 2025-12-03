<?php
class Reservation {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($user_id, $property_id, $start_date, $end_date, $message = '') {
        try {
            error_log("[Reservation Model] Attempting to create reservation with: user_id=$user_id, property_id=$property_id, start_date=$start_date, end_date=$end_date");
            
            // Vérifier que l'utilisateur existe
            $query = "SELECT COUNT(*) FROM users WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->fetchColumn() == 0) {
                error_log("[Reservation Model] User with ID $user_id does not exist");
                throw new Exception("Utilisateur introuvable.");
            }

            // Vérifier que la propriété existe
            $query = "SELECT COUNT(*) FROM properties WHERE id = :property_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->fetchColumn() == 0) {
                error_log("[Reservation Model] Property with ID $property_id does not exist");
                throw new Exception("Propriété introuvable.");
            }

            $query = "INSERT INTO reservations (user_id, property_id, start_date, end_date, message, status, created_at) 
                     VALUES (:user_id, :property_id, :start_date, :end_date, :message, 'pending', NOW())";
            
            error_log("[Reservation Model] Executing INSERT query");
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':message', $message);
            
            $result = $stmt->execute();
            error_log("[Reservation Model] INSERT query result: " . ($result ? "success" : "failed"));
            
            return $result;
        } catch (PDOException $e) {
            error_log("[Reservation Model] PDO Error creating reservation: " . $e->getMessage());
            throw new Exception("Erreur lors de la création de la réservation: " . $e->getMessage());
        }
    }

    public function checkAvailability($property_id, $start_date, $end_date) {
        try {
            error_log("[Reservation Model] Checking availability for property $property_id from $start_date to $end_date");
            
            $query = "SELECT COUNT(*) as count FROM reservations 
                     WHERE property_id = :property_id 
                     AND status IN ('confirmed', 'pending')
                     AND (
                         (start_date <= :end_date1 AND end_date >= :start_date1)
                         OR (start_date BETWEEN :start_date2 AND :end_date2)
                         OR (end_date BETWEEN :start_date3 AND :end_date3)
                     )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);
            $stmt->bindParam(':start_date1', $start_date);
            $stmt->bindParam(':end_date1', $end_date);
            $stmt->bindParam(':start_date2', $start_date);
            $stmt->bindParam(':end_date2', $end_date);
            $stmt->bindParam(':start_date3', $start_date);
            $stmt->bindParam(':end_date3', $end_date);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $isAvailable = $result['count'] == 0;
            
            error_log("[Reservation Model] Availability check result: " . ($isAvailable ? "available" : "not available") . " (count: " . $result['count'] . ")");
            
            return $isAvailable;
        } catch (PDOException $e) {
            error_log("[Reservation Model] PDO Error checking availability: " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification de la disponibilité: " . $e->getMessage());
        }
    }

    public function getUserBookings($user_id) {
        try {
            $query = "SELECT r.*, p.title, p.image, p.price 
                     FROM reservations r 
                     JOIN properties p ON r.property_id = p.id 
                     WHERE r.user_id = :user_id 
                     ORDER BY r.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user bookings: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des réservations.");
        }
    }

    public function getAllBookings() {
        try {
            $query = "SELECT r.*, u.email, p.title 
                     FROM reservations r 
                     JOIN users u ON r.user_id = u.id 
                     JOIN properties p ON r.property_id = p.id 
                     ORDER BY r.created_at DESC";
            
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all bookings: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des réservations: " . $e->getMessage());
        }
    }

    public function getPendingBookingsCount() {
        try {
            $query = "SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'";
            $stmt = $this->conn->query($query);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'];
        } catch (PDOException $e) {
            error_log("Error getting pending bookings count: " . $e->getMessage());
            throw new Exception("Erreur lors du comptage des réservations en attente.");
        }
    }

    public function cancel($reservation_id, $user_id) {
        try {
            // Vérifier que la réservation appartient à l'utilisateur
            $query = "SELECT id FROM reservations WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $reservation_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return false;
            }

            // Annuler la réservation
            $query = "UPDATE reservations SET status = 'cancelled' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $reservation_id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error cancelling booking: " . $e->getMessage());
            throw new Exception("Erreur lors de l'annulation de la réservation.");
        }
    }

    public function updateStatus($reservation_id, $status) {
        try {
            $query = "UPDATE reservations SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $reservation_id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating reservation status: " . $e->getMessage());
            throw new Exception("Erreur lors de la mise à jour du statut de la réservation.");
        }
    }

    public static function delete($pdo, $reservationId)
    {
        $sql = "DELETE FROM reservations WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $reservationId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // New method to find a reservation by its ID
    public static function findById($pdo, $reservationId)
    {
        $sql = "SELECT * FROM reservations WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $reservationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
