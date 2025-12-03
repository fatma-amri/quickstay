<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/Middleware.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($property['title']) ?> - QuickStay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-12 max-w-3xl px-3">
        <?php if (isset($property) && $property): ?>
            <h1 class="text-3xl font-semibold text-center mb-8 text-indigo-900">
                <?= htmlspecialchars($property['title']) ?>
            </h1>
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-12">
                <div class="mb-8 flex items-center justify-center">
                    <img 
                        src="<?= htmlspecialchars(BASE_URL . $property['image']) ?>"
                        alt="Image de la propriété"
                        class="mx-auto rounded-xl shadow-md border border-gray-100"
                        style="max-width:420px; max-height:260px; width:100%; height:auto; object-fit:cover; display:block;">
                </div>
                <div class="mb-2 text-gray-700 text-xl font-medium">
                    <?= htmlspecialchars($property['title']) ?>
                </div>
                <div class="text-blue-700 font-bold text-lg mb-3">
                    € <?= htmlspecialchars(number_format($property['price'], 2)) ?> / nuit
                </div>
                <div class="mb-6 text-gray-700 text-lg leading-relaxed border-b pb-6">
                    <?= nl2br(htmlspecialchars($property['description'])) ?>
                </div>

                <?php if (isUser()): ?>
                    <div class="mt-6 text-center">
                        <a href="<?= BASE_URL ?>index.php?route=show_booking_form&property_id=<?= htmlspecialchars($property['id']) ?>" 
                           class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                           <i class="fa-solid fa-calendar-check mr-2"></i>Réserver
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-6 text-center">
                        <a href="<?= BASE_URL ?>index.php?route=login" 
                           class="inline-block bg-gray-600 text-white px-6 py-3 rounded-md shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                           <i class="fa-solid fa-sign-in-alt mr-2"></i>Connectez-vous pour réserver
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erreur !</strong>
                <span class="block sm:inline">Propriété introuvable.</span>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
