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
    <title>Modifier une propriété - QuickStay</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/global.css">
    <script src="https://kit.fontawesome.com/a2e8d6c6c3.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">
                <i class="fa-solid fa-pen-to-square mr-2"></i>Modifier la propriété
            </h1>

            <?php if (isset($error) && $error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="index.php?route=admin_edit_property&id=<?= htmlspecialchars($property['id']) ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                    <input type="text" name="title" id="title" required
                           value="<?= htmlspecialchars($property['title']) ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?= htmlspecialchars($property['description']) ?></textarea>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Prix par nuit (dt)</label>
                    <input type="number" name="price" id="price" required min="0" step="0.01"
                           value="<?= htmlspecialchars($property['price']) ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                    <?php if (!empty($property['image'])): ?>
                        <div class="mt-2 mb-4">
                            <img src="<?= htmlspecialchars(BASE_URL . $property['image']) ?>" 
                                 alt="Image actuelle" 
                                 class="h-32 w-32 object-cover rounded-lg">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">Laissez vide pour conserver l'image actuelle</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="index.php?route=properties_list" 
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fa-solid fa-times mr-2"></i>Annuler
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fa-solid fa-save mr-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
