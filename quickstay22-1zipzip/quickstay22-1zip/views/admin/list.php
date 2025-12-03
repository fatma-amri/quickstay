<?php
require_once '../../config.php';
require_once '../../controllers/Middleware.php';
Middleware::adminOnly();

$stmt = $pdo->query("SELECT r.*, u.email, p.title FROM reservations r 
                     JOIN users u ON r.user_id = u.id
                     JOIN properties p ON r.property_id = p.id 
                     ORDER BY r.created_at DESC");
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservations - Admin - QuickStay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Liste des Réservations</h1>

        <?php if (isset($_GET['message'])): ?>
            <div class="mb-4 p-4 rounded text-white 
                <?= $_GET['message'] === 'reservation_valider' ? 'bg-green-500' : 'bg-red-500' ?>">
                <i class="fa-solid fa-circle-check mr-2"></i>
                Réservation <?= $_GET['message'] === 'reservation_valider' ? 'validée' : 'refusée' ?> avec succès.
            </div>
        <?php endif; ?>

        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="py-3 px-4 text-left">Client</th>
                    <th class="py-3 px-4 text-left">Propriété</th>
                    <th class="py-3 px-4 text-left">Dates</th>
                    <th class="py-3 px-4 text-left">Message</th>
                    <th class="py-3 px-4 text-left">Statut</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4"><?= htmlspecialchars($res['email']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($res['title']) ?></td>
                        <td class="py-3 px-4">
                            <?= htmlspecialchars($res['start_date']) ?> → <?= htmlspecialchars($res['end_date']) ?>
                        </td>
                        <td class="py-3 px-4"><?= nl2br(htmlspecialchars($res['message'])) ?></td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-sm font-semibold 
                                <?= $res['status'] === 'pending' ? 'bg-yellow-300 text-yellow-900' : '' ?>
                                <?= $res['status'] === 'confirmed' ? 'bg-green-300 text-green-900' : '' ?>
                                <?= $res['status'] === 'cancelled' ? 'bg-red-300 text-red-900' : '' ?>">
                                <?= ucfirst($res['status']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($res['status'] === 'pending'): ?>
                                <a href="action.php?id=<?= $res['id'] ?>&action=valider" 
                                   class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 mr-2">
                                    <i class="fa-solid fa-check-circle"></i> Valider
                                </a>
                                <a href="action.php?id=<?= $res['id'] ?>&action=refuser" 
                                   class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    <i class="fa-solid fa-times-circle"></i> Refuser
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400 italic">Action non disponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="6" class="text-center py-6 text-gray-500">Aucune réservation trouvée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
