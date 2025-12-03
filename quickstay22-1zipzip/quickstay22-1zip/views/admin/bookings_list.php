<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';
Middleware::adminOnly();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réservations - Admin - QuickStay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
    <script src="https://kit.fontawesome.com/a2e8d6c6c3.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fa-solid fa-calendar-check mr-2"></i>Gestion des Réservations
        </h1>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mb-4 p-4 rounded text-white 
                <?= $_SESSION['flash_type'] === 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propriété</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Aucune réservation trouvée.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($booking['email']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($booking['title']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d/m/Y', strtotime($booking['start_date'])) ?> - 
                                        <?= date('d/m/Y', strtotime($booking['end_date'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?= nl2br(htmlspecialchars($booking['message'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        <?= $booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                                        <?= $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : '' ?>
                                        <?= $booking['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : '' ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <form action="<?= BASE_URL ?>index.php?route=admin_reservation_action" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="reservation_id" value="<?= $booking['id'] ?>">
                                            <button type="submit" name="action" value="confirm" 
                                                    class="text-green-600 hover:text-green-900 mr-3">
                                                <i class="fa-solid fa-check-circle"></i> Confirmer
                                            </button>
                                            <button type="submit" name="action" value="cancel" 
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fa-solid fa-times-circle"></i> Annuler
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400">Action non disponible</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
