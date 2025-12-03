<?php
// views/reservation/success.php
require_once __DIR__ . '/../../config.php';

// Récupérer les détails de la réservation depuis la session 
$bookingDetails = $_SESSION['last_booking'] ?? null;
if ($bookingDetails) {
    unset($_SESSION['last_booking']); 
}
?>


</style>

<div class="success-container">
    <!-- Checkmark SVG -->
    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 52 52" class="mx-auto mb-6" role="img" aria-label="Icône de succès">
        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
    </svg>

    <!-- Titre -->
    <h2 class="text-3xl font-bold mb-4">Demande envoyée avec succès !</h2>

    <!-- Message principal -->
    <p class="mb-4 text-lg">Vous recevrez une confirmation par email une fois que votre demande aura été traitée.</p>

    <!-- Détails de la réservation (si disponibles) -->
    <?php if ($bookingDetails): ?>
        <div class="booking-details text-sm text-gray-600">
            <p><strong>Propriété :</strong> #<?= htmlspecialchars($bookingDetails['property_id']) ?></p>
            <p><strong>Du :</strong> <?= htmlspecialchars($bookingDetails['start_date']) ?> <strong>au</strong> <?= htmlspecialchars($bookingDetails['end_date']) ?></p>
        </div>
    <?php endif; ?>

    <!-- Bouton principal -->
    <a href="<?= BASE_URL ?>index.php?route=my_bookings" class="btn-primary inline-block px-8 py-3 mb-4 bg-blue-700 text-white font-semibold rounded-lg shadow">
        <i class="fa-solid fa-bookmark mr-2" aria-hidden="true"></i> Voir mes réservations
    </a>

    <!-- Message de redirection automatique -->
    <p id="redirectMessage" class="text-sm text-gray-500 mt-4">
        Redirection automatique dans <span id="countdown">3</span> secondes...
        <button id="cancelRedirect" class="text-blue-600 hover:underline ml-2">Annuler</button>
    </p>
</div>

<script>
    let countdown = 3;
    let redirectTimer = setInterval(function() {
        countdown--;
        document.getElementById('countdown').textContent = countdown;
        if (countdown <= 0) {
            clearInterval(redirectTimer);
            window.location.href = '<?= BASE_URL ?>index.php?route=my_bookings';
        }
    }, 1000);

    // Permettre à l'utilisateur d'annuler la redirection
    document.getElementById('cancelRedirect').addEventListener('click', function(e) {
        e.preventDefault();
        clearInterval(redirectTimer);
        document.getElementById('redirectMessage').innerHTML = 'Redirection annulée.';
    });
</script>
