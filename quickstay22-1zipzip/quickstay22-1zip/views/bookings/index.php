<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';
Middleware::userOnly();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - QuickStay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fa-solid fa-calendar-check mr-2"></i>Mes Réservations
        </h1>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mb-4 p-4 rounded text-white 
                <?= $_SESSION['flash_type'] === 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-600">Vous n'avez pas encore de réservations.</p>
                <a href="<?= BASE_URL ?>index.php?route=home" 
                   class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <i class="fa-solid fa-home mr-2"></i>Voir les propriétés
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($bookings as $booking): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?= htmlspecialchars(BASE_URL . $booking['image']) ?>" 
                             alt="<?= htmlspecialchars($booking['title']) ?>"
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">
                                <?= htmlspecialchars($booking['title']) ?>
                            </h2>
                            <div class="text-gray-600 mb-2">
                                <i class="fa-solid fa-calendar mr-2"></i>
                                <?= date('d/m/Y', strtotime($booking['start_date'])) ?> - 
                                <?= date('d/m/Y', strtotime($booking['end_date'])) ?>
                            </div>
                            <div class="text-gray-600 mb-2">
                                <i class="fa-solid fa-euro-sign mr-2"></i>
                                <?= number_format($booking['price'], 2) ?> / nuit
                            </div>
                            <div class="mb-4">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold
                                    <?= $booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                                    <?= $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : '' ?>
                                    <?= $booking['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : '' ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </div>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <a href="<?= BASE_URL ?>index.php?route=cancel_booking&id=<?= $booking['id'] ?>" 
                                   class="block w-full text-center bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
                                   onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                    <i class="fa-solid fa-times-circle mr-2"></i>Annuler
                                </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>index.php?route=user_delete_booking&id=<?= $booking['id'] ?>" 
                               class="block w-full text-center bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mt-2"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette réservation ?')">
                                <i class="fa-solid fa-trash mr-2"></i>Supprimer
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
