<?php
require_once __DIR__ . '/../../config.php';
// Middleware is handled in index.php
// require_once __DIR__ . '/../../controllers/Middleware.php';
// Middleware::userOnly(); // Apply user middleware if needed
?>
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/navbar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/cards.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/messages.css">
<!-- Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="container mx-auto mt-12 max-w-3xl px-3">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800"><i class="fa-solid fa-bookmark"></i> Mes Réservations</h2>
    <?php include __DIR__ . '/../partials/messages.php'; ?>
    <?php if (empty($bookings)): ?>
        <div class="alert alert-danger bg-red-50 border border-red-200 text-red-700 font-semibold p-4 rounded-xl shadow">
            <i class="fa-solid fa-exclamation-circle mr-2"></i> Aucune réservation pour le moment.
        </div>
    <?php else: ?>
        <ul class="space-y-4">
            <?php foreach ($bookings as $b): ?>
                <li class="p-4 bg-white shadow rounded border-l-4 
                           <?= $b['status'] === 'confirmed' ? 'border-green-500' : ($b['status'] === 'cancelled' ? 'border-red-500' : 'border-yellow-500') ?>">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($b['title'] ?? 'Sans titre') ?></h3>
                            <p class="text-sm text-gray-600">Du <?= date('d/m/Y', strtotime($b['start_date'] ?? '')) ?> au <?= date('d/m/Y', strtotime($b['end_date'] ?? '')) ?></p>
                            <p class="text-sm text-gray-600">Réservé le <?= date('d/m/Y H:i', strtotime($b['created_at'] ?? '')) ?></p>
                             <?php if (!empty($b['message'])): ?>
                                 <p class="text-sm text-gray-700 mt-2">Message: <?= nl2br(htmlspecialchars($b['message'])) ?></p>
                             <?php endif; ?>
                        </div>
                        <div class="flex flex-col items-end">
                             <span class="text-xs font-semibold px-2 py-1 rounded 
                                   <?= $b['status'] === 'confirmed' ? 'bg-green-100 text-green-800' :
                                         ($b['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                 <?= htmlspecialchars(strtoupper($b['status'] ?? 'PENDING')) ?>
                             </span>
                             <?php if ($b['status'] === 'pending'): ?>
                                 <!-- Bouton Annuler -->
                                 <form action="<?= BASE_URL ?>index.php?route=cancel_booking" method="POST" class="mt-2">
                                     <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($b['id']) ?>">
                                     <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                     <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                         <i class="fa-solid fa-times-circle"></i> Annuler
                                     </button>
                                 </form>
                             <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
