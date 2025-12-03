<?php
// Fichier : views/reservation/book.php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';

// Vérifier que l'utilisateur est connecté
Middleware::userOnly();

// Vérifier que property_id est défini
if (!isset($property_id) || !is_numeric($property_id)) {
    $_SESSION['flash_message'] = "Propriété non spécifiée.";
    $_SESSION['flash_type'] = "error";
    header("Location: index.php?route=home");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une propriété - QuickStay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
    <style>
        .alert-success { background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-lg mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">
                <i class="fa-solid fa-calendar-check mr-2"></i>Réserver cette propriété
            </h2>

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="<?= $_SESSION['flash_type'] === 'success' ? 'bg-green-100 border-green-400' : 'bg-red-100 border-red-400' ?> border text-<?= $_SESSION['flash_type'] === 'success' ? 'green' : 'red' ?>-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($_SESSION['flash_message']) ?></span>
                </div>
                <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
            <?php endif; ?>

            <form action="index.php?route=book_property" method="POST" class="space-y-6" id="bookingForm">
                <input type="hidden" name="property_id" value="<?= htmlspecialchars($property_id) ?>">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user']['id']) ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="redirect_to" value="index.php?route=booking_success">
                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Date d'arrivée</label>
                    <input type="date" name="start_date" id="start_date" required 
                           class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           min="<?= date('Y-m-d') ?>" 
                           aria-describedby="start_date_help">
                    <p id="start_date_help" class="text-xs text-gray-500 mt-1">Choisissez une date d'arrivée postérieure à aujourd'hui.</p>
                </div>
                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Date de départ</label>
                    <input type="date" name="end_date" id="end_date" required 
                           class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                           aria-describedby="end_date_help">
                    <p id="end_date_help" class="text-xs text-gray-500 mt-1">La date de départ doit être postérieure à la date d'arrivée.</p>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Message (optionnel)</label>
                    <textarea name="message" id="message" rows="3" 
                              class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Ajoutez un message pour le propriétaire..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            id="submitButton">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
